<?
ini_set('memory_limit', '-1');
set_time_limit(60);
$popup = 1;
require_once("session.php");

//validação página
autorizaPagina(2);
teclaEsc();
?>
<html>
<head>
<title><?echo $title;?></title>
</head>
<style>
	body {
		margin: 0px;
	}
</style>
<body>
<?
if ($_GET['rel']!="") {
	$arquivo = urldecode($_GET['rel']);
	
	echo "<object type=\"application/pdf\"  data=\"$arquivo?#zoom=100\" style=\"width: 100%; height: 100%;\">\n";
	echo "<a href=\"$arquivo?#zoom=100\">Ver PDF</a> <-- Para navegadores que não suportam object -->\n";
	echo "</object>";
}
?>
</body>
</html>