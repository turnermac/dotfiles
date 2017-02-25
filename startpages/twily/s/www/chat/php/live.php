<?php
    $live="";
    $list="";

    $timedout=false;
    if($_SESSION['nick']<>"" && $_SESSION['active']<time()-$sessionTimeout) { // Disconnect if timeout and nick is taken
        $timedout=true;
    }

    $_SESSION['active']=time();

    $fh=fopen($userfile,'r+');

    if(flock($fh,LOCK_EX)) { // Update user list
        $lines=explode("\n",fread($fh,filesize($userfile)));
        $user=$separator.$_SESSION['color'].$separator.$_SESSION['nick'];
        $found=false;

        for($i=0;$i<count($lines)-1;$i++) {
            $line=explode($separator,$lines[$i]);
            if($line[2]==$_SESSION['nick'] && $timedout) $forceleave=true;

            if($_SESSION['active']<$line[0]+$sessionTimeout) {
                if($line[2]==$_SESSION['nick'] && $line[1]==$_SESSION['color']) { // Update self
                    if($line[3]=="kick") {
                        $_SESSION['kicked']=intval($_SESSION['active']+$line[4]);
                        $forceleave=true;

                        $live.=$timestamp.$separator.$line[1].$separator.$line[2].$separator."has been kicked from the chat.".$separator.$servermsg."\n";
                    }

                    $admin="";
                    if($_SESSION['admin']) $admin=$separator.$admsym;
                    if($forceleave) {
                        $list.=($_SESSION['active']-$sessionTimeout).$separator.$_SESSION['color'].$separator.$_SESSION['nick'].$separator."active".$admin."\n";
                        unset($_SESSION['nick']);
                        unset($_SESSION['color']);

                        echo "update";
                    } else {
                        $list.=$_SESSION['active'].$user.$admin."\n";
                    }

                    $found=true;
                } else { // Keep active
                    $list.=$lines[$i]."\n";
                }
            } elseif($_SESSION['active']<$line[0]+$sessionTimeout+$notifyexclude) { // Dispose inactive
                $timedout="";
                if($line[3]!="active") $timedout=" (timeout ".$sessionTimeout."s)";
                $live.=$timestamp.$separator.$line[1].$separator.$line[2].$separator."has left the chat".$timedout.".".$separator.$servermsg."\n";
            }
        }

        if(!$found && $_SESSION['nick']) { // Add self
            $list.=$_SESSION['active'].$user."\n";
            $live.=$timestamp.$user.$separator."has joined the chat.".$separator.$servermsg."\n";
        }

        ftruncate($fh,0);
        rewind($fh);
        fwrite($fh,$list);

        flock($fh,LOCK_UN);
    }

    fclose($fh);
    

    if($live<>"") {
        $fh=fopen($chatfile,'a');
        
        if(flock($fh,LOCK_EX)) { // Post users that has timed out / left the chat
            fwrite($fh,$live);

            flock($fh,LOCK_UN);
        }

        fclose($fh);
    }
?>
