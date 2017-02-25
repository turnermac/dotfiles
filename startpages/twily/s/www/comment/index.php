<!DOCTYPE html>
<?php
    error_reporting(0);
    include("./php/file.php");
    include("./php/setup.php");

    if(isset($_GET['f'])) {
        $cid=stripslashes(htmlspecialchars($_GET['f']));
    } else {
        exit();
    }
    $file="./cmt/".$cid.".log";
    session_start();

    if(isset($_POST['text'])) {
        $name=preg_replace("/[^A-Za-z0-9!]/","",substr(str_replace($sep,"",stripslashes(htmlspecialchars($_POST['name']))),0,$name_max));
        $text=substr(str_replace($sep,"",stripslashes(htmlspecialchars($_POST['text']))),0,$text_max);
        $post=intval(preg_replace('/[^0-9-]+/','',$_POST['post']));

        if(strlen($text)>0) {
            $_SESSION['cmtName']=$name;
            if(strlen($name)<=0) $name="Anonymous";

            if(substr($name,0,1)=="!") {
                if($name==$password) {
                    $name="@".$passnick;
                } else {
                    $name="Anonymous";
                }
            }
            $name=str_replace("!","",$name);

            $text=preg_replace("/\r|\n/","",nl2br($text));
            $cmt=$timestamp.$sep.$post.$sep.$name.$sep.$text."\n";
            appendf($file,$cmt);

            header("Location: ./?f=".$cid);
        }
    }
?>
<!--

    Author:        Twily                                        2016
    Website:       http://twily.info/
    Compatibility: Mozilla Firefox, Internet Explorer, Google Chrome

-->
<html>
<head>
<title>twily.info :: comment section</title>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />

<link rel="stylesheet" type="text/css" href="./css/style.css?v=4" />
<script type="text/javascript" src="./js/main.js?v=3"></script>
<script type="text/javascript">
var comments=[
<?php
    $lines=explode("\n",readf($file));

    for($i=0;$i<count($lines)-1;$i++) {
        $data=explode($sep,$lines[$i]);
        echo "    [".$data[0].",".$data[1].",\"".$data[2]."\",\"".$data[3]."\"],\n";
    }
?>
];

var lLimit=<?php echo $line_limit; ?>;
var tLimit=<?php echo $text_limit; ?>;
var nMax=<?php echo $name_max; ?>;
var tMax=<?php echo $text_max; ?>;
var lName="<?php echo $_SESSION['cmtName']; ?>";
var cid="<?php echo $cid; ?>";
var tid=<?php echo time(); ?>;
/*window.onmessage=function(e) {
    alert(e.data);
}*/
</script>
</head>
<body onload="init();">

<br />
<span id="cmtStat">Comments (<?php echo count($lines)-1; ?>):</span>
<div id="comments"></div>

</body>
</html>
<?php session_write_close(); ?>

