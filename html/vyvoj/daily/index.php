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
		<a class="navbar-brand" href="#">
		<img src="../logo.png" alt="ČLB" width="60" height="35" class="d-inline-block align-text-center">Vývoj # Daily</a>
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
<div class="row my-4 justify-content-center">
<div class="col col-md-8">

<form class="mb-4">
	<div class="row justify-content-center">
		<div class="col col-5">
			<input type="date" class="form-control" name="date" value="2022-04-21" min="2020-03-02" max="2022-04-21">
		</div>
		<div class="col col-2">
			<button class="btn btn-warning opacity-75" type="submit">Zobrazit</button>
		</div>
	</div>
</form>

<table class="table table-striped">
  <thead class="table-success">
    <tr>
      <th scope="col">SysNo</th>
      <th scope="col">SIF</th>
      <th scope="col">Kód</th>
      <th scope="col">Popis</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">002746587	</th>
      <td>er</td>
      <td>096</td>
      <td>Neplatný prefix, chybný 2. indikátor v poli 245.</td>
    </tr>
    <tr>
      <th scope="row">002746587</th>
      <td>er</td>
      <td>162</td>
      <td>Možný chybný zápis 245c, 1.indikátor je 1, začíná na malé písmeno.</td>
    </tr>
    <tr>
      <th scope="row">002746586</th>
      <td>er</td>
      <td>108</td>
      <td>Nesoulad mezi daty v poli 008 a 264.</td>
    </tr>
  </tbody>
</table>

</div>
</div>
</main>

<script src="../bootstrap.min.js"></script>

</body>
</html>

