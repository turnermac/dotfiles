<!DOCTYPE html>
<?php
    error_reporting(0);
    include("./php/setup.php");

    if(isset($_GET['delete'])) {
        $file=preg_replace("/\.+\//","",stripslashes(htmlspecialchars($_GET['delete'])));
        unlink($file_upload.$file);

        header("Location: list.php");
    } else if(isset($_GET['download'])) {
        $file=preg_replace("/\.+\//","",stripslashes(htmlspecialchars($_GET['download'])));

		header("Content-Type: application/octet-stream");
		header("Content-Transfer-Encoding: Binary");
		header("Content-disposition: attachment; filename=\"".basename($file_upload.$file)."\"");
		header("Content-Length: ".filesize($file_upload.$file));
		ob_clean();
		flush();
        readfile($file_upload.$file);

        header("Location: list.php");
    }

    $fArr=array();
    if($handle=opendir($file_upload)) {
        while(false!==($entry=readdir($handle))) {
            if($entry!="." && $entry!="..") {
				$info=pathinfo($file_upload.$entry);
				$mime="unknown.png";
				switch(strToLower($info['extension'])) { // Detect filetype by extension
					case "png": case "jpg": case "jpeg": case "bmp": case "gif": case "xbm": case "ico":
						$mime="image.png"; break;
					case "wav": case "wma": case "mp3": case "ogg": case "flac": case "midi":
						$mime="audio.png"; break;
					case "avi": case "mp4": case "mkv": case "webm":
						$mime="video.png";break;
					case "zip": case "7z": case "tar": case "gz": case "rar":
						$mime="archive.png"; break;
					case "txt": case "log": case "conf":
						$mime="text.png"; break;
					default:
						$mime="unknown.png";
				}
				if($mime=="unknown.png") { // Dig deeper; regex first line
					$fh=fopen($file_upload.$entry,'r');
					$line=fread($fh,64);
					fclose($fh);
					switch($line) {
						case (preg_match('/[^\x00-\x7F]/',$line)?true:false):   // binary match
							$mime="application.png"; break;
						case (preg_match('/[ -~]/',$line)?true:false):          // ascii match
							$mime="text.png"; break;
						default:
							$mime="unknown.png";
					}
				}

                $size=filesize($file_upload.$entry);

                if($size>1024*1024) {   $psize=floor($size/1024/1024)."M";
                } else if($size>1024) { $psize=floor($size/1024)."K";
                } else {                $psize=$size."B";
                }

                $time=filemtime($file_upload.$entry);
                include("./php/time.php");

                array_push($fArr,array($entry,$ptime,$psize,$mime,$time,$size));
            }
        }
    }
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />

<link rel="stylesheet" type="text/css" href="./css/common.css" />
<link rel="stylesheet" type="text/css" href="./css/list.css" />
<script type="text/javascript" src="./js/list.js"></script>
<script type="text/javascript">
var path="<?php echo $file_upload; ?>";
var fList=[
<?php
    for($i=0;$i<count($fArr);$i++) {
        echo "    [\"".$fArr[$i][0]."\",\"".$fArr[$i][1]."\",\"".$fArr[$i][2]."\",\"".$fArr[$i][3]."\",".$fArr[$i][4].",".$fArr[$i][5]."],\n";
    }
?>
];
</script>
</head>
<body onload="resort('age');">


<div class="tbl" id="fList"></div>


</body>
</html>

