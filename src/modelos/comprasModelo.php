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

    public function listarCompras($tipo)
    {
        if($tipo=='completo'){

        }
        return $this->listarComprasP($tipo);
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

    private function listarComprasP($tipo)
    {
        if($tipo=='completo'){
            // todos los datos de la BD
        }elseif(){

        }
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
            return [];
        }
    }

    private function registrarCompraP()
    {
        try {

            // 1. Registrar la compra (cabecera)
            $datos_compra = [
                ["campo_nombre" => "rif_proveedor", "campo_marcador" => ":rif", "campo_valor" => $this->rifProveedor],
                ["campo_nombre" => "cedula_usuario", "campo_marcador" => ":cedula", "campo_valor" => $this->cedulaUsuario],
                ["campo_nombre" => "fecha_compra", "campo_marcador" => ":fecha", "campo_valor" => $this->fechaCompra],
            ];

            $idCompra = $this->guardarDatos('compras', $datos_compra);

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

    // Eliminar compra (eliminación lógica)
    public function eliminarCompra($idCompra)
    {
        return $this->eliminarCompraP($idCompra);
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

    /**
     * Inserta una nueva compra en la base de datos
     * 
     * @param string $rifProveedor
     * @param int $cedulaUsuario
     * @param string $fechaCompra
     * @return int ID de la compra insertada
     */
    public function insertarCompra($rifProveedor, $cedulaUsuario, $fechaCompra)
    {
        return $this->insertarCompraP($rifProveedor, $cedulaUsuario, $fechaCompra);
    }

    /**
     * Inserta un detalle de producto en la compra
     */
    public function insertarDetalleProducto($idCompra, $idProducto, $cantidad, $idUnidadMedida)
    {
        return $this->insertarDetalleProductoP($idCompra, $idProducto, $cantidad, $idUnidadMedida);
    }

    /**
     * Inserta un detalle de insumo en la compra
     */
    public function insertarDetalleInsumo($idCompra, $idInsumo, $cantidad, $idUnidadMedida)
    {
        return $this->insertarDetalleInsumoP($idCompra, $idInsumo, $cantidad, $idUnidadMedida);
    }

    /**
     * Inserta un detalle de materia prima en la compra
     */
    public function insertarDetalleMateriaPrima($idCompra, $idMateriaPrima, $cantidad, $idUnidadMedida)
    {
        return $this->insertarDetalleMateriaPrimaP($idCompra, $idMateriaPrima, $cantidad, $idUnidadMedida);
    }

    /**
     * Incrementa el stock de un producto
     */
    public function incrementarStockProducto($idProducto, $cantidad)
    {
        return $this->incrementarStockProductoP($idProducto, $cantidad);
    }

    /**
     * Incrementa el stock de un insumo
     */
    public function incrementarStockInsumo($idInsumo, $cantidad)
    {
        return $this->incrementarStockInsumoP($idInsumo, $cantidad);
    }

    /**
     * Incrementa el stock de una materia prima
     */
    public function incrementarStockMateriaPrima($idMateriaPrima, $cantidad)
    {
        return $this->incrementarStockMateriaPrimaP($idMateriaPrima, $cantidad);
    }

    /**
     * Inicia una transacción
     */
    public function iniciarTransaccion()
    {
        $this->iniciarTransaccionP();
    }

    /**
     * Confirma una transacción
     */
    public function confirmarTransaccion()
    {
        $this->confirmarTransaccionP();
    }

    /**
     * Revierte una transacción
     */
    public function revertirTransaccion()
    {
        $this->revertirTransaccionP();
    }

    // ==========================================
    // MÉTODOS PRIVADOS CON INTERACCIÓN BD
    // ==========================================

    /**
     * Inserta una nueva compra en la base de datos (operación BD)
     */
    private function insertarCompraP($rifProveedor, $cedulaUsuario, $fechaCompra)
    {
        $this->conectar();

        $datos = [
            ["campo_nombre" => "rif_proveedor", "campo_marcador" => ":rif", "campo_valor" => $rifProveedor],
            ["campo_nombre" => "cedula_usuario", "campo_marcador" => ":cedula", "campo_valor" => $cedulaUsuario],
            ["campo_nombre" => "fecha_compra", "campo_marcador" => ":fecha", "campo_valor" => $fechaCompra],
        ];

        return $this->guardarDatos('compras', $datos);
    }

    /**
     * Inserta un detalle de producto (operación BD)
     */
    private function insertarDetalleProductoP($idCompra, $idProducto, $cantidad, $idUnidadMedida)
    {
        $datos = [
            ["campo_nombre" => "id_compra", "campo_marcador" => ":id_compra", "campo_valor" => $idCompra],
            ["campo_nombre" => "id_producto", "campo_marcador" => ":id_producto", "campo_valor" => $idProducto],
            ["campo_nombre" => "cantidad_producto", "campo_marcador" => ":cantidad", "campo_valor" => $cantidad],
            ["campo_nombre" => "id_unidad_medida", "campo_marcador" => ":id_unidad_medida", "campo_valor" => $idUnidadMedida],
        ];

        return $this->guardarDatos('productos_compras', $datos);
    }

    /**
     * Inserta un detalle de insumo (operación BD)
     */
    private function insertarDetalleInsumoP($idCompra, $idInsumo, $cantidad, $idUnidadMedida)
    {
        $datos = [
            ["campo_nombre" => "id_compra", "campo_marcador" => ":id_compra", "campo_valor" => $idCompra],
            ["campo_nombre" => "id_insumo", "campo_marcador" => ":id_insumo", "campo_valor" => $idInsumo],
            ["campo_nombre" => "cantidad_insumo", "campo_marcador" => ":cantidad", "campo_valor" => $cantidad],
            ["campo_nombre" => "id_unidad_medida", "campo_marcador" => ":id_unidad_medida", "campo_valor" => $idUnidadMedida],
        ];

        return $this->guardarDatos('insumos_compras', $datos);
    }

    /**
     * Inserta un detalle de materia prima (operación BD)
     */
    private function insertarDetalleMateriaPrimaP($idCompra, $idMateriaPrima, $cantidad, $idUnidadMedida)
    {
        $datos = [
            ["campo_nombre" => "id_compra", "campo_marcador" => ":id_compra", "campo_valor" => $idCompra],
            ["campo_nombre" => "id_materia_prima", "campo_marcador" => ":id_materia_prima", "campo_valor" => $idMateriaPrima],
            ["campo_nombre" => "cantidad_materia_prima", "campo_marcador" => ":cantidad", "campo_valor" => $cantidad],
            ["campo_nombre" => "id_unidad_medida", "campo_marcador" => ":id_unidad_medida", "campo_valor" => $idUnidadMedida],
        ];

        return $this->guardarDatos('materias_primas_compras', $datos);
    }

    /**
     * Incrementa el stock de un producto (operación BD)
     */
    private function incrementarStockProductoP($idProducto, $cantidad)
    {
        return $this->actualizarStock('productos', 'id_producto', 'stock_producto', $idProducto, $cantidad);
    }

    /**
     * Incrementa el stock de un insumo (operación BD)
     */
    private function incrementarStockInsumoP($idInsumo, $cantidad)
    {
        return $this->actualizarStock('insumos', 'id_insumo', 'stock_insumo', $idInsumo, $cantidad);
    }

    /**
     * Incrementa el stock de una materia prima (operación BD)
     */
    private function incrementarStockMateriaPrimaP($idMateriaPrima, $cantidad)
    {
        return $this->actualizarStock('materias_primas', 'id_materia_prima', 'stock_materia_prima', $idMateriaPrima, $cantidad);
    }

    /**
     * Inicia una transacción (operación BD)
     */
    private function iniciarTransaccionP()
    {
        $this->conectar();
        // La conexión ya inicia transacción automáticamente en conectarP()
    }

    /**
     * Confirma una transacción (operación BD)
     */
    private function confirmarTransaccionP()
    {
        $this->commit();
    }

    /**
     * Revierte una transacción (operación BD)
     */
    private function revertirTransaccionP()
    {
        $this->rollback();
    }

    /**
     * Elimina una compra (eliminación lógica - operación BD)
     */
    private function eliminarCompraP($idCompra)
    {
        $this->conectar();

        // Eliminación lógica (cambiar status a 0)
        // El 4to parámetro false = eliminación lógica, true = eliminación permanente
        $resultado = $this->eliminarDatosP('compras', 'id_compra', $idCompra, false);

        return $resultado;
    }
}
