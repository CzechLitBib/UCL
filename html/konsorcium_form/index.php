<?php

session_start();

$id = uniqid();

$from= 'xxx';
$login = 'xxx';
$pass = 'xxx';
$server = 'xxx';

$DB_PATH='/var/www/data/form/form.db';
$FILE_PATH='/var/www/data/form/data/';
$UPLOAD='';

$message_map = array(
	1 => 'Platný kód.',
	2 => 'Neplatný kód.',
	3 => 'Chyba čtení databáze.',
	4 => 'Chyba zápisu databáze.',
	5 => 'Hotovo. Děkujeme!',
	6 => 'Hotovo. Děkujeme! (Byl Vám zaslán potvrzovací email.)',
	7 => 'Chyba formátu souboru.',
	8 => 'Chyba odesílání.'
);

if (!isset($_SESSION['message'])) { $_SESSION['message'] = null; }

if (isset($_POST['code']) and isset($_SESSION['secret'])) {
	($_SESSION['secret'] == $_POST['code']) ? $_SESSION['message'] = 1 : $_SESSION['message'] = 2;
}

function send_mail($sender, $recipient, $mime_message) {
	
	global $login, $pass, $server;
	$ctx = stream_context_create();

	stream_context_set_option($ctx, 'ssl', 'verify_peer', false);
	stream_context_set_option($ctx, 'ssl', 'verify_peer_name', false);

	$socket = stream_socket_client('tcp://' . $server . ':25', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);

	if (!$socket) {
		$_SESSION['message'] = 8;
	} else{
		fread($socket,8192);
		fwrite($socket, "EHLO ". $_SERVER['SERVER_NAME'] . "\r\n");
		fread($socket,8192);
		fwrite($socket, "STARTTLS\r\n");
		fread($socket,8192);

		stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_SSLv23_CLIENT);

		fwrite($socket, "EHLO " . $_SERVER['SERVER_NAME'] . "\r\n");
		fread($socket,8192);
		fwrite($socket, "MAIL FROM: <" . $sender . ">\r\n");
		fread($socket,8192);
		fwrite($socket, "AUTH LOGIN\r\n");
		fread($socket,8192);
		fwrite($socket, base64_encode($login) . "\r\n");
		fread($socket,8192);
		fwrite($socket, base64_encode($pass) . "\r\n");
		fread($socket,8192);
		fwrite($socket, "RCPT TO: <" . $recipient . ">\r\n");
		fread($socket,8192);
		fwrite($socket, "DATA\n");
		fread($socket,8192);
		fwrite($socket, $mime_message . "\r\n.\r\n");
		fread($socket,8192);
		fwrite($socket, "QUIT \n");
		fread($socket,8192);

		fclose($socket);
	}
}

if ($_SESSION['message'] == 1) {
	# SQL
	$db = new SQLite3($DB_PATH);
	if (!$db) {
		$_SESSION['message'] = 3;
	} else {
		isset($_POST['format']) ? $format = $_POST['format'] : $format = 'fulltext';// full-text

		$query = $db->exec("
			INSERT INTO data (id,visible,format,public,dedication,link,email,note,author,name,text_author,text_name,place,publisher,year,source,quote,page,other)"
			. " VALUES ('" . $id . "',1,'" . $format . "', 0," . $_POST['dedication'] . ",'"
			. str_replace("'", '_', $_POST['link']) . "','"
			. str_replace("'", '_', $_POST['email']) . "','"
			. str_replace("'", '_', $_POST['note']) . "','"
			. str_replace("'", '_', $_POST['author']) . "','"
			. str_replace("'", '_', $_POST['name']) . "','"
			. str_replace("'", '_', $_POST['text-author']) . "','"
			. str_replace("'", '_', $_POST['text-name']) . "','"
			. str_replace("'", '_', $_POST['place']) . "','"
			. str_replace("'", '_', $_POST['publisher']) . "','"
			. str_replace("'", '_', $_POST['year']) . "','"
			. str_replace("'", '_', $_POST['source']) . "','"
			. str_replace("'", '_', $_POST['quote']) . "','"
			. str_replace("'", '_', $_POST['page']) . "','"
			. str_replace("'", '_', $_POST['other']) . "'"
			. ");"
		);
		if (!$query) {
			$_SESSION['message'] = 4;
		} elseif ($_POST['public']) {
			$_SESSION['message'] = 6;
		} else {
			$_SESSION['message'] = 5;
		}		

		# FILE
		if (isset($_FILES['file'])) {
			if ($_FILES['file']['error'] == 0) {
				$finfo = new finfo(FILEINFO_MIME_TYPE);
				$ftype = $finfo->file($_FILES['file']['tmp_name']);
				if ($ftype == 'application/pdf') {
					# escape dot, space, slash and quote
					$UPLOAD = preg_replace("/^\.+| |\/|'|\.+$/", '_', $_FILES['file']['name']);
					move_uploaded_file($_FILES['file']['tmp_name'], $FILE_PATH . $id . '_' . $UPLOAD);
					$query = $db->exec("INSERT INTO file (id,name) VALUES ('" . $id . "','". $UPLOAD . "');");
					if (!$query) { $_SESSION['message'] = 4; }
				} else {
					$_SESSION['message'] = 7;
				}
			}
		}
		$db->close();
	}

	# NOTIFY
	if ($_SESSION['message'] == 5 or $_SESSION['message'] == 6) {

		# CONFIRMATION
		if ($_POST['public']) {

			$text = "MIME-Version: 1.0\r\n"; 
			$text .= "From: =?utf-8?B?" .base64_encode('ČLB Data'). "?= <" . $from . ">\r\n";
			$text .= "To: " . $_POST['email'] . "\r\n";
			$text .= "Content-type: text/html; charset=utf-8\r\n";
			$text .= "Subject: =?utf-8?B?" . base64_encode("ČLB - Návrhy podkladů") . "?=\r\n";

			$text .= '<html><head><meta charset="utf-8"></head><body><br>Dobrý den,<br><br>';
			$text .= 'z Vaší emailové adresy byl odeslán souhlas se zveřejněním následujících souborů<br>';
			$text .= 'v databázích České literární bibliografie:<br><br>';

			if($UPLOAD) { 
				$text .= $UPLOAD;
			} elseif (isset($_POST['link'])) {
				$text .= htmlspecialchars($_POST['link']);
			}

			$text .= '<br><br>Prosím, potvrďte tento souhlas kliknutím na následující odkaz:';		
			$text .= '<br><br><a target="_blank" href="https://clbdata.ucl.cas.cz/konsorcium_form/confirm.php?'
				. $id . '">https://clbdata.ucl.cas.cz/konsorcium_form/confirm.php?'
				. $id . '<a><br><br>Děkujeme za spolupráci<br>';
			$text .= 'Tým České literární bibliografie<br><br>--------------------------------<br>';
			$text .= 'TATO ZPRÁVA BYLA VYGENEROVÁNA AUTOMATICKY, NEODPOVÍDEJTE NA NI.</body></html>';

			send_mail($from, $_POST['email'], $text);
		}
	}
	
	#PRG
	header('Location: /konsorcium_form/');
	exit();
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
	<link href="color.css" rel="stylesheet">
</head>
<body class="bg-light" onload="on_load();">

<div class="container-md">
<main>
<div class="row py-4 justify-content-center">
<div class="col-md-8">

<?php

if ($_SESSION['message'] > 0) {
	echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">' . $message_map[$_SESSION['message']] . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
	$_SESSION['message'] = null;
}

?>

<div class="text-center">
<svg width="128" height="128" fill="currentColor" class="bi bi-clb-logo my-4 ms-4 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 314.4 226.08"><path d="M232.76 10.3c-2.08 5.68-11.64 32-21.27 58.46-9.63 26.45-17.4 48.19-17.22 48.33.32.31 15.1 5.68 15.53 5.68.28 0 42.61-116.1 42.61-116.84 0-.32-14.6-5.9-15.52-5.93-.21 0-2.08 4.66-4.13 10.3zM296.76 17c-30.2 82.94-36.3 99.98-35.95 100.16 1.13.6 15.45 5.68 15.6 5.54.45-.46 38.23-104.88 37.99-105.1-.32-.31-14.93-5.6-15.42-5.6-.21 0-1.2 2.25-2.22 5zM192.93 28.5c-3.66 9.6-32.03 88.16-31.92 88.41.2.57 15.41 5.96 15.73 5.61.28-.28 32.46-88.51 32.46-88.97 0-.1-2.58-1.13-5.75-2.26-3.14-1.13-6.7-2.43-7.94-2.93l-2.22-.8zM259.19 29.03l-16.27 44.7c-8.64 23.74-15.59 43.29-15.45 43.43.39.35 15.6 5.71 15.74 5.57.24-.28 32.3-88.58 32.3-89 0-.22-2.32-1.27-5.18-2.3-2.89-1.02-6.45-2.33-7.93-2.89l-2.68-.99zM.25 36.9C.1 37.04 0 40.64 0 44.87v7.7h16.59v160.51H0l.07 6.42.1 6.45 46.93.11c55.8.1 59.58-.04 69.81-2.61a58.4 58.4 0 0 0 13.8-5.12c14.99-7.37 24.4-19.01 27.55-34 .91-4.3.91-14.71.03-19.58-.81-4.38-2.71-10.06-4.55-13.44-7.44-13.97-23.67-23.32-48.44-27.87-2.85-.53-3.2-.36 4.77-2.08 27.83-6.07 40.57-19.09 40.57-41.46 0-20.92-12.99-35.84-35.7-41.1-8.47-1.93-6.85-1.86-62.73-2-28.43-.08-51.82-.04-51.96.1zm83.08 16.65c12.38 2.97 20.56 10.4 23.63 21.6 1.03 3.8 1.27 11.25.5 15.16-2.47 12.1-11.05 20.82-24.13 24.41-2.44.67-3.85.78-13.55.92l-10.87.14V52.5l10.7.18c9.17.14 11.07.25 13.72.88zM76.94 133c23.81 2.36 39.97 20.1 38.63 42.47-1.02 17.22-11.08 30.02-27.62 35.28-5.44 1.7-8.43 2.05-19.05 2.22l-9.99.18V132.64h7.2c3.95 0 8.82.18 10.83.36z"/></svg>
</div>
<div class="p-4 text-center"><h2>Návrhy podkladů pro zpracování v ČLB</h2></div>

<div class="accordion shadow-sm mb-4" id="accordionExample">
	<div class="accordion-item">
		<div class="accordion-header" id="headingOne">
			<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
	Tento formulář slouží pro zasílání návrhů dokumentů ke zpracování pro potřeby databází České literární bibliografie. Dokument je možné dodat buď v&nbsp;elektronické verzi (preferováno), nebo alespoň formou vyplnění bibliografické citace. 
			</button>
		</div>
		<div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
			<div class="accordion-body">
Tímto způsobem jsou přednostně sbírány informace o&nbsp;publikacích mimo běžný excerpční záběr ČLB. Zejmána jde o&nbsp;publikace vydané v&nbsp;zahraničí, malonákladové či regionální tiskoviny a&nbsp;články o&nbsp;literatuře v&nbsp;neliterárních periodikách. Na základě dodaných podkladů bude vytvořen bibliografický záznam. Pro vytvoření záznamu je vítané dodání plného textu dokumentu či umožnění přístupu k&nbsp;němu, aby mohly být ověřeny základní bibliografické údaje. Pokud navrhovatel neurčí jinak, ČLB se zavazuje plný text využít pouze pro účely zpracování bibliografického záznamu a&nbsp;nebude jej jakkoli ukládat a&nbsp;dále šířit. Návrhy dokumentů ke zpracování je možné zadat prostřednictvím formuláře níže.
			</div>
		</div>
	</div>
</div>

<div class="row my-4">
	<div class="d-grid gap-2 d-sm-flex justify-content-md-center">
		<input type="radio" class="btn-check" id="fulltext" name="type" onclick="type_load()" checked>
		<label class="btn btn-outline-danger w-100" for="fulltext">Vložit plný text</label>
		<input type="radio" class="btn-check" id="biblio" name="type" onclick="type_load()">
		<label class="btn btn-outline-danger text-nowrap w-100" for="biblio">Vložit bibliografický údaj</label>
	</div>
</div>

<hr/>

<form method="post" action="." enctype="multipart/form-data">

<div id="fulltext-block">

<p class="my-4">Nahrajte prosím plný text dokumentu, nebo uveďte odkaz na online verzi ke stažení.</p>

<div class="form-group">
	<label for="pdf" class="form-label">Elektronická verze</label>
	<span class="badge bg-secondary text-white">PDF &lt; 5MB</span>
	<input type="file" class="form-control" id="pdf" name="file">
</div>
<div class="form-floating my-2">
	<input type="text" class="form-control" id="link" name="link" value="<?php if (isset($_POST['link'])) { echo htmlspecialchars($_POST['link'], ENT_QUOTES, 'UTF-8'); } ?>"><label for="link">Vložte odkaz</label>
</div>
<div class="form-floating">
	<input type="email" class="form-control" id="email" name="email" value="<?php if (isset($_POST['email'])) { echo htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8'); } ?>"><label for="email">E-mailová adresa pro ověření kontaktu</label>
	<div id="help" class="form-text text-end">Nikdy neposkytujeme Váš e-mail třetím stranám.</div>
</div>
<div class="form-floating">
	<textarea class="form-control" id="note" name="note" style="height: 100px"><?php if (isset($_POST['note'])) { echo htmlspecialchars($_POST['note'], ENT_QUOTES, 'UTF-8'); } ?></textarea>
	<label for="note">Poznámka (nepovinné)</label>
</div>
<div class="alert alert-warning mt-3 pb-2" role="alert">Souhlasím s&nbsp;uveřejněním elektronické verze dokumentu a&nbsp;potvrzuji, že toto uveřejnění není v&nbsp;rozporu s&nbsp;autorským zákonem a&nbsp;právy třetích stran.

	<div class="row gap-2 mt-2 justify-content-end">
		<div class="d-grid col-2 col-md-1 p-0">
			<input type="radio" class="btn-check alert-link" id="public-true" name="public" value="1" onclick="mail_req(1)">
			<label class="btn btn-sm btn-outline-secondary" for="public-true">Ano</label>
		</div>
		<div class="d-grid col-2 col-md-1 p-0 me-3">
			<input type="radio" class="btn-check" id="public-false" name="public" value="0" onclick="mail_req(0)" checked>
			<label class="btn btn-sm btn-outline-secondary" for="public-false">Ne</label>
		</div>
	</div>
</div>

</div>
<div id="biblio-block">

<p class="my-4">Údaje není třeba vyplňovat, pakliže jsou dostupné v dodané elektronické verzi.</p>

<h4>Zvolte formát dokumentu</h4>

<div class="row mt-4 mb-5">
	<div class="d-grid gap-2 d-sm-flex justify-content-md-center">
		<input type="radio" class="btn-check" id="article" name="format" value="article" onclick="format_load()">
		<label class="btn btn-outline-danger w-100" for="article">Článek</label>
		<input type="radio" class="btn-check" id="chapter" name="format" value="chapter" onclick="format_load()">
		<label class="btn btn-outline-danger text-nowrap w-100" for="chapter">Část knihy</label>
		<input type="radio" class="btn-check" id="book" name="format" value="book" onclick="format_load()">
		<label class="btn btn-outline-danger w-100" for="book">Kniha</label>
		<input type="radio" class="btn-check" id="study" name="format" value="study" onclick="format_load()">
		<label class="btn btn-outline-danger text-nowrap w-100" for="study">Sborníková studie</label>
		<input type="radio" class="btn-check" id="other" name="format" value="other" onclick="format_load()">
		<label class="btn btn-outline-danger text-nowrap w-100" for="other">Ostatní</label>
	</div>
</div>

<h4 class="mt-4">Údaje o dokumentu</h4>

<div class="form-floating my-2">
	<input type="text" class="form-control" id="author" name="author" value="<?php if (isset($_POST['author'])) { echo htmlspecialchars($_POST['author'], ENT_QUOTES, 'UTF-8'); } ?>"><label for="author">Autor/Editor</label>
</div>
<div class="form-floating my-2">
	<input type="text" class="form-control" id="name" name="name" value="<?php if (isset($_POST['name'])) { echo htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8'); } ?>"><label for="name">Název</label>
</div>

<div id="chapter-block">
	<h4 class="mt-4">Zdrojový dokument</h4>
	<div class="form-floating my-2">
		<input type="text" class="form-control" id="text-author" name="text-author" value="<?php if (isset($_POST['text-author'])) { echo htmlspecialchars($_POST['text-author'], ENT_QUOTES, 'UTF-8'); } ?>"><label for="text-author">Autor/Editor</label>
	</div>
	<div class="form-floating my-2">
		<input type="text" class="form-control" id="text-name" name="text-name" value="<?php if (isset($_POST['text-name'])) { echo htmlspecialchars($_POST['text-name'], ENT_QUOTES, 'UTF-8'); } ?>"><label for="text-name">Název</label>
	</div>
</div>

<div id="chapter-book-block">
	<div class="form-floating my-2">
		<input type="text" class="form-control" id="place" name="place" value="<?php if (isset($_POST['place'])) { echo htmlspecialchars($_POST['place'], ENT_QUOTES, 'UTF-8'); } ?>"><label for="place">Místo vydání</label>
	</div>
	<div class="form-floating my-2">
		<input type="text" class="form-control" id="publisher" name="publisher" value="<?php if (isset($_POST['publisher'])) { echo htmlspecialchars($_POST['publisher'], ENT_QUOTES, 'UTF-8'); } ?>"><label for="publisher">Nakladatelství</label>
	</div>
	<div class="form-floating my-2">
		<input type="text" class="form-control" id="year" name="year" value="<?php if (isset($_POST['year'])) { echo htmlspecialchars($_POST['year'], ENT_QUOTES, 'UTF-8'); } ?>"><label for="year">Rok</label>
	</div>
</div>

<div id="article-block">
	<div class="form-floating my-2">
		<input type="text" class="form-control" id="source" name="source" value="<?php if (isset($_POST['source'])) { echo htmlspecialchars($_POST['source'], ENT_QUOTES, 'UTF-8'); } ?>"><label for="source">Zdrojový dokument</label>
	</div>
	<div class="form-floating my-2">
		<input type="text" class="form-control" id="quote" name="quote" value="<?php if (isset($_POST['quote'])) { echo htmlspecialchars($_POST['quote'], ENT_QUOTES, 'UTF-8'); } ?>"><label for="quote">Bibliografická citace</label>
	</div>
</div>

<div id="other-block">
	<div class="form-floating my-2">
		<input type="text" class="form-control" id="text-other" name="other" value="<?php if (isset($_POST['other'])) { echo htmlspecialchars($_POST['other'], ENT_QUOTES, 'UTF-8'); } ?>"><label for="text-other">Další údaje</label>
	</div>
</div>

<div id="page-block">
	<div class="form-floating my-2">
		<input type="text" class="form-control" id="text-page" name="page" value="<?php if (isset($_POST['page'])) { echo htmlspecialchars($_POST['page'], ENT_QUOTES, 'UTF-8'); } ?>"><label for="text-page">Stránkový rozsah</label>
	</div>
</div>

</div>
<hr/>

<div class="alert alert-warning my-2 pb-2" role="alert">V&nbsp;textu je uvedena dedikace na výzkumnou infrastrukturu. Uvádění dedikace je vyžadováno pro systém hodnocení vědy a&nbsp;výzkumu ČR (více <a class="alert-link" target="_blank" href="https://clb.ucl.cas.cz/jak-citovat-clb">zde</a>).

	<div class="row gap-2 mt-2 justify-content-end">
		<div class="d-grid col-2 col-md-1 p-0">
			<input type="radio" class="btn-check alert-link" id="dedication-true" name="dedication" value="1">
			<label class="btn btn-sm btn-outline-secondary" for="dedication-true">Ano</label>
		</div>
		<div class="d-grid col-2 col-md-1 p-0 me-3">
			<input type="radio" class="btn-check" id="dedication-false" name="dedication" value="0" checked>
			<label class="btn btn-sm btn-outline-secondary" for="dedication-false">Ne</label>
		</div>
	</div>
</div>

<div class="row my-4 justify-content-center">
	<div class="col-4 col-sm-2 d-flex align-items-center justify-content-center">
		<img src="validation.php" alt="Validation">
	</div>
	<div class="col-4 col-sm-3">
		<div class="form-floating">
			<input type="text" class="form-control text-center" maxlength="5" id="code" name="code" value="" required><label class="text-nowrap" for="code">Kontrolní kód</label>
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
	<p>&copy; 2021-<?php echo date('Y');?> ČLB AV ČR</p>
	<ul class="list-inline">
		<li class="list-inline-item"><a class="link-danger" target="_blank" href="https://clb.ucl.cas.cz/ochrana-osobnich-udaju/">Soukromí</a></li>
		<li class="list-inline-item"><a class="link-danger" href="#">Nahoru</a></li>
		<li class="list-inline-item"><a class="link-danger" href="mailto:clb@ucl.cas.cz?subject=ČLB%20-%20Návrhy%20podkladů">Kontakt</a></li>
	</ul>
</footer>

</div>

<script src="form.js"></script>
<script src="bootstrap.min.js"></script>

</body>
</html>

