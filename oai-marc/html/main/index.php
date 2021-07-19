
<?php

session_start();

$_SESSION['page'] = 'main';

if(empty($_SESSION['auth'])) {
	header('Location: /');
	exit();
}

?>

<html>
<head>
<title>UCL Vývoj</title>
<style>
	body	{background-color: lightgrey;}
	a	{text-decoration: none; color: black;}
	#icon	{width: 10px;}
	#link	{width: 450px;}
</style>
</head>
<body>
<div align="center">
<table><tr><td><img src="/sova.png"></td><td>UCL Vývoj</td></tr></table>
<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>
<table>
<tr><td id="icon"><img src="/dir.png"/></td><td id="link"><a href="/daily">Denní</a></td></tr>
<tr><td id="icon"><img src="/dir.png"/></td><td id="link"><a href="/weekly">Týdenní</a></td></tr>
<tr><td id="icon"><img src="/dir.png"/></td><td id="link"><a href="/seven">7</a></td></tr>
<tr><td id="icon"><img src="/dir.png"/></td><td id="link"><a href="/nkp">NKP</a></td></tr>
<tr><td id="icon"><img src="/dir.png"/></td><td id="link"><a href="/kat">KAT</a></td></tr>
<tr><td id="icon"><img src="/link.png"/></td><td id="link"><a href="/clo">CLO</a></td></tr>
<tr><td id="icon"><img src="/link.png"/></td><td id="link"><a href="/ucla">UCLA</a></td></tr>
<tr><td id="icon"><img src="/link.png"/></td><td id="link"><a href="/error">Chybové kódy</a></td></tr>
<tr><td id="icon"><img src="/link.png"/></td><td id="link"><a href="/clanky">Články</a></td></tr>
<tr><td id="icon"><img src="/link.png"/></td><td id="link"><a href="/aktualizace">Aktualizace</a></td></tr>
<tr><td id="icon"><img src="/link.png"/></td><td id="api"><a href="/api">API</a></td></tr>
</table>
<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>
</div>
</body>
</html>

