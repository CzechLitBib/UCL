<html>
<head></head>
<body bgcolor="lightgrey">

<div align="center">
<br><img src="/aktualizace/sova.png">
<br>
<p><b>Formulář pro aktualizaci kontrolních seznamů.</b></p>
<br>

<form method="post" action='index.php'>

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
<p><hr width="500"></p>

<?php

if (!empty($_POST)) { echo '<font color="red"><b>Hotovo.</b></font>'; }

?>

</div>
</body>
</html>


