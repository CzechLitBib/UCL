<?php

session_start();

if(empty($_SESSION['auth']) or $_SESSION['group'] !== 'admin') {
	header('Location: /');
	exit();
}

$_SESSION['page'] = 'aleph';

if(!isset($_SESSION['aleph'])) { $_SESSION['aleph'] = Null; }

// session store + redirect
#if (!empty($_POST['data'])) {
#	$_SESSION['daily'] = $_POST['date'];
#	header("Location: " . $_SERVER['REQUEST_URI']);
#	exit();
#}
//session re-evaluation...

if (!empty($_POST)) {

	$url='http://localhost:8983/solr/core/select';

	$wt='wt=csv';

	$csv_separator='csv.separator=' . urlencode(';');
	if (!empty($_POST['csv-separator'])) { $csv_separator='csv.separator=' . urlencode($_POST['csv-separator']); }
	$csv_mv_separator='csv.mv.separator=' . urlencode('#');
	if (!empty($_POST['csv-mv-separator'])) { $csv_mv_separator='csv.mv.separator=' . urlencode($_POST['csv-mv-separator']); }

	$q_op='q.op=';
	$q_op.=$_POST['op'] ? 'OR' : 'AND';

	$fl='fl=id';
	$select=array();
	$default=array('query','op','rows','csv-separator','csv-mv-separator');
	foreach($_POST as $key=>$val) {
		if (!in_array($key, $default)) { array_push($select, $key) ; }
	}
	if (!empty($select)) { $fl=$fl . urlencode(',' . implode(',', $select)); }

	$q='q=';
	if (!empty($_POST['query'])) { $q='q=' . urlencode($_POST['query']); }
	//if (!empty($select)) {
	//	foreach($select as $s) {
	//		if (strpos($q, $s) == false) {
	//			$q.=urlencode(' ' . $s . ':*');
	//		}
	//	}
	//}
	//if(empty($_POST['query']) and empty($select)) { $q='q=' . urlencode('*:*'); }
	if(empty($_POST['query'])) { $q='q=' . urlencode('*:*'); }

	$rows='rows=10';
	if (!empty($_POST['rows'])) {
		if (intval($_POST['rows']) > 0) { $rows='rows=' . strval(intval($_POST['rows'])); }
	} else { $rows='rows=1000000'; }

	$params=array($csv_separator, $csv_mv_separator, $fl, $q_op, $q, $rows, $wt);

	$request=$url . '?' . implode('&', $params);

	//print($request);
	//exit();	

	header('Content-type: application/octet-stream; charset=UTF-8');
	header('Content-disposition: attachment;filename=' . 'solr-' . strftime('%Y%m%d%H%M%S', time()) . '.csv');

	$opts = array('http'=>array('method'=>'GET'));

	$context = stream_context_create($opts);

	$fp = fopen($request, 'r', false, $context);

	if ($fp != false) { 
		while(!feof($fp)) {
			$buffer = fread($fp, 2048);
			print $buffer;
		}
	}
	if ($fp != false) { fclose($fp); }
	exit();
}

?>

<!DOCTYPE html>
<html><head><title>Aleph Solr</title><meta charset="utf-8"></head>
<body bgcolor="lightgrey">
<div align="center">
<table><tr><td><img src="/sova.png"/></td><td>Aleph Solr</td></tr></table>
<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>
<form method="post" action="." enctype="multipart/form-data">
<table width="500"><tr><td width="100"><b>Podmínka:</b></td><td><input type="text" name="query" size="40" value=""></td><td><img src="/form/help.png" title="Prefix:

Pole            tag_
Podpole     sub_
Ostatní       spec_

Příklad:

spec_008-815:[1995 TO *] spec_LDR-8:b
"></td></tr>
</table>
<table><tr><td colspan="2">
<input type="radio" name="op" value="1" checked><label>OR</label>
<input type="radio" name="op" value="0"><label>AND</label>
</td></tr></table>
<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>

<?php

$field = [
	'Pole' => array('LDR','001','003','005','008','015','020','022','035','040','041','044','072','080','100','110','111','130',
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
		echo '<td width="100"><input type="checkbox" name="' . 'tag_' . $t . '" value="1"><label>' . $t . '</label></td>';
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
		echo '<td width="100"><input type="checkbox" name="'
			. 'sub_' . $field .'-' . $s . '" value="1"><label>'
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

echo '<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>';

echo '<table width="500">';
echo '<tr><td colspan="5"><b>Ostatní</b></td></tr><tr>';
echo '<tr><td></td></tr>';
echo '<tr>
<td><input type="checkbox" name="spec_LDR-8" value="1"><label>LDR-8</label></td>
<td><input type="checkbox" name="spec_008-16" value="1"><label>008-16</label></td>
<td><input type="checkbox" name="spec_008-7" value="1"><label>008-7</label></td>
<td><input type="checkbox" name="spec_008-811" value="1"><label>008-811</label></td>
<td><input type="checkbox" name="spec_008-815" value="1"><label>008-815</label></td>
</tr>';
echo '<tr>
<td><input type="checkbox" name="spec_008-1618" value="1"><label>008-1618</label></td>
<td><input type="checkbox" name="spec_008-3638" value="1"><label>008-3638</label></td>
</tr>';
echo '</table>';

echo '<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>';

echo '<table width="500">';
echo '<tr><td colspan="5"><b>Výstup</b></td></tr><tr>';
echo '<tr><td></td></tr>';
echo '<tr><td><input type="text" name="rows" size="1" value="10"><label> Počet řádků.</label></td></tr>';
echo '<tr><td><input type="text" name="csv-separator" size="1" value=";"><label> CSV oddělovač.</label></td></tr>';
echo '<tr><td><input type="text" name="csv-mv-separator" size="1" value="#"><label> CSV oddělovač opakovatelných hodnot.</label></td></tr>';
echo '</table>';

?>

<p></p>
<table width="500"><tr><td align="middle"><input style="font-size: 14px; padding: 5px 24px;" type="submit" value="Odeslat"></td><td></tr></table>

</form>
<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>
<table width='500'><tr><td width="450" align="right"><a href="/main"><img src="/back.png"></a></td></tr></table>
</div>
</body>
</html>

