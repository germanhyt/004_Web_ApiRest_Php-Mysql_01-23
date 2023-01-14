<?php 

require_once 'class/auth.class.php';
require_once 'class/Respuestas.class.php';


$_auth = new auth;
$_responses = new respuestas;

if($_SERVER['REQUEST_METHOD']=="POST"){
  $postBody=file_get_contents("php://input");
  $datosArray=$_auth->login($postBody);
  header("Content-Type:application/json");
  if(isset($datosArray['result']['error_id'])){
    $responseCode=$datosArray['result']['error_id'];
    http_response_code($responseCode);
  }else{
    http_response_code(200);
  }
  echo  json_encode($datosArray); 
}else{
  header("Content-Type:application/json");
  $datosArray=$_responses->error_405();
  echo json_encode($datosArray);
}
