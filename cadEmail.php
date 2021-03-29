<?
require_once('config.php');

// A sessão precisa ser iniciada em cada página diferente
if (!isset($_SESSION)) session_start();
// Verifica se não há a variável da sessão que identifica o usuário
if (!isset($_SESSION['UsuarioID']) or !isset($_SESSION['UsuarioSitu'])) {
  // Destrói a sessão por segurança
  session_destroy();
  // Redireciona o visitante de volta pro login
  header("Location: login.php"); exit;
}
$retorno['error'] = 0;
$retorno['mensagem'] = "";
function errosql($num,$msg) {
    $retorno['error']++;
    $retorno['mensagem'] .= $num." - ".$msg."\r\n";
    echo json_encode($retorno);
}

if (isset($_GET) and $_GET['relatorio']=='entradas_ult_24h') {
    $tipo = $_GET['relatorio'];
    // $email = $_GET['entradas_ult_24h'];
    $email = $_GET['stringEmail'];
    $hora = $_GET['hora'];
    $minuto = $_GET['minuto'];
    // $empresa = $_SESSION['UsuarioEmpresa'];
    $empresa = $_GET['id_empresa'];
    $usuario = $_SESSION['UsuarioID'];
	if (strtotime($hora.':'.$minuto)<=time()) $day_prox = date('d')+1;
	else $day_prox = date('d');
	$prox_disparo = date('Y-m-'.$day_prox)." ".$hora.":".$minuto.":00";
    
    $pesq = $sql->query("select * from emails_automaticos where ativo = '1' and empresa = '$empresa' and tipo = '$tipo'") or die (errosql(mysqli_errno($sql),mysqli_error($sql)));
    if (mysqli_num_rows($pesq)>0 and $email!="") {
        $item = mysqli_fetch_array($pesq);
        if ($tipo!=$item['tipo'] or $hora!=$item['hora'] or $minuto!=$item['minuto'] or $email!=$item['email']) {
        	$disparoData = explode(" ",$item['prox_disparo']);
        	$disparoHora = explode(" ",$prox_disparo);
			$prox_disparo = $disparoData[0]." ".$disparoHora[1];
			// $prox_disparo = $item['prox_disparo'];
            $sql->query("update emails_automaticos set ativo = '0' where ativo = '1' and empresa = '$empresa' and tipo = '$tipo'") or die(errosql(mysqli_errno($sql),mysqli_error($sql)));
            $sql->query("insert into emails_automaticos (tipo,hora,minuto,email,prox_disparo,empresa,data,usuario,ativo) values ('$tipo','$hora','$minuto','$email','$prox_disparo','$empresa',now(),'$usuario','1')") or die (errosql(mysqli_errno($sql),mysqli_error($sql)));
        }
    } else if (mysqli_num_rows($pesq)>0 and $email=="") {
        $sql->query("update emails_automaticos set ativo = '0' where ativo = '1' and empresa = '$empresa' and tipo = '$tipo'") or die(errosql(mysqli_errno($sql),mysqli_error($sql)));
    } else if ($email!="") {
        $sql->query("insert into emails_automaticos (tipo,hora,minuto,email,prox_disparo,empresa,data,usuario,ativo) values ('$tipo','$hora','$minuto','$email','$prox_disparo','$empresa',now(),'$usuario','1')") or die (errosql(mysqli_errno($sql),mysqli_error($sql)));
    }
}
echo json_encode($retorno);
?>