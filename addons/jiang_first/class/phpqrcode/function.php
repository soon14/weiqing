<?php
function gen_qrcode($str,$size = 5)
{
	include "qrlib.php";
	$root_dir = 'temp/';
 	if (!is_dir($root_dir)) {@mkdir($root_dir);@chmod($root_dir, 0777);}
    $filename = md5($str."|".$size);
	$filesave = $root_dir.$filename.'.png';
	if(!file_exists($filesave)){QRcode::png($str, $filesave, 'Q', $size, 2);}
	return $root_dir.$filename.".png";
}
?>