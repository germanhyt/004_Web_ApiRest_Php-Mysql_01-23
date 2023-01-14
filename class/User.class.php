<?php
require_once "conexion/Conexion.php";
require_once "Respuestas.class.php";


class User extends Conexion
{

    //Atributos
    private $table = "users";
    private $id = "";
    private $name = "";
    private $lastname = "";
    private $email = "";
    private $phone = "";
    private $password = "";
    private $created_at = "";
    private $updated_at = "";
    private $token = "";
    private $image = "";
    private $state = "";

    public function listarUsers($pagina = 1)
    {
        $inicio = 0;
        $cantidad = 100;
        if ($pagina > 1) {
            $inicio = ($cantidad * ($pagina - 1) + 1);
            $cantidad = $cantidad * $pagina;
        }
        $query = "SELECT id,name,lastname,email,phone FROM " . $this->table . " LIMIT $cantidad OFFSET $inicio";
        $datos = parent::obtenerDatos($query);

        return $datos;
    }



    public function post($json)
    {
        $_responses = new respuestas;
        $datos = json_decode($json, true);

        if (!isset($datos['token'])) {
            return $_responses->error_401();
        } else {
            $this->token = $datos['token'];
            $arraytoken = $this->buscarToken();
            if ($arraytoken) {
                if (!isset($datos['name']) || !isset($datos['lastname']) || !isset($datos['email'])) {
                    return $_responses->error_400();
                } else {
                    $this->name = $datos['name'];
                    $this->lastname = $datos['lastname'];
                    $this->email = $datos['email'];
                    $this->password = $datos['password'];
                    $this->created_at = $datos['created_at'];
                    $this->updated_at = $datos['updated_at'];
                    $this->state = $datos['state'];
                    if (isset($datos['phone'])) {
                        $this->phone = $datos['phone'];
                    }
                    if (isset($datos['image'])) {
                        $resp = $this->processimage($datos['image']);
                        $this->image = $resp;
                    }

                    $resp = $this->insertUser();
                    if ($resp) {
                        $response = $_responses->response;
                        $response['result'] = array(
                            "id" => $resp
                        );
                        return $response;
                    } else {
                        return $_responses->error_500();
                    }
                }
            } else {
                return $_responses->error_401("El token que se envió es invalido o ha caducado");
            }
        }
    }


    public function put($json)
    {
        $_responses = new respuestas;
        $datos = json_decode($json, true);

        if (!isset($datos['token'])) {
            return $_responses->error_401();
        } else {
            $this->token = $datos['token'];
            $arraytoken = $this->buscarToken();
            if (!$arraytoken) {
                return $_responses->error_401("El token que ha enviado en inválido o a caducado");
            } else {
                if (!isset($datos['id'])) {
                    return $_responses->error_400();
                } else {
                    $this->id = $datos['id'];
                    $this->updated_at = date("Y-m-d H:i");
                    if (isset($datos['name'])) {
                        $this->name = $datos['name'];
                    }
                    if (isset($datos['lastname'])) {
                        $this->lastname = $datos['lastname'];
                    }
                    if (isset($datos['email'])) {
                        $this->email = $datos['email'];
                    }
                    if (isset($datos['password'])) {
                        $this->password = $datos['password'];
                    }
                    if (isset($datos['phone'])) {
                        $this->phone = $datos['phone'];
                    }
                    if (isset($datos['image'])) {
                        $resp = $this->processimage($datos['image']);
                        $this->image = $resp;
                    }
                    if (isset($datos['state'])) {
                        $this->state = $datos['state'];
                    }

                    $resp = $this->setUser();
                    if (!$resp) {
                        return $_responses->error_500();
                    } else {
                        $response = $_responses->response;
                        $response['result'] = array(
                            "id" => $this->id
                        );
                        return $response;
                    }
                }
            }
        }
    }

    public function delete($json)
    {
        $_responses = new respuestas;
        $datos = json_decode($json, true);

        if (!isset($datos['token'])) {
            return $_responses->error_401("El token que es inválido o ha caducado");
        } else {
            $this->token = $datos['token'];
            $arraytoken = $this->buscarToken();
            if ($arraytoken) {
                if (!isset($datos['id'])) {
                    return $_responses->error_400();
                } else {
                    $this->id = $datos['id'];
                    $resp = $this->deleteUser();
                    if (!$resp) {
                        return $_responses->error_500();
                    } else {
                        $response = $_responses->response;
                        $response['result'] = array(
                            "id" => $this->id
                        );
                        return $response;
                    }
                }
            }
        }
    }



    public function obtenerUser($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id='$id'";
        return parent::obtenerDatos($query);
    }

    public function insertUser()
    {
        $query = "INSERT INTO " . $this->table . "(name,lastname,email,phone,password,image,created_at,updated_at,state) 
        VALUES('" . $this->name . "','" . $this->lastname . "','" . $this->email . "','" . $this->phone . "','" . $this->password . "','" . $this->image . "','" . $this->created_at . "','" . $this->updated_at . "','" . $this->state . "') RETURNING id";
        $resp = parent::nonQueryId($query);
        if ($resp) {
            return $resp;
        } else {
            return 0;
        }
    }

    private function buscarToken()
    {
        $query = "SELECT  tokenid,userid,state from Users_token WHERE token = '" . $this->token . "' AND state = 'Activo'";
        $resp = parent::obtenerDatos($query);
        if ($resp) {
            return $resp;
        } else {
            return 0;
        }
    }

    private function processimage($img)
    {
        $direccion = dirname(__DIR__) . "\public\images\\"; //direccion

        $partes = explode(";base64", $img);
        $extension = explode('/', mime_content_type($img))[1];
        $imagen_base64 = base64_decode($partes[1]);
        $file = $direccion . uniqid() . "." . $extension;
        file_put_contents($file, $imagen_base64);
        $nueva_direccion = str_replace("\\", '/', $file);

        return $nueva_direccion;
    }

    private function setUser()
    {
        $query = "UPDATE " . $this->table . " SET name='" . $this->name . "', lastname='" . $this->lastname . "', email='" . $this->email .
            "', password='" . $this->password . "', state='" . $this->state . "', phone='" . $this->phone . "', image='" . $this->image .
            "' WHERE id='" . $this->id . "'";
        $resp = parent::nonQuery($query);
        if ($resp >= 1) {
            return $resp;
        } else {
            return 0;
        }
    }

    private function deleteUser()
    {
        $query = "DELETE FROM " . $this->table . " WHERE id='" . $this->id . "'";
        $resp = parent::nonQuery($query);
        if ($resp >= 1) {
            return $resp;
        } else {
            return 0;
        }
    }
}

// $obj = new User();
// // $test=$bj->obtenerUser(1);
// $test = $obj->listarUsers();
// print_r($test);


// DROP TABLE IF EXISTS public."Users_token";

// CREATE TABLE IF NOT EXISTS public."Users_token"
// (
// 	tokenid int NOT NULL PRIMARY KEY,
//   	Userid varchar(45) DEFAULT NULL,
//   	token varchar(45) DEFAULT NULL,
//   	state varchar(45) DEFAULT NULL,
//   	date varchar(45)  DEFAULT NULL
// );

// ALTER TABLE Users_token ADD FOREIGN KEY (Userid) REFERENCES users(id);
