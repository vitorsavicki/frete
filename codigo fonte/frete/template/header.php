 <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header page-scroll">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a  href="#page-top"><p class="navbar-brand">Savicki</p></a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav navbar-right">
            <li class="hidden">
              <a href="#page-top"></a>
            </li>
             <li class="page-scroll">
              <a href="?controle=Index&acao=Index">HOME</a>
            </li>
            <?php
               if(!isset($_SESSION['tb_perfil_idPer']))
               {
               ?>
            <li class="page-scroll">
              <a href="?controle=Index&acao=Index&#cadastre">CADASTRE-SE</a>
            </li>
            <?php
               }
               ?>
            <li class="page-scroll">
              <a href="?controle=Index&acao=Index&#about">SOBRE</a>
            </li>
            <li class="page-scroll">
              <a href="?controle=Index&acao=Index&#contact">FALE CONOSCO</a>
            </li>
             <?php
            
            if(!isset($_SESSION['primeiroNomePes'])){
            ?>
             <li class="page-scroll">
              <a href="?controle=Pessoa&acao=paginaLogin">ENTRAR</a>
            </li>
                <?php
            }
            ?>
             <?php
            if(isset($_SESSION['primeiroNomePes'])){
            ?>
             <?php
               if($_SESSION['codigoPer'] <> 'A')
               {
               ?>
            <li class="page-scroll">
            <?php require 'template/alertas.php'; ?>
            </li>
            <?php
               }
               ?>
            <li class="dropdown dropdown-right">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown"> 
            <?php if(isset($_SESSION['fotoPes']) AND $_SESSION['fotoPes'] <> "")
            {
                ?>
            <img class="img-rounded" height="25px" width="25px" src="<?php echo $_SESSION['fotoPes'];?>"> <?php echo $_SESSION['primeiroNomePes']; ?>  <b class="caret"></b>
            <?php
            }
            else {
            ?>
            <img class="img-rounded" height="25px" width="25px" src="http://pingendo.github.io/pingendo-bootstrap/assets/user_placeholder.png"> <?php echo $_SESSION['primeiroNomePes']; ?>  <b class="caret"></b>
            <?php
            }
            ?>
            </a>
            <ul class="dropdown-menu">
              <?php
               if($_SESSION['codigoPer'] == 'U')
               {
               ?>
              <li><a href="?controle=Pessoa&acao=areaCliente">Minha Área</a></li>
              <?php
               }
               ?>
               <?php
               if($_SESSION['codigoPer'] == 'T')
               {
               ?>
              <li><a href="?controle=Pessoa&acao=areaTransportador">Minha Área</a></li>
              <?php
               }
               ?>
               <?php
               if($_SESSION['codigoPer'] == 'A')
               {
               ?>
              <li><a href="?controle=Pessoa&acao=areaAdministrador">Minha Área</a></li>
              <?php
               }
               ?>
              <li><a href="?controle=Login&acao=logout">Sair</a></li>
            </ul>
            </li>
            <?php
            }
            ?>  
          </ul>
        </div>
        <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
    </nav>