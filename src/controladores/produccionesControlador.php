<?php

use src\modelos\produccionesModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {

  $inputJSON = file_get_contents('php://input');
  $data = json_decode($inputJSON, true);

  $accion = $data["accion"] ?? $_POST['accion'] ?? "";
  $id = $data['id_produccion'] ?? $_POST['id_produccion'] ?? "";
  $detalles = $data['productos'] ?? [];

  $objeto = new produccionesModelo();
  ob_clean();
  switch ($accion) {
    case "listar":
      $resultado = $objeto->seleccionarProduccion();
      echo json_encode($resultado);
      exit();
    case "seleccionarUno":
      $resultado = $objeto->seleccionarProduccion($id);
      echo json_encode($resultado);
      exit();
    case "registrar":
      $resultado = $objeto->registrarProduccion($detalles);
      echo json_encode($resultado);
      exit();
    case "actualizar":
      $resultado = $objeto->actualizarProduccion($id, $detalles);
      echo json_encode($resultado);
      exit();
    default:
      echo json_encode(["error" => "Acción no reconocida"]);
      exit();
  }
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
  require_once "src/config/inc/header.php";
  require_once "src/config/inc/sidebar.php";
  require_once "src/vistas/producciones/producciones.php";
}
