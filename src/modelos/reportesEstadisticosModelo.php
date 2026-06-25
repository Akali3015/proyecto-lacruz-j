<?php

namespace src\modelos;

use src\config\connect\conexion;

class reportesEstadisticosModelo extends conexion {
  use traitModelo;

  public function obtenerDatosDashboard(array $datos) {
    $this->conectar();
    
    $filtros = $this->construirFiltrosDeFecha($datos);

    try {
      return [
        'tipo' => 'datos',
        'datos' => [
          'topProductos' => $this->getTopProductos($filtros['ventas']),
          'topServicios' => $this->getTopServicios($filtros['ventas']),
          'topClientes' => $this->getTopClientes($filtros['ventas']),
          'ingresosEgresos' => $this->getIngresosEgresosMensuales(),
          'productosVsServicios' => $this->getProductosVsServicios($filtros['ventas']),
          'ventasPorDia' => $this->getVentasPorDiaSemana($filtros['ventas']),
          'topProveedores' => $this->getTopProveedores($filtros['compras']),
          'historialProduccion' => $this->getHistorialProduccion($filtros['produccion']),
          'cuentasPorCobrar' => $this->getCuentasPorCobrar(),
          'consumoMateriasPrimas' => $this->getTopMateriasPrimas($filtros['compras']),
          'actividadReciente' => $this->getActividadReciente(),
          'kpis' => $this->getKpis($filtros)
        ]
      ];
    } catch (\Throwable $th) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Error al cargar estadísticas',
        'texto' => 'Hubo un problema al procesar los datos: ' . $th->getMessage(),
        'icono' => 'error'
      ];
    }
  }

  private function construirFiltrosDeFecha($datos) {
    $filtros = [
        'ventas' => "AND o.fecha_orden_entrega_presupuesto >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)",
        'compras' => "AND c.fecha_compra >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)",
        'produccion' => "AND p.fecha_produccion >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)"
    ];

    if (isset($datos['rango'])) {
        if ($datos['rango'] === 'ultimos_3_meses') {
            $filtros['ventas'] = "AND o.fecha_orden_entrega_presupuesto >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
            $filtros['compras'] = "AND c.fecha_compra >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
            $filtros['produccion'] = "AND p.fecha_produccion >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
        } elseif ($datos['rango'] === 'personalizado' && !empty($datos['fecha_inicio']) && !empty($datos['fecha_fin'])) {
            // Validar formato de fecha seguro (YYYY-MM-DD) para evitar inyecciones SQL
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $datos['fecha_inicio']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $datos['fecha_fin'])) {
              $inicio = $datos['fecha_inicio'];
              $fin = $datos['fecha_fin'] . ' 23:59:59';
              $filtros['ventas'] = "AND o.fecha_orden_entrega_presupuesto BETWEEN '$inicio' AND '$fin'";
              $filtros['compras'] = "AND c.fecha_compra BETWEEN '$inicio' AND '$fin'";
              $filtros['produccion'] = "AND p.fecha_produccion BETWEEN '$inicio' AND '$fin'";
            }
        }
    }
    return $filtros;
  }

  private function formatChartData($data, $labelKey, $valueKey) {
    if (!$data || empty($data)) return ['labels' => [], 'data' => []];
    $labels = [];
    $values = [];
    foreach ($data as $row) {
        $labels[] = $row[$labelKey];
        $values[] = $row[$valueKey];
    }
    return ['labels' => $labels, 'data' => $values];
  }

  private function getKpis($filtros) {
    // Total Clientes (Ya que no existe fecha_registro)
    $sql1 = "SELECT COUNT(rif_cedula_cliente) FROM clientes";
    // Producciones realizadas
    $fProd = $filtros['produccion'];
    $sql2 = "SELECT COUNT(id_produccion) FROM producciones p WHERE p.status = 1 $fProd";
    // Pedidos Pendientes
    $sql3 = "SELECT COUNT(id_orden_entrega_presupuesto) FROM ordenes_entregas_presupuestos WHERE status = 5";

    return [
      'nuevosClientes' => self::$conexion->query($sql1)->fetchColumn() ?: 0,
      'producciones' => self::$conexion->query($sql2)->fetchColumn() ?: 0,
      'pedidosPendientes' => self::$conexion->query($sql3)->fetchColumn() ?: 0
    ];
  }

  private function getActividadReciente() {
    $sql = "(SELECT 'Venta' as tipo, id_orden_entrega_presupuesto as id, c.razon_social_cliente as referencia, fecha_orden_entrega_presupuesto as fecha 
             FROM ordenes_entregas_presupuestos o 
             INNER JOIN clientes c ON o.rif_cedula_cliente = c.rif_cedula_cliente 
             WHERE o.status IN (7,8) ORDER BY fecha DESC LIMIT 3)
            UNION ALL
            (SELECT 'Compra' as tipo, id_compra as id, pr.razon_social_proveedor as referencia, fecha_compra as fecha 
             FROM compras co 
             INNER JOIN proveedores pr ON co.rif_proveedor = pr.rif_proveedor 
             WHERE co.status = 1 ORDER BY fecha DESC LIMIT 3)
            ORDER BY fecha DESC LIMIT 5";
    $stmt = self::$conexion->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
  }

  private function getTopServicios($fechaFiltro) {
    $sql = "SELECT s.nombre_servicio as nombre, COUNT(sof.id_servicio_factura) as total_vendido 
            FROM servicios_ordenes_entregas_presupuestos sof
            INNER JOIN ordenes_entregas_presupuestos o ON sof.id_orden_entrega_presupuesto = o.id_orden_entrega_presupuesto
            INNER JOIN servicios s ON sof.id_servicio = s.id_servicio
            WHERE o.status IN (7,8) AND sof.status = 1 $fechaFiltro
            GROUP BY s.id_servicio 
            ORDER BY total_vendido DESC LIMIT 5";
    $stmt = self::$conexion->prepare($sql);
    $stmt->execute();
    return $this->formatChartData($stmt->fetchAll(\PDO::FETCH_ASSOC), 'nombre', 'total_vendido');
  }

  private function getTopProductos($fechaFiltro) {
    $sql = "SELECT pr.nombre_producto as nombre, SUM(pof.cantidad_producto) as total_vendido 
            FROM productos_ordenes_entregas_presupuestos pof
            INNER JOIN ordenes_entregas_presupuestos o ON pof.id_orden_entrega_presupuesto = o.id_orden_entrega_presupuesto
            INNER JOIN presentaciones_productos pp ON pof.id_presentacion_producto = pp.id_presentacion_producto
            INNER JOIN productos pr ON pp.id_producto = pr.id_producto
            WHERE o.status IN (7,8) AND pof.status = 1 $fechaFiltro
            GROUP BY pr.id_producto 
            ORDER BY total_vendido DESC LIMIT 5";
    $stmt = self::$conexion->prepare($sql);
    $stmt->execute();
    return $this->formatChartData($stmt->fetchAll(\PDO::FETCH_ASSOC), 'nombre', 'total_vendido');
  }

  private function getTopClientes($fechaFiltro) {
    $sql = "SELECT c.razon_social_cliente as nombre, COUNT(o.id_orden_entrega_presupuesto) as total_pedidos
            FROM ordenes_entregas_presupuestos o
            INNER JOIN clientes c ON o.rif_cedula_cliente = c.rif_cedula_cliente
            WHERE o.status IN (7,8) $fechaFiltro
            GROUP BY c.rif_cedula_cliente
            ORDER BY total_pedidos DESC LIMIT 5";
    $stmt = self::$conexion->prepare($sql);
    $stmt->execute();
    return $this->formatChartData($stmt->fetchAll(\PDO::FETCH_ASSOC), 'nombre', 'total_pedidos');
  }

  private function getIngresosEgresosMensuales() {
    $sql = "SELECT DATE_FORMAT(o.fecha_orden_entrega_presupuesto, '%Y-%m') as mes, COUNT(o.id_orden_entrega_presupuesto) as ingresos_cant
            FROM ordenes_entregas_presupuestos o
            WHERE o.status IN (7,8) AND o.fecha_orden_entrega_presupuesto >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY mes ORDER BY mes ASC";
    $stmt = self::$conexion->prepare($sql);
    $stmt->execute();
    $ingresos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    $sql2 = "SELECT DATE_FORMAT(c.fecha_compra, '%Y-%m') as mes, COUNT(c.id_compra) as egresos_cant
             FROM compras c
             WHERE c.status = 1 AND c.fecha_compra >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
             GROUP BY mes ORDER BY mes ASC";
    $stmt2 = self::$conexion->prepare($sql2);
    $stmt2->execute();
    $egresos = $stmt2->fetchAll(\PDO::FETCH_ASSOC);
    
    // Unificar en formato amigable para el frontend
    $meses = [];
    foreach (array_merge(array_column($ingresos, 'mes'), array_column($egresos, 'mes')) as $m) {
        $meses[$m] = true;
    }
    $meses = array_keys($meses);
    sort($meses);

    $ingresosData = [];
    $egresosData = [];
    foreach ($meses as $mes) {
        $keyIn = array_search($mes, array_column($ingresos, 'mes'));
        $ingresosData[] = $keyIn !== false ? $ingresos[$keyIn]['ingresos_cant'] : 0;
        
        $keyEg = array_search($mes, array_column($egresos, 'mes'));
        $egresosData[] = $keyEg !== false ? $egresos[$keyEg]['egresos_cant'] : 0;
    }

    if (empty($meses)) {
      return ['fechas' => [], 'ingresos' => [], 'egresos' => []];
    }

    return [
      'fechas' => $meses,
      'ingresos' => $ingresosData,
      'egresos' => $egresosData
    ];
  }

  private function getProductosVsServicios($fechaFiltro) {
    $sql1 = "SELECT COUNT(pof.id_producto_factura) as cant 
             FROM productos_ordenes_entregas_presupuestos pof
             INNER JOIN ordenes_entregas_presupuestos o ON pof.id_orden_entrega_presupuesto = o.id_orden_entrega_presupuesto
             WHERE o.status IN (7,8) $fechaFiltro";
    
    $sql2 = "SELECT COUNT(sof.id_servicio_factura) as cant 
             FROM servicios_ordenes_entregas_presupuestos sof
             INNER JOIN ordenes_entregas_presupuestos o ON sof.id_orden_entrega_presupuesto = o.id_orden_entrega_presupuesto
             WHERE o.status IN (7,8) $fechaFiltro";
             
    $stmt1 = self::$conexion->query($sql1);
    $stmt2 = self::$conexion->query($sql2);
    
    $p = $stmt1->fetchColumn() ?: 0;
    $s = $stmt2->fetchColumn() ?: 0;

    return [
      'productos' => $p,
      'servicios' => $s,
      'vacio' => ($p == 0 && $s == 0) // Bandera para indicar si está completamente vacío
    ];
  }

  private function getVentasPorDiaSemana($fechaFiltro) {
    $dias = [
      'Monday' => 'Lunes', 
      'Tuesday' => 'Martes', 
      'Wednesday' => 'Miércoles', 
      'Thursday' => 'Jueves', 
      'Friday' => 'Viernes', 
      'Saturday' => 'Sábado', 
      'Sunday' => 'Domingo'
    ];
    $dataMap = array_fill_keys(array_values($dias), 0);

    $sql = "SELECT DAYNAME(o.fecha_orden_entrega_presupuesto) as dia_en, COUNT(o.id_orden_entrega_presupuesto) as total
            FROM ordenes_entregas_presupuestos o
            WHERE o.status IN (7,8) $fechaFiltro
            GROUP BY dia_en";
    
    $stmt = self::$conexion->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    foreach ($rows as $row) {
      if (isset($dias[$row['dia_en']])) {
        $dataMap[$dias[$row['dia_en']]] = (int)$row['total'];
      }
    }

    return [
      'labels' => array_keys($dataMap),
      'data' => array_values($dataMap)
    ];
  }

  private function getTopProveedores($fechaFiltroCompras) {
    $sql = "SELECT pr.razon_social_proveedor as nombre, COUNT(c.id_compra) as total_compras
            FROM compras c
            INNER JOIN proveedores pr ON c.rif_proveedor = pr.rif_proveedor
            WHERE c.status = 1 $fechaFiltroCompras
            GROUP BY pr.rif_proveedor
            ORDER BY total_compras DESC LIMIT 5";
    $stmt = self::$conexion->prepare($sql);
    $stmt->execute();
    return $this->formatChartData($stmt->fetchAll(\PDO::FETCH_ASSOC), 'nombre', 'total_compras');
  }

  private function getHistorialProduccion($fechaFiltroProd) {
    $sql = "SELECT DATE_FORMAT(p.fecha_produccion, '%Y-%m-%d') as fecha, SUM(pp.cantidad_producida) as cantidad
            FROM producciones p
            INNER JOIN productos_producciones pp ON p.id_produccion = pp.id_produccion
            WHERE p.status = 1 $fechaFiltroProd
            GROUP BY fecha
            ORDER BY fecha ASC LIMIT 30";
    $stmt = self::$conexion->prepare($sql);
    $stmt->execute();
    return $this->formatChartData($stmt->fetchAll(\PDO::FETCH_ASSOC), 'fecha', 'cantidad');
  }

  private function getCuentasPorCobrar() {
    $sql = "SELECT COUNT(o.id_orden_entrega_presupuesto) as total_pendientes
            FROM ordenes_entregas_presupuestos o
            WHERE o.status = 5";
    $stmt = self::$conexion->query($sql);
    $pendientes = $stmt->fetchColumn() ?: 0;

    $sql2 = "SELECT COUNT(o.id_orden_entrega_presupuesto) as total_pagadas
             FROM ordenes_entregas_presupuestos o
             WHERE o.status IN (7,8)";
    $stmt2 = self::$conexion->query($sql2);
    $pagadas = $stmt2->fetchColumn() ?: 0;

    return ['pendientes' => $pendientes, 'pagadas' => $pagadas, 'vacio' => ($pendientes == 0 && $pagadas == 0)];
  }

  private function getTopMateriasPrimas($fechaFiltroCompras) {
    $sql = "SELECT mp.nombre_materia_prima as nombre, SUM(mpc.cantidad_materia_prima) as cantidad
            FROM materias_primas_compras mpc
            INNER JOIN compras c ON mpc.id_compra = c.id_compra
            INNER JOIN materias_primas mp ON mpc.id_materia_prima = mp.id_materia_prima
            WHERE c.status = 1 $fechaFiltroCompras
            GROUP BY mp.id_materia_prima
            ORDER BY cantidad DESC LIMIT 5";
    $stmt = self::$conexion->prepare($sql);
    $stmt->execute();
    return $this->formatChartData($stmt->fetchAll(\PDO::FETCH_ASSOC), 'nombre', 'cantidad');
  }
}
