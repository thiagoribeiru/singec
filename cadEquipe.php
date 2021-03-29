<?
$popup = 1;
require_once("session.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	//variaveis novas recebem o que foi postado no formulário
	if (!isset($_GET['cod_equipe'])) {
		$pesqCod = $sql->query("select cod_equipe from equipes_de_venda where ativo = 1 and empresa = ".$sql->real_escape_string($_POST['empresa'])." order by cod_equipe desc");
		if (mysqli_num_rows($pesqCod)==0) $cod_equipe = 1;
		else {
			$ultimo_cod = mysqli_fetch_array($pesqCod);
			$cod_equipe = $ultimo_cod['cod_equipe'] + 1;
		}
	} else $cod_equipe = $_GET['cod_equipe'];
	$equipe = $sql->real_escape_string($_POST['equipe']);
	$usuario = $_SESSION['UsuarioID'];
	$empresa = $sql->real_escape_string($_POST['empresa']);
	
			if (!empty($_POST) AND (empty($_POST['equipe']))) {
				echo "<script>alert('Preencha todos os campos!')</script>";
			}
			else {
				if (!isset($_GET['cod_equipe'])) {
					//verifica se equipe já foi cadastrado
					$pesquisa = "select equipe from equipes_de_venda where empresa = '".$empresa."' and equipe = '".$equipe."'";
					$sqll = $sql->query($pesquisa);
					$numCadastros = mysqli_num_rows($sqll);
					if ($numCadastros>0) {
						echo "<script>alert('A equipe informada já está cadastrada no sistema.')</script>";
					}
					else {
						// Arquiva os dados no banco de dados
						$sqll = "INSERT INTO equipes_de_venda (cod_equipe, equipe, data, usuario, empresa, ativo) VALUES ('$cod_equipe','$equipe',now(),'$usuario','$empresa',1)";
						$sql->query($sqll) or die (mysqli_error($sql));
							echo "<script>atualizaPagMae(); alert('Cadastro efetuado!'); window.close();</script>";
					}
				}
				if (isset($_GET['cod_equipe'])) {
					$antigo = mysqli_fetch_array($sql->query("select equipe from equipes_de_venda where cod_equipe = ".$_GET['cod_equipe']." and ativo = 1 and empresa = $empresa"));
					$equipeAnt = $antigo['equipe'];
					if ($equipeAnt!=$equipe) {
						//altera anterior para ativo = 0
						$sql->query("update equipes_de_venda set ativo = 0 where cod_equipe = $cod_equipe and empresa = $empresa") or die (mysqli_error($sql));
						$sql->query("INSERT INTO equipes_de_venda (cod_equipe, equipe, data, usuario, empresa, ativo) VALUES ('$cod_equipe','$equipe',now(),'$usuario','$empresa',1)") or die (mysqli_error($sql));
						echo "<script>atualizaPagMae(); alert('Alteração efetuada!'); window.close();</script>";
					} else echo "<script>alert('Nenhuma alteração foi detectada!')</script>";
				}
			}
}
else if (isset($_GET['cod_equipe']) and $_SERVER['REQUEST_METHOD'] != "POST") {
	if (!isset($_GET['emp']))
		$var = mysqli_fetch_array($sql->query("select equipe, empresa from equipes_de_venda where ativo = 1 and cod_equipe = ".$_GET['cod_equipe']." and empresa = ".$_SESSION['UsuarioEmpresa']));
	else
		$var = mysqli_fetch_array($sql->query("select equipe, empresa from equipes_de_venda where ativo = 1 and cod_equipe = ".$_GET['cod_equipe']." and empresa = ".$_GET['emp']));
	$equipe = $var['equipe'];
	$empresa = $var['empresa'];
}
else {
	$equipe = "";
	$empresa = "";
}
?>
<html>
<head>
<title><?echo $title." - Cadastro de Equipes de Venda";?></title>
</head>
<body onLoad="document.getElementById('txEquipe').focus();">
<?
//validação página
autorizaPagina(2);
teclaEsc();
?>

	<?if (isset($_GET['cod_equipe'])) {
		if (!isset($_GET['emp'])) echo "<form action=\"cadEquipe.php?cod_equipe=".$_GET['cod_equipe']."\" method=\"post\">";
		else echo "<form action=\"cadEquipe.php?cod_equipe=".$_GET['cod_equipe']."&emp=".$_GET['emp']."\" method=\"post\">";
	}
	else echo "<form action=\"cadEquipe.php\" method=\"post\">";?>
	<fieldset>
		<?if (isset($_GET['cod_equipe'])) echo "<legend>Edição de Equipes de Venda</legend>";
		else echo "<legend>Cadastro de Equipes de Venda</legend>";?>
		<table>
			<tr><td><label for="txEquipe">Equipe: </label></td>
				<td><input type="text" name="equipe" id="txEquipe" maxlength="20" size="55" value="<?echo $equipe;?>"/></td></tr>
				
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
				
			<?if (!isset($_GET['cod_equipe'])) echo "<input type=\"submit\" value=\"Salvar\"/></td></tr>";
			else echo "<input type=\"submit\" value=\"Alterar\"/></td></tr>";?>
		</table>
	</fieldset>
</form>

</body>
</html>