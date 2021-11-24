<?php

session_start();

$_SESSION['page'] = 'aktualizace';

if(empty($_SESSION['auth'])) {
	header('Location: /');
	exit();
}

if($_SESSION['group'] !== 'admin') {
        $_SESSION['error'] = True;
        header('Location: /main/');
        exit();
}

?>


<html>
<head></head>
<body bgcolor="lightgrey">
<div align="center">
<table><tr><td><img src="/sova.png"></td><td>Formulář pro aktualizaci kontrolních seznamů.</td></tr></table>
<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>
<form action="." method="post">

<?php

$pole = ['600','610','620'];

foreach ($pole as $p) {
	echo '<label>Pole ' . $p . ': </label><input type="text" name="' . $p . '" size="30"><input type="submit" value="Uložit"><br>';
}

foreach ($pole as $p) {
	if (array_key_exists($p, $_POST)) {
		if (!empty($_POST[$p])) {
			$f = fopen($p . '.txt', 'a');
			fwrite($f, $_POST[$p] . "\n");
		}
	}
}

?>

</form>
<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>
<table width='500'><tr><td width="450" align="right"><a href="/main"><img src="/back.png"></a></td></tr></table>

<?php
if (!empty($_POST)) { echo '<font color="red"><b>Hotovo.</b></font>'; }
?>

</div>
</body>
</html>

