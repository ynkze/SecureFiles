<?php
include 'security.php';
// connect to the database
$conn = mysqli_connect('localhost', 'root', '', 'file-management');
$conn_hash = mysqli_connect('localhost', 'root', '', 'integrity-management');

//check files in database
$sql = "SELECT * FROM files";

$sql = "SELECT * FROM files";
$result = mysqli_query($conn, $sql);

$files = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Uploads files
if (isset($_POST['save'])) { // if save button on the form is clicked
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
    } elseif ($_FILES['myfile']['size'] > 1000000) { // file shouldn't be larger than 1Megabyte
        echo "File too large!";
    } else {
        // move the uploaded (temporary) file to the specified destination

        if (move_uploaded_file($file, $destination)) {
            $md5_hash_file = md5_file($destination); //md5 hash file for integrity check
            $filename = encryptthis($filename, $key); // encrpyt filename
            $newdestination = 'uploads/' . $filename;
            $file = encryptFile($destination, $key, $newdestination); //encryptfilecontent
            $sql = "INSERT INTO files (name, size, downloads) VALUES ('$filename', $size, 0)";
            if (mysqli_query($conn, $sql)) {
                echo "File uploaded successfully";
                $last_id = mysqli_insert_id($conn);
                $sql_hash = "INSERT INTO integrity_table (file_id,md5_hash) VALUES ($last_id,'$md5_hash_file')";
                $sql2 = mysqli_query($conn_hash, $sql_hash);
            }
        } else {
            echo "Failed to upload file.";
        }
    }
}

// Downloads files
if (isset($_GET['file_id'])) {
    $id = $_GET['file_id'];
    echo $id;
    // fetch file to download from database
    $sql = "SELECT * FROM files WHERE id=$id";
    $sql_hash ="SELECT * FROM integrity_table WHERE file_id=$id";

    $result = mysqli_query($conn, $sql);
    $result_hash = mysqli_query($conn_hash, $sql_hash);

    $file = mysqli_fetch_assoc($result);
    $file_hash =  mysqli_fetch_assoc($result_hash);//fetch hash from fileid
    //md5('$file['name']')
    //if ($file_hash['md5_hash'] == md5($file['name'])){



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
                    // Now update downloads count
                    $newCount = $file['downloads'] + 1;
                    $updateQuery = "UPDATE files SET downloads=$newCount WHERE id=$id";
                    mysqli_query($conn, $updateQuery);
                    exit;
                }
           else{
             echo "The file has been tampered"; //if hash does not match
           }

       }
}
