<?php

include('solr.php');

$q='';

$options = array
(
	'secure' => SOLR_SECURE,
	'hostname' => SOLR_SERVER_HOSTNAME,
	'port' => SOLR_SERVER_PORT,
	'path' => SOLR_PATH,
);

$client = new SolrClient($options);
$query = new SolrQuery();

# q
if (isset($_GET['lookfor'])) { $q=$_GET['lookfor']; }
$query->setQuery($q);
# fq
if (isset($_GET['filter'])) {
	foreach ($_GET['filter'] as $fq) {
		$query->addFilterQuery($fq);
	}
}

# start
$query->setStart(0);
# rows
$query->setRows(SOLR_QUERY_LIMIT);
# fl
$query->addField('id');

$query_response = $client->query($query);
$response = $query_response->getResponse();

print_r($response);

?>

<!doctype html>
<html lang="cs">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>ČLB Vufind - Cards</title>
	<link href="bootstrap.min.css" rel="stylesheet">
	<!-- Favicons -->
	<link rel="apple-touch-icon" href="/cards/favicon/apple-touch-icon.png" sizes="180x180">
	<link rel="icon" href="/cards/favicon/favicon-32x32.png" sizes="32x32" type="image/png">
	<link rel="icon" href="/cards/favicon/favicon-16x16.png" sizes="16x16" type="image/png">
	<link rel="mask-icon" href="/cards/favicon/safari-pinned-tab.svg" color="#7952b3">
	<!-- Custom styles -->
	<link href="custom.css" rel="stylesheet">
</head>

<body class="bg-light">

<main class="container">
<div class="row my-4 justify-content-center">
<div class="col col-md-8">

<div class="card my-2">
	<div class="card-body">
		<div class="row"><div class="col text-end">
			<a class="external-link" target="_blank" href="/Record/002748068"><b>002748068</b></a>
		</div></div>
		<div class="row">
			<a class="external-color my-4" target="_blank" href="/Results?lookfor=HEJDOV%C3%81%2C+Irena&type=Author&limit=20">
				<h5 class="card-title"><b>HEJDOVÁ</b>, Irena</h5>
			</a>
			
			<div class="card-text">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-arrow-right-short" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8z"/>
</svg>
			Válka má na prodeje knih horší dopad než pandemie / Irena Hejdová.</div>
			<div class="card-text"><b>In:</b> denikn.cz [online] 15. 4. 2022.</div>
			<div class="card-text my-4">[ <i>Článek o dopadu války na Ukrajině na český knižní trh.</i> ]</div>
			<div class="card-text">války; ph144863; czenas; knižní obchod; ph114934; czenas; čtenářství; ph222812; czenas; Covid-19; ph1075649; czenas</div>
		</div>
	</div>
</div>


</div>
</div>
</main>

</body>
</html>

