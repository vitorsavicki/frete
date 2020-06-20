<?php
session_start();

if($_SERVER['REQUEST_METHOD'] == 'GET' && !isset($_GET['error'])){
  $appId = '814973045286446';
  $appSecret = 'e783346a66c7ae447e29f622ce4834da';
  $redirect_uri = urlencode('http://localhost/FreteImediato/views/associa-login-facebook-callback.php');
  $code = $_GET['code'];

  $token_url = "https://graph.facebook.com/oauth/access_token?client_id=".$appId."&redirect_uri="
  .$redirect_uri."&client_secret=".$appSecret."&code=".$code;

  $resposta = @file_get_contents($token_url);

  if($resposta){
    $resp_array = null;
    parse_str($resposta, $resp_array);

    if(isset($resp_array['access_token']) && $resp_array['access_token']){
      //tudo ok!

      $graph_url = "https://graph.facebook.com/me?access_token=".$resp_array['access_token'];
      $dados_do_user = json_decode( file_get_contents($graph_url) );

      //os dados do user nas session
      $_SESSION['primeiroNomePes'] = $dados_do_user->name;
	  $_SESSION['foto'] = 'https://graph.facebook.com/'.$dados_do_user->id.'/picture';
	  
     

    } else {
      //erro!
      $_SESSION['erro'] = 'sis';
    }
  } else {
    //erro!
    $_SESSION['erro'] = 'sis';
  }

} else {
  //erro!
  $_SESSION['erro'] = 'permitir';
}



  ?>

