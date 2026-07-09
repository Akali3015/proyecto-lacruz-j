<?php

use src\modelos\bitacoraModelo;
use src\config\inc\componentesModelo;
use src\modelos\accesosModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {

  $accion = $_POST["accion"];

  $objeto = new bitacoraModelo();
  ob_clean();
  $resultado=[
    'icono'=>'error',
    'titulo'=>'Acción no reconocida'
  ];
  switch ($accion) {
    case "listar":
      $resultado = $objeto->seleccionarBitacora();
      break;
  }
  $objeto->DECORE($resultado);
  exit();
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
  $objAcceso= new accesosModelo();
  $v= $objAcceso->validarPermisos('bitacora','ver bitácora');
  if($v) $objAcceso->DECORE($v);

  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/bitacora/bitacora.php";
}