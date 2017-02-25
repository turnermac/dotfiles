<!DOCTYPE html>
<?php
    error_reporting(0);
    session_start();

    $_SESSION['arrived']=time();
    $_SESSION['last']=0;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />

<link rel="stylesheet" type="text/css" href="./css/common.css" />
<link rel="stylesheet" type="text/css" href="./css/text.css" />
<script type="text/javascript" src="./js/text.js"></script>
</head>
<body onload="fetch();">

<div id="text"></div>

</body>
</html>
<?php session_write_close(); ?>
