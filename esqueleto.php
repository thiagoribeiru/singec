<html>
<head>
<?php
require_once("config.php");
// A sessão precisa ser iniciada em cada página diferente
if (!isset($_SESSION)) session_start();
// Verifica se não há a variável da sessão que identifica o usuário
if (!isset($_SESSION['UsuarioID'])) {
  // Destrói a sessão por segurança
  session_destroy();
  // Redireciona o visitante de volta pro login
  header("Location: login.php"); exit;
}
?>
<title><?
	echo $nomeSistema." - ";
	$idEmpresa = $_SESSION['UsuarioEmpresa'];
	$nomeEmpresa = mysqli_fetch_array($sql->query("SELECT nome FROM empresas WHERE id_empresa = $idEmpresa"));
	echo $nomeEmpresa['nome'];
?></title>
</head>
<body>
<?
$usuario = $_SESSION['UsuarioNome'];
echo "Olá, $usuario! <a href=\"logout.php\">LogOut</a>";



?>
</body>
</html>