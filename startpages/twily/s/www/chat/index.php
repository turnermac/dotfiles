<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<?php
    error_reporting(0);
    include("./php/setup.php");
    session_start();

    if(!isset($_SESSION['arrived']) || $_SESSION['active']<time()-$sessionTimeout) { // Session begin
        $_SESSION['arrived']=time();
        $_SESSION['last']=$timestamp;
        unset($_SESSION['nick']);
        unset($_SESSION['color']);
        unset($_SESSION['exit']);

        $silent=true;
        $message="/clear";
        include("./php/cmd.php");
    }
?>
<!--

    Author: Twily                                                                                              2015
    http://twily.info/

    Sources:
    http://stackoverflow.com/questions/3234580/read-a-file-backwards-line-by-line-using-fseek
    http://stackoverflow.com/questions/2450850/read-and-write-to-a-file-while-keeping-lock
    http://www.hackingwithphp.com/8/11/0/locking-files-with-flock
    http://www.w3schools.com/ajax/ajax_xmlhttprequest_send.asp
    http://stackoverflow.com/questions/8567114/how-to-make-an-ajax-call-without-jquery
    http://stackoverflow.com/questions/14958735/what-is-the-plain-javascript-equivalent-of-this-jquery-ajax-call
    http://stackoverflow.com/questions/43044/algorithm-to-randomly-generate-an-aesthetically-pleasing-color-palette
    http://stackoverflow.com/questions/17656623/position-absolute-scrolling
    http://stackoverflow.com/questions/30329031/absolute-bottom-position-in-scroll-div
    http://stackoverflow.com/questions/11076975/insert-text-into-textarea-at-cursor-position-javascript
    http://stackoverflow.com/questions/2896626/switch-statement-for-string-matching-in-javascript
    https://css-tricks.com/snippets/php/find-urls-in-text-make-links/#comment-227719
    http://www.aaronpeters.nl/blog/iframe-loading-techniques-performance

-->
<html xmlns="http://www.w3.org/1999/xhtml"<?php if($_SESSION['embed']) echo " style=\"background: #111113;\""; ?>>
<head>
<title><?php echo $title; ?></title>

<link rel="stylesheet" type="text/css" href="./css/style.css?v=4" />
<script type="text/javascript" src="./js/main.js?v=20"></script>
</head>
<body onload="init();">

<div id="qbox">
<label>?</label>
<ul>
    <li><a href="javascript:q('list',1);" target="_self">/list</a></li>
    <li><a href="javascript:q('me ',0);" target="_self">/me %m</a></li>
    <li><a href="javascript:q('pm ',0);" target="_self">/pm %n %m</a></li>
    <li><a href="javascript:q('seen ',0);" target="_self">/seen %n</a></li>
    <li><a href="javascript:q('size',1);" target="_self">/size</a></li>
    <li><a href="javascript:q('refresh',1);" target="_self">/refresh</a></li>
    <li><a href="javascript:q('clear',1);" target="_self">/clear</a></li>
    <li><a href="javascript:q('leave',1);" target="_self">/leave</a></li>
    <li><a href="javascript:q('help',1);" target="_self">/help</a></li>
</ul>
</div>

<div id="container" onmousedown="focusMsg(event,1);" onmouseup="focusMsg(event,2);">
<div id="wrapper">
<div id="log">
<?php echo $_SESSION['log']; ?>
</div>
</div>
</div>

<?php
    $inID="nick";
    $inPH="Type a nick to join this (public) chat...";
    $inVA="Join";
    if(isset($_SESSION['nick'])) {
        $inID="message";
        $inPH="Type a message...";
        $inVA="Send";
    }
?>
<input type="text" id="<?php echo $inID; ?>" value="" placeholder="<?php echo $inPH; ?>" autocomplete="off" maxlength="<?php echo $msglength; ?>" onkeypress="handle(event);" onfocus="this.value=this.value" />
<input type="button" id="enter" class="btn" value="<?php echo $inVA; ?>" onclick="post();" />

<?php
    $jsVAR==new SplFixedArray(4);
    if($_SESSION['embed']) {
        $jsVAR[0]="#111113";
        $jsVAR[1]="none";
        $jsVAR[2]="calc(100% - 2px)";
        $jsVAR[3]="calc(100% - 400px)";
        $jsVAR[4]="400px";
    }
?>
<script type="text/javascript">
document.body.style.background="<?php echo $jsVAR[0]; ?>";
window.addEventListener('message',function(event) {
    var data=event.data.split(" ");
    if(data[0]=="autosign" && $('nick')) { $('nick').value=data[1]; post(); }
});
parent.document.getElementById('chat').style.background="<?php echo $jsVAR[0]; ?>";
parent.document.getElementById('chat').style.transition="<?php echo $jsVAR[1]; ?>";
parent.document.getElementById('chat').style.height="<?php echo $jsVAR[2]; ?>";
parent.document.getElementById('container').style.width="<?php echo $jsVAR[3]; ?>";
parent.document.getElementById('container').style.left="<?php echo $jsVAR[4]; ?>";
<?php
    if($_SESSION['exit']) {
        echo "parent.document.getElementById('chat').style.display=\"none\";\n"
            ."parent.document.getElementById('chat_restore').style.display=\"block\";\n";
    }

    if(!$_SESSION['embed']) echo "parent.document.getElementsByTagName('body')[0].removeChild(parent.document.getElementById('backdrop'));\n";
?>
</script>

<?php session_write_close(); ?>
</body>
</html>
