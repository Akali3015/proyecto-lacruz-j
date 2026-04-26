<?php

use src\modelos\presentacionesModelo;
use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {

  $accion = $_POST["accion"];
  $id = $_POST['id_presentacion'] ?? "";
  $idUnidadMedida = $_POST['id_unidad_medida'] ?? "";
  $nombre = $_POST['nombre_presentacion'] ?? "";
  $cantidadPmp = $_POST['cantidad_pmp'] ?? "";

  $objeto = new presentacionesModelo();
  ob_clean();
  switch ($accion) {
    case "listar":
      $resultado = $objeto->seleccionarPresentaciones();
      echo json_encode($resultado);
      exit();
    case "seleccionarUno":
      $resultado = $objeto->seleccionarPresentaciones($id);
      echo json_encode($resultado);
      exit();
    case "registrar":
      $resultado = $objeto->registrarPresentaciones($id, $idUnidadMedida, $nombre, $cantidadPmp);
      echo json_encode($resultado);
      exit();
    case "actualizar":
      $resultado = $objeto->actualizarPresentaciones($id, $idUnidadMedida, $nombre, $cantidadPmp);
      echo json_encode($resultado);
      exit();
    case "eliminar":
      $resultado = $objeto->eliminarPresentaciones($id);
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
  require_once "src/vistas/presentaciones/presentaciones.php";
}
