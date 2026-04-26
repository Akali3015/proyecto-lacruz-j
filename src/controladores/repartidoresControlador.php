<?php

use src\modelos\repartidoresModelo;
use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {

  $accion = $_POST["accion"];
  $CedulaRepartidor = $_POST['cedula_repartidor'] ?? "";
  $codigoRifCedula = $_POST['codigo_rif_cedula_repartidor'] ?? "";
  $rifCedulaCompleto = $codigoRifCedula . $CedulaRepartidor;
  $nombre = $_POST['nombre_repartidor'] ?? "";
  $apellido = $_POST['apellido_repartidor'] ?? "";
  $telefono = $_POST['telefono_repartidor'] ?? "";

  $objeto = new repartidoresModelo();
  ob_clean();
  switch ($accion) {
    case 'listar':
      $resultado = $objeto->seleccionarRepartidor();
      echo json_encode($resultado);
      exit();
    case 'seleccionarUno':
      $resultado = $objeto->seleccionarRepartidor($rifCedulaCompleto);
      echo json_encode($resultado);
      exit();
    case 'registrar':
      $resultado = $objeto->registrarRepartidor($rifCedulaCompleto, $nombre, $apellido, $telefono);
      echo json_encode($resultado);
      exit();
    case 'actualizar':
      $resultado = $objeto->actualizarRepartidor($rifCedulaCompleto, $nombre, $apellido, $telefono);
      echo json_encode($resultado);
      exit();
    case 'eliminar':
      $resultado = $objeto->eliminarRepartidor($rifCedulaCompleto);
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
  require_once "src/vistas/repartidores/repartidores.php";
}