<?php
session_start();

if(isset($_SESSION['username'])){  //sluzi da se nemoze vratit na ovu stranicu dok god je neko logiran
  header("location: index.php");
  exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){

  if(isset($_POST["login_butt"])){   //Submitana je Login forma

    //Unset seassion varijabli za ispis greske
    unset($_SESSION['password_err']);
    unset($_SESSION['username_err']);

    //Prikupljanje podataka iz Login forme
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    if($username == "admin" && $password == md5("admin")){ //Uspjesan login
      $_SESSION['username'] = $username;
      header("location: index.php");
    }
    else{
      if($username != "admin"){  //Pogreska u username
        $_SESSION['username_err'] = "Wrong username!";
      }
      if($password != md5("admin")){  //Pogreska u lozinki
        $_SESSION['password_err'] = "Wrong password!";
      }
      header("location: Login.php");
    }

  }
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

  <body>
	 <div class="row text-center" style="padding-top: 40px">
		 <div class="col-md-4"></div>
		 <div class="col-md-4">

				 <div class="row text-center bg-text">
					 <!-- Tab links -->
					 <div style="margin:0 auto;padding-bottom: 15px">
					 	<h1 style="font-size: 36px;"> Login</h1>
				 	</div>

					<!-- Login form -->
          <form id="log_form"  class = "form_edit" style=" padding-top: 0px;" action="Login.php" method ="post" enctype="multipart/form-data">
            <div class="form-group">
              <input type="text" name="username" class="form-control <?php if(isset($_SESSION['username_err'])){echo "is-invalid";}?>" placeholder="Username" required>
              <div  class="invalid-feedback"><?php if(isset($_SESSION['username_err'])){echo $_SESSION['username_err'];} ?></div>
            </div>
            <div class="form-group">
              <input type="password" name="password" class="form-control <?php if(isset($_SESSION['password_err'])){echo "is-invalid";}?>" placeholder="Lozinka" required>
              <div  class="invalid-feedback"><?php if(isset($_SESSION['password_err'])){echo $_SESSION['password_err'];} ?></div>
            </div>
            <button type="submit" class="btn submit" name="login_butt">Login</button>
          </form>

				 </div>
		 </div>
		 <div class="col-md-4">
		 </div>
	 </div>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
<script src="script.js"></script>
  </body>
</html>
