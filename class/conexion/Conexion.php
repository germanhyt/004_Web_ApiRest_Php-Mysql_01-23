<?php


class Conexion
{

    private $server;
    private $user;
    private $password;
    private $database;
    private $port;
    private $conexion;


    function __construct()
    {
        $listadatos = $this->datosConexion();
        foreach ($listadatos as $key => $value) {
            $this->server = $value['server'];
            $this->user = $value['user'];
            $this->password = $value['password'];
            $this->database = $value['database'];
            $this->port = $value['port'];
        }

        // $this->conexion = new mysqli($this->server,$this->user,$this->password,$this->database,$this->port);
        $this->conexion = pg_connect("host=" . $this->server . " port=" . $this->port . " dbname=" . $this->database . " user=" . $this->user . " password=" . $this->password);


        if (pg_connection_status($this->conexion) != PGSQL_CONNECTION_OK) {
            echo "algo va mal con la conexion";
            die();
        }
    }

    //Para cargar los datos del JSON a un array
    private function datosConexion()
    {
        $direccion = dirname(__FILE__);
        $jsondata = file_get_contents($direccion . "/" . "config");
        return json_decode($jsondata, true); //Convertimos nuestro archivo JSON en un array
    }


    //Para convertir el array que obtenermos de la bd en utf-8
    private function convertirUTF8($array)
    {
        array_walk_recursive($array, function (&$item, $key) {
            if (!mb_detect_encoding($item, 'utf-8', true)) {
                $item = mb_convert_encoding($item, 'utf-8', "ISO-8859-1");  //se codifica a utf-8
            }
        });
        return $array;
    }

    //Para realizar la consulta a la bbdd, recibir el array de datos, y convertirlo en utf-8
    public function obtenerDatos($sqlstr)
    {

        $results = pg_fetch_all(pg_query($this->conexion, $sqlstr));
        print_r($results);

        return $this->convertirUTF8($results);
    }

    //Insertamos un registro y nos retorna la cantidad de filas afectadas
    public function nonQuery($sqlstr)
    {
        $results = pg_query($this->conexion, $sqlstr);

        return pg_affected_rows($results);
    }


    //Insertamos un registro y nos devulve el id intertado 
    public function nonQueryId($sqlstr)
    {
        $results = pg_query($this->conexion, $sqlstr);
        $filas = pg_affected_rows($results);
        if ($filas >= 1) {
            return pg_fetch_result($results, 0, 0);
        } else {
            return 0;
        }
    }

    //encriptar
    protected function encriptar($string)
    {
        return md5($string);
    }
}


// $obj = new conexion();
// $query = "INSERT INTO products(name,description,price,id_category,created_at,updated_at) VALUES('nuevo','descrip 6','12','1','12-10-2022','23-10-2022') RETURNING id";
// $result = $obj->nonQueryId($query);
// var_dump($result);
// print_r($result);
