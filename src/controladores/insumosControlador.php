<?php

use src\modelos\insumosModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {

    $accion = $_POST["accion"];
    $id = $_POST['id_insumo'] ?? "";
    $nombre = $_POST['nombre_insumo'] ?? "";
    $precio = $_POST['precio_insumo'] ?? "";
    $stock = $_POST['stock_insumo'] ?? "";
    
    $objeto = new insumosModelo();
    ob_clean();
    switch ($accion) {
        case "listar":
            $resultado = $objeto->seleccionarInsumos();
            echo json_encode($resultado);
            exit();
        case "seleccionarUno":
            $resultado = $objeto->seleccionarInsumos($id);
            echo json_encode($resultado);
            exit();
        case "registrar":
            $resultado = $objeto->registrarInsumos($nombre, $precio, $stock);
            echo json_encode($resultado);
            exit();
        case "actualizar":
            $resultado = $objeto->actualizarInsumos($id, $nombre, $precio, $stock);
            echo json_encode($resultado);
            exit();
        case "eliminar":
            $resultado = $objeto->eliminarInsumos($id);
            echo json_encode($resultado);
            exit();
        default:
            echo json_encode(["error" => "Acción no reconocida"]);
            exit();
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
    require_once "src/config/inc/header.php";
    require_once "src/config/inc/sidebar.php";
    require_once "src/vistas/insumos/insumos.php";
}