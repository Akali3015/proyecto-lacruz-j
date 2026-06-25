<?php
namespace src\modelos;
use PDO;
use src\modelos\traitModelo;

class chatbotModelo {
    use traitModelo;

    // Guarda el mensaje enviado por el usuario en la BD de seguridad
    public function guardarPrompt($cedula, $mensaje) {
        $instrucciones = [
            'tabla' => 'proyecto_lacruz_seguridad.prompts_usuarios',
            'BD' => 'seguridad',
            'datos' => [
                'cedula_usuario' => $cedula,
                'prompt' => $mensaje,
                'status' => 1
            ]
        ];
        $resultado = $this->guardarDatos2($instrucciones);
        $this->commit();
        return $resultado;
    }

    // Guarda la respuesta generada por Gemini
    public function guardarRespuestaBot($cedula, $respuesta) {
        // Obtenemos el último prompt de este usuario
        $resultado = $this->seleccionarDatos2([
            'campos' => 'MAX(id_prompt_usuario) as id',
            'tabla' => 'proyecto_lacruz_seguridad.prompts_usuarios',
            'BD' => 'seguridad',
            'WHERE' => [
                'cedula_usuario' => $cedula
            ]
        ]);
        $fila = $resultado->fetch(PDO::FETCH_ASSOC);
        $id = $fila['id'] ?? null;

        if($id) {
            $resultadoUpdate = $this->actualizarDatos2([
                'tabla' => 'proyecto_lacruz_seguridad.prompts_usuarios',
                'BD' => 'seguridad',
                'datos' => [
                    'respuesta_bot' => $respuesta
                ],
                'WHERE' => [
                    'id_prompt_usuario' => $id
                ]
            ]);
            $this->commit();
            return $resultadoUpdate;
        }
        return false;
    }

    // Obtiene los últimos 10 mensajes del usuario para recordar el contexto
    public function obtenerHistorial($cedula) {
        // Seleccionamos los datos filtrando por la cédula
        $instrucciones = [
            'campos' => 'prompt, respuesta_bot, fecha_prompt',
            'tabla' => 'proyecto_lacruz_seguridad.prompts_usuarios',
            'BD' => 'seguridad',
            'WHERE' => [
                'cedula_usuario' => $cedula,
                'status' => 1
            ]
        ];
        
        $resultado = $this->seleccionarDatos2($instrucciones);
        $historial = [];

        if ($resultado && $resultado->rowCount() > 0) {
            // Obtenemos todos los registros (ordenados como salgan de la BD, usualmente ascendente)
            $filas = $resultado->fetchAll(PDO::FETCH_ASSOC);
            
            // Ampliamos a los últimos 30 para mejor contexto a largo plazo
            $ultimos = array_slice($filas, -30);
            
            foreach ($ultimos as $fila) {
                $historial[] = [
                    "texto" => $fila['prompt'],
                    "respuesta" => $fila['respuesta_bot'] ?? "",
                    "fecha" => $fila['fecha_prompt']
                ];
            }
        }
        return $historial;
    }

    // Busca productos y servicios activos pidiéndoselos a sus respectivos módulos
    public function obtenerCatalogo() {
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