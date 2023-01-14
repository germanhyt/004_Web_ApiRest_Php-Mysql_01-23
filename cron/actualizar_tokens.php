<?php
    require_once __DIR__.'/../class/token.class.php';
    $_token = new token;
    date_default_timezone_set('America/Lima');
    $fecha = date('Y-m-d H:i');
    echo $_token->actualizarTokens($fecha);
?>