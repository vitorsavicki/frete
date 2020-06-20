<?php
if (!isset($_SESSION))
	session_start();
?>
<?php
header('Content-Type: text/html; charset=utf-8');
require_once 'lib/Application.php';
$o_Application = new Application();
$o_Application -> dispatch();
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

	</head>
	<?php
		require 'template/header.php';
	?>

	<body id="page-top" class="index">
		<header>
			<div class="container">
				<div class="row">
					<div class="col-lg-12">

						<div class="intro-text">
							<span class="name"><a  href="#page-top"> </a>A MUDANÇA COMEÇA AQUI</span>
						</div>
					</div>
				</div>
			</div>
		</header>

		<?php
if(!isset($_SESSION['tb_perfil_idPer']))
{
		?>
		<!-- Entrar Section -->
		<section id="cadastre">
		<div class="container">
		<div class="row" style="padding-bottom: 30px">
		<div class="col-lg-12 text-center">
		<h2>CADASTRE-SE</h2>
		</div>
		</div>
		<div class="row">
		<div class="col-lg-8 col-lg-offset-2">
		<div class="col-md-6 text-justify" >
		<a href="?controle=Pessoa&acao=cadastroCliente" class="btn btn-block btn-info btn-lg"></i>Cliente</a>
		<p></p>
		<p class="text-justify">Informe o que você precisa mudar,
		escolha suas datas e localização e aguarde os lances dos nossos profissionais cadastrados.</p>
		<p></p>
		</div>
		<div class="col-md-6 text-justify">
		<a href="?controle=Pessoa&acao=cadastroTransportador" class="btn btn-block btn-info btn-lg">Transportador	</a>
		<p>Realize o seu cadastro com a Frete Fácil para receber
		trabalhos na região em que você determinar. Cadastre um lance em cada trabalho e boa sorte!</p>
		<p class="text-justify"></p>
		<p></p>
		</div>
		</div>
		</div>
		</div>
		</section>
		<?php
		}
		?>

		<!-- Contact Section -->
		<section id="contact">
			<div class="container">
				<div class="row" style="padding-bottom: 30px">
					<div class="col-lg-12 text-center">
						<h2>FALE CONOSCO</h2>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-8 col-lg-offset-2">
						<!-- To configure the contact form email address, go to mail/contact_me.php
						and update the email address in the PHP file on line 19. -->
						<!-- The form should work on most web servers, but if the form is not
						working you may need to configure your web server differently. -->
						<form name="sentMessage" id="contactForm" novalidate="">
							<div class="row control-group">
								<div class="form-group col-xs-12 floating-label-form-group controls">
									<label>Nome</label>
									<input type="text" class="form-control" placeholder="Nome"
									id="name" required="" data-validation-required-message="Por favor coloque seu nome.">
									<p class="help-block text-danger"></p>
								</div>
							</div>
							<div class="row control-group">
								<div class="form-group col-xs-12 floating-label-form-group controls">
									<label>Email</label>
									<input type="email" class="form-control" placeholder="Email"
									id="email" required="" data-validation-required-message="Por favor coloque seu email.">
									<p class="help-block text-danger"></p>
								</div>
							</div>
							<div class="row control-group">
								<div class="form-group col-xs-12 floating-label-form-group controls">
									<label>Telefone de Contato</label>
									<input type="text" class="form-control" placeholder="Telefone de Contato"
									id="phone" required="" data-validation-required-message="Por favor coloque seu telefone.">
									<p class="help-block text-danger"></p>
								</div>
							</div>
							<div class="row control-group">
								<div class="form-group col-xs-12 floating-label-form-group controls">
									<label>Mensagem</label>
									<textarea rows="5" class="form-control" placeholder="Mensagem"
                                    id="message" required="" data-validation-required-message="Por favor escreva alguma mensagem."></textarea>
									<p class="help-block text-danger"></p>
								</div>
							</div>
							<br>
							<div id="success"></div>
							<div class="row">

								<div class="form-group col-xs-12">
									<div class="text-center">
										<button type="submit" class="btn btn-primary btn-lg">
											Enviar
										</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</section>

		<!-- About Section -->
		<section class="success" id="about">
			<div class="container">
				<div class="row" style="padding-bottom: 30px">
					<div class="col-lg-12 text-center">
						<h2>Sobre a empresa</h2>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-4 col-lg-offset-2">
						<p>
							Fundada com o objetivo de facilitar a procura de um fretista profissional seja para mudança residencial ou comercial a Savicki procura resolver  a sua mudança da melhor forma, com o melhor profissional e ao preço mais acessível.
						</p>
					</div>
					<div class="col-lg-4">
						<p>
							Com sede em Pinhais Paraná, a Frete Fácil busca também, oferecer uma ferramenta lucrativa para os profissionais na área de mudança, com a certeza que terão informações suficientes para realizar o seu trabalho da melhor forma possível.
						</p>
					</div>
				</div>
			</div>
		</section>
	</body>

	<footer class="text-center">
		<div class="footer-above">
			<div class="container">
				<div class="row">
					<div class="footer-col col-md-6">
						<h3>Endereço</h3>
						<p>
							Rua Rio Uruguai, 310
							<br>
							Pinhais-PR
						</p>
						<br>
						CEP 83322-220</p>
					</div>
					<div class="footer-col col-md-6">
						<h3>Redes Sociais</h3>
						<ul class="list-inline">
							<li>
								<a href="#" class="btn btn-social-icon btn-facebook"> <span class="fa fa-facebook"></span> </a>
							</li>

							<li>
								<a href="#" class="btn btn-social-icon btn-twitter"> <span class="fa fa-twitter"></span> </a>
							</li>

							<li>
								<a href="#" class="btn btn-social-icon btn-linkedin"> <span class="fa fa-linkedin"></span> </a>
							</li>

							<li>
								<a href="#" class="btn btn-social-icon btn-instagram"> <span class="fa fa-instagram"></span> </a>
							</li>

						</ul>
					</div>
				</div>
			</div>

		</div>
		<div class="footer-below">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						Copyright © Savicki Soluções em Tecnologia da Informação 2015
					</div>
				</div>
			</div>
		</div>
	</footer>

	<!-- Plugin JavaScript -->
	<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
	<script src="template/js/classie.js"></script>
	<script src="template/js/cbpAnimatedHeader.js"></script>
	<!-- Custom Theme JavaScript -->
</html>