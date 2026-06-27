<?php
namespace src\modelos;
use PDO;
use src\config\connect\conexion;

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
        ];
        $resultado = $this->guardarDatos2($instrucciones);
        $this->commit();

        require_once 'src/modelos/bitacoraModelo.php';
        $objBitacora = new \src\modelos\bitacoraModelo();
        $objBitacora->registrarBitacora('chatbot', 'envió un mensaje al asistente', 'Exitoso', true);

        return $resultado;
    }

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