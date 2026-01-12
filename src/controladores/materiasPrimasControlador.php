<?php

use src\modelos\materiasPrimasModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {

    $accion = $_POST["accion"];
    $id = $_POST['id_materia_prima'] ?? "";
    $idUnidadMedida = $_POST['id_unidad_medida'] ?? "";
    $nombre = $_POST['nombre_materia_prima'] ?? "";
    $stock = $_POST['stock_materia_prima'] ?? "";
    $costo = $_POST['costo_materia_prima'] ?? "";
    $presentaciones = $_POST['presentaciones'] ?? []; 
    
    $objeto = new materiasPrimasModelo();
    ob_clean();
    
    switch ($accion) {
        case "listar":
            $resultado = $objeto->seleccionarMateriasPrimas();
            echo json_encode($resultado);
            exit();        
        case "seleccionarUno":
            $resultado = $objeto->seleccionarMateriasPrimas($id);
            echo json_encode($resultado);
            exit();
        case "listarPresentaciones":
            $resultado = $objeto->obtenerPresentacionesMateriasPrimas($id);
            echo json_encode($resultado);
            exit();            
        case "registrar":
            $presentaciones = $_POST['presentaciones'] ?? [];
            $resultado = $objeto->registrarMateriasPrimas($idUnidadMedida, $nombre, $stock, $costo, $presentaciones);
            echo json_encode($resultado);
            exit();            
        case "actualizar":
            $presentaciones = $_POST['presentaciones'] ?? [];
            $resultado = $objeto->actualizarMateriasPrimas($id, $idUnidadMedida, $nombre, $stock, $costo, $presentaciones);
            echo json_encode($resultado);
            exit();           
        case "eliminar":
            $resultado = $objeto->eliminarMateriasPrimas($id);
            echo json_encode($resultado);
            exit();
        default:
            echo json_encode(["error" => "Acción no reconocida"]);
            exit();
    }
    
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
    require_once "src/config/inc/header.php";
    require_once "src/config/inc/sidebar.php";
    require_once "src/vistas/materiasPrimas/materiasPrimas.php";
}