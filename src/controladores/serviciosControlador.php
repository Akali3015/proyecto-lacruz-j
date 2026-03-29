<?php

use src\modelos\serviciosModelo;
use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {

  $accion = $_POST["accion"];
  $id = $_POST['id_servicio'] ?? "";
  $idUnidadMedida = $_POST['id_unidad_medida'] ?? "";
  $nombre = $_POST['nombre_servicio'] ?? "";
  $costo = $_POST['costo_servicio'] ?? "";

  $objeto = new serviciosModelo();
  ob_clean();
  switch ($accion) {
    case "listar":
      $resultado = $objeto->seleccionarServicios();
      echo json_encode($resultado);
      exit();
    case "seleccionarUno":
      $resultado = $objeto->seleccionarServicios($id);
      echo json_encode($resultado);
      exit();
    case "registrar":
      $resultado = $objeto->registrarServicios($idUnidadMedida, $nombre, $costo);
      echo json_encode($resultado);
      exit();
    case "actualizar":
      $resultado = $objeto->actualizarServicios($id, $idUnidadMedida, $nombre, $costo);
      echo json_encode($resultado);
      exit();
    case "eliminar":
      $resultado = $objeto->eliminarServicios($id);
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
  require_once "src/vistas/servicios/servicios.php";
}
