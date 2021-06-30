#include <stdio.h>
#include <opencv2/opencv.hpp>
#include <opencv2/dnn.hpp>
#include <opencv2/highgui.hpp>
#include <fstream>
#include <iostream>
#include <opencv2/core/ocl.hpp>
#include "tensorflow/lite/interpreter.h"
#include "tensorflow/lite/kernels/register.h"
#include "tensorflow/lite/string_util.h"
#include "tensorflow/lite/model.h"
#include <cmath>

#include "mjpeg_streamer.hpp"

#define MAX_IMGS 16
#define MAX_DETECTIONS 10
#define DIST_THRESH 50

using namespace cv;
using namespace std;

int model_width;
int model_height;
int model_channels;

int img_counter = 0;
//int no_mask_counter = 0;

std::unique_ptr<tflite::Interpreter> interpreter;

struct PrevDetection {
    float x1;
    float y1;
    bool no_mask;
};

PrevDetection prev_frame_detections[MAX_DETECTIONS] = {0};

//-----------------------------------------------------------------------------------------------------------------------
void GetImageTFLite(float* out, Mat &src)
{
    int i,Len;
    float f;
    uint8_t *in;
    static Mat image;

    // copy image to input as input tensor
    cv::resize(src, image, Size(model_width,model_height),INTER_NEAREST);

    //model posenet_mobilenet_v1_100_257x257_multi_kpt_stripped.tflite runs from -1.0 ... +1.0
    //model multi_person_mobilenet_v1_075_float.tflite                 runs from  0.0 ... +1.0
    in = image.data;
    Len = image.rows*image.cols*image.channels();
    for (i = 0; i < Len; i++) {
        f = in[i];
        out[i] = (f - 127.5f) / 127.5f;
    }
}
//-----------------------------------------------------------------------------------------------------------------------

float distance(float x1, float y1, float x2, float y2) {
    float x_dist = x2 - x1;
    float y_dist = y2 - y1;
    return std::sqrt(x_dist*x_dist + y_dist*y_dist);
}

void detect_from_video(Mat &src)
{
    Mat image;
    int cam_width =src.cols;
    int cam_height=src.rows;

    // copy image to input as input tensor
    GetImageTFLite(interpreter->typed_tensor<float>(interpreter->inputs()[0]), src);

    interpreter->AllocateTensors();
    interpreter->SetAllowFp16PrecisionForFp32(true);
    interpreter->SetNumThreads(4);      //quad core

    interpreter->Invoke();      // run your model

    const float* detection_locations = interpreter->tensor(interpreter->outputs()[0])->data.f;
    const float* detection_classes=interpreter->tensor(interpreter->outputs()[1])->data.f;
    const float* detection_scores = interpreter->tensor(interpreter->outputs()[2])->data.f;
    const int    num_detections = *interpreter->tensor(interpreter->outputs()[3])->data.f;

    //there are ALWAYS 10 detections no matter how many objects are detectable

    const float confidence_threshold = 0.5;
    for (int i = 0; i < num_detections; i++) {
        if (detection_scores[i] > confidence_threshold) {
            int  det_index = (int)detection_classes[i];
            //std::cout << i << "\n";
            float y1 = detection_locations[4*i  ]*cam_height;
            float x1 = detection_locations[4*i+1]*cam_width;
            float y2 = detection_locations[4*i+2]*cam_height;
            float x2 = detection_locations[4*i+3]*cam_width;

            Rect rec((int)x1, (int)y1, (int)(x2 - x1), (int)(y2 - y1));
            if (det_index == 0) {
                rectangle(src,rec, Scalar(0, 255, 0), 2, 8, 0);
                putText(src,"mask", Point(x1, y1-5) ,FONT_HERSHEY_SIMPLEX,0.7, Scalar(0, 255, 0), 1, 8, 0);
                prev_frame_detections[i].no_mask = false;
            }
            if (det_index == 1) {
                //cv::drawMarker(src, cv::Point(x1 + (x2 - x1)/2, y1 + (y2 - y1)/2),  cv::Scalar(0, 0, 255), MARKER_CROSS, x2-x1+6, 2);
                //cv::circle(src, cv::Point(x1 + (x2 - x1)/2, y1 + (y2 - y1)/2),(x2-x1)/2 + 3, cv::Scalar(0, 0, 255), 2);
                rectangle(src,rec, Scalar(0, 0, 255), 2, 8, 0);
                putText(src,"no mask", Point(x1, y1-5) ,FONT_HERSHEY_SIMPLEX,0.7, Scalar(0, 0, 255), 1, 8, 0);

                // save every 5th image of person with no mask
                /*if (!no_mask_counter) {
                    img_counter = (img_counter + 1) % MAX_IMGS;
                    imwrite("./web/slike/" + to_string(img_counter) + ".jpg", src);
                    std::cout << "saved no mask img\n";
                }
                no_mask_counter++;
                no_mask_counter %= 5;*/
                float old_x1 = prev_frame_detections[i].x1;
                float old_y1 = prev_frame_detections[i].y1;
                if (!prev_frame_detections[i].no_mask || distance(x1, y1, old_x1, old_y1) > DIST_THRESH) {
                    img_counter = (img_counter + 1) % MAX_IMGS;
                    imwrite("./web/slike/" + to_string(img_counter) + ".jpg", src);
                    std::cout << "New person with no mask - saved image: ./web/slike/" + std::to_string(img_counter) +".jpg\n";
                }
                prev_frame_detections[i].x1 = x1;
                prev_frame_detections[i].y1 = y1;
                prev_frame_detections[i].no_mask = true;
            }
            if (det_index == 2) {
                prev_frame_detections[i].no_mask = false;
                rectangle(src,rec, Scalar(0, 127, 255), 2, 8, 0);
                putText(src,"wear incorrect", Point(x1, y1-5) ,FONT_HERSHEY_SIMPLEX,0.7, Scalar(0, 127, 255), 1, 8, 0);
            }
        } else {
            prev_frame_detections[i].no_mask = false;
        }
    }
}



// for convenience
using MJPEGStreamer = nadjieb::MJPEGStreamer;

int main(int argc,char **argv)
{
    float f;
    float FPS[16];
    int i;
    int Fcnt = 0;
    Mat frame;
    chrono::steady_clock::time_point Tbegin, Tend;

    for (i = 0; i < 16; i++) FPS[i] = 0.0;

    // Load model
    std::unique_ptr<tflite::FlatBufferModel> model = tflite::FlatBufferModel::BuildFromFile("ssd_mobilenet_v2_fpnlite.tflite");
//  std::unique_ptr<tflite::FlatBufferModel> model = tflite::FlatBufferModel::BuildFromFile("ssdlite_mobilenet_v2.tflite");

    // Build the interpreter
    tflite::ops::builtin::BuiltinOpResolver resolver;
    tflite::InterpreterBuilder(*model.get(), resolver)(&interpreter);

    interpreter->AllocateTensors();

    // Get input dimension from the input tensor metadata
    // Assuming one input only
    int In = interpreter->inputs()[0];
    model_height   = interpreter->tensor(In)->dims->data[1];
    model_width    = interpreter->tensor(In)->dims->data[2];
    model_channels = interpreter->tensor(In)->dims->data[3];
    cout << "height   : " << model_height << endl;
    cout << "width    : " << model_width << endl;
    cout << "channels : " << model_channels << endl;

    cout << "tensors size: " << interpreter->tensors_size() << "\n";
    cout << "nodes size: " << interpreter->nodes_size() << "\n";
    cout << "inputs: " << interpreter->inputs().size() << "\n";
    cout << "input(0) name: " << interpreter->GetInputName(0) << "\n";
    cout << "outputs: " << interpreter->outputs().size() << "\n";

    std::vector<int> params = {cv::IMWRITE_JPEG_QUALITY, 90}; // bilo 90
    MJPEGStreamer streamer;
    // By default "/shutdown" is the target to graceful shutdown the streamer
    // if you want to change the target to graceful shutdown:
    //      streamer.setShutdownTarget("/stop");

    // By default 1 worker is used for streaming
    // if you want to use 4 workers:
    //      streamer.start(8080, 4);
    streamer.start(8080);

    //VideoCapture cap("Face_Mask_Video.mp4");//Norton_M3.mp4");
    VideoCapture cap(0);
    if (!cap.isOpened()) {
        cerr << "ERROR: Unable to open the camera" << endl;
        return 0;
    }

    cout << "Start grabbing, press ESC on Live window to terminate" << endl;

    while (streamer.isAlive()) {
//        frame=imread("Kapje_2.jpg");  //need to refresh frame before dnn class detection
        cap >> frame;
        if (frame.empty()) {
            cerr << "End of movie" << endl;
            break;
        }

        cv::resize(frame, frame, cv::Size(), 0.8, 0.8);
        detect_from_video(frame);
        Tend = chrono::steady_clock::now();
        //calculate frame rate
        f = chrono::duration_cast <chrono::milliseconds> (Tend - Tbegin).count();

        Tbegin = chrono::steady_clock::now();

        FPS[((Fcnt++)&0x0F)]=1000.0/f;

        for (f = 0, i = 0; i < 16; i++) {
            f+=FPS[i];
        }
        putText(frame, format("FPS %0.2f",f/16),Point(10,20),FONT_HERSHEY_SIMPLEX,0.6, Scalar(0, 0, 255));


        //std::cout << "rows: " << frame.rows << " cols: " << frame.cols << "\n";
        //cv::imshow("frame", frame);
        //cv::waitKey(140);

        // http://localhost:8080/bgr
        std::vector<uchar> buff_bgr;
        cv::imencode(".jpg", frame, buff_bgr, params);
        streamer.publish("/bgr", std::string(buff_bgr.begin(), buff_bgr.end()));

        //show output
        //imshow("RPi 4 - 2.0 GHz - 2 Mb RAM", frame);
        //img_counter = (img_counter + 1) % MAX_IMGS;
        //imwrite("./web2/slike/" + to_string(img_counter) + ".jpg", frame);
        //char esc = waitKey(5);
        //if(esc == 27) break;
    }

    cout << "Closing the camera" << endl;

    // When everything done, release the video capture and write object
    cap.release();
    streamer.stop();
    destroyAllWindows();

    return 0;
}
