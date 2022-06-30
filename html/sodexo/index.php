<?php
?>

<!doctype html>
<html lang="cs">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Sodexo Flexi Pass <?php echo date('Y');?></title>
	<link href="bootstrap.min.css" rel="stylesheet">
	<!-- Favicons -->
	<link rel="apple-touch-icon" href="./favicon/apple-touch-icon.png" sizes="180x180">
	<link rel="icon" href="./favicon/favicon-32x32.png" sizes="32x32" type="image/png">
	<link rel="icon" href="./favicon/favicon-16x16.png" sizes="16x16" type="image/png">
	<link rel="mask-icon" href="./favicon/safari-pinned-tab.svg" color="#7952b3">
	<!-- Custom styles -->
</head>

<body style="background-color: #dee2e6;">
<main class="container">
<form method="post" action="." enctype="multipart/form-data">

<div class="row justify-content-center">
	<div class="col col-md-7 mt-4 my-2">
		<div class="card shadow-sm">
			<div class="card-header bg-primary"></div>
		<div class="card-body p-md-4">
   			<h3 class="card-title">Sodexo Flexi Pass <?php echo date('Y');?></h3>
			<p class="card-text">Příspěvek mohou čerpat zaměstnanci s&nbsp;úvazkem 0,5 a&nbsp;vyšším. Hodnota příspěvku je 7000,-
V případě zájmu o&nbsp;nahrání příspěvku za rok 2022 na Multi Pass kartu prosím vyplňte informace níže a&nbsp;to nejpozději do 24.6.2022.</p>
		</div>
		</div>
	</div>
</div>

<div class="row justify-content-center">
	<div class="col col-md-7 my-2">
		<div class="card shadow-sm">
		<div class="card-body p-md-4">
		<p class="card-title">Příjmení zaměstnance</p>
		<p class="card-text">
		<input type="text" class="form-control" id="sn" name="sn" required>
		</p>
		</div>
		</div>
	</div>
</div>

<div class="row justify-content-center">
	<div class="col col-md-7 my-2">
		<div class="card shadow-sm">
		<div class="card-body p-md-4">
		<p class="card-title">Jméno zaměstnance</p>
		<p class="card-text">
		<input type="text" class="form-control" id="n" name="n" required>
		</p>
		</div>
		</div>
	</div>
</div>

<div class="row justify-content-center">
	<div class="col col-md-7 my-2">
		<div class="card shadow-sm">
		<div class="card-body p-md-4">
		<p class="card-title">Příspěvek ze Soc. fondu jsem v&nbsp;roce <?php echo date('Y');?> již čerpal/a</p>
		<p class="card-text">
			<input class="form-check-input" type="radio" name="q" id="rad" checked>
			<label class="form-check-label mx-2" for="no"> Ne</label>
		</p>
		<p class="card-text">
			<input class="form-check-input" type="radio" name="q" id="rad">
			<label class="form-check-label mx-2" for="yes"> Ano a&nbsp;zbývající částku chci nahrát na Flexi Pass</label>
		</p>
		</div>
		</div>
	</div>
</div>


<div class="row justify-content-center">
	<!--<div class="col col-md-7 m-2">-->
	<div class="d-grid col-6 col-md-3 mx-auto my-2">
		<button type="submit" class="btn btn-primary">Odeslat</button>
	</div>
</div>

<div class="row justify-content-center text-center">
	<div class="col m-2">
		<p class="text-muted">ÚČL &copy; 2021–<?php echo date('Y');?></p>
	</div>
</div>

</form>
</main>

</body>
</html>

