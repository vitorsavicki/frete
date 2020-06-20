<?php
header('Content-Type: text/html; charset=utf-8');
// A sessão precisa ser iniciada em cada página diferente
if (!isset($_SESSION)) session_start();
// Verifica se não há a variável da sessão que identifica o usuário
    unset($_SESSION['dataini']);
    unset($_SESSION['datafim']);
    unset($_SESSION['cidade']);
    unset($_SESSION['estado']);
    unset($_SESSION['status']);
    unset($_SESSION['statusAcesso']);
    unset($_SESSION['palavraChave']);
    
if ($_SESSION['codigoPer'] <> 'A') {
	// Destrói a sessão por segurança
	session_destroy();
	// Redireciona o visitante de volta pro login
	header("Location: index.php"); exit;
}
?>
<html>
    <head>
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
        <script src="template/js/jquery.validate.min.js"></script>
        <script type="text/javascript" src="template/js/jquery.mask.min.js"/></script>
        <script src="template/js/jquery.maskMoney.js" type="text/javascript"></script>
        <link rel="stylesheet" type="text/css" href="template/AdminLTE.min.css" />
        <link rel="stylesheet" type="text/css" href="template/css/datepicker.css">
        <script src="template/js/bootstrap-datepicker.js"></script>
        <script type="text/javascript" src="template/js/locales/bootstrap-datepicker.pt-BR.js"></script>
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
                Área do Administrador
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
                        <h1 class="text-center">Área do Administrador</h1>
                    </div>
                </div>
          <div class="container" style="padding-top: 50px">
	        <div class="row">
	          <div class="col-md-3">
	            <a href="?controle=Categoria&acao=listarCategoria" class="btn btn-block btn-lg btn-primary"><h6>Lista de Categoria</h6></a>
	          </div>
	          <div class="col-md-3">
	            <a href="?controle=Item&acao=listarItem" class="btn btn-block btn-lg btn-primary"><h6>Lista de Item</h6></a>
	          </div>
	          <div class="col-md-3">
	            <a href="?controle=Pessoa&acao=listarCliente" class="btn btn-block btn-lg btn-primary"><h6>Lista de Clientes</h6></a>
	          </div>
	          <div class="col-md-3">
                <a href="?controle=Pessoa&acao=listarTransportador" class="btn btn-block btn-lg btn-primary"><h6>Lista de Transportadores</h6></a>
              </div>
	        </div>
	 
	        <div class="row" style="padding-top: 50px">
	          <div class="col-md-3">
	            <a href="?controle=Mensalidade&acao=controlarMensalidade" class="btn btn-block btn-lg btn-primary"><h6>Controle de Mensalidade</h6></a>
	          </div>
	           <div class="col-md-3">
	            <a href="?controle=Mensalidade&acao=gerarMensalidade" class="btn btn-block btn-lg btn-primary"><h6>Gerar Mensalidade</h6></a>
	          </div> 
	          <div class="col-md-3">
                <a href="?controle=Voucher&acao=listarVoucher" class="btn btn-block btn-lg btn-primary"><h6>Lista de Vouchers</h6></a>
              </div>
              <div class="col-md-3">
                <a href="?controle=Log&acao=listarLog" class="btn btn-block btn-lg btn-primary"><h6>Lista de logs</h6></a>
              </div> 
	        </div>
	        
	        <div class="row" style="padding-top: 50px">
              <div class="col-md-3">
                <a href="?controle=RespostaPesquisa&acao=listarRespostaPesquisa" class="btn btn-block btn-lg btn-primary"><h6>Resultados da Pesquisa</h6></a>
              </div>
            </div>
	      </div>
	      </div>
	    </div>
    </body>
    <?php require 'template/footer.php'; ?>
</html>