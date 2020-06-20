<?php

// A sessão precisa ser iniciada em cada página diferente
if (!isset($_SESSION)) session_start();
unset($_SESSION['dataini']);
unset($_SESSION['datafim']);
unset($_SESSION['categoria']);
unset($_SESSION['cidadeOrigem']);
unset($_SESSION['cidadeDestino']);
unset($_SESSION['estadoOrigem']);
unset($_SESSION['estadoDestino']);
unset($_SESSION['palavraChave']);
unset($_SESSION['situacaoTransp']);
unset($_SESSION['quality']);


// Verifica se não há a variável da sessão que identifica o usuário
if ($_SESSION['codigoPer'] <> 'T') {
	// Destrói a sessão por segurança
	session_destroy();
	// Redireciona o visitante de volta pro login
	header("Location: index.php"); exit;
}

?>
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
        <script type="text/javascript" src="template/js/jquery.mask.min.js"/></script>
        <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
        <script type="text/javascript" src="http://netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
        <link href="template/bootstrap-datepicker/css/bootstrap-datepicker3.css" rel="stylesheet">
		<script src="template/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
        <script type="text/javascript" src="template/bootstrap-datepicker/locales/bootstrap-datepicker.pt-BR.min.js"></script>
        <script src="template/js/jquery.maskMoney.js" type="text/javascript"></script>
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
                <a href="?controle=Index&acao=Index">Home</a>
              </li>
              <li class="active">
                Área do Transportador
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
        <div class="section">
	      <div class="container">
	      		<div class="row">
                    <div class="col-md-12">
                        <h1 class="text-center">Área do Transportador</h1>
                    </div>
                </div>
     			<div id="resposta1"></div>
                <br>
                <br>
                <br>
	        <div class="row">
	          <?php if($_SESSION['codigoSta'] <> 'I')
				{ ?>
	          <div class="col-md-3">
	            <a href="?controle=Transporte&acao=cadastrarLance&opcao=clienteativosTrans" class="btn btn-block btn-lg btn-primary"><h6>Pesquisar Transportes</h6></a>
	          </div>
	          <?php }
				else{
				?>
	            <div class="col-md-3">
	            <a  class="btn btn-block btn-lg btn-primary" disabled=""><h6>Pesquisar Transportes</h6></a>
	          </div>
	          <?php
				}
				?>
	          <div class="col-md-3">
	            <a href="?controle=Lance&acao=meusLances" class="btn btn-block btn-lg btn-primary"><h6>Meus  Lances</h6></a>
	          </div>
	          <div class="col-md-3">
	            <a href="?controle=Transporte&acao=listarTransporte&opcao=transpganhos" class="btn btn-block btn-lg btn-primary"><h6>Transportes Ganhos</h6></a>
	          </div>
	          <div class="col-md-3">
	            <a href="?controle=Transporte&acao=listarTransporte&opcao=transpAndamentoTrans" class="btn btn-block btn-lg btn-primary"><h6>Transportes em Andamento</h6></a>
	          </div>
	        </div>   
	        <br>
	        <br>
	        <div class="row">
	        <div class="col-md-3">
	            <a href="?controle=Transporte&acao=listarTransporte&opcao=transpConcluidoTrans" class="btn btn-block btn-lg btn-primary"><h6>Transportes Concluídos</h6></a>
	          </div>
	          <div class="col-md-3">
	            <a href="?controle=Pessoa&acao=manterTransportador&idPes=<?php echo $_SESSION['idPes'] ?>" class="btn btn-block btn-lg btn-primary"><h6>Meus Dados</h6></a>
	          </div>
	          <div class="col-md-3">
	            <a href="?controle=Boleto&acao=listarBoletoTransportador&idPes=<?php echo $_SESSION['idPes'] ?>" class="btn btn-block btn-lg btn-primary"><h6>Boletos em Aberto</h6></a>
	          </div>
	          <div class="col-md-3">
	            <a href="?controle=Mensalidade&acao=listarMensalidadeCliente&idPes=<?php echo $_SESSION['idPes'] ?>" class="btn btn-block btn-lg btn-primary"><h6>Histórico Finaceiro</h6></a>
	          </div>
	        </div>
	        <br>
	        <br>
	         <div class="row">
	        	<div class="col-md-3">
	            	<a href="?controle=Mensagem&acao=listarMensagemTransp&opcao=Mensagens" class="btn btn-block btn-lg btn-primary"><h6>Mensagens</h6></a>
	         	 </div>
	         	  <div class="col-md-3">
                    <a href="?controle=Avaliacao&acao=listarAvaliacao&opcao=transpAvaliacoes" class="btn btn-block btn-lg btn-primary"><h6>Avaliações Recebidas</h6></a>
                 </div>
	        </div>
	      </div>
	    </div>
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
						<p>O transportador foi editado com sucesso.</p>
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
		<script>
		var session ="<?php  echo isset($_SESSION['codigoSta']) ? $_SESSION['codigoSta'] : '0'; ?>";
		
		if (session == 'I') {
			$(document).ready(function() {
				msg1('erro', 'Você não pode pesquisar transportes ou efetuar lances enquanto ouver pendências financeiras, veja se há boletos em aberto.');
					});
		}
		</script>
      
       <script>
       var targetParam = "<?php  echo isset($_GET['variavel']) ? $_GET['variavel'] : '0'; ?>";
       	 if (targetParam == 'editado') {
			$(document).ready(function() {
				$('#myModal1').modal();
			});
		};
       </script>
       
       <script>
       	function msg1(alerta, texto) {
			     var resposta1 = '';
			     $("#resposta1").empty();
			     if (alerta === 'sucesso') {
			       resposta1 = "<div class='alert btn-success text-center' role='alert'>" +
			         "<a href='#' class='close' data-dismiss='alert' aria-label='Close'>&times;</a>" +
			         texto + "</div>";
			     } else if (alerta === 'atencao') {
			       resposta1 = "<div class='alert btn-warning text-center' role='alert'>" +
			         "<a href='#' class='close' data-dismiss='alert' aria-label='Close'>&times;</a>" +
			         texto + "</div>";
			     } else if (alerta === 'erro') {
			       resposta1 = "<div class='alert btn-danger text-center' role='alert'>" +
			        
			         texto + "</div>";
			     }
			     $("#resposta1").append(resposta1);
			
			     
			   }
       </script>
       
    </body>
    <?php require 'template/footer.php'; ?>
</html>