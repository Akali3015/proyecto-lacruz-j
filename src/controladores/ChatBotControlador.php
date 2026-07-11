<?php
use src\modelos\chatbotModelo;

// Recibir acción estricta por POST 
$accion = $_POST["accion"] ?? "";

switch ($accion) {
    case "enviarMensaje":
        $modelo = new chatbotModelo();
        $respuesta = $modelo->procesarMensajeChatbot($_POST);
        
        ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($respuesta);
        exit;

    case "guardarPresupuesto":
        $modelo = new chatbotModelo();
        $respuesta = $modelo->guardarPresupuesto($_POST);
        
        ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($respuesta);
        exit;
        
    default:
        ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(["status" => "error", "mensaje" => "Acción no válida"]);
        exit;
}
?>
