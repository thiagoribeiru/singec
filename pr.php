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
	require_once("submenucadastro.php");

//######### INICIO Paginação
	$numreg = 15;
	// Quantos registros por página vai ser mostrado
	if (!isset($_GET['pg'])) $pg = 0; else $pg=$_GET['pg'];
	$inicial = $pg * $numreg;
//######### FIM dados Paginação
	
	//orientador de metodo para trabalho com paginação e busca
	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		$busca=urlencode($_POST['busca']);
		// header ("Location: ".$_SERVER['PHP_SELF']."?pg=$pg&busca=$busca");
		echo "<script>location.href=\"".$_SERVER['PHP_SELF']."?pg=$pg&busca=$busca\"</script>";
		exit;
	}
	else if (!isset($_GET['busca'])) $busca = "";
	else $busca=$_GET['busca'];
	if ($busca!="") $textBusca=" and (descricao like '%".str_replace("*","%",$busca)."%' or observacoes like '%".str_replace("*","%",$busca)."%' or cod_pr like '%".str_replace("*","%",$busca)."%')"; else $textBusca="";
	//final orientador

//tabela
echo "<div>";
echo "<table width=1255px><tr><td align=\"right\">\n";
	echo "<table><tr><td>";
	echo "<div style=\"margin: 0 15 2;width: 5px; position: relative; float: right;\">|</div>";
	echo "<div  style=\"position: relative; float: right;\"><a href=\"#\" onclick=\"window.open('cadpr.php', 'Cadastro de Processos', 'STATUS=NO, TOOLBAR=NO, ".
	"LOCATION=NO, DIRECTORIES=NO, RESISABLE=NO, SCROLLBARS=YES, TOP=200, LEFT=200, WIDTH=580, HEIGHT=385');\">Adicionar</a></div>";
	echo "<div style=\"margin: 0 15 2;width: 5px; position: relative; float: right;\">|</div>";
	echo "</td><td>";
	echo "<div style=\"margin: 0 15 2;width: 5px; position: relative; float: right;\">|</div>";
	echo "<div  style=\"position: relative; float: right;\"><form action=\"".$_SERVER['PHP_SELF']."?pg=$pg&busca=$busca\" method=\"POST\" style=\"margin:0;\">
	<label for=\"txBusca\">Procucar: </label><input type=\"text\" name=\"busca\" id=\"txBusca\" value=\"$busca\" style=\"height:18px; bottom:-1px; position: relative;\"></form></div>";
	echo "</td></tr></table>";
echo "</td></tr>\n"	;
	echo "<tr><td>\n";
	echo "<table border=1px cellpadding=2px cellspacing=0px>\n";
		echo "<tr>\n";
			echo "<td><center>"."<b>COD</b>"."</td>\n";
			echo "<td><center>"."<b>DESCRIÇÃO</b>"."</td>\n";
			echo "<td width=120px><center>"."<b>CUSTO</b>"."</td>\n";
			echo "<td><center>"."<b>UNI</b>"."</td>\n";
			echo "<td width=55px><center>"."<b>LARG.</b>"."</td>\n";
			echo "<td width=60px><center>"."<b>ICMS</b>"."</td>\n";
			echo "<td width=60px><center>"."<b>PIS/COF</b>"."</td>\n";
			echo "<td width=60px><center>"."<b>FRETE</b>"."</td>\n";
			echo "<td width=60px><center>"."<b>QUEB</b>"."</td>\n";
			echo "<td width=60px><center>"."<b>SET/FOR</b>"."</td>\n";
			echo "<td width=90px><center>"."<b>LIQUIDO</b>"."</td>\n";
			echo "<td><center>"."<b>OBSERVAÇÕES</b>"."</td>\n";
		echo "</tr>\n";
		$stringPesquisa = "select indice, cod_pr, descricao, moeda, custo, unidade, largura, setor_fornec, icms, piscofins, frete, quebra,".
					" observacoes from processos where empresa = ".$_SESSION['UsuarioEmpresa']." and ativo = 1".$textBusca." order by descricao";
		//conta os registros para paginação
		$sql_conta = $sql->query($stringPesquisa);
		$quantreg = mysqli_num_rows($sql_conta);
		//lista
		$listPr = $sql->query($stringPesquisa." limit $inicial, $numreg");
		$linhasPr = /*mysqli_num_rows($listMp)*/$numreg;
		for ($i=0;$i<$linhasPr;$i++) {
			$linha = mysqli_fetch_array($listPr);
				echo "<tr>\n";
					echo "<td><center>".$linha['cod_pr']."</center></td>\n";
					echo "<td><div style=\"width:220px; height:20px; overflow: auto;\">".$linha['descricao']."</div></td>\n";
					if ($linha['cod_pr']=="") echo "<td><center></center></td>\n";
						else {
							$simb = mysqli_fetch_array($sql->query("select moeda from moedas where iso = '".$linha['moeda']."' and ativo = 1"));
							echo "<td><div style=\"width: 45%; position: relative; float: left;\" align=\"left\">(".$linha['moeda'].
						")".$simb['moeda']."</div><div style=\"width: 55%; position: relative; float: right;\" align=\"right\">".number_format($linha['custo'],4,",",".")."</div></td>\n";
						}
					echo "<td><center>".$linha['unidade']."</center></td>\n";
					if ($linha['cod_pr']=="") echo "<td><center></center></td>\n";
						else echo "<td align=\"right\">".number_format($linha['largura'],2,",","")." m</td>\n";
					if ($linha['cod_pr']=="") echo "<td><center></center></td>\n";
						else if ($linha['icms']==0) echo "<td><center>-</center></td>\n";
							else echo "<td align=\"right\">".number_format($linha['icms'],2,",","")." %</td>\n";
					if ($linha['cod_pr']=="") echo "<td><center></center></td>\n";
						else if ($linha['piscofins']==0) echo "<td><center>-</center></td>\n";
							else echo "<td align=\"right\">".number_format($linha['piscofins'],2,",","")." %</td>\n";
					if ($linha['cod_pr']=="") echo "<td><center></center></td>\n";
						else if ($linha['frete']==0) echo "<td><center>-</center></td>\n";
							else echo "<td align=\"right\">".number_format($linha['frete'],2,",","")." %</td>\n";
					if ($linha['cod_pr']=="") echo "<td><center></center></td>\n";
						else if ($linha['quebra']==0) echo "<td><center>-</center></td>\n";
							else echo "<td align=\"right\">".number_format($linha['quebra'],2,",","")." %</td>\n";
					if ($linha['cod_pr']=="") echo "<td><center></center></td>\n";
					else {
						if ($linha['setor_fornec']==1) echo "<td>"."Interno"."</td>\n";
						if ($linha['setor_fornec']==2) echo "<td>"."Externo"."</td>\n";
					}
					if ($linha['cod_pr']=="") echo "<td><center></center></td>\n";
						else {
							$arg3 = vlrLiquido($linha['cod_pr'],"PR");
							echo "<td><div style=\"width: 23%; position: relative; float: left;\" align=\"left\">".$_SESSION['MoedaSinal']."</div><div style=\"width: 77%; position: relative; float: right;\" align=\"right\">".number_format(cotaToIsoSession($linha['moeda'],"compra",$arg3),"4",",",".")."</div></td>\n";
						}
					echo "<td><div style=\"width:230px; height:20px; overflow: auto;\">".$linha['observacoes']."</div></td>\n";
					if ($linha['cod_pr']=="") echo "<td width=40px><center></center></td>\n";
						else echo "<td width=40px><a href=\"#\" onclick=\"window.open('editPr.php?cod_pr=".$linha['cod_pr']."', 'Edição de Processos', 'STATUS=NO,". 
							"TOOLBAR=NO, LOCATION=NO, DIRECTORIES=NO, RESISABLE=NO, SCROLLBARS=YES, WIDTH=580, HEIGHT=385');\">editar</a></td>";
					if ($linha['cod_pr']=="") echo "<td width=52px><center></center></td>\n";
						else echo "<td width=52px><a href=\"#\" onclick=\"window.open('versPr.php?cod_pr=".$linha['cod_pr']."', 'Histórico de Versões');\">versões</a></td>";
				echo "</tr>\n";
		}
	echo "</table>\n</td></tr><tr><td><center>";
		if ($quantreg!=0) include("paginacao.php");
	echo "</td></tr></table>\n";
	
echo "</div>";
?>
</body>
</html>