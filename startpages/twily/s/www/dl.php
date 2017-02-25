<?php
    error_reporting(0);
    include("./php/setup.php");

    // Requires PHP ZIP extension

    $path=$loc.preg_replace("/\.+\//","",stripslashes(htmlspecialchars($_GET['p'])));

    if(substr($path,strlen($path)-1)!="/") {    // Single file download
        header("Content-Type: application/octet-stream");
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"".basename($path)."\"");
        header("Content-Length: ".filesize($path));
        ob_clean();
        flush();
        readfile($path);
    } else {                                    // Directory download
        $files=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path),RecursiveIteratorIterator::SELF_FIRST);

        $zname=substr(str_replace("/","-",$path),0,strlen($path)-1);
        $zipname=str_replace("\$",$zname,$zipname);
        $zippath=$zippath.$zipname;

        if(!file_exists($zippath)) {            // Make zip
            $zip=new ZipArchive;
            $zip->open($zippath,ZipArchive::CREATE);

            foreach($files as $file) {
                $file=str_replace("\\","/",$file);

                if(!in_array(substr($file,strrpos($file,"/")+1),array(".",".."))) {
                    if(strlen($path)>2) $lfile=substr($file,strrpos($path,"/",-2)+1);
                    else                $lfile=$file;

                    if(is_dir($file))   $zip->addEmptyDir($lfile);
                    else                $zip->addFile($file,$lfile);
                }
            }

            $zip->close();
        }

        header("Content-Type: application/zip");
        header("Content-disposition: attachment; filename=\"".basename($zippath)."\"");
        header("Content-Length: ".filesize($zippath));
        ob_clean();
        flush();
        readfile($zippath);

        ignore_user_abort(true);
        unlink($zippath);                       // Delete zip
    }
?>
