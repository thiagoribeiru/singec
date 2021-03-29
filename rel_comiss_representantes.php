<?
require_once("session.php");
?>
<html>
	<head>
		<link rel="stylesheet" href="jquery-ui.css"/>
	    <script src="jquery-ui.js"></script>
	    <title><?echo $title;?></title>
	    <style>
	    	fieldset {
	    		max-width: 350px;
	    		width: 350px;
	    	}
	    	.data {
	    		width: 120px;
	    	}
	    	#reps {
	    		display: none;
	    	}
	    	#esquerda {
	    		/*background: yellow;*/
	    		display: table-cell;
	    		float: left;
	    		width: 400px;
	    		max-height: 80%;
	    		overflow: auto;
	    	}
	    	#direita {
	    		float: left;
	    		/*background: green;*/
	    		width: 900px;
	    		height: 500px;
	    	}
	    </style>
	    <script language="JavaScript" type="text/javascript">
	    	$(document).ready(function(){
				// if ( $('[type="date"]').prop('type') != 'date' ) {
					// $('[type="date"]').datepicker();
				// }
				$('#dataDe').datepicker({
					dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado','Domingo'],
			        dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
			        dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
			        monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
			        monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
			    	// showOn: "button",
			    	showOn: "both",
			        buttonImage: "images/icon-calendario11x11.png",
			        buttonImageOnly: true, 
					showButtonPanel: true,
					currentText: "Hoje",
					closeText: "Fechar",
					dateFormat: 'dd/mm/yy',
					changeMonth: true,
			        changeYear: true,
					// showOtherMonths: true,
			        // selectOtherMonths: true,
					// numberOfMonths: 3,
					altFormat: "yy-mm-dd",
					altField: "#altdataDe",
				});
				$('#dataAte').datepicker({
					dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado','Domingo'],
			        dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
			        dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
			        monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
			        monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
			    	// showOn: "button",
			    	showOn: "both",
			        buttonImage: "images/icon-calendario11x11.png",
			        buttonImageOnly: true, 
					showButtonPanel: true,
					currentText: "Hoje",
					closeText: "Fechar",
					dateFormat: 'dd/mm/yy',
					changeMonth: true,
			        changeYear: true,
					// showOtherMonths: true,
			        // selectOtherMonths: true,
					// numberOfMonths: 3,
					altFormat: "yy-mm-dd",
					altField: "#altdataAte",
				});
				$('.data').keydown(function(){
					var campo = this;
					if (event.keyCode=='8'||event.keyCode=='46') {
						campo.value = '';
					} else {
						return false;
					}
				});
				$('.data').click(function(){
					this.select();
				});
				$('#all').click(function(){
					var check = this;
					if($(check).prop( "checked" ) == false){
						$('#reps').show("slow");
						$(check).prop( "checked" , false);
					} else {
						$('#reps').hide("slow");
						$(document).find('input[type=checkbox]').prop('checked', false);
						$(check).prop( "checked" , true);
					}
				});
				$('#filtro').submit(function(){
					if ($("input[type='radio'][name='tipoData']").is(':checked')) var radio = true;
					else var radio = false;
					var dataDeSplit = $('#dataDe').val().split("/");
					var dataDe = new Date(dataDeSplit[2]+"-"+dataDeSplit[1]+"-"+dataDeSplit[0]);
					var dataAteSplit = $('#dataAte').val().split("/");
					var dataAte = new Date(dataAteSplit[2]+"-"+dataAteSplit[1]+"-"+dataAteSplit[0]);
					if (dataAte>=dataDe) var data = true;
					else var data = false;
					if ($("input[type='checkbox'][name='representantes[]']").is(':checked')) var checkbox = true;
					else var checkbox = false;
					if (radio&&data&&checkbox) {
						var dadosJson = $('#filtro').serialize();
						$.ajax({
							type: 'POST',
							url: 'relComissoes.php',
							dataType: "html",
							data: dadosJson,
							beforeSend: function(){
								$('#direita').html('<img src="images/loader.gif" style="width: 32px; margin-top: 242px; margin-left: 451px;">');
							},
							success: function(html){
								$('#direita').html(html);
							},
							error: function(XMLHttpRequest, textStatus, errorThrown){
								alert("ERRO");
								$('#direita').html('');
							}
						});
						return false;
					} else {
						if (!radio) alert("Selecione um tipo de filtro!");
						if (!data) {
							alert("Problemas com as datas!");
							// alert(dataDeSplit[1]+"-"+dataDeSplit[0]+"-"+dataDeSplit[2]+"\n"+dataAteSplit[1]+"-"+dataAteSplit[0]+"-"+dataAteSplit[2]);
						}
						if (!checkbox) alert("Selecione um representante!");
						return false;
					}
				});
	    	});
	    </script>
	</head>
	<body>
		<?
		//validação página
		autorizaPagina(2);
		//painel boas vindas
		require_once("welcome.php");
		?>
		<div class="tabelacorpo">
			<div id="esquerda">
				<fieldset>
					<legend><b>Relatório de Comissões</b></legend>
					<form method="POST" id="filtro">
						<input type="radio" name="tipoData" value="entrada">Filtrar pela data de Entrada</input><br>
						<input type="radio" name="tipoData" value="faturamento">Filtrar pela data de Faturamento</input><br><br>
						Data:
						<input type="text" id="dataDe" class="data" \> até <input type="text" id="dataAte" class="data" \><br><br>
						<input type="hidden" id="altdataDe" name="dataDe" \>
						<input type="hidden" id="altdataAte" name="dataAte" \>
						Representantes:<br>
						<input type="checkbox" name="representantes[]" value="all" id="all" checked>Todos</input><br>
						<div id="reps">
							<?
							$repSql = $sql->query("select cod_rep, nome from representantes where ativo = 1 and empresa = '".$_SESSION['UsuarioEmpresa']."' order by nome asc") or die (mysqli_error($sql));
							if (mysqli_num_rows($repSql)>0) {
								for ($i=0;$i<mysqli_num_rows($repSql);$i++) {
									$rep = mysqli_fetch_array($repSql);
									echo "<input type=\"checkbox\" name=\"representantes[]\" value=\"".$rep['cod_rep']."\">".$rep['nome']."</input><br>\n";
								}
							}
							?>
						</div>
						<br><input type="submit" value="Gerar" \>
					</form>
				</fieldset>
			</div>
			<div id="direita">
				
			</div>
		</div>
	</body>
</html>