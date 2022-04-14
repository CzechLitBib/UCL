<!doctype html>
<html lang="cs">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>ČLB - Návrhy podkladů</title>
	<link href="bootstrap.min.css" rel="stylesheet">
	<!-- Favicons -->
	<!-- Custom styles for this template -->
</head>
<body class="bg-light">
<div class="container-md">
<main>
<div class="row py-4 justify-content-center">
	<div class="col-md-8">

<div class="text-center"><img src="logo3.png" alt="ČLB logo" width="209"/></div>
<div class="p-4 text-center"><h3>Návrhy podkladů pro zpracování v ČLB</h3></div>

<div class="card">
<div class="card-body">
Tento formulář slouží pro zasílání návrhů dokumentů ke zpracování pro potřeby databází České literární bibliografie. Tímto způsobem jsou přednostně sbírány informace o publikacích mimo běžný excerpční záběr ČLB či publikacích obtížněji dostupných – přednostně jde o publikace vydané v zahraničí, malonákladové či regionální tiskoviny, články o literatuře v tiskovinách, které se literatuře a literárnímu dění systematicky nevěnují atp. Pakliže daný dokument splňuje podmínky pro zařazení do bází ČLB, bude na základě dodaných podkladů vytvořen bibliografický záznam. Podmínkou pro vytvoření záznamu je dodání plného textu daného dokumentu či umožnění přístupu k němu, aby mohla být provedena obsahová analýza a ověřeny základní bibliografické údaje. Pokud navrhovatel neurčí jinak, ČLB se zavazuje plný text využít pouze pro účely zpracování bibliografického záznamu a nebude jej jakkoli dále distribuovat. Návrhy dokumentů ke zpracování je možné zadat prostřednictvím formuláře níže.
</div>
</div>

<form>

<div class="text-center gap-4">
<div class="btn-group my-4" role="group" aria-label="Basic radio toggle button group">
	<input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" checked>
	<label class="btn btn-primary" for="btnradio1">Článek</label>
	<input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
	<label class="btn btn-info" for="btnradio2">Část knihy</label>
	<input type="radio" class="btn-check" name="btnradio" id="btnradio3" autocomplete="off">
	<label class="btn btn-info" for="btnradio3">Kniha</label>
</div>
</div>

<h4>Plný text</h4>
<p>Nahrejte, prosím, plný text dokumentu, nebo uveďte odkaz na online verzi ke stažení.</p>

<div class="form-floating my-2">
	<input type="text" class="form-control" id="link" value=""><label for="link">Odkaz</label>
</div>

<div class="form-group">
	<label for="pdf" class="form-label">Elektronická verze</h4></label>
	<span class="badge bg-warning text-dark">PDF &lt; 5MB</span>
	<input type="file" class="form-control" id="pdf">
</div>

<div class="alert alert-warning my-2" role="alert">Souhlasím s uveřejněním elektronické verze dokumentu a potvrzuji, že tak mohu učinit a že toto uveřejnění není v rozporu s autorským zákonem a právy třetích stran.
	<div class="form-check form-switch p-2 float-end">
		<input class="form-check-input" type="checkbox" role="switch" id="agreed">
		<label class="form-check-label" for="agreed">Ne</label>
	</div>
</div>

<div class="form-floating">
	<input type="email" class="form-control" id="email" value=""><label for="email">Emailová adresa</label>
	<div id="help" class="form-text text-end">Nikdy neposkytujeme email třetím stranám.</div>
</div>

<div class="mb-2">
<div class="form-floating">
  <textarea class="form-control" id="note" style="height: 100px"></textarea>
  <label for="floatingTextarea">Poznámka</label>
</div>
</div>

<p>K bibliografickému záznamu daného dokumentu je možno přidat i odkaz na plný text. Ten bude k záznamu připojen, pokud:

a) je daný dokument zpřístupněn prostřednictvím veřejně dostupného repozitáře s perzistentním odkazem (např. repozitáře výzkumných institucí a univerzit atp.).

b) pokud jej navrhovatel, který je zároveň autorem dokumentu, dodá v elektronické verzi, souhlasí se zveřejněním a následně tuto skutečnost potvrdí prostřednictvím kontaktního emailu.
</p>

<hr/>

<h4>Údaje o dokumentu</h4>

<p>Údaje není třeba vyplňovat, pakliže jsou dostupné v dodané elektronické verzi.</p>

<div class="form-floating my-2">
	<input type="text" class="form-control" id="author" value=""><label for="author">Autor</label>
</div>
<div class="form-floating my-2">
	<input type="text" class="form-control" id="name" value=""><label for="name">Název</label>
</div>
<div class="form-floating my-2">
	<input type="text" class="form-control" id="source" value=""><label for="source">Zdrojový dokument</label>
</div>
<div class="form-floating my-2">
	<input type="text" class="form-control" id="quote" value=""><label for="quote">Citace</label>
</div>

<div class="row my-4 justify-content-center">
	<div class="col-auto align-self-center">
		<div class="bg-warning"><img src="validation.php" alt="Validation"></div>
	</div>
	<div class="col-3">
		<div class="form-floating">
			<input type="text" class="form-control" id="code" value=""><label for="code">Kontrolní kód</label>
		</div>
	</div>
</div>

<div class="d-grid col-4 mx-auto my-4">
	<button type="submit" class="btn btn-primary">Odeslat</button>
</div>
</form>
<hr/>

</div>
</div>

</main>

<footer class="text-muted text-small text-center">
	<p>&copy; 2021-<?php echo date('Y');?> ČLB</p>
	<ul class="list-inline">
		<li class="list-inline-item"><a href="#">Soukromí</a></li>
		<li class="list-inline-item"><a href="#">Podmínky</a></li>
		<li class="list-inline-item"><a href="#">Kontakt</a></li>
	</ul>
</footer>

</div>

<script src="form-validation.js"></script>
<script src="bootstrap.min.js"></script>

</body>
</html>

