<?php

use src\controladores\frontController;
use src\modelos\errorBD;

try {
    require_once 'vendor/autoload.php';
    require_once 'src/config/const.php';
    require_once 'src/config/inc/helperError.php';
    session_name(APP_SESSION_NAME);
    session_start();
    require_once 'src/config/inc/head.php';
    $controlador = new frontController;
    require_once 'src/config/inc/script.php';
} catch (errorBD $th) {
    echo json_encode($th->getDetalles());
} catch (\Throwable $th) {
    $error =  [
        'titulo' => 'Error del codigo PHP',
        'linea' => $th->getLine(),
        'código de error' => $th->getCode(),
        'mensaje de error' => $th->getMessage(),
        'Rastro' => $th->getTrace(),
    ];
    echo json_encode($error);
    // echo 'Error: ' . $th->getCode();
}
