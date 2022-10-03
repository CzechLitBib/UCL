<?php

session_start();

$user = ['xxx', 'xxx'];

$DB_FILE= '/var/www/data/sodexo.db';

$message_map = array(
        1 => 'Přihlášení.',
        2 => 'Přihlášení selhalo.',
        3 => 'Chyba čtení databáze.',
        4 => 'Chyba zápisu databáze.',
        5 => 'Uloženo.'
);

if (!isset($_SESSION['message'])) { $_SESSION['message'] = null; }

# DATA
if (isset($_POST['sn']) and isset($_POST['n']) and isset($_POST['q'])) {
	$db = new SQLite3($DB_FILE);
	if (!$db) {
		$_SESSION['message'] = 3;
	} else {
		$query = $db->exec("INSERT INTO data (y, n, sn, q)" . " VALUES ("
			. date('Y') . ",'"
			. $_POST['n'] . "','"
			. $_POST['sn'] . "','"
			. $_POST['q'] . "');"
		);

		if (!$query) {
			$_SESSION['message'] = 4;
		} else {
			$_SESSION['message'] = 5;
		}
		$db->close();
	}
	# PRG
	header('Location: /benefit/');
	exit();
}

# AUTH
if (isset($_POST['name']) and isset($_POST['pass'])) {
	if (in_array($_POST['name'], $user)) {	
		$ldap_dn = 'xxx';
		$ldap_conn = ldap_connect('xxx');

		ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);
		ldap_set_option($ldap_conn, LDAP_OPT_NETWORK_TIMEOUT, 10);

		$ldap_bind = @ldap_bind($ldap_conn, $ldap_dn, $_POST['pass']);

		if (!$ldap_bind) {//fall-back
			$ldap_conn2 = ldap_connect('xxx');

			ldap_set_option($ldap_conn2, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($ldap_conn2, LDAP_OPT_REFERRALS, 0);
			ldap_set_option($ldap_conn2, LDAP_OPT_NETWORK_TIMEOUT, 10);

			$ldap_bind = @ldap_bind($ldap_conn2, $ldap_dn, $_POST['pass']);
		}

		if ($ldap_bind) { $_SESSION['message'] = 1; }
	}
	if ($_SESSION['message'] == 1) {
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment;filename=benefity2022.csv");
		header("Content-Encoding: UTF-8");

		echo "\xEF\xBB\xBF";# UTF-8 BOM
		echo 'Jméno;Příjmení;Využití' . "\n";
		$db = new SQLite3($DB_FILE);
		if (!$db) {
			$_SESSION['message'] = 3;
		} else {
			$result = $db->query("SELECT n, sn, q FROM data");
			if ($result->fetchArray()) {
				$result->reset();
				while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
					echo $res['n'] . ';' . $res['sn'] . ';' . $res['q'] . "\n";
				}
			} else { $_SESSION['message'] = 3; }
			$db->close();
		}
		$_SESSION['message'] = null; # reset login
		exit();
	} else {
		$_SESSION['message'] = 2;
	}
	header('Location: /benefit/');
	exit();
}
?>

<!doctype html>
<html lang="cs">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Příspěvek <?php echo date('Y');?></title>
	<link href="bootstrap.min.css" rel="stylesheet">
	<!-- Favicons -->
	<link rel="apple-touch-icon" href="./favicon/apple-touch-icon.png" sizes="180x180">
	<link rel="icon" href="./favicon/favicon-32x32.png" sizes="32x32" type="image/png">
	<link rel="icon" href="./favicon/favicon-16x16.png" sizes="16x16" type="image/png">
	<link rel="mask-icon" href="./favicon/safari-pinned-tab.svg" color="#7952b3">
	<!-- Custom styles -->
	<link href="custom.css" rel="stylesheet">
</head>

<body style="background-color: #dee2e6;">
<main class="container mt-2">
<form method="post" action="." enctype="multipart/form-data">

<?php

if($_SESSION['message'] > 1) {
	echo '<div class="row justify-content-center"><div class="col col-md-7 mt-2">'
	. '<div class="alert alert-warning alert-dismissible fade show" role="alert">'
	. $message_map[$_SESSION['message']] . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'
	. '</div></div></div>';
	$_SESSION['message'] = null;
}

?>

<div class="row justify-content-center">
	<div class="col col-md-7 mb-2">
		<div class="card shadow-sm">
			<div class="card-header bg-primary"></div>
		<div class="card-body p-md-4">
   			<h3 class="card-title">Příspěvek ze Sociálního fondu <?php echo date('Y');?></h3>
			<p class="card-text">Příspěvek mohou čerpat zaměstnanci s&nbsp;úvazkem 0,5 a&nbsp;vyšším. Hodnota příspěvku je 7000,-. V&nbsp;případě zájmu o&nbsp;čerpání příspěvku vyplňte prosím informace níže.</p>
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
		<p class="card-title">Příspěvek chci využít na:</p>
		<p class="card-text">
			<input class="form-check-input" type="radio" name="q" id="flexipass" value="Flexi Pass" checked>
			<label class="form-check-label mx-2" for="flexipass">Flexi Pass</label>
		</p>
		<p class="card-text">
			<input class="form-check-input" type="radio" name="q" id="insurance" value="Pojištění">
			<label class="form-check-label mx-2" for="insurance">Penzijní a životní pojištění</label>
		</p>
		<p class="card-text">
			<input class="form-check-input" type="radio" name="q" id="course" value="Kurz">
			<label class="form-check-label mx-2" for="course">Jazykový kurz</label>
		</p>
		<p class="card-text">
			<input class="form-check-input" type="radio" name="q" id="vacation" value="Rekreace">
			<label class="form-check-label mx-2" for="vacation">Rekreaci a dětský tábor</label>
		</p>
		</div>
		</div>
	</div>
</div>

<div class="row justify-content-center my-2">
	<div class="col-2 col-md-2"></div>
	<div class="d-grid col col-md-3">
		<button type="submit" class="btn btn-primary">Odeslat</button>
	</div>
	<div class="col-2 col-md-2 text-end align-self-center">
	<svg xmlns="http://www.w3.org/2000/svg" onclick="mod();" width="24" height="24" fill="currentColor" class="bi bi-gear-fill me-2" viewBox="0 0 16 16"><path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/></svg>
	</div>
</div>

<div class="row justify-content-center text-center">
	<div class="col m-2">
		<p class="text-muted">ÚČL &copy; 2021–<?php echo date('Y');?></p>
	</div>
</div>

</form>
</main>

<div class="modal" tabindex="-1" id="data">
	<div class="modal-dialog modal-scrollable">
		<div class="modal-content">
			<div class="modal-body" id="list"></div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="login" aria-hidden="true">
	<form method="post" action="." enctype="multipart/form-data">
	<div class="modal-dialog">
	<div class="modal-content">
		<div class="container-fluid">
			<div class="row">
				<div class="col m-2 text-nowrap"><h5 class="modal-title" id="ModalLabel">Přihlašte se prosím</h5></div>
				<div class="col m-2 text-end">
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
			</div>
	
			<div class="row my-2">
			<div class="col">
				<div class="form-floating mt-2 mb-1">
					<input type="text" class="form-control" id="user" name="name" required autofocus>
					<label for="user">Uživatelské jméno</label>
				</div>
			</div>
			</div>
			<div class="row">
			<div class="col">
				<div class="form-floating mb-2">
					<input type="password" class="form-control" id="secret" name="pass" required>
					<label for="secret">Heslo</label>
				</div>
			</div>
			</div>
			<div class="row my-2">
			<div class="col text-end">
				<button class="btn btn-primary">Přihlásit</button>
			</div>
			</div>
		</div>
	</div>
	</div>
	</form>
</div>

<script src="bootstrap.min.js"></script>
<script src="custom.js"></script>

</body>
</html>

