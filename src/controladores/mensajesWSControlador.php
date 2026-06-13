<?php

use src\modelos\mensajesWSModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $datos = $_POST['metadatos'] ?? file_get_contents('php://input');
    $datos = json_decode($datos, true);
    $accion = $datos["accion"] ?? $_POST['accion'] ?? "";
    $idNotificacion = $datos['id_notificacion'] ?? '';
    $notificacion =  $datos['notificacion'] ?? '';
    $AccionMsj = $datos['AccionMsj'] ?? "";

    $modeloWS = new mensajesWSModelo();
    ob_clean();
    switch ($accion) {
        case "listarNotificaciones":
            $resultado = $modeloWS->seleccionarNotificaciones();
            echo json_encode($resultado);
            exit();
        case "seleccionarUnaNot":
            $resultado = $modeloWS->seleccionarNotificaciones($idNotificacion);
            echo json_encode($resultado);
            exit();
        case "registrarNoti":
            $resultado = $modeloWS->registrarNotificaciones($notificacion);
            echo json_encode($resultado);
            exit();
        case "marcarTodasNotComoLeidas":
            $resultado = $modeloWS->actualizarNotificaciones('marcarTodasComoLeidas');
            echo json_encode($resultado);
            exit();
        case "eliminarTodasNot":
            $resultado = $modeloWS->eliminarNotificaciones();
            echo json_encode($resultado);
            exit();
        case "listarAccionesResagadas":
            $resultado = $modeloWS->seleccionarAccionesResagadas();
            echo json_encode($resultado);
            exit();
        case "eliminarAccionResagada":
            $resultado = $modeloWS->eliminarAccionesResagadas($AccionMsj);
            echo json_encode($resultado);
            exit();
        default:
            echo json_encode(["error" => "Acción no reconocida"]);
            exit();
    }
}
