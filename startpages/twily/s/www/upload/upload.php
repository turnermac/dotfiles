<?php
    error_reporting(0);

    $fileName=$_FILES['file']['name'];
    $fileTmpLoc=$_FILES['file']['tmp_name'];
    $fileType=$_FILES['file']['type'];
    $fileSize=$_FILES['file']['size'];
    $fileErrorMsg=$_FILES['file']['error'];
    if(!$fileTmpLoc) {
        echo "Error: Please select a file to upload!";
        exit();
    }
    if(move_uploaded_file($fileTmpLoc,"files/$fileName")) {
        $shortName=$fileName;
        if(strlen($shortName)>32) $shortName=substr($shortName,0,32)."...";
        echo "Success: \"$shortName\" has been uploaded.";
    } else {
        echo "Error: File could not be uploaded!";
    }
?>
