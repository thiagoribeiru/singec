<?
require_once("session.php");
// activar Error reporting
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
?>
<html>
	<title></title>
	<head>
		<!--<script src="jquery-1.8.2.js"></script>-->
		<script type="text/javascript">
			$(document).ready(function(){
				var horaIni = new Date();
				horaIni = Math.floor(horaIni.getTime()/1000);
				$(document).click(function(){
					var horaNow = new Date();
					horaNow = Math.floor(horaNow.getTime()/1000);
					var difer = horaNow-horaIni;
					horaIni = horaIni + difer;
					setCookie('sessao_val', (parseInt(getCookie("sessao_val"))+difer));
				});
				$("#selectAll").click(function(){
					var cont = 0;
					for (var i=0;i<document.formulario.elements.length;i++) {
					    var x = document.formulario.elements[i];
					    if (x.name == 'uidl[]') { 
							x.checked = document.formulario.selall.checked;
						} 
					}
					if (cont == 0){    
						var elem = document.getElementById("selectAll");
						elem.innerHTML = "Desmarcar todos";
						cont = 1;
					} else {
						var elem = document.getElementById("selectAll");
						elem.innerHTML = "Marcar todos";
						cont = 0;
					}
				});
				$("input:radio").change(function(){
					selecionaLinha(this);
				});
				function selecionaLinha(objeto) {
					var obj = objeto;
					var table = obj.closest("table");
					var tr = table.children;
					if (tr[0].tagName=="TBODY"||tr[0].tagName=="tbody") {
						tr = tr[0].children;
					}
					for(var i=0;i<tr.length;i++) {
						tr[i].style.background = "";
						var td = tr[i].children;
						td[td.length-1].innerHTML = "";
						td[td.length-2].style.background = "";
					}
					var linha = obj.closest("tr");
					var indice = obj.getAttribute("indice");
					var linhasIguais = document.getElementsByClassName(indice);
					for (var el=0;el<linhasIguais.length;el++) {
						linhasIguais[el].style.background = "#FF0000";
						if (obj!=linhasIguais[el].children[0].children[0]) {
							linhasIguais[el].children[0].children[0].disabled = "true";
						}
					}
					linha.style.background = "#FFFF00";
					if (parseFloat(obj.getAttribute("quantdest"))<parseFloat(obj.getAttribute("quantorig"))) {
						var coluna = linha.children;
						coluna[coluna.length-1].innerHTML = "<input type=\"checkbox\" value=\"1\" name=\"" + obj.getAttribute("indice") + "\" style=\"vertical-align: middle; margin: -2px 2px 0px 0px;\"\">Gerar Saldo de "+(parseFloat(obj.getAttribute("quantorig"))-parseFloat(obj.getAttribute("quantdest")))+"</input>";
					}
					if (parseFloat(obj.getAttribute("valordest"))!=parseFloat(obj.getAttribute("valororig"))) {
						var coluna = linha.children;
						coluna[coluna.length-2].style.background = "#FF0000";
					}
				}
				$("#tabelaOpcoes tbody td").click(function(){
					var celula = this;
					if (celula.className!="coluna_saldo") {
						var linha = $(celula).closest("tr");
						var radioLinha = $(linha).find("input:radio");
						if (!radioLinha[0].disabled) {
							radioLinha[0].checked = true;
							selecionaLinha(radioLinha[0]);
						}
					}
				});
			});
		</script>
		<style>
			.tabelaOpcoes tr:hover {
				background: #DCDCDC;
			}
		</style>
	</head>
	<body>
<?
// carregar a classe PHPExcel
require_once('plugins/Excel/reader.php');
if ($_SERVER['REQUEST_METHOD']!='POST' and !isset($_GET['pas'])) {
?>
	<form method="post" action="phpxls.php" enctype="multipart/form-data">
	  <label>Arquivo</label>
	  <input type="file" name="arquivo" />
	  
	  <input type="submit" value="Enviar" />
	</form>
<?
} else if ($_SERVER['REQUEST_METHOD']=='POST') {
	if (isset($_POST['pas']) and $_POST['pas']=='3') {
		// var_dump($_POST);
		if (isset($_POST['lins'])) {
			if (isset($_POST['lins'])) {
				$numLins = $_POST['lins'];
				for ($i=0;$i<=$numLins;$i++) {
					if (isset($_POST['linha'.$i])) {
						$linha = unserialize(urldecode($_POST['linha'.$i]));
						$indice = $linha['indice'];
						$stringSql = "select * from rentabilidade where indice = '".$indice."' and ativo = '1'";
						$query = $sql->query($stringSql) or die (mysqli_error($sql));
						if (mysqli_num_rows($query)==1) {
							$posicao = mysqli_fetch_array($query) or die (mysqli_error($sql));
							$cod_rent = $posicao['cod_rent'];
							$dataped = $posicao['data_ped'];
							$data = date("Y-m-d",strtotime(str_replace("/","-",$linha['dataFaturamento'])));
							$cod_cli = $posicao['cliente'];
							$cod_sp = $posicao['cod_sp'];
							$quantAnt = floatval($posicao['quant']);
							$quant = floatval($linha['quantidade']);
							$vlr_nfAnt = $posicao['vlr_nf'];
							$vlr_nf = $linha['valor'];
							$data_cri = $posicao['data_cri'];
							$usuario = $_SESSION['UsuarioID'];
							$empresa = $_SESSION['UsuarioEmpresa'];
							// var_dump($linha);
							$sql->query("update rentabilidade set ativo = '0' where indice = '".$indice."' and ativo = '1' and empresa = '".$empresa."'") or die(msqli_error($sql));
							$sql->query("insert into rentabilidade (cod_rent, data_ped, faturado, data_nf, cliente, cod_sp, quant, vlr_nf, data_cri, data, usuario, empresa, ativo) values ('$cod_rent','$dataped',1,'$data','$cod_cli','$cod_sp','$quant','$vlr_nf','$data_cri',now(),'$usuario','$empresa',1)") or die (mysqli_error($sql));
							if (isset($_POST[$indice]) and $_POST[$indice]=='1') {
								//gera saldo
								// echo "Saldo: ".($quantAnt-$quant)."<br>";
								$newCod_rentPesq = $sql->query("select cod_rent from rentabilidade where ativo = '1' and empresa = '".$empresa."' order by cod_rent desc");
								if ($newCod_rentPesq->num_rows>0) {
									$newCod_rentResult = mysqli_fetch_array($newCod_rentPesq);
									$newCod_rent = $newCod_rentResult['cod_rent'] + 1;
								} else $newCod_rent = 0;
								$sql->query("insert into rentabilidade (cod_rent, data_ped, faturado, data_nf, cliente, cod_sp, quant, vlr_nf, data_cri, data, usuario, empresa, ativo) values ('$newCod_rent','$dataped',0,'$data','$cod_cli','$cod_sp','".($quantAnt-$quant)."','$vlr_nfAnt','$data_cri',now(),'$usuario','$empresa',1)") or die (mysqli_error($sql));
							}
						} else {
							echo "O item referente a linha ".$i." do arquivo carregado, não teve a importação bem secedida. Favor tentar novamente.<br>";
						}
					}
				}
			} else {
				echo "Não recebido parâmetro de lins! Favor entrar em contato com o administrador do sistema!";
			}
		} else {
			echo "Não foi possivel receber as orientações para proceguir com a atualização da tabela de rentabilidade!";
		}
		exit;
	} else {
		// Pasta onde o arquivo vai ser salvo
		$_UP['pasta'] = 'temp/';
		// Tamanho máximo do arquivo (em Bytes)
		$_UP['tamanho'] = 1024 * 1024 * 2; // 2Mb
		// Array com as extensões permitidas
		$_UP['extensoes'] = array('xls');
		// Renomeia o arquivo? (Se true, o arquivo será salvo como .xls e um nome único)
		$_UP['renomeia'] = true;
		// Array com os tipos de erros de upload do PHP
		$_UP['erros'][0] = 'Não houve erro';
		$_UP['erros'][1] = 'O arquivo no upload é maior do que o limite do PHP';
		$_UP['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
		$_UP['erros'][3] = 'O upload do arquivo foi feito parcialmente';
		$_UP['erros'][4] = 'Não foi feito o upload do arquivo';
		// Verifica se houve algum erro com o upload. Se sim, exibe a mensagem do erro
		if ($_FILES['arquivo']['error'] != 0) {
		  die("Não foi possível fazer o upload, erro:" . $_UP['erros'][$_FILES['arquivo']['error']]);
		  exit; // Para a execução do script
		}
		// Caso script chegue a esse ponto, não houve erro com o upload e o PHP pode continuar
		// Faz a verificação da extensão do arquivo
		$extensao = strtolower(end(explode('.', $_FILES['arquivo']['name'])));
		if (array_search($extensao, $_UP['extensoes']) === false) {
		  echo "Por favor, envie arquivos com as seguintes extensões: xls";
		  exit;
		}
		// Faz a verificação do tamanho do arquivo
		if ($_UP['tamanho'] < $_FILES['arquivo']['size']) {
		  echo "O arquivo enviado é muito grande, envie arquivos de até 2Mb.";
		  exit;
		}
		// O arquivo passou em todas as verificações, hora de tentar movê-lo para a pasta
		// Primeiro verifica se deve trocar o nome do arquivo
		if ($_UP['renomeia'] == true) {
		  // Cria um nome baseado no UNIX TIMESTAMP atual e com extensão .xls
		  $nome_final = md5(time()).'.xls';
		} else {
		  // Mantém o nome original do arquivo
		  $nome_final = $_FILES['arquivo']['name'];
		}
		  
		// Depois verifica se é possível mover o arquivo para a pasta escolhida
		if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'] . $nome_final)) {
		  // Upload efetuado com sucesso, exibe uma mensagem e um link para o arquivo
		  //echo "Upload efetuado com sucesso!";
		  //echo '<a href="' . $_UP['pasta'] . $nome_final . '">Clique aqui para acessar o arquivo</a><br>';
		  //echo "<br>".$_UP['pasta'] . $nome_final;
		  echo "Upload efetuado com sucesso! Aguarde o redirecionamento...";
		  echo "<script>location.href=\"phpxls.php?pas=1&aq=".urlencode($nome_final)."\";</script>";
		}  else {
		  // Não foi possível fazer o upload, provavelmente a pasta está incorreta
		  echo "Não foi possível enviar o arquivo, tente novamente";
		  exit;
		}
	}
} else if($_SERVER['REQUEST_METHOD']=='GET') {
	if ($_GET['pas']=='1') {
		//processo de montagem do array
		$data = new Spreadsheet_Excel_Reader();
		$data->setOutputEncoding('UTF-8');
		
		// if(!empty($_FILES['upfile']) && $_FILES['upfile']['type'] == "application/vnd.ms-excel") {
		// 	$data->read($_FILES['upfile']['tmp_name']);
		// }
		// else {
			$data->read('temp/'.urldecode($_GET['aq']));
		// }
		
		echo "<form name=\"formulario\" method=\"GET\" \">";
		echo "<input type=\"hidden\" name=\"pas\" value=\"2\" />";
		echo "<input type=\"hidden\" name=\"aq\" value=\"".urlencode(urldecode($_GET['aq']))."\" />";
		echo "<input type=\"hidden\" name=\"cols\" value=\"".$data->sheets[0]['numCols']."\" />";
		echo "<center><input type=\"submit\" value=\"Enviar dados >>\" /></center>";
		echo "<table border=\"0\" style=\"font-size: 10px; border-collapse: collapse;\">";
			for ($i = 0; $i <= $data->sheets[0]['numRows']; $i++) {
				if ($i==0) {
					echo "<tr>";
						for ($j = 0; $j <= $data->sheets[0]['numCols']; $j++) {
							if ($j==0) {
								echo "<td style=\"border: solid 1px;\"><input type=\"checkbox\" id=\"selectAll\" name=\"selall\" /></td>";
							} else {
								echo "<td style=\"border: solid 1px;\">";
									echo "<select name=\"coluna".$j."\">";
										echo "<option value=\"nenhum\" selected></option>";
										// echo "<option value=\"dataPedido\">Data Pedido</option>";
										// echo "<option value=\"cliente\">Cliente</option>";
										echo "<option value=\"produto\">Produto</option>";
										echo "<option value=\"quantidade\">Quantidade</option>";
										echo "<option value=\"valor\">Valor</option>";
										echo "<option value=\"dataFaturamento\">Data Faturamento</option>";
									echo "</select>";
								echo "</td>";
							}
						}
					echo "</tr>";
				} else {
					echo "<tr>";
						for ($j = 0; $j <= $data->sheets[0]['numCols']; $j++) {
							if ($j==0) {
								echo "<td style=\"border: solid 1px;\"><input type=\"checkbox\" name=\"uidl[]\" value=\"$i\" /></td>";
							} else {
								$celldata = utf8_encode((!empty($data->sheets[0]['cells'][$i][$j])) ? $data->sheets[0]['cells'][$i][$j] : "&nbsp;");
								echo "<td style=\"border: solid 1px;\">$celldata</td>";
							}
						}
					echo "</tr>";
				}
			}
		echo "</table>";
		echo "</form>";
		exit;
	} else if ($_GET['pas']=='2') {
		// var_dump($_GET);
		$numCols = $_GET['cols'];
		$arrayCols = array();
		$colunaArr = array();
		$contCol = 1;
		for ($c=1;$c<=$numCols;$c++) {
			if ($_GET['coluna'.$c]!='nenhum') {
				if (!isset($arrayCols[$_GET['coluna'.$c]])) {
					if ($_GET['coluna'.$c]=='dataPedido') $arrayCols['dataPedido'] = $c;
					else if ($_GET['coluna'.$c]=='cliente') $arrayCols['cliente'] = $c;
					else if ($_GET['coluna'.$c]=='produto') $arrayCols['produto'] = $c;
					else if ($_GET['coluna'.$c]=='quantidade') $arrayCols['quantidade'] = $c;
					else if ($_GET['coluna'.$c]=='valor') $arrayCols['valor'] = $c;
					else if ($_GET['coluna'.$c]=='dataFaturamento') $arrayCols['dataFaturamento'] = $c;
					$colunaArr[$contCol] = $_GET['coluna'.$c];
					$contCol++;
				} else {
					echo "colunas duplicadas!";
					exit;
				}
			}
		}
		if (count($arrayCols)==0) {
			echo "Nenhuma coluna selecionada!";
			exit;
		}
		// var_dump($arrayCols);
		// var_dump($colunaArr);
		// exit;
		$data = new Spreadsheet_Excel_Reader();
		$data->setOutputEncoding('UTF-8');
		$data->read('temp/'.urldecode($_GET['aq']));
		$celula = array();
		// echo "<table border=\"0\" style=\"font-size: 10px; border-collapse: collapse;\">";
		for ($i=0;$i<count($_GET['uidl']);$i++) {
			$linha = $_GET['uidl'][$i];
			// echo "<tr>";
			$celula[$i][0] = $linha;
			$celula[$i]['linha'] = $linha;
			for ($j=1;$j<=count($colunaArr);$j++) {
				$coluna = $colunaArr[$j];
				$celldata = utf8_encode((!empty($data->sheets[0]['cells'][$linha][$arrayCols[$coluna]])) ? $data->sheets[0]['cells'][$linha][$arrayCols[$coluna]] : "&nbsp;");
				// echo "<td style=\"border: solid 1px;\">$celldata</td>";
				$celula[$i][$j-1] = $celldata;
				$celula[$i][$coluna] = $celldata;
			}
			// echo "</tr>";
		}
		// echo "</table>";
		if (count($celula)>0) {
			// var_dump($celula);
			//SELECT * FROM `rentabilidade` where faturado = '0' and ativo = '1' and empresa = '3' and cliente = (SELECT cod_cli FROM `clientes` where cliente like '%luiza barcelos%' and ativo = '1' and empresa = '3') and cod_sp = '1095'
			echo "<form action=\"phpxls.php\" method=\"POST\" id=\"tabelaOpcoes\" >";
			echo "<input type=\"hidden\" name=\"pas\" value=\"3\" />";
			echo "<input type=\"hidden\" name=\"aq\" value=\"".urlencode(urldecode($_GET['aq']))."\" />";
			echo "<input type=\"hidden\" name=\"cols\" value=\"".$data->sheets[0]['numCols']."\" />";
			for ($i=0;$i<count($celula);$i++) {
				$filtro = "SELECT indice, 
				data_ped, 
				(select cliente from clientes where ativo = '1' and empresa = '".$_SESSION['UsuarioEmpresa']."' and cod_cli = rentabilidade.cliente) as cliente, 
				(select regiao from regioes where ativo = '1' and empresa = '".$_SESSION['UsuarioEmpresa']."' and cod_reg = (select regiao from clientes where ativo = '1' and empresa = '".$_SESSION['UsuarioEmpresa']."' and cod_cli = rentabilidade.cliente)) as regiao, 
				(select nome from representantes where ativo = '1' and empresa = '".$_SESSION['UsuarioEmpresa']."' and cod_rep = (select representante from clientes where ativo = '1' and empresa = '".$_SESSION['UsuarioEmpresa']."' and cod_cli = rentabilidade.cliente)) as representante, 
				cod_sp, 
				(select descricao from sp_dados where ativo = '1' and empresa = '".$_SESSION['UsuarioEmpresa']."' and cod_sp = rentabilidade.cod_sp) as descricao, 
				quant, 
				(select unidade from sp_dados where ativo = '1' and empresa = '".$_SESSION['UsuarioEmpresa']."' and cod_sp = rentabilidade.cod_sp) as unidade, 
				vlr_nf FROM rentabilidade where faturado = '0' and ativo = '1' and empresa = '".$_SESSION['UsuarioEmpresa']."'";
				// if ($celula['dataPedido'])
				if (isset($celula[$i]['cliente'])) {
					$filtro .= " and cliente = (SELECT cod_cli FROM clientes where cliente like '%".$celula[$i]['cliente']."%' and ativo = '1' and empresa = '3')";
				}
				if (isset($celula[$i]['produto'])) {
					$filtro .= " and cod_sp = '".$celula[$i]['produto']."'";
				}
				if (isset($celula[$i]['dataFaturamento'])) {
					$filtro .= " and data_ped <= '".date("Y-m-d",strtotime(str_replace("/","-",$celula[$i]['dataFaturamento'])))."'";
				}
				$pesquisa = $sql->query($filtro);
				if (mysqli_num_rows($pesquisa)>0) {
					echo "<table style=\"border-collapse: collapse; font-size: 12px;\"><tr>";
					for ($m = 0; $m <= $data->sheets[0]['numCols']; $m++) {
						if ($m==0) {
							echo "<td style=\"border: 1px solid; padding: 5px;\"><b>linha: ".$celula[$i]['linha']."</b></td>";
						} else {
							$celldata = utf8_encode((!empty($data->sheets[0]['cells'][$celula[$i]['linha']][$m])) ? $data->sheets[0]['cells'][$celula[$i]['linha']][$m] : "&nbsp;");
							echo "<td style=\"border: 1px solid; padding: 5px;\"><b>".$celldata."</b></td>";
						}
					}
					echo "</tr></table>";
					echo "<table style=\"border-collapse: collapse; font-size: 10px;\" class=\"tabelaOpcoes\">";
					for ($j=0;$j<mysqli_num_rows($pesquisa);$j++) {
						$resultado = mysqli_fetch_array($pesquisa);
						echo "<tr id=\"".$resultado['indice']."\" class=\"".$resultado['indice']."\">";
						$numLinhas = 0;
						for ($l=0;$l<=mysqli_num_fields($pesquisa);$l++) {
							if ($l==0) {
								echo "<td style=\"border: 1px solid;\"><input type=\"radio\" indice=\"".$resultado[$l]."\" name=\"linha".$celula[$i]['linha']."\" value=\"".urlencode(serialize(array('indice'=>$resultado[$l],'dataFaturamento'=>$celula[$i]['dataFaturamento'],'valor'=>$celula[$i]['valor'],'quantidade'=>$celula[$i]['quantidade'])))."\" quantdest=\"".$celula[$i]['quantidade']."\" quantorig=\"".$resultado['quant']."\" valordest=\"".$celula[$i]['valor']."\" valororig=\"".$resultado['vlr_nf']."\" /></td>";
							} else if($l==mysqli_num_fields($pesquisa)) {
								echo "<td style=\"border: 0px solid;\" class=\"coluna_saldo\" ></td>";
							} else {
							    if ($resultado[$l]==$resultado['data_ped']) {
							        echo "<td style=\"border: 1px solid; padding: 0 10 0 10;\">".date("d/m/Y",strtotime($resultado[$l]))."</td>";
							    } else {
								    echo "<td style=\"border: 1px solid; padding: 0 10 0 10;\">".$resultado[$l]."</td>";
							    }
							}
							$numLinhas = $celula[$i]['linha'];
						}
						echo "</tr>";
					}
					echo "</table>";
				} else {
					echo "Nenhum registro!<br>";
				}
				echo "<br><br>";
			}
			echo "<input type=\"hidden\" name=\"lins\" value=\"".$numLinhas."\" />";
			echo "<input type=\"submit\" value=\"Enviar Dados >>\" />";
			echo "</form>";
			exit;
		} else {
			echo "Empty!";
			exit;
		}
		exit;
	} else {
		exit;
	}
}

?>

	</body>
</html>