<?require_once("session.php");?>
<html>
<head>
<title>
	<?echo $title;?>
</title>
<script language="JavaScript" type="text/javascript">
	function submitenter(myfield,e)	{
		var keycode;
		if (window.event) keycode = window.event.keyCode;
		else if (e) keycode = e.which;
		else return true;
		
		if (keycode == 13) {
			myfield.form.submit();
			return false;
		} else return true;
	}
</script>
</head>
<body onunload="window.opener.location.reload();" <?if ($_GET["busca"]!="") echo "onload=\"var busca = document.getElementById('txBusca'); busca.focus(); busca.select();\"";?>>
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
		$busca = urlencode($_POST['busca']);
		$buscaCodComp = urlencode($_POST['buscaCodComp']);
		$buscaDescComp = urlencode($_POST['buscaDescComp']);
		// header ("Location: ".$_SERVER['PHP_SELF']."?pg=$pg&busca=$busca");
		echo "<script>location.href=\"".$_SERVER['PHP_SELF']."?pg=$pg&busca=$busca&buscaCodComp=$buscaCodComp&buscaDescComp=$buscaDescComp\"</script>";
		exit;
	}
	else if (!isset($_GET['busca'])) $busca = "";
	else $busca=$_GET['busca'];
	if ($busca!="") $textBusca=" and (descricao like '%".str_replace("*","%",$busca)."%' or observacoes like '%".str_replace("*","%",$busca)."%' or cod_sp like '%".str_replace("*","%",$busca)."%')";
	else $textBusca="";
	if (isset($_GET['buscaCodComp']) and  $_GET['buscaCodComp'] != "") {
		$buscaCodComp = $_GET['buscaCodComp'];
		$buscaCodCompExplode = explode(",",$_GET['buscaCodComp']);
		$query = "select cod_sp from sp_composicao where cod in (";
		for($j=0;$j<count($buscaCodCompExplode);$j++) {
			$query .= "'".$buscaCodCompExplode[$j]."'";
			if ($j!=(count($buscaCodCompExplode)-1)) $query .= ",";
		}
		$query .= ") and ativo = '1' and empresa = '".$_SESSION['UsuarioEmpresa']."' group by cod_sp";
		$pesqCod = $sql->query($query) or die (mysqli_error($sql));
		if ($pesqCod->num_rows > 0) {
			$textBusca .= " and cod_sp in (";
			for ($i=1;$i<=$pesqCod->num_rows;$i++) {
				$codigo = mysqli_fetch_array($pesqCod);
				$textBusca .= "'".$codigo['cod_sp']."'";
				if ($i!=$pesqCod->num_rows) $textBusca .= ",";
			}
			$textBusca .= ")";
		} else {
			$textBusca .= " and cod_sp = NULL";
		}
	}
	if (isset($_GET['buscaDescComp']) and  $_GET['buscaDescComp'] != "") {
		$buscaDescComp = $_GET['buscaDescComp'];
		$empp = $_SESSION['UsuarioEmpresa'];
		$pesqCod = $sql->query("SELECT cod_sp FROM sp_composicao where if(mps='MP',(select descricao from mp where empresa = '$empp' and ativo = '1' and cod_mp = cod),if(mps='PR',(select descricao from processos where empresa = '$empp' and ativo = '1' and cod_pr = cod),(select descricao from sp_dados where empresa = '$empp' and ativo = '1' and sp_dados.cod_sp = cod))) like '%".str_replace("*","%",$buscaDescComp)."%' and ativo = '1' and empresa = '$empp' group by cod_sp") or die (mysqli_error($sql));
		if ($pesqCod->num_rows > 0) {
			$textBusca .= " and cod_sp in (";
			for ($i=1;$i<=$pesqCod->num_rows;$i++) {
				$codigo = mysqli_fetch_array($pesqCod);
				$textBusca .= "'".$codigo['cod_sp']."'";
				if ($i!=$pesqCod->num_rows) $textBusca .= ",";
			}
			$textBusca .= ")";
		} else {
			$textBusca .= " and cod_sp = NULL";
		}
	}
	//final orientador

//tabela
echo "<div>";
echo "<table><tr><td align=\"right\">\n";
	echo "<table><tr><td>";
	echo "<div style=\"margin: 0 15 2;width: 5px; position: relative; float: right;\">|</div>";
	echo "<div  style=\"position: relative; float: right;\"><a href=\"#\" onclick=\"return abrirPopup('cadsp.php',940,535);\">Criar Sub-Produto</a></div>";
	echo "<div style=\"margin: 0 15 2;width: 5px; position: relative; float: right;\">|</div>";
	echo "</td><td>";
	echo "<div style=\"margin: 0 15 2;width: 5px; position: relative; float: right;\">|</div>";
	echo "<div  style=\"position: relative; float: right;\">\n";
		echo "<form action=\"".$_SERVER['PHP_SELF']."?pg=$pg&busca=$busca\" method=\"POST\" style=\"margin:0;\">\n";
			// echo "<fieldset style=\"\"><legend>Filtro</legend>\n";
				echo "<label for=\"txBusca\">Descrição: </label>\n";
				echo "<input type=\"text\" name=\"busca\" id=\"txBusca\" value=\"$busca\" style=\"height:18px; bottom:-1px; position: relative; width: 250px;\" onclick=\"this.select()\" onKeyPress=\"return submitenter(this,event)\">\n";
				echo "<label for=\"txBusca\">Cód Componente: </label>\n";
				echo "<input type=\"text\" name=\"buscaCodComp\" id=\"txBuscaCodComp\" value=\"$buscaCodComp\" style=\"height:18px; bottom:-1px; position: relative; width: 50px;\" onclick=\"this.select()\" onKeyPress=\"return submitenter(this,event)\">\n";
				echo "<label for=\"txBusca\">Desc. Componente: </label>\n";
				echo "<input type=\"text\" name=\"buscaDescComp\" id=\"txBuscaDescComp\" value=\"$buscaDescComp\" style=\"height:18px; bottom:-1px; position: relative; width: 150px;\" onclick=\"this.select()\" onKeyPress=\"return submitenter(this,event)\">\n";
			// echo "</fieldset>\n";
		echo "</form>\n";
	echo "</div>\n";
	echo "</td></tr></table>";
echo "</td></tr>\n"	;
	echo "<tr><td>\n";
	echo "<table border=1px cellpadding=2px cellspacing=0px>\n";
		echo "<tr>\n";
			echo "<td><center>"."<b>COD</b>"."</td>\n";
			echo "<td><center>"."<b>DESCRIÇÃO</b>"."</td>\n";
			echo "<td><center>"."<b>GRUPO</b>"."</td>\n";
			echo "<td width=120px><center>"."<b>CUSTO</b>"."</td>\n";
			echo "<td><center>"."<b>UNI</b>"."</td>\n";
			echo "<td width=55px><center>"."<b>LARG.</b>"."</td>\n";
			echo "<td width=60px><center>"."<b>G/M²</b>"."</td>\n";
			echo "<td><center>"."<b>OBSERVAÇÕES</b>"."</td>\n";
		echo "</tr>\n";
		$stringPesquisa = "select cod_sp, descricao, unidade, largura, gramatura, observacoes, moeda, (select grupo from grupos_de_produto where cod_grupo_prod = sp_dados.grupo and ativo = 1 and empresa = '".$_SESSION['UsuarioEmpresa']."') as grupo from sp_dados where empresa = '".$_SESSION['UsuarioEmpresa']."' and ativo = 1".$textBusca." order by descricao";
		//conta os registros para paginação
		$sql_conta = $sql->query($stringPesquisa);
		$quantreg = mysqli_num_rows($sql_conta);
		//lista
		$listSp = $sql->query($stringPesquisa." limit $inicial, $numreg");
		$linhasSp = /*mysqli_num_rows($listMp)*/$numreg;
		for ($i=0;$i<$linhasSp;$i++) {
			$linha = mysqli_fetch_array($listSp);
				echo "<tr>\n";
					echo "<td><center>".$linha['cod_sp']."</center></td>\n";
					echo "<td><div style=\"width:450px; height:20px; overflow: auto;\">".$linha['descricao']."</div></td>\n";
					echo "<td><div style=\"width:200px; height:20px; overflow: auto;\">".$linha['grupo']."</div></td>\n";
					if ($linha['cod_sp']=="") echo "<td><center></center></td>\n";
						else {
							$simb = mysqli_fetch_array($sql->query("select moeda from moedas where iso = '".$linha['moeda']."' and ativo = 1"));
							echo "<td><div style=\"width: 50%; position: relative; float: left;\" align=\"left\">(".$linha['moeda'].") ".$simb['moeda']."</div><div style=\"width: 50%; position: relative; float: right;\" align=\"right\">".number_format(somaSP($linha['cod_sp']),4,",",".")."</div></td>\n";
						}
					echo "<td><center>".$linha['unidade']."</center></td>\n";
					if ($linha['cod_sp']=="") echo "<td><center></center></td>\n";
						else if (somaLarg($linha['cod_sp'])!=0) echo "<td align=\"right\">".number_format(somaLarg($linha['cod_sp']),2,",","")." m</td>\n";
						else echo "<td align=\"right\"><center>-</center></td>\n";						
					if ($linha['cod_sp']=="") echo "<td><center></center></td>\n";
						else if (somaGR($linha['cod_sp'])!=0) echo "<td align=\"right\">".number_format(somaGR($linha['cod_sp']),0,"",".")." g/m²</td>\n";
						else echo "<td align=\"right\"><center>-</center></td>\n";
					echo "<td><div style=\"width:230px; height:20px; overflow: auto;\">".$linha['observacoes']."</div></td>\n";
					if ($linha['cod_sp']=="") echo "<td width=40px><center></center></td>\n";
						else echo "<td width=40px><a href=\"#\" onclick=\"return abrirPopup('cadsp.php?cod_sp=".$linha['cod_sp']."',940,535);\">editar</a></td>";
				echo "</tr>\n";
		}
	echo "</table>\n</td></tr><tr><td><center>";
		if ($quantreg!=0) include("paginacao.php");
	echo "</td></tr></table>\n";
	
echo "</div>";
?>
</body>
</html>