<?php

use src\modelos\rutasModelo;
use src\config\inc\componentesModelo;
use src\modelos\accesosModelo;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {

  $accion = $_POST["accion"];
  $objeto = new rutasModelo();
  ob_clean();
  $resultado = [
    'icono' => 'error',
    'titulo' => 'Acción no reconocida'
  ];
  switch ($accion) {
    case "listar":
    case "seleccionarUno":
      $resultado = $objeto->seleccionarRutas($_POST);
      break;
    case "registrar":
      $resultado = $objeto->registrarRutas($_POST);
      break;
    case "actualizar":
      $resultado = $objeto->actualizarRutas($_POST);
      break;
    case "eliminar":
      $resultado = $objeto->eliminarRutas($_POST);
      break;
  }
  $objeto->DECORE($resultado);
  exit();
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
  $objAcceso= new accesosModelo();
  $v= $objAcceso->validarPermisos('rutas','ver');
  if($v) $objAcceso->DECORE($v);

  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/rutas/rutas.php";
}
