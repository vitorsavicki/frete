<?php
session_start();
$failed = false;
$images;
	$novoNome = md5(microtime());
						
	$upload_dir = 'transporte/';
	$uploaddir = 'upload/transporte/';
	
if ($_SERVER['CONTENT_LENGTH'] < 8380000) {
if (isset($_FILES['kartik-input-700']) && $_FILES['kartik-input-700']['error'] != 0) {    
    
        foreach($_FILES['kartik-input-700']['tmp_name'] as $key=>$value) {

                $file = $_FILES['kartik-input-700']['name'][$key];
                // allow only image upload
                if(preg_match('#image#',$_FILES['kartik-input-700']['type'][$key])) {
                    if(!move_uploaded_file($value, $upload_dir.$novoNome.$file)) {
                        $failed = true;
                    } else {                    
                        $images = $uploaddir.$novoNome.$file;
                    }    
                }
        }
}
}

$_SESSION["imagenstransporteCaminho"] = $images;
echo json_encode($images);


?>

	   					


