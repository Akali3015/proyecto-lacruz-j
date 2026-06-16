<?php

use src\controladores\frontController;
use src\config\connect\errorBD;

try {
  require_once 'vendor/autoload.php';
  require_once 'src/config/const.php';
  require_once 'src/config/inc/errorHandler.php';
  require_once 'src/config/inc/sesion.php';
  require_once 'src/config/inc/head.php';
  $controlador = new frontController;
  require_once 'src/config/inc/script.php';
} catch (errorBD $th) {
  ob_end_clean();
  echo json_encode($th->getDetalles());
  exit();
} catch (\Throwable $th) {
  $error =  [
    'titulo' => 'Error del codigo PHP',
    'linea' => $th->getLine(),
    'código de error' => $th->getCode(),
    'mensaje de error' => $th->getMessage(),
    'Rastro' => $th->getTrace(),
  ];
  ob_end_clean();
  $_SESSION['codigoRequest'] = 500;
  echo json_encode($error);
  exit();
  // echo 'Error: ' . $th->getCode();
}
