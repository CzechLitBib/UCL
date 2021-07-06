<?php

session_start();

$_SESSION['page'] = 'nkp';

if(empty($_SESSION['auth'])) {
	header('Location: /');
	exit();
}

?>

<html>
<head></head>
<body bgcolor="lightgrey">
<div align="center">
<table><tr><td><img src="/sova.png"><td><td>Statistika NKP</td></tr></table>
<p><hr width="500"></p>
</div>
</body>
</html>

