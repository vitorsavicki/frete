<?php
session_start();
$failed = false;
$images = Array();


$caminhodasimagens = isset($_POST['caminhodasimagens']) ? $_POST['caminhodasimagens'] : '';
if (isset($caminhodasimagens) and $caminhodasimagens !== NULL and $caminhodasimagens !== '')
{
	$upload_dir = substr($caminhodasimagens, strpos($caminhodasimagens, '/')+1);
	$uploaddir = $caminhodasimagens;
}
else
{
	$novo_diretorio = date('YmdHis'). session_id(); 
	mkdir('transporte/' . $novo_diretorio);
	$upload_dir = 'transporte/'. $novo_diretorio.'/';
	$uploaddir = 'upload/transporte/'. $novo_diretorio.'/';
}
if ($_SERVER['CONTENT_LENGTH'] < 8380000) {
if (isset($_FILES['upload_files']) && $_FILES['upload_files']['error'] != 0) {    
    
        foreach($_FILES['upload_files']['tmp_name'] as $key=>$value) {

                $file = $_FILES['upload_files']['name'][$key];
                // allow only image upload
                if(preg_match('#image#',$_FILES['upload_files']['type'][$key])) {
                    if(!move_uploaded_file($value, $upload_dir.$file)) {
                        $failed = true;
                    } else {                    
                        $images[] = $uploaddir.$file;
                    }    
                } else {
                    $images = array("error"=>"S� � permitido fazer upload de imagens.");
                }
        }
}
} else {
    $images = array("error"=>"N�o � permitido fazer upload superior a 8Mb");
}
if($failed == true) {
	$images = array("error"=>"Erro de servidor<br/>Se o erro persistir entre em contato com o suporte.");
}
$_SESSION["imagenstransporte"] = $images;
?>

<html>
 <body>
  <script type="text/javascript">
  window.parent.parent.Uploader.done('<?php echo json_encode($images); ?>');
  </script>
 </body>
</html>
