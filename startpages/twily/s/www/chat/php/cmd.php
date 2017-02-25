<?php
    $cmd=explode(" ",substr($message,1));
    $lprint="";

    switch($cmd[0]) {
        case "nick": case "name": case "n": // Join chat or change nick
            if($_SESSION['kicked']<time()) {
                $nick=preg_replace("/[^A-Za-z0-9]/","",substr(str_replace($separator,"",stripslashes(htmlspecialchars($cmd[1]))),0,$nicklength));
                $found=false;

                if($nick=="") $nick="guest".rand(100,999);

                $fh=fopen($userfile,'r');

                if(flock($fh,LOCK_EX)) {
                    $lines=explode("\n",fread($fh,filesize($userfile)));
                    for($i=0;$i<count($lines);$i++) {
                        $user=explode($separator,$lines[$i]);

                        if($user[2]==$nick && $_SESSION['nick']!=$nick) {
                            $found=true;
                            break;
                        }
                    }
                    
                    flock($fh,LOCK_UN);
                }

                fclose($fh);

                if(!$found) {
                    if($_SESSION['nick']<>"") {
                        $message="/leave";
                        include("./php/cmd.php");
                    }

                    $_SESSION['nick']=$nick;

                    $color=[];
                    for($i=0;$i<3;$i++) $color[$i]=floor((rand($colorMin,$colorMax)+$colorMix)/2);

                    $admin="";
                    if($_SESSION['admin']) $admin=$admsym;

                    $_SESSION['color']=dechex($color[0]).dechex($color[1]).dechex($color[2]);
                    $lprint=$servermsg."You are now chatting as <span style=\"color: #".$_SESSION['color']."\" onclick=\"reply('".$_SESSION['nick']."');\"><b>".$admin.$_SESSION['nick']."</b></span>.<br>\n";
                } else {
                    $lprint=$servermsg."Nickname &lt;".$nick."&gt; is already taken. Please choose a different name.<br>\n";
                }
            } else {
                $lprint=$servermsg."You have been kicked and must wait ".intval($_SESSION['kicked']-time())." seconds before you can rejoin.<br>\n";
            }

            break;
        case "kick": // Kick user w/ cooldown (req. admin)
            $nick=preg_replace("/[^A-Za-z0-9]/","",substr(str_replace($separator,"",stripslashes(htmlspecialchars($cmd[1]))),0,$nicklength));

            if(strlen($nick<>"") && $cmd[2]>=0 && $_SESSION['admin']) {
                $fh=fopen($userfile,'r+');

                $found=false;
                if(flock($fh,LOCK_EX)) {
                    $lines=explode("\n",fread($fh,filesize($userfile)));
                    $list="";
                    for($i=0;$i<count($lines);$i++) {
                        $user=explode($separator,$lines[$i]);

                        $list.=$user[0].$separator.$user[1].$separator.$user[2];
                        if($user[2]==$nick) {
                            $admin="";
                            if($user[3]==$admsym) $admin=$separator.$admsym;
                            $list.=$separator."kick".$separator.$cmd[2].$admin."\n";
                            $found=true;
                            $lprint=$servermsg."Flagging &lt;".$user[2]."&gt; for a kick lasting ".$cmd[2]." second(s).<br>\n";
                        } else {
                            $list.="\n";
                        }
                    }

                    if($found) {
                        ftruncate($fh,0);
                        rewind($fh);
                        fwrite($fh,$list);   
                    }

                    flock($fh,LOCK_UN);
                }

                fclose($fh);

                if(!$found) $lprint=$servermsg."Nickname &lt;".$nick."&gt; could not be kicked (Not found).<br>\n";
            } else {
                $lprint=$servermsg."Unable to execute 'kick'. (Format: &lt;nick&gt; &lt;sec&gt;).<br>\n";
            }
            break;
        case "admin": // Toggle admin on/off (req. password)
            if($cmd[1]==$password) {
                $_SESSION['admin']=true;
                $lprint=$servermsg."Administrative features have been enabled.<br>\n";
            } else {
                unset($_SESSION['admin']);
                $lprint=$servermsg."Administrative features have been disabled.<br>\n";
            }
            break;
        case "help": case "?": // Show help
            $lprint=$servermsg."<a class=\"cmd space\" href=\"javascript:q('list',1)\" target=\"_self\">/list</a> List users online.<br>\n"
                   .$servermsg."<a class=\"cmd space\" href=\"javascript:q('me ',0)\" target=\"_self\">/me %m</a> Third person messages.<br>\n"
                   .$servermsg."<a class=\"cmd space\" href=\"javascript:q('pm ',0)\" target=\"_self\">/pm %n %m</a> Private messages.<br>\n"
                   .$servermsg."<a class=\"cmd space\" href=\"javascript:q('nick ',0)\" target=\"_self\">/nick %n</a> Change nickname.<br>\n"
                   .$servermsg."<a class=\"cmd space\" href=\"javascript:q('seen ',0)\" target=\"_self\">/seen %n</a> Last seen active.<br>\n"
                   .$servermsg."<a class=\"cmd space\" href=\"javascript:q('size',1)\" target=\"_self\">/size</a> Toggle chatbox size.<br>\n"
                   .$servermsg."<a class=\"cmd space\" href=\"javascript:q('refresh',1)\" target=\"_self\">/refresh</a> Refresh this frame.<br>\n"
                   .$servermsg."<a class=\"cmd space\" href=\"javascript:q('ping',1)\" target=\"_self\">/ping</a> Check your ping.<br>\n"
                   .$servermsg."<a class=\"cmd space\" href=\"javascript:q('clear',1)\" target=\"_self\">/clear</a> Clear the chat.<br>\n"
                   .$servermsg."<a class=\"cmd space\" href=\"javascript:q('leave',1)\" target=\"_self\">/leave</a> Leave the chat.<br>\n"
                   .$servermsg."<a class=\"cmd space\" href=\"javascript:q('exit',1)\" target=\"_self\">/exit</a> Remove the chatbox.<br>\n"
                   ;

            break;
        case "list": case "ls": // Users online
            $list="";
            $fh=fopen($userfile,'r');
            $lines=explode("\n",fread($fh,filesize($userfile)));
            for($i=0;$i<count($lines)-1;$i++) {
                $line=explode($separator,$lines[$i]);
                $admin="";
                if($line[3]==$admsym) $admin=$admsym;
                if(time()<$line[0]+$sessionTimeout) {
                    $list.=" <span style=\"color: #".$line[1]."\" onclick=\"reply('".$line[2]."');\"><b>".$admin.$line[2]."</b></span>";
                }
            }

            if($list=="") $list=".";
            else          $list=": ".$list.".";
            $lprint=$servermsg."Users online (".(count($lines)-1).")".$list."<br>\n";

            break;
        case "clear": case "cls": // Destroy log session
            unset($_SESSION['log']);

            $allowrb=true;
            $message="/readback";
            include("./php/cmd.php");

            $message="/list";
            include("./php/cmd.php");

            $lprint=$servermsg."Use <a class=\"cmd\" href=\"javascript:q('help',1);\" target=\"_self\">/help</a> or <a class=\"cmd\" href=\"javascript:q('?',1);\" target=\"_self\">/?</a> to list available commands.<br>\n";

            break;
        case "readback": // Old messages
            if($_SESSION['last']<>0 && $allowrb) {
                $readback=true && $rbc=$readbackmsg;
                include("./read.php");

                $lprint=$output."\n"
                       .$servermsg."^ Last <".$readbackmsg."> messages.<br>\n";
            } else if(!$allowrb) {
                $message="/clear";
                include("./php/cmd.php");

                exit();
            }

            break;
        case "leave": case "part": // Destroy user session
            $forceleave=true;
            include("./php/live.php");

            break;
        case "size": case "embed": // Toggle embed / size
            $_SESSION['embed']=!$_SESSION['embed'];

            break;
        case "exit": case "quit": // Leave chat and hide window (until timeout)
            $message="/leave";
            include("./php/cmd.php");
            
            unset($_SESSION['log']);
            unset($_SESSION['embed']);

            $_SESSION['exit']=!$_SESSION['exit'];
            if(!$_SESSION['exit']) unset($_SESSION['arrived']);

            break;
        case "seen": case "active": // Last seen active
            $nick=preg_replace("/[^A-Za-z0-9]/","",substr(str_replace($separator,"",stripslashes(htmlspecialchars($cmd[1]))),0,$nicklength));

            if($nick<>"") {
                $fh=fopen($chatfile,'r');

                if(flock($fh,LOCK_EX)) {
                    $pos=-1;
                    $c_time=0;
                    $c_color='';
                    $cLine='';

                    while(fseek($fh,$pos,SEEK_END)!=-1) {
                        $char=fgetc($fh);

                        if($char=="\n") {
                            if($cLine<>"") {
                                $cLine=explode($separator,$cLine);

                                //if($cLine[2]==$nick && !$cLine[4]) {
                                if($cLine[2]==$nick || $cLine[2]==$admsym.$nick) {

                                    $c_time=intval($cLine[0]/1000);
                                    $c_color=$cLine[1];

                                    break;
                                }
                            }
                        } else {
                            $cLine=$char.$cLine;
                        }

                        $pos--;
                    }

                    $lprint="";
                    $mtime=time()-$c_time;
                    switch($mtime) {
                        case $mtime>=60*60*24*7*52: $mtime=floor($mtime/52/7/24/60/60). " year";    break;
                        case $mtime>=60*60*24*7:    $mtime=floor($mtime/7/24/60/60). " week";       break;
                        case $mtime>=60*60*24:      $mtime=floor($mtime/24/60/60)." day";           break;
                        case $mtime>=60*60:         $mtime=floor($mtime/60/60)." hour";             break;
                        case $mtime>=60:            $mtime=floor($mtime/60)." minute";              break;
                        default:                    $mtime.=" second";
                    }
                    $mtime.=(substr($mtime,0,strpos($mtime," "))>1)?"s ago":" ago";

                    if($c_time>0) {
                        $lprint.=$servermsg."<span style=\"color: #".$c_color.";\" onclick=\"reply('".$nick."');\"><b>".$nick."</b> </span> was last active ".$mtime.".<br>\n";
                    } else {
                        $lprint.=$servermsg."There is no recorded activity from user `".$nick."´.<br>\n";
                    }

                    flock($fh,LOCK_UN);
                }

                fclose($fh);
            }

            break;
        case "refresh": case "re":

            break;
        case "ping":
            $pingTime=shell_exec("ping -c1 ".$_SERVER['REMOTE_ADDR']."|grep '64 bytes'|sed -n -e 's/^.*time=//p'");
            $lprint=$servermsg."Your ping from &lt;".$_SERVER['HTTP_HOST']."&gt; is ".$pingTime.".<br>\n";

            break;
        case "me": // /me says something
            if($_SESSION['nick']<>"") {
                $message="";
                for($i=1;$i<count($cmd);$i++) { $message.=$cmd[$i]." "; }

                $extra=$separator."*";
                writeMessage($message,$extra);
            } else {
                $lprint=$servermsg."You must sign in to use this command.<br>\n";
            }

            break;
        case "pm": // /pm username message
            if($_SESSION['nick']<>"") {
                $message="";
                for($i=2;$i<count($cmd);$i++) { $message.=$cmd[$i]." "; }

                $fh=fopen($userfile,'r');

                if(flock($fh,LOCK_EX)) {
                    $data=fread($fh,filesize($userfile));

                    $list=explode("\n",$data);
                    for($i=0;$i<count($list);$i++) {
                        $cell=explode($separator,$list[$i]);
                        if($cell[2]==$cmd[1]) {
                            $cmdColor=$cell[1];
                            break;
                        }
                    }

                    flock($fh,LOCK_UN);
                }

                fclose($fh);

                if($cmdColor<>"") {
                    $extra=$separator."@".$cmd[1].$separator.$cmdColor;
                    writeMessage($message,$extra);
                } else {
                    $lprint=$servermsg."Nickname &lt;".$cmd[1]."&gt; was not found.<br>\n";
                }
            } else {
                $lprint=$servermsg."You must sign in to use this command.<br>\n";
            }

            break;
        default:
            $lprint=$servermsg."Command not found: `".$cmd[0]."´<br>\n"
                   .$servermsg."Use <a class=\"cmd\" href=\"javascript:q('help',1);\" target=\"_self\">/help</a> or <a class=\"cmd\" href=\"javascript:q('?',1);\" target=\"_self\">/?</a> to list available commands.<br>\n";
    }

    $_SESSION['log'].=$lprint;
    if(!$silent) echo "update";
?>
