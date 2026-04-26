<?php
ob_start();

use src\modelos\comprasModelo;
use src\config\inc\componentesModelo;

// Función de respuesta disponible para todo el controlador
function responderSolicitud($respuesta, $codigo = 200)
{
  while (ob_get_level()) { ob_end_clean(); }
  if ($codigo !== 200) http_response_code($codigo);
  header('Content-Type: application/json');
  echo json_encode($respuesta);
  exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['cedula'])) {

  // El frontController ya transformó el cuerpo JSON a $_POST via transformarCuerpoAPost()
  $accion    = $_POST['accion']    ?? '';
  $id_compra = $_POST['id_compra'] ?? '';

  if (empty($accion)) {
    responderSolicitud(["tipo" => "simple", "titulo" => "Error", "texto" => "Acción requerida", "icono" => "error"]);
  }

  $objetoCompras = new comprasModelo();
  ob_clean();

  switch ($accion) {
    case 'listar':
      $resultado = $objetoCompras->seleccionarCompra();
      responderSolicitud($resultado);
      break;
    case 'seleccionarUno':
      if (empty($id_compra)) {
        responderSolicitud([
          "tipo" => "simple", "titulo" => "Error",
          "texto" => "ID de compra requerido", "icono" => "error"
        ]);
      }
      responderSolicitud($objetoCompras->seleccionarUno($id_compra));
      break;

    case 'registrar':
      $fecha_compra       = $_POST['fecha_compra']  ?? '';
      $post_rif_proveedor = trim($_POST['rif_proveedor'] ?? '');
      $detalles           = json_decode($_POST['detalles'] ?? '[]', true);

      if (empty($detalles)) {
        responderSolicitud([
          "tipo" => "simple", "titulo" => "Error",
          "texto" => "Debe agregar al menos un artículo", "icono" => "warning"
        ]);
      }

      // Agrupar items por proveedor
      $compras_por_proveedor = [];
      foreach ($detalles as $detalle) {
        $prov_id = $detalle['proveedorId'] ?? $post_rif_proveedor;
        if (empty($prov_id)) continue;
        $compras_por_proveedor[$prov_id][] = $detalle;
      }

      if (empty($compras_por_proveedor)) {
        responderSolicitud([
          "tipo" => "simple", "titulo" => "Error",
          "texto" => "No se detectaron proveedores válidos", "icono" => "error"
        ]);
      }

      $exitos = 0; $errores = 0; $mensajes_error = []; $ultimo_resultado = [];

      foreach ($compras_por_proveedor as $rif_prov => $items) {
        $resultado = $objetoCompras->registrarCompra($rif_prov, $fecha_compra, $items);
        if (isset($resultado['tipo']) && in_array($resultado['tipo'], ['exito', 'limpiar'])) {
          $exitos++;
          $ultimo_resultado = $resultado;
        } else {
          $errores++;
          if (isset($resultado['texto'])) $mensajes_error[] = $resultado['texto'];
        }
      }

      if ($errores == 0) {
        responderSolicitud($exitos == 1 ? $ultimo_resultado : [
          "tipo" => "limpiar", "titulo" => "Éxito",
          "texto" => "$exitos compras registradas correctamente", "icono" => "success"
        ]);
      } else {
        $msg = "$exitos registradas, $errores fallaron.";
        if (!empty($mensajes_error)) $msg .= " Errores: " . implode(", ", $mensajes_error);
        responderSolicitud([
          "tipo" => "simple", "titulo" => "Proceso Finalizado",
          "texto" => $msg, "icono" => $exitos > 0 ? "warning" : "error"
        ]);
      }
      break;

    case 'actualizar':
      $rif_proveedor = trim($_POST['rif_proveedor'] ?? '');
      $fecha_compra  = $_POST['fecha_compra'] ?? '';
      $detalles      = json_decode($_POST['detalles'] ?? '[]', true);

      if (empty($id_compra)) {
        responderSolicitud(["tipo" => "simple", "titulo" => "Error", "texto" => "ID requerido", "icono" => "error"]);
      }

      if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 1) {
        responderSolicitud([
          "tipo" => "simple", "titulo" => "No Autorizado",
          "texto" => "Solo el Super Usuario puede editar compras.", "icono" => "error"
        ]);
      }

      $resultado = $objetoCompras->actualizarCompra($id_compra, $rif_proveedor, $fecha_compra, $detalles);
      responderSolicitud($resultado);
      break;

    default:
      responderSolicitud(["tipo" => "simple", "titulo" => "Error", "texto" => "Acción no válida", "icono" => "error"]);
  }

} elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/compras/compras.php";
} else {
  responderSolicitud([
    "tipo" => "simple", "titulo" => "Error",
    "texto" => "Método no permitido", "icono" => "error"
  ], 405);
}
