<?
$popup = 1;
require_once ("session.php");
autorizaPagina(2);

$user = $_GET['user'];
$userPesq = $sql->query("select empresa from usuarios where id = '$user' and empresa = '".$_SESSION['UsuarioEmpresa']."'") or die (mysqli_error($sql));
if ($_SESSION['UsuarioID']!=$user and (mysqli_num_rows($userPesq)>0 or usuarioSys()==true)) {
    $pesquisa = "select ativo from usuarios where id = $user";
    $sqll = $sql->query($pesquisa);
    $result = mysqli_fetch_row($sqll);
    
    if ($result[0]==0) $update = "update usuarios set ativo = 1 where id = $user";
    if ($result[0]==1) $update = "update usuarios set ativo = 0 where id = $user";
    
    $sqlup = $sql->query($update);
    if ($sqlup) {
        // header ("Location: usuarios.php");
        ?>
        <script>
            // alert("Foi!");
            window.location.href='usuarios.php';
        </script>
        <?
    } else {
        ?>
        <script>
            alert("Algo deu errado!");
            window.location.href='usuarios.php';
        </script>
        <?
    }
} else if ($_SESSION['UsuarioID']==$user) {
    ?>
    <script>
        alert("Não é possível desativar seu próprio usuário!");
        window.location.href='usuarios.php';
    </script>
    <?
} else if (mysqli_num_rows($userPesq)<=0) {
    ?>
    <script>
        alert("Alteração não permitida!");
        window.location.href='usuarios.php';
    </script>
    <?
} else {
    ?>
    <script>
        alert("Erro não identificado! Favor entrar em contato para informar o erro!");
        window.location.href='usuarios.php';
    </script>
    <?
}

function usuarioSys() {
	global $sql;
    $sysPesquisa = "SELECT empresa FROM usuarios WHERE id = ".$_SESSION["UsuarioID"];
	$sysResult = $sql->query($sysPesquisa) or die (mysqli_error($sql));
	$sysLista = mysqli_fetch_row($sysResult);
	$nomeEmpresa = mysqli_fetch_array($sql->query("SELECT nome FROM empresas WHERE id_empresa = ".$sysLista[0]));
	
	if ($nomeEmpresa[0] == "Todas") return true;
	else return false;
}
?>