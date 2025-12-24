<?php

use src\modelos\clientesModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {

    $accion = $_POST["accion"];
    $rif = $_POST['rif_cedula_cliente'] ?? "";
    $razon = $_POST['razon_social_cliente'] ?? "";
    $telefono = $_POST['telefono_cliente'] ?? "";
    $correo = $_POST['correo_cliente'] ?? "";
    $direccion = $_POST['direccion_cliente'] ?? "";

    $objeto = new clientesModelo();
    ob_clean();
    switch ($accion) {
        case 'listar':
            $resultado = $objeto->seleccionarCliente();
            echo json_encode($resultado);
            exit();
        case 'seleccionarUno':
            $resultado = $objeto->seleccionarCliente($rif);
            echo json_encode($resultado);
            exit();
        case 'registrar':
            $resultado = $objeto->registrarCliente($rif, $razon, $telefono, $correo, $direccion);
            echo json_encode($resultado);
            exit();
        case 'actualizar':
            $resultado = $objeto->actualizarCliente($rif, $razon, $telefono, $correo, $direccion);
            echo json_encode($resultado);
            exit();
        case 'eliminar':
            $resultado = $objeto->eliminarCliente($rif);
            echo json_encode($resultado);
            exit();
        default:
            echo json_encode(["error" => "Acción no reconocida"]);
            exit();
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
    require_once "src/config/inc/header.php";
    require_once "src/config/inc/sidebar.php";
    require_once "src/vistas/clientes/clientes.php";
}
