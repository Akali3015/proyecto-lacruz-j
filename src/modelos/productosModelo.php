<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use PDO;
use PDOException;
use Exception;

class productosModelo extends conexion
{
    private $idProducto;
    private $idUnidadMedida;
    private $nombreProducto;
    private $precioProductoDetal;
    private $precioProductoMayor;
    private $stockProducto;
    private $fabricadoProducto;
    private $presentaciones;
    private $materiasPrimas;

    public function seleccionarProductos($id = null)
    {
        $this->idProducto = $id;
        if ($this->idProducto != null && $this->idProducto != "") {
            $campos = [
                [
                    "campo_nombre" => 'id_producto',
                    "campo_valor" => $this->idProducto,
                    "formulario_nombre" => "id del producto",
                    "requerido" => true,
                    "minimo" => minRegexId,
                    "maximo" => maxRegexId,
                    "expresion_re" => regexId,
                    "tabla" => 'productos',
                    "debeExistir" => true,
                ]
            ];

            $respuesta = $this->limpiar_Verificar($campos);
            if ($respuesta !== false) {
                return $respuesta;
                exit();
            }
        }
        return $this->seleccionarProductosP();
    }
    public function registrarProductos($idUnidadMedida, $nombre, $costoDetal, $costoMayor, $stock, $fabricado, $presentaciones = [], $materiasPrimas = [])
    {
        try {
            $this->idUnidadMedida = $idUnidadMedida;
            $this->nombreProducto = $nombre;
            $this->precioProductoDetal = $costoDetal;
            $this->precioProductoMayor = $costoMayor;
            $this->stockProducto = $stock;
            $this->fabricadoProducto = $fabricado;
            $this->presentaciones = $presentaciones;
            $this->materiasPrimas = $materiasPrimas;


            $campos = [
                [
                    "campo_nombre" => "id_unidad_medida",
                    "campo_valor" => &$this->idUnidadMedida,
                    "formulario_nombre" => "unidades de medida",
                    "requerido" => true,
                    "minimo" => minRegexId,
                    "maximo" => maxRegexId,
                    "expresion_re" => regexId,
                    "tabla" => "unidades_medidas",
                    "debeExistir" => true,
                ],
                [
                    "campo_nombre" => "nombre_producto",
                    "campo_valor" => &$this->nombreProducto,
                    "formulario_nombre" => "nombre del producto",
                    "requerido" => true,
                    "minimo" => minRegexNombreObj,
                    "maximo" => maxRegexNombreObj,
                    "expresion_re" => regexNombreObj,
                    "tabla" => "productos",
                    "debeSerUnico" => true,
                ],
                [
                    "campo_nombre" => "precio_producto_detal",
                    "campo_valor" => &$this->precioProductoDetal,
                    "formulario_nombre" => "costo del producto al detal",
                    "requerido" => true,
                    "minimo" => minRegexPrecio,
                    "maximo" => maxRegexPrecio,
                    "expresion_re" => regexPrecio,
                    "tabla" => "productos",
                ],
                [
                    "campo_nombre" => "precio_producto_mayor",
                    "campo_valor" => &$this->precioProductoMayor,
                    "formulario_nombre" => "costo del producto al mayor",
                    "requerido" => true,
                    "minimo" => minRegexPrecio,
                    "maximo" => maxRegexPrecio,
                    "expresion_re" => regexPrecio,
                    "tabla" => "productos",
                ],
                [
                    "campo_nombre" => "stock_producto",
                    "campo_valor" => &$this->stockProducto,
                    "formulario_nombre" => "stock del producto",
                    "requerido" => true,
                    "minimo" => minRegexCantidadItem,
                    "maximo" => maxRegexCantidadItem,
                    "expresion_re" => regexCantidadItem,
                    "tabla" => "productos",
                ],
                [
                    "campo_nombre" => "producto_es_fabricado",
                    "campo_valor" => &$this->fabricadoProducto,
                    "formulario_nombre" => "fabricado",
                    "minimo" => minRegexCantidadItem,
                    "maximo" => maxRegexCantidadItem,
                    "expresion_re" => regexCantidadItem,
                    "tabla" => "productos",
                ],
            ];

            //Presentaciones
            if(!is_array($this->presentaciones) && $this->presentaciones!=''){
                $this->presentaciones=[$this->presentaciones];
            }
            if (count($presentaciones) == 0) {
                $alerta = [
                    'tipo' => 'simple',
                    'titulo' => 'Sin Presentaciones',
                    'texto' => 'Debe seleccionar al menos una presentación',
                    'icono' => 'error',
                ];
                return $alerta;
                exit();
            } else {
                foreach ($presentaciones as &$idPresentacion) {
                    $campos[] = [
                        "campo_nombre" => "id_presentacion",
                        "campo_valor" => &$idPresentacion,
                        "formulario_nombre" => "presentación",
                        "requerido" => false,
                        "minimo" => minRegexId,
                        "maximo" => maxRegexId,
                        "expresion_re" => regexId,
                        "tabla" => "presentaciones",
                        "debeExistir" => true,
                    ];
                }
            }

            //Materias Primas
            if ($this->fabricadoProducto == 1) {
                if (count($this->materiasPrimas) == 0) {
                    $alerta = [
                        'tipo' => 'simple',
                        'icono' => 'error',
                        'titulo' => 'Sin materias primas',
                        'texto' => 'Si el producto es fabricado debe especificar las cantidades de materias primas que este usa para su elaboración',
                    ];
                    return $alerta;
                    exit();
                }
                foreach ($this->materiasPrimas as &$mp) {
                    $campos[] = [
                        "campo_nombre" => "id_materia_prima",
                        "campo_valor" => &$mp['id_materia_prima'],
                        "formulario_nombre" => "materia prima",
                        "requerido" => true,
                        "minimo" => minRegexId,
                        "maximo" => maxRegexId,
                        "expresion_re" => regexId,
                        "tabla" => "materias_primas",
                        "debeExistir" => true,
                    ];
                    $campos[] = [
                        "campo_valor" => &$mp['cantidad_materia_prima'],
                        "formulario_nombre" => "cantidad de materia prima",
                        "requerido" => true,
                        "minimo" => minRegexPrecio,
                        "maximo" => maxRegexPrecio,
                        "expresion_re" =>  regexPrecio,
                    ];
                }
            }

            $respuesta = $this->limpiar_Verificar($campos);
            if ($respuesta !== false) {
                return $respuesta;
                exit();
            } else {
                return $this->registrarProductosP();
            }
        } catch (PDOException $e) {
            error_log("Error en productosModelo->registrar(): " . $e->getMessage());
            throw new Exception("Error al registrar el producto en la base de datos: " . $e->getMessage());
        }
    }
    public function actualizarProductos($id, $idUnidadMedida, $nombre, $costoDetal, $costoMayor, $stock, $fabricado, $presentaciones = [], $materiasPrimas = [])
    {
        $this->idProducto = $id;
        $this->idUnidadMedida = $idUnidadMedida;
        $this->nombreProducto = $nombre;
        $this->precioProductoDetal = $costoDetal;
        $this->precioProductoMayor = $costoMayor;
        $this->stockProducto = $stock;
        $this->fabricadoProducto = $fabricado;
        $this->presentaciones = $presentaciones;
        $this->materiasPrimas = $materiasPrimas;

        $campos = [
            [
                "campo_nombre" => "id_producto",
                "campo_valor" => $this->idProducto,
                "formulario_nombre" => "id del producto",
                "requerido" => true,
                "minimo" => minRegexId,
                "maximo" => maxRegexId,
                "expresion_re" => regexId,
                "tabla" => "productos",
                "debeExistir" => true,
            ],
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
                "campo_nombre" => "nombre_producto",
                "campo_valor" => $this->nombreProducto,
                "formulario_nombre" => "nombre del producto",
                "requerido" => true,
                "minimo" => minRegexNombreObj,
                "maximo" => maxRegexNombreObj,
                "expresion_re" => regexNombreObj,
                "tabla" => "productos",
            ],
            [
                "campo_nombre" => "precio_producto_detal",
                "campo_valor" => $this->precioProductoDetal,
                "formulario_nombre" => "costo del producto al detal",
                "requerido" => true,
                "minimo" => minRegexPrecio,
                "maximo" => maxRegexPrecio,
                "expresion_re" => regexPrecio,
                "tabla" => "productos",
            ],
            [
                "campo_nombre" => "precio_producto_mayor",
                "campo_valor" => $this->precioProductoMayor,
                "formulario_nombre" => "costo del producto al mayor",
                "requerido" => true,
                "minimo" => minRegexPrecio,
                "maximo" => maxRegexPrecio,
                "expresion_re" => regexPrecio,
                "tabla" => "productos",
            ],
            [
                "campo_nombre" => "stock_producto",
                "campo_valor" => $this->stockProducto,
                "formulario_nombre" => "stock del producto",
                "requerido" => $this->fabricadoProducto == 0,
                "minimo" => minRegexCantidadItem,
                "maximo" => maxRegexCantidadItem,
                "expresion_re" => regexCantidadItem,
                "tabla" => "productos",
            ],
            [
                "campo_nombre" => "producto_es_fabricado",
                "campo_valor" => $this->fabricadoProducto,
                "formulario_nombre" => "fabricado",
                "minimo" => minRegexCantidadItem,
                "maximo" => maxRegexCantidadItem,
                "expresion_re" => regexCantidadItem,
                "tabla" => "productos",
            ],
        ];

        if(!is_array($this->presentaciones) && $this->presentaciones!=''){
            $this->presentaciones=[$this->presentaciones];
        }
        if (count($this->presentaciones) == 0) {
            $alerta = [
                'tipo' => 'simple',
                'icono' => 'error',
                'titulo' => 'Sin presentaciones seleccionadas',
                'texto' => 'Debe seleccionar al menos una presentación',
            ];
            return $alerta;
            exit();
        } else {
            
            foreach ($this->presentaciones as &$idPresentacion) {
                $campos[] = [
                    "campo_nombre" => "id_presentacion",
                    "campo_valor" => &$idPresentacion,
                    "formulario_nombre" => "presentación",
                    "requerido" => false,
                    "minimo" => minRegexId,
                    "maximo" => maxRegexId,
                    "expresion_re" => regexId,
                    "tabla" => "presentaciones",
                    "debeExistir" => true,
                ];
            }
        }

        if ($this->fabricadoProducto == 1) {
            foreach ($materiasPrimas as &$mp) {
                $campos[] = [
                    "campo_nombre" => "id_materia_prima",
                    "campo_valor" => &$mp['id_materia_prima'],
                    "formulario_nombre" => "materia prima",
                    "requerido" => true,
                    "minimo" => minRegexId,
                    "maximo" => maxRegexId,
                    "expresion_re" => regexId,
                    "tabla" => "materias_primas",
                    "debeExistir" => true,
                ];
                $campos[] = [
                    "campo_nombre" => "cantidad_materia_prima",
                    "campo_valor" => &$mp['cantidad_materia_prima'],
                    "formulario_nombre" => "cantidad de materia prima",
                    "requerido" => true,
                    "minimo" => minRegexCantidadItem,
                    "maximo" => maxRegexCantidadItem,
                    "expresion_re" => regexCantidadItem,
                    "tabla" => "materias_primas_productos",
                ];
            }
        }

        $respuesta = $this->limpiar_Verificar($campos);
        if ($respuesta !== false) {
            return $respuesta;
            exit();
        } else {
            return $this->actualizarProductosP();
        }
    }
    public function eliminarProductos($id)
    {
        $this->idProducto = $id;

        $campos = [
            [
                "campo_nombre" => "id_producto",
                "campo_valor" => $this->idProducto,
                "formulario_nombre" => "id",
                "requerido" => true,
                "minimo" => minRegexId,
                "maximo" => maxRegexId,
                "expresion_re" => regexId,
                "debeExistir" => true,
                "tabla" => "productos",
            ]
        ];

        $respuesta = $this->limpiar_Verificar($campos);
        if ($respuesta !== false) {
            return $respuesta;
            exit();
        } else {
            return $this->eliminarProductosP();
        }
    }
    private function seleccionarProductosP()
    {
        if ($this->idProducto == null || $this->idProducto == "") {
            $instruccionesBD = [
                'campos' => '
                    p.id_producto, p.nombre_producto, 
                    um.nombre_unidad_medida, p.stock_producto, 
                    p.precio_producto_detal, p.precio_producto_mayor
                ',
                'tabla' => 'productos as p',
                'PEL' => 'p',
                'datosJoins' => [
                    [
                        "TablaDestino" => "unidades_medidas as um",
                        "conexionLo" => "p.id_unidad_medida = um.id_unidad_medida",
                    ]
                ]
            ];

            $resultado = $this->seleccionarDatos($instruccionesBD);
            $Productos = $resultado->fetchAll(PDO::FETCH_ASSOC);

            return $Productos;
        } else {

            // Datos del producto
            $instruccionesBD = [
                'campos' => '*',
                'tabla' => 'productos',
                'WHERE' => [
                    [
                        "condicion_campo" => "id_producto",
                        "condicion_marcador" => ":id",
                        "condicion_valor" => $this->idProducto,
                        "comparacion" => "=",
                    ]
                ]
            ];
            $resultado = $this->seleccionarDatos($instruccionesBD);
            if ($resultado->rowCount() <= 0) {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Producto no encontrado",
                    "texto" => "El producto que ha intentado actualizar no se encuentra en la base de datos",
                    "icono" => "error"
                ];
                return $alerta;
                exit();
            } else {
                $producto = $resultado->fetch(PDO::FETCH_ASSOC);
            }

            // Sus presentaciones
            $idsPresentaciones=[];
            $instruccionesBD = [
                'campos' => 'id_presentacion',
                'tabla' => 'productos_presentaciones',
                'WHERE' => [
                    [
                        "condicion_campo" => "id_producto",
                        "condicion_marcador" => ":id",
                        "condicion_valor" => $this->idProducto,
                        "comparacion" => "=",
                    ]
                ]
            ];
            $resultado = $this->seleccionarDatos($instruccionesBD);
            if ($resultado->rowCount() > 0) {
                $idsPresentaciones = $resultado->fetchAll(PDO::FETCH_COLUMN);
            }

            // Materias Primas
            $materiasPrimas=[];
            $instruccionesBD = [
                'campos' => 'id_materia_prima, cantidad_materia_prima',
                'tabla' => 'materias_primas_productos',
                'WHERE' => [
                    [
                        "condicion_campo" => "id_producto",
                        "condicion_marcador" => ":id",
                        "condicion_valor" => $this->idProducto,
                        "comparacion" => "=",
                    ]
                ]
            ];
            $resultado = $this->seleccionarDatos($instruccionesBD);
            if ($resultado->rowCount() > 0) {
                $materiasPrimas = $resultado->fetchAll(PDO::FETCH_ASSOC);
            }

            $datosExtra=[
                'idsPresentaciones'=>$idsPresentaciones,
                'materias_primas'=>$materiasPrimas,
            ];
            $producto['detallesExtra']=$datosExtra;
            return $producto;
        }
    }
    private function registrarProductosP()
    {
        //Registro del producto
        $datos_registro_productos = [
            [
                "campo_nombre" => "id_unidad_medida",
                "campo_marcador" => ":unidadMedida",
                "campo_valor" => $this->idUnidadMedida,
            ],
            [
                "campo_nombre" => "nombre_producto",
                "campo_marcador" => ":nombre",
                "campo_valor" => $this->nombreProducto,
                "ponerEnMayusculas" => true,
            ],
            [
                "campo_nombre" => "stock_producto",
                "campo_marcador" => ":stock",
                "campo_valor" => $this->stockProducto,
            ],
            [
                "campo_nombre" => "precio_producto_detal",
                "campo_marcador" => ":costoDetal",
                "campo_valor" => $this->precioProductoDetal,
            ],
            [
                "campo_nombre" => "precio_producto_mayor",
                "campo_marcador" => ":costoMayor",
                "campo_valor" => $this->precioProductoMayor,
            ],
            [
                "campo_nombre" => "producto_es_fabricado",
                "campo_marcador" => ":fabricado",
                "campo_valor" => $this->fabricadoProducto,
            ],
        ];
        $idProducto = $this->guardarDatos('productos', $datos_registro_productos);
        $bitacoraModelo = new bitacoraModelo();
        if ($idProducto == false || $idProducto <= 0) {
            $alertaError = [
                'tipo' => 'simple',
                'titulo' => 'Producto no registrado',
                'texto' => 'El producto no ha podido ser registrado en la Base de Datos',
                'icono' => 'error',
            ];
            $bitacoraModelo->registrarBitacora("Productos", "Registrar", "Fallido");
            $this->rollback();
            return $alertaError;
            exit();
        }

        //Registro de las Presentaciones
        foreach ($this->presentaciones as $idPresentacion) {
            $datos_presentacion = [
                [
                    "campo_nombre" => "id_producto",
                    "campo_marcador" => ":id_producto",
                    "campo_valor" => $idProducto,
                ],
                [
                    "campo_nombre" => "id_presentacion",
                    "campo_marcador" => ":id_presentacion",
                    "campo_valor" => $idPresentacion,
                ],
            ];
            $ultimoIdPre = $this->guardarDatos('productos_presentaciones', $datos_presentacion);
            if ($ultimoIdPre == false || $ultimoIdPre <= 0) {
                $alertaError = [
                    'tipo' => 'simple',
                    'titulo' => 'Presentacion no registrada',
                    'texto' => 'La presentacion no ha podido ser registrada en la Base de Datos',
                    'icono' => 'error',
                ];
                $this->rollback();
                return $alertaError;
                exit();
            }
        }

        //Registro de las Materias Primas
        if ($this->fabricadoProducto == 1) {
            foreach ($this->materiasPrimas as $mp) {
                $datos_materia_prima = [
                    [
                        "campo_nombre" => "id_producto",
                        "campo_marcador" => ":id_producto",
                        "campo_valor" => $idProducto,
                    ],
                    [
                        "campo_nombre" => "id_materia_prima",
                        "campo_marcador" => ":id_materia_prima",
                        "campo_valor" => $mp['id_materia_prima'],
                    ],
                    [
                        "campo_nombre" => "cantidad_materia_prima",
                        "campo_marcador" => ":cantidad_materia_prima",
                        "campo_valor" => $mp['cantidad_materia_prima'],
                    ],
                ];

                $ultimoIdMp = $this->guardarDatos('materias_primas_productos', $datos_materia_prima);
                if ($ultimoIdMp == false || $ultimoIdMp <= 0) {
                    $alertaError = [
                        'tipo' => 'simple',
                        'titulo' => 'Materia prima no registrada',
                        'texto' => 'La materia prima no ha podido ser registrada en la Base de Datos',
                        'icono' => 'error',
                    ];
                    $this->rollback();
                    return $alertaError;
                    exit();
                }
            }
        }

        if (!isset($alertaError)) {
            $alerta = [
                "tipo" => "limpiar",
                "titulo" => "Producto registrado",
                "texto" => "El producto ha sido registrado exitosamente",
                "icono" => "success"
            ];
            $bitacoraModelo->registrarBitacora("Productos", "Registrar", "Exito");
            $this->commit();
            return $alerta;
        } else {
            $this->rollback();
            return $alertaError;
        }
    }
    private function actualizarProductosP()
    {   
        $PRD=0; $MPR=0; $PRE=0;

        // Datos del producto
        $instruccionesBD = [
            "tabla" => "productos",
            "datos" => [
                [
                    "campo_nombre" => "id_unidad_medida",
                    "campo_marcador" => ":unidadMedida",
                    "campo_valor" => $this->idUnidadMedida,
                ],
                [
                    "campo_nombre" => "nombre_producto",
                    "campo_marcador" => ":nombre",
                    "campo_valor" => $this->nombreProducto,
                    "ponerEnMayusculas" => true,
                ],
                [
                    "campo_nombre" => "stock_producto",
                    "campo_marcador" => ":stock",
                    "campo_valor" => $this->stockProducto,
                ],
                [
                    "campo_nombre" => "precio_producto_detal",
                    "campo_marcador" => ":precioDetal",
                    "campo_valor" => $this->precioProductoDetal,
                ],
                [
                    "campo_nombre" => "precio_producto_mayor",
                    "campo_marcador" => ":precioMayor",
                    "campo_valor" => $this->precioProductoMayor,
                ],
                [
                    "campo_nombre" => "producto_es_fabricado",
                    "campo_marcador" => ":fabricado",
                    "campo_valor" => $this->fabricadoProducto,
                ],
            ],
            "condiciones" => [
                [
                    "condicion_campo" => "id_producto",
                    "condicion_marcador" => ":id",
                    "condicion_valor" => $this->idProducto,
                    "comparacion" => "=",
                ]
            ]
        ];
        $resultado = $this->actualizarDatos($instruccionesBD);
        if ($resultado != false && $resultado > 0) {
            $PRD=1;
        }

        // Materias Primas
        $this->eliminarDatos("materias_primas_productos", "id_producto", $this->idProducto,true);
        if ($this->fabricadoProducto == 1) {
            foreach ($this->materiasPrimas as $mp) {
                $datos_materia_prima = [
                    [
                        "campo_nombre" => "id_producto",
                        "campo_marcador" => ":id",
                        "campo_valor" => $this->idProducto,
                    ],
                    [
                        "campo_nombre" => "id_materia_prima",
                        "campo_marcador" => ":id_materia_prima",
                        "campo_valor" => $mp['id_materia_prima'],
                    ],
                    [
                        "campo_nombre" => "cantidad_materia_prima",
                        "campo_marcador" => ":cantidad",
                        "campo_valor" => $mp['cantidad_materia_prima'],
                    ],
                ];
                $ultimoIdMp= $this->guardarDatos('materias_primas_productos', $datos_materia_prima);
                if ($ultimoIdMp != false && $ultimoIdMp > 0) {
                    $MPR+=1;
                }
            }
        }

        // Presentaciones
        $this->eliminarDatos("productos_presentaciones", "id_producto", $this->idProducto);
        foreach ($this->presentaciones as $idPresentacion) {
            $datos_presentacion = [
                [
                    "campo_nombre" => "id_producto",
                    "campo_marcador" => ":id",
                    "campo_valor" => $this->idProducto,
                ],
                [
                    "campo_nombre" => "id_presentacion",
                    "campo_marcador" => ":id_presentacion",
                    "campo_valor" => $idPresentacion,
                ],
            ];
            $ultimoIdPr= $this->guardarDatos('productos_presentaciones', $datos_presentacion);
            if ($ultimoIdPr != false && $ultimoIdPr > 0) {
                $PRE+=1;
            }
        }
        $bitacoraModelo = new bitacoraModelo();
        if ($PRD==0&& $PRE==0 && $MPR==0) {
            $this->rollback();
            $alerta=[
                'icono'=>'warning',
                'titulo'=>'Sin Actualizaciones',
                'texto'=>'No se realizaron cambios en el producto',
                'tipo'=>'simple',
            ];
            $bitacoraModelo->registrarBitacora("Productos", "Actualizar", "Fallido");
            $this->rollback();
            return $alerta;
        } else {
            $alerta = [
                "tipo" => "limpiarYcerrar",
                "titulo" => "Producto actualizado",
                "texto" => "El producto ha sido actualizada exitosamente",
                "icono" => "success",
            ];
            $bitacoraModelo->registrarBitacora("Productos", "Actualizar", "Exito");
            $this->commit();
            return $alerta;
        }
    }
    private function eliminarProductosP()
    {
        $producto = $this->seleccionarProductos($this->idProducto);
        $modeloBitacora = new bitacoraModelo();
        if ($producto['producto_es_fabricado'] == 1) {
            $materiasPrimasProducto = $producto['detallesExtra']['materias_primas'];
            foreach ($materiasPrimasProducto as $mp) {
                $sql = "UPDATE materias_primas SET stock_materia_prima = stock_materia_prima + :cantidad WHERE id_materia_prima = :id";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([':cantidad' => $mp['cantidad_materia_prima'], ':id' => $mp['id_materia_prima']]);
            }
        }

        $this->eliminarDatos("productos_presentaciones", "id_producto", $this->idProducto);
        $this->eliminarDatos("materias_primas_productos", "id_producto", $this->idProducto);
        $eliminarProductos = $this->eliminarDatos("productos", "id_producto", $this->idProducto);

        if ($eliminarProductos->rowCount() == 1) {
            $this->commit();
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Producto eliminado",
                "texto" => "El producto ha sido eliminado con éxito",
                "icono" => "success"
            ];
            $modeloBitacora->registrarBitacora("Productos", "Eliminar", "Exito");
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Producto no encontrado",
                "texto" => "El producto no existe en la Base de Datos",
                "icono" => "error"
            ];
            $modeloBitacora->registrarBitacora("Productos", "Eliminar", "Fallido");
        }

        return $alerta;
    }
}
