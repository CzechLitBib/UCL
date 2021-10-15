<?php

session_start();

if(empty($_SESSION['auth']) or $_SESSION['group'] !== 'admin') {
	header('Location: /');
	exit();
}

$_SESSION['page'] = 'aleph';

if(!isset($_SESSION['aleph'])) { $_SESSION['aleph'] = Null; }

//if (!empty($_POST['data'])) {
//	$_SESSION['daily'] = $_POST['date'];
//	header("Location: " . $_SERVER['REQUEST_URI']);
//	exit();
//}

//
// http://localhost:8983/solr/core/select?csv.mv.separator=%23&csv.separator=%3B&fl=id%2Ctag_100&indent=true&q.op=OR&q=tag_100%3A*&rows=10&wt=csv'
//

//leader_8 = 000[7]
//year_008 = 008[7-10]

?>

<!DOCTYPE html>
<html><head><title>Aleph Solr</title><meta charset="utf-8"></head>
<body bgcolor="lightgrey">
<div align="center">
<table><tr><td><img src="/sova.png"/></td><td>Aleph Solr Query</td></tr></table>
<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>
<form method="post" action="." enctype="multipart/form-data">
<b>Podmínka/dotaz:</b>
<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>
<table width="550">
<tr><td width="150"><b>Pole</b></td><td width="350"><b>Podpole</b></td></tr>
<tr><td><input type="checkbox" name="field" id="100" value="100"><label>100</label></td></tr>
<input type="checkbox" name="field" id="600" value="600"><label>600</label>
<input type="checkbox" name="field" id="700" value="700"><label>700</label>
<input type="checkbox" name="field" id="773" value="700"><label>773</label>
<input type="checkbox" name="subfield" id="773" value="700t"><label>t</label>
<input type="checkbox" name="subfield" id="773" value="7009"><label>9</label>
<input type="checkbox" name="field" id="964" value="964"><label>964</label>
</table>
</form>
<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>
<table width='500'><tr><td width="450" align="right"><a href="/main"><img src="/back.png"></a></td></tr></table>
</div>
</body>
</html>

