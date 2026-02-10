<?php

use src\modelos\proveedoresModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion']) && isset($_SESSION['cedula'])) {

    $accion = $_POST['accion'];
    $rif = $_POST['rif_proveedor'] ?? "";
    $razon = $_POST['razon_social_proveedor'] ?? "";
    $telefono = $_POST['telefono_proveedor'] ?? "";
    $correo = $_POST['correo_proveedor'] ?? "";
    $direccion = $_POST['direccion_proveedor'] ?? "";
// 
    $objeto = new proveedoresModelo();
    ob_clean(); 
    switch ($accion) {
        case 'listar':
            $resultado = $objeto->seleccionarProveedor();
            echo json_encode($resultado);
            exit();
        case 'seleccionarUno':
            $resultado = $objeto->seleccionarProveedor($rif);
            echo json_encode($resultado);
            exit();
        case 'registrar':
            $resultado = $objeto->registrarProveedor($rif, $razon, $telefono, $correo, $direccion);
            echo json_encode($resultado);
            exit();
        case 'actualizar':
            $resultado = $objeto->actualizarProveedor($rif, $razon, $telefono, $correo, $direccion);
            echo json_encode($resultado);
            exit();
        case 'eliminar':
            $resultado = $objeto->eliminarProveedor($rif);
            echo json_encode($resultado);
            exit();
        default:
            echo json_encode(["error" => "Acción no reconocida"]);
            exit();
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
    require_once "src/config/inc/header.php";
    require_once "src/config/inc/sidebar.php";
    require_once "src/vistas/proveedores/proveedores.php";
}
