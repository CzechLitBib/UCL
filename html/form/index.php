<?php

session_start();

$id = uniqid();

$error = '';

$from= 'xxx';
$target = 'xxx';
$server = 'xxx';

$valid=False;
if (isset($_POST['code']) and isset($_SESSION['secret'])) {
	if ($_SESSION['secret'] == $_POST['code'])  { $valid=True; }
}

if ($valid) {

	# SQL
	$db = new SQLite3('form.db');
	if (!$db) {
		$error = 'Chyba čtení databáze.';
	} else {
		$public = 0;
		if (isset($_POST['public'])) { $public = 1; }
			
		$query = $db->exec("
			INSERT INTO data (id,valid,type,public,link,email,note,text_author,text_name,author,name,place,publisher,year,source,quote)"
			. " VALUES ('" . $id . "',0,'" . $_POST['type'] . "'," . $public . ",'" 
			. str_replace("'", '_', $_POST['link']) . "','"
			. str_replace("'", '_', $_POST['email']) . "','"
			. str_replace("'", '_', $_POST['note']) . "','"
			. str_replace("'", '_', $_POST['text-author']) . "','"
			. str_replace("'", '_', $_POST['text-name']) . "','"
			. str_replace("'", '_', $_POST['author']) . "','"
			. str_replace("'", '_', $_POST['name']) . "','"
			. str_replace("'", '_', $_POST['place']) . "','"
			. str_replace("'", '_', $_POST['publisher']) . "','"
			. str_replace("'", '_', $_POST['year']) . "','"
			. str_replace("'", '_', $_POST['source']) . "','"
			. str_replace("'", '_', $_POST['quote']) . "'"
			. ");"
		);
		if (!$query) { $error = 'Chyba zápisu do databáze.'; }

		# FILE
		if (isset($_FILES['file'])) {
			if ($_FILES['file']['error'] == 0) {
				$finfo = new finfo(FILEINFO_MIME_TYPE);
				$ftype = $finfo->file($_FILES['file']['tmp_name']);
				if ($ftype == 'application/pdf') {
					# escape dot, space, slash and quote
					$fn = preg_replace("/^\.+| |\/|'|\.+$/", '_', $_FILES['file']['name']);
					move_uploaded_file($_FILES['file']['tmp_name'], 'data/' . $id . '_' . $fn);
					$query = $db->exec("INSERT INTO file (id,name) VALUES ('" . $id . "','". $fn . "');");
					if (!$query) { $error = 'Chyba zápisu do databáze.'; }
				} else {
					$error = 'Chyba formátu souboru.';
				}
			}
		}
		$db->close();
	}

	# NOTIFY
	if (!$error) {

		$headers="MIME-Version: 1.0\r\n";
		$headers.="From: UCL Vyvoj <" . $from . ">\r\n";
		$headers.="Reply-To: " . $from . "\r\n";
		$headers.="Content-type: text/html; charset=utf-8\r\n";

		$subject="=?utf-8?B?" . base64_encode("ČLB - Návrhy podkladů") . "?=";

		$text='<html><head><meta charset="utf-8"></head><body><br>Dobrý den,<br><br>Prostřednictvím formuláře ';
		
		if ($_POST['type'] == 'article') { $text.='byl zaslán nový článek.'; }
		if ($_POST['type'] == 'chapter') { $text.='byla zaslána nová kapitola v knize.'; }
		if ($_POST['type'] == 'book') { $text.='byla zaslána nová kniha.'; }

		$text.='<br><br><a target="_blank" href="https://vyvoj.ucl.cas.cz/form-data/">https://vyvoj.ucl.cas.cz/form-data/<a>
			<br><br>--------------------------------<br>TATO ZPRÁVA BYLA VYGENEROVÁNA AUTOMATICKY, NEODPOVÍDEJTE NA NI.
			</body></html>';

		mail($target, $subject, $text, $headers, '-f '. $from);
	}
}

?>

<!doctype html>
<html lang="cs">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>ČLB - Návrhy podkladů</title>
	<link href="custom.css" rel="stylesheet">
	<!-- Favicons -->
	<link rel="apple-touch-icon" href="favicon/apple-touch-icon.png" sizes="180x180">
	<link rel="icon" href="favicon/favicon-32x32.png" sizes="32x32" type="image/png">
	<link rel="icon" href="favicon/favicon-16x16.png" sizes="16x16" type="image/png">
	<link rel="mask-icon" href="favicon/safari-pinned-tab.svg" color="#7952b3">
	<!-- Custom styles -->
</head>
<body class="bg-light" onload="on_load();">
<div class="container-md">
<main>
<div class="row py-4 justify-content-center">
	<div class="col-md-8">

<div class="text-center"><img src="logo.png" alt="ČLB logo" width="209"/></div>
<div class="p-4 text-center"><h2>Návrhy podkladů pro zpracování v ČLB</h2></div>

<div class="card mb-3">
<div class="card-body">
Tento formulář slouží pro zasílání návrhů dokumentů ke zpracování pro potřeby databází České literární bibliografie. Tímto způsobem jsou přednostně sbírány informace o publikacích mimo běžný excerpční záběr ČLB či publikacích obtížněji dostupných – přednostně jde o publikace vydané v zahraničí, malonákladové či regionální tiskoviny, články o literatuře v tiskovinách, které se literatuře a literárnímu dění systematicky nevěnují atp. Pakliže daný dokument splňuje podmínky pro zařazení do bází ČLB, bude na základě dodaných podkladů vytvořen bibliografický záznam. Podmínkou pro vytvoření záznamu je dodání plného textu daného dokumentu či umožnění přístupu k němu, aby mohla být provedena obsahová analýza a ověřeny základní bibliografické údaje. Pokud navrhovatel neurčí jinak, ČLB se zavazuje plný text využít pouze pro účely zpracování bibliografického záznamu a nebude jej jakkoli dále distribuovat. Návrhy dokumentů ke zpracování je možné zadat prostřednictvím formuláře níže.
</div>
</div>

<h4>Formát</h4>

<form method="post" action="." enctype="multipart/form-data">

<div class="row my-4">
	<div class="d-grid gap-2 d-sm-flex justify-content-md-center">
		<input type="radio" class="btn-check" id="article" name="format" value="article" onclick="format_load();" checked>
		<label class="btn btn-outline-danger w-100" for="article">Článek</label>
		<input type="radio" class="btn-check" id="chapter" name="format" value="chapter" onclick="format_load();" >
		<label class="btn btn-outline-danger text-nowrap w-100" for="chapter">Část knihy</label>
		<input type="radio" class="btn-check" id="book" name="format" value="book" onclick="format_load();">
		<label class="btn btn-outline-danger w-100" for="book">Kniha</label>
	</div>
</div>

<h4>Plný text</h4>
<p>Nahrejte, prosím, plný text dokumentu, nebo uveďte odkaz na online verzi ke stažení.</p>

<div class="form-floating my-2">
	<input type="text" class="form-control" id="link" name="link" value="<?php if (!$valid and isset($_POST['link'])) { echo htmlspecialchars($_POST['link'], ENT_QUOTES, 'UTF-8'); } ?>"><label for="link">Odkaz</label>
</div>

<div class="form-group">
	<label for="pdf" class="form-label">Elektronická verze</label>
	<span class="badge bg-warning text-dark">PDF &lt; 5MB</span>
	<input type="file" class="form-control" id="pdf" name="file">
</div>

<div class="alert alert-warning my-2 pb-5" role="alert">Souhlasím s uveřejněním elektronické verze dokumentu a potvrzuji, že tak mohu učinit a že toto uveřejnění není v rozporu s autorským zákonem a právy třetích stran.
	<!--<div class="form-check form-switch p-2 float-end">-->
	<div class="mb-0">
	<div class="form-switch mt-1 p-2 float-end">
		<input class="form-check-input" id="public" name="public" type="checkbox" value="1" role="switch" onclick="yesno();">
		<label class="form-check-label" for="public" id="public-label">Ne</label>
	</div></div>
</div>

<div class="form-floating">
	<input type="email" class="form-control" id="email" name="email" value="<?php if (!$valid and isset($_POST['email'])) { echo htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8'); } ?>"><label for="email">Emailová adresa</label>
	<div id="help" class="form-text text-end">Nikdy neposkytujeme Váš email třetím stranám.</div>
</div>

<div class="mb-2">
<div class="form-floating">
	<textarea class="form-control" id="note" name="note" style="height: 100px"><?php if (!$valid and isset($_POST['note'])) { echo htmlspecialchars($_POST['note'], ENT_QUOTES, 'UTF-8'); } ?></textarea>
	<label for="note">Poznámka</label>
</div>
</div>

<p>K bibliografickému záznamu daného dokumentu je možno přidat i odkaz na plný text. Ten bude k záznamu připojen, pokud: a) je daný dokument zpřístupněn prostřednictvím veřejně dostupného repozitáře s perzistentním odkazem (např. repozitáře výzkumných institucí a univerzit atp.). b) pokud jej navrhovatel, který je zároveň autorem dokumentu, dodá v elektronické verzi, souhlasí se zveřejněním a následně tuto skutečnost potvrdí prostřednictvím kontaktního emailu.
</p>

<hr/>

<div id="chapter-block">
	<h4 class="mt-4">Text</h4>
	<div class="form-floating my-2">
		<input type="text" class="form-control" id="text-author" name="text-author" value="<?php if (!$valid and isset($_POST['text-author'])) { echo htmlspecialchars($_POST['text-author'], ENT_QUOTES, 'UTF-8'); } ?>"><label for="text-author">Autor</label>
	</div>
	<div class="form-floating my-2">
		<input type="text" class="form-control" id="text-name" name="text-name" value="<?php if (!$valid and isset($_POST['text-name'])) { echo htmlspecialchars($_POST['text-name'], ENT_QUOTES, 'UTF-8'); } ?>"><label for="text-name">Název</label>
	</div>

	<h4 class="mt-4">Zdrojový dokument</h4>
</div>

<div id="article-book-block">
	<h4 class="mt-4">Údaje o dokumentu</h4>

	<p>Údaje není třeba vyplňovat, pakliže jsou dostupné v dodané elektronické verzi.</p>
</div>

<div class="form-floating my-2">
	<input type="text" class="form-control" id="author" name="author" value="<?php if (!$valid and isset($_POST['author'])) { echo htmlspecialchars($_POST['author'], ENT_QUOTES, 'UTF-8'); } ?>"><label for="author">Autor</label>
</div>
<div class="form-floating my-2">
	<input type="text" class="form-control" id="name" name="name" value="<?php if (!$valid and isset($_POST['name'])) { echo htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8'); } ?>"><label for="name">Název</label>
</div>

<div id="chapter-book-block">
	<div class="form-floating my-2">
		<input type="text" class="form-control" id="place" name="place" value="<?php if (!$valid and isset($_POST['place'])) { echo htmlspecialchars($_POST['place'], ENT_QUOTES, 'UTF-8'); } ?>"><label for="place">Místo</label>
	</div>
	<div class="form-floating my-2">
		<input type="text" class="form-control" id="publisher" name="publisher" value="<?php if (!$valid and isset($_POST['publisher'])) { echo htmlspecialchars($_POST['publisher'], ENT_QUOTES, 'UTF-8'); } ?>"><label for="publisher">Nakladatelství</label>
	</div>
	<div class="form-floating my-2">
		<input type="text" class="form-control" id="year" name="year" value="<?php if (!$valid and isset($_POST['year'])) { echo htmlspecialchars($_POST['year'], ENT_QUOTES, 'UTF-8'); } ?>"><label for="year">Rok</label>
	</div>
</div>

<div id="article-block">
	<div class="form-floating my-2">
		<input type="text" class="form-control" id="source" name="source" value="<?php if (!$valid and isset($_POST['source'])) { echo htmlspecialchars($_POST['source'], ENT_QUOTES, 'UTF-8'); } ?>"><label for="source">Zdrojový dokument</label>
	</div>
	<div class="form-floating my-2">
		<input type="text" class="form-control" id="quote" name="quote" value="<?php if (!$valid and isset($_POST['quote'])) { echo htmlspecialchars($_POST['quote'], ENT_QUOTES, 'UTF-8'); } ?>"><label for="quote">Bibliografická citace</label>
	</div>
</div>

<div class="row my-4 justify-content-center">
	<div class="col-4 col-sm-2 d-flex align-items-center justify-content-center">
		<img src="validation.php" alt="Validation">
	</div>
	<div class="col-4 col-sm-3">
		<div class="form-floating">
			<input type="text" class="form-control" id="code" name="code" value="" required><label class="text-nowrap" for="code">Kontrolní kód</label>
		</div>
	</div>
</div>

<div class="d-grid col-4 mx-auto my-4">
	<button type="submit" class="btn btn-danger">Odeslat</button>
</div>
</form>

<hr/>

</div>
</div>

</main>

<footer class="text-muted text-small text-center">
	<p>&copy; 2021-<?php echo date('Y');?> ČLB</p>
	<ul class="list-inline">
		<li class="list-inline-item"><a class="link-danger" target="_blank" href="https://clb.ucl.cas.cz/ochrana-osobnich-udaju/">Soukromí</a></li>
		<li class="list-inline-item"><a class="link-danger" href="#">Nahoru</a></li>
		<li class="list-inline-item"><a class="link-danger" href="mailto:clb@ucl.cas.cz?subject=ČLB - Návrhy podkladů">Kontakt</a></li>
	</ul>
</footer>

</div>

<script src="form.js"></script>
<script src="bootstrap.min.js"></script>

<?php

if (isset($_POST['code'])) {
        if ($error) {
		echo '<div class="alert alert-warning" role="alert">' . $error . '</div>';
        } elseif ($valid) {
		echo '<div class="alert alert-warning" role="alert">Hotovo. Děkujeme!</div>';
        } else {
		echo '<div class="alert alert-warning" role="alert">Neplatný kontrolní kód.</div>';
        }
}

?>

</body>
</html>

