
<?php

session_start();

$_SESSION['page'] = 'main.php';

if(empty($_SESSION['auth'])) {
	header('Location: index.php');
	exit();
}

?>

<html>
<head>
<style>
	body	{background-color: lightgrey;}
	a	{text-decoration: none; color: black;}
	#folder {content:url(../icons/folder.gif);}
	#forward {content:url(../icons/forward.gif);}
	#icon	{width: 10px;}
	#link	{width: 450px;}
</style>

</head>
<body>
<div align="center">
<table><tr><td><img src="sova.png"><td><td>UCL Vývoj</td></tr></table>
<p><hr width="500"></p>
<table border="0">
<tr><td id="icon"><img src="dir.png"/></td><td id="link"><a href="daily">denní</a></td></tr>
<tr><td id="icon"><img src="dir.png"/></td><td id="link"><a href="weekly">týdenní</a></td></tr>
<tr><td id="icon"><img src="dir.png"/></td><td id="link"><a href="kat">KAT</a></td></tr>
<tr><td id="icon"><img src="dir.png"/></td><td id="link"><a href="nkp">NKP</a></td></tr>
<tr><td id="icon"><img src="link.png"/></td><td id="link"><a href="7">7</a></td></tr>
<tr><td id="icon"><img src="link.png"/></td><td id="link"><a href="error">chybové kódy</a></td></tr>
<tr><td id="icon"><img src="link.png"/></td><td id="link"><a href="clanky">články</a></td></tr>
<tr><td id="icon"><img src="link.png"/></td><td id="link"><a href="aktualizace">aktualizace</a></td></tr>
<tr><td id="icon"><img src="link.png"/></td><td id="api"><a href="api">API</a></td></tr>
</table>
<p><hr width="500"></p>
</div>
</body>
</html>

