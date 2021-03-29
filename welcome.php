<div id="fundo_fumace">
	<div id="load_img">
		<img src="images/3.png"><br>
		<span>Carregando...aguarde!</span>
	</div>
</div>
<?
if ($manuVar==1) {
	$styleWelcome = "style=\"background:#FF9673;\"";
	$check = 'checked';
	?>
	<style>
	body {
		background-color: #FFFFBF;
		background-image: url("images/manutencao.png");
	}
	</style>
	<?
} else {
	$styleWelcome = '';
	$check = '';
}
echo "<div id=\"welcome\" $styleWelcome>\n";
    echo "<div id=\"menu_horizontal\">\n";
        echo "<ul class=\"wmenu\">\n";
            echo "<li><a href=\"index.php\">Início</a></li>\n";
            echo "<li><a href=#>Cadastro</a>\n";
                echo "<ul class=\"wsubmenu-1\">\n";
                    echo "<li><a href=\"mp.php\">Matéria Prima</a></li>\n";
                    echo "<li><a href=\"pr.php\">Processos</a></li>\n";
                    echo "<li><a href=\"sp.php\">Sub-Produtos</a></li>\n";
                echo "</ul>\n";
            echo "</li>\n";
            echo "<li><a href=#>Comercial</a>\n";
                echo "<ul class=\"wsubmenu-1\">\n";
                    echo "<li><a href=\"clientes.php\">Clientes</a></li>\n";
                    echo "<li><a href=\"representantes.php\">Representantes</a></li>\n";
                echo "</ul>\n";
            echo "</li>\n";
            echo "<li><a href=#>Configurações</a>\n";
                echo "<ul class=\"wsubmenu-1\">\n";
                	echo "<li><a href=\"grupos_de_produto.php\">Grupos de Produto</a></li>\n";
                    echo "<li><a href=\"regioes.php\">ICMS/Regiões</a></li>\n";
                    echo "<li><a href=\"moedas.php\">Moedas</a></li>\n";
                    echo "<li><a href=\"parametros.php\">Parâmetros</a></li>\n";
                    echo "<li><a href=\"unidades.php\">Unidades</a></li>\n";
                    echo "<li><a href=\"usuarios.php\">Usuários</a></li>\n";
                echo "</ul>\n";
            echo "</li>\n";
            echo "<li><a href=#>Custos</a>\n";
                echo "<ul class=\"wsubmenu-1\">\n";
                	echo "<li class=\"submenu-x\"><a href=#>Relatórios</a>\n";
	                	echo "<ul class=\"wsubmenu-2\">\n";
	                		echo "<li><a href=\"rel_comiss_representantes.php\">Comissão/Representante</a></li>\n";
	                		// echo "<li><a href=#>Custo Oper./Equipe Venda</a></li>\n";
	                		// echo "<li><a href=#>Custo Oper./Representante</a></li>\n";
	                		echo "<li><a href=\"rel_rent_equipes.php\">Rent./Equipe Venda</a></li>\n";
	                	echo "</ul>\n";
	                echo "</li>\n";
	                echo "<li><a href=\"rel_automaticos.php\">Relatórios Automáticos</a></li>\n";
                    echo "<li><a href=\"rentabilidade.php\">Rentabilidade</a></li>\n";
                echo "</ul>\n";
            echo "</li>\n";
            echo "<li><a href=\"javascript: abrirContato();\">Fale Conosco</a></li>\n";
        echo "</ul>\n";
    echo "</div>\n";
    echo "<div style=\"margin-top: 2px; float: right;\">Olá, ".$_SESSION['UsuarioNome'];
	if ($_SESSION['UsuarioEmpresaNome']=='Todas') {
	?>
		<div style='position:relative; display:inline-block;'>
			<img src='images/settings.ico' id='ceo_settings' style='width:18px; vertical-align:middle; cursor:pointer;'>
			<div id="ceo_settings_div">
				<input type='checkbox' value='1' id='manutencao' style='line-height:20px;vertical-align:-10%;' <?echo $check?>>
					Modo de Manutenção
				</input>
			</div>
		</div>
	<?
	}
	echo " - <a href=\"logout.php\">LogOut</a></div>\n";
echo "</div>\n";
?>
<div id="popup" class="popup">
	<a href="javascript: fecharContato();" title="Fechar" style="background:#000000; padding:08px; border-radius:50%; color:red; text-decoration:none; margin:-24px 0 0 0; float:right;"> [ x ] </a>
	<div class="popupInner">
		<form action="<?echo "enviamsg.php?url=".$_SERVER['REQUEST_URI'];?>" method="post" class="formpop">
			<p class="titulo">Fale Conosco</p>
			<p class="inform">Responderemos o seu e-mail o mais rápido possivel!</p>
			<label for="email">Seu E-mail</label>
			<input type="mail" name="email" id="email" maxlength="30" />
			<label for="nome">Nome</label>
			<input type="text" name="nome" id="nome" maxlength="30" />
			<label for="assunto">Assunto</label>
			<input type="mail" name="assunto" id="assunto" maxlength="30" />
			<label for="texto">Texto</label>
			<textarea name="texto" id="texto" cols="35" rows="6"></textarea>
			<input type="submit" value="Enviar"/>
		</form>
	</div>
</div>
<script>
	$(function(){
	  var $form_inputs =   $('form input');
	  var $rainbow_and_border = $('.rain, .border');
	  /* Used to provide loping animations in fallback mode */
	  $form_inputs.bind('focus', function(){
		$rainbow_and_border.addClass('end').removeClass('unfocus start');
	  });
	  $form_inputs.bind('blur', function(){
		$rainbow_and_border.addClass('unfocus start').removeClass('end');
	  });
	  $form_inputs.first().delay(800).queue(function() {
		$(this).focus();
	  });
	});
	function fecharContato(){
		document.getElementById('popup').style.display = 'none';
	}
	function abrirContato(){
		document.getElementById('popup').style.display = 'block';
		/* setTimeout ("fecharContato()", 454502); */
	}
	function manutencao() {
		var dados = {
			'funcao':'manutencao'
		};
		$.ajax({
			type: 'POST',
			url: 'funcoesAjax.php',
			data: dados,
			dataType: "json",
			beforeSend: function(){
				$('#fundo_fumace').show();
			},
			success: function(json){
				if (json.error>0) {
					alert(json.mensagem);
					$('#fundo_fumace').hide();
				} else {
					window.location.reload();
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
	$(document).ready(function(){
		$('#manutencao').click(function(){
			var check = this;
			if(check.checked==true) {
				var dec = confirm('Tem certeza que gostaria de ativar o modo de manutenção?');
				if (dec) {
					check.checked = true;
					manutencao();
				} else {
					check.checked = false;
				}
			} else {
				var dec = confirm('Tem certeza que gostaria de desativar o modo de manutenção?');
				if (dec) {
					check.checked = false;
					manutencao();
				} else {
					check.checked = true;
				}
			}
		});
		$('#ceo_settings').click(function(){
			$('#ceo_settings_div').toggle();
		});
	});
</script>