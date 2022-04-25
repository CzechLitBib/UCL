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
		<img src="../logo.png" alt="ČLB" width="60" height="35" class="d-inline-block align-text-center">Vývoj # Formulář</a>
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

<table class="table table-striped caption-top text-center">
  <thead class="table-light">
    <tr>
      <th scope="col">ID</th>
      <th scope="col">typ</th>
      <th scope="col">veřejný</th>
      <th scope="col">odkaz</th>
      <th scope="col">email</th>
      <th scope="col"></th>
      <th scope="col"></th>
      <th scope="col">zpracováno</th>
    </tr>
  </thead>
  <tbody>
    <tr>
     <!--id valid public link email note -->
      <th scope="row" class="align-middle">123456789</th>
      <td>článek</td>
      <td>Ne</td>
      <td>
     <a href="">
	<img class="bi text-muted flex-shrink-0 me-3" src="../icons/link.svg" alt="User" width="24" height="24">
      </a>
      </td>
      <td>
      foo@bar.cz
      </td>
      <td>
     <a href="">
	<img class="bi text-muted flex-shrink-0 me-3" src="../icons/filetype-csv.svg" alt="User" width="24" height="24">
      </a>
      </td>
      <td>
      <a href="">
	<img class="bi text-muted flex-shrink-0 me-3" src="../icons/filetype-pdf.svg" alt="User" width="24" height="24">
      </a>
      </td>
      <td>
      <input class="form-check-input" type="checkbox" id="checkboxNoLabel" value="" aria-label="...">
      </td>
    </tr>
 </tbody>

</table>

<nav aria-label="Page navigation">
  <ul class="pagination justify-content-center">
    <li class="page-item disabled">
      <a class="page-link">Předchozí</a>
    </li>
    <li class="page-item active"><a class="page-link" href="#">1</a></li>
    <li class="page-item"><a class="page-link" href="#">2</a></li>
    <li class="page-item"><a class="page-link" href="#">3</a></li>
    <li class="page-item">
      <a class="page-link" href="#">Následující</a>
    </li>
  </ul>
</nav>

</div>
</div>
</main>

<script src="../bootstrap.min.js"></script>

</body>
</html>

