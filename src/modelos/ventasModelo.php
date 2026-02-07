<?php

namespace src\modelos;

use src\config\connect\conexion;
use PDO;
use Exception;

class ventasModelo extends conexion
{
    private $id_venta;
    private $rif_cedula_cliente;
    private $fecha_venta;
    private $total_venta;
    private $status;

    public function seleccionarVenta($id = null)
    {
        $this->id_venta = $id;
        $instruccionesBD = [
            'campos' => '
                v.id_venta,
                v.rif_cedula_cliente AS CLIENTE,
                v.fecha_venta,
                v.status,
                (SELECT COUNT(*) FROM productos_ventas pv WHERE pv.id_venta = v.id_venta AND pv.status = 1) AS productos,
                (SELECT COUNT(*) FROM servicios_ventas sv WHERE sv.id_venta = v.id_venta AND sv.status = 1) AS servicios
            ',
            'tabla' => 'ventas v',
            'PEL' => 'v',  // Prefijo para Eliminado Lógico
            'ORDER' => 'v.id_venta DESC'
        ];
        if (!empty($this->id_venta)) {
            $instruccionesBD['WHERE'] = [[
                "condicion_campo" => "v.id_venta",
                "condicion_marcador" => ":id",
                "condicion_valor" => $this->id_venta,
                "comparacion" => "="
            ]];
        }
        $resultado = $this->seleccionarDatos($instruccionesBD);
        return $resultado->fetchAll(PDO::FETCH_ASSOC);
    }
    public function registrarVenta($rif_cliente, $total, $productos = [], $servicios = [])
    {
        try {
            $this->rif_cedula_cliente = $rif_cliente;
            $this->total_venta = $total;
            $this->fecha_venta = date('Y-m-d H:i:s');
            $this->status = 1;

            /* ----------  CREAR VENTA ---------- */
            $id_venta = $this->guardarDatos('ventas', [
                ["campo_nombre" => "rif_cedula_cliente", "campo_marcador" => ":rif", "campo_valor" => $this->rif_cedula_cliente],
                ["campo_nombre" => "id_cambio_iva", "campo_marcador" => ":iva", "campo_valor" => 1],
                ["campo_nombre" => "fecha_venta", "campo_marcador" => ":fecha", "campo_valor" => $this->fecha_venta],
                ["campo_nombre" => "total_venta", "campo_marcador" => ":total", "campo_valor" => $this->total_venta],
                ["campo_nombre" => "status", "campo_marcador" => ":status", "campo_valor" => $this->status]
            ]);

            if (!$id_venta || $id_venta <= 0) {
                throw new Exception("No se pudo crear la venta principal");
            }

            /* ---------- PRODUCTOS DIRECTOS ó SOLO PRODUCTOS ---------- */
            if (!empty($productos)) {
                foreach ($productos as $p) {
                    if (!isset($p['id_producto'], $p['cantidad']) || $p['cantidad'] <= 0) {
                        continue;
                    }

                    // INSERT MANUAL
                    $this->conectar();
                    $stmt = $this->conexion->prepare("
                    INSERT INTO productos_ventas (id_producto, id_venta, cantidad_producto, status) 
                    VALUES (:prod, :venta, :cant, 1)
                ");
                    $stmt->execute([
                        ':prod' => $p['id_producto'],
                        ':venta' => $id_venta,
                        ':cant' => $p['cantidad']
                    ]);

                    if ($stmt->rowCount() === 0) {
                        throw new Exception("No se pudo registrar producto ID: " . $p['id_producto']);
                    }

                    // reducir stock
                    $stmt_stock = $this->conexion->prepare("
                        UPDATE productos 
                        SET stock_producto = GREATEST(stock_producto - :cant, 0)
                        WHERE id_producto = :id
                    ");
                    $stmt_stock->execute([
                        ':cant' => $p['cantidad'],
                        ':id' => $p['id_producto']
                    ]);

                    if ($stmt_stock->rowCount() === 0) {
                        throw new Exception("No hay stock suficiente para producto ID: " . $p['id_producto']);
                    }
                }
            }

            /* ---------- 3. SERVICIOS ---------- */
            if (!empty($servicios)) {
                foreach ($servicios as $s) {
                    if (!isset($s['id_servicio'], $s['cantidad'])) {
                        continue;
                    }

                    //  INSERT MANUAL servicios_ventas
                    $this->conectar();
                    $stmt_serv = $this->conexion->prepare("
                    INSERT INTO servicios_ventas (id_servicio, id_venta, cantidad_servicio, status)
                    VALUES (:serv, :venta, :cant, 1)
                ");
                    $stmt_serv->execute([
                        ':serv' => $s['id_servicio'],
                        ':venta' => $id_venta,
                        ':cant' => $s['cantidad']
                    ]);
                    $id_servicio_venta = $this->conexion->lastInsertId();

                    // Productos del servicio
                    if (!empty($s['productos'])) {
                        foreach ($s['productos'] as $ps) {
                            if (!isset($ps['id_producto'], $ps['cantidad'])) {
                                continue;
                            }

                            $stmt_ps = $this->conexion->prepare("
                            INSERT INTO productos_servicios_ventas (id_producto, id_servicio_venta, cantidad_producto, status)
                            VALUES (:prod, :serv_venta, :cant, 1)
                        ");
                            $stmt_ps->execute([
                                ':prod' => $ps['id_producto'],
                                ':serv_venta' => $id_servicio_venta,
                                ':cant' => $ps['cantidad']
                            ]);
                        }
                    }
                }
            }

            $this->commit();

            return [
                "tipo"     => "limpiar",
                "titulo"   => "Venta Registrada",
                "texto"    => "Venta #{$id_venta} registrada correctamente.",
                "icono"    => "success",
                "id_venta" => $id_venta
            ];
        } catch (Exception $e) {
            $this->rollback();
            return [
                "tipo"   => "simple",
                "titulo" => "Error",
                "texto"  => "Error al registrar la venta: " . $e->getMessage(),
                "icono"  => "error"
            ];
        }
    }
    public function actualizarEstadoVenta($id_venta, $estado)
    {
        $this->id_venta = $id_venta;
        $this->status = $estado;

        $resultado = $this->actualizarDatos([
            "tabla" => "ventas",
            "datos" => [[
                "campo_nombre" => "status",
                "campo_marcador" => ":s",
                "campo_valor" => $this->status
            ]],
            "condiciones" => [[
                "condicion_campo" => "id_venta",
                "condicion_marcador" => ":id",
                "condicion_valor" => $this->id_venta,
                "comparacion" => "="
            ]]
        ]);

        if ($resultado <= 0) {
            return [
                "tipo" => "simple",
                "titulo" => "Error",
                "texto" => "No se pudo actualizar el estado",
                "icono" => "warning"
            ];
        }

        return [
            "tipo" => "simple",
            "titulo" => "Estado actualizado",
            "texto" => "Estado de la venta actualizado correctamente",
            "icono" => "success"
        ];
    }
    public function eliminarVenta($id_venta)
    {
        try {
            $this->id_venta = $id_venta;


            $resultado = $this->eliminarDatos('ventas', 'id_venta', $this->id_venta, false);

            if ($resultado->rowCount() == 1) {
                $this->commit();

                return [
                    "tipo" => "simple",
                    "titulo" => "Venta eliminada",
                    "texto" => "La venta fue eliminada correctamente",
                    "icono" => "success"
                ];
            }

            $this->rollback();
            return [
                "tipo" => "simple",
                "titulo" => "Error",
                "texto" => "No se pudo eliminar la venta",
                "icono" => "error"
            ];
        } catch (Exception $e) {
            $this->rollback();
            return [
                "tipo" => "simple",
                "titulo" => "Error",
                "texto" => "Error al eliminar: " . $e->getMessage(),
                "icono" => "error"
            ];
        }
    }
}
