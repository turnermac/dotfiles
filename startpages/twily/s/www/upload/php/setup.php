<?php
    error_reporting(0);

    $file_upload="./files/";

    $text_upload="./data.txt";
    $timestamp=time().str_pad(intval(microtime(false)*1000),3,'0',STR_PAD_LEFT);
    $remote_ip=$_SERVER['REMOTE_ADDR'];
    $separator="\v";
    $msg_length=4096;
    $readback=25;
?>
