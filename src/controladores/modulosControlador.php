<?php

use src\modelos\modulosModelo;
use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {

  $accion = $_POST["accion"];
  $objeto = new modulosModelo();
  ob_clean();
  $resultado = [
    'icono' => 'error',
    'titulo' => 'Acción no reconocida'
  ];
  switch ($accion) {
    case "listar":
    case "seleccionarUno":
      $resultado = $objeto->seleccionarModulos($_POST);
      break;
    case "registrar":
      $resultado = $objeto->registrarModulos($_POST);
      break;
    case "actualizar":
      $resultado = $objeto->actualizarModulos($_POST);
      break;
    case "eliminar":
      $resultado = $objeto->eliminarModulos($_POST);
      break;
  }
  $objeto->DECORE($resultado);
  exit();
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/modulos/modulos.php";
}
