<?$popup = 1;
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (!isset($_GET['quant'])) {
			setcookie('atualizacadsp'.$_GET['cod_sp'],'atualizar',time()+(24*(60*60)));
		} else if (isset($_GET['quant'])) {
			if (!isset($_POST['quant']) or str_replace(",",".",$_POST['quant'])==0) {
				setcookie('atualizacadsp'.$_GET['cod_sp'],'atualizar',time()+(24*(60*60)));
			} else if ($_GET['quant']!=str_replace(",",".",$_POST['quant'])) {
				setcookie('atualizacadsp'.$_GET['cod_sp'],'atualizar',time()+(24*(60*60)));
			}
		}
	}
require_once("session.php");?>
<html>
<head>
<title><?echo $title." - Quantidades";?></title>
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
		var ok = alert("Defina todas as quantidades!");
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

if (isset($_GET['cod_mp'])) {
	$empresa = $_SESSION['UsuarioEmpresa'];
	$cod_sp = $_GET['cod_sp'];
	$mps = "MP";
	$cod = $_GET['cod_mp'];
	$sqll = mysqli_fetch_array($sql->query("SELECT descricao, moeda, custo, unidade, largura, gramatura from mp where cod_mp = $cod and ativo = 1 and empresa = $empresa")) or die (mysqli_error($sql));	
	$descricao = $sqll['descricao'];
	$moeda = $sqll['moeda'];
	$custo = $sqll['custo'];
	$unidade = $sqll['unidade'];
	$largura = $sqll['largura'];
	$gramatura = $sqll['gramatura'];
	$usuario = $_SESSION['UsuarioID'];
	
echo "<form action=\"".$_SERVER['REQUEST_URI']."\" method=\"post\" id=\"formquant\">";	
	echo "<div class=\"divquantidade\">\n";
		echo "<table border=\"0px\" cellpadding=\"1px\" cellspacing=\"0px\" bgcolor=\"#fffff\" class=\"tablecomposicao\">\n";
			echo "<tr bgcolor=\"#EEEEEE\">\n";
				echo "<td><center><b>M/P/S</td>\n";
				echo "<td width=\"70px\"><center><b>COD</td>\n";
				echo "<td width=\"410px\"><center><b>DESCRIÇÃO</td>\n";
				echo "<td width=\"90px\"><center><b>CUSTO</td>\n";
				echo "<td><center><b>UNI</td>\n";
				echo "<td width=\"90px\"><center><b>LARG.</td>\n";
				echo "<td><center><b>G/M²</td>\n";
				echo "<td width=\"90px\" class=\"colquant\"><center><b>QUANT.</td>\n";
			echo "</tr>\n";
			echo "<tr bgcolor=\"#FFFFFF\" class=\"linhaLink\">\n";
				echo "<td onclick=\"abrirPopup('editMp.php?cod_mp=$cod',500,335)\"><center>$mps</td>\n";
				echo "<td onclick=\"abrirPopup('editMp.php?cod_mp=$cod',500,335)\"><center>$cod</td>\n";
				echo "<td onclick=\"abrirPopup('editMp.php?cod_mp=$cod',500,335)\">$descricao</td>\n";
				echo "<td onclick=\"abrirPopup('editMp.php?cod_mp=$cod',500,335)\">$moeda ".number_format($custo,2,",","")."</td>\n";
				echo "<td onclick=\"abrirPopup('editMp.php?cod_mp=$cod',500,335)\"><center>$unidade</td>\n";
				echo "<td onclick=\"abrirPopup('editMp.php?cod_mp=$cod',500,335)\"><center>".number_format($largura,2,",","")."</td>\n";
				echo "<td onclick=\"abrirPopup('editMp.php?cod_mp=$cod',500,335)\"><center>".number_format($gramatura,0,"",".")."</td>\n";
				if (isset($_GET['quant'])) {
						$getquant = str_replace(".",",",$_GET['quant']);
						echo "<td class=\"colquant\"><input type=\"text\" name=\"quant\" id=\"txQuant\" maxlength=\"20\" size=\"10\" value=\"$getquant\"/></td>\n";
					} else echo "<td class=\"colquant\"><input type=\"text\" name=\"quant\" id=\"txQuant\" maxlength=\"20\" size=\"10\"/></td>\n";
			echo "</tr>\n";
		echo "</table>\n";
	echo "</div>\n";
	echo "<div style=\"margin-top: 5px; margin-bottom: -5px;\">\n<center>";
		if (isset($_GET['quant'])) echo "<input type=\"button\" value=\"Atualizar\" onclick=\"confirmaAtualizar()\"/>";
		else echo "<input type=\"button\" value=\"Adicionar\" onclick=\"confirma()\"/>";
		echo "<input type=\"button\" value=\"Cancelar\" onclick=\"javascript:window.close()\"/>";
	echo "</div>\n";
echo "</form>";
}
else if (isset($_GET['cod_pr'])) {
	$empresa = $_SESSION['UsuarioEmpresa'];
	$cod_sp = $_GET['cod_sp'];
	$mps = "PR";
	$cod = $_GET['cod_pr'];
	$sqll = mysqli_fetch_array($sql->query("SELECT descricao, moeda, custo, unidade, largura from processos where cod_pr = $cod and ativo = 1 and empresa = $empresa")) or die (mysqli_error($sql));	
	$descricao = $sqll['descricao'];
	$moeda = $sqll['moeda'];
	$custo = $sqll['custo'];
	$unidade = $sqll['unidade'];
	$largura = $sqll['largura'];
	$usuario = $_SESSION['UsuarioID'];
	
echo "<form action=\"".$_SERVER['REQUEST_URI']."\" method=\"post\" id=\"formquant\">";	
	echo "<div class=\"divquantidade\">\n";
		echo "<table border=\"0px\" cellpadding=\"1px\" cellspacing=\"0px\" bgcolor=\"#fffff\" class=\"tablecomposicao\">\n";
			echo "<tr bgcolor=\"#EEEEEE\">\n";
				echo "<td><center><b>M/P/S</td>\n";
				echo "<td width=\"70px\"><center><b>COD</td>\n";
				echo "<td width=\"410px\"><center><b>DESCRIÇÃO</td>\n";
				echo "<td width=\"90px\"><center><b>CUSTO</td>\n";
				echo "<td><center><b>UNI</td>\n";
				echo "<td width=\"90px\"><center><b>LARG.</td>\n";
				echo "<td><center><b>G/M²</td>\n";
				echo "<td width=\"90px\" class=\"colquant\"><center><b>QUANT.</td>\n";
			echo "</tr>\n";
			echo "<tr bgcolor=\"#FFFFFF\" class=\"linhaLink\">\n";
				echo "<td onclick=\"abrirPopup('editPr.php?cod_pr=$cod',500,335)\"><center>$mps</td>\n";
				echo "<td onclick=\"abrirPopup('editPr.php?cod_pr=$cod',500,335)\"><center>$cod</td>\n";
				echo "<td onclick=\"abrirPopup('editPr.php?cod_pr=$cod',500,335)\">$descricao</td>\n";
				echo "<td onclick=\"abrirPopup('editPr.php?cod_pr=$cod',500,335)\">$moeda ".number_format($custo,2,",","")."</td>\n";
				echo "<td onclick=\"abrirPopup('editPr.php?cod_pr=$cod',500,335)\"><center>$unidade</td>\n";
				echo "<td onclick=\"abrirPopup('editPr.php?cod_pr=$cod',500,335)\"><center>".number_format($largura,2,",","")."</td>\n";
				echo "<td onclick=\"abrirPopup('editPr.php?cod_pr=$cod',500,335)\"><center>-</td>\n";
				if (isset($_GET['quant'])) {
						$getquant = str_replace(".",",",$_GET['quant']);
						echo "<td class=\"colquant\"><input type=\"text\" name=\"quant\" id=\"txQuant\" maxlength=\"20\" size=\"10\" value=\"$getquant\"/></td>\n";
					} else echo "<td class=\"colquant\"><input type=\"text\" name=\"quant\" id=\"txQuant\" maxlength=\"20\" size=\"10\"/></td>\n";
			echo "</tr>\n";
		echo "</table>\n";
	echo "</div>\n";
	echo "<div style=\"margin-top: 5px; margin-bottom: -5px;\">\n<center>";
		if (isset($_GET['quant'])) echo "<input type=\"button\" value=\"Atualizar\" onclick=\"confirmaAtualizar()\"/>";
		else echo "<input type=\"button\" value=\"Adicionar\" onclick=\"confirma()\"/>";
		echo "<input type=\"button\" value=\"Cancelar\" onclick=\"javascript:window.close()\"/>";
	echo "</div>\n";
echo "</form>";
}
else if (isset($_GET['sp'])) {
	$empresa = $_SESSION['UsuarioEmpresa'];
	$cod_sp = $_GET['cod_sp'];
	$mps = "SP";
	$cod = $_GET['sp'];
	$sqll = mysqli_fetch_array($sql->query("SELECT descricao, moeda, unidade, largura from sp_dados where cod_sp = $cod and ativo = 1 and empresa = $empresa")) or die (mysqli_error($sql));	
	$descricao = $sqll['descricao'];
	$moeda = $sqll['moeda'];
	$custo = somaSP($cod);
	$unidade = $sqll['unidade'];
	$largura = $sqll['largura'];
	$usuario = $_SESSION['UsuarioID'];
	
	echo "<form action=\"".$_SERVER['REQUEST_URI']."\" method=\"post\" id=\"formquant\">";	
		echo "<div class=\"divquantidade\">\n";
			echo "<table border=\"0px\" cellpadding=\"1px\" cellspacing=\"0px\" bgcolor=\"#fffff\" class=\"tablecomposicao\">\n";
				echo "<tr bgcolor=\"#EEEEEE\">\n";
					echo "<td><center><b>M/P/S</td>\n";
					echo "<td width=\"70px\"><center><b>COD</td>\n";
					echo "<td width=\"410px\"><center><b>DESCRIÇÃO</td>\n";
					echo "<td width=\"90px\"><center><b>CUSTO</td>\n";
					echo "<td><center><b>UNI</td>\n";
					echo "<td width=\"90px\"><center><b>LARG.</td>\n";
					echo "<td><center><b>G/M²</td>\n";
					echo "<td width=\"90px\" class=\"colquant\"><center><b>QUANT.</td>\n";
				echo "</tr>\n";
				echo "<tr bgcolor=\"#FFFFFF\" class=\"linhaLink\">\n";
					echo "<td onclick=\"abrirPopup('cadsp.php?cod_sp=$cod',940,535,'fechar')\"><center>$mps</td>\n";
					echo "<td onclick=\"abrirPopup('cadsp.php?cod_sp=$cod',940,535,'fechar')\"><center>$cod</td>\n";
					echo "<td onclick=\"abrirPopup('cadsp.php?cod_sp=$cod',940,535,'fechar')\">$descricao</td>\n";
					echo "<td onclick=\"abrirPopup('cadsp.php?cod_sp=$cod',940,535,'fechar')\">$moeda ".number_format($custo,2,",","")."</td>\n";
					echo "<td onclick=\"abrirPopup('cadsp.php?cod_sp=$cod',940,535,'fechar')\"><center>$unidade</td>\n";
					echo "<td onclick=\"abrirPopup('cadsp.php?cod_sp=$cod',940,535,'fechar')\"><center>".number_format($largura,2,",","")."</td>\n";
					echo "<td onclick=\"abrirPopup('cadsp.php?cod_sp=$cod',940,535,'fechar')\"><center>-</td>\n";
					if (isset($_GET['quant'])) {
						$getquant = str_replace(".",",",$_GET['quant']);
						echo "<td class=\"colquant\"><input type=\"text\" name=\"quant\" id=\"txQuant\" maxlength=\"20\" size=\"10\" value=\"$getquant\"/></td>\n";
					} else echo "<td class=\"colquant\"><input type=\"text\" name=\"quant\" id=\"txQuant\" maxlength=\"20\" size=\"10\"/></td>\n";
				echo "</tr>\n";
			echo "</table>\n";
		echo "</div>\n";
		echo "<div style=\"margin-top: 5px; margin-bottom: -5px;\">\n<center>";
			if (isset($_GET['quant'])) echo "<input type=\"button\" value=\"Atualizar\" onclick=\"confirmaAtualizar()\"/>";
		else echo "<input type=\"button\" value=\"Adicionar\" onclick=\"confirma()\"/>";
			echo "<input type=\"button\" value=\"Cancelar\" onclick=\"javascript:window.close()\"/>";
		echo "</div>\n";
	echo "</form>";
}

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (!isset($_GET['quant'])) {
			if (!isset($_POST['quant']) or str_replace(",",".",$_POST['quant'])==0) {echo "<script>quantzerada('".$_SERVER['REQUEST_URI']."');</script>"; exit;}
			$quantidade = str_replace(",",".",$_POST['quant']);
			$sql->query("insert into sp_composicao (mps, cod, cod_sp, quantidade, empresa, data, usuario, ativo) values ('$mps','$cod','$cod_sp','$quantidade','$empresa',now(),'$usuario','1')") or die (mysqli_error($sql));
			// setcookie('atualizacadsp'.$_GET['cod_sp'],'atualizar',time()+(24*(60*60)));
			echo "<script>window.close();</script>";
		} else if (isset($_GET['quant'])) {
			if (!isset($_POST['quant']) or str_replace(",",".",$_POST['quant'])==0) {
				$sql->query("update sp_composicao set ativo='0' where mps = '$mps' and cod = $cod and cod_sp = $cod_sp and ativo = 1 and empresa = $empresa") or die (mysqli_error($sql));
				// setcookie('atualizacadsp'.$_GET['cod_sp'],'atualizar',time()+(24*(60*60)));
				echo "<script>window.close();</script>";
			} else if ($_GET['quant']!=str_replace(",",".",$_POST['quant'])) {
				$quantidade = str_replace(",",".",$_POST['quant']);
				$sql->query("update sp_composicao set ativo='0' where mps = '$mps' and cod = $cod and cod_sp = $cod_sp and ativo = 1 and empresa = $empresa") or die (mysqli_error($sql));
				$sql->query("insert into sp_composicao (mps, cod, cod_sp, quantidade, empresa, data, usuario, ativo) values ('$mps','$cod','$cod_sp','$quantidade','$empresa',now(),'$usuario','1')") or die (mysqli_error($sql));
				// setcookie('atualizacadsp'.$_GET['cod_sp'],'atualizar',time()+(24*(60*60)));
				echo "<script>window.close();</script>";
			}
		}
	}

?>
</body>
</html>