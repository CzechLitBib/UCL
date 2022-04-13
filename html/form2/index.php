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

<div class="bg-primary"><img src="logo3.png" alt="ČLB logo" width="209"/></div>
<div class="bg-secondary"><h3>Návrhy podkladů pro zpracování v ČLB</h3></div>
<div class="bg-success"><p>Tento formulář slouží pro zasílání návrhů dokumentů ke zpracování pro potřeby databází České literární bibliografie. Tímto způsobem jsou přednostně sbírány informace o publikacích mimo běžný excerpční záběr ČLB či publikacích obtížněji dostupných – přednostně jde o publikace vydané v zahraničí, malonákladové či regionální tiskoviny, články o literatuře v tiskovinách, které se literatuře a literárnímu dění systematicky nevěnují atp. Pakliže daný dokument splňuje podmínky pro zařazení do bází ČLB, bude na základě dodaných podkladů vytvořen bibliografický záznam. Podmínkou pro vytvoření záznamu je dodání plného textu daného dokumentu či umožnění přístupu k němu, aby mohla být provedena obsahová analýza a ověřeny základní bibliografické údaje. Pokud navrhovatel neurčí jinak, ČLB se zavazuje plný text využít pouze pro účely zpracování bibliografického záznamu a nebude jej jakkoli dále distribuovat. Návrhy dokumentů ke zpracování je možné zadat prostřednictvím formuláře níže.</p></div>

<hr/>

<form>
<div class="form-check form-check-inline">
	<input class="form-check-input" type="radio" name="article" id="article" value="article" checked>
	<label class="form-check-label" for="exampleRadios1">Clanek</label>
</div>
<div class="form-check form-check-inline">
	<input class="form-check-input" type="radio" name="chapter" id="chapter" value="chpater">
	<label class="form-check-label" for="exampleRadios1">Cast knihy</label>
</div>
<div class="form-check form-check-inline">
	<input class="form-check-input" type="radio" name="article" id="book" value="book">
	<label class="form-check-label" for="exampleRadios1">Kniha</label>
</div>
<h5><u>Plny text</u></h5>
<p>Nahrejte, prosím, plný text dokumentu, nebo uveďte odkaz na online verzi ke stažení:</p>
<div class="form-group">
	<label for="link">Odkaz</label>
	<input type="text" class="form-control" id="link">
</div>
<div class="form-group">
	<label for="pdf">Elektronicka verze</label>
	<input type="file" class="form-control" id="pdf">
</div>

<p>Souhlasím s uveřejněním elektronické verze dokumentu a potvrzuji, že tak mohu učinit a že toto uveřejnění není v rozporu s autorským zákonem a právy třetích stran.</p>

<div class="form-check form-switch">
  <input class="form-check-input" type="checkbox" role="switch" id="agreed">
  <label class="form-check-label" for="agreed">Ne</label>
</div>

<div class=form-group">
    <label for="email" class="form-label">Emailova adresa</label>
    <input type="email" class="form-control" id="email" aria-describedby="emailHelp">
    <div id="emailHelp" class="form-text">Nikdy neposkytujeme email tretim stranam.</div>
  </div>

<div class="form-group">
  <label for="note" class="form-label">Poznamka</label>
  <textarea class="form-control" id="note" rows="3"></textarea>
</div>

<p>K bibliografickému záznamu daného dokumentu je možno přidat i odkaz na plný text. Ten bude k záznamu připojen, pokud:

a) je daný dokument zpřístupněn prostřednictvím veřejně dostupného repozitáře s perzistentním odkazem (např. repozitáře výzkumných institucí a univerzit atp.).

b) pokud jej navrhovatel, který je zároveň autorem dokumentu, dodá v elektronické verzi, souhlasí se zveřejněním a následně tuto skutečnost potvrdí prostřednictvím kontaktního emailu.
</p>

<h5><u>Udaje o dokumentu</u></h5>

<p>Údaje není třeba vyplňovat, pakliže jsou dostupné v dodané elektronické verzi.</p>

<div class="form-group">
	<label for="link">Autor</label>
	<input type="text" class="form-control" id="author">
</div>
<div class="form-group">
	<label for="link">Nazev</label>
	<input type="text" class="form-control" id="name">
</div>
<div class="form-group">
	<label for="link">Zdrojovy dokument</label>
	<input type="text" class="form-control" id="source">
</div>
<div class="form-group">
	<label for="link">Citace</label>
	<input type="text" class="form-control" id="quote">
</div>

<img src="validation.php" alt="Validation">

<div class="form-group">
	<input type="text" class="form-control" id="code">
	<div id="help" class="form-text">Vyplnte kontrolni kod.</div>
</div>

<button type="submit" class="btn btn-primary">Odeslat</button>

</form>

<hr/>

<footer class="text-muted text-small">
	<p>&copy; 2021–2021 ČLB</p>
	<ul class="list-inline">
		<li class="list-inline-item"><a href="#">Soukromí</a></li>
		<li class="list-inline-item"><a href="#">Podmínky</a></li>
		<li class="list-inline-item"><a href="#">Kontakt</a></li>
	</ul>
</footer>

<script src="form-validation.js"></script>
<script src="bootstrap.min.js"></script>

</body>
</html>

