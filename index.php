<?php

use src\controladores\frontController;
use src\modelos\errorBD;

try {
    require_once 'vendor/autoload.php';
    require_once 'src/config/const.php';
    session_name(APP_SESSION_NAME);
    session_start();
    require_once 'src/config/inc/head.php';
    $controlador = new frontController;
    require_once 'src/config/inc/script.php';
} catch (errorBD $th) {
    echo json_encode($th->getDetalles());
} catch (\Throwable $th) {
    $error = [
        'mensaje de error' => $th->getMessage(),
        'código de error' => $th->getCode(),
        'linea' => $th->getLine(),
        'rastro' => $th->getTrace(),
    ];
    echo json_encode($error);
    // echo 'Error: ' . $th->getCode();
}
