<?
require_once('config.php');
if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$remetente = $_POST['email'];
	$nome = $_POST['nome'];
	$assunto = $_POST['assunto'];
	$texto = $_POST['texto'];
	// $to      = 'singec@singec.com.br';
	$to = 'singec@singec.com.br';
	// $headers = 'From: '.$nome.' <'.$remetente.'>'."\r\n".'Reply-To: '.$remetente."\r\n".'X-Mailer: PHP/'.phpversion();
	// mail($to, $assunto, $texto, $headers);
	$headers = array(
		'fromName' => $nome,
		'fromAddress' => $remetente,
		'replyTo' => $remetente,
		'replyToName' => $nome,
	);
	enviaEmail($to,$assunto,$texto,'',$headers);
	
	echo "<script>alert('Mensagem enviada! Em breve entraremos em contato!'); document.location.href = '".$_GET['url']."';</script>";
}
?>