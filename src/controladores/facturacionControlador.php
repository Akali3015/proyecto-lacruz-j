<?php

use src\modelos\facturacionModelo;
use src\config\inc\componentesModelo;

if (
  $_SERVER["REQUEST_METHOD"] === "POST" &&
  isset($_POST["accion"]) &&
  isset($_SESSION['cedula'])
) {
  $accion  = $_POST["accion"];
  $objeto  = new facturacionModelo();
  ob_clean();

  switch ($accion) {
    case 'listar':
      $resultado = $objeto->ListarFacturas();
      echo json_encode($resultado);
      exit();

    case 'listarMetodosPago':
      $resultado = $objeto->ListarMetodosPago();
      echo json_encode($resultado);
      exit();

    case 'obtenerUno':
      $id = $_POST['id_factura'] ?? '';
      echo json_encode($objeto->ObtenerFactura($id));
      exit();

    case 'obtenerDetalle':
      $id = $_POST['id_factura'] ?? '';
      echo json_encode($objeto->ObtenerDetalleFactura($id));
      exit();

    case 'registrar':
      $rifCliente = trim($_POST['rif_cedula_cliente'] ?? '');
      $productos  = json_decode($_POST['productos']  ?? '[]', true);
      $servicios  = json_decode($_POST['servicios']  ?? '[]', true);
      $delivery   = json_decode($_POST['delivery']   ?? '{}', true);
      $estadoSel  = intval($_POST['estadoSeleccionado'] ?? 1);

      $resultado = $objeto->RegistrarFactura(
        $rifCliente,
        is_array($productos) ? $productos : [],
        is_array($servicios) ? $servicios : [],
        is_array($delivery)  ? $delivery  : [],
        $estadoSel
      );
      echo json_encode($resultado);
      exit();

    case 'registrarPago':
      $idFactura = $_POST['id_factura'] ?? '';
      $pagosArray = json_decode($_POST['pagos'] ?? '[]', true);
      
      echo json_encode($objeto->RegistrarPago($idFactura, is_array($pagosArray) ? $pagosArray : []));
      exit();

    case 'despachar':
      $id = $_POST['id_factura'] ?? '';
      echo json_encode($objeto->DespacharFactura($id));
      exit();

    case 'anular':
      $id = $_POST['id_factura'] ?? '';
      echo json_encode($objeto->AnularFactura($id));
      exit();

    default:
      echo json_encode([
        'tipo'   => 'simple',
        'titulo' => 'Acción no válida',
        'texto'  => 'La acción solicitada no existe',
        'icono'  => 'error',
      ]);
      exit();
  }
} elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/facturacion/facturacion.php";
} else {
  http_response_code(405);
  echo json_encode([
    'tipo'   => 'simple',
    'titulo' => 'Método no permitido',
    'texto'  => 'Solo se permiten peticiones GET y POST',
    'icono'  => 'error',
  ]);
}
