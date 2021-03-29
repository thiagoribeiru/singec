<?require_once("config.php");?>
<html>
<head>
<title> 
<?
echo $nomeSistema;
?></title>
<script type="text/javascript">
	function atualizaUsuarios() {
		opener.location.reload();
	}
</script>
</head>
<body onunload="atualizaUsuarios();">
<?
// Verifica se houve POST e se o usuário ou a senha ou nome é(são) vazio(s)
if (!empty($_POST) AND (empty($_POST['email']) OR empty($_POST['nome']))) {
  header("Location: editUser.php"); exit;
}

//variaveis novas recebem o que foi postado no formulário
$user = $sql->real_escape_string($_POST['user']);
$nome = $sql->real_escape_string($_POST['nome']);
$email = $sql->real_escape_string($_POST['email']);
$empresa = $sql->real_escape_string($_POST['empresa']);
$nivel = $sql->real_escape_string($_POST['nivel']);

// Arquiva os dados no banco de dados
$sqll = "UPDATE usuarios set nome='$nome', email='$email', empresa='$empresa', nivel='$nivel' where id='$user'";
$sql->query($sqll) or die (mysqli_error($sql));
	echo "Atualização efetuada!";
	
	//header("Location: index.php"); exit;
?>
</body>
</html>