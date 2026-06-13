<?php

use src\modelos\categoriasProductosModelo;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['cedula'])) {

  $accion = $datos["accion"] ?? $_POST['accion'] ?? "";
  $objeto = new categoriasProductosModelo();
  ob_clean();
  $resultado = [
    'icono' => 'error',
    'titulo' => 'Acción no reconocida'
  ];
  switch ($accion) {
    case "listar":
      $resultado = $objeto->seleccionarCategorias($_POST);
      break;
    case "seleccionarUno":
      $resultado = $objeto->seleccionarCategorias($_POST);
      break;
    case "registrar":
      $resultado = $objeto->registrarCategorias($_POST);
      break;
    case "actualizar":
      $resultado = $objeto->actualizarCategorias($_POST);
      break;
    case "eliminar":
      $resultado = $objeto->eliminarCategorias($_POST);
      break;
  }
  $objeto->DECORE($resultado);
  exit();
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
  $objComponentes = new \src\config\inc\componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/categoriasProductos/categoriasProductos.php";
}
