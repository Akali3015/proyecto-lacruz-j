<?php

use src\modelos\cambiosIvaModelo;
use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {

  $accion = $_POST["accion"];
  $monto = $_POST['monto_cambio_iva'] ?? "";

  $modeloCambiosIva = new cambiosIvaModelo();
  ob_clean();
  switch ($accion) {
    case "listar":
      $resultado = $modeloCambiosIva->seleccionarCambiosIva();
      echo json_encode($resultado);
      exit();
    case "actualizar":
      $resultado = $modeloCambiosIva->registrarCambiosIva($monto);
      echo json_encode($resultado);
      exit();
    default:
      echo json_encode(["error" => "Acción no reconocida"]);
      exit();
  }
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/cambios_iva/cambios_iva.php";
}
