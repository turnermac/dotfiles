<?php
    error_reporting(0);
    include("./php/setup.php");
    session_start();
    
    if(isset($_POST['send'])) {
        $message=substr(str_replace("\n","<br>",str_replace($separator,"",stripslashes(htmlspecialchars($_POST['message'])))),0,$msg_length);

        if($message<>"") {
            $fh=fopen($text_upload,'a');

            if(flock($fh,LOCK_EX)) {
                $message="\n".$timestamp.$separator.$remote_ip.$separator.$message;
                fwrite($fh,$message);

                flock($fh,LOCK_UN);
                
                echo "success";
            }

            fclose($fh);
        }
    } else {
        $fh=fopen($text_upload,'r');

        if(flock($fh,LOCK_EX)) {
            $pos=-1;
            $lines=array();
            $cLine='';

            while(fseek($fh,$pos,SEEK_END)!=-1) {
                $char=fgetc($fh);

                if($char=="\n") {
                    if($cLine<>"") {
                        if(substr($cLine,0,strpos($cLine,$separator))>$_SESSION['last'] && count($lines)<$readback) {
                            $lines[]=$cLine;
                            $cLine='';
                        } else {
                            break;
                        }
                    }
                } else {
                    $cLine=$char.$cLine;
                }

                $pos--;
            }

            $output="";
            for($i=0;$i<=count($lines);$i++) {
                if($lines[$i]<>"") {
                    $line=explode($separator,$lines[$i]);

                    $time=floor($line[0]/1000);
                    include("./php/time.php");

                    $output.="<div class=\"box\"><span class=\"age\">".$ptime." ago</span>".$line[2]."</div><br />";

                    if($line[0]>$_SESSION['last']) $_SESSION['last']=$line[0];
                }
            }

            if($output<>"") echo "message".$output;

            flock($fh,LOCK_UN);
        }

        fclose($fh);
    }

    session_write_close();
?>
