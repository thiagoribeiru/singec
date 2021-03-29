<?$popup = 1;
require_once("session.php");?>
<html>
<head>
<title><?echo $title." - Comissão por Grupo";?></title>
<script type="text/javascript">
	function confirma() {
		var ok = confirm("Tem certeza que deseja adicionar este item?");
		if(ok){
			document.getElementById("formquant").submit()
		};
	}
	function confirmaAtualizar() {
		var ok = confirm("Tem certeza que deseja atualizar este item?");
		if(ok){
			document.getElementById("formquant").submit()
		};
	}
	function quantzerada(url) {
		var ok = alert("Defina todos os campos!");
		if(ok){
			location.href=url;
		}
	}
</script>
</head>
<body onLoad="document.getElementById('txQuant').focus();" bgcolor="#CEECF5">
<?
//validação página
autorizaPagina(2);
teclaEsc();
    
    $sysLista = mysqli_fetch_row($sql->query("SELECT empresa FROM usuarios WHERE id = ".$_SESSION['UsuarioID'])) or die (mysqli_error($sql));
	$nomeEmpresa = mysqli_fetch_array($sql->query("SELECT nome FROM empresas WHERE id_empresa = ".$sysLista[0])) or die (mysqli_error($sql));
	if (isset($_GET['emp']) and $nomeEmpresa['nome']=="Todas") $empresa = $_GET['emp'];
	else $empresa = $_SESSION['UsuarioEmpresa'];
	$cod_rep = $_GET['cod_rep'];
	$cod_grupo = $_GET['cod_grupo'];
	$sqll = mysqli_fetch_array($sql->query("SELECT grupo from grupos_de_produto where cod_grupo_prod = '$cod_grupo' and ativo = 1 and empresa = '$empresa'")) or die (mysqli_error($sql));	
	$grupo = $sqll['grupo'];
	$comissPesq = $sql->query("select comiss from representantes_grupos where cod_rep = '$cod_rep' and ativo = 1 and empresa = '$empresa' and cod_grupo_prod = '$cod_grupo'") or die (mysqli_error($sql));
	if (mysqli_num_rows($comissPesq)>0) {
		$vetorComiss = mysqli_fetch_array($comissPesq);
		$comissao = number_format($vetorComiss['comiss'],4,",","");
	}
	$usuario = $_SESSION['UsuarioID'];
	
echo "<form action=\"".$_SERVER['REQUEST_URI']."\" method=\"post\" id=\"formquant\">";	
	echo "<div class=\"divquantidade\">\n";
		echo "<table border=\"0px\" cellpadding=\"1px\" cellspacing=\"0px\" bgcolor=\"#fffff\" class=\"tablecomposicao\">\n";
			echo "<tr bgcolor=\"#EEEEEE\">\n";
				echo "<td width=\"240px\"><center><b>GRUPO</td>\n";
				echo "<td width=\"60px\" class=\"colquant\"><center><b>COMISSÃO</td>\n";
			echo "</tr>\n";
			echo "<tr bgcolor=\"#FFFFFF\">\n";
				echo "<td>$grupo</td>\n";
				echo "<td class=\"colquant\"><input type=\"text\" name=\"comissao\" id=\"txQuant\" maxlength=\"20\" size=\"10\" value=\"$comissao\" style=\"text-align: right;\" onfocus=\"this.select();\"/></td>\n";
			echo "</tr>\n";
		echo "</table>\n";
	echo "</div>\n";
	echo "<div style=\"margin-top: 5px; margin-bottom: -5px;\">\n<center>";
		if ($comissao!="") echo "<input type=\"button\" value=\"Atualizar\" onclick=\"confirmaAtualizar()\"/>";
		else echo "<input type=\"button\" value=\"Adicionar\" onclick=\"confirma()\"/>";
		echo "<input type=\"button\" value=\"Cancelar\" onclick=\"javascript:window.close()\"/>";
	echo "</div>\n";
echo "</form>";

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (!isset($comissao)) {
			if ($_POST['comissao']=="") {echo "<script>quantzerada('".$_SERVER['REQUEST_URI']."');</script>"; exit;}
			$quantidade = str_replace(",",".",$_POST['comissao']);
			$sql->query("insert into representantes_grupos (cod_rep, cod_grupo_prod, comiss, empresa, data, usuario, ativo) values ('$cod_rep','$cod_grupo','$quantidade','$empresa',now(),'$usuario','1')") or die (mysqli_error($sql));
			// setcookie('atualizacadsp'.$_GET['cod_sp'],'atualizar',time()+(24*(60*60)));
			echo "<script>atualizaPagMae(); window.close();</script>";
		} else if (isset($comissao)) {
			if ($_POST['comissao']=="") {
				$sql->query("update representantes_grupos set ativo='0' where cod_rep = '$cod_rep' and cod_grupo_prod = '$cod_grupo' and ativo = 1 and empresa = '$empresa'") or die (mysqli_error($sql));
				// setcookie('atualizacadsp'.$_GET['cod_sp'],'atualizar',time()+(24*(60*60)));
				echo "<script>atualizaPagMae(); window.close();</script>";
			} else {
				$quantidade = str_replace(",",".",$_POST['comissao']);
				$sql->query("update representantes_grupos set ativo='0' where cod_rep = '$cod_rep' and cod_grupo_prod = '$cod_grupo' and ativo = 1 and empresa = '$empresa'") or die (mysqli_error($sql));
				$sql->query("insert into representantes_grupos (cod_rep, cod_grupo_prod, comiss, empresa, data, usuario, ativo) values ('$cod_rep','$cod_grupo','$quantidade','$empresa',now(),'$usuario','1')") or die (mysqli_error($sql));
				// setcookie('atualizacadsp'.$_GET['cod_sp'],'atualizar',time()+(24*(60*60)));
				echo "<script>atualizaPagMae(); window.close();</script>";
			}
		}
	}

?>
</body>
</html>