<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}
?>

<?php require 'filesLogic.php';?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Home Page</title>
		<link href="style.css?<?php echo time(); ?>" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	</head>
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<h1>Website Title</h1>
				<a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>

		<div class="content">
			<h2>Home Page</h2>
			<p>Welcome back, <?=$_SESSION['name']?>!</p>
            <div class="container">
                <div class="row">
                    <form action="home.php" method="post" enctype="multipart/form-data" >
                    <h3>Upload File</h3>
                    <input type="file" name="myfile"> <br>
                    <button type="submit" name="save">upload</button>
                    </form>
                </div>
            </div>

            <div class="container">
                <h3>Download file</h3>
                <button>Download</button>
            </div>
		</div>
	</body>
</html>
