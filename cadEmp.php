<?
$popup = 1;
require_once("session.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	if ($_POST['nome']=="Todas") {
		echo "<script>alert('Parâmetro reservado não permitido! Por questões de segurança, a operação será cancelada.'); window.close();</script>";
	} else {
	//variaveis novas recebem o que foi postado no formulário
	$nome = $sql->real_escape_string($_POST['nome']);
	$sinais = array(".", ",", "/", "-", " ");
	$cnpj = str_replace($sinais,"",$sql->real_escape_string($_POST['cnpj']));
	$moeda = $_POST['moeda'];
	
	if(is_numeric($cnpj)) {
		if ((strlen($cnpj)==11) or (strlen($cnpj)==14)) {
			if (!empty($_POST) AND (empty($_POST['nome']) OR empty($_POST['cnpj']))) {
				echo "<script>alert('Preencha todos os campos!')</script>";
			}
			else {
				if (!isset($_GET['emp'])) {
					//verifica se cnpj já foi cadastrado
					$pesquisa = "select cnpj from empresas where cnpj = '".$cnpj."'";
					$sqll = $sql->query($pesquisa);
					$numCadastros = mysqli_num_rows($sqll);
					if ($numCadastros>0) {
						echo "<script>alert('O cnpj informado já está cadastrado no sistema.')</script>";
					}
					else {
						// Arquiva os dados no banco de dados
						$sqll = "INSERT INTO empresas (nome, cnpj, moeda) VALUES ('$nome','$cnpj','$moeda')";
						$sql->query($sqll) or die (mysqli_error($sql));
						$cod = mysqli_fetch_array($sql->query("select id_empresa from empresas where cnpj = '$cnpj'")) or die (mysqli_error($sql));
							echo "<script>opener.location.href=\"parametros.php?emp=".$cod['id_empresa']."\"; alert('Cadastro efetuado!'); window.close();</script>";
					}
				}
				if (isset($_GET['emp'])) {
					$antigo = mysqli_fetch_array($sql->query("select nome, cnpj, moeda from empresas where cnpj = ".$_GET['emp']));
					$nomeAnt = $antigo['nome'];
					$cnpjAnt = $antigo['cnpj'];
					$moedaAnt = $antigo['moeda'];
					if (($nomeAnt!=$nome) or ($cnpjAnt!=$cnpj) or ($moedaAnt!=$moeda)) {
						$sql->query("UPDATE empresas SET cnpj='".$cnpj."',nome='".$nome."',moeda='".$moeda."' WHERE cnpj = ".$_GET['emp']);
						if (!isset($_SESSION)) session_start();
						$moedaUser = mysqli_fetch_array($sql->query("select moeda from empresas where id_empresa = '".$_SESSION['UsuarioEmpresa']."'")) or die(mysqli_error($sql));
  						$_SESSION['MoedaIso'] = $moedaUser['moeda'];
  						$sinal = mysqli_fetch_array($sql->query("select moeda from moedas where iso = '".$moedaUser['moeda']."' and ativo = 1")) or die(mysqli_error($sql));
  						$_SESSION['MoedaSinal'] = $sinal['moeda'];
  						atualizaCookieValor();
						echo "<script>atualizaPagMae(); alert('Alteração efetuada!'); window.close();</script>";
					} else echo "<script>alert('Nenhuma alteração foi detectada!')</script>";
				}
			}
		} else echo "<script>alert('CNPJ ou CPF inválidos!')</script>";
	} else echo "<script>alert('Apenas números!')</script>";
}}
else if (isset($_GET['emp']) and $_SERVER['REQUEST_METHOD'] != "POST") {
	$empresa = mysqli_fetch_array($sql->query("select nome, cnpj, moeda from empresas where cnpj = ".$_GET['emp']));
	$nome = $empresa['nome'];
	$cnpj = $empresa['cnpj'];
	$moeda = $empresa['moeda'];
	if ($nome=="Todas") {
		echo "<script>alert('Parâmetro reservado não permitido! Por questões de segurança, a operação será cancelada.'); window.close();</script>";
	}
}
else {
	$nome = "";
	$cnpj = "somente números";
	$onfocus = " onfocus=\"this.value='';\"";
	$moeda = "";
}
?>
<html>
<head>
<title><?echo $title." - Cadastro de Empresas";?></title>
</head>
<body>
<?
//validação página
autorizaPagina(2);
teclaEsc();
?>

	<?if (!isset($_GET['emp'])) echo "<form action=\"cadEmp.php\" method=\"post\">";
	  if (isset($_GET['emp'])) echo "<form action=\"cadEmp.php?emp=".$_GET['emp']."\" method=\"post\">";?>
	<fieldset>
		<?if (!isset($_GET['emp'])) echo "<legend>Cadastro de Empresas</legend>";
		  if (isset($_GET['emp'])) echo "<legend>Editar Empresa</legend>";?>
		<table>
			<tr><td><label for="txNome">Nome</label></td>
				<td><input type="text" name="nome" id="txNome" maxlength="25" size="40" value="<?echo $nome;?>"/></td></tr>
				
			<tr><td><label for="txCnpj">CNPJ/CPF</label></td>
				<td><input type="text" name="cnpj" id="txCnpj" maxlength="40" size="40" value="<?echo $cnpj;?>"<?if (isset($onfocus)) echo $onfocus;?>/></td></tr>
			
			<tr><td><label for="txMoeda">Moeda Padrão</label></td>
				<td><select name="moeda" id="txMoeda">
					<?
					$isosQuery = $sql->query("select iso from moedas where ativo = 1 order by iso");
					for ($i=1;mysqli_num_rows($isosQuery)>=$i;$i++) {
						$iso = mysqli_fetch_array($isosQuery);
						$valor = $iso['iso'];
						if ($valor==$moeda) echo "<option value=\"$valor\" selected=\"selected\">$valor</option>";
						else echo "<option value=\"$valor\">$valor</option>";
					}
					?>
				</select></td></tr>
				
			<tr><td></td><td><center><input type="submit" value="<?if (isset($_GET['emp'])) echo "Salvar Atualização"; else echo "Salvar";?>"/></center></td>
		</table>
	</fieldset>
</form>

</body>
</html>