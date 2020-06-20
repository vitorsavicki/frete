<?php
if(!isset($_SESSION)) session_start();
header('Content-Type: text/html; charset=utf-8');
?>

<html>
    
    <head>
        <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
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
		<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
        <script type="text/javascript" src="http://netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
        <link href="template/css/fileinput.css" media="all" rel="stylesheet" type="text/css" />
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <script src="template/js/fileinput.min.js" type="text/javascript"></script>
        <script src="template/js/fileinput_locale_pt-BR.js" type="text/javascript"></script>
        <script src="template/js/jquery.validate.min.js"></script>
        <script type="text/javascript" src="template/js/jquery.mask.min.js"/></script>
        <link rel="stylesheet" href="template/css/star-rating.css" media="all" rel="stylesheet" type="text/css"/>
	    <script src="template/js/star-rating.js" type="text/javascript"></script>
	            <style>
			   	.colorgraph {
				  height: 5px;
				  border-top: 0;
				  background: #c4e17f;
				  border-radius: 5px;
				  background-image: -webkit-linear-gradient(left, #c4e17f, #c4e17f 12.5%, #f7fdca 12.5%, #f7fdca 25%, #fecf71 25%, #fecf71 37.5%, #f0776c 37.5%, #f0776c 50%, #db9dbe 50%, #db9dbe 62.5%, #c49cde 62.5%, #c49cde 75%, #669ae1 75%, #669ae1 87.5%, #62c2e4 87.5%, #62c2e4);
				  background-image: -moz-linear-gradient(left, #c4e17f, #c4e17f 12.5%, #f7fdca 12.5%, #f7fdca 25%, #fecf71 25%, #fecf71 37.5%, #f0776c 37.5%, #f0776c 50%, #db9dbe 50%, #db9dbe 62.5%, #c49cde 62.5%, #c49cde 75%, #669ae1 75%, #669ae1 87.5%, #62c2e4 87.5%, #62c2e4);
				  background-image: -o-linear-gradient(left, #c4e17f, #c4e17f 12.5%, #f7fdca 12.5%, #f7fdca 25%, #fecf71 25%, #fecf71 37.5%, #f0776c 37.5%, #f0776c 50%, #db9dbe 50%, #db9dbe 62.5%, #c49cde 62.5%, #c49cde 75%, #669ae1 75%, #669ae1 87.5%, #62c2e4 87.5%, #62c2e4);
				  background-image: linear-gradient(to right, #c4e17f, #c4e17f 12.5%, #f7fdca 12.5%, #f7fdca 25%, #fecf71 25%, #fecf71 37.5%, #f0776c 37.5%, #f0776c 50%, #db9dbe 50%, #db9dbe 62.5%, #c49cde 62.5%, #c49cde 75%, #669ae1 75%, #669ae1 87.5%, #62c2e4 87.5%, #62c2e4);
				}

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
		
    .bs-example{
    	margin: 20px;
    }

        </style>
      <style>
            body { padding-top: 120px; }
      </style>
    </head>
    <?php require 'template/header.php'; ?>
		  <body id="page-top">
		   	<div class="section">
		      <div class="container">
		        <div class="row">
		          <div class="col-md-12">
		            <ul class="breadcrumb">
		            <li>
		                <a href="controle=Index&acao=Index">Home</a>
		             </li>
		             <li>
		              	<?php
		              	if ($_SESSION['codigoPer'] == 'U' )
						{
						?>
		                <a href="?controle=Pessoa&acao=areaCliente">Área do Cliente</a> 
		                <?php
		                }
						?>
						<?php
						if ($_SESSION['codigoPer'] == 'T' )
						{
						?>
		                <a href="?controle=Pessoa&acao=areaTransportador">Área do Transportador</a> 
		                 <?php
		                }
						?>
		              </li>
		               <li>
		               <?php
		               	if(isset($_GET['opcao']) and $_GET['opcao'] == "clienteativos")
						{
						?>
						<a href="?controle=Transporte&acao=listartransporte&opcao=clienteativos">Transportes Ativos</a>
						<?php 
						}
						?>
					
						<?php
		               	if(isset($_GET['opcao']) and $_GET['opcao'] == "clienteinativos")
						{
						?>
						<a href="?controle=Transporte&acao=listartransporte&opcao=clienteinativos">Transportes Cancelados</a>
						<?php 
						}
						?>
							<?php
		               	if(isset($_GET['opcao']) and $_GET['opcao'] == "transpganhos")
						{
						?>
						<a href="?controle=Transporte&acao=listartransporte&opcao=transpganhos">Transportes Ganhos</a>
						<?php 
						}
						?>
						<?php 
						if(isset($_GET['opcao'] ) and $_GET['opcao'] == 'clienteativosTrans'){
							?>
		               <a href="?controle=Transporte&acao=listartransporte&opcao=clienteativosTrans">Pesquisar Transporte</a>
		               <?php
						}
						?>
						<?php
		               	if(isset($_GET['opcao']) and $_GET['opcao'] == "transpandamento")
						{
						?>
						<a href="?controle=Transporte&acao=listartransporte&opcao=transpandamento">Transportes em Andamento</a>
						<?php 
						}
						?>
						<?php
		               	if(isset($_GET['opcao']) and $_GET['opcao'] == "transpAndamentoTrans")
						{
						?>
						<a href="?controle=Transporte&acao=listartransporte&opcao=transpAndamentoTrans">Transportes em Andamento</a>
						<?php 
						}
						?>
						<?php
		               	if(isset($_GET['opcao']) and $_GET['opcao'] == "transpconcluido")
						{
						?>
						<a href="?controle=Transporte&acao=listartransporte&opcao=transpconcluido">Transportes Concluidos</a>
						<?php 
						}
						?>
						<?php
		               	if(isset($_GET['opcao']) and $_GET['opcao'] == "transpConcluidoTrans")
						{
						?>
						<a href="?controle=Transporte&acao=listartransporte&opcao=transpConcluidoTrans">Transportes Concluidos</a>
						<?php 
						}
						?>
						</li>
		               <li class="active">
		              	Avaliação de Transportador
		               </li>
		            </ul>
		          </div>
		        </div>
		      </div>
		    </div>  

   <body id="page-top">
     			<!-- Inicio Novo -->
  <div class="container">
	<div class="row">
   		<div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
			<form role="form" method="post" id="form" name="form" enctype="multipart/form-data">
				<h2 class="text-center">Avalie o transporte</h2>
				<hr class="colorgraph">           
							<div class="form-group input-lg">
								 <label for="valorAva1">O frete foi pontual?</label>
			                     <input id="valorAva1" name="valorAva1"  type="number" class="rating" min="0" max="10" step="1" data-size="sm" tabindex="1"
		           				  data-symbol="&#xf005;" data-glyphicon="false" data-rating-class="rating-fa" class="required-entry form-required aria-required form-control" required>               
							</div>
							<br>
							<br>
							<div class="form-group input-lg">
								 <label for="valorAva2">O trasportador foi prestativo?</label>
			                     <input id="valorAva2" name="valorAva2" type="number" class="rating" min="0" max="10" step="1" data-size="sm" tabindex="2"
		           				  data-symbol="&#xf005;" data-glyphicon="false" data-rating-class="rating-fa" class="required-entry form-required aria-required form-control" required>                 
							</div>
							<br>
							<br>
							<div class="form-group input-lg">
								 <label for="valorAva3">A comunicacao com o transportador foi eficaz?</label>
			                     <input id="valorAva3" name="valorAva3" type="number" class="rating" min="0" max="10" step="1" data-size="sm" tabindex="3"
		           				  data-symbol="&#xf005;" data-glyphicon="false" data-rating-class="rating-fa" class="required-entry form-required aria-required form-control">            
							</div>
							<br>
							<br>
							<div class="form-group input-lg">
								 <label for="valorAva4">O veiculo usado estava em boas condicoes?</label>
			                     <input id="valorAva4" name="valorAva4" type="number" class="rating" min="0" max="10" step="1" data-size="sm" tabindex="4"
		           				  data-symbol="&#xf005;" data-glyphicon="false" data-rating-class="rating-fa" class="required-entry form-required aria-required form-control" required>
							</div>
							<br>
							<br>
							<div class="form-group input-lg">
								 <label for="valorAva5">Voce recomedaria esse transportador para amigos ou familiares?</label>
			                     <input id="valorAva5" name="valorAva5" type="number" class="rating" min="0" max="10" step="1" data-size="sm" tabindex="5"
		           				  data-symbol="&#xf005;" data-glyphicon="false" data-rating-class="rating-fa" class="required-entry form-required aria-required form-control" required>                
							</div>
							<br>
							<br>
							<br>
							<br>
							<div class="form-group input-lg">
								 <label for="conteudoAva">Deixe aqui seu comentário sobre o Transporte</label>
			                     <textarea name="conteudoAva" id="conteudoAva"  class="form-control input-lg" cols="5" rows="3" placeholder="Coloque seu comentário aqui."></textarea>                  
							</div>
							<br>
						    <br> 
						    <br>
						    <br>
						    <hr class="colorgraph">
							<div class="row">
								<input type="hidden" name="controle" value="Pessoa">
								<input type="hidden" name="acao" value="cadastroCliente">
								<div class="col-xs-12 col-md-6"><a data-toggle="modal"  class="btn btn-primary btn-block btn-lg" href="#addBookDialog" tabindex="6">Confirmar</a></div>
								<div class="col-xs-12 col-md-6"><a href="?controle=Transporte&acao=listarTransporte&opcao=transpconcluido" type="button" class="btn btn-danger btn-block btn-lg" tabindex="7">Cancelar</a></div>
							</div> 
				
                            <div id="addBookDialog" class="modal fade" role="dialog">
							  <div class="modal-dialog">
							    <!-- Modal content-->
							    <div class="modal-content">
							      <div class="modal-header">
							        <button type="button" class="close" data-dismiss="modal">&times;</button>
							        <h4 class="modal-title"></h4>
							      </div>
							      <div class="modal-body text-center">
							        <p>Tem certeza que deseja conluir essa avaliação?</p>
							        <div class="form-group">
								  		<input type="hidden" name="controle" value="Avaliacao">
								        <input type="hidden" name="acao" value="manterAvaliacao">
								        <input type='hidden' name='tb_tranporte_idTransp' value='<?php echo $_GET['idTransp'] ?>'>
										<button type="submit" id="confirmar" name="confirmar" class="btn btn-primary">Sim</button>
								        <button type="button" class="btn btn-danger" data-dismiss="modal">Não</button>
								  	</div>
							      </div>
							      <div class="modal-footer">
							      </div>
							    </div>
							  </div>
							</div> 
							    
						</form>
                    </div>
                </div>
            </div>

         <script type="text/javascript">
           $('form').validate({
            rules: {
			     	valorAva10: {
					required:true				
				}
		      },
		     
		      messages: {
		         valorAva10: {
		            required: 'Informe o número minimo de estrelas'
		         },
		      
		      },

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
			</script>
			<br>
		    <br>
		    <br>
    </body>
    <?php require 'template/footer.php'; ?>
</html>