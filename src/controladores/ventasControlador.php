<?php

use src\modelos\ventasModelo;
use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {

  $accion = $_POST["accion"];
  $objeto = new ventasModelo();
  ob_clean();

  switch ($accion) {
    case 'listar':
      $resultado = $objeto->seleccionarVenta();

      // Si no hay resultados, devolver array vacío
      if (empty($resultado)) {
        echo json_encode([]);
      } else {
        echo json_encode($resultado);
      }
      exit();

    case 'seleccionarUno':
      $id_venta = $_POST['id_venta'] ?? "";
      if (empty($id_venta)) {
        echo json_encode([
          "tipo" => "simple",
          "titulo" => "Error",
          "texto" => "ID de venta requerido",
          "icono" => "error"
        ]);
        exit();
      }
      echo json_encode($objeto->seleccionarVenta($id_venta));
      exit();

    case 'registrar':

      $rif_cliente = trim($_POST['rif_cedula_cliente'] ?? '');
      $total = floatval($_POST['total_venta'] ?? 0);
      $productos = json_decode($_POST['productos'] ?? '[]', true);
      $servicios = json_decode($_POST['servicios'] ?? '[]', true);

      if (empty($rif_cliente)) {
        echo json_encode([
          "tipo" => "simple",
          "titulo" => "Error",
          "texto" => "Seleccione un cliente",
          "icono" => "warning"
        ]);
        exit();
      }

      if ($total <= 0) {
        echo json_encode([
          "tipo" => "simple",
          "titulo" => "Error",
          "texto" => "Total inválido",
          "icono" => "warning"
        ]);
        exit();
      }

      $resultado = $objeto->registrarVenta($rif_cliente, $total, $productos, $servicios);
      echo json_encode($resultado);
      exit();

    case 'eliminar':
      $id_venta = $_POST['id_venta'] ?? "";
      if (empty($id_venta)) {
        echo json_encode([
          "tipo" => "simple",
          "titulo" => "Error",
          "texto" => "ID requerido",
          "icono" => "error"
        ]);
        exit();
      }
      echo json_encode($objeto->eliminarVenta($id_venta));
      exit();


    default:
      echo json_encode([
        "tipo" => "simple",
        "titulo" => "Error",
        "texto" => "Acción no válida",
        "icono" => "error"
      ]);
      exit();
  }
} elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/ventas/ventas.php";
} else {
  http_response_code(405);
  echo json_encode([
    "tipo" => "simple",
    "titulo" => "Error",
    "texto" => "Método no permitido",
    "icono" => "error"
  ]);
}