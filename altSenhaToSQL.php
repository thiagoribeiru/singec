<?require_once("config.php");?>
<html>
<head>
<title> 
<?
echo $nomeSistema;
?></title>
</head>
<body>
<?
$user = $sql->real_escape_string($_POST['user']);
$nome = $sql->real_escape_string($_POST['nome']);
$email = $sql->real_escape_string($_POST['email']);
$numEmpresa = $sql->real_escape_string($_POST['numEmpresa']);
$nivel = $sql->real_escape_string($_POST['nivel']);

// Verifica se houve POST e se o usuário ou a senha ou nome é(são) vazio(s)
if (!empty($_POST) AND (empty($_POST['senhaAtual']) OR empty($_POST['novaSenha']))) {
  header("Location: altSenha.php?user=$user&nome=$nome&email=$email&numEmpresa=$numEmpresa&nivel=$nivel"); exit;
}

//variaveis novas recebem o que foi postado no formulário
$senhaAtual = $sql->real_escape_string($_POST['senhaAtual']);
$novaSenha = $sql->real_escape_string($_POST['novaSenha']);

$query = $sql->query("SELECT senha FROM usuarios WHERE id = $user");
$senhaAntiga = mysqli_fetch_row($query);

// Arquiva os dados no banco de dados
if (sha1($senhaAtual)==$senhaAntiga[0]) {
	$sqll = "UPDATE usuarios SET senha='".sha1($novaSenha)."' WHERE id = $user";
	$sql->query($sqll) or die (mysqli_error($sql));
		echo "Atualização efetuada!<br><a href=\"editUser.php?user=$user&nome=$nome&email=$email&numEmpresa=$numEmpresa&nivel=$nivel&form=altSenhaToSQL\">Voltar</a>";
} else {
	echo "Senha atual errada. <a href=\"altSenha.php?user=$user&nome=$nome&email=$email&numEmpresa=$numEmpresa&nivel=$nivel\">Clique aqui</a> para tentar novamente";
	exit;
}
	
	//header("Location: index.php"); exit;
?>
</body>
</html>