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
		<a class="navbar-brand" href="/vyvoj/main">
		<img src="../logo.png" alt="ČLB" width="60" height="35" class="d-inline-block align-text-center">Vývoj # Aleph</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<ul class="navbar-nav me-auto mb-2 mb-lg-0">
			</ul>
			<span clas="navbar-text">Username</span>
			<form class="d-flex align-items-center">
			<img class="d-inline-block align-text-center mx-2" src="../icons/person-fill.svg" alt="User" width="32" height="32"> 
			</form>
		</div>
	</div>
</nav>
   
<main class="container">
<div class="container px-4 py-2" id="icon-grid">

<div class="row my-4 justify-content-center">
	<div class="col-md-6">
		<div class="d-grid gap-2 d-sm-flex">
			<input type="radio" class="btn-check" id="uclo" name="core" value="UCLO" checked>
			<label class="btn btn-outline-danger w-100" for="uclo">UCLO</label>
			<input type="radio" class="btn-check" id="clo" name="core" value="CLO">
			<label class="btn btn-outline-danger text-nowrap w-100" for="clo">CLO</label>
		</div>
	</div>
</div>

<div class="row my-4 justify-content-center">
	<div class="col-6">
		<div class="form-floating">
			<input type="text" class="form-control" id="q" name="q" value=""><label for="q">Podmínka</label>
	</div>
	<div class="col">
		<button type="button" class="btn btn-warning">+</button>
	</div>
</div>


<div class="row my-4 justify-content-center">
	<div class="col-md-2">
	<div class="d-grid gap-2 d-sm-flex">
		<input type="radio" class="btn-check" id="or" name="op" value="UCLO" checked>
		<label class="btn btn-outline-danger w-100" for="or">OR</label>
		<input type="radio" class="btn-check" id="and" name="op" value="CLO">
		<label class="btn btn-outline-danger text-nowrap w-100" for="and">AND</label>
	</div>
	</div>
</div>

<div class="row justify-content-center">
<div class="col-6">
 <div class="form-check form-check-inline">
  <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
  <label class="form-check-label" for="inlineCheckbox1">LDR</label>
<div class="form-check form-check-inline">
  <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
  <label class="form-check-label" for="inlineCheckbox1">001</label>
</div>
<div class="form-check form-check-inline">
  <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
  <label class="form-check-label" for="inlineCheckbox1">003</label>
</div>
<div class="form-check form-check-inline">
  <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
  <label class="form-check-label" for="inlineCheckbox1">005</label>
</div>
<div class="form-check form-check-inline">
  <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
  <label class="form-check-label" for="inlineCheckbox1">008</label>
</div>
<div class="form-check form-check-inline">
  <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
  <label class="form-check-label" for="inlineCheckbox1">015</label>
</div>
<div class="form-check form-check-inline">
  <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
  <label class="form-check-label" for="inlineCheckbox1">020</label>
</div>
</div>
</div>


</div>
</main>

<script src="../bootstrap.min.js"></script>

</body>
</html>

