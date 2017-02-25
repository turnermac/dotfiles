<?php
	error_reporting(0);

	$diff=time()-$time;

	if($diff>=60*60*24*7*52)   { $ptime=floor($diff/52/7/24/60/60)." year"; }
	else if($diff>=60*60*24*7) { $ptime=floor($diff/7/24/60/60)." week"; }
	else if($diff>=60*60*24)   { $ptime=floor($diff/24/60/60)." day"; }
	else if($diff>=60*60)      { $ptime=floor($diff/60/60)." hour"; }
	else if($diff>=60)         { $ptime=floor($diff/60)." minute"; }
	else                       { $ptime=$diff." second"; }

	$ptime.=(substr($ptime,0,strpos($ptime," "))<>1)?"s":"";
?>
