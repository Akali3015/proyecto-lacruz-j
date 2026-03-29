<?php

use src\modelos\reportesModelo;
use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $datos = file_get_contents('php://input');
  $datos = json_decode($datos, true);
  $reporte = isset($datos["reporte"]) ? $datos["reporte"] : $_POST["reporte"];

  $filtrosVenta = $datos["ventas"][0] ?? '';
  $filtrosCompras = $datos["compras"][0] ?? '';
  $filtrosMateria = $datos["materias"][0] ?? '';
  $filtrosServicios = $datos["servicios"][0] ?? '';
  $filtrosProductos = $datos["productos"][0] ?? '';
  $idVenta = $_POST["id_venta"] ?? '';
  $filtrosCierre = $datos["cierre"][0] ?? '';
  $objReportes = new reportesModelo('P', 'mm', array(215, 280));

  ob_clean();
  switch ($reporte) {
    case "reporte_ventas":
      $filtrosVentas = [
        'tipo_item' => $datos['tipo_producto'] ?? 'todos',
        'id_item' => $datos['id_item'] ?? '',
        'periodo' => $datos['periodo'] ?? 'dia',
        'fecha_desde' => $datos['fecha_desde'] ?? '',
        'fecha_hasta' => $datos['fecha_hasta'] ?? '',
        'mes' => $datos['mes'] ?? date('m'),
        'anio' => $datos['anio'] ?? date('Y')
      ];

      $resultado = $objReportes->VentasParametrizadas_Rep($filtrosVentas);
      if ($resultado instanceof \FPDF) {
        $resultado->Output('I', 'Reporte_Ventas.pdf');
      } else {
        $objReportes->DECORE($resultado);
      }
      exit();
    case "reporte_compras":
      $filtrosCompras = [
        'tipo_materia' => $datos['tipo_materia'] ?? 'todos',
        'id_materia' => $datos['id_materia'] ?? '',
        'periodo' => $datos['periodo'] ?? 'dia',
        'fecha_desde' => $datos['fecha_desde'] ?? '',
        'fecha_hasta' => $datos['fecha_hasta'] ?? '',
        'mes' => $datos['mes'] ?? date('m'),
        'anio' => $datos['anio'] ?? date('Y')
      ];
      $resultado = $objReportes->comprasParametrizadas_Rep($filtrosCompras);
      $objReportes->DECORE($resultado);
      exit();
    case "reporte_cierre_caja":
      $filtrosCierre = ['fecha' => $datos['fecha'] ?? date('Y-m-d')];
      $resultado = $objReportes->Cierre_Rep($filtrosCierre);
      $objReportes->DECORE($resultado);
      exit();
    case "reporte_servicios":
      $filtrosServicios = isset($datos["servicios"]) ? $datos["servicios"][0] : [];
      $resultado = $objReportes->Servicios_Rep($filtrosServicios);
      $objReportes->DECORE($resultado);
      exit();
    case "reporte_productos":
      $filtrosProductos = [
        'nombre' => isset($datos["nombre_producto"]) ? $datos["nombre_producto"] : ''
      ];
      $resultado = $objReportes->Productos_Rep($filtrosProductos);
      $objReportes->DECORE($resultado);
      exit();
    case "reporte_materia_prima":
      $filtrosMateria = isset($datos["materias"]) ? $datos["materias"][0] : [];
      $resultado = $objReportes->Materia_Rep($filtrosMateria);
      $objReportes->DECORE($resultado);
      exit();
    default:
      $objReportes->DECORE([
        "icono" => "error",
        'titulo' => 'No se reconoce el tipo de reporte'
      ]);
      exit();
  }
} else {
  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/reportes/reportes.php";
}
