<?php

use src\modelos\productosModelo;
use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['cedula'])) {

  $accion = $_POST['accion'] ?? "";
  $objeto = new productosModelo();
  ob_clean();
  switch ($accion) {
    case "listar":
    case "seleccionarUno":
      $resultado = $objeto->seleccionarProductos($_POST);
      break;
    case "registrar":
      $resultado = $objeto->registrarProductos($_POST);
      break;
    case "actualizar":
      $resultado = $objeto->actualizarProductos($_POST);
      break;
    case "eliminar":
      $resultado = $objeto->eliminarProductos($_POST);
      break;
    case "actualizarFoto":
      $resultado = $objeto->actualizarFotosProductos($_POST);
      break;
    case "eliminarFoto":
      $resultado = $objeto->eliminarFotosProductos($_POST);
      break;
    default:
      echo json_encode(["error" => "Acción no reconocida"]);
      exit();
  }
  $objeto->DECORE($resultado);
  exit();
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/productos/productos.php";
}
