<?$popup = 1;
require_once("session.php");?>
<html>
<head>
<title><?echo $title." - Cadastro de Matéria Prima";?></title>
<script type="text/javascript">
	function confirma() {
		var ok = confirm("Tem certeza que deseja cadastrar este item?");
		if(ok){
			document.getElementById("formcadmp").submit()
		};
	}
	function atualizaMp() {
		opener.location.reload();
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
	$list_cod_mp = $sql->query("select cod_mp from mp where empresa = ".$sql->real_escape_string($_POST['empresa'])." order by cod_mp desc");
	if (mysqli_num_rows($list_cod_mp)==0) $cod_mp = 1;
	if (mysqli_num_rows($list_cod_mp)>0) {$ultimoCod = mysqli_fetch_row($list_cod_mp); $cod_mp = $ultimoCod[0] + 1;}
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

	//verifica se descricao ja não existe
	$listDescricao = $sql->query("select descricao from mp where descricao = '".$descricao."' and empresa = ".$empresa." and ativo = '1'") or die (mysqli_error($sql));
	$numCadastros = mysqli_num_rows($listDescricao);
	if ($numCadastros>0) {
		echo "A descrição informada já está cadastrada no sistema.<br>";
		exit;
	}

	// Arquiva os dados no banco de dados
	$sqll = "INSERT INTO mp (cod_mp, descricao, moeda, custo, unidade, gramatura, largura, icms, piscofins, frete, quebra, observacoes, estado, empresa, data, ativo, usuario)
			VALUES ('$cod_mp','$descricao','$moeda','$custo','$unidade','$gramatura','$largura','$icms','$piscofins','$frete','$quebra','$observacoes','1','$empresa',now(),'1','".$_SESSION['UsuarioID']."')";
	$sql->query($sqll) or die (mysqli_error($sql));
		echo "Cadastro efetuado!<br>";
		echo "<script>atualizaMp();</script>";
		
	//header("Location: index.php"); exit;
}
?>
<form action="cadmp.php" method="post" id="formcadmp">
	<fieldset>
		<legend>Cadastro de Matéria Prima</legend>
		<table><tr><td><table>
			<tr><td><label for="txDescricao">Descrição</label></td>
				<td><input type="text" name="descricao" id="txDescricao" maxlength="70" size="70" style="width: 450px;"/></td></tr>
				
			<tr><td><label for="txCusto">Custo</label></td>
				<td>
					<select name="moeda" id="txMoeda">
					<?
					$listMoedas = $sql->query("select iso, moeda from moedas where ativo = 1 order by iso") or die (mysqli_error($sql));
					for ($i=0;$i<mysqli_num_rows($listMoedas);$i++) {
						$moeda = mysqli_fetch_row($listMoedas);
						echo "<option value=\"$moeda[0]\">($moeda[0]) $moeda[1]</option>";
					}
					?>
					</select>
					<input type="text" name="custo" id="txCusto" maxlength="15" size="15"/>
					<label for="txUnidade">/</label>
					<select name="unidade" id="txUnidade">
					<?
					if ($_SESSION["UsuarioEmpresa"]==1)
						$listUnidades = $sql->query("select unidade from unidades group by unidade") or die (mysqli_error($sql));
					else
						$listUnidades = $sql->query("select unidade from unidades where empresa = ".$_SESSION["UsuarioEmpresa"]) or die (mysqli_error($sql));
					for ($i=0;$i<mysqli_num_rows($listUnidades);$i++) {
						$unidade = mysqli_fetch_row($listUnidades);
						echo "<option value=\"$unidade[0]\">$unidade[0]</option>";
					}
					?>
					</select>
					<label for="txGramatura">Gramatura:</label>
					<input type="text" name="gramatura" id="txGramatura" maxlength="5" size="5"/>
					<label>g/m²</label>
				</td>
			</tr>
				
			<tr><td><label for="txIcms">% ICMS</label></td>
				<td><input type="text" name="icms" id="txIcms" maxlength="20" size="20"/>
					<label for="txPisCofins">% Pis/Cofins</label>
					<input type="text" name="piscofins" id="txPisCofins" maxlength="20" size="20"/></td></tr>
				
			<tr><td><label for="txFrete">% Frete</label></td>
				<td><input type="text" name="frete" id="txFrete" maxlength="20" size="20"/></td></tr>
				
			<tr><td><label for="txQuebra">% Quebra</label></td>
				<td><input type="text" name="quebra" id="txQuebra" maxlength="20" size="20"/>
				<label for="txLargura">Larg:</label>
				<input type="text" name="largura" id="txLargura" maxlength="18" size="18"/>
				<label>m</label>
				</td></tr>
		</table></td></tr>
		
		<tr><td><label for="txObservacoes">Observações</label></td></tr>
		<tr><td><textarea name="observacoes" id="txObservacoes" cols="63" rows="5"></textarea></td></tr>
			
		<input type="hidden" name="empresa" value="<?echo $_SESSION["UsuarioEmpresa"];?>">
		<tr><td><center><input type="button" value="Salvar" onclick="confirma()"/></center></td></tr>
		
		</table>
	</fieldset>
</form>

</body>
</html>