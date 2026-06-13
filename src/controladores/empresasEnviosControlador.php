<?php

use src\modelos\empresasEnviosModelo;
use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {

  $accion = $_POST["accion"];
  $objeto = new empresasEnviosModelo();
  ob_clean();
    $resultado = [
    'icono' => 'error',
    'titulo' => 'Acción no reconocida'
  ];
  switch ($accion) {
    case "listar":
    case "seleccionarUno":
      $resultado = $objeto->seleccionarEmpresasEnvios($_POST);
      break;
    case "registrar":
      $resultado = $objeto->registrarEmpresasEnvios($_POST);
      break;
    case "actualizar":
      $resultado = $objeto->actualizarEmpresasEnvios($_POST);
      break;
    case "eliminar":
      $resultado = $objeto->eliminarEmpresasEnvios($_POST);
      break;
  }
  $objeto->DECORE($resultado);
  exit();
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/empresasEnvios/empresasEnvios.php";
}
