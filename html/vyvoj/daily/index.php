<?php
  
session_start();

$_SESSION['page'] = 'daily';

if(empty($_SESSION['auth'])) {
        header('Location: /vyvoj/');
        exit();
}

if($_SESSION['group'] !== 'admin') {
        $_SESSION['error'] = True;
        header('Location: /vyvoj/main');
        exit();
}

if(!isset($_SESSION['daily'])) { $_SESSION['daily'] = Null; }

if (!empty($_POST['date'])) {
        $_SESSION['daily'] = $_POST['date'];
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
}

?>

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
		<img src="../logo.png" alt="ČLB" width="60" height="35" class="d-inline-block align-text-center">Vývoj # Daily</a>
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

<form class="mb-4" method="post" action="." enctype="multipart/form-data">
	<div class="row justify-content-center">
		<div class="col col-5">
<?php

$default = date("Y-m-d", strtotime("-1 day"));
if (!empty($_SESSION['daily'])) { $default = $_SESSION['daily']; }

echo '<input type="date" class="form-control" name="date" value="' . $default . '" min="2020-03-02" max="' . date("Y-m-d", strtotime("-1 day")) . '">';

?>

		</div>
		<div class="col col-2">
			<button class="btn btn-danger" type="submit">Zobrazit</button>
		</div>
	</div>
</form>

<?php

if (!empty($_SESSION['daily'])){
        if (preg_match('/\d{4}-\d{2}-\d{2}/', $_SESSION['daily'])) {

                $file =  'data/' . preg_replace('/(\d{4})-(\d{2})-.*/', '${1}/${2}', $_SESSION['daily']) . '/' . $_SESSION['daily'] . '.csv';

                if (file_exists($file)) {
                        $csv = array();
                        $row = 0;
                        if (($handle = fopen($file, 'r')) !== FALSE) {
                                while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                                        $num = count($data);
                                        for ($c=0;  $c < $num; $c++) {
                                                $csv[$row][] = $data[$c];
                                        }
                                        $row++;
                                }
                                fclose($handle);
                        }
		
			echo '<table class="table">';
			echo '<thead class=""><tr><th class="text-center" scope="col">SysNo</th><th class="text-center" scope="col">SIF</th><th scope="col">Kód</th><th scope="col">Popis</th></tr>';
			echo '</thead><tbody>';

			array_multisort(array_column($csv,0), SORT_DESC, SORT_NUMERIC, $csv);
                        foreach($csv as $row) {
                                echo '<tr><th class="text-center"><a class="text-dark text-decoration-none" target="_blank" href="' . 'https://aleph22.lib.cas.cz/F/?func=direct&doc_number='
					. $row[0] . '&local_base=AV&format=001"><b>' . $row[0] . '</b></a></th>';
				echo '<td class="text-center">' . $row[1] . '</td>';
				echo '<td>' . '<a class="text-dark text-decoration-none" href="/vyvoj/error/#' . $row[2] . '"><b>' . $row[2] . '</b></a></td>';
				echo '<td>' . $row[3] . '</td></tr>';
                        }

			echo '</tbody></table>';

                } else {
                        echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">Žádná data.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                }
        }
}

?>

</div>
</div>
</main>

<script src="../bootstrap.min.js"></script>

</body>
</html>

