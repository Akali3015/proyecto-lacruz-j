<?php
require_once "src/modelos/chatbotModelo.php";
<<<<<<< HEAD

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
=======
use src\modelos\chatbotModelo;

// Recibir acción (POST desde JS o GET desde Python)
$accion = $_POST["accion"] ?? $_GET["accion"] ?? "";
// 1. ENDPOINT FRONTAL: Recibe el mensaje de la vista, lo guarda y llama a Python
if ($accion == "enviarMensaje") {
    
    $mensaje = $_POST["mensaje"] ?? "";
    // Obtenemos la cédula del usuario en sesión. Si no hay, asignamos un identificador por defecto.
    $cedula = $_SESSION['cedula'] ?? "00000000";

    if (empty($mensaje)) {
        ob_end_clean();
        echo json_encode(["error" => "Mensaje vacío"]);
        exit;
    }

    // Instanciar el modelo para interactuar con la Base de Datos
    $modelo = new chatbotModelo();
    
    // Guardar el mensaje del usuario en la tabla prompts_usuarios
    $modelo->guardarPrompt($cedula, $mensaje);
    
    // Obtener los últimos mensajes de este usuario para enviarlos como contexto
    $historial = $modelo->obtenerHistorial($cedula);

    // Endpoint del microservicio FastAPI
    $urlMicroservicio = "http://localhost:8000/api/chat";
    
    // Preparar payload enviando el mensaje actual y el historial completo
    $datosPost = json_encode([
        "mensaje" => $mensaje,
        "sesion_id" => session_id(),
        "historial" => $historial
    ]);

    $tokenInterno = "JLacruz2026Secure";

    // Init cURL
    $ch = curl_init($urlMicroservicio);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Content-Length: " . strlen($datosPost),
        "X-Internal-Token: " . $tokenInterno
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datosPost);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    // Ejecutar petición
    $respuestaMicroservicio = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $errorCurl = curl_error($ch);
    curl_close($ch); 

    // LOG DE DEPURACION:
    error_log("=== CHATBOT DEBUG ===");
    error_log("Mensaje: " . $mensaje);
    error_log("JSON a enviar: " . $datosPost);
    error_log("HTTP Code desde Python: " . $httpCode);
    error_log("Respuesta cruda Python: " . $respuestaMicroservicio);

    // Validar respuesta del microservicio
    if ($respuestaMicroservicio === false || $httpCode >= 400) {
        $errorJson = json_encode([
            "status" => "error",
            "mensaje" => "Disculpa, en este momento el asistente no está disponible. Por favor, intenta de nuevo más tarde.",
            "detalle" => $errorCurl,
            "http_code" => $httpCode
        ]);
        error_log("Enviando a JS: " . $errorJson);
        ob_end_clean();
        header('Content-Type: application/json');
        echo $errorJson;
    } else {
        // Parsear para guardar la respuesta en la BD
        $datosPython = json_decode($respuestaMicroservicio, true);
        if(isset($datosPython['respuesta']) && !empty($datosPython['respuesta'])) {
            $modelo->guardarRespuestaBot($cedula, $datosPython['respuesta']);
        }
        
        error_log("Enviando a JS: " . $respuestaMicroservicio);
        ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        echo $respuestaMicroservicio;
    }

    
    exit;
>>>>>>> 3846421cf5efb48613d85c33c8d9e18934dd566f
}

// 2. API INTERNA: Endpoint al que Python llamará cuando necesite catálogo
if ($accion == "obtenerCatalogo") {
<<<<<<< HEAD
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
=======
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
?>
>>>>>>> 3846421cf5efb48613d85c33c8d9e18934dd566f
