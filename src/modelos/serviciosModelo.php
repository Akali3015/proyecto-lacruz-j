<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use PDO;
use PDOException;
use Exception;

class serviciosModelo extends conexion
{
    private $idServicio;
    private $idUnidadMedida;
    private $nombreServicio;
    private $costoServicio;

    public function seleccionarServicios($id = null)
    {
        $this->idServicio = $id;

        if ($this->idServicio != null && $this->idServicio != "") {
            $campos = [
                [
                    "campo_nombre" => 'id_servicio',
                    "campo_valor" => $this->idServicio,
                    "formulario_nombre" => "id del servicio",
                    "requerido" => true,
                    "minimo" => minRegexId,
                    "maximo" => maxRegexId,
                    "expresion_re" => regexId,
                    "tabla" => 'servicios',
                    "debeExistir" => true,
                ]
            ];

            $respuesta = $this->limpiar_Verificar($campos);
            if ($respuesta !== false) {
                return $respuesta;
                exit();
            }
        }
        return $this->seleccionarServiciosP();
    }
    public function registrarServicios($idUnidadMedida, $nombre, $costo)
    {
        try {
            $this->idUnidadMedida = $idUnidadMedida;
            $this->nombreServicio = $nombre;
            $this->costoServicio = $costo;

            $campos = [
                [
                    "campo_nombre" => "id_unidad_medida",
                    "campo_valor" => $this->idUnidadMedida,
                    "formulario_nombre" => "unidades de medida",
                    "requerido" => true,
                    "minimo" => minRegexId,
                    "maximo" => maxRegexId,
                    "expresion_re" => regexId,
                    "tabla" => "unidades_medidas",
                    "debeExistir" => true,
                ],
                [
                    "campo_nombre" => "nombre_servicio",
                    "campo_valor" => $this->nombreServicio,
                    "formulario_nombre" => "nombre del servicio",
                    "requerido" => true,
                    "minimo" => minRegexNombreObj,
                    "maximo" => maxRegexNombreObj,
                    "expresion_re" => regexNombreObj,
                    "tabla" => "servicios",
                    "debeSerUnico" => true,
                ],
                [
                    "campo_nombre" => "costo_servicio",
                    "campo_valor" => $this->costoServicio,
                    "formulario_nombre" => "costo del servicio",
                    "requerido" => true,
                    "minimo" => minRegexPrecio,
                    "maximo" => maxRegexPrecio,
                    "expresion_re" => regexPrecio,
                    "tabla" => "servicios",
                ],
            ];

            $respuesta = $this->limpiar_Verificar($campos);
            if ($respuesta !== false) {
                return $respuesta;
                exit();
            } else {
                return $this->registrarServiciosP();
            }
        } catch (PDOException $e) {
            error_log("Error en Servicio->registrar(): " . $e->getMessage());
            throw new Exception("Error al registrar el servicio en la base de datos: " . $e->getMessage());
        }
    }
    public function actualizarServicios($id, $idUnidadMedida, $nombre, $costo)
    {
        $this->idServicio = $id;
        $this->idUnidadMedida = $idUnidadMedida;
        $this->nombreServicio = $nombre;
        $this->costoServicio = $costo;

        $campos = [
            [
                "campo_nombre" => "id_servicio",
                "campo_valor" => $this->idServicio,
                "formulario_nombre" => "id del servicio",
                "requerido" => true,
                "minimo" => minRegexId,
                "maximo" => maxRegexId,
                "expresion_re" => regexId,
                "tabla" => "servicios",
                "debeExistir" => true,
            ],
            [
                "campo_nombre" => "id_unidad_medida",
                "campo_valor" => $this->idUnidadMedida,
                "formulario_nombre" => "id de la unidad de medida",
                "requerido" => true,
                "minimo" => minRegexId,
                "maximo" => maxRegexId,
                "expresion_re" => regexId,
                "tabla" => "unidades_medidas",
                "debeExistir" => true,
            ],
            [
                "campo_nombre" => "nombre_servicio",
                "campo_valor" => $this->nombreServicio,
                "formulario_nombre" => "nombre del servicio",
                "requerido" => true,
                "minimo" => minRegexNombreObj,
                "maximo" => maxRegexNombreObj,
                "expresion_re" => regexNombreObj,
                "tabla" => "servicios",
            ],
            [
                "campo_nombre" => "costo_servicio",
                "campo_valor" => $this->costoServicio,
                "formulario_nombre" => "costo del servicio",
                "requerido" => true,
                "minimo" => minRegexPrecio,
                "maximo" => maxRegexPrecio,
                "expresion_re" => regexPrecio,
                "tabla" => "servicios",
            ],
        ];

        $respuesta = $this->limpiar_Verificar($campos);
        if ($respuesta !== false) {
            return $respuesta;
            exit();
        } else {
            return $this->actualizarServiciosP();
        }
    }
    public function eliminarServicios($id)
    {
        $this->idServicio = $id;

        $campos = [
            [
                "campo_nombre" => "id_servicio",
                "campo_valor" => $this->idServicio,
                "formulario_nombre" => "id",
                "requerido" => true,
                "minimo" => minRegexId,
                "maximo" => maxRegexId,
                "expresion_re" => regexId,
                "debeExistir" => true,
                "tabla" => "servicios",
            ]
        ];

        $respuesta = $this->limpiar_Verificar($campos);
        if ($respuesta !== false) {
            return $respuesta;
            exit();
        } else {
            return $this->eliminarServiciosP();
        }
    }

    private function seleccionarServiciosP()
    {
        if ($this->idServicio == null || $this->idServicio == "") {
            $instruccionesBD = [
                'campos' => 's.id_servicio, s.nombre_servicio, um.nombre_unidad_medida, s.costo_servicio',
                'tabla' => 'servicios as s',
                'PEL' => 's',
                'datosJoins' => [
                    [
                        "TablaDestino" => "unidades_medidas as um",
                        "conexionLo" => "s.id_unidad_medida = um.id_unidad_medida",
                    ]
                ]

            ];
            $resultado = $this->seleccionarDatos($instruccionesBD);
            $Servicios = $resultado->fetchAll(PDO::FETCH_ASSOC);
            return $Servicios;
        } else {
            $instruccionesBD = [
                'campos' => '*',
                'tabla' => 'servicios',
                'WHERE' => [
                    [
                        "condicion_campo" => "id_servicio",
                        "condicion_marcador" => ":id",
                        "condicion_valor" => $this->idServicio,
                        "comparacion" => "=",
                    ]
                ]
            ];
            $resultado = $this->seleccionarDatos($instruccionesBD);

            if ($resultado->rowCount() <= 0) {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Servicio no encontrado",
                    "texto" => "El servicio que ha intentado actualizar no se encuentra en la base de datos",
                    "icono" => "error"
                ];
                return $alerta;
                exit();
            } else {
                $servicio = $resultado->fetch(PDO::FETCH_ASSOC);
            }
            return $servicio;
        }
    }
    private function registrarServiciosP()
    {
        $datos_registro_servicios = [
            [
                "campo_nombre" => "id_unidad_medida",
                "campo_marcador" => ":unidadMedida",
                "campo_valor" => $this->idUnidadMedida,
            ],
            [
                "campo_nombre" => "nombre_servicio",
                "campo_marcador" => ":nombre",
                "campo_valor" => $this->nombreServicio,
                "ponerEnMayusculas" => true,
            ],
            [
                "campo_nombre" => "costo_servicio",
                "campo_marcador" => ":costo",
                "campo_valor" => $this->costoServicio,
            ],
        ];
        $modeloBitacora = new bitacoraModelo();
        $ultimoId = $this->guardarDatos('servicios', $datos_registro_servicios);
        if ($ultimoId !== false && $ultimoId > 0) {
            $alerta = [
                "tipo" => "limpiarYcerrar",
                "titulo" => "Servicio registrado",
                "texto" => "El servicio ha sido registrado exitosamente",
                "icono" => "success"
            ];
            $modeloBitacora->registrarBitacora("Servicios", "Registrar", "Exito");
            $this->commit();
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Servicio no registrado",
                "texto" => "Error al registrar el servicio",
                "icono" => "error",
            ];
            $modeloBitacora->registrarBitacora("Servicios", "Registrar", "Fallido");
        }
        return $alerta;
    }
    private function actualizarServiciosP()
    {

        $instruccionesBD = [
            "tabla" => "servicios",
            "datos" => [
                [
                    "campo_nombre" => "id_servicio",
                    "campo_marcador" => ":Id",
                    "campo_valor" => $this->idServicio
                ],
                [
                    "campo_nombre" => "nombre_servicio",
                    "campo_marcador" => ":Nombre",
                    "campo_valor" => $this->nombreServicio,
                    "ponerEnMayusculas" => true,
                ],
                [
                    "campo_nombre" => "costo_servicio",
                    "campo_marcador" => ":Costo",
                    "campo_valor" => $this->costoServicio,
                ],
                [
                    "campo_nombre" => "id_unidad_medida",
                    "campo_marcador" => ":unidadMedida",
                    "campo_valor" => $this->idUnidadMedida,
                ],
            ],
            "condiciones" => [
                [
                    "condicion_campo" => "id_servicio",
                    "condicion_marcador" => ":Id",
                    "condicion_valor" => $this->idServicio,
                    "comparacion" => "=",
                ]
            ]
        ];

        $resultado = $this->actualizarDatos($instruccionesBD);
        $modeloBitacora = new bitacoraModelo();
        if ($resultado == false || $resultado <= 0) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Sin cambios realizados",
                "texto" => "No se realizó ningún cambio en el servicio",
                "icono" => "warning",
            ];
            $modeloBitacora->registrarBitacora("Servicios", "Actualizar", "Fallido");
        } else {
            $alerta = [
                "tipo" => "limpiarYcerrar",
                "titulo" => "Servicio actualizado",
                "texto" => "El servicio ha sido actualizado exitosamente",
                "icono" => "success",
            ];
            $modeloBitacora->registrarBitacora("Servicios", "Actualizar", "Exito");
            $this->commit();
        }
        return $alerta;
    }
    private function eliminarServiciosP()
    {
        $eliminarServicio = $this->eliminarDatos("servicios", "id_servicio", $this->idServicio);
        $modeloBitacora = new bitacoraModelo();
        if ($eliminarServicio->rowCount() == 1) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Servicio eliminado",
                "texto" => "El servico ha sido eliminado con éxito",
                "icono" => "success"
            ];
            $modeloBitacora->registrarBitacora("Servicios", "Eliminar", "Exito");
            $this->commit();
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Servicio no encontrado",
                "texto" => "El servicio no existe en la Base de Datos",
                "icono" => "error"
            ];
            $modeloBitacora->registrarBitacora("Servicios", "Eliminar", "Fallido");
        }
        return $alerta;
    }
}
