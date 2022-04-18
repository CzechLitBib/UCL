<?php

putenv('GDFONTPATH=' . realpath('.'));

// session
session_start();

$secret = '';
$abc = '0123456789';

// image
$im = imagecreate(130, 45);
$pastel = imagecolorallocate($im, 210, 210, 210);
$black = imagecolorallocate($im, 0, 0, 0);

imagerectangle($im, 0, 0, 129, 44, $black);

for($i=0; $i < 5; $i++) {
	$ran = rand(0, 4);
	$secret .= $abc[rand(0, strlen($abc)-1)];
	imagettftext($im, 22+$ran, 0, 15+(20*$i), 34, $black, 'font', $secret[$i]);
}

// code
$_SESSION['secret'] = $secret;

// header
header('Content-Type: image/png');
header('Cache-Control: no-cache, must-revalidate'); 
header('Expires: Sat, 26 Jul 2042 05:00:00 GMT');

imagepng($im);
imagedestroy($im);

?>
