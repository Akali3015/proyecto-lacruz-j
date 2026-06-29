<?php

use src\modelos\reportesEstadisticosModelo;
use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {
  $accion = $_POST["accion"];
  $objReportes = new reportesEstadisticosModelo();
  ob_clean();

  $resultado = [
    "icono" => "error",
    'titulo' => 'Acción no reconocida'
  ];

  switch ($accion) {
    case 'obtenerDatosDashboard':
      $resultado = $objReportes->obtenerDatosDashboard($_POST);
      break;
  }

  echo json_encode($resultado);
  exit();
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/reportesEstadisticos/reportesEstadisticos.php";
}
