<?php

use src\modelos\proveedoresModelo;
use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion']) && isset($_SESSION['cedula'])) {

  $accion = $_POST['accion'];
  $objeto = new proveedoresModelo();
  ob_clean();
  $resultado = [
    'icono' => 'error',
    'titulo' => 'Acción no reconocida'
  ];
  switch ($accion) {
    case 'listar':
      $resultado = $objeto->seleccionarProveedores($_POST);
      break;
    case 'seleccionarUno':
      $resultado = $objeto->seleccionarProveedores($_POST);
      break;
    case 'registrar':
      $resultado = $objeto->registrarProveedores($_POST);
      break;
    case 'actualizar':
      $resultado = $objeto->actualizarProveedores($_POST);
      break;
    case 'eliminar':
      $resultado = $objeto->eliminarProveedores($_POST);
      break;
  }
  $objeto->DECORE($resultado);
  exit();
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/proveedores/proveedores.php";
}
