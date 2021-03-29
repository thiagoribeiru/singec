<?require_once("session.php");?>
<html>
<head>
<title><?echo $title;?></title>
	<link rel="stylesheet" href="jquery-ui.css" />
    <!--<script src="jquery-1.8.2.js"></script>-->
    <!--<script src="jQuery1.12.1.min.js"></script>-->
    <script src="jquery-ui.js"></script> 
<script language="JavaScript" type="text/javascript">
	function submitenter(myfield,e)	{
		var keycode;
		if (window.event) keycode = window.event.keyCode;
		else if (e) keycode = e.which;
		else return true;
		
		if (keycode == 13) {
			myfield.form.submit();
			return false;
		} else return true;
	}
	function barra(objeto){
		if (objeto.value.length == 2 || objeto.value.length == 5 ){
			objeto.value = objeto.value+"/";
		}
	}
	function destacarQuebras(inicial,final) {
		for (var i=inicial;i<=final;i++){
			if (document.getElementById('cliente'+i).clientHeight>12) {
				document.getElementById('cliente'+i).style.height = "12px";
				document.getElementById('cliente'+i).style.overflow = "auto";
				document.getElementById('clienteseta'+i).style.textAlign = "right";
				document.getElementById('clienteseta'+i).innerHTML = "<img src=\"images/down.png\" width=\"7px\"></img>";
			}
			if (document.getElementById('regiao'+i).clientHeight>12) {
				document.getElementById('regiao'+i).style.height = "12px";
				document.getElementById('regiao'+i).style.overflow = "auto";
				document.getElementById('regiaoseta'+i).style.textAlign = "right";
				document.getElementById('regiaoseta'+i).innerHTML = "<img src=\"images/down.png\" width=\"7px\"></img>";
			}
			if (document.getElementById('representante'+i).clientHeight>12) {
				document.getElementById('representante'+i).style.height = "12px";
				document.getElementById('representante'+i).style.overflow = "auto";
				document.getElementById('representanteseta'+i).style.textAlign = "right";
				document.getElementById('representanteseta'+i).innerHTML = "<img src=\"images/down.png\" width=\"7px\"></img>";
			}
			if (document.getElementById('descricao'+i).clientHeight>12) {
				document.getElementById('descricao'+i).style.height = "12px";
				document.getElementById('descricao'+i).style.overflow = "auto";
				document.getElementById('descricaoseta'+i).style.textAlign = "right";
				document.getElementById('descricaoseta'+i).innerHTML = "<img src=\"images/down.png\" width=\"7px\"></img>";
			}
		}
		return false;
	}
	var clique = 0;
	function mostradiv(id_div) {
		var strdiv = id_div;
		var div = document.getElementById(strdiv);
		if (div.style.display=="") {
			div.style.display = "block";
			div.setAttribute("onclick","clique=1");
		} else if (div.style.display=="block") {
			div.style.display = "none";
			if (clique!=0) {
				document.getElementById('formpesq').submit();
			}
		} else if (div.style.display=="none") {
			div.style.display = "block";
			div.setAttribute("onclick","clique=1");
		}
	}
	function abreRel(dtIni,dtFin) {
		var stringP1 = document.getElementById('hiddenPesqP1').value;
		var stringP2 = "%espand%espdata_nf%esp>=%esp'"+dtIni+"'%espand%espdata_nf%esp<=%esp'"+dtFin+"'%esporder%espby%espdata_nf%virg%espdata_cri";
		// document.cookie = "search="+stringP1+stringP2;
		window.open('relRent.php?search='+stringP1+stringP2,'_blank');
	}
	function verificaStatusCli(nome,id){
		if(nome.form.tudoCli.checked == 1) {
				nome.form.tudoCli.checked = 1;
				marcarTodos(nome,id);
			}
		else {
				nome.form.tudoCli.checked = 0;
				desmarcarTodos(nome,id);
			}
	}
	function verificaStatusRep(nome,id){
		if(nome.form.tudoRep.checked == 1) {
				nome.form.tudoRep.checked = 1;
				marcarTodos(nome,id);
			}
		else {
				nome.form.tudoRep.checked = 0;
				desmarcarTodos(nome,id);
			}
	} 
	function marcarTodos(nome,id){
	   for (var i=0;i<nome.form.elements.length;i++)
		  //if(nome.form.elements[i].type == "checkbox")
		  if(nome.form.elements[i].id == id)
			 nome.form.elements[i].checked=1
	}
	 
	function desmarcarTodos(nome,id){
	   for (var i=0;i<nome.form.elements.length;i++)
		  //if(nome.form.elements[i].type == "checkbox")
		  if(nome.form.elements[i].id == id)
			 nome.form.elements[i].checked=0
	}
	function geraRelatorio(filtro) {
		dt1 = document.getElementById("txDtpedIni").value;
		dt2 = document.getElementById("txDtpedFin").value;
		dt3 = document.getElementById("txDtfatIni").value;
		dt4 = document.getElementById("txDtfatFin").value;
		if ( (dt1.split("/") == "") && (dt2.split("/") == "") ) {
		   data1 = new Date();
		   data2 = new Date();
		}
		else {
		   var dtInicio = dt1.split("/");
		   var dtFim = dt2.split("/");
		   data1 = new Date(dtInicio[2] + "/" + dtInicio[1] + "/" + dtInicio[0]);
		   data2 = new Date(dtFim[2] + "/" + dtFim[1] + "/" + dtFim[0]);
		}
		var diferenca = Math.abs(data1 - data2); //diferença em milésimos e positivo
		var dia = 1000*60*60*24; // milésimos de segundo correspondente a um dia
		var total1 = Math.round(diferenca/dia); //valor total de dias arredondado 
		
		if ( (dt3.split("/") == "") && (dt4.split("/") == "") ) {
		   data1 = new Date();
		   data2 = new Date();
		}
		else {
		   var dtInicio = dt3.split("/");
		   var dtFim = dt4.split("/");
		   data1 = new Date(dtInicio[2] + "/" + dtInicio[1] + "/" + dtInicio[0]);
		   data2 = new Date(dtFim[2] + "/" + dtFim[1] + "/" + dtFim[0]);
		}
		var diferenca = Math.abs(data1 - data2); //diferença em milésimos e positivo
		var dia = 1000*60*60*24; // milésimos de segundo correspondente a um dia
		var total2 = Math.round(diferenca/dia); //valor total de dias arredondado 
		
		if (total1<=30 && total2<=30) {
			$.ajax({
	            method: "GET",
	            url: "relRent3.php",
	            data: "filtro="+filtro,
	            dataType: "json",
	            beforeSend: function(){
	                $('#fundo_fumace').show();
	            },
	            success: function(json){
	            	if (json.sucess==1) {
		                $('#fundo_fumace').hide();
		                var arquivo = json.arquivo;
		                window.open('exibeRel.php?rel='+arquivo,'_blank');
	            	} else {
	            		alert("O sistema não retornou nenhum relatório.");
	            		$('#fundo_fumace').hide();
	            	}
	            },
	            error: function() {
	            	alert("ERRO!");
	            	$('#fundo_fumace').hide();
	            },
	            timeout: 90000
	        });
		} else {
			alert("Relatório de no máximo 30 dias!");
		}
	}
</script>
<style>
	.filtro_representante {
		border: solid 1px;
		border-color: #848484;
		background-color: rgba(200,200,200,0.95);
		font-size: 11px;
		padding: 2px;
		max-height: 150px;
		overflow: auto;
		position: absolute;
		top: 85px;
		left: 420px;
		display: none;
	}
	.filtro_cliente {
		border: solid 1px;
		border-color: #848484;
		background-color: rgba(200,200,200,0.95);
		font-size: 11px;
		padding: 2px;
		max-height: 150px;
		overflow: auto;
		position: absolute;
		top: 85px;
		left: 225px;
		display: none;
	}
	.filtro_dtped {
		border: solid 1px;
		border-color: #848484;
		background-color: rgba(200,200,200,0.95);
		font-size: 11px;
		padding: 2px;
		max-height: 150px;
		overflow: auto;
		position: absolute;
		top: 85px;
		left: 45px;
		display: none;
		padding-right: 40px;
	}
	.filtro_dtfat {
		border: solid 1px;
		border-color: #848484;
		background-color: rgba(200,200,200,0.95);
		font-size: 11px;
		padding: 2px;
		max-height: 150px;
		overflow: auto;
		position: absolute;
		top: 85px;
		left: 1050px;
		display: none;
		padding-right: 40px;
	}
	#confirma {
		position: absolute;
		top: 3px;
		right: 2px;
		cursor: pointer;
	}
	#limpar {
		width: 19px;
		position: absolute;
		top: 2px;
		right: 20px;
		cursor: pointer;
	}
	table thead tr th, table tfoot tr th {
		/*background-color: #ccc;*/
		/*border: 1px solid #FFF;*/
		/*font-size: 8pt;*/
		/*padding: 4px;*/
	}
	table thead tr .header {
		background-image: url(images/bg.gif);
		background-size: 22px;
		background-repeat: no-repeat;
		background-position: center left -11px;
		cursor: pointer;
	}
	table thead tr .headerSortUp {
		background-image: url(images/asc.gif);
		background-size: 23px;
		background-repeat: no-repeat;
		background-position: center left -11px;
		cursor: pointer;
	}
	table thead tr .headerSortDown {
		background-image: url(images/desc.gif);
		background-size: 24px;
		background-repeat: no-repeat;
		background-position: center left -13px;
		cursor: pointer;
	}
	table thead tr .headerSortDown, table thead tr .headerSortUp {
		/*background-color: #666;*/
		background-color: #ccc;
		color:#fff;
	}
	#tb1 {
		font-size: 9px;
		border-collapse: collapse;
		border: solid 1px;
	}
	#tb1 thead tr th, #tb1 tbody tr td {
		border: solid 1px;
		border-color: #000000;
	}
	/*#tb1 tbody tr td div {*/
	/*	height: 14px;*/
	/*	overflow: auto;*/
	/*}*/
	.divtitulo {
		width: 75%;
		float: left;
		position: relative;
		line-height: 15px;
	}
	.divlupa {
		width: 15%;
		float: right;
		text-align: right;
	}
	.divlupa img {
		width: 15px;
		cursor: pointer;
	}
	#th0 .divlupa img, #th1 .divlupa img {
		position: relative;
		left: -8px;
	}
	tr.linhaLinkfechado {
		background: white;
		color: #585858;
		font-style: italic;
	}
	tr.linhaLinkfechado:hover {
		background: #EEEEEE;
		cursor: pointer;
	}
	#divseparador {
		margin: 0 8 0 9;
		/*width: 5px;*/
		position: relative;
		float: left;
	}
	#divoptions {
		position: absolute;
		top: 20px;
		right: 0px;
		border: solid 1px;
		border-color: #848484;
		background-color: rgba(200,200,200,0.95);
		font-size: 11px;
		padding: 2px;
		max-height: 150px;
		overflow: auto;;
		display: none;
		z-index: 10;
		width: 180px;
	}
</style>
<!--<script type="text/javascript" src="paging.js"></script>-->
<!--<script type="text/javascript" src="jquery.tablesorter.js"></script>-->

</head>
<body onunload="window.opener.location.reload();">
	<script>
	$(function() {
	    $( "#txDtpedIni" ).datepicker({
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
	    });
	    $( "#txDtpedFin" ).datepicker({
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
	    });
	    $( "#txDtfatIni" ).datepicker({
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
	    });
	    $( "#txDtfatFin" ).datepicker({
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
	    });
	});
	$(document).ready(function(){
		$("#th2 > div > img").click(function(e){
			$("#divfiltrocli").toggle();
			e.stopPropagation();
		});
		$("#th0 > div > img").click(function(e){
			$("#divfiltrodtped").toggle();
			e.stopPropagation();
		});
		$("#th1 > div > img").click(function(e){
			$("#divfiltrodtfat").toggle();
			e.stopPropagation();
		});
		$("#th4 > div > img").click(function(e){
			$("#divfiltrorep").toggle();
			e.stopPropagation();
		});
		$("#imgoptions").click(function(e){
			$("#divoptions").toggle();
			e.stopPropagation();
		});
		$(document).on('click', function (e) {    
		    if (!$(e.target).closest('#divfiltrodtped').length) $('#divfiltrodtped').hide();
		    if (!$(e.target).closest('#divfiltrodtfat').length) $('#divfiltrodtfat').hide();
		    if (!$(e.target).closest('#divfiltrocli').length) $('#divfiltrocli').hide();
		    if (!$(e.target).closest('#divfiltrorep').length) $('#divfiltrorep').hide();
		    if (!$(e.target).closest('#divoptions').length) $('#divoptions').hide();
		});
	});
	</script>
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
	//######### INICIO Paginação
		$numreg = 30;
		// Quantos registros por página vai ser mostrado
		if (!isset($_GET['pg'])) $pg = 0; else $pg=$_GET['pg'];
		$inicial = $pg * $numreg;
	//######### FIM dados Paginação

	//orientador de data
	if (!isset($_GET['filtro'])) {
		$filtro = array();
		$filtroSer = serialize($filtro);
		echo "<script>location.href=\"".$_SERVER['PHP_SELF']."?pg=$pg&busca=$busca&filtro=$filtroSer&order=&sent=asc\"</script>";
		exit;
	} else if ($_SERVER['REQUEST_METHOD'] != "POST") {
		// $dtIni = $_GET['inicio'];
		// $dtFin = $_GET['final'];
		// $dtIniSql = date("Y-m-d",strtotime(str_replace("/","-",$dtIni)));
		// $dtFinSql = date("Y-m-d",strtotime(str_replace("/","-",$dtFin)));
		$filtro = unserialize(urldecode($_GET['filtro']));
		$frep = $filtro['filtrorep']; //filtro de representante
		$fequipe = $filtro['filtroEquipe']; //filtro de equipe
		$fcli = $filtro['filtrocli']; //filtro de cliente
		$fDtPed = $filtro['filtrodtped']; //filtro de data do pedido
		$fDtFat = $filtro['filtrodtfat']; //filtro de data de faturamento
		$fCodProd = $filtro['filtroCodProd']; //filtro de codigo de produto
		$fFaturados = $filtro['filtroFaturados'];
		$fAbertos = $filtro['filtroAbertos'];
		$order = $_GET['order'];
		if ($_GET['mudar']=="sim") {
			if ($order!="" and $_GET['sent']=="asc") $sent = "desc";
			else if ($order!="" and $_GET['sent']=="desc") $sent = "asc";
			else if ($order=="") $sent = "asc";
			else {
				echo "<script>location.href=\"".$_SERVER['PHP_SELF']."?pg=$pg&busca=$busca&filtro=".urlencode(serialize(unserialize(urldecode($_GET['filtro']))))."&order=$order&sent=asc\"</script>";
				exit;
			}
		} else if ($_GET['mudar']=="nao") {
			echo "<script>location.href=\"".$_SERVER['PHP_SELF']."?pg=$pg&busca=$busca&filtro=".urlencode(serialize(unserialize(urldecode($_GET['filtro']))))."&order=&sent=\"</script>";
			exit;
		} else if ($_GET['sent']=="asc" or $_GET['sent']=="desc") $sent = $_GET['sent'];
		else {
			echo "<script>location.href=\"".$_SERVER['PHP_SELF']."?pg=$pg&busca=$busca&filtro=".urlencode(serialize(unserialize(urldecode($_GET['filtro']))))."&order=$order&sent=asc\"</script>";
			exit;
		}
	}
	//orientador de metodo para trabalho com paginação e busca e filtros
	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		$busca = urlencode($_POST['busca']);
		// $dtIni = $_POST['dtIni'];
		// $dtFin = $_POST['dtFin'];
		$filtro['filtrorep'] = $_POST['filtrorep']; //filtro de representante
		$filtro['filtroEquipe'] = $_POST['filtroEquipe']; //filtro de equipe
		$filtro['filtrocli'] = $_POST['filtrocli']; //filtro de cliente
		$filtrodtped['inicial'] = $_POST['dtpedini'];
		$filtrodtped['final'] = $_POST['dtpedfin'];
		$filtrodtfat['inicial'] = $_POST['dtfatini'];
		$filtrodtfat['final'] = $_POST['dtfatfin'];
		$filtro['filtrodtped'] = $filtrodtped;
		$filtro['filtrodtfat'] = $filtrodtfat;
		$filtro['filtroCodProd'] = $_POST['cod_sp'];
		$filtro['filtroFaturados'] = $_POST['filtroFaturados'];
		$filtro['filtroAbertos'] = $_POST['filtroAbertos'];
		$filtroSer = urlencode(serialize($filtro));
		// header ("Location: ".$_SERVER['PHP_SELF']."?pg=$pg&busca=$busca");
		echo "<script>location.href=\"".$_SERVER['PHP_SELF']."?pg=$pg&busca=$busca&filtro=$filtroSer&order=".$_GET['order']."&sent=".$_GET['sent']."\"</script>";
		exit;
	}
	else if (!isset($_GET['busca'])) $busca = "";
	else $busca=$_GET['busca'];
	$varUrlOrder = $_SERVER['PHP_SELF']."?pg=$pg&busca=$busca&filtro=".urlencode(serialize(unserialize(urldecode($_GET['filtro']))))."&order=";
	$varUrlSent = "&sent=";
	if ($busca!="") $textBusca=" and (data_nf like '%".str_replace("*","%",$busca)."%' or (select cliente from clientes where ativo = 1 and empresa = rentabilidade.empresa and cod_cli = rentabilidade.cliente) like '%".str_replace("*","%",$busca)."%' or cod_sp like '%".str_replace("*","%",$busca)."%' or (select descricao from sp_dados where ativo = 1 and empresa = rentabilidade.empresa and cod_sp = rentabilidade.cod_sp) like '%".str_replace("*","%",$busca)."%' or (select nome from representantes where ativo = 1 and empresa = rentabilidade.empresa and cod_rep = (select representante from clientes where ativo = 1 and empresa = rentabilidade.empresa and cod_cli = rentabilidade.cliente)) like '%".str_replace("*","%",$busca)."%')"; else $textBusca="";
	//final orientador
	
	if (isset($frep) or isset($fcli) or isset($fDtPed) or isset($fDtFat) or isset($fequipe) or isset($fCodProd)) {
		$stringRep = "(select representante from clientes where ativo = 1 and empresa = rentabilidade.empresa and cod_cli = rentabilidade.cliente)";
		$rows = mysqli_num_rows($sql->query("select cod_rep, nome from representantes where ativo = 1 and empresa = ".$_SESSION['UsuarioEmpresa']." order by nome"));
		$buscarep = "";
		for ($i=1;$i<=$rows;$i++) {
			if (isset($frep[$i]) and $buscarep=="") {
				$buscarep = " and ($stringRep = ".$frep[$i]." ";
			} else if (isset($frep[$i]) and $buscarep!="") {
				$buscarep = $buscarep." or $stringRep = ".$frep[$i]." ";
			}
		}
		if ($buscarep!="") $buscarep = $buscarep.") ";
		
		$stringCli = "cliente";
		$rowscli = mysqli_num_rows($sql->query("select cod_cli, cliente from clientes where ativo = 1 and empresa = ".$_SESSION['UsuarioEmpresa']." order by cliente"));
		$buscacli = "";
		for ($i=1;$i<=$rowscli;$i++) {
			if (isset($fcli[$i]) and $buscacli=="") {
				$buscacli = " and ($stringCli = ".$fcli[$i]." ";
			} else if (isset($fcli[$i]) and $buscacli!="") {
				$buscacli = $buscacli." or $stringCli = ".$fcli[$i]." ";
			}
		}
		if ($buscacli!="") $buscacli = $buscacli.") ";
		
		if ($fDtPed['inicial']!="" and $fDtPed['final']!="") {
			$dtpedini = date("Y-m-d",strtotime(str_replace("/","-",$fDtPed['inicial'])));
			$dtpedfin = date("Y-m-d",strtotime(str_replace("/","-",$fDtPed['final'])));
			// $buscadtped = " and data_ped >= '$dtpedini' and data_ped <= '$dtpedfin'";
			$buscadtped = " and data_ped between '$dtpedini' and '$dtpedfin'";
		} else $buscadtped = "";
		
		if ($fDtFat['inicial']!="" and $fDtFat['final']!="") {
			$dtfatini = date("Y-m-d",strtotime(str_replace("/","-",$fDtFat['inicial'])));
			$dtfatfin = date("Y-m-d",strtotime(str_replace("/","-",$fDtFat['final'])));
			// $buscadtfat = " and data_nf >= '$dtfatini' and data_nf <= '$dtfatfin' and faturado = 1";
			$buscadtfat = " and data_nf between '$dtfatini' and '$dtfatfin' and faturado = 1";
		} else $buscadtfat = "";
		
		$stringEquipe = "(SELECT concat(',',group_concat(cod_equipe order by cod_equipe, ','),',') FROM representantes_equipes WHERE cod_rep = (select representante from clientes where ativo = 1 and empresa = rentabilidade.empresa and cod_cli = rentabilidade.cliente) and empresa = rentabilidade.empresa and ativo = 1 and dentro = 1)";
		$buscaequipe = "";
		for ($i=0;$i<count($fequipe);$i++) {
			if (isset($fequipe) and $i==0 and $buscaequipe=="") {
				$buscaequipe = " and ($stringEquipe like '%,".$fequipe[$i].",%' ";
			} else if (isset($fequipe) and $i!=0 and $buscaequipe!="") {
				$buscaequipe = $buscaequipe." or $stringEquipe like '%,".$fequipe[$i].",%' ";
			}
		}
		if ($buscaequipe!="") $buscaequipe = $buscaequipe.") ";
		
		if ($fCodProd!="") {
			// $buscaCodProd = " and cod_sp = '$fCodProd'";
			$buscaCodProd = " and cod_sp in ($fCodProd)";
		} else $buscaCodProd = "";
		
		if ($fFaturados!="" and $fAbertos!="") {
			$buscaFaturados = " and (faturado = '$fFaturados' or faturado = '$fAbertos')";
			$checkedFaturados1 = " checked";
			$checkedAbertos1 = " checked";
		} else if ($fFaturados=="1" and $fAbertos=="") {
			$buscaFaturados = " and faturado = '$fFaturados'";
			$checkedFaturados1 = " checked";
			$checkedAbertos1 = "";
		} else if ($fFaturados=="" and $fAbertos=="0") {
			$buscaFaturados = " and faturado = '$fAbertos'";
			$checkedFaturados1 = "";
			$checkedAbertos1 = " checked";
		} else $buscaFaturados = " and faturados = '-1'";
		
	} else {
		$buscarep = "";
		$buscacli = "";
		$buscadtped = "";
		$buscadtfat = "";
		$buscaequipe = "";
		$buscaCodProd = "";
		$buscaFaturados = "";
		$checkedFaturados1 = " checked";
		$checkedAbertos1 = " checked";
	}
	
	echo "<div class=\"tabelacorpo\">";
	
	echo "<form action=\"".$_SERVER['PHP_SELF']."?pg=$pg&busca=$busca&order=$order&sent=$sent&filtro=".$_GET['filtro']."\" method=\"POST\" style=\"margin:0;\" id=\"formpesq\">\n";

//tabela
echo "<div id=\"menusuperiortab\" style=\"position: relative; margin-bottom: 2px; float: left; width: 1318px; max-width: 100%;\">\n";
	echo "<div style=\"float: right;\">\n";
		echo "<div id=\"divseparador\">|</div>";
		echo "<div style=\"float: left;\"><a href=\"#\" onclick=\"abrirPopupFull('fechamento.php');\" style=\"bottom: -1px; position: relative;\">Fechamento</a></div>";
		// echo "<div id=\"divseparador\">|</div>";
		// echo "<div style=\"float: left;\"><a href=\"#\" onclick=\"window.open('relRent.php','_blank');\" style=\"bottom: -1px; position: relative;\">Gerar Relatório</a></div>";
		
		echo "<div id=\"divseparador\">|</div>";
		// echo "<div style=\"float: left;\"><a href=\"#\" onclick=\"window.open('relRent2.php?filtro=".urlencode(serialize($buscaFaturados.$textBusca.$buscarep.$buscacli.$buscaequipe.$buscaCodProd." ".$buscadtped.$buscadtfat))."&order=".$order."&sent=".$sent."','_blank');\" style=\"bottom: -1px; position: relative;\">Gerar Relatório</a></div>";
		echo "<div style=\"float: left;\"><a href=\"#\" onclick=\"geraRelatorio('".urlencode(serialize($buscaFaturados.$textBusca.$buscarep.$buscacli.$buscaequipe.$buscaCodProd." ".$buscadtped.$buscadtfat))."&order=".$order."&sent=".$sent."');\" style=\"bottom: -1px; position: relative;\">Gerar Relatório</a></div>";
		
		echo "<div id=\"divseparador\">|</div>";
		echo "<div style=\"float: left;\"><a href=\"#\" onclick=\"abrirPopup('add_rent.php',1000,100);\" style=\"bottom: -1px; position: relative;\">Adicionar</a></div>";
		echo "<div id=\"divseparador\">|</div>";
		// echo "<div style=\"position: relative; float: left;\"><label for=\"txBusca\">Procucar: </label>
		// 			<input type=\"text\" name=\"busca\" id=\"txBusca\" value=\"$busca\" style=\"height:18px; bottom:-1px; position: relative;\" onKeyPress=\"return submitenter(this,event)\"/></div>";
		// echo "<div id=\"divseparador\">|</div>";
		echo "<div  style=\"position: relative; float: left;\"><label for=\"txCod\">Cod. Prod.: </label>
					<input type=\"text\" name=\"cod_sp\" id=\"txCod\" value=\"$fCodProd\" style=\"height:18px; bottom:-1px; position: relative;\" onKeyPress=\"return submitenter(this,event)\" size=\"8\"/></div>";
		echo "<div id=\"divseparador\">|</div>";
		echo "<div style=\"position: relative; float: left;\"><img id=\"imgoptions\" src=\"images/options.png\" style=\"height: 19px; cursor: pointer;\" onclickk=\"mostradiv('divoptions');\">";
			echo "<div id=\"divoptions\">\n";
				echo "<input type=\"checkbox\" name=\"filtroFaturados\" value=\"1\"$checkedFaturados1>Exibir Pedidos Faturados</input><br>\n";
				echo "<input type=\"checkbox\" name=\"filtroAbertos\" value=\"0\"$checkedAbertos1>Exibir Pedidos em Aberto</input><br>\n";
			echo "</div>\n";
		echo "</div>\n";
		echo "<div style=\"position: relative; float: left;\"><img src=\"images/filtrar.gif\" style=\"height: 19px; cursor: pointer;\" onclick=\"document.getElementById('formpesq').submit();\"></div>\n";
	echo "</div>\n";
echo "</div>\n"	;
	
	?>
	<style>
		.larguraAdap {
			width: 1318px;
			float: left;
			max-width: 100%;
		}
		@media (max-width: 1300px) {
			.larguraAdap {
				min-width: 1250px;
			}
			#tb1 {
				font-size: 8px;
			}
			#tb1 thead {
				font-size: 7px;
			}
			.divtitulo {
				width: 75%;
			}
			.divlupa {
				width: 10%;
			}
		}
	</style>
	<script>
		// $(document).ready(function(){
		// 	var tabela = document.getElementById('tb1');
		// 	var tbody = tabela.children;
		// 	for (var i in tbody) {
		// 		if (tbody[i].tagName=="THEAD") {
		// 			tbody = tbody[i];
		// 			break;
		// 		}
		// 	}
		// 	var tr = tbody.children;
		// 	for (var i in tr) {
		// 		if (tr[i].tagName=="TR") {
		// 			tr = tr[i];
		// 			break;
		// 		}
		// 	}
		// 	var td = tr.children;
		// 	for (var i in td) {
		// 		if (td[i].tagName=="TH") {
		// 			var st = "";
		// 			st = td[i].clientWidth;
		// 			st += "px";
		// 			var div = td[i].children;
		// 			for (var j in div) {
		// 				if (div[j].tagName=="DIV") {
		// 					var classe = div[j].className;
		// 					var divWidth = div[j].clientWidth;
		// 					st += " (div."+classe+" => "+divWidth+"px)";
		// 					break;
		// 				}
		// 			}
		// 			alert(st);
		// 		}
		// 	}
		// });
	</script>
	<?
	echo "<div class=\"larguraAdap\">";
	echo "<table id=\"tb1\">\n";
		echo "<thead><tr height=18px>\n";
			$iniLink = "onclick=\"location.href='$varUrlOrder";
			if ($_GET['sent']=='desc') {
				$sentLink = '';
				$finLink = $varUrlSent.$sentLink."&mudar=nao'\"";;
			} else {
				$sentLink = $sent;
				$finLink = $varUrlSent.$sentLink."&mudar=sim'\"";
			}
			if ($buscadtped!="") $fundodtped = "bgcolor=\"#E6F8E0\""; else $fundodtped = "";
			echo "<th width=47px $fundodtped class=\"header\" id=\"th0\"><div class=\"divtitulo\" $iniLink"."dataped"."$finLink><center>"."DT.PED"."</center></div><div class=\"divlupa\"><img src=\"images/lupa.png\" onclickk=\"mostradiv('divfiltrodtped');\"></div></th>\n";
			// echo "<th width=50px $iniLink"."dataped"."$finLink class=\"header\" id=\"th0\"><center>"."DT.PED"."</center></th>\n";
			if ($buscacli!="") $fundocli = "bgcolor=\"#E6F8E0\""; else $fundocli = "";
			echo "<th width=180px $fundocli class=\"header\" id=\"th2\"><div class=\"divtitulo\" $iniLink"."cliente"."$finLink><center>"."CLIENTE"."</center></div><div class=\"divlupa\"><img src=\"images/lupa.png\" onclickk=\"mostradiv('divfiltrocli');\"></div></th>\n";
			// echo "<th width=180px $iniLink"."cliente"."$finLink class=\"header\" id=\"th2\"><center>"."CLIENTE"."</center></th>\n";
			echo "<th width=50px $iniLink"."regiao"."$finLink class=\"header\" id=\"th3\"><center>"."REGIÃO"."</center></th>\n";
			if ($buscarep!="" or $buscaequipe!="") $fundorep = "bgcolor=\"#E6F8E0\""; else $fundorep = "";
			echo "<th width=142px $fundorep class=\"header\" id=\"th4\"><div class=\"divtitulo\" $iniLink"."representante"."$finLink><center>"."REPRESENTANTE"."</center></div><div class=\"divlupa\"><img src=\"images/lupa.png\" onclickk=\"mostradiv('divfiltrorep');\"></div></th>\n";
			echo "<th width=50px $iniLink"."prod"."$finLink class=\"header\" id=\"th5\"><center>"."PROD."."</center></th>\n";
			echo "<th width=260px $iniLink"."descricao"."$finLink class=\"header\" id=\"th6\"><center>"."DESCRIÇÃO"."</center></th>\n";
			echo "<th width=50px $iniLink"."quant"."$finLink class=\"header\" id=\"th7\"><center>"."QUANT."."</center></th>\n";
			echo "<th width=20px $iniLink"."und"."$finLink class=\"header\" id=\"th8\"><center>"."UND"."</center></th>\n";
			echo "<th width=48px $iniLink"."vlr_padr"."$finLink class=\"header\" id=\"th9\"><center>"."VLR.PADR."."</center></th>\n";
			echo "<th width=48px $iniLink"."vlr_nf"."$finLink class=\"header\" id=\"th10\"><center>"."VLR. NF"."</center></th>\n";
			echo "<th width=52px $iniLink"."icms"."$finLink class=\"header\" id=\"th11\"><center>"."(-) ICMS"."</center></th>\n";
			echo "<th width=52px $iniLink"."comis"."$finLink class=\"header\" id=\"th12\"><center>"."(-) COMIS"."</center></th>\n";
			echo "<th width=68px $iniLink"."total"."$finLink class=\"header\" id=\"th13\"><center>"."TOTAL FAT."."</center></th>\n";
			echo "<th width=60px $iniLink"."vlr_mp"."$finLink class=\"header\" id=\"th16\"><center>"."MP.BRUT."."</center></th>\n";
			echo "<th width=60px $iniLink"."vlr_contr"."$finLink class=\"header\" id=\"th14\"><center>"."RENTABILIDADE"."</center></th>\n";
			echo "<th width=40px $iniLink"."margem"."$finLink class=\"header\" id=\"th15\"><center>"."MARG."."</center></th>\n";
			if ($buscadtfat!="") $fundodtfat = "bgcolor=\"#E6F8E0\""; else $fundodtfat = "";
			echo "<th width=47px $fundodtfat class=\"header\" id=\"th1\"><div class=\"divtitulo\" $iniLink"."data"."$finLink><center>"."DT.FAT"."</center></div><div class=\"divlupa\"><img src=\"images/lupa.png\" onclickk=\"mostradiv('divfiltrodtfat');\"></div></th>\n";
			// echo "<th width=50px $iniLink"."data"."$finLink class=\"header\" id=\"th1\"><center>"."DT.FAT"."</center></th>\n";
		echo "</tr></thead><tbody>\n";
		$stringPesqP1 = "select cod_rent, data_nf, (select cliente from clientes where ativo = 1 and empresa = rentabilidade.empresa and cod_cli = rentabilidade.cliente) as cliente, (select regiao from regioes where ativo = 1 and empresa = rentabilidade.empresa and cod_reg = (select regiao from clientes where ativo = 1 and empresa = rentabilidade.empresa and cod_cli = rentabilidade.cliente)) as regiao, (select nome from representantes where ativo = 1 and empresa = rentabilidade.empresa and cod_rep = (select representante from clientes where ativo = 1 and empresa = rentabilidade.empresa and cod_cli = rentabilidade.cliente)) as representante, (select representante from clientes where ativo = 1 and empresa = rentabilidade.empresa and cod_cli = rentabilidade.cliente) as cod_rep, cod_sp as prod, (select descricao from sp_dados where ativo = 1 and empresa = rentabilidade.empresa and cod_sp = rentabilidade.cod_sp) as descricao, (select grupo from sp_dados where ativo = 1 and empresa = rentabilidade.empresa and cod_sp = rentabilidade.cod_sp) as grupo, quant as quant, (select unidade from sp_dados where ativo = 1 and empresa = rentabilidade.empresa and cod_sp = rentabilidade.cod_sp) as und, (select icms from regioes where ativo = 1 and empresa = rentabilidade.empresa and cod_reg = (select regiao from clientes where ativo = 1 and empresa = rentabilidade.empresa and cod_cli = rentabilidade.cliente)) as icms, (select comissao_padrao from representantes where ativo = 1 and empresa = rentabilidade.empresa and cod_rep = (select representante from clientes where ativo = 1 and empresa = rentabilidade.empresa and cod_cli = rentabilidade.cliente)) as comissao_padrao, vlr_nf, fechado, clientevar, regiaovar, representantevar, descricaovar, undvar, vlr_padr, icms as icmsvar, comis, marg, data_ped, faturado from rentabilidade where empresa = ".$_SESSION['UsuarioEmpresa']." and ativo = 1".$buscaFaturados.$textBusca.$buscarep.$buscacli.$buscaequipe.$buscaCodProd;
		// echo $stringPesqP1;
		$stringPesqP2 = " ".$buscadtped.$buscadtfat." order by data_cri";
		echo "<input type=\"hidden\" value=\"".str_replace(" ","%esp",str_replace(",","%virg",$stringPesqP1))."\" id=\"hiddenPesqP1\" />";
		echo "<input type=\"hidden\" value=\"".str_replace(" ","%esp",str_replace(",","%virg",$stringPesqP2))."\" id=\"hiddenPesqP2\" />";
		$stringPesquisa = $stringPesqP1.$stringPesqP2;
		// echo $stringPesquisa;
		//conta os registros para paginação
		// $sql_conta = $sql->query($stringPesquisa);
		// $quantreg = mysqli_num_rows($sql_conta);
		//lista
		if ($buscaFaturados!=" and faturados = '-1'" and ($buscadtped!="" or $buscadtfat!="" or $textBusca!="" or $buscaCodProd!="" or ($fFaturados=="" and $fAbertos=="0"))) {
			$listMp = $sql->query($stringPesquisa/*." limit $inicial, $numreg"*/);
			$quantreg = mysqli_num_rows($listMp);
		}
			$_SESSION['quantfechamento'] = $quantreg;
		$linhasMp = /*mysqli_num_rows($listMp)*/$numreg;
		$impostos = mysqli_fetch_array($sql->query("select percentual from outros_impostos where empresa = ".$_SESSION['UsuarioEmpresa']." and ativo = 1"));
		$custo_fixo = mysqli_fetch_array($sql->query("select percentual from custo_fixo where empresa = ".$_SESSION['UsuarioEmpresa']." and ativo = 1"));
		$margem = mysqli_fetch_array($sql->query("select percentual from margem_fixa where empresa = ".$_SESSION['UsuarioEmpresa']." and ativo = 1"));
		for ($i=1;$i<=$quantreg;$i++) {
			$linha = mysqli_fetch_array($listMp);
			if ($linha['fechado']==0) {
				$fechado[] = 0;
				$cod_rent[] = $linha['cod_rent'];
				if ($linha['faturado']==1) $data_nf_array[] = $linha['data_nf']; else $data_nf_array[] = "-";
				$cliente[] = $linha['cliente'];
				$regiao[] = $linha['regiao'];
				$representante[] = $linha['representante'];
				$prod[] = $linha['prod'];
				$descricao[] = $linha['descricao'];
				$quant[] = $linha['quant'];
				$und[] = $linha['und'];
				$comissPesq = $sql->query("select comiss from representantes_grupos where cod_rep = '".$linha['cod_rep']."' and ativo = 1 and empresa = '".$_SESSION['UsuarioEmpresa']."' and cod_grupo_prod = '".$linha['grupo']."'");
				if (mysqli_num_rows($comissPesq)>0) {
					$comissGrupo = mysqli_fetch_array($comissPesq);
					$comissao = $comissGrupo['comiss'];
				} else {
					$comissao = $linha['comissao_padrao'];
				}
				$comissao_padrao[] = $comissao;
				$vlrpadraovar = somaSp($linha['prod'])/((100-($linha['icms']+$impostos['percentual']+$comissao+$custo_fixo['percentual']+$margem['percentual']))/100);
				$vlrpadrao[] = $vlrpadraovar;
				$vlrmp[] = somaMP($linha['prod'])*$linha['quant'];
				$vlr_nf[] = $linha['vlr_nf'];
				$icms[] = $linha['icms'];
				$total_nf[] = $linha['quant']*$linha['vlr_nf'];
				// $contribuicao[] = ($linha['quant']*$linha['vlr_nf'])-($linha['quant']*$vlrpadraovar);
				$margem_guia = ((($linha['vlr_nf']*(100-($linha['icms']+$impostos['percentual']+$comissao)))-($vlrpadraovar*(100-($linha['icms']+$impostos['percentual']+$comissao+$margem['percentual']))))/$linha['vlr_nf'])-$margem['percentual'];
				$margem_item[] = $margem_guia;
				$contribuicao[] = ($linha['quant']*$linha['vlr_nf'])*($margem_guia/100);
				if ($linha['data_ped']!="0000-00-00") $data_ped[] = $linha['data_ped']; else $data_ped[] = "-";
				$faturado[] = $linha['faturado'];
			}
			if ($linha['fechado']==1) {
				$fechado[] = 1;
				$cod_rent[] = $linha['cod_rent'];
				if ($linha['faturado']==1) $data_nf_array[] = $linha['data_nf']; else $data_nf_array[] = "-";
				$cliente[] = $linha['clientevar'];
				$regiao[] = $linha['regiaovar'];
				$representante[] = $linha['representantevar'];
				$prod[] = $linha['prod'];
				$descricao[] = $linha['descricaovar'];
				$quant[] = $linha['quant'];
				$und[] = $linha['undvar'];
				$vlrpadraovar = $linha['vlr_padr'];
				$vlrpadrao[] = $vlrpadraovar;
				$vlrmp[] = somaMP($linha['prod'])*$linha['quant'];
				$vlr_nf[] = $linha['vlr_nf'];
				$icms[] = $linha['icmsvar'];
				$comissao_padrao[] = $linha['comis'];
				$total_nf[] = $linha['quant']*$linha['vlr_nf'];
				// $contribuicao[] = ($linha['quant']*$linha['vlr_nf'])-($linha['quant']*$vlrpadraovar);
				$margem_item[] = $linha['marg'];
				$contribuicao[] = ($linha['quant']*$linha['vlr_nf'])*($linha['marg']/100);
				if ($linha['data_ped']!="0000-00-00") $data_ped[] = $linha['data_ped']; else $data_ped[] = "-";
				$faturado[] = $linha['faturado'];
			}
		}
		
		//divs filtro
			//filtro cliente
			$filtroCliPesq = $sql->query("select cod_cli, cliente from clientes where ativo = 1 and empresa = ".$_SESSION['UsuarioEmpresa']." order by cliente");
			echo "<div class=\"filtro_cliente\" id=\"divfiltrocli\">";
			if (count($fcli)>0) $checkedCli = "checked"; else $checkedCli = "";
			echo "<div style=\"width: 100%; background-color: #FFFFFF;\"><input type=\"checkbox\" name=\"tudoCli\" onclick=\"verificaStatusCli(this,'checkCli')\" $checkedCli/><b><i>CLIENTES</i></b></div>\n";
			for ($cli=1;$cli<=mysqli_num_rows($filtroCliPesq);$cli++) {
				$filtroCli = mysqli_fetch_array($filtroCliPesq);
				if ($fcli[$filtroCli['cod_cli']]==$filtroCli['cod_cli']) echo "<input type=\"checkbox\" name=\"filtrocli[".$filtroCli['cod_cli']."]\" value=\"".$filtroCli['cod_cli']."\" id=\"checkCli\" checked>".$filtroCli['cliente']."</input><br>\n";
				else echo "<input type=\"checkbox\" name=\"filtrocli[".$filtroCli['cod_cli']."]\" value=\"".$filtroCli['cod_cli']."\" id=\"checkCli\">".$filtroCli['cliente']."</input><br>\n";
			}
			echo "</div>";
			//filtro data pedido
			echo "<div class=\"filtro_dtped\" id=\"divfiltrodtped\">";
				echo "<label for=\"txDtpedIni\">Filtrar: De</label>";
				echo "<input type=\"text\" name=\"dtpedini\" id=\"txDtpedIni\" value=\"".$fDtPed['inicial']."\" style=\"height:18px; width: 80px; bottom:-1px; position: relative;\" onKeyPress=\"return submitenter(this,event)\" onclick=\"this.select()\" maxlength=\"10\" onKeyUp=\"barra(this)\"/>";
				echo " <label for=\"txDtpedFin\">até</label>";
				echo "<input type=\"text\" name=\"dtpedfin\" id=\"txDtpedFin\" value=\"".$fDtPed['final']."\" style=\"height:18px; width: 80px; bottom:-1px; position: relative;\" onKeyPress=\"return submitenter(this,event)\" onclick=\"this.select()\" maxlength=\"10\" onKeyUp=\"barra(this)\"/>";
				echo "<img id=\"confirma\" src=\"images/filtrar.gif\" onclick=\"document.getElementById('formpesq').submit()\">";
				echo "<img id=\"limpar\" src=\"images/limpar.png\" onclick=\"document.getElementById('txDtpedIni').value=''; document.getElementById('txDtpedFin').value=''; \">";
			echo "</div>";
			//filtro data faturamento
			echo "<div class=\"filtro_dtfat\" id=\"divfiltrodtfat\">";
				echo "<label for=\"txDtfatIni\">Filtrar: De</label>";
				echo "<input type=\"text\" name=\"dtfatini\" id=\"txDtfatIni\" value=\"".$fDtFat['inicial']."\" style=\"height:18px; width: 80px; bottom:-1px; position: relative;\" onKeyPress=\"return submitenter(this,event)\" onclick=\"this.select()\" maxlength=\"10\" onKeyUp=\"barra(this)\"/>";
				echo " <label for=\"txDtfatFin\">até</label>";
				echo "<input type=\"text\" name=\"dtfatfin\" id=\"txDtfatFin\" value=\"".$fDtFat['final']."\" style=\"height:18px; width: 80px; bottom:-1px; position: relative;\" onKeyPress=\"return submitenter(this,event)\" onclick=\"this.select()\" maxlength=\"10\" onKeyUp=\"barra(this)\"/>";
				echo "<img id=\"confirma\" src=\"images/filtrar.gif\" onclick=\"document.getElementById('formpesq').submit()\">";
				echo "<img id=\"limpar\" src=\"images/limpar.png\" onclick=\"document.getElementById('txDtfatIni').value=''; document.getElementById('txDtfatFin').value=''; \">";
			echo "</div>";
			//filtro representante
			echo "<div class=\"filtro_representante\" id=\"divfiltrorep\">";
			if (count($frep)>0) $checkedRep = "checked"; else $checkedRep = "";
			echo "<div style=\"width: 100%; background-color: #FFFFFF;\"><input type=\"checkbox\" name=\"tudoRep\" onclick=\"verificaStatusRep(this,'checkRep')\" $checkedRep/><b><i>REPRESENTANTES</i></b></div>\n";
			$filtroEquipePesq = $sql->query("select cod_equipe, equipe from equipes_de_venda where ativo = 1 and empresa = '".$_SESSION['UsuarioEmpresa']."' order by equipe");
			if (mysqli_num_rows($filtroEquipePesq)>0) {
				echo "<div style=\"padding-left: 20px;\"><span onclick=\"mostradiv('divfiltroequi');\" style=\"cursor: pointer;\">+ Por Equipe</span></div>";
				if (count($fequipe)>0) echo "<div id=\"divfiltroequi\" style=\"position: relative; padding-left: 20px; display: block;\">";
				else echo "<div id=\"divfiltroequi\" style=\"position: relative; padding-left: 20px; display: none;\">";
				for ($equi=0;$equi<mysqli_num_rows($filtroEquipePesq);$equi++) {
					$filtroEquipe = mysqli_fetch_array($filtroEquipePesq);
					if (isset($fequipe) and in_array($filtroEquipe['cod_equipe'],$fequipe)!=0) $checkedEqui = " checked"; else $checkedEqui = "";
					echo "<input type=\"checkbox\" name=\"filtroEquipe[]\" value=\"".$filtroEquipe['cod_equipe']."\"$checkedEqui>".$filtroEquipe['equipe']."</input><br>\n";
				}
				echo "</div>";
			}
			$filtroRepPesq = $sql->query("select cod_rep, nome from representantes where ativo = 1 and empresa = '".$_SESSION['UsuarioEmpresa']."' order by nome");
			for ($rep=1;$rep<=mysqli_num_rows($filtroRepPesq);$rep++) {
				$filtroRep = mysqli_fetch_array($filtroRepPesq);
				if ($frep[$filtroRep['cod_rep']]==$filtroRep['cod_rep']) echo "<input type=\"checkbox\" name=\"filtrorep[".$filtroRep['cod_rep']."]\" value=\"".$filtroRep['cod_rep']."\" id=\"checkRep\" checked>".$filtroRep['nome']."</input><br>\n";
				else echo "<input type=\"checkbox\" name=\"filtrorep[".$filtroRep['cod_rep']."]\" value=\"".$filtroRep['cod_rep']."\" id=\"checkRep\">".$filtroRep['nome']."</input><br>\n";
			}
			echo "</div></form>";
		//fim divs filtro
		
		//ordenador
		if ($order!="" and $quantreg>0) {
			if ($order=="data" and $sent=="desc") {
				array_multisort($data_nf_array, SORT_ASC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th1').className = \"headerSortDown\";</script>";}
			if ($order=="data" and $sent=="asc") {
				array_multisort($data_nf_array, SORT_DESC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th1').className = \"headerSortUp\";</script>";}
			if ($order=="cliente" and $sent=="desc") {
				array_multisort($cliente, SORT_ASC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th2').className = \"headerSortDown\";</script>";}
			if ($order=="cliente" and $sent=="asc") {
				array_multisort($cliente, SORT_DESC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th2').className = \"headerSortUp\";</script>";}
			if ($order=="regiao" and $sent=="desc") {
				array_multisort($regiao, SORT_ASC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th3').className = \"headerSortDown\";</script>";}
			if ($order=="regiao" and $sent=="asc") {
				array_multisort($regiao, SORT_DESC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th3').className = \"headerSortUp\";</script>";}
			if ($order=="representante" and $sent=="desc") {
				array_multisort($representante, SORT_ASC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th4').className = \"headerSortDown\";</script>";}
			if ($order=="representante" and $sent=="asc") {
				array_multisort($representante, SORT_DESC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th4').className = \"headerSortUp\";</script>";}
			if ($order=="prod" and $sent=="desc") {
				array_multisort($prod, SORT_ASC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th5').className = \"headerSortDown\";</script>";}
			if ($order=="prod" and $sent=="asc") {
				array_multisort($prod, SORT_DESC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th5').className = \"headerSortUp\";</script>";}
			if ($order=="descricao" and $sent=="desc") {
				array_multisort($descricao, SORT_ASC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th6').className = \"headerSortDown\";</script>";}
			if ($order=="descricao" and $sent=="asc") {
				array_multisort($descricao, SORT_DESC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th6').className = \"headerSortUp\";</script>";}
			if ($order=="quant" and $sent=="desc") {
				array_multisort($quant, SORT_ASC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th7').className = \"headerSortDown\";</script>";}
			if ($order=="quant" and $sent=="asc") {
				array_multisort($quant, SORT_DESC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th7').className = \"headerSortUp\";</script>";}
			if ($order=="und" and $sent=="desc") {
				array_multisort($und, SORT_ASC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th8').className = \"headerSortDown\";</script>";}
			if ($order=="und" and $sent=="asc") {
				array_multisort($und, SORT_DESC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th8').className = \"headerSortUp\";</script>";}
			if ($order=="vlr_padr" and $sent=="desc") {
				array_multisort($vlrpadrao, SORT_ASC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th9').className = \"headerSortDown\";</script>";}
			if ($order=="vlr_padr" and $sent=="asc") {
				array_multisort($vlrpadrao, SORT_DESC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th9').className = \"headerSortUp\";</script>";}
			if ($order=="vlr_nf" and $sent=="desc") {
				array_multisort($vlr_nf, SORT_ASC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th10').className = \"headerSortDown\";</script>";}
			if ($order=="vlr_nf" and $sent=="asc") {
				array_multisort($vlr_nf, SORT_DESC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th10').className = \"headerSortUp\";</script>";}
			if ($order=="icms" and $sent=="desc") {
				array_multisort($icms, SORT_ASC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th11').className = \"headerSortDown\";</script>";}
			if ($order=="icms" and $sent=="asc") {
				array_multisort($icms, SORT_DESC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th11').className = \"headerSortUp\";</script>";}
			if ($order=="comis" and $sent=="desc") {
				array_multisort($comissao_padrao, SORT_ASC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th12').className = \"headerSortDown\";</script>";}
			if ($order=="comis" and $sent=="asc") {
				array_multisort($comissao_padrao, SORT_DESC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th12').className = \"headerSortUp\";</script>";}
			if ($order=="total" and $sent=="desc") {
				array_multisort($total_nf, SORT_ASC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th13').className = \"headerSortDown\";</script>";}
			if ($order=="total" and $sent=="asc") {
				array_multisort($total_nf, SORT_DESC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th13').className = \"headerSortUp\";</script>";}
			if ($order=="vlr_contr" and $sent=="desc") {
				array_multisort($contribuicao, SORT_ASC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th14').className = \"headerSortDown\";</script>";}
			if ($order=="vlr_contr" and $sent=="asc") {
				array_multisort($contribuicao, SORT_DESC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th14').className = \"headerSortUp\";</script>";}
			if ($order=="margem" and $sent=="desc") {
				array_multisort($margem_item, SORT_ASC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th15').className = \"headerSortDown\";</script>";}
			if ($order=="margem" and $sent=="asc") {
				array_multisort($margem_item, SORT_DESC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th15').className = \"headerSortUp\";</script>";}
			if ($order=="dataped" and $sent=="desc") {
				array_multisort($data_ped, SORT_ASC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th0').className = \"headerSortDown\";</script>";}
			if ($order=="dataped" and $sent=="asc") {
				array_multisort($data_ped, SORT_DESC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $vlrmp);
				echo "<script>document.getElementById('th0').className = \"headerSortUp\";</script>";}
			if ($order=="vlr_mp" and $sent=="desc") {
				array_multisort($vlrmp, SORT_ASC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $data_ped);
				echo "<script>document.getElementById('th16').className = \"headerSortDown\";</script>";}
			if ($order=="vlr_mp" and $sent=="asc") {
				array_multisort($vlrmp, SORT_DESC, SORT_REGULAR, $cod_rent, $data_nf_array, $cliente, $regiao, $representante, $prod, $descricao, $quant, $und, $vlrpadrao, $vlr_nf, $icms, $comissao_padrao, $total_nf, $contribuicao, $margem_item, $fechado, $data_ped, $faturado, $data_ped);
				echo "<script>document.getElementById('th16').className = \"headerSortUp\";</script>";}
		}
		//final da ordenação
		
		for ($i=$inicial;$i<($inicial+$numreg);$i++) {
				$alturalinha = 15;
			if ($cod_rent[$i]!="" and $fechado[$i]==0)	echo "<tr height=$alturalinha class=\"linhaLink\" onclick=\"abrirPopup('add_rent.php?cod_rent=".$cod_rent[$i]."',1000,100);\">\n";
			else if ($cod_rent[$i]!="" and $fechado[$i]==1) echo "<tr height=$alturalinha class=\"linhaLinkfechado\" onclick=\"alert('Rentabilidade fechada. Não é possível fazer alterações!');\">\n";
			else echo "<tr height=$alturalinha>\n";
				if ($data_ped[$i]!="" and $data_ped[$i]!="-") $data_ped_user = date('d/m/y',strtotime($data_ped[$i]));
				else if ($data_ped[$i]=="-") $data_ped_user = "-";
				else $data_ped_user = "";
				echo "<td $fundodtped><center>".$data_ped_user."</td>";
				if ($data_ped_user!="") {
					?>
					<style>
						.seta {
							width: 18px;
							height: 10px;
							float: right;
						}
						.cliente {
							width: 140px;
							float: left;
						}
						.regiao {
							width: 30px;
							float: left;
							overflow: hidden;
						}
						.representante {
							width: 120px;
							float: left;
						}
						.descricao {
							width: 238px;
							float: left;
						}
						@media (max-width: 1318px) {
							.seta{width: 8px;}
							.cliente {width: 140px;}
							.regiao {width: 30px;}
							.representante {width: 126px;}
							.descricao {width: 229px;}
						}
					</style>
					<?
					echo "<td $fundocli><div class=\"cliente\" id=\"cliente$i\">".$cliente[$i]."</div>";
					echo "<div class=\"seta\" id=\"clienteseta$i\"></div></td>";
					echo "<td><div class=\"regiao\" id=\"regiao$i\">".$regiao[$i]."</div>";
					echo "<div class=\"seta\" id=\"regiaoseta$i\"></div></td>";
					echo "<td $fundorep><div class=\"representante\" id=\"representante$i\">".$representante[$i]."</div>";
					echo "<div class=\"seta\" id=\"representanteseta$i\"></div></td>";
					echo "<td>".$prod[$i]."</td>";
					echo "<td><div class=\"descricao\" id=\"descricao$i\">".$descricao[$i]."</div>";
					echo "<div class=\"seta\" id=\"descricaoseta$i\"></div></td>";
					echo "<td>".number_format($quant[$i],2,".","")."</td>";
					echo "<td>".$und[$i]."</td>";
			 		// $vlrpadrao = somaSp($linha['prod'])/((100-($linha['icms']+$impostos['percentual']+$linha['comissao_padrao']+$custo_fixo['percentual']+$margem['percentual']))/100);
					echo "<td>".number_format($vlrpadrao[$i],4,".","")."</td>";
					echo "<td>".number_format($vlr_nf[$i],4,".","")."</td>";
					echo "<td>".number_format($icms[$i],4,".","")."%</td>";
					echo "<td>".number_format($comissao_padrao[$i],4,".","")."%</td>";
					$subTotalFat = $subTotalFat + $total_nf[$i];
					echo "<td align=\"right\">".number_format($total_nf[$i],4,".","")."</td>";
					$subTotalCont = $subTotalCont + $contribuicao[$i];
					echo "<td align=\"right\">".number_format($vlrmp[$i],4,".","")."</td>";
					$subTotalMp = $subTotalMp + $vlrmp[$i];
					echo "<td align=\"right\">".number_format($contribuicao[$i],4,".","")."</td>";
			// 		$margem_item = (($linha['vlr_nf']*(100-($linha['icms']+$impostos['percentual']+$linha['comissao_padrao'])))-($vlrpadrao*(100-($linha['icms']+$impostos['percentual']+$linha['comissao_padrao']+$margem['percentual']))))/$linha['vlr_nf'];
					echo "<td align=\"right\">".number_format($margem_item[$i],2,".","")."%</td>";
					if ($data_nf_array[$i]!="" and $data_nf_array[$i]!="-") $data_nf = date('d/m/y',strtotime($data_nf_array[$i]));
					else if ($data_nf_array[$i]=="-") $data_nf = "-";
					else $data_nf = "";
					echo "<td $fundodtfat><center>".$data_nf."</td>";
				} else {
					for ($j=1;$j<17;$j++){
						echo "<td></td>";
					}
				}
			echo "</tr>\n";
		}
		// echo "<script>destacarQuebras($linhasMp)</script>";
		echo "<script>destacarQuebras($inicial,$inicial+$numreg)</script>";
	echo "</tbody>\n";
		echo "<tfoot style=\"font-size:11px;\">\n";
			echo "<tr style=\"background-color:#DCDCDC;\">\n";
				echo "<td colspan=\"12\" align=\"right\"><b>SubTotal:</b></td>\n";
				echo "<td align=\"right\"><b>".number_format($subTotalFat,2,",",".")."</b></td>\n";
				echo "<td align=\"right\"><b>".number_format($subTotalMp,2,",",".")."</b></td>\n";
				echo "<td align=\"right\"><b>".number_format($subTotalCont,2,",",".")."</b></td>\n";
				echo "<td colspan=\"2\"></td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
				echo "<td colspan=\"12\" align=\"right\"></td>\n";
				echo "<td align=\"right\"></td>\n";
				echo "<td align=\"right\"><b>".number_format((($subTotalMp/$subTotalFat)*100),3,",","")." %</b></td>\n";
				echo "<td align=\"right\"><b>".number_format((($subTotalCont/$subTotalFat)*100),3,",","")." %</b></td>\n";
				echo "<td colspan=\"2\"></td>\n";
			echo "</tr>\n";
			//TOTALIZADORES
			for ($t=0;$t<$quantreg;$t++) {
				// $total = mysqli_fetch_array($sql_conta);
				$totalFat = $totalFat + $total_nf[$t];
				$totalMp = $totalMp + $vlrmp[$t];
				// $vlrpadraoTot = somaSp($total['prod'])/((100-($total['icms']+$impostos['percentual']+$total['comissao_padrao']+$custo_fixo['percentual']+$margem['percentual']))/100);
				$totalCont = $totalCont + /*(($total['quant']*$total['vlr_nf'])-($total['quant']*$vlrpadraoTot))*/$contribuicao[$t];
			}
			echo "<tr style=\"background-color:#DCDCDC;\">\n";
				echo "<td colspan=\"12\" align=\"right\"><b>Total:</b></td>\n";
				echo "<td align=\"right\"><b>".number_format($totalFat,2,",",".")."</b></td>\n";
				echo "<td align=\"right\"><b>".number_format($totalMp,2,",",".")."</b></td>\n";
				echo "<td align=\"right\"><b>".number_format($totalCont,2,",",".")."</b></td>\n";
				echo "<td colspan=\"2\"></td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
				echo "<td colspan=\"12\" align=\"right\"></td>\n";
				echo "<td align=\"right\"></td>\n";
				echo "<td align=\"right\"><b>".number_format((($totalMp/$totalFat)*100),3,",","")." %</b></td>\n";
				echo "<td align=\"right\"><b>".number_format((($totalCont/$totalFat)*100),3,",","")." %</b></td>\n";
				echo "<td colspan=\"2\"></td>\n";
			echo "</tr>\n";
		echo "</tfoot>\n";
	echo "</table>\n";
	echo "</div>";
		
	echo "<div class=\"larguraAdap\" style=\"position: relative;\">";
		echo "<div id=\"pageNav\" align=\"center\">";
			if ($quantreg!=0) include("paginacao.php");
		echo "</div>";
		// echo "<div style=\"float: right; width: 300px; padding: 5px; position: absolute; top: 0px; right: 0px;\" align=\"right\"><table style=\"border: 1px solid; margin-right: 90px; text-align: right; font-size:12px\">";
			// echo "<tr><td><b>SubTotal:</b></td><td width=\"82px\"><b>".number_format($subTotalFat,2,",",".")."</b></td><td width=\"82px\"><b>".number_format($subTotalCont,2,",",".")."</b></td></tr>";
			
			// echo "<tr><td><b>Total:</b></td><td><b>".number_format($totalFat,2,",",".")."</b></td><td><b>".number_format($totalCont,2,",",".")."</b></td></tr></table>";
			// echo "</table>";
		// echo "</div>";
	echo "</div>";
	
	echo "</div>\n";

echo "</div>";

		//SERIALIZA RESULTADO
		$fechamento['fechado'] = $fechado;
		$fechamento['cod_rent'] = $cod_rent;
		$fechamento['data_nf_array'] = $data_nf_array;
		$fechamento['cliente'] = $cliente;
		$fechamento['regiao'] = $regiao;
		$fechamento['representante'] = $representante;
		$fechamento['prod'] = $prod;
		$fechamento['descricao'] = $descricao;
		$fechamento['quant'] = $quant;
		$fechamento['und'] = $und;
		$fechamento['vlrpadrao'] = $vlrpadrao;
		$fechamento['vlr_nf'] = $vlr_nf;
		$fechamento['icms'] = $icms;
		$fechamento['comissao_padrao'] = $comissao_padrao;
		$fechamento['total_nf'] = $total_nf;
		$fechamento['contribuicao'] = $contribuicao;
		$fechamento['margem_item'] = $margem_item;
		$fechamento['data_ped'] = $data_ped;
		$fechamento['faturado'] = $faturado;
		$_SESSION['relatorio'] = serialize($fechamento);
		// echo $_SESSION['relatorio'];
		//FINAL SERIALIZA RESULTADO
?>
<!-- div onde será criados os links da paginação -->
<!--<div id="pageNav"></div>-->
<script type="text/javascript">
// $(function() {	
// 	$("#tb1").tablesorter();
// });	
// var tabela = 'tb1';
// var registros = 20;
// var pager = new Pager(tabela, registros);
// pager.init();
// pager.showPageNav('pager', 'pageNav');
// // var linhas = document.getElementById(tabela).rows.length;
// // var paginas = Math.ceil(linhas/registros);
// // pager.showPage(paginas);
// pager.showPage(1);
</script>
</body>
</html>