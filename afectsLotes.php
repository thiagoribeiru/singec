<?$popup = 1;
require_once("session.php");?>
<html>
<head>
<title><?echo $title." - Edição em Lotes";?></title>
<script src="jquery.nanoscroller.min.js"></script>
<link rel="stylesheet" href="nanoscroller.css">
<script type="text/javascript">
	$(document).ready(function(){
		$("#altLotes").submit(function(){
			return false;
		});
		$(".nano").nanoScroller();
		$("#checkTodos").click(function(){
			var check = document.getElementById("checkTodos");
			if (check.checked == true) {
				var checkbox = document.getElementsByName("item");
				for (var i = 0;i<checkbox.length;i++) {
					if (checkbox[i].checked==false) {
						checkbox[i].checked = true;
					}
				}
				iniciaAlteracao();
			} else {
				var checkbox = document.getElementsByName("item");
				for (var i = 0;i<checkbox.length;i++) {
					if (checkbox[i].checked==true) {
						checkbox[i].checked = false;
					}
				}
				cancelaAlteracao();
			}
		});
		$("#alteracao").change(function(){
			var alteracao = document.getElementById("alteracao");
			if (alteracao.value=="substituir") {
				$("#mps").show();
				var mps = document.getElementById("mps");
				if (mps.value=="mp") {
					$("#mp").show();
					$("#pr").hide();
					$("#sp").hide();
					$("#quantidade").show();
					verificaQuantidade();
				} else if (mps.value=="pr") {
					$("#mp").hide();
					$("#pr").show();
					$("#sp").hide();
					$("#quantidade").show();
					verificaQuantidade();
				} else if (mps.value=="sp") {
					$("#mp").hide();
					$("#pr").hide();
					$("#sp").show();
					$("#quantidade").show();
					verificaQuantidade();
				}
				// document.getElementById("submit").style.display = "none";
			} else {
				cancelaAlteracao();
				iniciaAlteracao();
			}
		});
		$("#mps").change(function(){
			var mps = document.getElementById("mps");
			if (mps.value=="mp") {
				$("#mp").show();
				$("#pr").hide();
				$("#sp").hide();
				$("#quantidade").show();
			} else if (mps.value=="pr") {
				$("#mp").hide();
				$("#pr").show();
				$("#sp").hide();
				$("#quantidade").show();
			} else if (mps.value=="sp") {
				$("#mp").hide();
				$("#pr").hide();
				$("#sp").show();
				$("#quantidade").show();
			}
		});
		$("#quantidade").keyup(function(){
			verificaQuantidade();
		});
		$("#submit").click(function(){
			if (($(document.getElementById("alteracao")).val()!="excluir"&&($(document.getElementById("quantidade")).val()).replace(",",".")!=""&&($(document.getElementById("quantidade")).val()).replace(",",".")>0)||$(document.getElementById("alteracao")).val()=="excluir") {
				submeter();
			}
			return false;
		});
	});
	function somenteNumeros(num) {
        var er = /[^0-9.,]/;
        er.lastIndex = 0;
        var campo = num;
        if (er.test(campo.value)) {
          campo.value = "";
        }
    }
	function verificaCheck(itemCheck) {
		if (itemCheck.checked==false) {
			var checkTodos = document.getElementById("checkTodos");
			if (checkTodos.checked==true) {
				checkTodos.checked = false;
			}
			var checked = false;
			var itens = document.getElementsByName("item");
			for (var j = 0;j<itens.length;j++) {
				if (itens[j].checked==true) {
					checked = true;
				}
			}
			if (checked==false) {
				cancelaAlteracao();
			}
		} else {
			var checked = true;
			var itens = document.getElementsByName("item");
			for (var j = 0;j<itens.length;j++) {
				if (itens[j].checked==false) {
					checked = false;
				}
			}
			if (checked==true) {
				var checkTodos = document.getElementById("checkTodos");
				if (checkTodos.checked==false) {
					checkTodos.checked = true;
				}
			}
			iniciaAlteracao();
		}
	}
	var shole = 0;
	function iniciaAlteracao() {
		$("#body").animate({height: "85%"});
		$("#alteracao").show();
		document.getElementById("submit").style.display = "block";
		var alteracao = document.getElementById("alteracao");
		if (shole==0) {
			shole = 1;
			for (var n = 0;n<alteracao.options.length;n++) {	
				if (alteracao.options[n].value == "excluir") {
					alteracao.options[n].selected = true;
					break;
				}
			}
		}
	}
	function cancelaAlteracao() {
		$("#body").animate({height: "100%"});
		shole = 0;
		var selects = document.getElementsByTagName("select");
		for (var m = 0;m<selects.length;m++) {
			selects[m].style.display = "none";
			$("#quantidade").hide();
			$("#submit").hide();
		}
	}
	function verificaQuantidade() {
		var quantidade = document.getElementById("quantidade");
		if (quantidade.value!="") {
			// $("#body").animate({height: "85%"});
			document.getElementById("submit").style.display = "block";
		} else {
			// $("#body").animate({height: "93%"});
			document.getElementById("submit").style.display = "none";
		}
	}
	function submeter() {
		var item = document.getElementsByName("item");
		var contArray = 0;
		var itemArray = [];
		for (var i=0;i<item.length;i++) {
			if (item[i].checked==true) {
				itemArray[contArray] = item[i].value;
				contArray++;
			}
		}
		var strItem = "";
		for (var j=0;j<itemArray.length;j++) {
			strItem += itemArray[j];
			if (j<(itemArray.length-1)) {strItem += ",";}
		}
		var prod = $(document.getElementById("submit")).attr("prod");
		var alteracao = $(document.getElementById("alteracao")).val();
		var mpsDest = $(document.getElementById("mps")).val();
		var mpDest = $(document.getElementById("mp")).val();
		var prDest = $(document.getElementById("pr")).val();
		var spDest = $(document.getElementById("sp")).val();
		var quantidade = ($(document.getElementById("quantidade")).val()).replace(",",".");
		var autoriza = false;
		if (alteracao=="excluir") {
			var confirma = confirm("Tem certeza que deseja excluir esta composição?");
			if (confirma) {
				autoriza = true;
			}
		} else {
			autoriza = true;
		}
		if (autoriza==true) {
			var dados1 = {
				'funcao':'alteraLotes',
				'prod':prod,
				'mps':'mp',
				'itens':encodeURIComponent(strItem),
				'alteracao':alteracao,
				'mpsDest':mpsDest,
				'mpDest':mpDest,
				'prDest':prDest,
				'spDest':spDest,
				'quantidade':quantidade
			};
			$.ajax({
				type: 'POST',
				url: 'funcoesAjax.php',
				data: dados1,
				dataType: "json",
				beforeSend: function(){
					// $('#fundo_fumace').show();
					$(document.getElementById("submit")).html("Alterando <img style=\"width: 13px;\" src=\"images/91.png\">");
				},
				success: function(json){
					if (json.error>0) {
						alert(json.mensagem);
						$(document.getElementById("submit")).html("Continuar alteração >>");
					} else {
						window.location.href = "editMp.php?cod_mp="+prod+"&desativar=sim";
						$(document.getElementById("submit")).html("Continuar alteração >>");
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown){
					alert("erro!");
					$(document.getElementById("submit")).html("Continuar alteração >>");
					// for(i in XMLHttpRequest) {
					// if (i!="channel")
					// document.write(i + ":" + XMLHttpRequest[i] + "<br>");
					// }
				}
			});
		} else {
			alert("Operação cancelada!");
		}
		return false;
	}
</script>
<style>
	table tr:hover {
		background: #E6E6E6;
	}
</style>
</head>
<body style="overflow: hidden;">
	<form id=\"altLotes\">
		<div id="body" class="nano"><div class="nano-content">
			<?
			//validação página
			autorizaPagina(2);
			teclaEsc();
			?>
			<fieldset>
				<legend>Edição de Lotes</legend>
				<p style="font-size: 11px;">Este item faz parte da composição de alguns Sub Produtos. Abaixo, selecione como proceder com eles.</p>
				<table style="font-size: 10px; border-collapse: collapse;" border="0px">
					<tr>
						<th width="15px"><input type="checkbox" name="todos" id="checkTodos" style="margin: 0px;" value="1" /></th>
						<th width="70px">CÓD SP</th>
						<th width="325px">DESCRICÃO</th>
						<th width="100px">QUANT.</th>
					</tr>
					<?
					$item = explode(",",$_GET['afetados']);
					$query = "select cod_sp, descricao, (select quantidade from sp_composicao where ativo = '1' and empresa = '".$_SESSION['UsuarioEmpresa']."' and mps = 'mp' and cod = '".$_GET['prod']."' and cod_sp = sp_dados.cod_sp) as quant, (select unidade from mp where ativo = '1' and empresa = '".$_SESSION['UsuarioEmpresa']."' and cod_mp = '".$_GET['prod']."') as unidade from sp_dados where ativo = '1' and empresa = '".$_SESSION['UsuarioEmpresa']."' and cod_sp in (";
					for ($j=0;$j<count($item);$j++) {
						$query .= "'".$item[$j]."'";
						if ($j!=(count($item)-1)) $query .= ",";
					}
					$query .= ") order by cod_sp";
					$spPesq = $sql->query($query) or die (mysqli_error($sql));
					if ($spPesq->num_rows > 0) {
						for ($i=0;$i<$spPesq->num_rows;$i++) {
							$sp = mysqli_fetch_array($spPesq);
							echo "<tr style=\"border-bottom: 1px solid #848484;\">\n";
								echo "<td><input style=\"margin: 0px;\" type=\"checkbox\" name=\"item\" value=\"".$sp['cod_sp']."\" onclick=\"verificaCheck(this);\" /></td>\n";
								echo "<td>".$sp['cod_sp']."</td>\n";
								echo "<td>".limitarTexto($sp['descricao'],55)."</td>\n";
								echo "<td style=\"text-align: right;\">".number_format($sp['quant'],4,",",".")." ".$sp['unidade']."</td>\n";
							echo "</tr>\n";
						}
					}
					?>
				</table>
			</fieldset>
		</div></div>
		<div id="body2" style="margin-top: 5px;">
			<select name="alteracao" id="alteracao" style="width: 100px; display: none; height: 21px;">
				<option value="excluir">Excluir</option>
				<option value="substituir">Substituir por</option>
			</select>
			<select name="mps" id="mps" style="width: 100px; display: none; height: 21px;">
				<option value="mp">matéria prima</option>
				<option value="pr">processo</option>
				<option value="sp">sub produto</option>
			</select>
			<select name="mp" id="mp" style="width: 250px; display: none; height: 21px;">
				<?
				$mpPesq = $sql->query("select cod_mp, descricao, unidade from mp where ativo = '1' and empresa = '".$_SESSION['UsuarioEmpresa']."' and cod_mp != '".$_GET['prod']."' and estado = '1' order by descricao") or die (mysqli_error($sql));
				if ($mpPesq->num_rows > 0) {
					for ($l=0;$l<$mpPesq->num_rows;$l++) {
						$mp = mysqli_fetch_array($mpPesq);
						echo "<option value=\"".$mp['cod_mp']."\">".limitarTexto($mp['descricao'],40)." (".$mp['unidade'].")</option>";
					}
				}
				?>
			</select>
			<select name="pr" id="pr" style="width: 250px; display: none; height: 21px;">
				<?
				$prPesq = $sql->query("select cod_pr, descricao, unidade from processos where ativo = '1' and empresa = '".$_SESSION['UsuarioEmpresa']."' order by descricao") or die (mysqli_error($sql));
				if ($prPesq->num_rows > 0) {
					for ($m=0;$m<$prPesq->num_rows;$m++) {
						$pr = mysqli_fetch_array($prPesq);
						echo "<option value=\"".$pr['cod_pr']."\">".limitarTexto($pr['descricao'],40)." (".$pr['unidade'].")</option>";
					}
				}
				?>
			</select>
			<select name="sp" id="sp" style="width: 250px; display: none; height: 21px;">
				<?
				$spPesq = $sql->query("select cod_sp, descricao, unidade from sp_dados where ativo = '1' and empresa = '".$_SESSION['UsuarioEmpresa']."' order by descricao") or die (mysqli_error($sql));
				if ($spPesq->num_rows > 0) {
					for ($n=0;$n<$spPesq->num_rows;$n++) {
						$sp = mysqli_fetch_array($spPesq);
						echo "<option value=\"".$sp['cod_sp']."\">".limitarTexto($sp['descricao'],40)." (".$sp['unidade'].")</option>";
					}
				}
				?>
			</select>
			<input type="text" id="quantidade" name="quantidade" placeholder="Quant." style="width: 70px; display: none; height: 21px;" onkeyup="somenteNumeros(this);" />
			<br><button id="submit" style="display: none; float: right; margin-top: 5px;" prod="<?echo $_GET['prod'];?>">Continuar alteração >><img style="width: 13px; display: none;" src="images/91.png"></button>
		</div>
	</form>
</body>
</html>