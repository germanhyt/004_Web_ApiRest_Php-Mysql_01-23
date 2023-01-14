<?php
require_once "conexion/conexion.php";
require_once "respuestas.class.php";


class Products extends Conexion
{

    //Atributos
    private $table = "products";
    private $productid = "";
    private $description = "";
    private $price = "";
    private $categoryid = "";
    private $created_at = "";
    private $updated_at = "";
    private $token = "";
    private $imagen = "";


    //Método para listar pacientes porm paginación de 100 en 100
    public function listaProducts($pagina = 1)
    {

        $inicio  = 0;
        $cantidad = 100;
        if ($pagina > 1) {
            $inicio = ($cantidad * ($pagina - 1)) + 1;
            $cantidad = $cantidad * $pagina;
        }
        $query = "SELECT id,description,price,categoryid FROM " . $this->table . " limit $inicio,$cantidad";
        $datos = parent::obtenerDatos($query);
        return ($datos);
    }

    //Método para  obtener datos de un paciente por id
    public function obtenerProduct($id)
    {

        $query = "SELECT * FROM " . $this->table . " WHERE id = '$id'";
        return parent::obtenerDatos($query);
    }

    //Método para insertar un paciente mediante el método post
    public function post($json)
    {
        // ruta: 
        // envio: 

        $_respuestas = new respuestas;
        $datos = json_decode($json, true);

        if (!isset($datos['token'])) {
            return $_respuestas->error_401();
        } else {
            $this->token = $datos['token'];
            $arrayToken =   $this->buscarToken();
            if ($arrayToken) {

                if (!isset($datos['description']) || !isset($datos['price']) || !isset($datos['categoryid'])) {
                    return $_respuestas->error_400();
                } else {
                    $this->description = $datos['description'];
                    $this->price = $datos['price'];
                    $this->categoryid = $datos['categoryid'];
                    if (isset($datos['description'])) {
                        $this->description = $datos['description'];
                    }
                    if (isset($datos['price'])) {
                        $this->price = $datos['price'];
                    }
                    if (isset($datos['categoryid'])) {
                        $this->categoryid = $datos['categoryid'];
                    }
                    if (isset($datos['created_at'])) {
                        $this->created_at = $datos['created_at'];
                    }
                    if (isset($datos['updated_at'])) {
                        $this->updated_at = $datos['updated_at'];
                    }

                    if (isset($datos['imagen'])) {
                        // echo "hola image";
                        $resp = $this->procesarImagen($datos['imagen']);
                        $this->imagen = $resp;
                    }

                    $resp = $this->insertarProduct();
                    if ($resp) {
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "pacienteId" => $resp
                        );
                        return $respuesta;
                    } else {
                        return $_respuestas->error_500();
                    }
                }
            } else {
                return $_respuestas->error_401("El Token que envio es invalido o ha caducado");
            }
        }
    }

    private function procesarImagen($img)
    {
        $direccion = dirname(__DIR__) . "\public\imagenes\\"; //direccion

        $partes = explode(";base64", $img); //separa una cadena de otra(devolviendo un array de las cadenas sobrantes) , el 1er parámetro es el que se desea quitar, y el 2do es la cadena original
        // print_r($partes); echo " partes";
        $extension = explode('/', mime_content_type($img))[1]; //Separa la parte del array([0]=>"image", [1]=> "png")
        // print_r($extension); echo "extension";
        $imagen_base64 = base64_decode($partes[1]);
        $file = $direccion . uniqid() . "." . $extension;
        file_put_contents($file, $imagen_base64);
        $nueva_direccion = str_replace("\\", '/', $file);

        return $nueva_direccion;
    }


    private function insertarProduct()
    {
        $query = "INSERT INTO " . $this->table . " (description,price,categoryid,created_at,updated_at.imagen)
        values
        ('" . $this->description . "','" . $this->price . "','" . $this->categoryid . "','" . $this->created_at . "','"  . $this->updated_at . "')";
        $resp = parent::nonQueryId($query);
        if ($resp) {
            return $resp;
        } else {
            return 0;
        }
    }

    public function put($json)
    {
        $_respuestas = new respuestas;
        $datos = json_decode($json, true);

        if (!isset($datos['token'])) {
            return $_respuestas->error_401();
        } else {
            $this->token = $datos['token'];
            $arrayToken =   $this->buscarToken();
            if ($arrayToken) {
                if (!isset($datos['pacienteId'])) {
                    return $_respuestas->error_400();
                } else {
                    $this->productid = $datos['id'];
                    if (isset($datos['description'])) {
                        $this->description = $datos['description'];
                    }
                    if (isset($datos['price'])) {
                        $this->price = $datos['price'];
                    }
                    if (isset($datos['categoryid'])) {
                        $this->categoryid = $datos['categoryid'];
                    }
                    if (isset($datos['created_at'])) {
                        $this->created_at = $datos['created_at'];
                    }
                    if (isset($datos['updated_at'])) {
                        $this->updated_at = $datos['updated_at'];
                    }

                    $resp = $this->modificarProduct();
                    if ($resp) {
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "pacienteId" => $this->productid
                        );
                        return $respuesta;
                    } else {
                        return $_respuestas->error_500();
                    }
                }
            } else {
                return $_respuestas->error_401("El Token que envio es invalido o ha caducado");
            }
        }
    }


    private function modificarProduct()
    {
        $query = "UPDATE " . $this->table . " SET description ='" . $this->description . "',price ='" . $this->price . "', categoryid ='" . $this->categoryid . "', created_at ='" . $this->created_at . "', updated_at ='" . $this->updated_at;
        $resp = parent::nonQuery($query);
        if ($resp >= 1) {
            return $resp;
        } else {
            return 0;
        }
    }


    public function delete($json)
    {
        $_respuestas = new respuestas;
        $datos = json_decode($json, true);

        if (!isset($datos['token'])) {
            return $_respuestas->error_401();
        } else {
            $this->token = $datos['token'];
            $arrayToken =   $this->buscarToken();
            if ($arrayToken) {

                if (!isset($datos['id'])) {
                    return $_respuestas->error_400();
                } else {
                    $this->productid = $datos['productid'];
                    $resp = $this->eliminarProduct();
                    if ($resp) {
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "id" => $this->productid
                        );
                        return $respuesta;
                    } else {
                        return $_respuestas->error_500();
                    }
                }
            } else {
                return $_respuestas->error_401("El Token que envio es invalido o ha caducado");
            }
        }
    }


    private function eliminarProduct()
    {
        $query = "DELETE FROM " . $this->table . " WHERE id= '" . $this->productid . "'";
        $resp = parent::nonQuery($query);
        if ($resp >= 1) {
            return $resp;
        } else {
            return 0;
        }
    }


    private function buscarToken()
    {
        $query = "SELECT  TokenId,UsuarioId,Estado from usuarios_token WHERE Token = '" . $this->token . "' AND Estado = 'Activo'";
        $resp = parent::obtenerDatos($query);
        if ($resp) {
            return $resp;
        } else {
            return 0;
        }
    }

    private function actualizarToken($tokenid)
    {
        $date = date("Y-m-d H:i");
        $query = "UPDATE usuarios_token SET Fecha = '$date' WHERE TokenId = '$tokenid' ";
        $resp = parent::nonQuery($query);
        if ($resp >= 1) {
            return $resp;
        } else {
            return 0;
        }
    }
}
