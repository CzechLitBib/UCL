<?php

session_start();

if(empty($_SESSION['auth']) or $_SESSION['group'] !== 'admin') {
	header('Location: /');
	exit();
}

$_SESSION['page'] = 'aleph';

if(!isset($_SESSION['aleph'])) { $_SESSION['aleph'] = Null; }

//if (!empty($_POST['data'])) {
//	$_SESSION['daily'] = $_POST['date'];
//	header("Location: " . $_SERVER['REQUEST_URI']);
//	exit();
//}

?>

<!DOCTYPE html>
<html><head><title>Aleph Solr</title><meta charset="utf-8"></head>
<body bgcolor="lightgrey">
<div align="center">
<table><tr><td><img src="/sova.png"/></td><td>Aleph Solr</td></tr></table>
<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>
<form method="post" action="." enctype="multipart/form-data">
<table width="500"><tr><td width="100"><b>Podmínka:</b></td><td><input type="text" name="query" size="40" value=""></td><td><img src="/form/help.png" title=""></td></tr></table>
<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>

<?php

$field = [
	'Návěští' => array('LDR','FMT','001','003','005','008','015','020','022','035','040','041','044','072','080'),
	'Hlavní záhlaví' => array('100','110','111','130'),
	'Údaje o názvu a autorské odpovědnosti' => array('245','246','250','260','264')
];

foreach($field as $name=>$tags) {
	echo '<table width="500">';
	echo '<tr><td colspan="5"><b>' . $name . '</b></td></tr><tr>';
	echo '<tr><td></td></tr>';
	$cnt=0;
	foreach($tags as $t) {
		echo '<td width="100"><input type="checkbox" name="tag_'
			. $t . '" id="100" value="tag_'
			. $t . '"><label>'
			. $t . '</label></td>';
		$cnt++;
		if ($cnt == 5) {
			echo '</tr><tr>';
			$cnt = 0;
		}
	}
	echo '</tr></table>';
	echo '<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>';
}

?>

</table>
</form>
<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>
<table width='500'><tr><td width="450" align="right"><a href="/main"><img src="/back.png"></a></td></tr></table>
</div>
</body>
</html>

