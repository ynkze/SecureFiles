<?php
//connect to phplogin database
$conn_phplogin = mysqli_connect('localhost', 'root', '', 'phplogin');
$conn_file_management = mysqli_connect('localhost', 'root', '', 'file-management');
$conn_integrity_management = mysqli_connect('localhost', 'root', '', 'integrity-management');
$conn_key_mangament = mysqli_connect('localhost', 'root', '', 'key-management');

//create accounts table
$sql_accounts = "CREATE TABLE IF NOT EXISTS `accounts` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
  	`username` varchar(50) NOT NULL,
  	`password` varchar(255) NOT NULL,
  	`email` varchar(100) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8";

//create files table
$sql_files = "CREATE TABLE IF NOT EXISTS `files` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
  	`name` varchar(255) NOT NULL,
  	`size` varchar(11) NOT NULL,
		`owner`varchar(255) NOT NULL,
  	`downloads` int(11) NOT NULL,
    PRIMARY KEY (`id`) )";

//create integrity_table table
$sql_integrity = "CREATE TABLE IF NOT EXISTS `integrity_table` (
	`file_id` int(11) NOT NULL,
  	`md5_hash` varchar(255) NOT NULL
)";

//create key_storage table
$sql_key = "CREATE TABLE IF NOT EXISTS `key_storage` (
	`file_id` int(11) NOT NULL,
  	`key_storage` varchar(255) NOT NULL
)";

//check for errors
if (mysqli_query($conn_phplogin, $sql_accounts)) {
  echo "Table accounts created successfully";
} else {
  echo "Error creating table: " . mysqli_error($conn);
}

if (mysqli_query($conn_file_management, $sql_files)) {
  echo "Table files created successfully";
} else {
  echo "Error creating table: " . mysqli_error($conn);
}

if (mysqli_query($conn_integrity_management, $sql_integrity)) {
  echo "Table integrity_table created successfully";
} else {
  echo "Error creating table: " . mysqli_error($conn);
}

if (mysqli_query($conn_key_mangament, $sql_key)) {
  echo "Table key_storage created successfully";
} else {
  echo "Error creating table: " . mysqli_error($conn);
}

//insert account particulars into accounts table
$user1 = 'test';
$password1 = password_hash("test", PASSWORD_DEFAULT);
$sql_account1 = "INSERT INTO `accounts` (`id`, `username`, `password`, `email`) VALUES (1, '$user1', '$password1', 'test@test.com')";
mysqli_query($conn_phplogin, $sql_account1);
?>
