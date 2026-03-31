<?php

use src\modelos\rutasModelo;
use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {

  $accion = $_POST["accion"];
  $id = $_POST['id_ruta'] ?? "";
  $nombre = $_POST['nombre_ruta'] ?? "";
  $precio = $_POST['precio_ruta'] ?? "";
  $minimoKm = $_POST['minimo_km_ruta'] ?? "";
  $maximoKm = $_POST['maximo_km_ruta'] ?? "";
  $objeto = new rutasModelo();
  ob_clean();
  switch ($accion) {
    case "listar":
      $resultado = $objeto->seleccionarRutas();
      echo json_encode($resultado);
      exit();
    case "seleccionarUno":
      $resultado = $objeto->seleccionarRutas($id);
      echo json_encode($resultado);
      exit();
    case "registrar":
      $resultado = $objeto->registrarRutas($nombre, $precio, $minimoKm, $maximoKm);
      echo json_encode($resultado);
      exit();
    case "actualizar":
      $resultado = $objeto->actualizarRutas($id, $nombre, $precio, $minimoKm, $maximoKm);
      echo json_encode($resultado);
      exit();
    case "eliminar":
      $resultado = $objeto->eliminarRutas($id);
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
  require_once "src/vistas/rutas/rutas.php";
}
