<?php

use src\modelos\inventarioModelo;
use src\config\inc\componentesModelo;
use src\modelos\accesosModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"])) {
  
  $accion = $_POST["accion"] ?? '';
  $modeloInventarios = new inventarioModelo();
  
  ob_clean();
  $resultado = [
    "icono" => "error",
    "titulo" => "Accion no reconocida",
    "texto" => "La acción solicitada no es valida"
  ];

  switch ($accion) {
    case "registrarMovimientosProductos":
      $_POST['tipo_item'] = 'producto';
      $resultado = $modeloInventarios->registrarMovimientos($_POST);
      break;
    case "registrarMovimientosMateriasPrimas":
      $_POST['tipo_item'] = 'materia_prima';
      $resultado = $modeloInventarios->registrarMovimientos($_POST);
      break;
    case "verEntradasSalidas":
      $resultado = $modeloInventarios->verEntradasSalidas($_POST);
      break;
    case "reporteProductos":
      $resultado = $modeloInventarios->reporteProductos($_POST);
      break;
    case "reporteMateriasPrimas":
      $resultado = $modeloInventarios->reporteMateriasPrimas($_POST);
      break;
  }
  $modeloInventarios->DECORE($resultado);
  exit();
} else {
  $objAcceso= new accesosModelo();
  $v= $objAcceso->validarPermisos('inventario','ver inventario');
  if($v) $objAcceso->DECORE($v);

  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/inventario/inventario.php";
}