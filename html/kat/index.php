<?php

session_start();

$_SESSION['page'] = 'kat';

if(empty($_SESSION['auth'])) {
	header('Location: ../index.php');
	exit();
}

?>

<html>
<head></head>
<body bgcolor="lightgrey">
<div align="center">
<table><tr><td><img src="/sova.png"><td><td>Statistika podpole CAT/KAT</td></tr></table>
<p><hr width="500"></p>
<form action="." method="post">

<?php

echo '<input type="date" name="date" value="' . $today . '" min="2019-01-01" max="'. $today . '">';

?>

</form>
<p><hr width="500"></p>
</div>
</body>
</html>

