<?php

session_start();

$_SESSION['page'] = 'uclo';

if(empty($_SESSION['auth'])) {
	header('Location: /vyvoj');
	exit();
}

if($_SESSION['group'] !== 'admin') {
        $_SESSION['error'] = True;
        header('Location: /vyvoj/main/');
        exit();
}

?>

<!doctype html>
<html lang="cs">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>ČLB Vývoj</title>
	<link href="../bootstrap.min.css" rel="stylesheet">
	<!-- Favicons -->
	<link rel="apple-touch-icon" href="../favicon/apple-touch-icon.png" sizes="180x180">
	<link rel="icon" href="../favicon/favicon-32x32.png" sizes="32x32" type="image/png">
	<link rel="icon" href="../favicon/favicon-16x16.png" sizes="16x16" type="image/png">
	<link rel="mask-icon" href="../favicon/safari-pinned-tab.svg" color="#7952b3">
	<!-- Custom styles -->
	<link href="../custom.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark" style="background-color:#dc3545;">
	<div class="container-fluid">
		<a class="navbar-brand" href="/vyvoj/main">
		<img src="../logo.png" alt="ČLB" width="60" height="35" class="d-inline-block align-text-center">Vývoj # UCLO</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<ul class="navbar-nav me-auto mb-2 mb-lg-0">
			</ul>
			<span clas="navbar-text">Username</span>
			<form class="d-flex align-items-center">
			<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-person-fill d-inline-block align-text-center mx-2" viewBox="0 0 16 16"><path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/><svg/>
			</form>
		</div>
	</div>
</nav>
   
<main class="container">
<div class="row my-4 justify-content-center">
<div class="col col-md-8">

<?php

function getLines($file)
{
	$f = fopen($file, 'rb');
	$lines = 0;
	while (!feof($f)) {
		$lines += substr_count(fread($f, 8192), "\n");
	}
	fclose($f);
	return $lines;
}

$dir = 'data';

$files = array_filter(scandir($dir), function ($var) { return preg_match('/\d{3}.*/', $var); } );
$tags = array_unique(array_map(function ($var) { return explode('.', $var)[0]; }, $files));

$no_seven = 0;
$seven = 0;

if (!empty($tags)) {

	echo '<table class="table table-sm caption-top text-center"><caption>Poslední záznam: <b>30.03.2022</b></caption>'
	. '<thead class="table-light"><tr><th scope="col">#</th><th scope="col" colspan="4">Podpole 7</th>'
	. '<th scope="col" colspan="4">Bez podpole 7</th></tr></thead><tbody>';

	foreach ($tags as $tag)	{

		$has_seven = 0;
		$has_no_seven = 0;

		if(in_array($tag . '.7.csv', $files)) {
			$has_seven = getLines($dir . '/' . $tag . '.7.csv');
			$seven += $has_seven;
		}
		if(in_array($tag . '.csv', $files)) {
			$has_no_seven = getLines($dir . '/' . $tag . '.csv');
			$no_seven += $has_no_seven;
		}

		if (!empty($has_seven)) {
			echo '<tr><th scope="row" class="align-middle">' . $tag . '</th>'
			. '<td><a href="' . $dir . '/' . $tag . '.7.csv"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-file-earmark-binary" viewBox="0 0 16 16"><path d="M7.05 11.885c0 1.415-.548 2.206-1.524 2.206C4.548 14.09 4 13.3 4 11.885c0-1.412.548-2.203 1.526-2.203.976 0 1.524.79 1.524 2.203zm-1.524-1.612c-.542 0-.832.563-.832 1.612 0 .088.003.173.006.252l1.559-1.143c-.126-.474-.375-.72-.733-.72zm-.732 2.508c.126.472.372.718.732.718.54 0 .83-.563.83-1.614 0-.085-.003-.17-.006-.25l-1.556 1.146zm6.061.624V14h-3v-.595h1.181V10.5h-.05l-1.136.747v-.688l1.19-.786h.69v3.633h1.125z"/><path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/></svg></a></td>'
			. '<td><a href="/vyvoj/seven/data/' . $tag  . '"><svg xmlns="http://www.w3.org/2000/svg" width="24" hei
ght="24" fill="currentColor" class="bi bi-bar-chart" viewBox="0 0 16 16"><path d="M4 11H2v3h2v-3zm5-4H7v7h2V7zm5-5v12h-2V2h2zm-2-1a1 1 0 0 0-1 
1v12a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1h-2zM6 7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7zm-5 4a1 1 0 0 1 1-1h
2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1v-3z"/></svg></a></td>'
			. '<td>' . $has_seven . '</td>'
			. '<td>' . round($has_seven/($has_seven + $has_no_seven)*100) . '%</td>';
		} else {
			echo '<tr><th class="align-middle">' . $tag . '</th><td colspan="3"></td><td>0%</td>';
		}

		if (!empty($has_no_seven)) {
			echo '<td><a href="' . $dir . '/' . $tag . '.csv"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-file-earmark-binary" viewBox="0 0 16 16"><path d="M7.05 11.885c0 1.415-.548 2.206-1.524 2.206C4.548 14.09 4 13.3 4 11.885c0-1.412.548-2.203 1.526-2.203.976 0 1.524.79 1.524 2.203zm-1.524-1.612c-.542 0-.832.563-.832 1.612 0 .088.003.173.006.252l1.559-1.143c-.126-.474-.375-.72-.733-.72zm-.732 2.508c.126.472.372.718.732.718.54 0 .83-.563.83-1.614 0-.085-.003-.17-.006-.25l-1.556 1.146zm6.061.624V14h-3v-.595h1.181V10.5h-.05l-1.136.747v-.688l1.19-.786h.69v3.633h1.125z"/><path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/></svg></a></td>'
			. '<td><a href="/vyvoj/seven/data/' . $tag  . '"><svg xmlns="http://www.w3.org/2000/svg" width="24" hei
ght="24" fill="currentColor" class="bi bi-bar-chart" viewBox="0 0 16 16"><path d="M4 11H2v3h2v-3zm5-4H7v7h2V7zm5-5v12h-2V2h2zm-2-1a1 1 0 0 0-1 
1v12a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1h-2zM6 7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7zm-5 4a1 1 0 0 1 1-1h
2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1v-3z"/></svg></a></td>'
			. '<td>' . $has_no_seven . '</td>'
			. '<td>' . round($has_no_seven/($has_seven + $has_no_seven)*100) . '%</td></tr>';

		} else {
			echo '<td colspan="3"></td><td>0%</td></tr>';
		}
	}

	echo '</tbody><tfoot><tr><th scope="col" colspan="3" class="text-start">Celkem</th>'
	. '<th scope="col">' . $seven . '</th>'
	. '<th scope="col">' . round($seven/($seven + $no_seven)*100) . '%</th>'
	. '<th scope="col" colspan="2"></th>'
	. '<th scope="col">' . $no_seven . '</th>'
	. '<th scope="col">' . round($no_seven/($seven + $no_seven)*100) . '%</th></tr>'
	. '</tfoot></table>';
}

?>


</div>
</div>
</main>

<script src="../bootstrap.min.js"></script>

</body>
</html>

