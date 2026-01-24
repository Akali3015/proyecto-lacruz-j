<?php

namespace src\modelos;

use src\config\connect\conexion;
use PDO;
use PDOException;
use Exception;

class proveedoresModelo extends conexion
{

    private $rif_proveedor;
    private $razon_social_proveedor;
    private $telefono_proveedor;
    private $correo_proveedor;
    private $direccion_proveedor;

    public function seleccionarProveedor($rif = null)
    {

        $this->rif_proveedor = $rif;

        if ($this->rif_proveedor != null && $this->rif_proveedor != "") {
            // Arrays para las validaciones
            $campos = [
                [
                    "campo_valor" => $this->rif_proveedor,
                    "formulario_nombre" => "RIF",
                    "requerido" => true,
                    "minimo" => minRegexNombreObj,
                    "maximo" => maxRegexNombreObj,
                    "expresion_re" => regexNombreObj
                ]
            ];

            $respuesta = $this->limpiar_Verificar($campos);
            if ($respuesta !== false) {
                return $respuesta;
                exit();
            } else {
                return $this->seleccionarProveedorP();
            }
        } else {
            return $this->seleccionarProveedorP();
        }
    }
    public function registrarProveedor($rif, $razon, $telefono, $correo, $direccion)
    {
        try {
            $this->rif_proveedor = $rif;
            $this->razon_social_proveedor = $razon;
            $this->telefono_proveedor = $telefono;
            $this->correo_proveedor = $correo;
            $this->direccion_proveedor = $direccion;

            $campos = [
                [
                    "campo_nombre" => "rif_proveedor",
                    "campo_valor" => $this->rif_proveedor,
                    "formulario_nombre" => "RIF del proveedor",
                    "requerido" => true,
                    "minimo" => minRegexNombreObj,
                    "maximo" => maxRegexNombreObj,
                    "expresion_re" => regexNombreObj,
                    "tabla" => "proveedores",
                    "debeSerUnico" => true,
                ],
                [
                    "campo_nombre" => "razon_social_proveedor",
                    "campo_valor" => $this->razon_social_proveedor,
                    "formulario_nombre" => "razón social del proveedor",
                    "requerido" => true,
                    "minimo" => minRegexNombreObj,
                    "maximo" => maxRegexNombreObj,
                    "expresion_re" => regexNombreObj,
                    "tabla" => "proveedores",
                    "debeSerUnico" => true,
                ],
                [
                    "campo_nombre" => "telefono_proveedor",
                    "campo_valor" => $this->telefono_proveedor,
                    "formulario_nombre" => "teléfono del proveedor",
                    "requerido" => true,
                    "minimo" => minRegexTelefono,
                    "maximo" => maxRegexTelefono,
                    "expresion_re" => regexTelefono,
                ],
                [
                    "campo_nombre" => "correo_proveedor",
                    "campo_valor" => $this->correo_proveedor,
                    "formulario_nombre" => "correo electrónico del proveedor",
                    "requerido" => true,
                    "minimo" => minRegexCorreo,
                    "maximo" => maxRegexCorreo,
                    "expresion_re" => regexCorreo,
                ],
                [
                    "campo_nombre" => "direccion_proveedor",
                    "campo_valor" => $this->direccion_proveedor,
                    "formulario_nombre" => "dirección del proveedor",
                    "requerido" => true,
                    "minimo" => minRegexDescripcion,
                    "maximo" => maxRegexDescripcion,
                    "expresion_re" => regexDescripcion,
                ]
            ];


            $respuesta = $this->limpiar_Verificar($campos);

            if ($respuesta !== false) {
                return $respuesta;
                exit();
            } else {
                return $this->registrarProveedoresP();
            }
        } catch (PDOException $e) {
            error_log("Error: " . $e->getMessage());
            throw new Exception("Error al registrar el proveedor en la base de datos: " . $e->getMessage());
        }
    }
    public function actualizarProveedor($rif, $razon, $telefono, $correo, $direccion)
    {
        $this->rif_proveedor = $rif;
        $this->razon_social_proveedor = $razon;
        $this->telefono_proveedor = $telefono;
        $this->correo_proveedor = $correo;
        $this->direccion_proveedor = $direccion;
        $campos = [
            [
                "campo_nombre" => "rif_proveedor",
                "campo_valor" => $this->rif_proveedor,
                "formulario_nombre" => "RIF",
                "requerido" => true,
                "minimo" => minRegexNombreObj,
                "maximo" => maxRegexNombreObj,
                "expresion_re" => regexNombreObj,
                "tabla" => "proveedores",
                "debeExistir" => true,
            ],
            [
                "campo_nombre" => "razon_social_proveedor",
                "campo_valor" => $this->razon_social_proveedor,
                "formulario_nombre" => "razón social del proveedor",
                "requerido" => true,
                "minimo" => minRegexNombreObj,
                "maximo" => maxRegexNombreObj,
                "expresion_re" => regexNombreObj,
                "tabla" => "proveedores",
                "debeSerUnico" => true,
            ],
            [
                "campo_nombre" => "telefono_proveedor",
                "campo_valor" => $this->telefono_proveedor,
                "formulario_nombre" => "teléfono del proveedor",
                "requerido" => true,
                "minimo" => minRegexTelefono,
                "maximo" => maxRegexTelefono,
                "expresion_re" => regexTelefono,
            ],
            [
                "campo_nombre" => "correo_proveedor",
                "campo_valor" => $this->correo_proveedor,
                "formulario_nombre" => "correo electrónico del proveedor",
                "requerido" => true,
                "minimo" => minRegexCorreo,
                "maximo" => maxRegexCorreo,
                "expresion_re" => regexCorreo,
            ],
            [
                "campo_nombre" => "direccion_proveedor",
                "campo_valor" => $this->direccion_proveedor,
                "formulario_nombre" => "dirección del proveedor",
                "requerido" => true,
                "minimo" => minRegexDescripcion,
                "maximo" => maxRegexDescripcion,
                "expresion_re" => regexDescripcion,
            ],
        ];
        $respuesta = $this->limpiar_Verificar($campos);

        if ($respuesta !== false) {
            return $respuesta;
            exit();
        } else {
            return $this->actualizarProveedoresP();
        }
    }
    public function eliminarProveedor($rif)
    {
        $this->rif_proveedor = $rif;

        $campos = [
            [
                "campo_nombre" => "rif_proveedor",
                "campo_valor" => $this->rif_proveedor,
                "formulario_nombre" => "RIF del proveedor",
                "requerido" => true,
                "minimo" => minRegexNombreObj,
                "maximo" => maxRegexNombreObj,
                "expresion_re" => regexNombreObj,
                "tabla" => "proveedores",
                "debeExistir" => true,
                "camposDiferentes" => 1
            ]
        ];

        $respuesta = $this->limpiar_Verificar($campos);
        if ($respuesta !== false) {
            return $respuesta;
            exit();
        } else {
            return $this->eliminarProveedorP();
        }
    }

    // PRIVADOS
    private function seleccionarProveedorP()
    {
        if ($this->rif_proveedor == null || $this->rif_proveedor == "") {
            $instruccionesBD = [
                'campos' => 'rif_proveedor, razon_social_proveedor, telefono_proveedor, correo_proveedor, direccion_proveedor',
                'tabla' => 'proveedores',
            ];
            $resultado = $this->seleccionarDatos($instruccionesBD);
            $proveedor = $resultado->fetchAll(PDO::FETCH_ASSOC);
            return $proveedor;
        } else {

            $instruccionesBD = [
                'campos' => 'rif_proveedor, razon_social_proveedor, telefono_proveedor, correo_proveedor, direccion_proveedor',
                'tabla' => 'proveedores',
                'WHERE' => [
                    [
                        "condicion_campo" => "rif_proveedor",
                        "condicion_marcador" => ":RIF",
                        "condicion_valor" => $this->rif_proveedor,
                        "comparacion" => "="
                    ]
                ]
            ];
            $resultado = $this->seleccionarDatos($instruccionesBD);
            if ($resultado->rowCount() <= 0) {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Proveedor no encontrado",
                    "texto" => "El proveedor que ha intentado buscar no se encuentra en la base de datos",
                    "icono" => "error"
                ];
                return $alerta;
                exit();
            } else {
                $clientes = $resultado->fetch(PDO::FETCH_ASSOC);
            }
            return $clientes;
        }
    }
    private function registrarProveedoresP()
    {
        $datos_registro_proveedor = [
            [
                "campo_nombre" => "rif_proveedor",
                "campo_marcador" => ":rif_proveedor",
                "campo_valor" => $this->rif_proveedor
            ],
            [
                "campo_nombre" => "razon_social_proveedor",
                "campo_marcador" => ":razon_social",
                "campo_valor" => $this->razon_social_proveedor,
                "ponerEnMayusculas" => true
            ],
            [
                "campo_nombre" => "telefono_proveedor",
                "campo_marcador" => ":telefono",
                "campo_valor" => $this->telefono_proveedor
            ],
            [
                "campo_nombre" => "correo_proveedor",
                "campo_marcador" => ":correo",
                "campo_valor" => $this->correo_proveedor
            ],
            [
                "campo_nombre" => "direccion_proveedor",
                "campo_marcador" => ":direccion",
                "campo_valor" => $this->direccion_proveedor
            ],
        ];

        $condicion = [
            "condicion_campo" => "rif_proveedor",
            "condicion_marcador" => ":rif_proveedor",
            "condicion_valor" => $this->rif_proveedor
        ];

        $ultimoID = $this->guardarDatos('proveedores', $datos_registro_proveedor, $condicion);

        if ($ultimoID !== false && $ultimoID > 0) {
            $alerta = [
                "tipo" => "limpiar",
                "titulo" => "Proveedor registrado",
                "texto" => "El proveedor ha sido registrado exitosamente",
                "icono" => "success",
            ];
            $this->commit();
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Proveedor no registrado",
                "texto" => "El proveedor no ha sido registrado exitosamente",
                "icono" => "error",
            ];
        }
        return $alerta;
    }
    private function actualizarProveedoresP()
    {
        $instruccionesBD = [
            "campos" => "rif_proveedor",
            "tabla" => "proveedores",
            'WHERE' => [
                [
                    "condicion_campo" => "rif_proveedor",
                    "condicion_marcador" => ":rif_proveedor",
                    "condicion_valor" => $this->rif_proveedor,
                    "comparacion" => "="
                ]
            ]
        ];
        $resultado = $this->seleccionarDatos($instruccionesBD);
        $proveedoresExistente = $resultado->fetch(PDO::FETCH_ASSOC);

        if ($this->rif_proveedor == '') {
            $this->rif_proveedor = $proveedoresExistente['rif_proveedor'];
        }
        $instruccionesBD = [
            "tabla" => "proveedores",
            "datos" => [
                [
                    "campo_nombre" => "rif_proveedor",
                    "campo_marcador" => ":RIF",
                    "campo_valor" => $this->rif_proveedor
                ],
                [
                    "campo_nombre" => "razon_social_proveedor",
                    "campo_marcador" => ":razon_social",
                    "campo_valor" => $this->razon_social_proveedor,
                    "ponerEnMayusculas" => true
                ],
                [
                    "campo_nombre" => "telefono_proveedor",
                    "campo_marcador" => ":telefono",
                    "campo_valor" => $this->telefono_proveedor
                ],
                [
                    "campo_nombre" => "correo_proveedor",
                    "campo_marcador" => ":correo",
                    "campo_valor" => $this->correo_proveedor
                ],
                [
                    "campo_nombre" => "direccion_proveedor",
                    "campo_marcador" => ":direccion",
                    "campo_valor" => $this->direccion_proveedor
                ]
            ],
            "condiciones" => [
                [
                    "condicion_campo" => "rif_proveedor",
                    "condicion_marcador" => ":rif_proveedor",
                    "condicion_valor" => $this->rif_proveedor,
                    "comparacion" => "="
                ]
            ]
        ];

        $resultado = $this->actualizarDatos($instruccionesBD);
        if ($resultado == false || $resultado <= 0) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Proveedor no actualizado",
                "texto" => "El proveedor no ha sido actualizado exitosamente",
                "icono" => "warning",
            ];
        } else {
            $alerta = [
                "tipo" => "limpiarYcerrar",
                "titulo" => "Proveedor actualizado",
                "texto" => "El proveedor ha sido actualizado exitosamente",
                "icono" => "success",
            ];
            $this->commit();
        }
        return $alerta;
    }
    private function eliminarProveedorP()
    {
        $eliminarProveedor = $this->eliminarDatos('proveedores', 'rif_proveedor', $this->rif_proveedor);
        if ($eliminarProveedor->rowCount() == 1) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Proveedor eliminado",
                "texto" => "El proveedor ha sido eliminado exitosamente",
                "icono" => "success",
            ];
            $this->commit();
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Proveedor no encontrado",
                "texto" => "El proveedor no existe en la base de datos",
                "icono" => "error",
            ];
        }
        return $alerta;
    }
}
