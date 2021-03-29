<?
require_once("config.php");
// echo basename($_SERVER['PHP_SELF'],'.php');

// A sessão precisa ser iniciada em cada página diferente
if (!isset($_SESSION)) session_start();
// Verifica se não há a variável da sessão que identifica o usuário
if (!isset($_SESSION['UsuarioID']) or !isset($_SESSION['UsuarioSitu'])) {
  // Destrói a sessão por segurança
  session_destroy();
  // Redireciona o visitante de volta pro login
  header("Location: login.php"); exit;
}
	//echo $nomeSistema." - ";
	$idEmpresa = $_SESSION['UsuarioEmpresa'];
	$nomeEmpresa = mysqli_fetch_array($sql->query("SELECT nome FROM empresas WHERE id_empresa = $idEmpresa"));
	//echo $nomeEmpresa['nome'];
	$title = $nomeSistema." - ".$nomeEmpresa['nome'];
	
//destrói sessão usuário inativado
$situUsuario = mysqli_fetch_array($sql->query("select ativo from usuarios where id = ".$_SESSION['UsuarioID']));
if ($situUsuario['ativo']!=1){
	session_destroy();
	header ("Location: login.php");
}
	
function autorizaPagina($nivUser) {
	if ($nivUser != $_SESSION['UsuarioNivel']) {
		header("Location: index.php");
		exit;
	}
}

//verifica se esse já não está vencido
if (mysqli_num_rows($sql->query("select id_user from users_logados where now() > date_add(validade, interval 1 minute) and id_user = "
				.$_SESSION['UsuarioID']))> 0 and count(explode("phpxls.",$_SERVER['PHP_SELF']))>1) {header("Location: logout.php"); exit;}
$userPesq = $sql->query("select id_user, id_sessao, validade from users_logados where id_user = ".$_SESSION['UsuarioID']);
//verifica se sessao é a unica aberta se não derruba essa e deixa a ultima
$userSit = mysqli_fetch_array($userPesq);
if ($userSit['id_sessao']!=session_id()) {header("Location: logout.php"); exit;}
//verifica sessao e da update na validade
$numIds = mysqli_num_rows($userPesq);
if ($numIds==1) {
	$sql->query("update users_logados set id_sessao = '".session_id().
	"', validade = date_add(now(), interval $tempoOcioso second) where id_user = ".$_SESSION['UsuarioID']) or die (mysqli_error($sql));
} setcookie('sessao_val',strtotime($userSit['validade']));
//verifica se algo ocorreu errado e tem mais de uma sessao do usuario aberta e derruba todo mundo
if ($numIds>1) {header("Location: logout.php"); exit;}

//armazena utlima página visitada nas ultimas 24 horas para recuperar no próximo login
if (!isset($popup))
	setcookie("ultimaPagUser".$_SESSION['UsuarioID'], $_SERVER['REQUEST_URI'], time()+(24*(60*60)));

require_once("funcoes.php"); //somente funcoes sem echo!!

function teclaEsc() {
	echo "<!--<script src=\"jQuery1.11.1.js\"></script>-->
	<script type=\"text/javascript\">
	// Controlamos que si pulsamos escape se cierre el div
    $(document).keyup(function(event){
			if(event.which==27)
			{
				window.close();
				//$(\"#infoWeb\").hide(); //para fechar divs ;)
			}
    });
	</script>";
}

function atualizaCookieValor() {
	global $sql;
	$valor = mysqli_fetch_array($sql->query("select compra, venda from moedas where iso = '".$_SESSION['MoedaIso']."' and ativo = 1")) or die(mysqli_error($sql));
	setcookie("vlrCompra",$valor['compra']);
	setcookie("vlrVenda",$valor['venda']);
	if (!isset($_COOKIE['vlrCompra']) or !isset($_COOKIE['vlrVenda'])) {
		echo "<meta http-equiv=\"Content-Type\" content=\"text/html\" charset=\"utf-8\">";
		echo "-> Recarregando informações...<br>-> Aguarde...";
		echo "<script>window.location.reload();</script>";
		exit;
	}
}
if (!isset($_COOKIE['ultimaCota'])) {
	$pesq = $sql->query("select datetime from moedas where iso = 'USD' and ativo = 1");
	if (mysqli_num_rows($pesq)>0) {
		$dolar = mysqli_fetch_array($pesq);
		if ((strtotime($dolar['datetime'])+7200)<time()) {
			cotaServ();
			atualizaCookieValor();
			setcookie("ultimaCota",time());
		} else {
			atualizaCookieValor();
			setcookie("ultimaCota",strtotime($dolar['datetime']));
		}
	}
} else {
	if (($_COOKIE['ultimaCota']+7200)<time()) {
		cotaServ();
		$pesq = $sql->query("select datetime from moedas where iso = 'USD' and ativo = 1");
		if (mysqli_num_rows($pesq)>0) {
			$dolar = mysqli_fetch_array($pesq);
			atualizaCookieValor();
			setcookie("ultimaCota",strtotime($dolar['datetime']));
		}
	}
}

echo "<link rel=\"shortcut icon\" type=\"image/x-icon\" href=\"images/favicon.ico\"/>\n";
echo "<script src=\"jquery-1.8.2.js\"></script>";
// echo "<script src=\"jQuery1.11.1.js\"></script>";
// echo "<script src=\"jQuery1.12.1.min.js\"></script>";
//date_default_timezone_set('America/Sao_Paulo');
echo "<link type=\"text/css\" href=\"style.css\" rel=\"stylesheet\">\n";
if (!isset($popup)) echo "<link type=\"text/css\" href=\"styleSemPopUp.css\" rel=\"stylesheet\">\n";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html\" charset=\"utf-8\">\n";
if (!isset($popup)) echo "<script type=\"text/javascript\">setInterval('validaAtiv()', 3000);</script>\n";
else if (isset($popup)) echo "<script type=\"text/javascript\">setInterval('validaAtiv(\"popup\")', 3000);</script>\n";
else echo "<script type=\"text/javascript\">setInterval('validaAtiv()', 3000);</script>\n";
if (!isset($popup)) {
	echo "<script src=\"plugins/pace/pace.min.js\"></script>\n";
	echo "<link type=\"text/css\" href=\"plugins/pace/minimal.css\" rel=\"stylesheet\">\n";
}
?>
<script type="text/javascript">
	function atualizaPagMae() {
		opener.location.reload();
	}
	function quantidade (url,fechamento) {
		if (typeof(fechamento) == "undefined") {window.close();}
		abrirPopup(url,800,150);
	}
	function abrirPopup(url,w,h,fechamento) {
		var newW = w + 100;
		var newH = h + 100;
		var left = (screen.width-newW)/2;
		var top = (screen.height-newH)/2;
		var newwindow = window.open(url, '', 'width='+newW+',height='+newH+',left='+left+',top='+top);
		if (typeof(fechamento) != "undefined") {window.close();}
		newwindow.resizeTo(newW, newH);
		 
		//posiciona o popup no centro da tela
		newwindow.moveTo(left, top);
		newwindow.focus();
		return false;
	}
	function abrirPopupFull(url,fechamento) {
		
		var newwindow = window.open(url, '','status=no, toolbar=no, menubar=no, location=no, fullscreen=1, scrolling=auto');
		if (typeof(fechamento) != "undefined") {window.close();}
		
		return false;
	}
	function getCookie(name) {
		var cookies = document.cookie;
		var prefix = name + "=";
		var begin = cookies.indexOf("; " + prefix);
	 
		if (begin == -1) {
			begin = cookies.indexOf(prefix);
			if (begin != 0) {
				return null;
			}
		} else {
			begin += 2;
		}
		var end = cookies.indexOf(";", begin);
		if (end == -1) {
			end = cookies.length;                        
		}
		return unescape(cookies.substring(begin + prefix.length, end));
	}
	function setCookie(name, value, duration) {
        var cookie = name + "=" + escape(value) +
        ((duration) ? "; duration=" + duration.toGMTString() : "");
 
        document.cookie = cookie;
	}
	function deleteCookie(name) {
       if (getCookie(name)) {
              document.cookie = name + "=" +
              "; expires=Thu, 01-Jan-70 00:00:01 GMT";
       }
	}
	function validaAtiv(popup) {
		if (typeof(popup) != "undefined") {
			var now = new Date();
			//var sessao_id = getCookie("sessao_id");
			var sessao_val = new Date(getCookie("sessao_val") * 1000);
			if (sessao_val.getTime() <= now.getTime()) {
				window.close();
			}
		}
		if (typeof(popup) == "undefined") {
			var now = new Date();
			//var sessao_id = getCookie("sessao_id");
			var sessao_val = new Date(getCookie("sessao_val") * 1000);
			if (sessao_val.getTime() <= now.getTime()) {
				location.href="logout.php";
				// alert(sessao_val);
			}
			//document.write(now.getTime() + " - " + sessao_val.getTime());
		}
	}
	//setInterval('validaAtiv()', 3000);
	function sleep(seconds) {
		var milliseconds = seconds * 1000;
		var start = new Date().getTime();
		for (var i = 0;i<1e7*2;i++) {
			if ((new Date().getTime() - start) > milliseconds) {
				break;
			}
		}
	}
</script>