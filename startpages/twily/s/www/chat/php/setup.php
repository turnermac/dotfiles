<?php
    date_default_timezone_set("Europe/Oslo");

    $title="twily.info :: chat";

    $nicklength=16;
    $msglength=300;

    $colorMax=255;
    $colorMin=75;
    $colorMix=135;

    $sessionTimeout=20;
    $notifyexclude=30;

    $separator="\v";
    $chatfile="./chat.log";
    $userfile="./user.log";

    $password="mypass";
    $admsym="@";

    $servermsg="---- ";
    $readbackmsg=50;

    $timestamp=time().str_pad(intval(microtime(false)*1000),3,'0',STR_PAD_LEFT);
?>
