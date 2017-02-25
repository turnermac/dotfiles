<?php
    function colortime($mtime) {
        $timespan=60*60*24*7*52;
        $colors=[
            [104,224,184],      // 0% (now)     (cyan)  [68E0B8]
            [246,106,121],      // 1%           (red)   [F66A79]
            [111,103,230],      // 15%          (blue)  [6F67E6]
            [ 96, 97, 99]       // 100%         (grey)  [606163]
        ];

        $c=count($colors)-1;
        $cR=$colors[$c][0];
        $cG=$colors[$c][1];
        $cB=$colors[$c][2];

        if($mtime<$timespan) {
            $sel=[$c-1,$c];
            if($mtime<(1*$timespan/100))       { $timespan=floor(1*$timespan/100);  $sel=[0,1]; }
            else if($mtime<(15*$timespan/100)) { $timespan=floor(15*$timespan/100); $sel=[1,2]; }

            $p=$mtime*100/$timespan;
                      
                      $cR=$colors[$sel[0]][0]-floor($p*($colors[$sel[0]][0]-$colors[$sel[1]][0])/100);
            if($cR<0) $cR=$colors[$sel[0]][0]+floor($p*($colors[$sel[1]][0]-$colors[$sel[0]][0])/100);

                      $cG=$colors[$sel[0]][1]-floor($p*($colors[$sel[0]][1]-$colors[$sel[1]][1])/100);
            if($cG<0) $cG=$colors[$sel[0]][1]+floor($p*($colors[$sel[1]][1]-$colors[$sel[0]][1])/100);

                      $cB=$colors[$sel[0]][2]-floor($p*($colors[$sel[0]][2]-$colors[$sel[1]][2])/100);
            if($cB<0) $cB=$colors[$sel[0]][2]+floor($p*($colors[$sel[1]][2]-$colors[$sel[0]][2])/100);
        }

        if($mtime>=60*60*24*7*52)   { $mtime=floor($mtime/52/7/24/60/60)." year"; }
        else if($mtime>=60*60*24*7) { $mtime=floor($mtime/7/24/60/60)." week"; }
        else if($mtime>=60*60*24)   { $mtime=floor($mtime/24/60/60)." day"; }
        else if($mtime>=60*60)      { $mtime=floor($mtime/60/60)." hour"; }
        else if($mtime>=60)         { $mtime=floor($mtime/60)." minute"; }
        else                        { $mtime=$mtime." second"; }
        $mtime.=(substr($mtime,0,strpos($mtime," "))>1)?"s ago":" ago";

        return array($mtime,"rgb(".$cR.",".$cG.",".$cB.")");
    }
?>
