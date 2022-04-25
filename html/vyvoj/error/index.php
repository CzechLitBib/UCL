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

</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark" style="background-color:#dc3545;">
	<div class="container-fluid">
		<a class="navbar-brand" href="/vyvoj/">
		<img src="../logo.png" alt="ČLB" width="60" height="35" class="d-inline-block align-text-center">Vývoj # Error</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<ul class="navbar-nav me-auto mb-2 mb-lg-0">
			</ul>
			<span clas="navbar-text"><b><?php echo $_SESSION['username'];?></b></span>
			<form class="d-flex align-items-center">
			<img class="d-inline-block align-text-center mx-2" src="../icons/person-fill.svg" alt="User" width="32" height="32"> 
			</form>
		</div>
	</div>
</nav>
   
<main class="container">
<div class="row my-4 justify-content-center">
<div class="col col-md-8">

<?php

$db = new SQLite3('error.db');

if (!$db) {
	echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">Chyba databáze.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
} else {

	$result = $db->query("SELECT id,label,text FROM error");
        
	while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
		echo '<div class="card my-2"><div class="card-body"><div class="row"><div class="col-2 col-md-1">';
		echo '<h5 class="card-title text-danger text-nowrap" id="' . $res['id'] . '">' . $res['id'] . '</h5>';
		echo '</div><div class="col"><h5 class="card-title">' . $res['label'] . '</h5>';
		echo '<p class="card-text">' . $res['text'] . '</p></div></div></div></div>';
        }
        
	$db->close();
}

?>

</div>
</div>
</main>

<script src="../bootstrap.min.js"></script>

</body>
</html>

