<?php

putenv('GDFONTPATH=' . realpath('.'));

// session
$session = session_start();

$captcha = '';
$abc = '0123456789';

// image
$im = imagecreate(130, 40);
$pastel = imagecolorallocate($im, 210, 210, 210);
$black = imagecolorallocate($im, 0, 0, 0);

imagerectangle($im, 0, 0, 129, 39, $black);

for($i=0; $i < 5; $i++) {
	$ran = rand(0, 4);
	$captcha .= $abc[rand(0, strlen($abc)-1)];
	imagettftext($im, 22+$ran, 0, 15+(20*$i), 30, $black, 'font', $captcha[$i]);
}

// code
$_SESSION['captcha'] = $captcha;

// header
header('Content-Type: image/png');
header("Cache-Control: no-cache, must-revalidate"); 
header("Expires: Sat, 26 Jul 2042 05:00:00 GMT");

imagepng($im);
imagedestroy($im);

?>
