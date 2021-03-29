<?$popup = 1;
require_once("session.php");?>
<html>
<head>
<title><?echo $title." - Edição de Processos";?></title>
<script type="text/javascript">
	function confirma() {
		var ok = confirm("Tem certeza que deseja alterar este item?");
		if(ok){
			document.getElementById("formeditpr").submit()
		};
	}
</script>
</head>
<body onLoad="document.getElementById('txDescricao').focus();">
<?
//validação página
autorizaPagina(2);
teclaEsc();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$cod_pr = $_POST['cod_pr'];
	$list_indice = mysqli_fetch_row($sql->query("select indice from processos where cod_pr = $cod_pr and ativo = 1 and empresa = ".$_SESSION['UsuarioEmpresa']));
	if (!empty($_POST['descricao'])) $descricao = $sql->real_escape_string($_POST['descricao']); else {header("Location: cadpr.php"); exit;}
	if (!empty($_POST['moeda'])) $moeda = $sql->real_escape_string($_POST['moeda']); else {header("Location: cadpr.php"); exit;}
	if (!empty($_POST['custo'])) $custo = str_replace(",",".",$sql->real_escape_string($_POST['custo'])); else $custo = 0;
	if (!empty($_POST['unidade'])) $unidade = $sql->real_escape_string($_POST['unidade']); else {header("Location: cadpr.php"); exit;}
	if (!empty($_POST['largura'])) $largura = str_replace(",",".",$sql->real_escape_string($_POST['largura'])); else {header("Location: cadpr.php"); exit;}
	if (!empty($_POST['icms'])) $icms = str_replace(",",".",$sql->real_escape_string($_POST['icms'])); else $icms = 0;
	if (!empty($_POST['piscofins'])) $piscofins = str_replace(",",".",$sql->real_escape_string($_POST['piscofins'])); else $piscofins = 0;
	if (!empty($_POST['frete'])) $frete = str_replace(",",".",$sql->real_escape_string($_POST['frete'])); else $frete = 0;
	if (!empty($_POST['quebra'])) $quebra = str_replace(",",".",$sql->real_escape_string($_POST['quebra'])); else $quebra = 0;
	if (!empty($_POST['observacoes'])) $observacoes = $sql->real_escape_string($_POST['observacoes']); else $observacoes = "";
	if (!empty($_POST['setor'])) $setor_fornec = $sql->real_escape_string($_POST['setor']); else {header("Location: cadpr.php"); exit;}
	$empresa = $sql->real_escape_string($_POST['empresa']);
	//$indice = $sql->real_escape_string($_POST['indice']);
	$indice = $sql->real_escape_string($list_indice[0]);
	
	//verifica se teve alteração
	$prAnt = mysqli_fetch_array($sql->query("select descricao, moeda, custo, unidade, setor_fornec, largura, icms, piscofins, frete, quebra, observacoes
											from processos where indice = $indice"));
	$descricaoAnt = $prAnt['descricao'];
	$moedaAnt = $prAnt['moeda'];
	$custoAnt = $prAnt['custo'];
	$unidadeAnt = $prAnt['unidade'];
	$setor_fornecAnt = $prAnt['setor_fornec'];
	$larguraAnt = $prAnt['largura'];
	$icmsAnt = $prAnt['icms'];
	$piscofinsAnt = $prAnt['piscofins'];
	$freteAnt = $prAnt['frete'];
	$quebraAnt = $prAnt['quebra'];
	$observacoesAnt = $prAnt['observacoes'];
	
	if ($descricaoAnt!=$descricao or $moedaAnt!=$moeda or $custoAnt!=$custo or $unidadeAnt!=$unidade or $setor_fornecAnt!=$setor_fornec or 
		$larguraAnt!=$largura or $icmsAnt!=$icms or $piscofinsAnt!=$piscofins or $freteAnt!=$frete or $quebraAnt!=$quebra) {
		// Arquiva os dados no banco de dados em nova linha menos observacoes
		$sqll = "INSERT INTO processos (cod_pr, descricao, moeda, custo, unidade, setor_fornec, largura, icms, piscofins, frete, quebra, observacoes, empresa, data, ativo, usuario)
				VALUES ('$cod_pr','$descricao','$moeda','$custo','$unidade','$setor_fornec','$largura','$icms','$piscofins','$frete','$quebra','$observacoes','$empresa',now(),'1','".$_SESSION['UsuarioID']."')";
		$sql->query($sqll) or die (mysqli_error($sql));
		// Atualiza o ultimo registro para desativado
		$sqll = "update processos set ativo='0' where indice = $indice";
		$sql->query($sqll) or die (mysqli_error($sql));
			//echo "Atualização efetuada!<br>";
			//echo "<script>atualizaMp();</script>";
			// header ("Location: ".$_SERVER['PHP_SELF']."?cod_pr=$cod_pr&confirma=ok");
			echo "<script>location.href=\"".$_SERVER['PHP_SELF']."?cod_pr=$cod_pr&confirma=ok\"</script>";
			exit;
	} else if ($observacoesAnt!=$observacoes) {
		$sqll = "update processos set observacoes='".$observacoes."' where indice = ".$indice;
		$sql->query($sqll) or die (mysqli_error($sql));
			//echo "Atualização efetuada!<br>";
			//echo "<script>atualizaMp();</script>";
			// header ("Location: ".$_SERVER['PHP_SELF']."?cod_pr=$cod_pr&confirma=ok");
			echo "<script>location.href=\"".$_SERVER['PHP_SELF']."?cod_pr=$cod_pr&confirma=ok\"</script>";
			exit;
	} else {echo "Nada foi atualizado!"; exit;}
} else {
if ($_SERVER['REQUEST_METHOD'] == "GET") {
	if (isset($_GET['confirma'])) {if ($_GET['confirma']=="ok") {echo "Atualização efetuada!<br>"; echo "<script>atualizaPagMae();</script>";}}
	$cod_pr = $_GET['cod_pr'];
	
	$linhaPr = mysqli_fetch_array($sql->query("select descricao, moeda, custo, unidade, setor_fornec, largura, icms, piscofins, frete, quebra, observacoes, 
												empresa from processos where cod_pr = ".$cod_pr." and ativo = 1 and empresa = ".$_SESSION['UsuarioEmpresa']));
	$descricao = $linhaPr['descricao'];
	$moeda = $linhaPr['moeda'];
	$custo = str_replace(".",",",$linhaPr['custo']);
	$unidade = $linhaPr['unidade'];
	$setor_fornec = $linhaPr['setor_fornec'];
	$largura = str_replace(".",",",$linhaPr['largura']);
	$icms = str_replace(".",",",$linhaPr['icms']);
	$piscofins = str_replace(".",",",$linhaPr['piscofins']);
	$frete = str_replace(".",",",$linhaPr['frete']);
	$quebra = str_replace(".",",",$linhaPr['quebra']);
	$observacoes = $linhaPr['observacoes'];
	$empresa = $linhaPr['empresa'];
}}
?>
<form action="editPr.php" method="post" id="formeditpr">
	<fieldset>
		<legend>Edição de Processos</legend>
		<table><tr><td><table>
			<tr><td><label for="txDescricao">Descrição</label></td>
				<td><input type="text" name="descricao" id="txDescricao" maxlength="70" size="70" value="<?echo "$descricao";?>"/></td></tr>
				
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
					<input type="text" name="custo" id="txCusto" maxlength="18" size="18" value="<?echo "$custo";?>"/>
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
			<tr><td></td>
			<td><label for="txSetor">Setor / Fornecedor: </label>
				<input type ="radio" name="setor" id="txSetor" value="1" <?if ($setor_fornec==1) echo "checked"?>>Interno</input>
				<input type ="radio" name="setor" id="txSetor" value="2" <?if ($setor_fornec==2) echo "checked"?>>Externo</input>
			</td>
			</tr>
		</table></td></tr>
		
		<tr><td><label for="txObservacoes">Observações</label></td></tr>
		<tr><td><textarea name="observacoes" id="txObservacoes" cols="63" rows="3"><?echo "$observacoes";?></textarea></td></tr>
			
		<input type="hidden" name="empresa" value="<?echo "$empresa";?>">
		<input type="hidden" name="cod_pr" value="<?echo "$cod_pr";?>">
		<tr><td><center><input type="button" value="Salvar" onclick="confirma()"/></center></td></tr>
		
		</table>
	</fieldset>
</form>

</body>
</html>