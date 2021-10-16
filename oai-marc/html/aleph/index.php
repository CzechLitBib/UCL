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
<table width="500"><tr><td width="100"><b>Podmínka:</b></td><td><input type="text" name="query" size="40" value=""></td><td><img src="/form/help.png" title="Příklad:

year_008:[1995 TO *] AND leader_8:b
"></td></tr></table>
<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>

<?php

$field = [
	'Pole' => array('LDR','FMT','001','003','005','008','015','020','022','035','040','041','044','072','080','100','110','111','130',
	'245','246','250','260','264','300','336','337','338','490','500','505','506','520','599','600','610','611','630','648','650',
	'651','653','655','700','710','711','730','773','787','830','856','910','928','961','964','LKR','OWN','CAT','SYS','SIF','STA',
	'ZAZ','ZAR')
];

foreach($field as $name=>$tags) {
	echo '<table width="500">';
	echo '<tr><td colspan="5"><b>Pole</b></td></tr><tr>';
	echo '<tr><td></td></tr><tr>';
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

$subfield = [
	'245' => array('a','b','c','n','p'),
	'505' => array('t','r','g'),
	'773' => array('a','t','n','b','d','h','k','g','q','z','y','9'),
	'787' => array('i','a','t','n','b','d','h','k','g','z','y','9')
];

echo '<table width="500">';
echo '<tr><td colspan="5"><b>Podpole</b></td></tr><tr>';
echo '<tr><td></td></tr>';

foreach($subfield as $field=>$subs) {
	echo '<tr>';
	$cnt=0;
	foreach($subs as $s) {
		echo '<td width="100"><input type="checkbox" name="tag_'
			. $field .'-' . $s . '" id="100" value="tag_'
			. $field .'-' . $s . '"><label>'
			. $field .'-' . $s . '</label></td>';
		$cnt++;
		if ($cnt == 5) {
			echo '</tr><tr>';
			$cnt = 0;
		}
	}
	echo '</tr><tr><td></td></tr>';
}
echo '</table>';

?>

<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>
<table width="500"><tr><td align="middle"><input style="font-size: 14px; padding: 5px 24px;" type="submit" value="Odeslat"></td><td></tr></table>

</form>
<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>
<table width='500'><tr><td width="450" align="right"><a href="/main"><img src="/back.png"></a></td></tr></table>
</div>
</body>
</html>

