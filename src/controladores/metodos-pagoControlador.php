<?php

use src\modelos\metodosPagoModelo;
use src\config\inc\componentesModelo;
use src\modelos\accesosModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {

  $objeto = new metodosPagoModelo();
  $accion = $_POST["accion"];
  ob_clean();
  $resultado = [
    'icono' => 'error',
    'titulo' => 'Acción no reconocida'
  ];
  switch ($accion) {
    case "listar":
      $resultado = $objeto->seleccionarMetodosPagos($_POST);
      break;
    case "seleccionarUno":
      $resultado = $objeto->seleccionarMetodosPagos($_POST);
      break;
    case "registrar":
      $resultado = $objeto->registrarMetodosPagos($_POST);
      break;
    case "actualizar":
      $resultado = $objeto->actualizarMetodosPagos($_POST);
      break;
    case "eliminar":
      $resultado = $objeto->eliminarMetodosPagos($_POST);
      break;
  }
  $objeto->DECORE($resultado);
  exit();
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
  $objAcceso = new accesosModelo();
  $v = $objAcceso->validarPermisos('metodos-pago', 'vero');
  if ($v) $objAcceso->DECORE($v);

  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/metodos-pago/metodos-pago.php";
} else {
  http_response_code(403);
}
