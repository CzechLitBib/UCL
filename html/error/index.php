<?php

session_start();

$_SESSION['page'] = 'error';

if(empty($_SESSION['auth'])) {
	header('Location: ../index.php');
	exit();
}

?>

<html>
<head><meta charset="utf-8"></head>
<body bgcolor="lightgrey">
<div align="center">
<table><tr><td><img src="/sova.png"></td><td>Popis chybových zpráv</td></tr></table>

<?php

$db = new SQLite3('error.db');

if (!$db) {
	echo '<p><font color="red">DB error.</font></p>';
} else {

	$result = $db->query("SELECT id,label,text FROM error");
	
	while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
		echo '<p><table cellpadding="5" width="700">';
		echo '<tr><td width="42" align="center"><font color="red"><b>' . $res['id'] . '</b></font></td><td width="650"><b>' . $res['label'] . '</b></td></tr>';
		echo '<tr><td></td><td width="650"><div align="justify">' . $res['text'] . '</div></td></tr>';
		echo '</table></p>';
		echo '<p><hr width="650" style=" border:none; height:1px; background-color: black;"></p>';
	}
	
	$db->close();
}

?>

<br>
</div>
</body>
</html>

