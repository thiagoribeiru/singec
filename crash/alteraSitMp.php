<html><body onunload="atualizaMp();">
<script type="text/javascript">
	function atualizaMp() {
		opener.location.reload();
	}
</script>
<?
require_once ("session.php");
autorizaPagina(2);

$indice = $_GET['indice'];
$pesquisa = "select ativo from mp where indice = $indice";
$sql = mysql_query($pesquisa);
$result = mysql_fetch_row($sql);

if ($result[0]==0) $update = "update mp set ativo = '1' where indice = $indice";
if ($result[0]==1) $update = "update mp set ativo = '0' where indice = $indice";

$sqlup = mysql_query($update);
if ($sqlup) echo "<script>window.close();</script>";
else echo "Algo deu errado!";
?>
</body></html>