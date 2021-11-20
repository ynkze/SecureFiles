<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}
$conn_key = mysqli_connect('localhost', 'root', '', 'key-management');
?>

<?php require 'filesLogic.php';?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <link rel="stylesheet" href="style.css">
  <title>Download files</title>
</head>
<body>

<table>
<thead>
    <th>ID</th>
    <th>Filename</th>
    <th>size (in mb)</th>
    <th>Downloads</th>
    <th>Action</th>
</thead>
<tbody>

  <?php foreach ($files as $file): ?>
    <tr>
      <td><?php echo $file['id']; ?></td>
			    <?php $id = $file['id']; ?>
					<?php $sql_key = "SELECT * FROM key_storage WHERE file_id= $id"; ?>
					<?php $result_key = mysqli_query($conn_key, $sql_key); ?>
					<?php $key = mysqli_fetch_assoc($result_key); ?>
      <td><?php echo decryptthis($file['name'],$key['key_storage']); ?></td>
      <td><?php echo floor($file['size'] / 1000) . ' KB'; ?></td>
      <td><?php echo $file['downloads']; ?></td>
      <td><a href="downloads.php?file_id=<?php echo $file['id'] ?>">Download</a></td>
    </tr>
  <?php endforeach;?>

</tbody>
</table>

</body>
</html>
