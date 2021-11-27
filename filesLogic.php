<?php

require 'security.php';
//
// connect to the database
$conn = mysqli_connect('localhost', 'root', '', 'file-management');
$conn_hash = mysqli_connect('localhost', 'root', '', 'integrity-management');
$conn_key = mysqli_connect('localhost', 'root', '', 'key-management');

//check files in database
$sql = "SELECT * FROM files";
$result = mysqli_query($conn, $sql);
$files = mysqli_fetch_all($result, MYSQLI_ASSOC);

//check files belonging owner


// Uploads files
if (isset($_POST['save'])) { // if save button on the form is clicked
    session_start();
    //get user information
    $currentUser = $_SESSION['name'];
    //fetch user public key
    if ($currentUser == 'alice'){
        $sql_publickey="SELECT * FROM key_management WHERE username= 'bob'";
        $result_key = mysqli_query($conn_key, $sql_publickey);
        $result_key = mysqli_fetch_assoc($result_key);
        $user_publickey = $result_key['publickey'];
        $key = generateDHKey_alice($user_publickey); //generate dhkey for encryption
      }

    else{
        $sql_publickey="SELECT * FROM key_management WHERE username= 'alice'";
        $result_key = mysqli_query($conn_key, $sql_publickey);
        $result_key = mysqli_fetch_assoc($result_key);
        $user_publickey = $result_key['publickey'];
        $key = generateDHKey_bob($user_publickey); //generate dhkey for encryption
      }
    // name of the uploaded file
    $filename = $_FILES['myfile']['name'];

    // destination of the file on the server
    $destination = 'uploads/' . $filename;

    // get the file extension
    $extension = pathinfo($filename, PATHINFO_EXTENSION);

    // the physical file on a temporary uploads directory on the server
    $file = $_FILES['myfile']['tmp_name'];
    $size = $_FILES['myfile']['size'];
    if (!in_array($extension, ['zip', 'pdf', 'docx'])) {
        echo "You file extension must be .zip, .pdf or .docx";
    } elseif ($_FILES['myfile']['size'] > 10000000) { // file shouldn't be larger than 10Megabyte
        echo "File too large!";
    } else {
        // move the uploaded (temporary) file to the specified destination

        if (move_uploaded_file($file, $destination)) {

            //$key = openssl_random_pseudo_bytes(32);
            //$key = bin2hex($key);
            $md5_hash_file = md5_file($destination); //md5 hash file for integrity check
            $filename = encryptthis($filename, $key); // encrpyt filename using AES-256
            $newdestination = 'uploads/' . $filename;
            $file = encryptFile($destination, $key, $newdestination); //encryptfilecontent using AES-256
            unlink($destination);
            $sql = "INSERT INTO files (name, size, owner, downloads) VALUES ('$filename', $size, '$currentUser', 0)";
            if (mysqli_query($conn, $sql)) {
                echo $currentUser;
                echo "File uploaded successfully";
                $last_id = mysqli_insert_id($conn);
                $sql_hash = "INSERT INTO integrity_table (file_id,md5_hash) VALUES ($last_id,'$md5_hash_file')"; //insert hash to database
                $sql2 = mysqli_query($conn_hash, $sql_hash);
                $sql_key = "INSERT INTO key_storage (file_id,key_storage) VALUES ($last_id,'$key')";
                $sql3 = mysqli_query($conn_key, $sql_key);

            }
        } else {
            echo "Failed to upload file.";
        }
    }
}

// Downloads files
if (isset($_GET['file_id'])) {
    session_start();
    $currentUser = $_SESSION['name'];

    $id = $_GET['file_id'];

    // fetch file and relevant hash

    $sql = "SELECT * FROM files WHERE id=$id";
    $sql_hash ="SELECT * FROM integrity_table WHERE file_id=$id";

    $result = mysqli_query($conn, $sql);
    $result_hash = mysqli_query($conn_hash, $sql_hash);

    $file = mysqli_fetch_assoc($result);
    $file_hash =  mysqli_fetch_assoc($result_hash);//fetch hash from fileid

    //fetch relevant public keys
    if ($currentUser == 'alice'){
        $sql_publickey="SELECT * FROM key_management WHERE username= 'bob'";
        $result_key = mysqli_query($conn_key, $sql_publickey);
        $result_key = mysqli_fetch_assoc($result_key);
        $user_publickey = $result_key['publickey'];
        $key = generateDHKey_alice($user_publickey); //generate dhkey for encryption
      }

    elseif ($currentUser == 'bob'){
        $sql_publickey="SELECT * FROM key_management WHERE username= 'alice'";
        $result_key = mysqli_query($conn_key, $sql_publickey);
        $result_key = mysqli_fetch_assoc($result_key);
        $user_publickey = $result_key['publickey'];
        $key = generateDHKey_bob($user_publickey); //generate dhkey for encryption
      }


        $decrptedfilename = decryptthis($file['name'], $key);
        $filepath = 'uploads/' . $file['name'];
        $decrypted_filepath = 'uploads/' . $decrptedfilename;
        $decryptedfile = decryptFile($filepath, $key, $decrypted_filepath);



        if (file_exists($decrypted_filepath)) {
            if (md5_file($decrypted_filepath) == $file_hash['md5_hash']){  // check for intergrity
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename=' . basename($decrypted_filepath));
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize('uploads/' . $decrptedfilename));
                    readfile('uploads/' . $decrptedfilename);
                    unlink($decrypted_filepath);
                    // Now update downloads count
                    $newCount = $file['downloads'] + 1;
                    $updateQuery = "UPDATE files SET downloads=$newCount WHERE id=$id";
                    mysqli_query($conn, $updateQuery);
                    exit;
                }
           else{
             echo "The file has been tampered"; //if hash does not match
             unlink($decrypted_filepath);
           }

       }
}

//remove file from database
if (isset($_GET['remove_id'])){
  $id = $_GET['remove_id'];
  $sql_delete_file = "DELETE FROM files WHERE id = $id";
  $sql_delete_hash = "DELETE FROM integrity_table WHERE file_id = $id";
  if (mysqli_query($conn, $sql_delete_file) && mysqli_query($conn_hash, $sql_delete_hash)){
    echo "The file has been removed from the database";
    exit;
  }
  else {
    echo "Process failed";
  }
}
