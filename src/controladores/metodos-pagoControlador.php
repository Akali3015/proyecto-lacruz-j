<?php

use src\modelos\metodosPagoModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {

    $accion = $_POST["accion"];
    $id = $_POST['id_metodo_pago'] ?? "";
    $nombre = $_POST['nombre_metodo_pago'] ?? "";
    $necesitaMoneda = $_POST['necesita_moneda'] ?? "";

    $objeto = new metodosPagoModelo();
    ob_clean();
    switch ($accion) {
        case "listar":
            $resultado = $objeto->seleccionarMetodoPago();
            echo json_encode($resultado);
            exit();
        case "seleccionarUno":
            $resultado = $objeto->seleccionarMetodoPago($id);
            echo json_encode($resultado);
            exit();
        case "registrar":
            $resultado = $objeto->registrarMetodoPago($nombre, $necesitaMoneda);
            echo json_encode($resultado);
            exit();
        case "actualizar":
            $resultado = $objeto->actualizarMetodoPago($id, $nombre, $necesitaMoneda);
            echo json_encode($resultado);
            exit();
        case "eliminar":
            $resultado = $objeto->eliminarMetodoPago($id);
            echo json_encode($resultado);
            exit();
        default:
            echo json_encode(["error" => "Acción no reconocida"]);
            exit();
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
    require_once "src/config/inc/header.php";
    require_once "src/config/inc/sidebar.php";
    require_once "src/vistas/metodos-pago/metodos-pago.php";
}
