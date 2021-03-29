<?
$popup = 1;
require_once("session.php");

$sysLista = mysqli_fetch_row($sql->query("SELECT empresa FROM usuarios WHERE id = ".$_SESSION['UsuarioID']));
$nomeEmpresa = mysqli_fetch_array($sql->query("SELECT nome FROM empresas WHERE id_empresa = ".$sysLista[0]));

if ($nomeEmpresa['nome']!="Todas") {
	echo "<script>window.close()</script>";
	exit;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	//variaveis novas recebem o que foi postado no formulário
	$nome = $sql->real_escape_string($_POST['nome']);
	$iso = strtoupper($sql->real_escape_string($_POST['iso']));
	$simbolo = strtoupper($sql->real_escape_string($_POST['simbolo']));
	
			if (!empty($_POST) AND ((empty($_POST['nome']) OR empty($_POST['iso']) OR empty($_POST['simbolo'])))) {
				echo "<script>alert('Preencha todos os campos!')</script>";
			}
			else {
				$retorno = cota($iso);
				if ($retorno['cotacao']!="N/A") {
					$pesquisa = "select iso from moedas where iso = '".$iso."'";
					$sqll = $sql->query($pesquisa);
					$numCadastros = mysqli_num_rows($sqll);
					if ($numCadastros>0) {
						echo "<script>alert('A moeda informada já está cadastrada no sistema.')</script>";
					}
					else {
						// Arquiva os dados no banco de dados
						$sqll = "INSERT INTO moedas (nome, iso, moeda, ativo) VALUES ('$nome','$iso','$simbolo','1')";
						$sql->query($sqll) or die (mysqli_error($sql));
						forcaCotaServ();
							echo "<script>atualizaPagMae(); alert('Cadastro efetuado!'); window.close();</script>";
					}
				}
				else {
					echo "<script>alert('Código ISO inválido! Favor verificar o ISO, tentar novamente ou entrar em contato.')</script>";
				}
			}
}
else {
	$nome = "";
	$iso = "";
	$simbolo = "";
}
?>
<html>
<head>
<title><?echo $title." - Cadastro de Moedas";?></title>
</head>
<body onLoad="document.getElementById('txNome').focus();">
<?
//validação página
autorizaPagina(2);
teclaEsc();
?>

	<form action="cadMoeda.php" method="post">
	<fieldset>
		<legend>Cadastro de Moedas</legend>
		<table>
			<tr><td><label for="txNome">Nome Moeda: </label></td>
				<td><input type="text" name="nome" id="txNome" maxlength="30" size="40" value="<?echo $nome;?>"/></td></tr>
				
			<tr><td><label for="txIso">Cod. ISO: </label></td>
				<td><input type="text" name="iso" id="txIso" maxlength="3" size="40" value="<?echo $iso;?>"/></td></tr>
			
			<tr><td><label for="txSimbolo">Símbolo: </label></td>
				<td><input type="text" name="simbolo" id="txSimbolo" maxlength="3" size="40" value="<?echo $simbolo;?>"/></td></tr>
				
			<tr><td></td><td><center><input type="submit" value="<?echo "Salvar";?>"/></center></td>
		</table>
	</fieldset>
</form>

</body>
</html>