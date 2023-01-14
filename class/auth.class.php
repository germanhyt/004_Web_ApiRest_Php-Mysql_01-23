<?php
require_once "conexion/Conexion.php";
require_once "Respuestas.class.php";

class auth extends Conexion
{

  //Login
  public function login($json)
  {
    $_responses = new respuestas;
    $datos = json_decode($json, true);
    if (!isset($datos['email']) || !isset($datos['password'])) {
      return $_responses->error_400();
    } else {
      $email = $datos['email'];
      $pass = $datos['password'];
      // $pass = parent::encriptar($pass);
      $datos = $this->obtenerDatosUser($email);
      if ($datos) {
        if ($pass == $datos[0]['password']) {
          if ($datos[0]['state'] == "Activo") {
            $verificar = $this->insertToken($datos[0]['id']);
            if ($verificar) {
              $result = $_responses->response;
              $result['result'] = array(
                "token" => $verificar
              );
              return $result;
            } else {
              return $_responses->error_500("Error interno, no se pudo completar la operación");
            }
          } else {
            return $_responses->error_200("El usuario está inactivo");
          }
        } else {
          return $_responses->error_200("El password es invalido");
        }
      } else {
        return $_responses->error_200("El usuario $email no existe");
      }
    }
  }

  private function obtenerDatosUser($email)
  {
    $query = "SELECT id,password,state FROM users WHERE email='$email'";
    $datos = parent::obtenerDatos($query);
    if (isset($datos[0]['id'])) {
      return $datos;
    } else {
      return 0;
    }
  }

  private function insertToken($userid)
  {
    $val = true;
    $token = bin2hex(openssl_random_pseudo_bytes(16, $val));
    $date = date("Y-m-d H:i");
    $state = "Activo";
    $query = "INSERT INTO users_token(userid,token,state,date) VALUES('$userid','$token','$state','$date')";
    $verifica = parent::nonQuery($query);
    if ($verifica) {
      return $token;
    } else {
      return 0;
    }
  }
}
