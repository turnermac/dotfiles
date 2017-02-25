<?php
    error_reporting(0);
    include("./php/setup.php");
    session_start();

    $_SESSION['active']=time();

	function writeMessage($message,$extra="") {
		global $chatfile;
		global $separator;
		global $timestamp;
		$fh=fopen($chatfile,'a');

		if(flock($fh,LOCK_EX)) {
			$regexURL="/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,4}(\/\S*)?/";

			if(preg_match_all($regexURL,$message,$url)) {
				$matches=array_unique($url[0]);
				foreach($matches as $match) {
					$replacement=" <a href=\"".$match."\" target=\"_blank\">".$match."</a> ";
					$message=str_replace($match,$replacement,$message);
				}
			}

			$regexURL="/(~\/)+(\S*)?/";

			if(preg_match_all($regexURL,$message,$url)) {
				$matches=array_unique($url[0]);
				foreach($matches as $match) {
					$replacement="<a href=\"javascript:go('".substr($match,1)."')\" target=\"_self\">".$match."</a>";
					$message=str_replace($match,$replacement,$message);
				}
			}

			$message=$timestamp.$separator.$_SESSION['color'].$separator.$_SESSION['nick'].$separator.$message.$extra."\n";
			fwrite($fh,$message);

			flock($fh,LOCK_UN);

			echo "success";
		}

		fclose($fh);
	}

    if(isset($_POST['send'])) {
        $message=substr(str_replace($separator,"",stripslashes(htmlspecialchars($_POST['message']))),0,$msglength);

        if($message<>"") {
            if(substr($message,0,1)=="/") { // Command
                include("./php/cmd.php");
            } else {
                if($_SESSION['nick']<>"") { // Message
					writeMessage($message);
                }
            }
        }
    }

    session_write_close();
?>
