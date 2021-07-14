<?php
session_start();

if(!isset($_SESSION['username'])){  //sluzi da se nemoze doc na ovu stranicu dok god se netko ne  logira (sprijecava se unosenje linka u preglednik)
  header("location: Login.php");
  exit();
}

?>

<html>
	<head >
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<meta http-equiv="X-UA-Compatible" content="IE-edge">
		<title>PUS pandemijski policajac</title>
			<link rel="stylesheet" href="bootstrap-4.4.1-dist/css/bootstrap.min.css">
			<link rel="stylesheet" href="style.css">
	</head>

  <body onload="loadImages();">
	 <div class="row text-center" style="padding-top: 40px">
		 <div class="col-md-3"></div>
		 <div class="col-md-6">

				 <div class="row text-center bg-text">
					 <!-- Tab links -->
					 <div class="row text-center"style="margin:0 auto;padding-bottom: 15px">
						 <div class="col-md-1"></div>
						 <div class="col-md-10">
					 		<h1 style="font-size: 36px;"> Pandemic policeman</h1>
						</div>
						<div class="col-md-1">
							<a href="Logout.php"><img src=logout.png class="logout_img"></a>
						</div>
				 	</div>

					 <div class="tab">
					  <button id="defaultOpen"class="tablinks" onclick="openCam(event, 'LiveCam')">Live Cam</button>
					  <button class="tablinks" onclick="openCam(event, 'Criminals'); loadImages();">Criminals</button>
					 </div>

					 <!-- Tab content -->
					 <div id="LiveCam" class="tabcontent">
					 <h3>LiveCam</h3>
					 <img src="http://127.0.0.1:8080/stream" class="live_cam_settings">
					 </div>

					 <div id="Criminals" class="tabcontent">
					 <h3>Criminals</h3>
					 	<button onclick="loadFirst()" id="load_first" class="button-next"><<</button>
					 	<button onclick="loadLess()" id="load_less" class="button-next"><</button>
					 	<button onclick="loadMore()" id="load_more" class="button-next">></button>
					 	<button onclick="loadLast()" id="load_last" class="button-next">>></button>
					 	<p style="margin-bottom: 0px" id = "page"></p>
					 <div id="slikice"></div>
					 </div>

				 </div>
		 </div>
		 <div class="col-md-3">
		 </div>
	 </div>
<script src="jquery-3.6.0.min.js"></script>
<script src="bootstrap-4.4.1-dist/js/bootstrap.min.js"></script>
<script src="script.js"></script>
  </body>
</html>
