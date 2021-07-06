<?php

session_start();

$_SESSION['page'] = 'weekly';

if(empty($_SESSION['auth'])) {
	header('Location: /');
	exit();
}

?>

<html>
<head></head>
<body bgcolor="lightgrey">
<div align="center">
<table><tr><td><img src="/sova.png"><td><td>Kontrola Aleph protokolem OAI-PMH.</td></tr></table>
<p><hr width="500"></p>
<form action="." method="post">

<?php

echo '<input type="date" name="date" value="' . $today . '" min="2019-01-01" max="'. $today . '" step="7">';

?>

</form>
<p><hr width="500"></p>
</div>
</body>
</html>

