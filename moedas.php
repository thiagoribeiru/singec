<?require_once("session.php");?>
<html>
<head>
<title><?echo $title;?></title>
</head>
<body onunload="window.opener.location.reload();">
<?
//validação página
autorizaPagina(2);

//painel boas vindas
require_once("welcome.php");
echo "<div id=\"divmenu\">\n";
	require_once("menu.php");
echo "\n</div>";

//menu opções
	require_once("submenuconfiguracoes.php");

//tabela
echo "<div class=\"tabelacorpo\">";
echo "<table><tr><td>";
	echo "<table border=1px cellpadding=2px cellspacing=0px>\n";
		echo "<tr>\n";
			echo "<td><center><b>Nome Moeda</b></center></td>\n";
			echo "<td><center><b>Iso</b></center></td>\n";
			echo "<td><center><b>Símbolo</b></center></td>\n";
			echo "<td width=\"90px\"><center><b>Vlr. Compra</b></center></td>\n";
			echo "<td width=\"90px\"><center><b>Vlr. Venda</b></center></td>\n";
			echo "<td><center><b>Última Cotação</b></center></td>\n";
		echo "</tr>\n";
		
		//######### INICIO Paginação
		$numreg = 10;
		// Quantos registros por página vai ser mostrado
		if (!isset($_GET['pg'])) $pg = 0; else $pg=$_GET['pg'];
		$inicial = $pg * $numreg;
		//######### FIM dados Paginação
		
		$stringPesquisa = "select * from moedas where ativo = 1 order by nome";
		//conta os registros para paginação
		$sql_conta = $sql->query($stringPesquisa);
		$quantreg = mysqli_num_rows($sql_conta);
		//lista
		$listMp = $sql->query($stringPesquisa." limit $inicial, $numreg");
		// $linhasMp = /*mysqli_num_rows($listMp)*/$numreg;
		$linhasMp = mysqli_num_rows($listMp)<$numreg ? mysqli_num_rows($listMp) : $numreg;
		for ($i=0;$i<$linhasMp;$i++) {
			$linha = mysqli_fetch_array($listMp);
			echo "<tr>\n";
				echo "<td style=\"height: 20px;\">".$linha['nome']."</td>\n";
				echo "<td><center>".$linha['iso']."</center></td>\n";
				echo "<td><center>".$linha['moeda']."</center></td>\n";
				if ($linha['cod']=="") echo "<td><center></center></td>\n";
					else if ($linha['compra']==0) echo "<td><center>-</center></td>\n";
						else echo "<td align=\"right\"><div class=\"divsimbolo\">".$_SESSION['MoedaSinal']."</div><div class=\"divvalor\">".number_format(cotaToIsoSession($linha['iso'],"compra"),4,",","")."</div></td>\n";
				if ($linha['cod']=="") echo "<td><center></center></td>\n";
					else if ($linha['venda']==0) echo "<td><center>-</center></td>\n";
						else echo "<td align=\"right\"><div class=\"divsimbolo\">".$_SESSION['MoedaSinal']."</div><div class=\"divvalor\">".number_format(cotaToIsoSession($linha['iso'],"venda"),4,",","")."</div></td>\n";
				if ($linha['cod']=="") echo "<td><center></center></td>\n";
					else if ($linha['datetime']==0) echo "<td><center>-</center></td>\n";
						else echo "<td>".date('d/m/Y-H\h:i\m:s\s',strtotime($linha['datetime']))."</td>\n";
			echo "</tr>\n";
		}
	echo "</table></td></tr><tr><td><center>\n";
		if ($quantreg!=0) include("paginacao.php");
	echo "</center></td></tr></table>\n";

echo "<div style=\"font-size: 13px; padding-bottom: 5px;\">Você pode solicitar o cadastramento de mais moedas através do <a href=\"javascript: abrirContato();\" title=\"Entrar em contato\" class=\"linkcontato\">Formulário de Contato</a>.</div>\n";



?>

<?
//menu opções
$sysLista = mysqli_fetch_row($sql->query("SELECT empresa FROM usuarios WHERE id = ".$_SESSION['UsuarioID']));
$nomeEmpresa = mysqli_fetch_array($sql->query("SELECT nome FROM empresas WHERE id_empresa = ".$sysLista[0]));

echo "<ul id=\"submenu\">";
		if ($nomeEmpresa['nome']=="Todas") echo "<li><a href=\"#\" onclick=\"abrirPopup('cadMoeda.php',400,250);\">Cadastrar Moeda</a></li>";
echo "</ul>";

echo "</div>";
?>
</body>
</html>