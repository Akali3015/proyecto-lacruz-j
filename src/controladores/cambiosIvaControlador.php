<?php

use src\modelos\cambiosIvaModelo;
use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {

  $accion = $_POST["accion"];

  $modeloCambiosIva = new cambiosIvaModelo();
  ob_clean();
  $resultado=[
    'icono'=>'error',
    'titulo'=>'Acción no reconocida'
  ];
  switch ($accion) {
    case "listar":
      $resultado = $modeloCambiosIva->seleccionarCambiosIva($_POST);
      break;
    case "actualizar":
      $resultado = $modeloCambiosIva->registrarCambiosIva($_POST);
      break;
    default:
      $modeloCambiosIva->DECORE([
        'icono'=>'error',
        'titulo'=>'Acción no reconocida'
      ]);
      exit();
  }
  $modeloCambiosIva->DECORE($resultado);
  exit();
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/cambios_iva/cambios_iva.php";
}
