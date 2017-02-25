<?php
    function readf($file) {
        $fh=fopen($file,'r');
        $data=fread($fh,filesize($file));
        fclose($fh);
        return $data;
    }

    function writef($file,$data) {
        $fh=fopen($file,'w');
        fwrite($fh,$data);
        fclose($fh);
        return 0;
    }

    function appendf($file,$data) {
        $fh=fopen($file,'a');
        fwrite($fh,$data);
        fclose($fh);
        return 0;
    }

    function no_ext($name) {
        return substr($name,0,strrpos($name,'.'));
    }
?>
