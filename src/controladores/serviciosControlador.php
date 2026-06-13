<?php

use src\modelos\serviciosModelo;
use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {

  $accion = $_POST["accion"];
  $objeto = new serviciosModelo();
  ob_clean();
  $resultado = [
    'icono' => 'error',
    'titulo' => 'Acción no reconocida'
  ];
  switch ($accion) {
    case 'listar':
      $resultado = $objeto->seleccionarServicios($_POST);
      echo json_encode($resultado);
      exit();
    case 'seleccionarUno':
      $resultado = $objeto->seleccionarServicios($_POST);
      echo json_encode($resultado);
      exit();
    case 'registrar':
      $resultado = $objeto->registrarServicio($_POST);
      echo json_encode($resultado);
      exit();
    case 'actualizar':
      $resultado = $objeto->actualizarServicio($_POST);
      echo json_encode($resultado);
      exit();
    case 'eliminar':
      $resultado = $objeto->eliminarServicio($_POST);
      echo json_encode($resultado);
      exit();
    case 'actualizarFoto':
      $resultado = $objeto->actualizarFotoServicio($_POST);
      echo json_encode($resultado);
      exit();
    case 'eliminarFoto':
      $resultado = $objeto->eliminarFotoServicio($_POST);
      echo json_encode($resultado);
      exit();
    default:
      $objeto->DECORE([
        'icono' => 'error',
        'titulo' => 'Acción no reconocida'
      ]);
      exit();
  }
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/servicios/servicios.php";
}
