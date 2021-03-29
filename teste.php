<?
$nome = 'Thiago Ribeiro';
$remetente = 'singec@singec.com.br';
$headers = 'From: '.$nome.' <'.$remetente.'>'."\r\n".'Reply-To: '.$remetente."\r\n".'X-Mailer: PHP/'.phpversion();

$headers = array(
	'fromName' => $nome,
	'fromAddress' => $remetente,
	'replyTo' => $remetente,
);
var_dump($headers);

// if (1==0) {
    // require_once('plugins/crontab/crontab.php');
    // $crontab=new crontab("/www/test/cron/", "filename");
    // $crontab=new crontab();
    
    //[minutos] [horas] [dias do mês] [mês] [dias da semana]
    // - Minutos: informe números de 0 a 59;
    
    // - Horas: informe números de 0 a 23;
    
    // - Dias do mês: informe números de 0 a 31;
    
    // - Mês: informe números de 1 a 12;
    
    // - Dias da semana: informe números de 0 a 7;
    
    // $crontab->setDateParams('00,30', "*", "*", "*", "*");
    // $crontab->setCommand("curl http://www.mysite.com/?action=do_me");
    // $crontab->setCommand('php -q '.$_SERVER['DOCUMENT_ROOT'].'/tarefasCron.php');
    // $crontab->saveCronFile();
    // $crontab->addToCrontab();
    // $crontab->destroyFilePoint(); // OPTIONAL
    //crontab -r deleta crontab
// }

// require_once('PHPMailer/PHPMailerAutoload.php');

// $mail = new PHPMailer;

//$mail->SMTPDebug = 3;                               // Enable verbose debug output

// $mail->isSMTP();                                      // Set mailer to use SMTP
// $mail->Host = 'ssl://br660.hostgator.com.br';  // Specify main and backup SMTP servers
// $mail->SMTPAuth = true;                               // Enable SMTP authentication
// $mail->Username = 'noreply@singec.com.br';                 // SMTP username
// $mail->Password = 'AOw1sLITieW1';                           // SMTP password
// $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
// $mail->Port = 465;                                    // TCP port to connect to

// $mail->setFrom('singec@singec.com.br', 'Mailer');
// $mail->addAddress('singec@singec.com.br', 'Joe User');     // Add a recipient
// $mail->addAddress('ellen@example.com');               // Name is optional
// $mail->addReplyTo('info@example.com', 'Information');
// $mail->addCC('cc@example.com');
// $mail->addBCC('bcc@example.com');

// $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
// $mail->isHTML(true);                                  // Set email format to HTML

// $mail->Subject = 'Here is the subject';
// $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
// $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

// if(!$mail->send()) {
    // echo 'Message could not be sent.';
    // echo 'Mailer Error: ' . $mail->ErrorInfo;
// } else {
    // echo 'Message has been sent';
// }
?>