<?php

session_start();

$_SESSION['page'] = '/aleph/';

if(empty($_SESSION['auth'])) {
	header('Location: /');
	exit();
}

try {
	$db = new SQlite3('/var/www/data/devel.db');
} catch (Exception $e) {
	$db = null;
}

if ($db) {
	$access = "SELECT * FROM module_group WHERE module = 'aleph' AND access_group = '" . $_SESSION['group'] . "';";
	if (!$db->querySingle($access)) {
		$_SESSION['error'] = 'Nedostatečné oprávnění.';
		header('Location: /main/');
		exit();
	}
} else {
	$_SESSION['error'] = 'Chyba čtení databáze.';
	header('Location: /main/');
	exit();
}

$error=False;

if (!empty($_POST)) {

	$url='http://localhost:8983/solr/' . $_POST['index'] . '/select';

	$wt='wt=' . $_POST['wt'];

	$csv_separator='csv.separator=' . urlencode(';');
	if (!empty($_POST['csv-separator'])) { $csv_separator='csv.separator=' . urlencode($_POST['csv-separator']); }
	$csv_mv_separator='csv.mv.separator=' . urlencode('#');
	if (!empty($_POST['csv-mv-separator'])) { $csv_mv_separator='csv.mv.separator=' . urlencode($_POST['csv-mv-separator']); }

	$q_op='q.op=' . $_POST['op'];

	$fl='fl=id';
	$select=array();
	$default=array('index','op','rows','csv-separator','csv-mv-separator','wt');
	foreach($_POST as $key=>$val) {
		if (!in_array($key, $default)) {
			if (strpos($key, 'query') === false) {
				array_push($select, $key);
			}
		}	
	}
	if (!empty($select)) { $fl=$fl . urlencode(',' . implode(',', $select)); }

	$q='q=';
	$query=array();
	foreach($_POST as $key=>$val) {
		if (strpos($key, 'query') !== false) {
			if (!empty($val)) {
				array_push($query, $val);
			}
		}
	}
	if (!empty($query)) {
		$q.=urlencode(implode(' ' . $q_op . ' ', $query));
	} else { $q.=urlencode('*:*'); }
	
	$rows='rows=10';
	if (!empty($_POST['rows'])) {
		if (intval($_POST['rows']) > 0) { $rows='rows=' . strval(intval($_POST['rows'])); }
	} else { $rows='rows=1000000'; }

	$params=array($csv_separator, $csv_mv_separator, $fl, $q_op, $q, $rows, $wt);

	$request=$url . '?' . implode('&', $params);

	//print($request);
	//exit();	

	$context = stream_context_create(array('http'=>array('method'=>'GET')));

	$fp = fopen($request, 'r', false, $context);

	if ($fp != false) {
		if($_POST['wt'] == 'csv') { header('Content-type: application/vnd.ms-excel; charset=UTF-8'); }
		if($_POST['wt'] == 'json') { header('Content-type: application/json; charset=UTF-8'); }
		if($_POST['wt'] == 'xml') { header('Content-type: application/xml; charset=UTF-8'); }

		header('Content-disposition: attachment;filename=' . $_POST['index'] . '-' . strftime('%Y%m%d%H%M', time()) . '.' . $_POST['wt']);
		header('Content-Encoding: UTF-8');

		if($_POST['wt'] == 'csv') { echo "\xEF\xBB\xBF"; }# UTF-8 BOM

		while(!feof($fp)) {
			$buffer = fread($fp, 2048);
			print $buffer;
		}
		fclose($fp);
		exit();
	} else { $error=True; }
}

?>

<!doctype html>
<html lang="cs">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>ČLB Vývoj - Aleph Solr</title>
	<!-- Favicons -->
	<link rel="apple-touch-icon" href="../favicon/apple-touch-icon.png" sizes="180x180">
	<link rel="icon" href="../favicon/favicon-32x32.png" sizes="32x32" type="image/png">
	<link rel="icon" href="../favicon/favicon-16x16.png" sizes="16x16" type="image/png">
	<link rel="mask-icon" href="../favicon/safari-pinned-tab.svg" color="#7952b3">
	<!-- Custom styles -->
	<link href="../custom.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar container-fluid navbar-expand-md navbar-dark" style="background-color:#dc3545;">
	<div class="row align-items-center gx-0">
		<div class="col">
			<svg width="32" height="32" fill="currentColor" class="bi bi-clb-logo ms-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 314.4 226.08"><path d="M232.76 10.3c-2.08 5.68-11.64 32-21.27 58.46-9.63 26.45-17.4 48.19-17.22 48.33.32.31 15.1 5.68 15.53 5.68.28 0 42.61-116.1 42.61-116.84 0-.32-14.6-5.9-15.52-5.93-.21 0-2.08 4.66-4.13 10.3zM296.76 17c-30.2 82.94-36.3 99.98-35.95 100.16 1.13.6 15.45 5.68 15.6 5.54.45-.46 38.23-104.88 37.99-105.1-.32-.31-14.93-5.6-15.42-5.6-.21 0-1.2 2.25-2.22 5zM192.93 28.5c-3.66 9.6-32.03 88.16-31.92 88.41.2.57 15.41 5.96 15.73 5.61.28-.28 32.46-88.51 32.46-88.97 0-.1-2.58-1.13-5.75-2.26-3.14-1.13-6.7-2.43-7.94-2.93l-2.22-.8zM259.19 29.03l-16.27 44.7c-8.64 23.74-15.59 43.29-15.45 43.43.39.35 15.6 5.71 15.74 5.57.24-.28 32.3-88.58 32.3-89 0-.22-2.32-1.27-5.18-2.3-2.89-1.02-6.45-2.33-7.93-2.89l-2.68-.99zM.25 36.9C.1 37.04 0 40.64 0 44.87v7.7h16.59v160.51H0l.07 6.42.1 6.45 46.93.11c55.8.1 59.58-.04 69.81-2.61a58.4 58.4 0 0 0 13.8-5.12c14.99-7.37 24.4-19.01 27.55-34 .91-4.3.91-14.71.03-19.58-.81-4.38-2.71-10.06-4.55-13.44-7.44-13.97-23.67-23.32-48.44-27.87-2.85-.53-3.2-.36 4.77-2.08 27.83-6.07 40.57-19.09 40.57-41.46 0-20.92-12.99-35.84-35.7-41.1-8.47-1.93-6.85-1.86-62.73-2-28.43-.08-51.82-.04-51.96.1zm83.08 16.65c12.38 2.97 20.56 10.4 23.63 21.6 1.03 3.8 1.27 11.25.5 15.16-2.47 12.1-11.05 20.82-24.13 24.41-2.44.67-3.85.78-13.55.92l-10.87.14V52.5l10.7.18c9.17.14 11.07.25 13.72.88zM76.94 133c23.81 2.36 39.97 20.1 38.63 42.47-1.02 17.22-11.08 30.02-27.62 35.28-5.44 1.7-8.43 2.05-19.05 2.22l-9.99.18V132.64h7.2c3.95 0 8.82.18 10.83.36z"/></svg>
		</div>
		<div class="col"><a class="navbar-brand nav-link active" href="/main/">Vývoj # Aleph Solr</a></div>
	</div>
	<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<a href="/logout.php" class="btn btn-sm mx-1 ms-auto btn-outline-light">Odhlásit</a>
		<span class="mx-2 align-middle"><b><?php echo $_SESSION['username'];?></b></span>
		<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16"><path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/><svg/>
	</div>
</nav>

<main class="container">
<div class="row my-4 justify-content-center">
<div class="col col-md-6">

<form method="post" action="." enctype="multipart/form-data">

<?php
	if($error) { echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">Neplatný dotaz.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'; }
?>

<div class="row mt-4 justify-content-center">
	<div class="col-md-8">
		<div class="d-grid gap-2 d-sm-flex">
			<input type="radio" class="btn-check" id="uclo" name="index" value="uclo" onclick="get_selection('uclo')" checked>
			<label class="btn btn-outline-danger w-100" for="uclo">UCLO</label>
			<input type="radio" class="btn-check" id="clo" name="index" value="clo" onclick="get_selection('clo')">
			<label class="btn btn-outline-danger text-nowrap w-100" for="clo">CLO</label>
			<input type="radio" class="btn-check" id="aut" name="index" value="aut" onclick="get_selection('aut')">
			<label class="btn btn-outline-danger text-nowrap w-100" for="aut">AUT</label>
			<input type="radio" class="btn-check" id="cnb" name="index" value="cnb" onclick="get_selection('cnb')">
			<label class="btn btn-outline-danger text-nowrap w-100" for="cnb">CNB</label>
		</div>
	</div>
</div>

<div class="row mt-3 gx-0 justify-content-center">
	<div class="col col-md">
		<div class="form-floating">
			<input type="text" class="form-control" id="q" name="query0" value="<?php if (isset($_POST['query0'])) { echo htmlspecialchars($_POST['query0'], ENT_QUOTES, 'UTF-8'); } ?>"><label for="q">Podmínka</label>
		</div>
	</div>
	<div class="col-auto ps-0 m-0 d-flex align-items-center">
		<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" onclick="help()" fill="currentColor" class="bi bi-info" viewBox="0 0 16 16"><path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/></svg>
	</div>
</div>

<div class="row mt-3 gx-2 justify-content-center">
	<div class="col-4 col-md-2">
		<input type="radio" class="btn-check" id="or" name="op" value="OR" checked>
		<label class="btn btn-outline-danger w-100" for="or">OR</label>
	</div>
	<div class="col-4 col-md-2">
		<input type="radio" class="btn-check" id="and" name="op" value="AND">
		<label class="btn btn-outline-danger text-nowrap w-100" for="and">AND</label>
	</div>
</div>

<h4 class="my-2">Pole</h4>
<div class="row row-cols-sm-5 mx-2 my-3">

<?php

$field = [
	'Pole' => array(
	'LDR','001','003','005','008','015','020','022','024','035','040','041','044','046','072','080','100','110','111','130',
	'245','246','250','260','264','300','336','337','338','368','370','371','372','373','374','375','377','400','490','500',
	'505','506','520','594','599','600','610','611','630','648','650','651','653','655','663','664','665','667','670','675',
	'678','680','682','700','710','711','730','773','787','830','856','906','910','928','961','962','963','964','965','966',
	'967','990','994','995','OWN','CAT','KAT','KON','POS','POZ','VER','SYS','SIF','STA','ZAZ','ZAR'
	)
];

foreach($field as $name=>$tags) {
	foreach($tags as $t) {
		echo '<div class="col-3 col-md-2"><div class="form-check">'
		. '<input class="form-check-input" type="checkbox" id="' . $t . '" name="field_' . $t . '" value="1">'
		. '<label class="form-check-label" for="'. $t . '">' . $t . '</label>'
		. '</div></div>';
	}
}

?>

</div>

<hr/>
<h4 class="my-2">Podpole</h4>
<div class="row row-cols-sm-5 mx-2 my-3">

<?php

$subfield = [
	'100' => array('4'),
	'110' => array('4'),
	'111' => array('4'),
	'245' => array('a','b','c','n','p'),
	'505' => array('t','r','g'),
	'700' => array('4'),
	'710' => array('4'),
	'711' => array('4'),
	'773' => array('a','t','x','n','b','d','h','k','g','q','z','y','9'),
	'787' => array('i','a','t','n','b','d','h','k','g','z','y','4')
];

foreach($subfield as $field=>$subs) {
	foreach($subs as $s) {
		echo '<div class="col-3 col-md-2"><div class="form-check">'
		. '<input class="form-check-input" type="checkbox" id="' . $field . '-' . $s . '" name="subfield_'.$field . '-' . $s . '" value="1">'
		. '<label class="form-check-label" for="'. $field . '-' . $s . '">' . $field . '-' . $s . '</label>'
		. '</div></div>';
	}
}

?>

</div>

<hr/>
<h4 class="my-2">Ostatní</h4>
<div class="row row-cols-sm-5 mx-2 my-3">
<div class="col-3 col-md-3">
	<div class="form-check">
		<input class="form-check-input" type="checkbox" id="local_LDR-8" name="local_LDR-8" value="1">
		<label class="form-check-label" for="local_LDR-8">LDR-8</label>
	</div>
</div>
<div class="col-3 col-md-3">
	<div class="form-check">
		<input class="form-check-input" type="checkbox" id="local_008-16" name="local_008-16" value="1">
		<label class="form-check-label" for="local_008-16">008-16</label>
	</div>
</div>
<div class="col-3 col-md-3">
	<div class="form-check">
		<input class="form-check-input" type="checkbox" id="local_008-7" name="local_008-7" value="1">
		<label class="form-check-label" for="local_008-7">008-7</label>
	</div>
</div>
<div class="col-3 col-md-3">
	<div class="form-check">
		<input class="form-check-input" type="checkbox" id="local_008-811" name="local_008-811" value="1">
		<label class="form-check-label" for="local_008-811">008-811</label>
	</div>
</div>
<div class="col-3 col-md-3">
	<div class="form-check">
		<input class="form-check-input" type="checkbox" id="local_008-815" name="local_008-815" value="1">
		<label class="form-check-label" for="local_008-815">008-815</label>
	</div>
</div>
<div class="col-3 col-md-3">
	<div class="form-check">
		<input class="form-check-input" type="checkbox" id="local_008-1618" name="local_008-1618" value="1">
		<label class="form-check-label" for="local_008-1618">008-1618</label>
	</div>
</div>
<div class="col-3 col-md-3">
	<div class="form-check">
		<input class="form-check-input" type="checkbox" id="local_008-3638" name="local_008-3638" value="1">
		<label class="form-check-label" for="local_008-3638">008-3638</label>
	</div>
</div>
</div>

<hr/>
<h4 class="my-2">Výstup</h4>
<div class="row my-4 justify-content-center">
	<div class="col">
		<div class="d-grid gap-2 d-sm-flex">
			<div class="form-floating">
				<input type="text" class="form-control" id="rows" name="rows" value="10"><label for="rows">Počet řádků</label>
			</div>
			<div class="form-floating">
				<input type="text" class="form-control" id="csv-separator" name="csv-separator" value=";"><label for="csv-separator">Oddělovač polí</label>
			</div>
			<div class="form-floating">
				<input type="text" class="form-control" id="csv-mv-separator" name="csv-mv-separator" value="#"><label for="csv-mv-separator">Oddělovač hodnot</label>
			</div>
		</div>
	</div>
</div>

<div class="row mt-4 justify-content-center">
	<div class="col-md-8">
		<div class="d-grid gap-2 d-sm-flex">
			<input type="radio" class="btn-check" id="csv" name="wt" value="csv" checked>
			<label class="btn btn-outline-danger w-100" for="csv">CSV</label>
			<input type="radio" class="btn-check" id="json" name="wt" value="json">
			<label class="btn btn-outline-danger text-nowrap w-100" for="json">JSON</label>
			<input type="radio" class="btn-check" id="xml" name="wt" value="xml">
			<label class="btn btn-outline-danger text-nowrap w-100" for="xml">XML</label>
		</div>
	</div>
</div>

<hr/>
<div class="d-grid col-md-4 mx-auto my-4">
	<button type="submit" class="btn btn-danger">Odeslat</button>
</div>

</form>

</div>
</div>
</main>

<!-- Modal -->
<div class="modal fade" id="help" tabindex="-1" aria-labelledby="help" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable">
	<div class="modal-content">
	<div class="modal-header">
		<h5 class="modal-title" id="staticBackdropLabel">Nápověda - Apache Solr</h5>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	</div>
	<div class="modal-body">
		<h5>Indexy</h5>
		<p>Pole, podpole a speciální hodnoty se zapisují pomocí pojmenovaných indexů s&nbsp;předponou podle typu.</p>
		<div class="alert alert-warning" role="alert">
			<b>field_</b>500<br>
			<b>subfield_</b>787-a<br>
			<b>local_</b>008-16
		</div>
		<h5>Hodnoty</h5>
		<p>Hledané hodnoty jsou odděleny dvojtečkou. Zástupné znaky hvězdička, otazník, uvozovky nebo hranaté závorky nahrazují libovolný, částečný, nebo složený text včetně rozsahu.</p>
		<div class="alert alert-warning" role="alert">
			field_500<b>:*</b><br>
			subfield_264-a<b>:Pra?a</b><br>
			subfield_338-a<b>:"online zdroj"</b><br>
			subfield_912-r<b>:[2001 TO 2022]</b>
		</div>
		<h5>Logické operátory</h5>
		<p>Operátory a&nbsp; kulaté závorky spojují kombinované podmínky.</p>
		<div class="alert alert-warning" role="alert">
			<b>(</b>field_100:* <b>OR</b> field_110:*<b>) AND</b> field_964:*
		</div>
		<h5>Rozhraní</h5>
		<p>Zaškrtávací políčka pod podmínkou určují jaké hodnoty budou součástí výstupu. Pole a&nbsp;podpole výstupu nemusí odpovídat polím a&nbsp;podpolím podmínky. Indentifikátor záznamu se vkládá automaticky. Prázdné pole "počet řádků" vrátí všechny dostupné záznamy.</p> 
		<h5>Příklad</h5>
		<p>Všechny záznamy které obsahují libovolné pole 856 a&nbsp;pole 964 s&nbsp;hodnotou INT.</b>
		<div class="alert alert-warning" role="alert">
			field_856:* AND field_964:INT
		</div>
		<h5>Více</h5>
		<p><a class="link-danger" href="https://solr.apache.org/guide/8_1/the-standard-query-parser.html#specifying-terms-for-the-standard-query-parser" target="_blank">https://solr.apache.org/guide/8_1/the-standard-query-parser.html#specifying-terms-for-the-standard-query-parser</a>
	</div>
	</div>
	</div>
</div>

<script src="../bootstrap.min.js"></script>
<script src="custom.js"></script>

</body>
</html>

