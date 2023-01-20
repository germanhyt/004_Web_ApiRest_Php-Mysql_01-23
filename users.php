<?php
require_once 'class/Respuestas.class.php';
require_once 'class/User.class.php';
// header("Access-Control-Allow-Origin: *");  
// header("Access-Control-Allow-Methods: *");  
// header("Access-Control-Allow-Headers: *");
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");



$_responses = new respuestas;
// $_users = new User;


if ($_SERVER['REQUEST_METHOD'] == "GET") {
  $_users = new User;
  if (isset($_GET['page'])) {
    $page = $_GET['page'];
    $listUsers = $_users->listarUsers($page);

    header("Content-Type: application/json");
    echo json_encode($listUsers);
    http_response_code(200);
  } else if (isset($_GET['id'])) {
    $userid = $_GET['id'];
    $datosUsers = $_users->obtenerUser($userid);

    header("Content-Type: application/json");
    echo json_encode($datosUsers);
    http_response_code(200);
  }
} else if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $_users = new User;
  $postBody = file_get_contents("php://input");
  $datosArray = $_users->post($postBody);
  header("Content-Type: application/json");
  if (isset($datosArray['result']['error_id'])) {
    $respcode = $datosArray['result']['error_id'];

    http_response_code($respcode);
  } else {

    http_response_code(200);
  } 
  echo json_encode($datosArray);
} else if ($_SERVER['REQUEST_METHOD'] == "PUT") {
  $_users = new User;
  $postBody = file_get_contents("php://input");
  $datosArray = $_users->put($postBody);
  header("Content-Type:application/json");
  if(isset($datosArray['result']['error_id'])){
    $respCode=$datosArray['result']['error_id'];
    http_response_code($respCode);
  }else{
    http_response_code(200);
  }
  echo json_encode($datosArray);
}else if($_SERVER['REQUEST_METHOD']=="DELETE"){
  $_users = new User;
  $headers=getallheaders();
  if(isset($headers['token'])&&isset($headers['id'])){
    $send=[
      "token"=>$headers['token'],
      "id"=>$headers['id']
    ];
    $postBody=json_encode($send);
  }else{
    $postBody=file_get_contents("php://input");
  }

  $datosArray=$_users->delete($postBody);
  header("Content-Type:application/json");
  if(isset($datosArray['result']['error_id'])){
    $respCode=$datosArray['result']['error_id'];
    http_response_code($respCode);
  }else{
    http_response_code(200);
  }
  echo json_encode($datosArray);

}else{
  header("Content-Type:application/json");
  $datosArray=$_responses->error_405();
  echo json_encode($datosArray);
}
