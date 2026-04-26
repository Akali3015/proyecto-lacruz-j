<?php

use src\modelos\bancosModelo;
use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {
    $accion = $_POST["accion"];
    $id = $_POST['id_banco'] ?? "";
    $nombre = $_POST['nombre_banco'] ?? "";

    $objeto = new bancosModelo();
    ob_clean();
    $resultado = [];
    switch ($accion) {
        case "listar":
            $resultado = $objeto->seleccionarBancos();
            break;
        case "seleccionarUno":
            $resultado = $objeto->seleccionarBancos($id);
            break;
        case "registrar":
            $resultado = $objeto->registrarBancos($nombre);
            break;
        case "actualizar":
            $resultado = $objeto->actualizarBancos($id, $nombre);
            break;
        case "eliminar":
            $resultado = $objeto->eliminarBancos($id);
            break;
        default:
            $resultado = ["error" => "Acción no reconocida"];
    }
    $objeto->DECORE($resultado);
    exit();
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
    $objComponentes = new componentesModelo();
    require_once "src/config/inc/header.php";
    echo $objComponentes->sidebar();
    require_once "src/vistas/bancos/bancos.php";
} else {
    http_response_code(403);
}