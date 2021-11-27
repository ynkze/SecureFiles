<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}
$conn = mysqli_connect('localhost', 'root', '', 'phplogin');
$sql = "SELECT * FROM accounts";
$accounts = mysqli_query($conn, $sql);
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
				<h1>SecureFiles</h1>
				<a href="home.php"><i class="fas fa-home"></i>Home</a>
				<a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>

		<div class="content">
			<h2>Home Page</h2>
			<p>Welcome back, <?=$_SESSION['name']?>!</p>
            <div class="container">
                <div class="row">
					<h3>Upload File</h3>
					<h4>Select user</h4>
					<div class="dropdown">
						<button onclick="show()" class="dropbtn">Find user</button>
						<div id="myDropdown" class="dropdown-content">
							<form action="home.php" method="post" enctype="multipart/form-data">
							  <label for="cars">Select user:</label>
							  <select name="username" id="name">
							    <option value="alice">alice</option>
							    <option value="bob">bob</option>
							  </select>
							  <br><br>
							  <input type="submit" value="Submit">
							</form>
						</div>
					</div><br>
                    <form action="home.php" method="post" enctype="multipart/form-data" >
						<h4>Choose file</h4>
						<input type="file" name="myfile"> <br>
						<button type="submit" name="save">Upload</button>
                    </form>
                </div>
            </div>

            <div class="container">
				<div class="row">
					<form action="downloads.php" >
						<h3>Download file</h3>
						<button>See downloadable files</button>
					</form>
				</div>
            </div>

			<div class="container">
				<div class="row">
					<form action="removeFile.php" >
						<h3>Remove owned file</h3>
						<button>See removable file</button>
					</form>
				</div>
            </div>
		</div>
		<script>
		/* When the user clicks on the button,
		toggle between hiding and showing the dropdown content */
		function show() {
		document.getElementById("myDropdown").classList.toggle("show");
		}
		</script>

	</body>
</html>
