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
			<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
			<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
			<link rel="stylesheet" href="style.css">
	</head>

  <body onload="load()">
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
					  <button class="tablinks" onclick="openCam(event, 'Criminals')">Criminals</button>
					 </div>

					 <!-- Tab content -->
					 <div id="LiveCam" class="tabcontent">
					 <h3>LiveCam</h3>
					 <img src="http://127.0.0.1:8080/stream" class="live_cam_settings">
					 </div>

					 <div id="Criminals" class="tabcontent">
					 <?php include 'GetImagesFromFolder.php';?>
					 <h3>Criminals</h3>
					 <?php if(sizeof($images) > 0){ ?>
					 	<button onclick="loadFirst()" id="load_first" class="button-next"><<</button>
					 	<button onclick="loadLess()" id="load_less" class="button-next"><</button>
					 	<button onclick="loadMore()" id="load_more" class="button-next">></button>
					 	<button onclick="loadLast()" id="load_last" class="button-next">>></button>
					 	<p style="margin-bottom: 0px" id = "page"></p>
					 <?php } ?>
					 <div id="slikice"></div>
					 </div>

				 </div>
		 </div>
		 <div class="col-md-3">
		 </div>
	 </div>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
<script type='text/javascript'>
    var pictures = <?php echo json_encode($images); ?>;
</script>
<script src="script.js"></script>
  </body>
</html>
