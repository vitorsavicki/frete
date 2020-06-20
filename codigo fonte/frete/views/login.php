<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Bootstrap Core CSS - Uses
        Bootswatch Flatly Theme: http://bootswatch.com/flatly/ -->
        <link href="template/css/bootstrap.min.css" rel="stylesheet">
        <!-- Custom CSS -->
        <link href="template/css/freelancer.css" rel="stylesheet">
        <!-- Custom Fonts -->
        <link href="template/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <link href="http://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
        <link href="http://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media
        queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file://
        -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        <link href="template/css/fileinput.css" media="all" rel="stylesheet" type="text/css" />
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <script src="template/js/fileinput.min.js" type="text/javascript"></script>
        <script src="template/js/fileinput_locale_pt-BR.js" type="text/javascript"></script>
        <script type="text/javascript" src="template/js/ajaxupload.3.5.js" ></script>
        <link rel="stylesheet" type="text/css" href="template/css/upload.css" />
        <script src="template/js/jquery.validate.min.js"></script>
        <script type="text/javascript" src="template/js/jquery.mask.min.js"></script>
        <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
        <script type="text/javascript" src="http://netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
        <script src="template/js/jquery.maskMoney.js" type="text/javascript"></script>
        <link rel="stylesheet" type="text/css" href="template/AdminLTE.min.css" />
        <script src="template/js/jquery.validate.min.js"></script>
        <script type="text/javascript">
        var valido = true;
		  function validar(){
		    var str = document.form.emailPes.value;
		    var filter = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
		    if(filter.test(str))
		      valido = true;
		    else{
		      
		      document.cadastro.email.focus();
		      valido = false;
		    }
		    return valido;
		  }

        	function fncRecuperarSenha()
			{
				if ($('#emailPes').val() == '')
				{	
					$(document).ready(function() {
						msg('campoEmail', 'Informe o e-mail que deseja redefinir a senha no campo Email.');
					});
				}
				else
				{	
					
					var  email =  $('#fEmail').val($('#emailPes').val()).val();
					var valido = validar(email);
					if(valido){
					$('input[type="password"][name="senhaPes"]').prop("required", false).change();
					
        			waitingDialog.show('Enviando Email');

					$("#formRecuperarSenha").submit();
					};

					return false;
				}
			}

			$(function() {
				$("#formRecuperarSenha").on("submit", function(e) {
					e.preventDefault();
					$.ajax({
						url: $(this).attr("action"),
						type: 'POST',
						data: $(this).serialize(),
						success: function(data) {
							 $(document).ready(function() {
							     $('input[type="password"][name="senhaPes"]').prop("required", true).change();
									msg('senhaEnviada', 'Confira seu email para recuperar sua senha!');
									setTimeout(function () {waitingDialog.hide();});
							});
							
						}
					});
				});
			});
			
        </script>
        <style>
        	@media (min-width: 768px) {
			    .omb_row-sm-offset-3 div:first-child[class*="col-"] {
			        margin-left: 25%;
			    }
			}
			
			.omb_login .omb_authTitle {
			    text-align: center;
				line-height: 300%;
			}
				
			.omb_login .omb_socialButtons a {
				color: white; // In yourUse @body-bg 
				opacity:0.9;
			}
			.omb_login .omb_socialButtons a:hover {
			    color: white;
				opacity:1;    	
			}
			.omb_login .omb_socialButtons .omb_btn-facebook {background: #3b5998;}
			.omb_login .omb_socialButtons .omb_btn-twitter {background: #00aced;}
			.omb_login .omb_socialButtons .omb_btn-google {background: #c32f10;}
			
			
			.omb_login .omb_loginOr {
				position: relative;
				font-size: 1.5em;
				color: #aaa;
				margin-top: 1em;
				margin-bottom: 1em;
				padding-top: 0.5em;
				padding-bottom: 0.5em;
			}
			.omb_login .omb_loginOr .omb_hrOr {
				background-color: #cdcdcd;
				height: 1px;
				margin-top: 0px !important;
				margin-bottom: 0px !important;
			}
			.omb_login .omb_loginOr .omb_spanOr {
				display: block;
				position: absolute;
				left: 50%;
				top: -0.6em;
				margin-left: -1.5em;
				background-color: white;
				width: 3em;
				text-align: center;
			}			
			
			.omb_login .omb_loginForm .input-group.i {
				width: 2em;
			}
			.omb_login .omb_loginForm  .help-block {
			    color: red;
			}
			
				
			@media (min-width: 768px) {
			    .omb_login .omb_forgotPwd {
			        text-align: right;
					margin-top:10px;
			 	}		
			}
        </style>
        <style>
            body { padding-top: 120px; }
        </style>
    </head>
    <?php require 'template/header.php'; ?>
    <body id="page-top">
    	<!-- Inicio Novo -->
    		<div class="container">
		    <div class="row">
		        <div class="row">
		            <div class="col-md-4 col-md-offset-4">
		                <div class="panel panel-default">
		                    <div class="panel-body">
		                        <div class="text-center">
		                          <h3 class="text-center">Acesso ao Sistema</h3>
		                            <div class="panel-body">
		                              
		                              <form  role="form" method="post" id="form" name="form" enctype="multipart/form-data"><!--start form--><!--add form action as needed-->
		                                <fieldset>
                        
                        <div class="form-group">
		                	<div class="input-group">
		                		<span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
		               			<input id="emailPes" name="emailPes" placeholder="Coloque seu e-mail" class="form-control" type="email" required="">
		                	</div>
		                </div>
		                
		                <div class="form-group">
		                	<div class="input-group">
		                		<span class="input-group-addon"><i class="glyphicon glyphicon-lock "></i></span>
		                		<input id="senhaPes" name="senhaPes" placeholder="Coloque sua Senha" class="form-control" type="password" required="">
		                	</div>
		                </div>
                            <div id="resposta"></div>
                            
                        <script>
						function clearAutofill() {
						    if ( navigator.userAgent.toLowerCase().indexOf('chrome') >= 0 ) {
						        $('input[autocomplete="off"]').each( function(){
						            $(this).val('');
						        });
						    }
						}
						setTimeout(clearAutofill,100);
						</script>
						
                           
			
                            <div class="row">
                               <div class="form-group col-xs-12">
                                	<div class="col-md-12 text-center">
                                   		<input type="hidden" name="controle" value="Login">
				                    	<input type="hidden" name="acao" value="login">
							      		<button type="submit" id="confirm" name="confirm" class="btn btn-primary">Confirmar</button>
                                   	 	<a href="?controle=Index&acao=Index" type="button" class="btn btn-danger" >Cancelar</a>  
                                	</div>
                            	</div>
                            </div>
                             <br>
                             <div class="row">
                                <div class="form-group col-xs-12">
                                    <div class="col-md-12 text-center">
                                    <button type="button" id="esqueciSenha" onclick="fncRecuperarSenha()" name="esqueciSenha" class="btn btn-warning btn-xs" >Esqueci minha senha</button>
                                </div>
                                </div>
                            </div>
                  			 </fieldset>
		                     </form><!--/end form-->
		                            	</div>
		                        	</div>
		                    	</div>
		                	</div>
		            	</div>
		        	</div>
	   			</div>
			</div>
    	<!-- Fim  Novo -->    
			    <br>
			    <br>
			    <br>	    	    
    	<div id="myModal1" class="modal fade" role="dialog">
			<div class="modal-dialog">
							    <!-- Modal content-->	
				<div class="modal-content">
					<div class="text-center">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Sucesso</h4>
					</div>
					</div>
					<div class="text-center">
					<div class="modal-body">
						<p>Cadastro concluido com sucesso.</p>
					 </div>
					 </div>
					<div class="text-center">
					<div class="modal-footer-center">
					 	<button type="submit" class="btn btn-primary" data-dismiss="modal">OK</button>
						<br>
						<br>
						<br>		      	
					</div>
					</div>
				</div>			
			</div>
		</div>
		
		<div id="myModal2" class="modal fade" role="dialog">
			<div class="modal-dialog">
							    <!-- Modal content-->	
				<div class="modal-content">
					<div class="text-center">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Sucesso</h4>
					</div>
					</div>
					<div class="text-center">
					<div class="modal-body">
						<p>Sua senha foi redefinida com sucesso.</p>
					 </div>
					 </div>
					<div class="text-center">
					<div class="modal-footer-center">
					 	<button type="submit" class="btn btn-primary" data-dismiss="modal">OK</button>
						<br>
						<br>
						<br>		      	
					</div>
					</div>
				</div>			
			</div>
		</div>
		
	
			
	 <form action="./?controle=Pessoa&acao=recuperarSenha" id="formRecuperarSenha" method="post">
		<input type="hidden" id="fEmail" name="fEmail" value="">
	 </form>
	 
		<br>
		<br>
    </body>
		<br>
		<br>
		<br>
		<script type="text/javascript">
		
				function msg(alerta, texto) {
			     var resposta = '';
			     $("#resposta").empty();
			     if (alerta === 'sucesso') {
			       resposta = "<div class='alert btn-success text-center' role='alert'>" +
			         "<a href='#' class='close' data-dismiss='alert' aria-label='Close'>&times;</a>" +
			         texto + "</div>";
			     } else if (alerta === 'atencao') {
			       resposta = "<div class='alert btn-warning text-center' role='alert'>" +
			         "<a href='#' class='close' data-dismiss='alert' aria-label='Close'>&times;</a>" +
			         texto + "</div>";
			     } else if (alerta === 'erro') {
			       resposta = "<div class='alert btn-danger text-center' role='alert'>" +
			         "<a href='#' class='close' data-dismiss='alert' aria-label='Close'>&times;</a>" +
			         texto + "</div>";
			     }
			     else if (alerta === 'login') {
			       resposta = "<div class='alert btn-danger text-center' role='alert'>" +
			         "<a href='#' class='close' data-dismiss='alert' aria-label='Close'>&times;</a>" +
			         texto + "</div>";
			     }
			     else if (alerta === 'campoEmail') {
			       resposta = "<div class='alert btn-warning text-center' role='alert'>" +
			         "<a href='#' class='close' data-dismiss='alert' aria-label='Close'>&times;</a>" +
			         texto + "</div>";
			     }
			     else if (alerta === 'senhaEnviada') {
			       resposta = "<div class='alert btn-success text-center' role='alert'>" +
			         "<a href='#' class='close' data-dismiss='alert' aria-label='Close'>&times;</a>" +
			         texto + "</div>";
			     }
			     $("#resposta").append(resposta);
			
			     $(".alert").click(function() {
			       $(".alert").hide();
			     });
			   }
			
			   $("#success").click(function() {
			     msg('sucesso', 'Configuracoes salvas com sucesso.');
			   });  
			  
			$('form').validate({
		    
	        highlight: function(element) {
	            $(element).closest('.form-group').addClass('has-error');
	        },
	        unhighlight: function(element) {
	            $(element).closest('.form-group').removeClass('has-error');
	        },
	        errorElement: 'span',
	        errorClass: 'help-block',
	        errorPlacement: function(error, element) {
	            if(element.parent('.input-group').length) {
	                error.insertAfter(element.parent());
	            } else {
	                error.insertAfter(element);
	            }
	        }
	    });
	    
		var waitingDialog = waitingDialog || (function ($) {
		    'use strict';
		
			// Creating modal dialog's DOM
			var $dialog = $(
				'<div class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true" style="padding-top:15%; overflow-y:visible;">' +
				'<div class="modal-dialog modal-m">' +
				'<div class="modal-content">' +
					'<div class="modal-header"><h3 style="margin:0;"></h3></div>' +
					'<div class="modal-body">' +
						'<div class="progress progress-striped active" style="margin-bottom:0;"><div class="progress-bar" style="width: 100%"></div></div>' +
					'</div>' +
				'</div></div></div>');
		
			return {
				/**
				 * Opens our dialog
				 * @param message Custom message
				 * @param options Custom options:
				 * 				  options.dialogSize - bootstrap postfix for dialog size, e.g. "sm", "m";
				 * 				  options.progressType - bootstrap postfix for progress bar type, e.g. "success", "warning".
				 */
				show: function (message, options) {
					// Assigning defaults
					if (typeof options === 'undefined') {
						options = {};
					}
					if (typeof message === 'undefined') {
						message = 'Loading';
					}
					var settings = $.extend({
						dialogSize: 'm',
						progressType: '',
						onHide: null // This callback runs after the dialog was hidden
					}, options);
		
					// Configuring dialog
					$dialog.find('.modal-dialog').attr('class', 'modal-dialog').addClass('modal-' + settings.dialogSize);
					$dialog.find('.progress-bar').attr('class', 'progress-bar');
					if (settings.progressType) {
						$dialog.find('.progress-bar').addClass('progress-bar-' + settings.progressType);
					}
					$dialog.find('h3').text(message);
					// Adding callbacks
					if (typeof settings.onHide === 'function') {
						$dialog.off('hidden.bs.modal').on('hidden.bs.modal', function (e) {
							settings.onHide.call($dialog);
						});
					}
					// Opening dialog
					$dialog.modal();
				},
				/**
				 * Closes dialog
				 */
				hide: function () {
					$dialog.modal('hide');
				}
			};
		
		})(jQuery);
		</script>
		
		<script>
				var targetParam = "<?php  echo $_GET['variavel'];  ?>";
				if (targetParam == 'loginInvalido') {
					$(document).ready(function() {
						msg('login', 'Email ou senha Invalido.');
					});
				};
				if (targetParam == 'login') {
					$(document).ready(function() {
						$('#myModal1').modal();
					});
				};
				if (targetParam == 'senhaAlterada') {
					$(document).ready(function() {
						$('#myModal2').modal();
					});
				};	 
		</script>
     <?php require 'template/footer.php'; ?>
</html>
    
    


	
    
