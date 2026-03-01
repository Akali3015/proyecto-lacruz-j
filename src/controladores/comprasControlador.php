<?php
ob_start();

use src\modelos\comprasModelo;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {

  $accion = $_POST["accion"];
  $objetoCompras = new comprasModelo();
  ob_clean();
  $id_compra = $_POST['id_compra'] ?? "";
  switch ($accion) {
    case 'listar':
      $resultado = $objetoCompras->seleccionarCompra();
      $objetoCompras->DECORE($resultado);
      break;
    case 'seleccionarUno':

      if (empty($id_compra)) {
        responderSolicitud([
          "tipo" => "simple",
          "titulo" => "Error",
          "texto" => "ID de compra requerido",
          "icono" => "error"
        ]);
      }
      responderSolicitud($objetoCompras->seleccionarUno($id_compra));
      break;

    case 'registrar':
      $fecha_compra = $_POST['fecha_compra'] ?? '';
      $detalles = json_decode($_POST['detalles'] ?? '[]', true);

      if (empty($detalles)) {
        responderSolicitud([
          "tipo" => "simple",
          "titulo" => "Error",
          "texto" => "Debe agregar al menos un artículo",
          "icono" => "warning"
        ]);
      }

      // Agrupar items por proveedor
      $compras_por_proveedor = [];
      $post_rif_proveedor = trim($_POST['rif_proveedor'] ?? '');

      foreach ($detalles as $detalle) {
        // Usar ID del item o el general del POST como fallback
        $prov_id = $detalle['proveedorId'] ?? $post_rif_proveedor;

        if (empty($prov_id)) {
          continue;
        }

        if (!isset($compras_por_proveedor[$prov_id])) {
          $compras_por_proveedor[$prov_id] = [];
        }
        $compras_por_proveedor[$prov_id][] = $detalle;
      }

      if (empty($compras_por_proveedor)) {
        responderSolicitud([
          "tipo" => "simple",
          "titulo" => "Error",
          "texto" => "No se detectaron proveedores válidos en los artículos",
          "icono" => "error"
        ]);
      }

      $exitos = 0;
      $errores = 0;
      $mensajes_error = [];
      $ultimo_resultado = [];

      // Registrar una compra por cada proveedor
      foreach ($compras_por_proveedor as $rif_prov => $items) {
        $resultado = $objetoCompras->registrarCompra($rif_prov, $fecha_compra, $items);

        // Verificar si fue exitoso (generalmente devuelve un array con tipo 'exito' o 'limpiar')
        if (isset($resultado['tipo']) && ($resultado['tipo'] == 'exito' || $resultado['tipo'] == 'limpiar')) {
          $exitos++;
          $ultimo_resultado = $resultado;
        } else {
          $errores++;
          if (isset($resultado['texto'])) {
            $mensajes_error[] = $resultado['texto'];
          }
        }
      }

      if ($errores == 0) {
        // Si todo salió bien, devolvemos éxito
        if ($exitos == 1) {
          responderSolicitud($ultimo_resultado);
        } else {
          responderSolicitud([
            "tipo" => "exito",
            "titulo" => "Éxito",
            "texto" => "$exitos compras registradas correctamente",
            "icono" => "success"
          ]);
        }
      } else {
        // Si hubo errores parciales o totales
        $msg = "$exitos registradas, $errores fallaron.";
        if (!empty($mensajes_error)) {
          $msg .= " Errores: " . implode(", ", $mensajes_error);
        }

        responderSolicitud([
          "tipo" => "simple",
          "titulo" => "Proceso Finalizado",
          "texto" => $msg,
          "icono" => $exitos > 0 ? "warning" : "error"
        ]);
      }
      break;

    case 'actualizar':
      $id_compra = $_POST['id_compra'] ?? "";
      $rif_proveedor = trim($_POST['rif_proveedor'] ?? '');
      $fecha_compra = $_POST['fecha_compra'] ?? '';

      $detalles = json_decode($_POST['detalles'] ?? '[]', true);

      if (empty($id_compra)) {
        responderSolicitud([
          "tipo" => "simple",
          "titulo" => "Error",
          "texto" => "ID requerido",
          "icono" => "error"
        ]);
      }

      // Validar Rol (Solo Super Usuario: 1)
      if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 1) {
        responderSolicitud([
          "tipo" => "simple",
          "titulo" => "No Autorizado",
          "texto" => "Solo el Super Usuario puede editar compras.",
          "icono" => "error"
        ]);
      }

      $resultado = $objetoCompras->actualizarCompra($id_compra, $rif_proveedor, $fecha_compra, $detalles);
      responderSolicitud($resultado);
      break;

    default:
      responderSolicitud([
        "tipo" => "simple",
        "titulo" => "Error",
        "texto" => "Acción no válida",
        "icono" => "error"
      ]);
  }
} elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
  require_once "src/config/inc/header.php";
  require_once "src/config/inc/sidebar.php";
  require_once "src/vistas/compras/compras.php";
} else {
  responderSolicitud([
    "tipo" => "simple",
    "titulo" => "Error",
    "texto" => "Método no permitido",
    "icono" => "error"
  ], 405);
}

function responderSolicitud($respuesta, $codigo = 200)
{
  if ($codigo !== 200) {
    http_response_code($codigo);
  }
  echo json_encode($respuesta);
  exit();
}
