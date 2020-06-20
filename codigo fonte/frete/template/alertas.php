
<link rel="stylesheet" type="text/css" href="template/AdminLTE.min.css" />
<link rel="stylesheet" type="text/css" href="template/alerta.css" />

	       
	       <?php if($_SESSION['codigoPer'] == 'U')
				{
				?>
				
			<li class="dropdown messages-menu">
                <!-- Menu toggle button -->
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <i class="fa fa-flag-o"></i>
                  <span class="label label-success"></span>
                </a>
                <ul class="dropdown-menu">
                  <li>
                    <!-- inner menu: contains the messages -->
                    <ul class="menu" id="res">
           
                    </ul><!-- /.menu -->
                  </li>
                  <!-- /.messages-menu <li class="footer"><a href="#">See All Messages</a></li>-->
                </ul>
              </li><!-- /.messages-menu -->
	
				<?php
				}
				?>

	    	 <li class="dropdown messages-menu">
                <!-- Menu toggle button -->
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <i class="fa fa-envelope-o"></i>
                  <span class="label label-danger"></span>
                </a>
                <ul class="dropdown-menu">
                  <li>
                    <!-- inner menu: contains the messages -->
                    <ul class="menu" id="resmen">
           
                    </ul><!-- /.menu -->
                  </li>
                  <!-- /.messages-menu <li class="footer"><a href="#">See All Messages</a></li>-->
                </ul>
              </li><!-- /.messages-menu -->

 	<script type="text/javascript" src="./js/mod_xhr.js"></script>
 	<script type="text/javascript">
 		document.addEventListener('DOMContentLoaded', function(){
 			var icon_not = document.getElementsByClassName('notifs')[0],
 				dp 	 	 = document.getElementsByClassName('dp')[0],
 				id_user  = document.getElementById('id_user'),
 				total_not= document.getElementsByClassName('label label-success')[0],
 				res 	 = document.getElementById('res'),
 				icon_men = document.getElementsByClassName('menfs')[0],
 				dpmen 	 	 = document.getElementsByClassName('dpmen')[0],
 				total_men= document.getElementsByClassName('label label-danger')[0],
 				resmen 	 = document.getElementById('resmen');
 				//$('.ctnots').css('display','none');
 				//$('.ctmen').css('display','none');


 			document.addEventListener('click', function(){
 				//dpmen.style.display = 'none';
 			});

				var sessao = "<?php  echo $_SESSION['codigoPer'];  ?>"
			  
			   if( sessao == 'T')
			   {
			  
          			window.setInterval(function(){
		 				xhr.get('views/requestsMensagemTransp.php?acao=verificar', function(totalMen){
			 				total_men.innerHTML = totalMen;
			 			
			 			
			 			});
		 			}, 1000);
		
		 			window.setInterval(function(){
		 				xhr.get('views/requestsMensagemTransp.php?acao=getnots', function(mens){
			 				resmen.innerHTML = mens;
			 			});
		 			}, 1000);
		
		 			resmen.addEventListener('click', function(e){
		 				var elemento = e.target;
		 					xhr.get('views/requestsMensagemTransp.php?acao=vis&idnot='+elemento.id, function(resmen){
		 						//alert('views/requests.php?acao=vis&idnot='+elemento.id);
		 					});
		
		 				if(elemento.classList.contains('vis')){
		 					xhr.get('views/requestsMensagemTransp.php?acao=vis&idnot='+elemento.id, function(resmen){
		 						//alert(res);
		 					});
		 				}
		 			});
           
				}
			   if( sessao == 'U')
			   {

		 			document.addEventListener('click', function(){
		 				//dp.style.display = 'none';
		 			});
		
		
		
		 			window.setInterval(function(){
		 				xhr.get('views/requests.php?acao=verificar', function(total){
			 				total_not.innerHTML = total;
			 				
			 		
			 				
			 			
			 			});
		 			}, 1000);
		
		 			window.setInterval(function(){
		 				xhr.get('views/requests.php?acao=getnots', function(nots){
			 				res.innerHTML = nots;
			 				
			 			});
		 			}, 1000);
		
		 			res.addEventListener('click', function(e){
		 				var elemento = e.target;
		 				
		 				xhr.get('views/requests.php?acao=vis&idnot='+elemento.id, function(res){
		 						//alert('views/requests.php?acao=vis&idnot='+elemento.id);
		 					});
		
		 				if(elemento.classList.contains('vis')){
		 					xhr.get('views/requests.php?acao=vis&idnot='+elemento.id, function(res){
		 						//alert('views/requests.php?acao=vis&idnot='+elemento.id);
		 					});
		 				}
		 				
		 					
		 				
		 			});
 			
			   	
					window.setInterval(function(){
		 				xhr.get('views/requestsMensagemCli.php?acao=verificar', function(totalMen){
			 				total_men.innerHTML = totalMen;
			 			
			 			});
		 			}, 1000);
		
		 			window.setInterval(function(){
		 				xhr.get('views/requestsMensagemCli.php?acao=getnots', function(mens){
			 				resmen.innerHTML = mens;
			 			});
		 			}, 1000);
		
		 			resmen.addEventListener('click', function(e){
		 				var elemento = e.target;
		 				xhr.get('views/requestsMensagemCli.php?acao=vis&idnot='+elemento.id, function(resmen){
		 						//alert('views/requestsMensagemCli.php?acao=vis&idnot='+elemento.id);
		 					});
		
		
		 				if(elemento.classList.contains('vis')){
		 					xhr.get('views/requestsMensagemCli.php?acao=vis&idnot='+elemento.id, function(resmen){
		 						//alert('views/requestsMensagemCli.php?acao=vis&idnot='+elemento.id);
		 					});
		 				}
		 			});
			   }

 			
 		});
 	</script>
				