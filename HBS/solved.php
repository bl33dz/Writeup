<?php
$x = file_get_contents("access.log");
preg_match_all('/HEAD \/(.*?)\/ HTTP/', $x, $res);
foreach($res[1] as $biner) {
	echo $biner." ";
}	
