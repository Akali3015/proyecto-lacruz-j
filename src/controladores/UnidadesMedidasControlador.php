<?php

use src\modelos\unidadesMedidasModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {

    $accion = $_POST["accion"];
    $id = $_POST['id_unidad_medida'] ?? "";
    $nombre = $_POST['nombre_unidad_medida'] ?? "";
    $simbolo = $_POST['simbolo_unidad_medida'] ?? "";
    $equivalencia_ub = $_POST['equivalencia_ub'] ?? "";
    
    $objeto = new unidadesMedidasModelo();
    ob_clean();
    switch ($accion) {
        case "listar":
            $resultado = $objeto->seleccionarUnidadesMedidas();
            echo json_encode($resultado);
            exit();
        case "seleccionarUno":
            $resultado = $objeto->seleccionarUnidadesMedidas($id);
            echo json_encode($resultado);
            exit();
        case "registrar":
            $resultado = $objeto->registrarUnidadesMedidas($nombre,$simbolo,$equivalencia_ub);
            echo json_encode($resultado);
            exit();
        case "actualizar":
            $resultado = $objeto->actualizarUnidadesMedidas($id, $nombre,$simbolo, $equivalencia_ub);
            echo json_encode($resultado);
            exit();
        case "eliminar":
            $resultado = $objeto->eliminarUnidadesMedidas($id);
            echo json_encode($resultado);
            exit();
        default:
            echo json_encode(["error" => "Acción no reconocida"]);
            exit();
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
    require_once "src/config/inc/header.php";
    require_once "src/config/inc/sidebar.php";
    require_once "src/vistas/unidadesMedidas/unidadesMedidas.php";
}
