<?php

use src\modelos\metodosPagoModelo;
use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {
  $objeto = new metodosPagoModelo();
  $accion = $_POST["accion"];
  $id = $_POST['id_metodo_pago'] ?? "";
  ob_clean();
  $resultado = [];

  switch ($accion) {
    case "listar":
      $resultado = $objeto->seleccionarMetodosPagos();
      break;
    case "seleccionarUno":
      $resultado = $objeto->seleccionarMetodosPagos($id);
      break;
    case "registrar":
      $resultado = $objeto->registrarMetodosPagos($_POST);
      break;
    case "actualizar":
      $resultado = $objeto->actualizarMetodosPagos($id, $_POST);
      break;
    case "eliminar":
      $resultado = $objeto->eliminarMetodosPagos($id);
      break;
  }
  $objeto->DECORE($resultado);
  exit();
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/metodos-pago/metodos-pago.php";
} else {
  http_response_code(403);
}
