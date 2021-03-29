<?
$popup = 1;
if (isset($_GET['cod_sp'])) {
	if (isset($_COOKIE['atualizacadsp'.$_GET['cod_sp']])) {
		setcookie ('atualizacadsp'.$_GET['cod_sp']);
	} 
}
require_once("session.php");
?>
<html>
<head>
<title><?echo $title." - Cadastro de Sub-Produtos";?></title>
<script type="text/javascript">
	function confirma() {
		var ok = confirm("Tem certeza que deseja salvar este item?");
		if(ok){
			document.getElementById("formcadsp").submit()
		};
	}
	function atualizar() {
		location.href="cadsp.php";
	}
	function atualizar2(url) {
		location.href=url;
	}
	function confirmaAntes() {
		var ok = confirm("Você irá salvar o item antes de adicionar composição. Deseja prosseguir?");
		if(ok){
			document.getElementById("formcadsp").submit()
		};
	}
	var txCod = "";
	var txDescricao = "";
	var txUnidade = "";
	var txLargura = "";
	var txGramatura = "";
	var txMoeda = "";
	var txObservacoes = "";
	var txGrupo = "";
	function salvaEabre(url) {
		var txCod2 = document.getElementById('txCod').value;
		var txDescricao2 = document.getElementById('txDescricao').value;
		var txUnidade2 = document.getElementById('txUnidade').value;
		var txLargura2 = document.getElementById('txLargura').value;
		var txGramatura2 = document.getElementById('txGramatura').value;
		var txMoeda2 = document.getElementById('txMoeda').value;
		var txObservacoes2 = document.getElementById('txObservacoes').value;
		var txGrupo2 = document.getElementById('txGrupo').value;
		if (txCod!=txCod2 || txDescricao!=txDescricao2 || txUnidade!=txUnidade2 || txLargura!=txLargura2 || txGramatura!=txGramatura2 || txMoeda!=txMoeda2 || txObservacoes!=txObservacoes2 || txGrupo!=txGrupo2) {
			document.getElementById("formcadsp").submit();
		}
			abrirPopup(url,520,385);
	}
	function recarrega(nomecookie) {
		if (getCookie(nomecookie)=="atualizar") {
			window.location.reload();
		}
	}
	function pegaSitInicial() {
		txCod = document.getElementById('txCod').value;
		txDescricao = document.getElementById('txDescricao').value;
		txUnidade = document.getElementById('txUnidade').value;
		txLargura = document.getElementById('txLargura').value;
		txGramatura = document.getElementById('txGramatura').value;
		txMoeda = document.getElementById('txMoeda').value;
		txObservacoes = document.getElementById('txObservacoes').value;
		txGrupo = document.getElementById('txGrupo').value;
	}
	function _GET(name)	{
		var url   = window.location.search.replace("?", "");
		var itens = url.split("&");
		for(n in itens)	{
			if( itens[n].match(name) ) {
				return decodeURIComponent(itens[n].replace(name+"=", ""));
			}
		}
		return null;
	}
	document.onkeydown = function(e){
		var keychar;
		// Internet Explorer
		try {
			keychar = String.fromCharCode(event.keyCode);
			e = event;
		}
		// Firefox, Opera, Chrome, etc...
		catch(err) {
			keychar = String.fromCharCode(e.keyCode);
		}
		if (e.altKey && (keychar=='M' || keychar=='P' || keychar=='I')) {
			var cod_sp = _GET('cod_sp');
			if (cod_sp==null) {
				confirmaAntes();
				return false;
			} else {
				if (keychar=='M') salvaEabre('tosp.php?cod_sp='+cod_sp+'&add=MP');
				if (keychar=='P') salvaEabre('tosp.php?cod_sp='+cod_sp+'&add=PR');
				if (keychar=='I') salvaEabre('tosp.php?cod_sp='+cod_sp+'&add=SP');
				return false;
			}
		}
		if (e.altKey && keychar=='S') {confirma(); return false;}
		if (e.altKey && keychar=='N') {atualizar(); return false;}
		if (e.altKey && keychar=='D') {window.close(); return false;}
		if (e.altKey && keychar=='C' && document.getElementById("txCodNew").value!="") {clonar(); return false;}
	}
	function clonar() {
		document.getElementById("txCodCopy").value = document.getElementById("txCod").value;
		document.getElementById("txCod").value = "";
		document.getElementById("txCod").style = "";
		document.getElementById("txCod").removeAttribute("readonly");
		document.getElementById("txCod").setAttribute("onfocus","Tip('Deixe o COD em branco caso queira um código automático! Lembre-se que este código não poderá ser alterado e nem usado por outro produto!')");
		document.getElementById("txCod").setAttribute("onblur","UnTip()");
		document.getElementById("txCodNew").value = "";
		document.getElementById("botaoSalva").innerHTML = "<u>S</u>alvar";
		document.getElementById("botaoClone").setAttribute("disabled","");
		// document.getElementById("linhaLink").setAttribute("onclick","alert('Salve o item antes de editar!')");
		var linhas = document.getElementById("linhasComp").value;
		for (var i=1;i<=linhas;i++) {
			document.getElementById("linhaLink"+i).setAttribute("onclick","alert('Salve o item antes de editar!')");
		}
		// alert(document.getElementById("txCodCopy").value);
		return false;
	}
</script>
</head>
<?
//validação página
autorizaPagina(2);
teclaEsc();

/*if (isset($_COOKIE['atualizacadsp'.$_GET['cod_sp']])) {
	echo "<script>atualizaPagMae();</script>";
	setcookie ('atualizacadsp'.$_GET['cod_sp']);
	//echo "cookie deletado";
} */
if (isset($_COOKIE['atualizacadsp'.$_GET['cod_sp']])) echo "<script>opener.location.reload();</script>";
$nomecookie = "atualizacadsp".$_GET['cod_sp'];
echo "<script>setInterval('recarrega(\"$nomecookie\")', 1000);</script>";

$cod_sp = "";
$cod_spNew = $cod_sp;
$descricao = "";
$unidade = "";
$largura = "";
$moeda = "";
$observacoes = "";
$grupo = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	if (empty($_POST['cod_spNew'])) {
		if (empty($_POST['cod_spCopy'])) {
			//variaveis novas recebem o que foi postado no formulário
			if (empty($_POST['cod_sp'])) {
				$list_cod_sp = $sql->query("select cod_sp from sp_dados where empresa = ".$sql->real_escape_string($_POST['empresa'])." and ativo = 1 order by cod_sp asc");
				if (mysqli_num_rows($list_cod_sp)==0) $cod_sp = 1;
				if (mysqli_num_rows($list_cod_sp)>0) {
					for ($i=1;$i<=mysqli_num_rows($list_cod_sp);$i++) {
						$primeiroCod = mysqli_fetch_array($list_cod_sp);
						if  ($i!=$primeiroCod['cod_sp']) {
							$cod_sp = $i;
							// echo $cod_sp;
							break;
						}
					}
				}
				$cod_spNew = $cod_sp;
			} else {
				$cod_sp = $sql->real_escape_string($_POST['cod_sp']);
				$cod_spNew = $cod_sp;
			}
			if (!empty($_POST['descricao'])) $descricao = $sql->real_escape_string($_POST['descricao']); else {echo "<script>location.href=\"cadsp.php\"</script>"; exit;}
			if (!empty($_POST['unidade'])) $unidade = $sql->real_escape_string($_POST['unidade']); else {echo "<script>location.href=\"cadsp.php\"</script>"; exit;}
			if (!empty($_POST['largura'])) $largura = str_replace(",",".",$sql->real_escape_string($_POST['largura'])); else $largura = "";
			if (!empty($_POST['gramatura'])) $gramatura = str_replace(",",".",$sql->real_escape_string($_POST['gramatura'])); else $gramatura = "";
			if (!empty($_POST['moeda'])) $moeda = $sql->real_escape_string($_POST['moeda']); else {echo "<script>location.href=\"cadsp.php\"</script>"; exit;}
			if (!empty($_POST['observacoes'])) $observacoes = $sql->real_escape_string($_POST['observacoes']); else $observacoes = "";
			$empresa = $sql->real_escape_string($_POST['empresa']);
			if (!empty($_POST['grupo'])) $grupo = $sql->real_escape_string($_POST['grupo']); else {echo "<script>location.href=\"cadsp.php\"</script>"; exit;}
	
			//verifica se descricao ja não existe
			$listDescricao = $sql->query("select descricao from sp_dados where descricao = '".$descricao."' and empresa = ".$empresa) or die (mysqli_error($sql));
			$numCadastros = mysqli_num_rows($listDescricao);
			if ($numCadastros>0) {
				echo "A descrição informada já está cadastrada no sistema.<br>";
				exit;
			}
			//verifica se código ja não existe
			$listDescricao = $sql->query("select cod_sp from sp_dados where cod_sp = '".$cod_sp."' and empresa = ".$empresa) or die (mysqli_error($sql));
			$numCadastros = mysqli_num_rows($listDescricao);
			if ($numCadastros>0) {
				echo "O código informado já está cadastrado no sistema.<br>";
				exit;
			}
	
			// Arquiva os dados no banco de dados
			$sqll = "INSERT INTO sp_dados (cod_sp, descricao, moeda, unidade, largura, gramatura, observacoes, empresa, data, ativo, usuario, grupo)
					VALUES ('$cod_sp','$descricao','$moeda','$unidade','$largura','$gramatura','$observacoes','$empresa',now(),'1','".$_SESSION['UsuarioID']."','$grupo')";
			$sql->query($sqll) or die (mysqli_error($sql));
				//echo "Cadastro efetuado!<br>";
				echo "<script>if (window.opener.location.pathname.indexOf(\"add_rent\")>-1) {window.opener.recarrega();} else {atualizaPagMae();}</script>";
				echo "<script>atualizar2(\"cadsp.php?cod_sp=$cod_sp\");</script>";
				//header("Location: cadsp.php?cod_sp=".$cod_sp);
				exit;
		} else if (!empty($_POST['cod_spCopy'])) {
			//variaveis novas recebem o que foi postado no formulário
			if (empty($_POST['cod_sp'])) {
				$list_cod_sp = $sql->query("select cod_sp from sp_dados where empresa = ".$sql->real_escape_string($_POST['empresa'])." and ativo = 1 order by cod_sp asc");
				if (mysqli_num_rows($list_cod_sp)==0) $cod_sp = 1;
				if (mysqli_num_rows($list_cod_sp)>0) {
					for ($i=1;$i<=mysqli_num_rows($list_cod_sp);$i++) {
						$primeiroCod = mysqli_fetch_array($list_cod_sp);
						if  ($i!=$primeiroCod['cod_sp']) {
							$cod_sp = $i;
							// echo $cod_sp;
							break;
						}
					}
				}
				$cod_spNew = $cod_sp;
			} else {
				$cod_sp = $sql->real_escape_string($_POST['cod_sp']);
				$cod_spNew = $cod_sp;
			}
			$cod_spCopy = $_POST['cod_spCopy'];
			if (!empty($_POST['descricao'])) $descricao = $sql->real_escape_string($_POST['descricao']); else {echo "<script>location.href=\"cadsp.php\"</script>"; exit;}
			if (!empty($_POST['unidade'])) $unidade = $sql->real_escape_string($_POST['unidade']); else {echo "<script>location.href=\"cadsp.php\"</script>"; exit;}
			if (!empty($_POST['largura'])) $largura = str_replace(",",".",$sql->real_escape_string($_POST['largura'])); else $largura = "";
			if (!empty($_POST['gramatura'])) $gramatura = str_replace(",",".",$sql->real_escape_string($_POST['gramatura'])); else $gramatura = "";
			if (!empty($_POST['moeda'])) $moeda = $sql->real_escape_string($_POST['moeda']); else {echo "<script>location.href=\"cadsp.php\"</script>"; exit;}
			if (!empty($_POST['observacoes'])) $observacoes = $sql->real_escape_string($_POST['observacoes']); else $observacoes = "";
			$empresa = $sql->real_escape_string($_POST['empresa']);
			if (!empty($_POST['grupo'])) $grupo = $sql->real_escape_string($_POST['grupo']); else {echo "<script>location.href=\"cadsp.php\"</script>"; exit;}
	
			//verifica se descricao ja não existe
			$listDescricao = $sql->query("select descricao from sp_dados where descricao = '".$descricao."' and empresa = ".$empresa) or die (mysqli_error($sql));
			$numCadastros = mysqli_num_rows($listDescricao);
			if ($numCadastros>0) {
				echo "A descrição informada já está cadastrada no sistema.<br>";
				exit;
			}
			//verifica se código ja não existe
			$listDescricao = $sql->query("select cod_sp from sp_dados where cod_sp = '".$cod_sp."' and empresa = ".$empresa) or die (mysqli_error($sql));
			$numCadastros = mysqli_num_rows($listDescricao);
			if ($numCadastros>0) {
				echo "O código informado já está cadastrado no sistema.<br>";
				exit;
			}
	
			// Arquiva os dados no banco de dados
			$sqll = "INSERT INTO sp_dados (cod_sp, descricao, moeda, unidade, largura, gramatura, observacoes, empresa, data, ativo, usuario, grupo)
					VALUES ('$cod_sp','$descricao','$moeda','$unidade','$largura','$gramatura','$observacoes','$empresa',now(),'1','".$_SESSION['UsuarioID']."','$grupo')";
			$sql->query($sqll) or die (mysqli_error($sql));
			//duplica valores composição
			$composicaoSql = $sql->query("select * from sp_composicao where ativo = 1 and empresa = $empresa and cod_sp = $cod_spCopy");
			$composicaoLinhas = mysqli_num_rows($composicaoSql);
			for ($c=1;$c<=$composicaoLinhas;$c++) {
				$coluna = mysqli_fetch_array($composicaoSql);
				$sql->query("insert into sp_composicao (mps,cod,cod_sp,quantidade,empresa,data,usuario,ativo) values ('".$coluna['mps']."','".$coluna['cod']."','$cod_sp','".$coluna['quantidade']."','".$coluna['empresa']."',now(),'".$_SESSION['UsuarioID']."',1)") or die (mysqli_error($sql));
			}
				//echo "Cadastro efetuado!<br>";
				echo "<script>if (window.opener.location.pathname.indexOf(\"add_rent\")>-1) {window.opener.recarrega();} else {atualizaPagMae();}</script>";
				echo "<script>atualizar2(\"cadsp.php?cod_sp=$cod_sp\");</script>";
				//header("Location: cadsp.php?cod_sp=".$cod_sp);
				exit;
		}
	}
	if (!empty($_POST['cod_spNew'])) {
		$cod_sp = $_POST['cod_sp'];
		$antigas = mysqli_fetch_array($sql->query("select descricao, unidade, largura, gramatura, moeda, observacoes, grupo from sp_dados where ativo = 1 and cod_sp = $cod_sp  and empresa = ".$_SESSION['UsuarioEmpresa'])) or die (mysqli_error($sql));
		
		$descricaoAnt = $antigas['descricao'];
		$unidadeAnt = $antigas['unidade'];
		if ($antigas['largura']!=0) $larguraAnt = $antigas['largura']; else $larguraAnt = "";
		if ($antigas['gramatura']!=0) $gramaturaAnt = $antigas['gramatura']; else $gramaturaAnt = "";
		$moedaAnt = $antigas['moeda'];
		$observacoesAnt = $antigas['observacoes'];
		$grupoAnt = $antigas['grupo'];
		
		//variaveis novas recebem o que foi postado no formulário
		if (!empty($_POST['descricao'])) $descricao = $sql->real_escape_string($_POST['descricao']); else {header("Location: cadsp.php"); exit;}
		if (!empty($_POST['unidade'])) $unidade = $sql->real_escape_string($_POST['unidade']); else {header("Location: cadsp.php"); exit;}
		if (!empty($_POST['largura'])) $largura = str_replace(",",".",$sql->real_escape_string($_POST['largura'])); else $largura = "";
		if (!empty($_POST['gramatura'])) $gramatura = str_replace(",",".",$sql->real_escape_string($_POST['gramatura'])); else $gramatura = "";
		if (!empty($_POST['moeda'])) $moeda = $sql->real_escape_string($_POST['moeda']); else {header("Location: cadsp.php"); exit;}
		if (!empty($_POST['observacoes'])) $observacoes = $sql->real_escape_string($_POST['observacoes']); else $observacoes = "";
		$empresa = $sql->real_escape_string($_POST['empresa']);
		if (!empty($_POST['grupo'])) $grupo = $sql->real_escape_string($_POST['grupo']); else {header("Location: cadsp.php"); exit;}
		
		if (($descricaoAnt!=$descricao) or ($unidadeAnt!=$unidade) or ($larguraAnt!=$largura) or ($moedaAnt!=$moeda) or ($gramaturaAnt!=$gramatura) or ($grupoAnt!=$grupo)) {
		
		// desativa anterior
		$sql->query("update sp_dados set ativo = 0 where cod_sp = $cod_sp and empresa = $empresa") or die (mysqli_error($sql));

		// Arquiva os dados no banco de dados
		$sqll = "INSERT INTO sp_dados (cod_sp, descricao, moeda, unidade, largura, gramatura, observacoes, empresa, data, ativo, usuario, grupo)
				VALUES ('$cod_sp','$descricao','$moeda','$unidade','$largura','$gramatura','$observacoes','$empresa',now(),'1','".$_SESSION['UsuarioID']."','$grupo')";
		$sql->query($sqll) or die (mysqli_error($sql));
			//echo "Cadastro efetuado!<br>";
			echo "<script>if (window.opener.location.pathname.indexOf(\"add_rent\")>-1) {window.opener.recarrega();} else {atualizaPagMae();}</script>";
			echo "<script>atualizar2(\"cadsp.php?cod_sp=$cod_sp\");</script>";
			//header("Location: cadsp.php?cod_sp=".$cod_sp);
			echo "<script>if (window.opener.location.pathname.indexOf(\"add_rent\")>-1) {window.opener.recarrega();} else {atualizaPagMae();}</script>";
			exit;
		} else if ($observacoesAnt!=$observacoes) {
			$sql->query("update sp_dados set observacoes = '$observacoes' where cod_sp = $cod_sp and empresa = $empresa and ativo = 1") or die (mysqli_error($sql));
			//echo "Cadastro efetuado!<br>";
			echo "<script>if (window.opener.location.pathname.indexOf(\"add_rent\")>-1) {window.opener.recarrega();} else {atualizaPagMae();}</script>";
			echo "<script>atualizar2(\"cadsp.php?cod_sp=$cod_sp\");</script>";
			//header("Location: cadsp.php?cod_sp=".$cod_sp);
			exit;
		} else {/*header("Location: cadsp.php?cod_sp=".$cod_sp);*/echo "<script>atualizar2(\"cadsp.php?cod_sp=$cod_sp\");</script>"; exit;}
	}
}
	if (isset($_GET['cod_sp'])) {
		$listSp = mysqli_fetch_array($sql->query("select cod_sp, descricao, moeda, unidade, largura, gramatura, observacoes, grupo from sp_dados where ativo = 1 and empresa = ".$_SESSION['UsuarioEmpresa']." and cod_sp = ".$_GET['cod_sp']));
		
		$cod_sp = $listSp['cod_sp'];
		$cod_spNew = $cod_sp;
		$descricao = $listSp['descricao'];
		$unidade = $listSp['unidade'];
		$largura = $listSp['largura'];
		$gramatura = $listSp['gramatura'];
		$moeda = $listSp['moeda'];
		$observacoes = $listSp['observacoes'];
		$grupo = $listSp['grupo'];
	}
?>
<body <? if ($cod_sp=="") echo "onLoad=\"document.getElementById('txCod').focus(); pegaSitInicial();\""; else echo "onLoad=\"document.getElementById('txDescricao').focus(); pegaSitInicial();\"";?> bgcolor="#d8d8d8">
<script type="text/javascript" src="wz_tooltip.js"></script>
<form action="cadsp.php<?/*echo "?cod_sp=".$cod_sp;*/?>" method="post" id="formcadsp">
	<fieldset style="width: 980px; background: #ECF6CE;"><legend>Cadastro de Sub-Produto</legend>
		<table width="100%">
			<tr><td>
				<label for="txCod">CÓD.:</label>
				<?
				if ($cod_sp=="") echo "<input type=\"text\" name=\"cod_sp\" id=\"txCod\" size=\"5\" value=\"$cod_sp\" onfocus=\"Tip('Deixe o COD em branco caso queira um código automático! Lembre-se que este código não poderá ser alterado e nem usado por outro produto!')\" onblur=\"UnTip()\"/>";
				else echo "<input type=\"text\" name=\"cod_sp\" id=\"txCod\" size=\"5\" readonly style=\"background: #d8d8d8; cursor: not-allowed; color: #777\" value=\"$cod_sp\"/>";
				?>
				<input type="hidden" name="cod_spNew" id="txCodNew" value="<?echo $cod_spNew;?>">
				<input type="hidden" name="cod_spCopy" id="txCodCopy" value="">
				<label for="txDescricao">Descrição:</label>
				<input type="text" name="descricao" id="txDescricao" maxlength="125" size="125" style="width: 770px;" value="<?echo $descricao;?>"/>
			</td></tr><tr><td>
				<label for="txUnidade">Unidade:</label>
				<select id="txUnidade" name="unidade" <?if ($unidade!="") echo "onfocus=\"this.initialSelect = this.selectedIndex;\" onchange=\"this.selectedIndex = this.initialSelect;\" style=\"background: #d8d8d8; cursor: not-allowed; color: #777\""; else echo "onfocus=\"Tip('Você não poderá alterar esta unidade de medida depois (por enquanto)!')\" onblur=\"UnTip()\"";?>>
					<?
					$uniPesq = $sql->query("select unidade from unidades where empresa = ".$_SESSION['UsuarioEmpresa']) or die(mysqli_error($sql));
					$uniLinhas = mysqli_num_rows($uniPesq);
					for ($i=0;$i<$uniLinhas;$i++) {
						$uni = mysqli_fetch_array($uniPesq);
						if ($unidade==$uni['unidade']) echo "<option value=\"".$uni['unidade']."\" style=\"width: 20px;\" selected=\"selected\">".$uni['unidade']."</option>\n";
						else echo "<option value=\"".$uni['unidade']."\" style=\"width: 20px;\">".$uni['unidade']."</option>\n";
					}
					?>
				</select>
				<label for="txLargura">Largura:<label>
				<input type="text" name="largura" id="txLargura" size="5" value="<?if ($largura!=0) echo number_format($largura,2,",","");?>" onfocus="Tip('Deixe a Largura em branco caso queira uma largura automática!')" onblur="UnTip()"/>
				<label for="txGramatura">Gramatura:<label>
				<input type="text" name="gramatura" id="txGramatura" size="5" value="<?if ($gramatura!=0) echo number_format($gramatura,0,"",".");?>" onfocus="Tip('Deixe a Gramatura em branco caso queira uma gramatura automática!')" onblur="UnTip()"/>
				<label for="txMoeda">Moeda:</label>
				<select id="txMoeda" name="moeda">
					<?
					$moedaPesq = $sql->query("select iso, moeda from moedas where ativo = 1") or die(mysqli_error($sql));
					$moedaLinhas = mysqli_num_rows($moedaPesq);
					for ($i=0;$i<$moedaLinhas;$i++) {
						$moed = mysqli_fetch_array($moedaPesq);
						if ($moeda==$moed['iso']) echo "<option value=\"".$moed['iso']."\" style=\"width: 70px;\" selected=\"selected\">(".$moed['iso'].") ".$moed['moeda']."</option>\n";
						else echo "<option value=\"".$moed['iso']."\" style=\"width: 70px;\">(".$moed['iso'].") ".$moed['moeda']."</option>\n";
					}
					?>
				</select>
				<label for="txGrupo">Grupo:</label>
				<select id="txGrupo" name="grupo">
					<?
					$grupoPesq = $sql->query("select cod_grupo_prod, grupo from grupos_de_produto where ativo = 1 and empresa = ".$_SESSION['UsuarioEmpresa']) or die(mysqli_error($sql));
					$grupoLinhas = mysqli_num_rows($grupoPesq);
					for ($i=0;$i<$grupoLinhas;$i++) {
						$grup = mysqli_fetch_array($grupoPesq);
						if ($grupo==$grup['cod_grupo_prod']) echo "<option value=\"".$grup['cod_grupo_prod']."\" style=\"width: 100px;\" selected=\"selected\">".$grup['grupo']."</option>\n";
						else echo "<option value=\"".$grup['cod_grupo_prod']."\" style=\"width: 100px;\">".$grup['grupo']."</option>\n";
					}
					?>
				</select>
				<?
				if ($cod_spNew!="") {
				echo "<button id=\"botaoClone\" onclick=\"return clonar()\" style=\"padding: 0px; height: 23px; top: 1px; position: relative;\"><img src=\"images/copy.png\" height=\"14px\" style=\"margin-bottom: -1px;\"><span style=\"position: relative; top: -1px;\"> <u>C</u>lonar Item</span></button>";
				} else {
					echo "<button id=\"botaoClone\" disabled onclick=\"return clonar()\" style=\"padding: 0px; height: 23px; top: 1px; position: relative;\"><img src=\"images/copy.png\" height=\"14px\" style=\"margin-bottom: -1px;\"><span style=\"position: relative; top: -1px;\"> <u>C</u>lonar Item</span></button>";
				}
				?>
			</td></tr><tr><td>
				<label for="txObservacoes">Observações:<label>
			</td></tr><tr><td>
				<textarea name="observacoes" id="txObservacoes" cols="118" rows="2"><?echo $observacoes;?></textarea>
			</td></tr><tr><td>
				<fieldset style="width: 940px;"><legend>Composição</legend>
					<table width="100%">
					<tr><td>
					<?
						if ($cod_sp=="")
							echo "<a class=\"menuaddsp\" href=\"#\" onclick=\"confirmaAntes();\"><u>I</u>mportar SP</a>";
						else echo "<a class=\"menuaddsp\" href=\"#\" onclick=\"salvaEabre('tosp.php?cod_sp=$cod_sp&add=SP')\"><u>I</u>mportar SP</a>";
						if ($cod_sp=="")
							echo "<a class=\"menuaddsp\" href=\"\" onclick=\"confirmaAntes();\">Adicionar <u>P</u>R</a>";
						else echo "<a class=\"menuaddsp\" href=\"#\" onclick=\"salvaEabre('tosp.php?cod_sp=$cod_sp&add=PR')\">Adicionar <u>P</u>R</a>";
						if ($cod_sp=="")
							echo "<a class=\"menuaddsp\" href=\"#\" onclick=\"confirmaAntes();\">Adicionar <u>M</u>P</a>";
						else echo "<a class=\"menuaddsp\" href=\"#\" onclick=\"salvaEabre('tosp.php?cod_sp=$cod_sp&add=MP')\">Adicionar <u>M</u>P</a>";
					?>
					</td></tr>
					<tr><td>
					<div class="divcomposicao" id="divcomposicao">
						<table border="0px" cellpadding="1px" cellspacing="0px" bgcolor="#fffff" class="tablecomposicao">
							<tr bgcolor="#d8d8d8">
								<td><center><b>M/P/S</td>
								<td width="70px"><center><b>COD</td>
								<td width="410px"><center><b>DESCRIÇÃO</td>
								<td width="120px"><center><b>CUSTO</td>
								<td><center><b>UNI</td>
								<td width="60px"><center><b>LARG.</td>
								<td><center><b>QUANT.</td>
								<td width="120px" class="tdsubtotal"><center><b>LIQUIDO</td>
							</tr>
							<? if ($cod_sp!="") {
							$pesquisaComp = $sql->query("select mps, cod, quantidade from sp_composicao where cod_sp = $cod_sp and ativo = 1 and empresa = ".$_SESSION['UsuarioEmpresa']);
							$linhas = mysqli_num_rows($pesquisaComp);
							echo "<input type=\"hidden\" name=\"linhasComp\" id=\"linhasComp\" value=\"$linhas\">";
							for ($i=1;$i<=$linhas;$i++) {
								$listComp = mysqli_fetch_array($pesquisaComp);
								$listCod = $listComp['cod'];
								$listQuant = $listComp['quantidade'];
								if ($listComp['mps']=="MP") {
									$atributos = mysqli_fetch_array($sql->query("select icms, piscofins, frete, quebra from mp where ativo = 1 and cod_mp = $listCod and empresa = ".$_SESSION['UsuarioEmpresa']));
									echo "<tr id=\"linhaLink$i\" onclick=\"quantidade('quantidade.php?cod_sp=".$_GET['cod_sp']."&cod_mp=$listCod&quant=$listQuant','naofechar')\" class=\"linhaLink\" onmouseover=\"Tip('Icms: ".number_format($atributos['icms'],"2",",",".")."% | Pis/Cofins: ".number_format($atributos['piscofins'],"2",",",".")."% | Frete: ".number_format($atributos['frete'],"2",",",".")."% | Quebra: ".number_format($atributos['quebra'],"2",",",".")."%')\" onmouseout=\"UnTip()\">\n";
								}
								if ($listComp['mps']=="PR") {
									$atributos = mysqli_fetch_array($sql->query("select icms, piscofins, frete, quebra from processos where ativo = 1 and cod_pr = $listCod and empresa = ".$_SESSION['UsuarioEmpresa']));
									echo "<tr id=\"linhaLink$i\" onclick=\"quantidade('quantidade.php?cod_sp=".$_GET['cod_sp']."&cod_pr=$listCod&quant=$listQuant','naofechar')\" class=\"linhaLink\" onmouseover=\"Tip('Icms: ".number_format($atributos['icms'],"2",",",".")."% | Pis/Cofins: ".number_format($atributos['piscofins'],"2",",",".")."% | Frete: ".number_format($atributos['frete'],"2",",",".")."% | Quebra: ".number_format($atributos['quebra'],"2",",",".")."%')\" onmouseout=\"UnTip()\">\n";
								}
								if ($listComp['mps']=="SP") echo "<tr id=\"linhaLink$i\" onclick=\"quantidade('quantidade.php?cod_sp=".$_GET['cod_sp']."&sp=$listCod&quant=$listQuant','naofechar')\" class=\"linhaLink\">\n";
								echo "<td><center>".$listComp['mps']."</td>\n";
								echo "<td><center>".$listComp['cod']."</td>\n";
								if ($listComp['mps']=="MP") {
									$listMp = mysqli_fetch_array($sql->query("select descricao, moeda, custo, unidade, largura from mp where cod_mp = ".$listComp['cod']." and ativo = 1 and empresa = ".$_SESSION['UsuarioEmpresa'])) or die (mysqli_error($sql));
									echo "<td>".$listMp['descricao']."</td>\n";
									$simb = mysqli_fetch_array($sql->query("select moeda from moedas where iso = '".$listMp['moeda']."' and ativo = 1")) or die(mysqli_error($sql));
									echo "<td>(".$listMp['moeda'].") ".$simb['moeda']." ".number_format($listMp['custo'],4,",","")."</td>\n";
									$unimoeda = $listMp['moeda'];
									$custo = str_replace(",",".",vlrLiquido($listComp['cod'],$listComp['mps']));
									echo "<td><center>".$listMp['unidade']."</td>\n";
									echo "<td><center>".number_format($listMp['largura'],2,",","")."</td>\n";
								}
								else if ($listComp['mps']=="PR") {
									$listMp = mysqli_fetch_array($sql->query("select descricao, moeda, custo, unidade, largura from processos where cod_pr = ".$listComp['cod']." and ativo = 1 and empresa = ".$_SESSION['UsuarioEmpresa'])) or die (mysqli_error($sql));
									echo "<td>".$listMp['descricao']."</td>\n";
									$simb = mysqli_fetch_array($sql->query("select moeda from moedas where iso = '".$listMp['moeda']."' and ativo = 1")) or die(mysqli_error($sql));
									echo "<td>(".$listMp['moeda'].") ".$simb['moeda']." ".number_format($listMp['custo'],4,",","")."</td>\n";
									$unimoeda = $listMp['moeda'];
									$custo = str_replace(",",".",vlrLiquido($listComp['cod'],$listComp['mps']));
									echo "<td><center>".$listMp['unidade']."</td>\n";
									echo "<td><center>".number_format($listMp['largura'],2,",","")."</td>\n";
								}
								else if ($listComp['mps']=="SP") {
									$listMp = mysqli_fetch_array($sql->query("select cod_sp, descricao, moeda, unidade, largura from sp_dados where cod_sp = ".$listComp['cod']." and ativo = 1 and empresa = ".$_SESSION['UsuarioEmpresa'])) or die (mysqli_error($sql));
									echo "<td>".$listMp['descricao']."</td>\n";
									$simb = mysqli_fetch_array($sql->query("select moeda from moedas where iso = '".$listMp['moeda']."' and ativo = 1")) or die(mysqli_error($sql));
									echo "<td>(".$listMp['moeda'].") ".$simb['moeda']." ".number_format(somaSP($listMp['cod_sp']),4,",","")."</td>\n";
									$unimoeda = $listMp['moeda'];
									$custo = somaSP($listMp['cod_sp']);
									echo "<td><center>".$listMp['unidade']."</td>\n";
									echo "<td><center>".number_format(somaLarg($listMp['cod_sp']),2,",","")."</td>\n";
								}
								echo "<td><center>".number_format($listComp['quantidade'],4,",","")."</td>\n";
								$quantidade = $listComp['quantidade'];
								$subtotal = $quantidade*$custo;
								$simbSP = mysqli_fetch_array($sql->query("select moeda from moedas where iso = '$moeda' and ativo = 1")) or die(mysqli_error($sql));
								echo "<td class=\"tdsubtotal\">($moeda) ".$simbSP['moeda']." ".number_format(cotaToIsoSession($unimoeda,"compra",$subtotal,$moeda),4,",","")."</td>\n";
								// echo "<td class=\"tdsubtotal\">".$unimoeda." ".number_format($subtotal,4,",","")."</td>\n";
								echo "</tr>\n";
							}
							}?>
						</table>
					</div>
					</td></tr>
					<tr><td>
						<p align="right">
							<b> Gramatura: <?echo number_format(somaGR($cod_sp),0,"",".")." g/m²";?>
							<b> | Largura: <?echo number_format(somaLarg($cod_sp),2,",","")." m";?>
							<b> | Custo Total: <?echo "(".$moeda.") ".$simbSP['moeda']." ".number_format(somaSP($cod_sp),4,",","");?>
						</p>
					</td></tr>
					</table>
				</fieldset>
			</td></tr>
			<input type="hidden" name="empresa" value="<?echo $_SESSION["UsuarioEmpresa"];?>">
			<tr><td><center>
				<button onclick="confirma()"><span id="botaoSalva"><?if (isset($_GET['cod_sp'])) echo "<u>S</u>alvar Alterações"; else echo "<u>S</u>alvar";?></span></button>
				<button onclick="atualizar()"><?if (isset($_GET['cod_sp'])) echo "<u>N</u>ovo"; else echo "Limpar";?></button>
				<button onclick="javascript:window.close()"><?echo "<u>D</u>escartar";?></button>
			</center></td></tr>
		</table>
	</fieldset>
</form>

</body>
</html>