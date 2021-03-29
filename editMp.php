<?$popup = 1;
require_once("session.php");?>
<html>
<head>
<title><?echo $title." - Edição de Matéria Prima";?></title>
<script type="text/javascript">
	function confirma() {
		var ok = confirm("Tem certeza que deseja alterar este item?");
		if(ok){
			document.getElementById("formeditmp").submit()
		};
	}
	function atualizaMp() {
		opener.location.reload();
	}
	$(document).ready(function(){
		$("#on_off").click(function(){
			$(document.getElementById("bot_loader")).show();
			var img = $("#on_off");
			if (img.attr("estado")==1) {
				onOff(img.attr("prod"));
			} else {
				ativaDesativa(img.attr("prod"));
			}
		});
	});
	function ativaDesativa(codProd) {
		var dados = {
			'funcao':'on_off',
			'prod':codProd,
			'mps':'mp'
		};
		$.ajax({
			type: 'POST',
			url: 'funcoesAjax.php',
			data: dados,
			dataType: "json",
			beforeSend: function(){
				$('#fundo_fumace').show();
			},
			success: function(json){
				if (json.error>0) {
					alert(json.mensagem);
					$('#fundo_fumace').hide();
				} else {
					// if (json.estado=='0') {
					// 	var botao = $(document.getElementById("on_off"));
					// 	botao.attr("src","images/bot_off.png");
					// 	botao.attr("estado","0");
					// 	atualizaMp();
					// } else {
					// 	var botao = $(document.getElementById("on_off"));
					// 	botao.attr("src","images/bot_on.png");
					// 	botao.attr("estado","1");
					// 	atualizaMp();
					// }
					atualizaMp();
					window.location.href = "editMp.php?cod_mp="+codProd;
					$('#fundo_fumace').hide();
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown){
				alert("erro!");
				$('#fundo_fumace').hide();
				// for(i in XMLHttpRequest) {
				// if (i!="channel")
				// document.write(i + ":" + XMLHttpRequest[i] + "<br>");
				// }
			}
		});
	}
	function onOff(codProd) {
		var dados = {
			'funcao':'verificaProdAfect',
			'prod':codProd,
			'mps':'mp'
		};
		$.ajax({
			type: 'POST',
			url: 'funcoesAjax.php',
			data: dados,
			dataType: "json",
			beforeSend: function(){
				$('#fundo_fumace').show();
			},
			success: function(json){
				if (json.error>0) {
					alert(json.mensagem);
					$('#fundo_fumace').hide();
				} else {
					if (json.existe==true) {
						window.location.href = "afectsLotes.php?afetados="+json.itensAfetados+"&prod="+codProd+'&mps=mp';
					} else {
						ativaDesativa(codProd);
					}
					$('#fundo_fumace').hide();
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown){
				alert("erro!");
				$('#fundo_fumace').hide();
				// for(i in XMLHttpRequest) {
				// if (i!="channel")
				// document.write(i + ":" + XMLHttpRequest[i] + "<br>");
				// }
			}
		});
	}
</script>
</head>
<body onLoad="document.getElementById('txDescricao').focus();">
<?
//validação página
autorizaPagina(2);
teclaEsc();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	//variaveis novas recebem o que foi postado no formulário
	//$list_cod_mp = mysqli_fetch_row($sql->query("select cod_mp from mp where indice = ".$_POST['indice']));
	//$cod_mp = $list_cod_mp[0];
	$cod_mp = $_POST['cod_mp'];
	$list_indice = mysqli_fetch_row($sql->query("select indice from mp where cod_mp = $cod_mp and ativo = 1 and empresa = ".$_SESSION['UsuarioEmpresa']));
	if (!empty($_POST['descricao'])) $descricao = $sql->real_escape_string($_POST['descricao']); else {header("Location: cadmp.php"); exit;}
	if (!empty($_POST['moeda'])) $moeda = $sql->real_escape_string($_POST['moeda']); else {header("Location: cadmp.php"); exit;}
	if (!empty($_POST['custo'])) $custo = str_replace(",",".",$sql->real_escape_string($_POST['custo'])); else $custo = 0;
	if (!empty($_POST['unidade'])) $unidade = $sql->real_escape_string($_POST['unidade']); else {header("Location: cadmp.php"); exit;}
	if (!empty($_POST['gramatura'])) $gramatura = str_replace(",",".",$sql->real_escape_string($_POST['gramatura'])); else $gramatura = 0;
	if (!empty($_POST['largura'])) $largura = str_replace(",",".",$sql->real_escape_string($_POST['largura'])); else {header("Location: cadmp.php"); exit;}
	if (!empty($_POST['icms'])) $icms = str_replace(",",".",$sql->real_escape_string($_POST['icms'])); else $icms = 0;
	if (!empty($_POST['piscofins'])) $piscofins = str_replace(",",".",$sql->real_escape_string($_POST['piscofins'])); else $piscofins = 0;
	if (!empty($_POST['frete'])) $frete = str_replace(",",".",$sql->real_escape_string($_POST['frete'])); else $frete = 0;
	if (!empty($_POST['quebra'])) $quebra = str_replace(",",".",$sql->real_escape_string($_POST['quebra'])); else $quebra = 0;
	if (!empty($_POST['observacoes'])) $observacoes = $sql->real_escape_string($_POST['observacoes']); else $observacoes = "";
	$empresa = $sql->real_escape_string($_POST['empresa']);
	//$indice = $sql->real_escape_string($_POST['indice']);
	$indice = $sql->real_escape_string($list_indice[0]);
	
	//verifica se teve alteração
	$mpAnt = mysqli_fetch_array($sql->query("select descricao, moeda, custo, unidade, gramatura, largura, icms, piscofins, frete, quebra, observacoes, estado
											from mp where indice = $indice"));
	$descricaoAnt = $mpAnt['descricao'];
	$moedaAnt = $mpAnt['moeda'];
	$custoAnt = $mpAnt['custo'];
	$unidadeAnt = $mpAnt['unidade'];
	$gramaturaAnt = $mpAnt['gramatura'];
	$larguraAnt = $mpAnt['largura'];
	$icmsAnt = $mpAnt['icms'];
	$piscofinsAnt = $mpAnt['piscofins'];
	$freteAnt = $mpAnt['frete'];
	$quebraAnt = $mpAnt['quebra'];
	$observacoesAnt = $mpAnt['observacoes'];
	$estadoAnt = $mpAnt['estado'];
	
	if ($descricaoAnt!=$descricao or $moedaAnt!=$moeda or $custoAnt!=$custo or $unidadeAnt!=$unidade or $gramaturaAnt!=$gramatura or 
		$larguraAnt!=$largura or $icmsAnt!=$icms or $piscofinsAnt!=$piscofins or $freteAnt!=$frete or $quebraAnt!=$quebra) {
		// Arquiva os dados no banco de dados em nova linha menos observacoes
		$sqll = "INSERT INTO mp (cod_mp, descricao, moeda, custo, unidade, gramatura, largura, icms, piscofins, frete, quebra, observacoes, estado, empresa, data, ativo, usuario)
				VALUES ('$cod_mp','$descricao','$moeda','$custo','$unidade','$gramatura','$largura','$icms','$piscofins','$frete','$quebra','$observacoes','$estadoAnt','$empresa',now(),'1','".$_SESSION['UsuarioID']."')";
		$sql->query($sqll) or die (mysqli_error($sql));
		// Atualiza o ultimo registro para desativado
		$sqll = "update mp set ativo='0' where indice = $indice";
		$sql->query($sqll) or die (mysqli_error($sql));
			//echo "Atualização efetuada!<br>";
			//echo "<script>atualizaMp();</script>";
			// header ("Location: ".$_SERVER['PHP_SELF']."?cod_mp=$cod_mp&confirma=ok");
			// echo "<meta HTTP-EQUIV='Refresh' CONTENT='0;URL=".$_SERVER['PHP_SELF']."?cod_mp=$cod_mp&confirma=ok";
			echo "<script>location.href=\"".$_SERVER['PHP_SELF']."?cod_mp=$cod_mp&confirma=ok\"</script>";
			exit;
	} else if ($observacoesAnt!=$observacoes) {
		$sqll = "update mp set observacoes='".$observacoes."' where indice = ".$indice;
		$sql->query($sqll) or die (mysqli_error($sql));
			//echo "Atualização efetuada!<br>";
			//echo "<script>atualizaMp();</script>";
			// header ("Location: ".$_SERVER['PHP_SELF']."?cod_mp=$cod_mp&confirma=ok");
			// echo "<meta HTTP-EQUIV='Refresh' CONTENT='0;URL=".$_SERVER['PHP_SELF']."?cod_mp=$cod_mp&confirma=ok";
			echo "<script>location.href=\"".$_SERVER['PHP_SELF']."?cod_mp=$cod_mp&confirma=ok\"</script>";
			exit;
	} else {echo "Nada foi atualizado!"; exit;}
} else {
if ($_SERVER['REQUEST_METHOD'] == "GET") {
	if (isset($_GET['confirma'])) {if ($_GET['confirma']=="ok") {echo "Atualização efetuada!<br>"; echo "<script>atualizaMp();</script>";}}
	$cod_mp = $_GET['cod_mp'];
	if (isset($_GET['desativar']) and $_GET['desativar']=="sim") {
		echo "<script>onOff($cod_mp);</script>";
		exit;
	}
	
	$linhaMp = mysqli_fetch_array($sql->query("select descricao, moeda, custo, unidade, gramatura, largura, icms, piscofins, frete, quebra, observacoes, empresa, estado from mp where cod_mp = ".$cod_mp." and ativo = 1 and empresa = ".$_SESSION['UsuarioEmpresa']));
	$descricao = $linhaMp['descricao'];
	$moeda = $linhaMp['moeda'];
	$custo = str_replace(".",",",$linhaMp['custo']);
	$unidade = $linhaMp['unidade'];
	$gramatura = str_replace(".",",",$linhaMp['gramatura']);
	$largura = str_replace(".",",",$linhaMp['largura']);
	$icms = str_replace(".",",",$linhaMp['icms']);
	$piscofins = str_replace(".",",",$linhaMp['piscofins']);
	$frete = str_replace(".",",",$linhaMp['frete']);
	$quebra = str_replace(".",",",$linhaMp['quebra']);
	$observacoes = $linhaMp['observacoes'];
	$empresa = $linhaMp['empresa'];
	$estado = $linhaMp['estado'];
}}
?>
<form action="editMp.php" method="post" id="formeditmp">
	<fieldset>
		<legend>Edição de Matéria Prima</legend>
		<table><tr><td><table>
			<tr><td><label for="txDescricao">Descrição</label></td>
				<td><input type="text" name="descricao" id="txDescricao" maxlength="70" size="70" value="<?echo "$descricao";?>" style="width: 380px;"/>
					<?
					if ($estado==1) echo "<img id=\"on_off\" src=\"images/bot_on.png\" style=\"height: 15px; cursor: pointer;\" prod=\"$cod_mp\" point=\"".$_SESSION['UsuarioEmpresa']."\" estado=\"1\">";
					else echo "<img id=\"on_off\" src=\"images/bot_off.png\" style=\"height: 15px; cursor: pointer;\" prod=\"$cod_mp\" point=\"".$_SESSION['UsuarioEmpresa']."\" estado=\"0\">";
					?>
					<img id="bot_loader" src="images/91.png" style="width: 15px; display: none;">
				</td></tr>
				
			<tr><td><label for="txCusto">Custo</label></td>
				<td>
					<select name="moeda" id="txMoeda">
					<?
					$listMoedas = $sql->query("select iso, moeda from moedas where ativo = 1 order by iso") or die (mysqli_error($sql));
					for ($i=0;$i<mysqli_num_rows($listMoedas);$i++) {
						$moedaRow = mysqli_fetch_row($listMoedas);
						if ($moeda==$moedaRow[0]) echo "<option value=\"$moedaRow[0]\" selected=\"selected\">($moedaRow[0]) $moedaRow[1]</option>";
						else echo "<option value=\"$moedaRow[0]\">($moedaRow[0]) $moedaRow[1]</option>";
					}
					?>
					</select>
					<input type="text" name="custo" id="txCusto" maxlength="15" size="15" value="<?echo "$custo";?>"/>
					<label for="txUnidade">/</label>
					<select name="unidade" id="txUnidade">
					<?
					if ($_SESSION["UsuarioEmpresa"]==1)
						$listUnidades = $sql->query("select unidade from unidades group by unidade") or die (mysqli_error($sql));
					else
						$listUnidades = $sql->query("select unidade from unidades where empresa = ".$_SESSION["UsuarioEmpresa"]) or die (mysqli_error($sql));
					for ($i=0;$i<mysqli_num_rows($listUnidades);$i++) {
						$unidadeRow = mysqli_fetch_row($listUnidades);
						if ($unidade==$unidadeRow[0]) echo "<option value=\"$unidadeRow[0]\" selected=\"selected\">$unidadeRow[0]</option>";
						else echo "<option value=\"$unidadeRow[0]\">$unidadeRow[0]</option>";
					}
					?>
					</select>
					<label for="txGramatura">Gramatura:</label>
					<input type="text" name="gramatura" id="txGramatura" maxlength="5" size="5" value="<?echo "$gramatura";?>"/>
					<label>g/m²</label>
				</td>
			</tr>
				
			<tr><td><label for="txIcms">% ICMS</label></td>
				<td><input type="text" name="icms" id="txIcms" maxlength="20" size="20" value="<?echo "$icms";?>"/>
					<label for="txPisCofins">% Pis/Cofins</label>
					<input type="text" name="piscofins" id="txPisCofins" maxlength="20" size="20" value="<?echo "$piscofins";?>"/></td></tr>
				
			<tr><td><label for="txFrete">% Frete</label></td>
				<td><input type="text" name="frete" id="txFrete" maxlength="20" size="20" value="<?echo "$frete";?>"/></td></tr>
				
			<tr><td><label for="txQuebra">% Quebra</label></td>
				<td><input type="text" name="quebra" id="txQuebra" maxlength="20" size="20" value="<?echo "$quebra";?>"/>
				<label for="txLargura">Larg:</label>
				<input type="text" name="largura" id="txLargura" maxlength="18" size="18" value="<?echo "$largura";?>"/>
				<label>m</label>
				</td></tr>
		</table></td></tr>
		
		<tr><td><label for="txObservacoes">Observações</label></td></tr>
		<tr><td><textarea name="observacoes" id="txObservacoes" cols="63" rows="5"><?echo "$observacoes";?></textarea></td></tr>
			
		<input type="hidden" name="empresa" value="<?echo "$empresa";?>">
		<input type="hidden" name="cod_mp" value="<?echo "$cod_mp";?>">
		<tr><td><center><input type="button" value="Salvar" onclick="confirma()"/></center></td></tr>
		
		</table>
	</fieldset>
</form>

</body>
</html>