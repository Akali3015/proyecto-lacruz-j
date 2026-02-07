<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use PDO;
use PDOException;
use Exception;

class comprasModelo extends conexion
{
    use traitModelo;

    private $idCompra;
    private $rifProveedor;
    private $cedulaUsuario;
    private $fechaCompra;
    private $detalles;

    public function listarCompras()
    {
        return $this->listarComprasP();
    }
    public function registrarCompra($rifProveedor, $cedulaUsuario, $fechaCompra, $detalles)
    {
        try {
            $this->rifProveedor = $rifProveedor;
            $this->cedulaUsuario = $cedulaUsuario;
            $this->fechaCompra = $fechaCompra;
            $this->detalles = $detalles;

            // Validar proveedor
            $campos = [
                [
                    "campo_nombre" => "rif_proveedor",
                    "campo_valor" => &$this->rifProveedor,
                    "formulario_nombre" => "RIF del proveedor",
                    "requerido" => true,
                    "minimo" => minRegexEnteroGrande,
                    "maximo" => maxRegexEnteroGrande,
                    "expresion_re" => regexEnteroGrande,
                    "tabla" => "proveedores",
                    "debeExistir" => true,
                ],
                [
                    "campo_nombre" => "cedula_usuario",
                    "campo_valor" => &$this->cedulaUsuario,
                    "formulario_nombre" => "Cédula del usuario responsable",
                    "requerido" => true,
                    "minimo" => minRegexEnteroGrande,
                    "maximo" => maxRegexEnteroGrande,
                    "expresion_re" => regexEnteroGrande,
                    "tabla" => "usuarios",
                    "debeExistir" => true,
                ]
            ];

            $respuesta = $this->limpiar_Verificar($campos);
            if ($respuesta !== false) {
                return $respuesta;
            }

            return $this->registrarCompraP();
        } catch (Exception $e) {
            return [
                "tipo" => "simple",
                "titulo" => "Error",
                "texto" => "Error al registrar la compra: " . $e->getMessage(),
                "icono" => "error"
            ];
        }
    }
    // Eliminar compra (eliminación lógica)
    public function eliminarCompra($idCompra)
    {
        try {
            $this->conectar();

            // Eliminación lógica (cambiar status a 0)
            // El 4to parámetro false = eliminación lógica, true = eliminación permanente
            $resultado = $this->eliminarDatosP('compras', 'id_compra', $idCompra, false);

            $this->commit();

            if ($resultado) {
                return [
                    "tipo" => "recargar",
                    "titulo" => "Compra Eliminada",
                    "texto" => "La compra ha sido eliminada correctamente",
                    "icono" => "success"
                ];
            } else {
                return [
                    "tipo" => "simple",
                    "titulo" => "Error",
                    "texto" => "No se pudo eliminar la compra",
                    "icono" => "error"
                ];
            }
        } catch (Exception $e) {
            $this->rollback();
            return [
                "tipo" => "simple",
                "titulo" => "Error",
                "texto" => "Error al eliminar la compra: " . $e->getMessage(),
                "icono" => "error"
            ];
        }
    }
    // Obtener compra por ID
    public function obtenerCompra($idCompra)
    {
        try {
            $this->conectar();

            $instruccionesBD = [
                'campos' => 'c.*, p.razon_social_proveedor',
                'tabla' => 'compras c',
                'PEL' => 'c',
                'datosJoins' => [
                    [
                        'TablaDestino' => 'proveedores p',
                        'conexionLo' => 'c.rif_proveedor = p.rif_proveedor'
                    ]
                ],
                'WHERE' => [
                    [
                        'condicion_campo' => 'c.id_compra',
                        'condicion_marcador' => ':id',
                        'condicion_valor' => $idCompra,
                        'comparacion' => '='
                    ]
                ]
            ];

            $resultado = $this->seleccionarDatos($instruccionesBD);
            $compra = $resultado->fetch();

            if ($compra) {
                return $compra;
            } else {
                return [
                    "tipo" => "simple",
                    "titulo" => "Error",
                    "texto" => "No se encontró la compra",
                    "icono" => "error"
                ];
            }
        } catch (Exception $e) {
            return [
                "tipo" => "simple",
                "titulo" => "Error",
                "texto" => "Error al obtener la compra: " . $e->getMessage(),
                "icono" => "error"
            ];
        }
    }
    // Actualizar compra (solo información general)
    public function actualizarCompra($idCompra, $rifProveedor, $cedulaUsuario, $fechaCompra)
    {
        try {
            $this->conectar();

            // Validar que la compra existe
            $campos = [
                [
                    "campo_nombre" => "id_compra",
                    "campo_valor" => $idCompra,
                    "formulario_nombre" => "ID de compra",
                    "requerido" => true,
                    "minimo" => minRegexId,
                    "maximo" => maxRegexId,
                    "expresion_re" => regexId,
                    "tabla" => "compras",
                    "debeExistir" => true,
                ]
            ];
            $respuesta = $this->limpiar_Verificar($campos);
            if ($respuesta !== false) {
                return $respuesta;
            }

            // Actualizar los datos
            $instrucciones = [
                'tabla' => 'compras',
                'datos' => [
                    ["campo_nombre" => "rif_proveedor", "campo_marcador" => ":rif", "campo_valor" => $rifProveedor],
                    ["campo_nombre" => "cedula_usuario", "campo_marcador" => ":cedula", "campo_valor" => $cedulaUsuario],
                    ["campo_nombre" => "fecha_compra", "campo_marcador" => ":fecha", "campo_valor" => $fechaCompra],
                ],
                'condiciones' => [
                    [
                        "condicion_campo" => "id_compra",
                        "condicion_marcador" => ":id",
                        "condicion_valor" => $idCompra,
                        "comparacion" => "="
                    ]
                ]
            ];

            $resultado = $this->actualizarDatos($instrucciones);

            $this->commit();

            // Registrar en bitácora
            $bitacora = new bitacoraModelo();
            $bitacora->registrarBitacora(
                "Actualización",
                "compras",
                "Se actualizó la compra con ID: " . $idCompra
            );

            return [
                "tipo" => "recargar",
                "titulo" => "Compra Actualizada",
                "texto" => "La compra se actualizó correctamente",
                "icono" => "success"
            ];
        } catch (Exception $e) {
            $this->rollback();
            return [
                "tipo" => "simple",
                "titulo" => "Error",
                "texto" => "Error al actualizar la compra: " . $e->getMessage(),
                "icono" => "error"
            ];
        }
    }
    
    private function listarComprasP()
    {
        // UNION de las 3 tablas de detalles para mostrar todos los items comprados
        $sql = "
            SELECT 
                c.id_compra,
                c.fecha_compra,
                p.razon_social_proveedor,
                'Producto' as tipo,
                prod.nombre_producto as articulo,
                CONCAT(det.cantidad_producto, ' ', COALESCE(um.simbolo_unidad_medida, 'UNID')) as cantidad
            FROM compras c
            JOIN proveedores p ON c.rif_proveedor = p.rif_proveedor
            JOIN productos_compras det ON c.id_compra = det.id_compra
            JOIN productos prod ON det.id_producto = prod.id_producto
            LEFT JOIN unidades_medidas um ON prod.id_unidad_medida = um.id_unidad_medida
            WHERE c.status = 1

            UNION ALL

            SELECT 
                c.id_compra,
                c.fecha_compra,
                p.razon_social_proveedor,
                'Insumo' as tipo,
                i.nombre_insumo as articulo,
                CONCAT(det.cantidad_insumo, ' UNID') as cantidad
            FROM compras c
            JOIN proveedores p ON c.rif_proveedor = p.rif_proveedor
            JOIN insumos_compras det ON c.id_compra = det.id_compra
            JOIN insumos i ON det.id_insumo = i.id_insumo
            WHERE c.status = 1

            UNION ALL

            SELECT 
                c.id_compra,
                c.fecha_compra,
                p.razon_social_proveedor,
                'Materia Prima' as tipo,
                mp.nombre_materia_prima as articulo,
                CONCAT(det.cantidad_materia_prima, ' ', COALESCE(um.simbolo_unidad_medida, 'UNID')) as cantidad
            FROM compras c
            JOIN proveedores p ON c.rif_proveedor = p.rif_proveedor
            JOIN materias_primas_compras det ON c.id_compra = det.id_compra
            JOIN materias_primas mp ON det.id_materia_prima = mp.id_materia_prima
            LEFT JOIN unidades_medidas um ON mp.id_unidad_medida = um.id_unidad_medida
            WHERE c.status = 1
            
            ORDER BY fecha_compra DESC
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [
                'error'=>$e
            ];
        }
    }
    private function registrarCompraP()
    {
        try {

            // 1. Registrar la compra (cabecera)
            $datos_compra = [
                ["campo_nombre" => "rif_proveedor", "campo_marcador" => ":rif", "campo_valor" => $this->rifProveedor],
                ["campo_nombre" => "fecha_compra", "campo_marcador" => ":fecha", "campo_valor" => $this->fechaCompra],
            ];

            $idCompra = $this->guardarDatos('compras', $datos_compra);

            error_log("ID Compra devuelto: " . var_export($idCompra, true));
            error_log("Datos compra: " . json_encode($datos_compra));

            if (!$idCompra) {
                throw new Exception("Error al registrar la compra");
            }

            // 2. Procesar cada detalle
            foreach ($this->detalles as $detalle) {
                $tipo = $detalle['tipo'];
                $idItem = $detalle['id'];
                $cantidad = $detalle['cantidad'];
                $idUnidadMedida = $detalle['id_unidad_medida'] ?? null;

                // Validar cantidad
                if (!preg_match('/' . regexCantidadItem . '/', $cantidad)) {
                    throw new Exception("Cantidad inválida");
                }

                // Registrar en la tabla de detalles correspondiente y actualizar stock
                switch ($tipo) {
                    case 'producto':
                        // Validar que el producto existe
                        $campos_producto = [
                            [
                                "campo_nombre" => "id_producto",
                                "campo_valor" => $idItem,
                                "formulario_nombre" => "ID del producto",
                                "requerido" => true,
                                "minimo" => minRegexId,
                                "maximo" => maxRegexId,
                                "expresion_re" => regexId,
                                "tabla" => "productos",
                                "debeExistir" => true,
                            ]
                        ];
                        $respuesta = $this->limpiar_Verificar($campos_producto);
                        if ($respuesta !== false) {
                            throw new Exception($respuesta['texto']);
                        }

                        // Insertar detalle
                        $datos_detalle = [
                            ["campo_nombre" => "id_compra", "campo_marcador" => ":id_compra", "campo_valor" => $idCompra],
                            ["campo_nombre" => "id_producto", "campo_marcador" => ":id_producto", "campo_valor" => $idItem],
                            ["campo_nombre" => "cantidad_producto", "campo_marcador" => ":cantidad", "campo_valor" => $cantidad],
                            ["campo_nombre" => "id_unidad_medida", "campo_marcador" => ":id_unidad_medida", "campo_valor" => $idUnidadMedida],
                        ];
                        $this->guardarDatos('productos_compras', $datos_detalle);

                        // Actualizar stock
                        $this->actualizarStock('productos', 'id_producto', 'stock_producto', $idItem, $cantidad);
                        break;

                    case 'insumo':
                        // Validar que el insumo existe
                        $campos_insumo = [
                            [
                                "campo_nombre" => "id_insumo",
                                "campo_valor" => $idItem,
                                "formulario_nombre" => "ID del insumo",
                                "requerido" => true,
                                "minimo" => minRegexId,
                                "maximo" => maxRegexId,
                                "expresion_re" => regexId,
                                "tabla" => "insumos",
                                "debeExistir" => true,
                            ]
                        ];
                        $respuesta = $this->limpiar_Verificar($campos_insumo);
                        if ($respuesta !== false) {
                            throw new Exception($respuesta['texto']);
                        }

                        // Insertar detalle
                        $datos_detalle = [
                            ["campo_nombre" => "id_compra", "campo_marcador" => ":id_compra", "campo_valor" => $idCompra],
                            ["campo_nombre" => "id_insumo", "campo_marcador" => ":id_insumo", "campo_valor" => $idItem],
                            ["campo_nombre" => "cantidad_insumo", "campo_marcador" => ":cantidad", "campo_valor" => $cantidad],
                            ["campo_nombre" => "id_unidad_medida", "campo_marcador" => ":id_unidad_medida", "campo_valor" => $idUnidadMedida],
                        ];
                        $this->guardarDatos('insumos_compras', $datos_detalle);

                        // Actualizar stock
                        $this->actualizarStock('insumos', 'id_insumo', 'stock_insumo', $idItem, $cantidad);
                        break;

                    case 'materia_prima':
                        // Validar que la materia prima existe
                        $campos_mp = [
                            [
                                "campo_nombre" => "id_materia_prima",
                                "campo_valor" => $idItem,
                                "formulario_nombre" => "ID de la materia prima",
                                "requerido" => true,
                                "minimo" => minRegexId,
                                "maximo" => maxRegexId,
                                "expresion_re" => regexId,
                                "tabla" => "materias_primas",
                                "debeExistir" => true,
                            ]
                        ];
                        $respuesta = $this->limpiar_Verificar($campos_mp);
                        if ($respuesta !== false) {
                            throw new Exception($respuesta['texto']);
                        }

                        // Insertar detalle
                        $datos_detalle = [
                            ["campo_nombre" => "id_compra", "campo_marcador" => ":id_compra", "campo_valor" => $idCompra],
                            ["campo_nombre" => "id_materia_prima", "campo_marcador" => ":id_materia_prima", "campo_valor" => $idItem],
                            ["campo_nombre" => "cantidad_materia_prima", "campo_marcador" => ":cantidad", "campo_valor" => $cantidad],
                            ["campo_nombre" => "id_unidad_medida", "campo_marcador" => ":id_unidad_medida", "campo_valor" => $idUnidadMedida],
                        ];
                        $this->guardarDatos('materias_primas_compras', $datos_detalle);

                        // Actualizar stock
                        $this->actualizarStock('materias_primas', 'id_materia_prima', 'stock_materia_prima', $idItem, $cantidad);
                        break;

                    default:
                        throw new Exception("Tipo de inventario inválido");
                }
            }

            // 3. Registrar en bitácora
            $bitacora = new bitacoraModelo();
            $bitacora->registrarBitacora(
                "Registro",
                "compras",
                "Se registró una compra con ID: " . $idCompra
            );

            // 4. IMPORTANTE: Hacer commit de la transacción para persistir los datos
            $this->commit();

            return [
                "tipo" => "recargar",
                "titulo" => "Compra Registrada",
                "texto" => "La compra se registró correctamente y el stock fue actualizado",
                "icono" => "success"
            ];
        } catch (Exception $e) {
            return [
                "tipo" => "simple",
                "titulo" => "Error",
                "texto" => "Error al registrar la compra: " . $e->getMessage(),
                "icono" => "error"
            ];
        }
    }
    private function actualizarStock($tabla, $campoId, $campoStock, $id, $cantidad)
    {
        $sql = "UPDATE $tabla SET $campoStock = $campoStock + :cantidad WHERE $campoId = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':cantidad', $cantidad);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
}
