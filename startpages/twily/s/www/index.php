<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<?php
    error_reporting(0);
    include("./php/setup.php");
    include("./php/file.php");
    include("./php/colortime.php");

    $path=preg_replace("/\.+\//","",stripslashes(htmlspecialchars($_GET['p'])));

    while(!is_dir($loc.$path) && !is_file($loc.$path) && $path<>"") {
        if(substr($path,strlen($substr)-1)=="/") {
            $path=substr($path,0,strlen($path)-1);
        } else {
            $path=substr($path,0,strrpos($path,"/"));
        }
    }
    if(substr($path,strlen($path)-1)!="/" && is_dir($loc.$path) && $path<>"") $path.="/";

    $fpath=$loc.$path;
    $vpath="";
    $dpath="";
    $fname="";
    $view=false;
    if(is_file($fpath)) {           // Preparing file preview
        if(strpos($path,"/")) {
            $path=substr($path,0,strrpos($path,"/")+1);
        } else {
            $path="";
        }
        $vpath=$fpath;
        $fname=substr($vpath,strrpos($vpath,"/")+1);
        $fpath=substr($fpath,0,strrpos($fpath,"/")+1);
        $dpath=$dl.$path.$fname;
        $view=true;

        appendf($analytics,time()."\v".$path.$fname."\n");
    }

    $farr=array();
    $darr=array();

    if($handle=opendir($fpath)) {   // List current directory
        while(false!==($entry=readdir($handle))) {
            if($entry!="." && $entry!="..") {
                if(is_dir($fpath.$entry)) {
                    array_push($darr,$entry);
                } else {
                    array_push($farr,$entry);
                }
            }
        }
    }
    closedir($handle);

    $items=count($darr)+count($farr);
    $items.=($items>1)?" items":" item";

    sort($darr);
    sort($farr);

    session_start();                // Visit counter
    $visits=intval(readf($visitpath));
    if(!$_SESSION['visited']) {
        $_SESSION['visited']=true;
        $visits++;
        if($visits>1) writef($visitpath,$visits);
    }
?>
<!--

    Author: Twily                                                                                              2015
    http://twily.info/

    Syntax Highlighting
    https://highlightjs.org/

    Flattr Icons
    https://github.com/NitruxSA/flattr-icons

    Twilight Vector
    http://racer437.deviantart.com/art/Twilight-Sparkle-Vector-321236369

    Other Sources:
    http://stackoverflow.com/questions/1173194/select-all-div-text-with-single-mouse-click
    http://stackoverflow.com/questions/1060008/is-there-a-way-to-detect-if-a-browser-window-is-not-currently-active
    http://stackoverflow.com/questions/260857/changing-website-favicon-dynamically

-->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="description" content="<?php echo $description; ?>">
<meta name="keywords" content="<?php echo $keywords; if($view && is_file($fpath.$fname)) { echo ",".$fname; } ?>">
<title><?php echo $title." ~/".$path.$fname; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />

<link id="favicon" rel="shortcut icon" href="/favicon.ico?v=2" />
<link rel="stylesheet" type="text/css" href="/css/style.css?v=26" />
<?php if($view) echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"/css/highlight.css?v=4\" />\n"; ?>
<script type="text/javascript" src="/js/main.js?v=13"></script>
</head>
<body>

<?php if($_SESSION['embed']) echo "<div id=\"backdrop\"></div>\n"; ?>
<div id="container"<?php if($_SESSION['embed']) echo " style=\"left: 400px; width: calc(100% - 400px);\""; ?>>
<a class="btn bb<?php if($path=="") echo " selected"; ?>" href="/" target="_self">~</a><?php
    $lpath=explode("/",$path);
    for($i=0;$i<count($lpath)-1;$i++) {
        $lpath_url="/";
        $selected="";
        for($j=0;$j<=$i;$j++) $lpath_url.=$lpath[$j]."/";
        if($i==count($lpath)-2) $selected=" selected";
        echo "<a class=\"btn bb".$selected."\" href=\"".$lpath_url."\" target=\"_self\">".$lpath[$i]."</a>";
    }
?>

<div class="action">
    <span class="items"><?php echo $items; ?></span> •
    <a class="btn bb" href="<?php echo "/".$dl.$path; ?>" target="_blank">Download ./*</a>
</div>
<hr /><br />
<div id="browse">
<?php
    $up=substr($path,0,strrpos($path,"/"));
    $up=substr($path,0,strrpos($up,"/"));
    if($up<>"") $up.="/";
    echo "    <a class=\"dir\" href=\"/".$up."\" target=\"_self\"><div class=\"icon\"><img src=\"/img/folder-up.png\" onload=\"this.style.visibility='visible';\" /></div>..</a>\n";

    $bad=array("%","!","?","&","#"," ","+");
    $good=array("%25","%21","%3F","%26","%23","%20","%2B");

    for($i=0;$i<count($darr);$i++) {    // Create directories
        $hidden="";
        $cutn="";
        $dimg="folder.png";
        if(substr($darr[$i],0,1)==".")  $hidden=" hid";
        if(strlen($darr[$i])>$fnamelen) $cutn="...";
        if($path.$darr[$i]=="stuff") $dimg="desktop.png";
        echo "    <a class=\"dir".$hidden."\" href=\"/".str_replace($bad,$good,$path.$darr[$i])."/\" target=\"_self\"><div class=\"icon\"><img src=\"/img/".$dimg."\" onload=\"this.style.visibility='visible';\" /></div>".substr($darr[$i],0,$fnamelen).$cutn."</a>\n";
    }

    for($i=0;$i<count($farr);$i++) {    // Create files
        $info=pathinfo($fpath.$farr[$i]);
        $img="unknown.png";
        switch($info['extension']) {    // Detect filetype by extension
            case "png": case "jpg": case "jpeg": case "bmp": case "gif": case "xbm": case "ico":
                $img="image.png";       break;
            case "wav": case "wma": case "mp3": case "ogg": case "flac": case "midi":
                $img="audio.png";       break;
            case "avi": case "mp4": case "mkv": case "webm":
                $img="video.png";       break;
            case "zip": case "7z": case "tar": case "gz": case "rar":
                $img="archive.png";     break;
            case "xml": case "xhtml": case "html": case "php":
                $img="xml.png";         break;
            case "txt": case "log": case "conf":
                $img="text.png";        break;
            case "otf": case "pcf": case "ttf":
                $img="font.png";        break;
            case "c": case "cpp":
                $img="c.png";           break;
            case "pl":
                $img="perl.png";        break;
            case "py":
                $img="python.png";      break;
            case "sh":
                $img="script.png";      break;
            case "css":
                $img="css.png";         break;
            case "js":
                $img="js.png";          break;
            case "url":
                $img="url.png";         break;
            case "$":
                $img="info.png";        break;
            default:
                $img="unknown.png";
        }
        if($img=="unknown.png") {       // Dig deeper; regex first line
            //$line=(new SplFileObject($fpath.$farr[$i]))->fgets();
            $fh=fopen($fpath.$farr[$i],'r');
            $line=fread($fh,64);
            fclose($fh);
            switch($line) {
                case (preg_match('/#!.*sh.*/',$line)?true:false):       // script match
                    $img="script.png";  break;
                case (preg_match('/[^\x00-\x7F]/',$line)?true:false):   // binary match
                    $img="application.png";  break;
                case (preg_match('/[ -~]/',$line)?true:false):          // ascii match
                    $img="text.png";    break;
                default:
                    $img="unknown.png";
            }
        }

        $selected="";
        $hidden="";
        $cutn="";
        $new="";
        if(time()-filemtime($fpath.$farr[$i])<$oldafter) { $new=" new"; }
        if($view) if($fname==$farr[$i]) $selected=" selected";
        if(substr($farr[$i],0,1)==".")  $hidden=" hid";
        if(strlen($farr[$i])>$fnamelen) $cutn="...";
        echo "    <a class=\"file".$selected.$hidden.$new."\" href=\"/".str_replace($bad,$good,$path.$farr[$i])."#view\" target=\"_self\"><div class=\"icon\"><img src=\"/img/".$img."\" onload=\"this.style.visibility='visible';\" /></div>".substr($farr[$i],0,$fnamelen).$cutn."</a>\n";
    }
?>
</div>
<div id="view"></div>
<br /><hr />
<?php
    if($view) {                                 // File preview
        if(file_exists($vpath)) {
            $mtime=filemtime($vpath);
            $timestamp=date($dformat,$mtime);
            $mtime=time()-$mtime;
            list($mtime,$color)=colortime($mtime);

            $lpath=explode("/",$path);
            for($i=count($lpath)-2;$i<count($lpath)-1;$i++) {
                $lpath_url="#";
                if(count($lpath)<2) $lpath[$i]="~";
                if($lpath[$i]<>"") echo "<a class=\"btn bt\" href=\"".$lpath_url."\" target=\"_self\">".$lpath[$i]."</a>";
            }
            echo "<a class=\"btn bt selected\" href=\"/".$path.$fname."#view\" target=\"_self\">".$fname."</a>\n"
                ."<div class=\"action\">\n"
                ."    <span class=\"lastm\" style=\"color: ".$color.";\" title=\"Last modified ".$timestamp."\">".$mtime."</span> • \n"
                ."    <a class=\"btn bt\" href=\"/".$dpath."\" target=\"_blank\">Download</a>"
                ."<a class=\"btn bt\" href=\"/".$vpath."\" target=\"_blank\">Raw</a>"
                ."<a class=\"btn bt\" href=\"/".$path."\" target=\"_self\">Close</a>\n"
                ."</div>\n<br />\n";

            $info=pathinfo($vpath);
            $prev="";
            switch($info['extension']) {        // Detect best way to preview by file extension
                case "png": case "jpg": case "jpeg": case "bmp": case "gif": case "ico":
                    $prev="<br /><br /><img id=\"text\" class=\"media\" onmouseover=\"zindex(this.id);\" src=\"/".str_replace($bad,$good,$vpath)."\" /><br /><br />\n";
                    break;
                case "wav": case "mp3": case "ogg":
                    $prev="<br /><br /><audio id=\"text\" class=\"media\" onmouseover=\"zindex(this.id);\" autoplay controls loop><source src=\"/".str_replace($bad,$good,$vpath)."\"></audio></br /><br />\n";
                    break;
                case "mp4": case "webm":
                    $prev="<br /><br /><video id=\"text\" class=\"media\" onmouseover=\"zindex(this.id);\" autoplay controls loop><source src=\"/".str_replace($bad,$good,$vpath)."\"></video><br /><br />\n";
                    break;
                case "url":
                    $fc=readf($vpath);
                    $prev="<br /><br /><br /><a class=\"btn\" href=\"".$fc."\" target=\"_blank\">".$fc."</a><br /><br /><br /><br />\n";
                    break;
                case "$":
                    echo "<br /><br /><div id=\"text\" onmouseover=\"zindex(this.id);\">\n";
                    if(is_file($vpath)) include($vpath);
                    echo "</div><br /><br />\n";
                    break;
                default:
                    $bad=array("&","<",">");
                    $good=array("&amp;","&lt;","&gt;");

                    $fc=readf($vpath);
                    $tmp=explode("\n",$fc);
                    if(!preg_match('/[^\x00-\x7F]/',$tmp[0])) { // Detect binary file
                        $fc=str_replace($bad,$good,$fc);
                        $prev="<br /><pre><code id=\"text\" onmouseover=\"zindex(this.id);\" onclick=\"zindex(this.id);\" onmousedown=\"selectText(event,1);\" onmouseup=\"selectText(event,2);\">".$fc."</code></pre><br />\n";
                    } else {
                        $prev="<br /><br />&lt;no preview&gt;<br /><br /><br />\n";   
                    }
?>
<script type="text/javascript" src="/js/highlight.min.js?v=1"></script>
<script type="text/javascript">
hljs.configure({tabReplace: '    ',classPrefix: ''});
hljs.initHighlightingOnLoad();
</script>
<?php
            }

            echo "<center>\n".$prev."\n<a class=\"btn bb\" id=\"top\" href=\"#\" target=\"_self\">Top</a>\n<hr />\n</center>\n";
        }
    }
?>
<script type="text/javascript">function contact(x) { window.location.href='mailto:'+x.replace(' at ','@').replace(' dot ','.'); }</script>
<div id="contact"><a class="btn bt" href="javascript:contact('<?php echo $email; ?>');" target="_self"><?php echo $email; ?></a></div>
<div id="copyleft"><span class="cleft"><a class="btn bb" href="https://en.wikipedia.org/wiki/Copyleft" target="_blank">©</a></span><a class="btn bt" href="/" target="_self"><?php echo $copyl; ?></div>
<?php
    if($view) {
        echo "<br /><br /><br /><iframe src=\"/comment/?f=".hash('sha256',$path.$fname)."\" id=\"comment\" onload=\"loadCmt('comment');\"></iframe>";
    }
?>
<div id="visits"><?php echo number_format($visits,0,","," "); ?> visits</div>
</div>

<script type="text/javascript">
var lt,l=true;
function createChat() {
    var i=document.createElement("iframe");
    i.id="chat";
    i.src="/chat/";
    i.scrolling="no";
    i.onmouseover=function() { zindex(i.id); };
    i.onload=function() {
        i.style.visibility="visible";
<?php if(!$_SESSION['activated']) { ?>
        if(l) {
            clearTimeout(lt);
            lt=setTimeout(function() { i.src=i.src; l=false; },1000);
        }
<?php $_SESSION['activated']=true; } ?>
    };
<?php if($_SESSION['embed']) echo "    i.style=\"height: calc(100% - 2px);\";\n"; ?>

    document.body.appendChild(i);
}

if(window.addEventListener) window.addEventListener("load",createChat,false);
else if(window.attachEvent) window.attachEvent("onload",createChat);
else                        window.onload=createChat;

function restore_chat() {
    $('chat').contentWindow.document.getElementById('nick').value="/exit";
    $('chat').contentWindow.post();
    document.getElementsByTagName('body')[0].removeChild($('chat'));
    createChat();
}
</script>
<a id="chat_restore" class="btn" href="javascript:restore_chat();" target="_self">...</a>

<a id="top_arrow" class="btn arrow bt" href="#view" target="_self">^</a>
<a id="bot_arrow" class="btn arrow bb" href="#comment" target="_self">v</a>

</body>
</html>
