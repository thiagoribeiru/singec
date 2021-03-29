<?require_once("session.php");?>
<html>
<head>
	<title><?echo $title;?></title>
	<link rel="stylesheet" href="jquery-ui.css" />
    <!--<script src="jquery-1.8.2.js"></script>-->
    <script src="jquery-ui.js"></script> 
    <script type="text/javascript" src="js/serialize-0.2.js"></script>
	<script language="JavaScript" type="text/javascript">
		var emailArray = "";
		var emailOrig = "";
		var horaArray = "";
		var horaOrig = "";
		var minutoArray = "";
		var minutoOrig = "";
		function valoresOriginais() {
			//pega valores originais
			emailArray = document.getElementsByClassName("entradas_ult_24h");
			emailOrig = new Array();
			for (var e=0;e<emailArray.length;e++) {
				emailOrig[e] = emailArray[e].value;
			}
			horaArray = document.getElementsByClassName("ent_24h_hora");
			horaOrig = new Array();
			for (var h=0;h<horaArray.length;h++) {
				horaOrig[h] = horaArray[h].value;
			}
			minutoArray = document.getElementsByClassName("ent_24h_minuto");
			minutoOrig = new Array();
			for (var m=0;m<minutoArray.length;m++) {
				minutoOrig[m] = minutoArray[m].value;
			}
		}
		function botao_ent_24h(idForm) {
			var valor2 = $("#ent_24h_hora").val();
			var valor3 = $("#ent_24h_minuto").val();
			var difs = 0;
			for (var i=0;i<emailOrig.length;i++) {
				if (emailArray[i].value!=emailOrig[i]) {
					difs++;
				}
			}
			if (difs>0) {
				//pegar elementos pelo tipo button
				$("#"+idForm+" button").attr('disabled',false);
			} else {
				$("#"+idForm+" button").attr('disabled',true);
			}
		}
		function enviaForm(obj,pagina) {
			if (validaTodos()) {
				var form = obj.closest('form');
				var dados = serialize(form);
				var camposEmail = document.getElementsByClassName("entradas_ult_24h");
				var stringEmail = "";
				for (var i=0;i<camposEmail.length;i++) {
					if (camposEmail[i].value!="") {
						if (stringEmail!="") {
							stringEmail = stringEmail + ", ";
						}
						stringEmail = stringEmail + camposEmail[i].value;
					}
				}
				
				// alert(dados);
				$.ajax({
	                method: "GET",
	                url: pagina,
	                data: dados + "&stringEmail=" + stringEmail,
	                dataType: "json",
	                beforeSend: function(){
	                    $('#fundo_fumace').show();
	                },
	                success: function(json){
	                	if (json.error>0) {
	                		alert(json.mensagem);
	                		$('#fundo_fumace').hide();
	                	} else {
	                		$("#ent_24h button").attr('disabled',true);
	                		valoresOriginais();
	                		$('#fundo_fumace').hide();
	                	}
	                },
	                error: function(XMLHttpRequest, textStatus, errorThrown){
	                	alert("erro!");
	                	$('#fundo_fumace').hide();
						// for(i in XMLHttpRequest) {
							// if (i!="channel")
								// document.write(i + ":" + XMLHttpRequest[i] + "<br>");
						// }
	                }
	            });
			}
			return false;
		}
		function limite(form,limite) {
		   var form = form;
		   if( form.value.length >= limite ) {
		       form.value = form.value.substring( 0, limite );
		   }
		}
		function ativaCron() {
			var confirma = confirm("As Tarefas Cron deste sistema utilizam um recurso de loop infinito de baixo processamento no servidor. Tem certeza que deseja ativa-las?");
			if (confirma) {
				$.ajax({
					method: "GET",
					url: 'ativaCron.php',
					data: '',
					dataType: "html",
					beforeSend: function(){
						$('#fundo_fumace').show();
					},
					success: function(){
						var popup = window.open("cronJobs.php",'',"toolbar=no,status=no,menubar=no,scrollbars=no,resizable=no,left=10000,top=10000,width=10,height=10,visible=none");
						popup.blur();
						sleep(10);
						$('#atvCron').attr('disabled','disabled');
						popup.close();
						$('#fundo_fumace').hide();
					},
					error: function(XMLHttpRequest, textStatus, errorThrown){
						alert("erro!");
						$('#fundo_fumace').hide();
						// for(i in XMLHttpRequest) {
						// if (i!="channel")
						// document.write(i + ":" + XMLHttpRequest[i] + "<br>");
						// }
					}
				});
				// abrirPopup('cronJobss.php',100,100);
			}
			return false;
		}
		function validacaoEmail(field) {
			usuario = field.value.substring(0, field.value.indexOf("@"));
			dominio = field.value.substring(field.value.indexOf("@")+ 1, field.value.length);
			
			if (((usuario.length >=1) &&
			    (dominio.length >=3) && 
			    (usuario.search("@")==-1) && 
			    (dominio.search("@")==-1) &&
			    (field.value.search(",")==-1) &&
			    (field.value.search(";")==-1) &&
			    (usuario.search(" ")==-1) && 
			    (dominio.search(" ")==-1) &&
			    (dominio.search(".")!=-1) &&      
			    (dominio.indexOf(".") >=1) && 
			    (dominio.lastIndexOf(".") < dominio.length - 1)) ||
			    field.value=="") {
				// field.style.background = "";
				var divPai = field.closest("div");
				$(divPai).children("div").children("img").attr("src","images/yes.png");
				$(divPai).children("div").children("img").css("margin","3px 0px 0px 3px");
				if (field.value=="") {
					$(divPai).children("div").hide();
					return true;
				} else {
					$(divPai).children("div").show();
					return true;
				}
			}
			else{
				// field.style.background = "#FF0000";
				var divPai = field.closest("div");
				$(divPai).children("div").children("img").attr("src","images/no.png");
				$(divPai).children("div").children("img").css("margin","7px 0px 0px 3px");
				$(divPai).children("div").show();
				return false;
			}
		}
		function validaTodos() {
			var inputs = document.getElementsByClassName("entradas_ult_24h");
			var falses = 0;
			for (var inp=0;inp<inputs.length;inp++) {
				if (!validacaoEmail(inputs[inp])) {
					falses++;
				}
			}
			if (falses>0) {
				return false;
			} else {
				return true;
			}
		}
		$(document).ready(function(){
			valoresOriginais();
			validaTodos();
			$(".entradas_ult_24h").keyup(function(){
				var idForm = $(this).closest("form").attr("id");
				botao_ent_24h(idForm);
			});
			$(".ent_24h_hora").change(function(){
				var idForm = $(this).closest("form").attr("id");
				botao_ent_24h(idForm);
			});
			$(".ent_24h_minuto").change(function(){
				var idForm = $(this).closest("form").attr("id");
				botao_ent_24h(idForm);
			});
		});
	</script>
</head>
<body onunload="window.opener.location.reload();">
<?
//validação página
autorizaPagina(2);

//painel boas vindas
require_once("welcome.php");
echo "<div id=\"divmenu\">\n";
	require_once("menu.php");
echo "\n</div>";
?>
<!--<div id="fundo_fumace">
	<div id="load_img">
		<img src="images/3.png"><br>
		<span>Carregando...aguarde!</span>
	</div>
</div>-->
<?
echo "<div class=\"tabelacorpo\">\n";
	$empresa = $_SESSION['UsuarioEmpresa'];
	if ($empresa!=1) $buscaEmp = " and id_empresa = '$empresa'"; else $buscaEmp = "";
	$dtc = "ativo = '1' $buscaEmp";
	$empresaPesq = $sql->query("select id_empresa, nome, cnpj from empresas where id_empresa > 0 $buscaEmp order by nome") or die (mysqli_error($sql));
	if (mysqli_num_rows($empresaPesq)>1) {
		echo "<form>";
			echo "<label for=\"empresas\"><b>Selecione a empresa: </b></label>";
			echo "<select name=\"empresas\" id=\"empresas\">";
			for ($i=1;mysqli_num_rows($empresaPesq)>=$i;$i++) {
				$emp = mysqli_fetch_array($empresaPesq);
				if ($emp['id_empresa']!=1) echo "<option value=\"".$emp['cnpj']."\">".$emp['nome']." - ".$emp['cnpj']."</option>";
				$vetEmp[$i-1]['id_empresa'] = $emp['id_empresa'];
				$vetEmp[$i-1]['nome'] = $emp['nome'];
				$vetEmp[$i-1]['cnpj'] = $emp['cnpj'];
				$display = 'none';
			}
			echo "</select>";
			// $cronPesq = $sql->query("select * from processoscron");
			// if ($cronPesq->num_rows>0) $disabled = "disabled";
			// else $disabled = "";
			// echo "<button id=\"atvCron\" onclick=\"return ativaCron();\" $disabled>Ativar Tarefas Cron</button>";
		echo "</form>";
	} else {
		$emp = mysqli_fetch_array($empresaPesq);
		$vetEmp[0]['id_empresa'] = $emp['id_empresa'];
		$vetEmp[0]['nome'] = $emp['nome'];
		$vetEmp[0]['cnpj'] = $emp['cnpj'];
		$display = 'block';
	}
	
	for ($i=0;$i<count($vetEmp);$i++) {
		if ($vetEmp[$i]['id_empresa']!=1) {
			echo "<div id=\"".$vetEmp[$i]['cnpj']."\" style=\"display: $display; position: relative; width: 600px;\">";
				?>
				<fieldset style="position: relative;">
					<legend><b>E-mails automáticos</b></legend>
					<div style="position: relative; display: table;">
						<form id=<?echo "\"ent_24h".$vetEmp[$i]['cnpj']."\"";?>>
						<?$pesq = $sql->query("select * from emails_automaticos where ativo = '1' and empresa = '".$vetEmp[$i]['id_empresa']."' and tipo = 'entradas_ult_24h'") or die(mysqli_error($sql));
						if (mysqli_num_rows($pesq)>0) $item = mysqli_fetch_array($pesq);
						else $item = array();?>
						<div style="position: relative; float: left; width: 150px; text-align: right;"><label for="entradas_ult_24h">Entradas nas últimas 24h:</label></div>
						<div style="position: relative; float: left; width: 410px; margin-left: 10px;">
							<? $numeroDeEmails = 4;
							$emailsArray = explode(", ",$item['email']);
							for ($em=0;$em<$numeroDeEmails;$em++) {?>
							<div style="position: relative; display: table;">
								<input type="text" id="entradas_ult_24h" class="entradas_ult_24h" name="entradas_ult_24h" onblur="validacaoEmail(this)"  maxlength="50" size="50" style="resize: none; float: left; width: 390px; height: 23px;" placeholder="Insira um email válido" value="<?echo $emailsArray[$em];?>"/>
								<div style="height: 23px; float: left; display: none;">
									<img src="images/yes.png" style="width: 15px; margin: 3px 0px 0px 3px;">
								</div>
							</div>
							<?}?>
							<div style="position: relative; margin-top: 5px;">
								<label for="hora">A partir de: (hora:minuto):</label>
								<select name="hora" id="ent_24h_hora" class="ent_24h_hora">
									<?
									for ($h=0;$h<24;$h++) {
										if ($item['hora']==$h) $selec1 = "selected='selected'"; else $selec1 = "";
										if ($h<10) echo "<option value=\"0$h\" $selec1>0$h</option>\n";
										else echo "<option value=\"$h\" $selec1>$h</option>\n";
									}
									?>
								</select> :
								<select name="minuto" id="ent_24h_minuto" class="ent_24h_minuto">
									<?
									for ($m=0;$m<60;$m+=10) {
										if ($item['minuto']==$m) $selec2 = "selected='selected'"; else $selec2 = "";
										if ($m<10) echo "<option value=\"0$m\" $selec2>0$m</option>\n";
										else echo "<option value=\"$m\" $selec2>$m</option>\n";
									}
									?>
								</select>
								<input type="hidden" name="relatorio" value="entradas_ult_24h" />
								<button style="float: right; margin-right: 20px;" onclick="return enviaForm(this,'cadEmail.php')" disabled=true>Salvar</button>
							</div>
						</div>
						</form>
					</div>
				</fieldset>
				<?
			echo "</div>";
		}
	}
	
	//formação do script de exibição das divs
	if (count($vetEmp)>1) {
		echo "<script type=\"text/javascript\">\n";
		echo "window.onload = function(){\n";
			echo "id('empresas').onchange = function(){\n";
				for ($i=0;$i<count($vetEmp);$i++) {
					if ($vetEmp[$i]['id_empresa']!=1) {
						echo "if( this.value==".$vetEmp[$i]['cnpj']." )\n";
							echo "id('".$vetEmp[$i]['cnpj']."').style.display = 'block';\n";
						echo "if( this.value!=".$vetEmp[$i]['cnpj']." )\n";
							echo "id('".$vetEmp[$i]['cnpj']."').style.display = 'none';\n";
					}
				}
			echo "}\n";
		echo "}\n";
		echo "function id( el ){\n";
		    echo "return document.getElementById( el );\n";
		echo "}\n";
		echo "</script>\n";
	}
	
echo "</div>\n";
?>
</body>
</html>