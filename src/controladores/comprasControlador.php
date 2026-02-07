<?php

// Activar reporte de errores temporalmente para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

use src\modelos\comprasModelo;
use src\modelos\proveedoresModelo;
use src\modelos\productosModelo;
use src\modelos\insumosModelo;
use src\modelos\materiasPrimasModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['cedula'])) {

    // Leer datos JSON si existen, sino usar $_POST
    $datos = file_get_contents('php://input');
    $datosJson = json_decode($datos, true);

    error_log("RAW INPUT: " . $datos);
    error_log("JSON DECODED: " . json_encode($datosJson));
    error_log("POST DATA: " . json_encode($_POST));

    // Determinar la fuente de datos
    if ($datosJson && isset($datosJson['accion'])) {
        $accion = $datosJson["accion"];
        $rifProveedor = $datosJson['rif_proveedor'] ?? "";
        $cedulaUsuario = $datosJson['cedula_usuario'] ?? "";
        $fechaCompra = $datosJson['fecha_compra'] ?? "";
        $detalles = $datosJson['detalles'] ?? [];
        $tipoItem = $datosJson['tipo'] ?? "";
    } else {
        $accion = $_POST['accion'] ?? "";
        $rifProveedor = $_POST['rif_proveedor'] ?? "";
        $cedulaUsuario = $_POST['cedula_usuario'] ?? "";
        $fechaCompra = $_POST['fecha_compra'] ?? "";
        $detalles = $_POST['detalles'] ?? [];
        // Si detalles es un string JSON, decodificarlo
        if (is_string($detalles)) {
            $detalles = json_decode($detalles, true) ?? [];
        }
        $tipoItem = $_POST['tipo'] ?? "";
    }

    error_log("CONTROLADOR - Accion: " . $accion);
    error_log("CONTROLADOR - RIF: " . $rifProveedor);
    error_log("CONTROLADOR - Cedula: " . $cedulaUsuario);
    error_log("CONTROLADOR - Fecha: " . $fechaCompra);
    error_log("CONTROLADOR - Detalles: " . json_encode($detalles));

    $objeto = new comprasModelo();
    ob_clean();

    switch ($accion) {
        case "listar":
            $resultado = $objeto->listarCompras();
            echo json_encode($resultado);
            exit();

        case "registrar":
            $resultado = $objeto->registrarCompra($rifProveedor, $cedulaUsuario, $fechaCompra, $detalles);
            echo json_encode($resultado);
            exit();

        case "obtenerProveedores":
            $proveedoresModelo = new proveedoresModelo();
            $resultado = $proveedoresModelo->seleccionarProveedor();
            echo json_encode($resultado);
            exit();

        case "obtenerItems":
            switch ($tipoItem) {
                case 'producto':
                    $productosModelo = new productosModelo();
                    $resultado = $productosModelo->seleccionarProductos();
                    break;

                case 'insumo':
                    $insumosModelo = new insumosModelo();
                    $resultado = $insumosModelo->seleccionarInsumos();
                    break;

                case 'materia_prima':
                    $materiasPrimasModelo = new materiasPrimasModelo();
                    $resultado = $materiasPrimasModelo->seleccionarMateriasPrimas();
                    break;

                default:
                    $resultado = [];
            }
            echo json_encode($resultado);
            exit();

        case "eliminar":
            $idCompra = $datosJson['id_compra'] ?? ($_POST['id_compra'] ?? "");
            $resultado = $objeto->eliminarCompra($idCompra);
            echo json_encode($resultado);
            exit();

        case "obtener":
            $idCompra = $datosJson['id_compra'] ?? ($_POST['id_compra'] ?? "");
            $resultado = $objeto->obtenerCompra($idCompra);
            echo json_encode($resultado);
            exit();

        case "actualizar":
            $idCompra = $datosJson['id_compra'] ?? ($_POST['id_compra'] ?? "");
            $resultado = $objeto->actualizarCompra($idCompra, $rifProveedor, $cedulaUsuario, $fechaCompra);
            echo json_encode($resultado);
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
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
    require_once "src/config/inc/header.php";
    require_once "src/config/inc/sidebar.php";
    require_once "src/vistas/compras/compras.php";
}
