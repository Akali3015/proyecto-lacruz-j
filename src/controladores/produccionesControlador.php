<?php

use src\modelos\produccionesModelo;
use src\config\inc\componentesModelo;
use src\modelos\accesosModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['cedula'])) {

  if (isset($_POST['productos']) && is_string($_POST['productos'])) {
    $_POST['productos'] = json_decode($_POST['productos'], true);
  }

  $accion = $_POST['accion'] ?? '';
  $objeto = new produccionesModelo();
  ob_clean();
  $resultado = [
    'icono' => 'error',
    'titulo' => 'Acción no reconocida'
  ];
  
  switch ($accion) {
    case "listar":
      $resultado = $objeto->seleccionarProducciones($_POST);
      break;
    case "seleccionarUno":
      $resultado = $objeto->seleccionarProducciones($_POST);
      break;
    case "registrar":
      $resultado = $objeto->registrarProducciones($_POST);
      break;
    case "actualizar":
      $resultado = $objeto->actualizarProducciones($_POST);
      break;
  }
  $objeto->DECORE($resultado);
  exit();
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
  $objAcceso = new accesosModelo();
  $v = $objAcceso->validarPermisos('producciones', 'ver');
  if ($v) $objAcceso->DECORE($v);

  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/producciones/producciones.php";
}