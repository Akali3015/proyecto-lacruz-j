<?php
require_once "src/modelos/chatbotModelo.php";

use src\modelos\chatbotModelo;

// Recibir acción (POST desde JS o GET desde Python)
$accion = $_POST["accion"] ?? "";
// 1. ENDPOINT FRONTAL: Recibe el mensaje de la vista, lo guarda y llama a Python
if ($accion == "enviarMensaje") {
  $mensaje = $_POST["mensaje"] ?? "";
  $cedula = $_SESSION['cedula'] ?? "00000000";

  // 1. Instanciar el modelo
  $modelo = new chatbotModelo();

  try {
    // 2. Delegar toda la lógica de negocio, validaciones y cURL al modelo
    $respuesta = $modelo->procesarMensajeChatbot($cedula, $mensaje);
  } catch (\Throwable $e) {
    // SEGURIDAD: Evita que un colapso de PHP imprima rutas de archivos y versiones de Apache/PHP
    error_log("Error crítico en ChatBotControlador: " . $e->getMessage());
    $respuesta = [
      "status" => "error",
      "codigo_error" => "ERR_INTERNO_SERVIDOR",
      "mensaje" => "Disculpa, ha ocurrido un error interno en el servidor."
    ];
  }

  // 3. Responder al cliente
  ob_end_clean();
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($respuesta);
  exit;
}

// 2. API INTERNA: Endpoint al que Python llamará cuando necesite catálogo
if ($accion == "obtenerCatalogo") {
  $headers = getallheaders();
  $token = $headers['X-Internal-Token'] ?? '';
  if ($token !== "JLacruz2026Secure") {
    ob_end_clean();
    http_response_code(403);
    echo json_encode(["error" => "No autorizado"]);
    exit;
  }

  $modelo = new chatbotModelo();
  $catalogo = $modelo->obtenerCatalogo();

  // Devolvemos los datos en formato JSON para que Python los consuma
  $jsonRespuesta = json_encode([
    "status" => "success",
    "data" => $catalogo
  ]);
  ob_end_clean();
  echo $jsonRespuesta;
  exit;
}
