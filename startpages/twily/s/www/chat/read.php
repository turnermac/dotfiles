<?php
    error_reporting(0);
    include("./php/setup.php");
    session_start();

    if($_SESSION['last']<>0 && !isset($_SESSION['exit'])) {
        $fh=fopen($chatfile,'r');

        if(flock($fh,LOCK_EX)) {
            $pos=-1;
            $lines=array();
            $cLine='';

            while(fseek($fh,$pos,SEEK_END)!=-1) { // Read file line by line in reverse
                $char=fgetc($fh);

                if($char=="\n") {
                    if($cLine<>"") {
                        if(substr($cLine,0,strpos($cLine,$separator))>$_SESSION['last'] && !$readback) {
                            $lines[]=$cLine;
                            $cLine='';
                        } else if($readback && $rbc>0) {
                            if(substr($cLine,strrpos($cLine,$separator,-8)+1,1)!="@") {
                                $rbc--;
                            }
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

            $i=count($lines)-1;
            $output="";

            while($i>=0) {
                if($lines[$i]<>"") { // Print out each missing message since session 'last' (or readback)
                    $line=explode($separator,$lines[$i]);

                    if($readback) $output.="<span class=\"past\">";

                    $skip=false;
                    if($line[4]==$servermsg) {
                        $output.=$line[4]."<span style=\"color: #".$line[1].";\" onclick=\"reply('".$line[2]."');\"><b>".$line[2]."</b> </span>"; // Server
                    } else if($line[4]=="*") {
                        $output.=$line[4]." "."<span style=\"color: #".$line[1].";\" onclick=\"reply('".$line[2]."');\"><b>".$line[2]."</b> </span>"." "; // 'me'
                    } else if(substr($line[4],0,1)=="@") {
                        if(!$readback) {
                            if(substr($line[4],1)==$_SESSION['nick']) { // From
                                $output.="<span onclick=\"q('pm ".$line[2]." ');\">[From]</span> <span style=\"color: #".$line[1].";\" onclick=\"reply('".$line[2]."');\"><b>".$line[2].":</b> </span> ";
                            } elseif($line[2]==$_SESSION['nick']) { // To
                                $output.="<span onclick=\"q('pm ".substr($line[4],1)." ');\">[To]</span> <span style=\"color: #".$line[5].";\" onclick=\"reply('".substr($line[4],1)."');\"><b>".substr($line[4],1).":</b></span> ";
                            } else {
                                $skip=true;
                            }
                        } else {
                            $skip=true;
                        }
                    } else {
                        $output.="<span style=\"color: #".$line[1].";\" onclick=\"reply('".$line[2]."');\"><b>".$line[2].":</b> </span>"; // User
                    }

                    if(!$skip) $output.=$line[3];
                    if($readback) $output.="</span>";
                    if(!$skip) $output.="<br>\n";

                    $_SESSION['last']=$line[0];
                }

                $i--;
            }

            if($output<>"" && !$readback) {
                $_SESSION['log'].=$output;
                echo "message".$output;
            }
            
            flock($fh,LOCK_UN);
        }

        fclose($fh);
    }

    include("./php/live.php");
    if(!$readback) session_write_close();
?>
