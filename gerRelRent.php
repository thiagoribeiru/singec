<?
$popup = 1;
require_once("session.php");
?>
<html>
<head>
<title><?echo $title." - Data do Relatório";?></title>
</head>
<body onLoad="document.getElementById('dtIni').focus();">
<?
//validação página
autorizaPagina(2);
teclaEsc();
?>

	<form>
		<fieldset><legend>Insira a Data</legend>
			<table>
				<tr><td><label for="dtIni">Data Inicial:</label></td>
					<td><input type="text" name="dtIni" id="dtIni" size="10" maxlength="10" value="<?echo $_GET['dtIni'];?>" onKeyPress="barra(this)" onFocus="this.select()" /></td></tr>
				<tr><td><label for="dtFin">Data Final:</label></td>
					<td><input type="text" name="dtFin" id="dtFin" size="10" maxlength="10" value="<?echo $_GET['dtFin'];?>" onKeyPress="barra(this)" onFocus="this.select()" /></td></tr>
			</table>
			<div width="100%"><center><button id="gera" onclick="abreRel()">Gerar</button></center></div>
		</fieldset>
	</form>
	<script>
		function abreRel(){
			var dtIni = document.getElementById('dtIni').value;
			var dtFin = document.getElementById('dtFin').value;
			dtIni = dtIni.split("/");
			dtFin = dtFin.split("/");
			window.opener.abreRel(dtIni[2]+"-"+dtIni[1]+"-"+dtIni[0],dtFin[2]+"-"+dtFin[1]+"-"+dtFin[0]);
			window.close();
		}
		function barra(objeto){
			if (objeto.value.length == 2 || objeto.value.length == 5 ){
				objeto.value = objeto.value+"/";
			}
			if (objeto.value.length == 9) {
				if (objeto.getAttribute('id') == 'dtIni')
					document.getElementById('dtFin').focus();
				if (objeto.getAttribute('id') == 'dtFin')
					document.getElementById('gera').focus();
			}
		}
	</script>

</body>
</html>