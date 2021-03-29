<?
$popup = 1;
require_once("session.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	//variaveis novas recebem o que foi postado no formulário
	if (!isset($_GET['cod_reg'])) {
		$pesqCod = $sql->query("select cod_reg from regioes where ativo = 1 and empresa = ".$sql->real_escape_string($_POST['empresa'])." order by cod_reg desc");
		if (mysqli_num_rows($pesqCod)==0) $cod_reg = 1;
		else {
			$ultimo_cod = mysqli_fetch_array($pesqCod);
			$cod_reg = $ultimo_cod['cod_reg'] + 1;
		}
	} else $cod_reg = $_GET['cod_reg'];
	$regiao = $sql->real_escape_string($_POST['regiao']);
	$icms = $sql->real_escape_string(str_replace(",",".",$_POST['icms']));
	if ($icms!=0) {
		if (!is_numeric($icms)) {echo "<script>alert('ICMS não permite símbolos especiais!'); window.close();</script>"; exit;}
	}
	$usuario = $_SESSION['UsuarioID'];
	$empresa = $sql->real_escape_string($_POST['empresa']);
	
			if (!empty($_POST) AND (empty($_POST['regiao']))) {
				echo "<script>alert('Preencha todos os campos!')</script>";
			}
			else {
				if (!isset($_GET['cod_reg'])) {
					//verifica se unidade já foi cadastrada
					// $pesquisa = "select unidade from unidades where empresa = '".$empresa."' and unidade = '".$unidade."'";
					// $sqll = $sql->query($pesquisa);
					// $numCadastros = mysqli_num_rows($sqll);
					// if ($numCadastros>0) {
					// 	echo "<script>alert('A unidade informada já está cadastrada no sistema.')</script>";
					// }
					// else {
					// 	// Arquiva os dados no banco de dados
						$sqll = "INSERT INTO regioes (cod_reg, regiao, icms, data, usuario, empresa, ativo) VALUES ('$cod_reg','$regiao','$icms',now(),'$usuario','$empresa',1)";
						$sql->query($sqll) or die (mysqli_error($sql));
							echo "<script>atualizaPagMae(); alert('Cadastro efetuado!'); window.close();</script>";
					// }
				}
				if (isset($_GET['cod_reg'])) {
					$antigo = mysqli_fetch_array($sql->query("select regiao, icms from regioes where cod_reg = ".$_GET['cod_reg']." and ativo = 1 and empresa = $empresa"));
					$regiaoAnt = $antigo['regiao'];
					$icmsAnt = $antigo['icms'];
					if (($regiaoAnt!=$regiao) or ($icmsAnt!=$icms)) {
						//altera anterior para ativo = 0
						$sql->query("update regioes set ativo = 0 where cod_reg = $cod_reg and empresa = $empresa") or die (mysqli_error($sql));
						$sql->query("INSERT INTO regioes (cod_reg, regiao, icms, data, usuario, empresa, ativo) VALUES ('$cod_reg','$regiao','$icms',now(),'$usuario','$empresa',1)") or die (mysqli_error($sql));
						echo "<script>atualizaPagMae(); alert('Alteração efetuada!'); window.close();</script>";
					} else echo "<script>alert('Nenhuma alteração foi detectada!')</script>";
				}
			}
}
else if (isset($_GET['cod_reg']) and $_SERVER['REQUEST_METHOD'] != "POST") {
	if (!isset($_GET['emp']))
		$var = mysqli_fetch_array($sql->query("select regiao, icms, empresa from regioes where ativo = 1 and cod_reg = ".$_GET['cod_reg']." and empresa = ".$_SESSION['UsuarioEmpresa']));
	else
		$var = mysqli_fetch_array($sql->query("select regiao, icms, empresa from regioes where ativo = 1 and cod_reg = ".$_GET['cod_reg']." and empresa = ".$_GET['emp']));
	$regiao = $var['regiao'];
	$icms = $var['icms'];
	$empresa = $var['empresa'];
}
else {
	$regiao = "";
	$icms = "";
	$empresa = "";
}
?>
<html>
<head>
<title><?echo $title." - Cadastro de Regiões - ICMS";?></title>
</head>
<body onLoad="document.getElementById('txRegiao').focus();">
<?
//validação página
autorizaPagina(2);
teclaEsc();
?>

	<?if (isset($_GET['cod_reg'])) {
		if (!isset($_GET['emp'])) echo "<form action=\"cadReg.php?cod_reg=".$_GET['cod_reg']."\" method=\"post\">";
		else echo "<form action=\"cadReg.php?cod_reg=".$_GET['cod_reg']."&emp=".$_GET['emp']."\" method=\"post\">";
	}
	else echo "<form action=\"cadReg.php\" method=\"post\">";?>
	<fieldset>
		<?if (isset($_GET['cod_reg'])) echo "<legend>Edição de Regiões - ICMS</legend>";
		else echo "<legend>Cadastro de Regiões - ICMS</legend>";?>
		<table>
			<tr><td><label for="txRegiao">Região: </label></td>
				<td><input type="text" name="regiao" id="txRegiao" maxlength="20" size="55" value="<?echo $regiao;?>"/></td></tr>
				
			<tr><td><label for="txIcms">Icms: </label></td>
				<td><input type="text" name="icms" id="txIcms" maxlength="10" size="40" value="<?echo $icms;?>"/> %</td></tr>
				
			<tr><td><label for="txEmpresa">Empresa: </label></td>
				<td>
				<?
					$sysLista = mysqli_fetch_row($sql->query("SELECT empresa FROM usuarios WHERE id = ".$_SESSION['UsuarioID']));
					$nomeEmpresa = mysqli_fetch_array($sql->query("SELECT nome FROM empresas WHERE id_empresa = ".$sysLista[0]));
					if ($nomeEmpresa['nome']=="Todas" and !isset($_GET['emp'])) {
						echo "<select id=\"txEmpresa\" name=\"empresa\">\n";
						$pesqEmp = $sql->query("select id_empresa, nome, cnpj from empresas where nome != 'Todas'");
						for ($i=1;mysqli_num_rows($pesqEmp)>=$i;$i++) {
							$empList = mysqli_fetch_array($pesqEmp);
							if ($empresa==$empList['id_empresa'])
								echo "<option value=\"".$empList['id_empresa']."\" selected=\"selected\">".$empList['nome']." - ".$empList['cnpj']."</option>\n";
							else
								echo "<option value=\"".$empList['id_empresa']."\">".$empList['nome']." - ".$empList['cnpj']."</option>\n";
						}
						echo "</select>\n";
					} else {
						if (!isset($_GET['emp'])){
							echo $nomeEmpresa['nome']."\n";
							echo "<input type=\"hidden\" name=\"empresa\" value=\"".$_SESSION['UsuarioEmpresa']."\">\n";
						} else {
							$empNome = mysqli_fetch_array($sql->query("select nome from empresas where id_empresa = ".$_GET['emp']));
							echo $empNome['nome']."\n";
							echo "<input type=\"hidden\" name=\"empresa\" value=\"".$_GET['emp']."\">\n";
						}
					}
				?>
				
			<?if (!isset($_GET['cod_reg'])) echo "<input type=\"submit\" value=\"Salvar\"/></td></tr>";
			else echo "<input type=\"submit\" value=\"Alterar\"/></td></tr>";?>
		</table>
	</fieldset>
</form>

</body>
</html>