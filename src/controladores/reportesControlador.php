<?php

use src\modelos\reportesModelo;
use src\config\inc\componentesModelo;
use src\modelos\PDF;
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $reporte = $_POST["reporte"] ?? '';
  $objReportes = new reportesModelo();

  ob_clean();
  $resultado = [
    "icono" => "error",
    'titulo' => 'No se reconoce el tipo de reporte'
  ];
  switch ($reporte) {
    case "reporteVentas":
      $resultado = $objReportes->reporteVentas($_POST);
      break;
    case "reporteCompras":
      $resultado = $objReportes->reporteCompras($_POST);
      break;
    case "reporteCierre":
      $resultado = $objReportes->reporteCierre($_POST);
      break;
    case "reporteServicios":
      $resultado = $objReportes->reporteServicios();
      break;
    case "reporteProductos":
      $resultado = $objReportes->reporteProductos();
      break;
    case "reporteMateriaPrima":
      $resultado = $objReportes->reporteMateriaPrima();
      break;
  }
  $objReportes->DECORE($resultado);
  exit();
} else {
  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/reportes/reportes.php";
}
