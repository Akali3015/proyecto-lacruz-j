<?php

use src\modelos\pedidosModelo;
use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {

  $accion = $_POST["accion"];

  $objeto = new pedidosModelo();
  ob_clean();
  $resultado = [
    'icono' => 'error',
    'titulo' => 'Acción no reconocida'
  ];
  switch ($accion) {
    case 'listar':
    case 'seleccionarUno':
      $resultado = $objeto->listarPedidos($_POST);
      break;
    case 'registrar':
      $resultado = $objeto->registrarPedidos($_POST);
      break;
    case 'asignarRepartidor':
      $resultado = $objeto->asignarRepartidoresPedidos($_POST);
      break;
    case 'cambiarEstado':
      $resultado = $objeto->actualizarPedido($_POST);
      break;
  }
  $objeto->DECORE($resultado);
  exit();
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/pedidos/pedidos.php";
}
