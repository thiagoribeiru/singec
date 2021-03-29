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
		var horaOrig = "";
		var minutoOrig = "";
		function valoresOriginais() {
			//pega valores originais
			emailArray = document.getElementsByClassName("entradas_ult_24h");
			emailOrig = new Array();
			for (var e=0;e<emailArray.length;e++) {
				emailOrig[e] = emailArray[e].value;
			}
			horaOrig = document.getElementById("ent_24h_hora").value;
			minutoOrig = document.getElementById("ent_24h_minuto").value;
		}
		function botao_ent_24h() {
			var horaNew = $("#ent_24h_hora").val();
			var minutoNew = $("#ent_24h_minuto").val();
			var difs = 0;
			for (var i=0;i<emailOrig.length;i++) {
				if (emailArray[i].value!=emailOrig[i]) {
					difs++;
				}
			}
			if (difs>0 || horaOrig!=horaNew || minutoOrig!=minutoNew) {
				//pegar elementos pelo tipo button
				$("#ent_24h button").attr('disabled',false);
			} else {
				$("#ent_24h button").attr('disabled',true);
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
		function preencheCampos() {
			var cnpj = document.getElementById("empresas").value;
			var dadosEmpresa = {
				'funcao':'pegaIdEmpresa',
				'cnpj':cnpj
			};
			var idEmpresa = 0;
			$.ajax({
				type: 'POST',
				url: 'funcoesAjax.php',
				dataType: "json",
				data: dadosEmpresa,
				beforeSend: function(){
					$('#fundo_fumace').show();
				},
				success: function(jsonId){
					idEmpresa = jsonId.id_empresa;
					var dadosJson = {
						'funcao':'preencheEmailsAutomaticos',
						'cnpj':cnpj
					};
					$.ajax({
						type: 'POST',
						url: 'funcoesAjax.php',
						dataType: "json",
						data: dadosJson,
						beforeSend: function(){
							$('#fundo_fumace').show();
						},
						success: function(json){
							if (json.retorno) {
								var array = JSON.parse(json.array);
								var email = array.email.split(", ");
								var input = document.getElementsByClassName("entradas_ult_24h");
								for (var ems=0;ems<email.length;ems++) {
									input[ems].value = email[ems];
								}
								var hora = document.getElementById("ent_24h_hora");
								for (var i=0;i<hora.options.length;i++) {
									if (parseInt(hora.options[i].value)==array.hora) {
										hora.options[i].selected = "true";
										break;
									}
								}
								var minuto = document.getElementById("ent_24h_minuto");
								for (var i=0;i<minuto.options.length;i++) {
									if (parseInt(minuto.options[i].value)==array.minuto) {
										minuto.options[i].selected = "true";
										break;
									}
								}
								var imputEmp = document.getElementById("id_empresa");
								imputEmp.value = array.empresa;
								valoresOriginais();
								validaTodos();
							} else {
								var input = document.getElementsByClassName("entradas_ult_24h");
								for (var ems=0;ems<input.length;ems++) {
									input[ems].value = "";
								}
								var hora = document.getElementById("ent_24h_hora");
								hora.options[0].selected = "true";
								var minuto = document.getElementById("ent_24h_minuto");
								minuto.options[0].selected = "true";
								var imputEmp = document.getElementById("id_empresa");
								imputEmp.value = idEmpresa;
								valoresOriginais();
								validaTodos();
							}
							$('#fundo_fumace').hide();
						},
						error: function(XMLHttpRequest, textStatus, errorThrown){
							alert("erro!");
							$('#fundo_fumace').hide();
						}
					});
				},
				error: function(XMLHttpRequest, textStatus, errorThrown){
					alert("erro!");
					$('#fundo_fumace').hide();
				}
			});
		}
		$(document).ready(function(){
			preencheCampos();
			valoresOriginais();
			validaTodos();
			$(".entradas_ult_24h").keyup(function(){
				botao_ent_24h();
			});
			$("#ent_24h_hora").change(function(){
				botao_ent_24h();
			});
			$("#ent_24h_minuto").change(function(){
				botao_ent_24h();
			});
			$("#empresas").change(function(){
				preencheCampos();
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
			}
			echo "</select>";
		echo "</form>";
	} else {
		$emp = mysqli_fetch_array($empresaPesq);
		echo "<form>";
			echo "<input type=\"hidden\" name=\"empresas\" id=\"empresas\" value=\"".$emp['cnpj']."\">";
		echo "</form>";
	}
	
	echo "<div style=\"display: $display; position: relative; width: 600px;\">";
		?>
		<fieldset style="position: relative;">
			<legend><b>E-mails automáticos</b></legend>
			<div style="position: relative; display: table;">
				<form id="ent_24h">
				<div style="position: relative; float: left; width: 150px; text-align: right;"><label for="entradas_ult_24h">Entradas nas últimas 24h:</label></div>
				<div style="position: relative; float: left; width: 410px; margin-left: 10px;">
					<? $numeroDeEmails = 4;
					for ($em=0;$em<$numeroDeEmails;$em++) {?>
					<div style="position: relative; display: table;">
						<input type="text" id="entradas_ult_24h" class="entradas_ult_24h" name="entradas_ult_24h" onblur="validacaoEmail(this)"  maxlength="50" size="50" style="resize: none; float: left; width: 390px; height: 23px;" placeholder="Insira um email válido" />
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
								if ($h<10) echo "<option value=\"0$h\">0$h</option>\n";
								else echo "<option value=\"$h\">$h</option>\n";
							}
							?>
						</select> :
						<select name="minuto" id="ent_24h_minuto" class="ent_24h_minuto">
							<?
							for ($m=0;$m<60;$m+=5) {
								if ($m<10) echo "<option value=\"0$m\">0$m</option>\n";
								else echo "<option value=\"$m\">$m</option>\n";
							}
							?>
						</select>
						<input type="hidden" name="relatorio" value="entradas_ult_24h" />
						<input type="hidden" name="id_empresa" id="id_empresa" value="" />
						<button style="float: right; margin-right: 20px;" onclick="return enviaForm(this,'cadEmail.php')" disabled=true>Salvar</button>
					</div>
				</div>
				</form>
			</div>
		</fieldset>
		<?
	echo "</div>";
	
echo "</div>\n";
?>
</body>
</html>