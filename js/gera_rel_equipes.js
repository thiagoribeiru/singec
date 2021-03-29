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
    $('#txDtfatIni, #txDtfatFin').focus(function(){
        $('#txDtpedIni, #txDtpedFin').val("");
    });
    $('#txDtpedIni, #txDtpedFin').focus(function(){
        $('#txDtfatIni, #txDtfatFin').val("");
    });
    $('#txDtfatIni, #txDtfatFin, #txDtpedIni, #txDtpedFin').keyup(function(){
        var campo = new $(this);
        var valor = campo.val();
        if (valor.length==2 || valor.length==5) {
            campo.val(valor+"/");
        }
    });
    $('#txDtfatIni, #txDtfatFin, #txDtpedIni, #txDtpedFin').submit(function(){
        return false;
    });
    $('#divfiltrodtfat #confirma').click(function(){
        //verifica se os dois campos estão preenchidos
        if ($('#txDtfatIni').val()!="" && $('#txDtfatFin').val()!="") {
            dt1 = document.getElementById("txDtfatIni").value;
    		dt2 = document.getElementById("txDtfatFin").value;
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
    		
    		if (total1<=30) {
                var valores = $('#formpesq').serialize();
                var url_form = $('#formpesq').attr('action');
                $.ajax({
                	timeout:540000,
                    method: "GET",
                    url: url_form,
                    data: valores+"&rel=faturamento",
                    beforeSend: function(){
                        $('#relatoriopdf').html('<img src="images/loader.gif" style="width: 32px; margin-top: 242px; margin-left: 451px;">');
                    },
                    success: function(data){
                        $('#relatoriopdf').html(data);
                    },
                    error: function() {
                    	alert("ERRO!");
                    	$('#relatoriopdf').html("");
                    }
                });
    		} else {
    			alert("Relatório de no máximo 30 dias!");
    		}
        } else {
            alert('Os dois campos precisam estar preenchidos!');
        }
    });
    $('#divfiltrodtped #confirma').click(function(){
        //verifica se os dois campos estão preenchidos
        if ($('#txDtpedIni').val()!="" && $('#txDtpedFin').val()!="") {
            dt1 = document.getElementById("txDtpedIni").value;
    		dt2 = document.getElementById("txDtpedFin").value;
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
    		
    		if (total1<=30) {
                var valores = $('#formpesq').serialize();
                var url_form = $('#formpesq').attr('action');
                $.ajax({
                    method: "GET",
                    url: url_form,
                    data: valores+"&rel=entrada",
                    beforeSend: function(){
                        $('#relatoriopdf').html('<img src="images/loader.gif" style="width: 32px; margin-top: 242px; margin-left: 451px;">');
                    },
                    success: function(data){
                        $('#relatoriopdf').html(data);
                    }
                });
    		} else {
    			alert("Relatório de no máximo 30 dias!");
    		}
        } else {
            alert('Os dois campos precisam estar preenchidos!');
        }
    });
});