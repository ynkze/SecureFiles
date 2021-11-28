<?php
//connect to phplogin database
$conn_phplogin = mysqli_connect('localhost', 'root', '', 'phplogin');
$conn_file_management = mysqli_connect('localhost', 'root', '', 'file-management');
$conn_integrity_management = mysqli_connect('localhost', 'root', '', 'integrity-management');
$conn_key_management = mysqli_connect('localhost', 'root', '', 'key-management');

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
		`sharewith`varchar(255) NOT NULL,
    PRIMARY KEY (`id`) )";

//create integrity_table table
$sql_integrity = "CREATE TABLE IF NOT EXISTS `integrity_table` (
	`file_id` int(11) NOT NULL,
  	`md5_hash` varchar(255) NOT NULL
)";

//create key_storage table
$sql_key = "CREATE TABLE IF NOT EXISTS `key_management` (
	`username` varchar(50) NOT NULL,
  `publickey` varchar(255) NOT NULL
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

if (mysqli_query($conn_key_management, $sql_key)) {
  echo "Table key_storage created successfully";
} else {
  echo "Error creating table: " . mysqli_error($conn);
}

//insert account particulars into accounts table
//$user1 = 'test';
//$password1 = password_hash("test", PASSWORD_DEFAULT);
//$sql_account1 = "INSERT INTO `accounts` (`id`, `username`, `password`, `email`) VALUES (1, '$user1', '$password1', 'test@test.com')";
//mysqli_query($conn_phplogin, $sql_account1);

$user2 = 'alice';
$password2 = password_hash("alice", PASSWORD_DEFAULT);
$public_key2 = '4c3e5563c9cf9a141a449e226fb0e668036e4f55364b73e6869994f8a19a166c982ddea074fb4cdcbba834c5134dd0379e8909de20b7d7b08c5f08446f951f62';
$sql_account2 = "INSERT INTO `accounts` (`id`, `username`, `password`, `email`) VALUES (2, '$user2', '$password2', 'alice@test.com')";
$sql_publickey2 = "INSERT INTO `key_management` (`username`, `publickey`) VALUES ('$user2', '$public_key2')";
mysqli_query($conn_phplogin, $sql_account2);
mysqli_query($conn_key_management, $sql_publickey2);

$user3 = 'bob';
$password3 = password_hash("bob", PASSWORD_DEFAULT);
$public_key3 = '009ce7680c7aae2f71a3392e72176ccadcf6d9da3ee4598c2cdc801eac2846e4057268622b175760d99c463757b9f105e56b89b14e43d9135808e52e28e2361ce2';
$sql_account3 = "INSERT INTO `accounts` (`id`, `username`, `password`, `email`) VALUES (3, '$user3', '$password3', 'bob@test.com')";
$sql_publickey3 = "INSERT INTO `key_management` (`username`, `publickey`) VALUES ('$user3', '$public_key3')";
mysqli_query($conn_phplogin, $sql_account3);
mysqli_query($conn_key_management, $sql_publickey3);
?>
