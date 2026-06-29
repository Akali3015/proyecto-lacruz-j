<?php
namespace src\modelos;
use PDO;
use src\config\connect\conexion;
<<<<<<< HEAD
use src\modelos\bitacoraModelo;

class chatbotModelo extends conexion {
  private string $cedula = '';
  private string $mensaje = '';
  private string $respuesta = '';

  public function procesarMensajeChatbot(string $cedula, string $mensaje) {
    $this->cedula = $cedula;
    $this->mensaje = $mensaje;

    $respuestaVal = $this->limpiar_Verificar([
      [
        "campo_nombre" => "cedula_usuario",
        "campo_valor" => &$this->cedula,
        "formulario_nombre" => "cédula",
        "requerido" => true,
        "minimo" => 7,
        "maximo" => 20
      ],
      [
        "campo_nombre" => "prompt",
        "campo_valor" => &$this->mensaje,
        "formulario_nombre" => "mensaje",
        "requerido" => true,
        "minimo" => 1,
        "maximo" => 10000
      ]
    ]);

    if ($respuestaVal !== false) {
      // Transformar la validacion fallida a nuestro estandar de JSON
      return [
        "status" => "error",
        "mensaje" => $respuestaVal["mensaje"] ?? "Mensaje no válido."
      ];
    }

    return $this->procesarMensajeChatbotP();
  }
  public function guardarPrompt(string $cedula, string $mensaje) {
    $this->cedula = $cedula;
    $this->mensaje = $mensaje;

    $respuestaVal = $this->limpiar_Verificar([
      [
        "campo_nombre" => "cedula_usuario",
        "campo_valor" => &$this->cedula,
        "formulario_nombre" => "cédula",
        "requerido" => true,
        "minimo" => 7,
        "maximo" => 20
      ],
      [
        "campo_nombre" => "prompt",
        "campo_valor" => &$this->mensaje,
        "formulario_nombre" => "mensaje",
        "requerido" => true,
        "minimo" => 1,
        "maximo" => 10000
      ]
    ]);
    if ($respuestaVal !== false) return $respuestaVal;

    return $this->guardarPromptP();
  }
  public function obtenerHistorial(string $cedula) {
    $this->cedula = $cedula;
    $respuestaVal = $this->limpiar_Verificar([
      [
        "campo_nombre" => "cedula_usuario",
        "campo_valor" => &$this->cedula,
        "formulario_nombre" => "cédula",
        "requerido" => true,
        "minimo" => 7,
        "maximo" => 20
      ]
    ]);
    if ($respuestaVal !== false) return []; // Retornar vacío si falla la validación

    return $this->obtenerHistorialP();
  }
  public function guardarRespuestaBot(string $cedula, string $respuesta) {
    $this->cedula = $cedula;
    $this->respuesta = $respuesta;

    $respuestaVal = $this->limpiar_Verificar([
      [
        "campo_nombre" => "cedula_usuario",
        "campo_valor" => &$this->cedula,
        "formulario_nombre" => "cédula",
        "requerido" => true,
        "minimo" => 7,
        "maximo" => 20
      ],
      [
        "campo_nombre" => "respuesta_bot",
        "campo_valor" => &$this->respuesta,
        "formulario_nombre" => "respuesta del bot",
        "requerido" => true,
        "minimo" => 1,
        "maximo" => 20000
      ]
    ]);
    if ($respuestaVal !== false) return $respuestaVal;

    return $this->guardarRespuestaBotP();
  }
  public function obtenerCatalogo() {
    return $this->obtenerCatalogoP();
  }

  private function guardarRespuestaBotP() {
    // Obtenemos el último prompt de este usuario
    $resultado = $this->seleccionarDatos2([
      'campos' => 'MAX(id_prompt_usuario) as id',
      'tabla' => 'proyecto_lacruz_seguridad.prompts_usuarios',
      'BD' => 'seguridad',
      'WHERE' => [
        'cedula_usuario' => $this->cedula
      ]
    ]);
    $fila = $resultado->fetch(PDO::FETCH_ASSOC);
    $id = $fila['id'] ?? null;

    if ($id) {
      $resultadoUpdate = $this->actualizarDatos2([
        'tabla' => 'proyecto_lacruz_seguridad.prompts_usuarios',
        'BD' => 'seguridad',
        'datos' => [
          'respuesta_bot' => $this->respuesta
        ],
        'WHERE' => [
          'id_prompt_usuario' => $id
        ]
      ]);
      $this->commit();

      require_once 'src/modelos/bitacoraModelo.php';
      $objBitacora = new \src\modelos\bitacoraModelo();
      $objBitacora->registrarBitacora('chatbot', 'recibió una respuesta del asistente', 'Exitoso', true);

      return $resultadoUpdate;
    }
    return false;
  }
  private function guardarPromptP() {
    $resultado = $this->guardarDatos2([
      'tabla' => 'prompts_usuarios',
      'BD' => 'seguridad',
      'datos' => [
        'cedula_usuario' => $this->cedula,
        'prompt' => $this->mensaje,
        'status' => 1
      ]
    ]);
    $objBitacora = new bitacoraModelo();
    $objBitacora->registrarBitacora('chatbot', 'envió un mensaje al asistente', 'Exitoso', true);
    return $resultado;
  }
  private function procesarMensajeChatbotP() {
    // 1. Guardar el prompt en BD
    $exitoGuardar = $this->guardarPromptP();
    if (!$exitoGuardar) {
      return [
        "status" => "error",
        "mensaje" => "Disculpa, ocurrió un error interno al registrar tu mensaje."
      ];
    }

    // 2. Obtener el historial previo
    $historial = $this->obtenerHistorialP();

    // 3. Empaquetar y llamar al Microservicio Python (cURL)
    $urlMicroservicio = "http://localhost:8000/api/chat";
    $datosPost = json_encode([
      "mensaje" => $this->mensaje,
      "sesion_id" => session_id(),
      "historial" => $historial
    ]);

    $ch = curl_init($urlMicroservicio);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      "Content-Type: application/json",
      "Content-Length: " . strlen($datosPost),
      "X-Internal-Token: JLacruz2026Secure"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datosPost);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $respuestaMicroservicio = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // 4. Manejo Estricto de Errores de Comunicación
    if ($respuestaMicroservicio === false || $httpCode >= 400) {
      return [
        "status" => "error",
        "mensaje" => "Disculpa, en este momento el asistente no está disponible. Por favor, intenta de nuevo más tarde."
      ];
    }

    $datosPython = json_decode($respuestaMicroservicio, true);
    if (!$datosPython) {
      return [
        "status" => "error",
        "mensaje" => "Error de formato en la respuesta del asistente.",
      ];
    }

    // 5. Guardar la respuesta exitosa en BD
    if (isset($datosPython['respuesta']) && !empty($datosPython['respuesta'])) {
      $this->respuesta = $datosPython['respuesta'];
      $this->guardarRespuestaBotP();
    }


    return $datosPython;
  }
  private function obtenerHistorialP() {
    // Seleccionamos los datos filtrando por la cédula
    $instrucciones = [
      'campos' => 'prompt, respuesta_bot, fecha_prompt',
      'tabla' => 'proyecto_lacruz_seguridad.prompts_usuarios',
      'BD' => 'seguridad',
      'WHERE' => [
        'cedula_usuario' => $this->cedula,
        'status' => 1
      ]
    ];

    $resultado = $this->seleccionarDatos2($instrucciones);
    $historial = [];

    if ($resultado && $resultado->rowCount() > 0) {
      // Obtenemos todos los registros
      $filas = $resultado->fetchAll(PDO::FETCH_ASSOC);

      // Ampliamos a los últimos 30 para mejor contexto a largo plazo
      $ultimos = array_slice($filas, -30);

      foreach ($ultimos as $fila) {
        $historial[] = [
          "texto" => $fila['prompt'],
          "respuesta" => "",
          "fecha" => $fila['fecha_prompt']
=======

class chatbotModelo extends conexion {

    private string $cedula = '';
    private string $mensaje = '';
    private string $respuesta = '';

    // Guarda el mensaje enviado por el usuario en la BD de seguridad
    public function guardarPrompt($cedula, $mensaje) {
        $this->cedula = $cedula;
        $this->mensaje = $mensaje;

        $respuestaVal = $this->limpiar_Verificar([
            [
                "campo_nombre" => "cedula_usuario",
                "campo_valor" => &$this->cedula,
                "formulario_nombre" => "cédula",
                "requerido" => true,
                "minimo" => 7,
                "maximo" => 20
            ],
            [
                "campo_nombre" => "prompt",
                "campo_valor" => &$this->mensaje,
                "formulario_nombre" => "mensaje",
                "requerido" => true,
                "minimo" => 1,
                "maximo" => 10000
            ]
        ]);
        if ($respuestaVal !== false) return $respuestaVal;

        return $this->guardarPromptP();
    }

    private function guardarPromptP() {
        $instrucciones = [
            'tabla' => 'proyecto_lacruz_seguridad.prompts_usuarios',
            'BD' => 'seguridad',
            'datos' => [
                'cedula_usuario' => $this->cedula,
                'prompt' => $this->mensaje,
                'status' => 1
            ]
>>>>>>> 3846421cf5efb48613d85c33c8d9e18934dd566f
        ];
        $resultado = $this->guardarDatos2($instrucciones);
        $this->commit();

        require_once 'src/modelos/bitacoraModelo.php';
        $objBitacora = new \src\modelos\bitacoraModelo();
        $objBitacora->registrarBitacora('chatbot', 'envió un mensaje al asistente', 'Exitoso', true);

        return $resultado;
    }
<<<<<<< HEAD
    return $historial;
  }
  private function obtenerCatalogoP() {
    $catalogo = [
      "productos" => [],
      "servicios" => []
    ];

    require_once 'src/modelos/productosModelo.php';
    $objProductos = new \src\modelos\productosModelo();
    $catalogo['productos'] = $objProductos->obtenerParaChatbot();

    require_once 'src/modelos/serviciosModelo.php';
    $objServicios = new \src\modelos\serviciosModelo();
    $catalogo['servicios'] = $objServicios->obtenerParaChatbot();
=======

    // Guarda la respuesta generada por Gemini
    public function guardarRespuestaBot($cedula, $respuesta) {
        $this->cedula = $cedula;
        $this->respuesta = $respuesta;

        $respuestaVal = $this->limpiar_Verificar([
            [
                "campo_nombre" => "cedula_usuario",
                "campo_valor" => &$this->cedula,
                "formulario_nombre" => "cédula",
                "requerido" => true,
                "minimo" => 7,
                "maximo" => 20
            ],
            [
                "campo_nombre" => "respuesta_bot",
                "campo_valor" => &$this->respuesta,
                "formulario_nombre" => "respuesta del bot",
                "requerido" => true,
                "minimo" => 1,
                "maximo" => 20000
            ]
        ]);
        if ($respuestaVal !== false) return $respuestaVal;

        return $this->guardarRespuestaBotP();
    }
>>>>>>> 3846421cf5efb48613d85c33c8d9e18934dd566f

    private function guardarRespuestaBotP() {
        // Obtenemos el último prompt de este usuario
        $resultado = $this->seleccionarDatos2([
            'campos' => 'MAX(id_prompt_usuario) as id',
            'tabla' => 'proyecto_lacruz_seguridad.prompts_usuarios',
            'BD' => 'seguridad',
            'WHERE' => [
                'cedula_usuario' => $this->cedula
            ]
        ]);
        $fila = $resultado->fetch(PDO::FETCH_ASSOC);
        $id = $fila['id'] ?? null;

        if($id) {
            $resultadoUpdate = $this->actualizarDatos2([
                'tabla' => 'proyecto_lacruz_seguridad.prompts_usuarios',
                'BD' => 'seguridad',
                'datos' => [
                    'respuesta_bot' => $this->respuesta
                ],
                'WHERE' => [
                    'id_prompt_usuario' => $id
                ]
            ]);
            $this->commit();
            
            require_once 'src/modelos/bitacoraModelo.php';
            $objBitacora = new \src\modelos\bitacoraModelo();
            $objBitacora->registrarBitacora('chatbot', 'recibió una respuesta del asistente', 'Exitoso', true);

            return $resultadoUpdate;
        }
        return false;
    }

    // Obtiene los últimos 10 mensajes del usuario para recordar el contexto
    public function obtenerHistorial($cedula) {
        $this->cedula = $cedula;
        $respuestaVal = $this->limpiar_Verificar([
            [
                "campo_nombre" => "cedula_usuario",
                "campo_valor" => &$this->cedula,
                "formulario_nombre" => "cédula",
                "requerido" => true,
                "minimo" => 7,
                "maximo" => 20
            ]
        ]);
        if ($respuestaVal !== false) return []; // Retornar vacío si falla la validación
        
        return $this->obtenerHistorialP();
    }

    private function obtenerHistorialP() {
        // Seleccionamos los datos filtrando por la cédula
        $instrucciones = [
            'campos' => 'prompt, respuesta_bot, fecha_prompt',
            'tabla' => 'proyecto_lacruz_seguridad.prompts_usuarios',
            'BD' => 'seguridad',
            'WHERE' => [
                'cedula_usuario' => $this->cedula,
                'status' => 1
            ]
        ];
        
        $resultado = $this->seleccionarDatos2($instrucciones);
        $historial = [];

        if ($resultado && $resultado->rowCount() > 0) {
            // Obtenemos todos los registros
            $filas = $resultado->fetchAll(PDO::FETCH_ASSOC);
            
            // Ampliamos a los últimos 30 para mejor contexto a largo plazo
            $ultimos = array_slice($filas, -30);
            
            foreach ($ultimos as $fila) {
                $historial[] = [
                    "texto" => $fila['prompt'],
                    "respuesta" => "",
                    "fecha" => $fila['fecha_prompt']
                ];
            }
        }
        return $historial;
    }

    // Busca productos y servicios activos pidiéndoselos a sus respectivos módulos
    public function obtenerCatalogo() {
        // No recibe inputs de usuario externo, pero se encapsula por convención estricta
        return $this->obtenerCatalogoP();
    }

    private function obtenerCatalogoP() {
        $catalogo = [
            "productos" => [],
            "servicios" => []
        ];

        // 1. Instanciar módulo de Productos y pedirle la información
        require_once 'src/modelos/productosModelo.php';
        $objProductos = new \src\modelos\productosModelo();
        $catalogo['productos'] = $objProductos->obtenerParaChatbot();

        // 2. Instanciar módulo de Servicios y pedirle la información
        require_once 'src/modelos/serviciosModelo.php';
        $objServicios = new \src\modelos\serviciosModelo();
        $catalogo['servicios'] = $objServicios->obtenerParaChatbot();

        return $catalogo;
    }
}
?>