<?php

use src\modelos\bancosModelo;
use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {
  $accion = $_POST["accion"];
  $objeto = new bancosModelo();
  ob_clean();
  $resultado = [];

  switch ($accion) {
    case "listar":
    case "seleccionarUno":
      $resultado = $objeto->seleccionarBancos($_POST);
      break;
    case "registrar":
      $resultado = $objeto->registrarBancos($_POST);
      break;
    case "actualizar":
      $resultado = $objeto->actualizarBancos($_POST);
      break;
    case "eliminar":
      $resultado = $objeto->eliminarBancos($_POST);
      break;
    default:
      $resultado = ["error" => "Acción no reconocida"];
  }
  $objeto->DECORE($resultado);
  exit();
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/bancos/bancos.php";
} else {
  http_response_code(403);
}
