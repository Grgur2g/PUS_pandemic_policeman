<?php
define('IMAGEPATH', 'slike/');
 $images = [];
foreach(glob(IMAGEPATH.'*') as $filename){
    $images[] =  basename($filename);
}
echo json_encode($images);
?>
