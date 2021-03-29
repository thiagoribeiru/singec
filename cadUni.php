<?
$popup = 1;
require_once("session.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	//variaveis novas recebem o que foi postado no formulário
	$unidade = $sql->real_escape_string($_POST['unidade']);
	$empresa = $sql->real_escape_string($_POST['empresa']);
	
			if (!empty($_POST) AND (empty($_POST['unidade']) OR empty($_POST['empresa']))) {
				echo "<script>alert('Preencha todos os campos!')</script>";
			}
			else {
				// if (!isset($_GET['emp'])) {
					//verifica se unidade já foi cadastrada
					$pesquisa = "select unidade from unidades where empresa = '".$empresa."' and unidade = '".$unidade."'";
					$sqll = $sql->query($pesquisa);
					$numCadastros = mysqli_num_rows($sqll);
					if ($numCadastros>0) {
						echo "<script>alert('A unidade informada já está cadastrada no sistema.')</script>";
					}
					else {
						// Arquiva os dados no banco de dados
						$sqll = "INSERT INTO unidades (unidade, empresa) VALUES ('$unidade','$empresa')";
						$sql->query($sqll) or die (mysqli_error($sql));
							echo "<script>atualizaPagMae(); alert('Cadastro efetuado!'); window.close();</script>";
					}
				// }
				// if (isset($_GET['emp'])) {
				// 	$antigo = mysqli_fetch_array($sql->query("select nome, cnpj from empresas where cnpj = ".$_GET['emp']));
				// 	$nomeAnt = $antigo['nome'];
				// 	$cnpjAnt = $antigo['cnpj'];
				// 	if (($nomeAnt!=$nome) or ($cnpjAnt!=$cnpj)) {
				// 		$sql->query("UPDATE empresas SET cnpj='".$cnpj."',nome='".$nome."' WHERE cnpj = ".$_GET['emp']);
				// 		echo "<script>atualizaPagMae(); alert('Alteração efetuada!'); window.close();</script>";
				// 	} else echo "<script>alert('Nenhuma alteração foi detectada!')</script>";
				// }
			}
}
// else if (isset($_GET['emp']) and $_SERVER['REQUEST_METHOD'] != "POST") {
// 	$empresa = mysqli_fetch_array($sql->query("select nome, cnpj from empresas where cnpj = ".$_GET['emp']));
// 	$nome = $empresa['nome'];
// 	$cnpj = $empresa['cnpj'];
// 	if ($nome=="Todas") {
// 		echo "<script>alert('Parâmetro reservado não permitido! Por questões de segurança, a operação será cancelada.'); window.close();</script>";
// 	}
// }
else {
	$unidade = "";
	$empresa = "";
}
?>
<html>
<head>
<title><?echo $title." - Cadastro de Unidades";?></title>
</head>
<body onLoad="document.getElementById('txUnidade').focus();">
<?
//validação página
autorizaPagina(2);
teclaEsc();
?>

	<?//if (!isset($_GET['emp'])) echo "<form action=\"cadEmp.php\" method=\"post\">";
	  //if (isset($_GET['emp'])) echo "<form action=\"cadEmp.php?emp=".$_GET['emp']."\" method=\"post\">";?>
	<form action="cadUni.php" method="post">
	<fieldset>
		<?//if (!isset($_GET['emp'])) echo "<legend>Cadastro de Empresas</legend>";
		  //if (isset($_GET['emp'])) echo "<legend>Editar Empresa</legend>";?>
		<legend>Cadastro de Unidades</legend>
		<table>
			<tr><td><label for="txUnidade">Unidade: </label></td>
				<td><input type="text" name="unidade" id="txUnidade" maxlength="25" size="40" value="<?echo $unidade;?>"/></td></tr>
				
			<tr><td><label for="txEmpresa">Empresa: </label></td>
				<td>
				<?
					$sysLista = mysqli_fetch_row($sql->query("SELECT empresa FROM usuarios WHERE id = ".$_SESSION['UsuarioID']));
					$nomeEmpresa = mysqli_fetch_array($sql->query("SELECT nome FROM empresas WHERE id_empresa = ".$sysLista[0]));
					if ($nomeEmpresa['nome']=="Todas") {
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
						echo $nomeEmpresa['nome']."\n";
						echo "<input type=\"hidden\" name=\"empresa\" value=\"".$_SESSION['UsuarioEmpresa']."\">\n";
					}
				?>
				</td>
				
			<tr><td></td><td><center><input type="submit" value="<?echo "Salvar";?>"/></center></td>
		</table>
	</fieldset>
</form>

</body>
</html>