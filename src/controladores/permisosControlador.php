<?php

use src\modelos\permisosModelo;
use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {

  $accion = $_POST["accion"];
  $objeto = new permisosModelo();
  ob_clean();
  $resultado = [
    'icono' => 'error',
    'titulo' => 'Acción no reconocida'
  ];
  switch ($accion) {
    case "listar":
    case "seleccionarUno": 
      $resultado = $objeto->seleccionarPermisos($_POST);
      break;
    case "registrar":
      $resultado = $objeto->registrarPermisos($_POST);
      break;
    case "actualizar":
      $resultado = $objeto->actualizarPermisos($_POST);
      break;
    case "eliminar":
      $resultado = $objeto->eliminarPermisos($_POST);
      break;
  }
  $objeto->DECORE($resultado);
  exit();
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/permisos/permisos.php";
}