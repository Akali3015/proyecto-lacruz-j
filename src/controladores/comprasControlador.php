<?php

use src\modelos\comprasModelo;
use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['cedula'])) {

  $accion = $_POST['accion'] ?? '';

  $objetoCompras = new comprasModelo();

  $resultado = [
    'icono'  => 'error',
    'titulo' => 'Acción no reconocida'
  ];

  ob_clean();
  switch ($accion) {
    case 'listar':
    case 'seleccionarUno':
      $resultado = $objetoCompras->seleccionarCompra($_POST);
      break;
    case 'listarProductosParaCompra':
      $resultado = $objetoCompras->listarProductosParaCompra();
      break;
    case 'registrar':
      $resultado = $objetoCompras->registrarCompra($_POST);
      break;
    case 'actualizar':
      $resultado = $objetoCompras->actualizarCompra($_POST);
      break;
    case 'eliminar':
      $resultado = $objetoCompras->eliminarCompra($_POST);
      break;
  }

  $objetoCompras->DECORE($resultado);
  exit();
} elseif ($_SERVER["REQUEST_METHOD"] === "GET") {

  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/compras/compras.php";
}
