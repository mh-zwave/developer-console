<?php

session_start();
include("functions.php");
if (isset($_SESSION['user'])) {

    //$id = $_POST['id'];
    $id = $_SESSION['user']->id;
    $target_dir = "temp/" . $id . "/";
    rrmdir($target_dir);
    if (is_dir($target_dir) == true) {
        
    } else {
        mkdir($target_dir, 0777, true);
        chmod('temp', 0777);
        chmod($target_dir, 0777);
    }


    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

// Check if file already exists
    if (file_exists($target_file)) {
        //echo "Sorry, file already exists.";
        // rename old_file + version
        $uploadOk = 0;
    }
// Check file size
    if ($_FILES["fileToUpload"]["size"] > 500000) {
        //echo "Sorry, your file is too large.";
         $response->status = 500;
        $response->message = 'Sorry, your file is too large.';
        $response->json($response);
    }
// Allow certain file formats

    $imageFileType = strtolower($imageFileType);

    if ($imageFileType == "gz" XOR $imageFileType == "zip") {
        //echo "Sorry, only tar.gz or zip files are allowed.";
        $uploadOk = 1;
    } else {
//echo "Sorry, only tar.gz or zip files are allowed.";
        $response->status = 500;
        $response->message = 'Sorry, only tar.gz or zip files are allowed.';
        $response->json($response);
    }
// Check if $uploadOk is set to 0 by error
    if ($uploadOk == 0) {
        //echo "Sorry, your file was not uploaded. Wrong Format";
        $response->status = 500;
        $response->message = 'Sorry, your file was not uploaded. Wrong Format.';
        $response->json($response);
// if everything is ok, try to upload file
    } else {

        $target_file = strtolower($target_file);
        $imageFileType = strtolower($imageFileType);

        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            if ($imageFileType == "gz") {
                unpack_tar($target_file);
            }
            if ($imageFileType == "zip") {
                unpack_zip($target_file);
                $target_file = str_replace('.zip', '.tar.gz', $target_file);
                $zip_file = pathinfo($_FILES["fileToUpload"]['name']);
                $tar_file = $target_dir . strtolower($zip_file['filename']);
                
                if (is_file($tar_file . '.tar.gz')) {
                    unlink($tar_file . '.tar.gz');
                }
               
                if (is_file($tar_file . '.zip')) {
                    unlink($tar_file . '.zip');
                }
                try {
                    $a = new PharData($tar_file . '.tar');
                    $a->buildFromDirectory($target_dir . strtolower($zip_file['filename']));
                    $a->compress(Phar::GZ);
                    unset($a);
                     Phar::unlinkArchive($tar_file . '.tar');
                } catch (Exception $e) {
                    $response->status = 500;
                    $response->message = $e->getMessage();
                    $response->json($response);
                }
            }
            $wrong_folder =   current(explode(".", $_FILES["fileToUpload"]["name"]));
            
            //store data in DB
            $response = read_json($target_file, $id,'gz',$wrong_folder);
        } else {
            $response->status = 500;
        $response->message = 'Sorry, your file was not uploaded.';
        $response->json($response);
        }
    }
} else {
//    echo "not logged in";
//    header('Location: index.php');
//    exit;
    $response = new Response;
    $response->status = 403;
    $response->message = 'Unauthorized';
    $response->json($response);
}
?>