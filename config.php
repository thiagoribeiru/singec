<?
require_once('dependFunc.php');
if (dominio("singec.com.br")) {
	$servername='localhost';
	$username='c3rdw';
	$password='thi102030';
	$database='c3_singec_main';
	$dbport = false;
	$tempoOcioso = 10*60;//300 seconds or 5 minutes
	$showErrorDeprecated = false;
	$displayError = false;
} else if (dominio("c9user")) {
    $servername = getenv('IP');
    $username = substr(getenv('C9_USER'),0,16);
    $password = "";
    $database = "c9";
    $dbport = 3306;
    $tempoOcioso = 60*60;
	$showErrorDeprecated = false;
	$displayError = false;
} else if (dominio("localhost")) {
    $servername = 'localhost';
    $username = 'bd_singec';
    $password = 'singec123';
    $database = 'singec';
    $dbport = 3306;
    $tempoOcioso = 60*60;
	$showErrorDeprecated = false;
	$displayError = true;
} else if (dominio("ribeirodesenvolvimentoweb")) {
    $servername = 'localhost';
    $username = 'c3rdw';
    $password = 'thi102030';
    $database = 'c3_singec_d';
    $dbport = 3306;
    $tempoOcioso = 60*60;
	$showErrorDeprecated = true;
	$displayError = true;
} else {
	echo "Verifique sua conexão com o Banco de Dados! \n";
	exit;
}
conecta($servername,$username,$password,$dbport,$database,$tempoOcioso,$nomeSistema,$versaoSistema,$showErrorDeprecated,$displayError);
?>