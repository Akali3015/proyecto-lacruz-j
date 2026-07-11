<?php

use src\modelos\pagosModelo;
use src\config\inc\componentesModelo;
use src\modelos\accesosModelo;

if (
  $_SERVER["REQUEST_METHOD"] === "POST" &&
  isset($_POST["accion"]) &&
  isset($_SESSION['cedula'])
) {
  $accion = $_POST["accion"];
  $objeto = new pagosModelo();
  ob_clean();

  $resultado = [
    'tipo'   => 'simple',
    'icono'  => 'error',
    'titulo' => 'Acción no válida',
    'texto'  => 'La acción solicitada no existe',
  ];

  switch ($accion) {
    case 'listar':
      $resultado = $objeto->listarPagos($_POST);
      break;
    case 'obtenerUno':
      $resultado = $objeto->obtenerDetallePago($_POST);
      break;
    case 'obtenerOEPs':
      $resultado = $objeto->listarOEPs($_POST);
      break;
    case 'registrar':
      $resultado = $objeto->registrarPago($_POST);
      break;
    case 'actualizar':
      $resultado = $objeto->actualizarPago($_POST);
      break;
    case 'eliminar':
      $resultado = $objeto->eliminarPago($_POST);
      break;
    case 'eliminarComprobante':
      $resultado = $objeto->eliminarComprobante($_POST);
      break;
  }

  $objeto->DECORE($resultado);
  exit();

} elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
  $objAcceso = new accesosModelo();
  $v = $objAcceso->validarPermisos('pagos', 'ver');
  if ($v) $objAcceso->DECORE($v);

  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/pagos/pagos.php";
} else {
  http_response_code(405);
  echo json_encode([
    'tipo'   => 'simple',
    'titulo' => 'Método no permitido',
    'texto'  => 'Solo se permiten peticiones GET y POST',
    'icono'  => 'error',
  ]);
}
