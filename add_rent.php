<?$popup = 1;
require_once("session.php");
teclaEsc();?>
<html>
<head>
<title><?echo $title." - Quantidades";?></title>
<!--<script src="jQuery1.12.1.min.js" type="text/javascript"></script>-->
<script language="JavaScript" type="text/javascript">
	function mascaraData(campoData){
		// var data = campoData.value;
		// if (data.length == 2){
		// 	data = data + '/';
		// 	document.forms[0].data.value = data;
		// 	return true;              
		// }
		// if (data.length == 5){
		// 	data = data + '/';
		// 	document.forms[0].data.value = data;
		// 	return true;
		// }
		var objeto = campoData;
		if (objeto.value.length == 2 || objeto.value.length == 5 ){
			objeto.value = objeto.value+"/";
		}
	}
	function focar() {
		if (document.getElementById('txDataPed').value==""){
			document.getElementById('txDataPed').focus();
		} else if (document.getElementById('txCodCli').value==""){
			document.getElementById('txCodCli').focus();
		} else if (document.getElementById('txCodSp').value==""){
			document.getElementById('txCodSp').focus();
		} else if (document.getElementById('txQuant').value==""){
			document.getElementById('txQuant').focus();
		} else if (document.getElementById('txVlrNf').value==""){
			document.getElementById('txVlrNf').focus();
		} else if (document.getElementById('txData').value==""){
			document.getElementById('txData').focus();
		} else document.getElementById('txDataPed').focus();
	}
	function selecDescelec(element,onoff) {
		var checkbox = document.getElementById(element);
		var status = onoff;
		checkbox.checked = status;
	}
	function opcSaldo(quantOldd,rentCod) {
		var quantOld = Number(quantOldd.replace(",","."));
		var newQuant = Number(document.getElementById("txQuant").value.replace(",","."));
		var datafat = document.getElementById("txData").value;
		if (rentCod!="" && newQuant<quantOld && datafat!="") {
			document.getElementById("saldo").style.display = "block";
			selecDescelec("saldoinput",true);
		} else {
			document.getElementById("saldo").style.display = "none";
			selecDescelec("saldoinput",false);
		}
	}
	$(document).ready(function(){
		var quant = $("#txQuant").val();
		var codRent = $("#codRentHidden").val();
		$("#txQuant").blur(function(){
			opcSaldo(quant,codRent);
		});
		$("#txData").on('input',function(){
			opcSaldo(quant,codRent);
		});
	});
</script>
</head>
<body onLoad="focar();" bgcolor="#CEECF5">
<?
//validação página
autorizaPagina(2);
// teclaEsc();

if (!isset($_GET['cod_rent'])) {
	echo "<script>\n";
		echo "function recarrega(){\n";
		echo "var dataped = document.getElementById(\"txDataPed\").value;\n";
		echo "if (dataped==undefined) {dataped=\"\";}\n";
		echo "var cod_cli = document.getElementById(\"txCodCli\").value;\n";
		echo "if (cod_cli==undefined) {cod_cli=\"\";}\n";
		echo "var cod_sp = document.getElementById(\"txCodSp\").value;\n";
		echo "if (cod_sp==undefined) {cod_sp=\"\";}\n";
		echo "var quant = document.getElementById(\"txQuant\").value;\n";
		echo "if (quant==undefined) {quant=\"\";}\n";
		echo "var vlr_nf = document.getElementById(\"txVlrNf\").value;\n";
		echo "if (vlr_nf==undefined) {vlr_nf=\"\";}\n";
		echo "var data = document.getElementById(\"txData\").value;\n";
		echo "if (data==undefined) {data=\"\";}\n";
	echo "window.location.href=\"add_rent.php?dataped=\"+dataped+\"&cod_cli=\"+cod_cli+\"&cod_sp=\"+cod_sp+\"&quant=\"+quant+\"&vlr_nf=\"+vlr_nf+\"&data=\"+data;\n";
		echo "}\n";
	echo "</script>\n";
} else {
	echo "<script>\n";
		echo "function recarrega(){\n";
		echo "var dataped = document.getElementById(\"txDataPed\").value;\n";
		echo "if (dataped==undefined) {dataped=\"\";}\n";
		echo "var cod_cli = document.getElementById(\"txCodCli\").value;\n";
		echo "if (cod_cli==undefined) {cod_cli=\"\";}\n";
		echo "var cod_sp = document.getElementById(\"txCodSp\").value;\n";
		echo "if (cod_sp==undefined) {cod_sp=\"\";}\n";
		echo "var quant = document.getElementById(\"txQuant\").value;\n";
		echo "if (quant==undefined) {quant=\"\";}\n";
		echo "var vlr_nf = document.getElementById(\"txVlrNf\").value;\n";
		echo "if (vlr_nf==undefined) {vlr_nf=\"\";}\n";
		echo "var data = document.getElementById(\"txData\").value;\n";
		echo "if (data==undefined) {data=\"\";}\n";
		echo "window.location.href=\"add_rent.php?cod_rent=".$_GET['cod_rent']."&dataped=\"+dataped+\"&cod_cli=\"+cod_cli+\"&cod_sp=\"+cod_sp+\"&quant=\"+quant+\"&vlr_nf=\"+vlr_nf+\"&data=\"+data;\n";
		echo "}\n";
	echo "</script>\n";
}
if ($_SERVER['REQUEST_METHOD'] != "POST") {
	$empresa = $_SESSION['UsuarioEmpresa'];
	$cod_rent = $_GET['cod_rent'];
	if ($cod_rent!="") {
		$query = $sql->query("select * from rentabilidade where ativo = 1 and empresa = $empresa and cod_rent = $cod_rent");
		$pesq = mysqli_fetch_array($query);
		if ($pesq['faturado']==1) $data = date("d/m/Y",strtotime($pesq['data_nf'])); else $data = "";
		$cod_cli = $pesq['cliente'];
		$cod_sp = $pesq['cod_sp'];
		$quant = str_replace(".",",",$pesq['quant']);
		$vlr_nf = number_format($pesq['vlr_nf'],4,",","");
		$dataped = date("d/m/Y",strtotime($pesq['data_ped']));
	} else {
		$data = $_GET['data'];
		$cod_cli = $_GET['cod_cli'];
		$cod_sp = $_GET['cod_sp'];
		$quant = $_GET['quant'];
		$vlr_nf = $_GET['vlr_nf'];
		$dataped = $_GET['dataped'];
	}
}

echo "<form action=\"".$_SERVER['REQUEST_URI']."\" method=\"post\" id=\"formquant\">";	
	echo "<input type=\"hidden\" name=\"codRentHidden\" id=\"codRentHidden\" value=\"$cod_rent\">";
	echo "<div class=\"divquantidade\">\n";
		echo "<table border=\"0px\" cellpadding=\"1px\" cellspacing=\"0px\" bgcolor=\"#fffff\" class=\"tablecomposicao\">\n";
			echo "<tr bgcolor=\"#EEEEEE\">\n";
				echo "<td width=150px><center><b>Data Pedido</td>\n";
				echo "<td width=150px><center><b>CLIENTE</td>\n";
				echo "<td width=150px><center><b>PRODUTO</td>\n";
				echo "<td width=150px><center><b>QUANTIDADE</td>\n";
				echo "<td width=205px><center><b>VALOR</td>\n";
				echo "<td width=150px class=\"colquant\"><center><b>Data Faturamento</td>\n";
			echo "</tr>\n";
			echo "<tr bgcolor=\"#FFFFFF\">\n";
				echo "<td><input type=\"text\" name=\"dataped\" id=\"txDataPed\" maxlength=\"10\" size=\"21\" style=\"width: 100%;\" OnKeyUp=\"mascaraData(this);\" value=\"".$dataped."\"/></td>\n";
				// echo "<td><input type=\"text\" name=\"cod_cli\" id=\"txCodCli\" maxlength=\"10\" size=\"21\"/></td>\n";
				echo "<td>";
					echo "<select name=\"cod_cli\" id=\"txCodCli\" style=\"width: 135px;\">";
						$clientesPesq = $sql->query("select cod_cli, cliente from clientes where empresa = ".$_SESSION['UsuarioEmpresa']." and ativo = 1 order by cliente");
						for ($c=1;$c<=$clientesPesq->num_rows;$c++) {
							$clientes = mysqli_fetch_array($clientesPesq);
							if ($cod_cli==$clientes['cod_cli']) echo "<option value=\"".$clientes['cod_cli']."\" selected=\"selected\">".$clientes['cliente']."</option>";
							else echo "<option value=\"".$clientes['cod_cli']."\">".$clientes['cliente']."</option>";
						}
					echo "</select>";
					echo "<a href=\"#\" onclick=\"abrirPopup('cadCli.php',400,300);\"><img src=\"images/new_file.jpg\" width=15px></a>";
				echo "</td>";
				// echo "<td><input type=\"text\" name=\"cod_sp\" id=\"txCodSp\" maxlength=\"10\" size=\"21\"/></td>\n";
				echo "<td>";
					echo "<select name=\"cod_sp\" id=\"txCodSp\" style=\"width: 135px;\">";
						$clientesPesq = $sql->query("select cod_sp, descricao from sp_dados where empresa = ".$_SESSION['UsuarioEmpresa']." and ativo = 1 order by cod_sp");
						for ($c=1;$c<=$clientesPesq->num_rows;$c++) {
							$clientes = mysqli_fetch_array($clientesPesq);
							if ($cod_sp==$clientes['cod_sp']) echo "<option value=\"".$clientes['cod_sp']."\" selected=\"selected\">".$clientes['cod_sp']." - ".$clientes['descricao']."</option>";
							else echo "<option value=\"".$clientes['cod_sp']."\">".$clientes['cod_sp']." - ".$clientes['descricao']."</option>";
						}
					echo "</select>";
					echo "<a href=\"#\" onclick=\"abrirPopup('cadsp.php',940,535);\"><img src=\"images/new_file.jpg\" width=15px></a>";
				echo "</td>";
				echo "<td><input type=\"text\" name=\"quant\" id=\"txQuant\" maxlength=\"20\" size=\"21\" style=\"width: 148px;\" value=\"".$quant."\"/></td>\n";
				echo "<td><input type=\"text\" name=\"vlr_nf\" id=\"txVlrNf\" maxlength=\"20\" size=\"30\" style=\"width: 203px;\" value=\"".$vlr_nf."\"/></td>\n";
				echo "<td class=\"colquant\">
						<input type=\"text\" name=\"data\" id=\"txData\" maxlength=\"10\" size=\"21\" style=\"width: 148px;\" OnKeyUp=\"mascaraData(this);\" value=\"".$data."\"/>
					</td>\n";
			echo "</tr>\n";
		echo "</table>\n";
	echo "</div>\n";
	echo "<div id=\"saldo\" style=\"position: absolute; left: 850px; width: 100px; display: none;\"><input type=\"checkbox\" name=\"saldo\" value=\"1\" id=\"saldoinput\">Gerar Saldo</div>";
	echo "<div style=\"margin-top: 5px; margin-bottom: -5px;\">\n<center>";
		if (!isset($_GET['cod_rent']) or $_GET['cod_rent']=="") echo "<input type=\"submit\" value=\"Adicionar\"/>";
		else echo "<input type=\"submit\" value=\"Alterar Item\"/>";
		echo "<input type=\"button\" value=\"Cancelar\" onclick=\"javascript:window.close()\"/>";
	echo "</div>\n";
echo "</form>";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$empresa = $_SESSION['UsuarioEmpresa'];
	if (!isset($_GET['cod_rent']) or $_GET['cod_rent']=="") {
		$cod_rentPesq = $sql->query("select cod_rent from rentabilidade where ativo = 1 and empresa = $empresa order by cod_rent desc");
		if ($cod_rentPesq->num_rows>0) {
			$cod_rentResult = $cod_rentPesq->fetch_array(MYSQLI_ASSOC);
			$cod_rent = $cod_rentResult['cod_rent'] + 1;
		} else $cod_rent = 0;
	} else $cod_rent = $_GET['cod_rent'];
	$data = date("Y-m-d",strtotime(str_replace("/","-",$_POST['data'])));
	$dataped = date("Y-m-d",strtotime(str_replace("/","-",$_POST['dataped'])));
	$cod_cli = $sql->real_escape_string($_POST['cod_cli']);
	$cod_sp = $sql->real_escape_string($_POST['cod_sp']);
	$quant = str_replace(",",".",$sql->real_escape_string($_POST['quant']));
	$vlr_nf = str_replace(",",".",$sql->real_escape_string($_POST['vlr_nf']));
	$usuario = $_SESSION['UsuarioID'];
	
	if (isset($_POST) and !empty($_POST['dataped']) and !empty($_POST['cod_cli']) and !empty($_POST['cod_sp']) and $_POST['quant']!="" and !empty($_POST['vlr_nf'])) {
		$ver_cliente = $sql->query("select cod_cli from clientes where ativo = 1 and empresa = $empresa and cod_cli = $cod_cli");
		if ($ver_cliente->num_rows!=0) {
			$ver_sp = $sql->query("select cod_sp from sp_dados where ativo = 1 and empresa = $empresa and cod_sp = $cod_sp");
			if ($ver_sp->num_rows!=0) {
				if ((!isset($_GET['cod_rent']) or $_GET['cod_rent']=="") and !empty($_POST['quant'])) {
					if ($_POST['data']!="") $sql->query("insert into rentabilidade (cod_rent, data_ped, faturado, data_nf, cliente, cod_sp, quant, vlr_nf, data_cri, data, usuario, empresa, ativo) values ('$cod_rent','$dataped',1,'$data','$cod_cli','$cod_sp','$quant','$vlr_nf',now(),now(),'$usuario','$empresa',1)") or die (mysqli_error($sql));
					else $sql->query("insert into rentabilidade (cod_rent, data_ped, faturado, data_nf, cliente, cod_sp, quant, vlr_nf, data_cri, data, usuario, empresa, ativo) values ('$cod_rent','$dataped',0,'$dataped','$cod_cli','$cod_sp','$quant','$vlr_nf',now(),now(),'$usuario','$empresa',1)") or die (mysqli_error($sql));
					echo "<script>atualizaPagMae(); alert('Item cadastrado!'); window.location.href=\"".$_SERVER['PHP_SELF']."\";</script>";
				} else if ((!isset($_GET['cod_rent']) or $_GET['cod_rent']=="") and empty($_POST['quant'])) {
					echo "<script>alert('Preencha todos os campos!!');</script>";
				} else if ($_GET['cod_rent']=="" or $_POST['quant']=="" or $_POST['quant']==0) {
					$sql->query("update rentabilidade set ativo = 0 where ativo = 1 and empresa = $empresa and cod_rent = $cod_rent") or die (mysqli_error($sql));
					echo "<script>atualizaPagMae(); alert('1 item zerado!'); window.close();</script>";
				} else {
					$data_criSql = mysqli_fetch_array($sql->query("select data_cri from rentabilidade where ativo = 1 and empresa = $empresa and cod_rent = $cod_rent"));
					$data_cri = $data_criSql['data_cri'];
					if ($_POST['data']!="") {
						//fatura material
						$quantOldQuery = $sql->query("select quant from rentabilidade where ativo = 1 and empresa = $empresa and cod_rent = $cod_rent") or die(mysqli_error($sql));
						if ($quantOldQuery->num_rows>0) {
							$quantOldFetch = mysqli_fetch_array($quantOldQuery);
							$quantOld = $quantOldFetch['quant'];
							$sql->query("update rentabilidade set ativo = 0 where ativo = 1 and empresa = $empresa and cod_rent = $cod_rent") or die (mysqli_error($sql));
							$sql->query("insert into rentabilidade (cod_rent, data_ped, faturado, data_nf, cliente, cod_sp, quant, vlr_nf, data_cri, data, usuario, empresa, ativo) values ('$cod_rent','$dataped',1,'$data','$cod_cli','$cod_sp','$quant','$vlr_nf','$data_cri',now(),'$usuario','$empresa',1)") or die (mysqli_error($sql));
							if ($_POST['saldo']=='1') {
								//gera saldo
								$newCod_rentPesq = $sql->query("select cod_rent from rentabilidade where ativo = 1 and empresa = $empresa order by cod_rent desc");
								if ($newCod_rentPesq->num_rows>0) {
									$newCod_rentResult = mysqli_fetch_array($newCod_rentPesq);
									$newCod_rent = $newCod_rentResult['cod_rent'] + 1;
								} else $newCod_rent = 0;
								$quantNew = $quantOld - $quant;
								$sql->query("insert into rentabilidade (cod_rent, data_ped, faturado, data_nf, cliente, cod_sp, quant, vlr_nf, data_cri, data, usuario, empresa, ativo) values ('$newCod_rent','$dataped',0,'$dataped','$cod_cli','$cod_sp','$quantNew','$vlr_nf','$data_cri',now(),'$usuario','$empresa',1)") or die (mysqli_error($sql));
							}
						}
					} else {
						//cancela faturamento
						$sql->query("update rentabilidade set ativo = 0 where ativo = 1 and empresa = $empresa and cod_rent = $cod_rent") or die (mysqli_error($sql));
						$sql->query("insert into rentabilidade (cod_rent, data_ped, faturado, data_nf, cliente, cod_sp, quant, vlr_nf, data_cri, data, usuario, empresa, ativo) values ('$cod_rent','$dataped',0,'$dataped','$cod_cli','$cod_sp','$quant','$vlr_nf','$data_cri',now(),'$usuario','$empresa',1)") or die (mysqli_error($sql));
					}
					echo "<script>atualizaPagMae(); alert('1 item foi alterado!'); window.close();</script>";
				}
			} else echo "<script>alert('Código de Sub Produto não localizado!');</script>";
		} else echo "<script>alert('Código de cliente não localizado!');</script>";
	} else echo "<script>alert('Preencha todos os campos!!');</script>";
}

?>
</body>
</html>