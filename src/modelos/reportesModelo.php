<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\pdfModel;

class reportesModelo extends conexion {
  
  private array $filtros = [];

  public function reporteVentas(array $filtros) {
    if ($filtros['fecha_desde'] == '' || $filtros['fecha_hasta'] == '') {
      return [
        'tipo' => 'simple',
        'icono' => 'error',
        'titulo' => 'Sin fecha específicada',
        'texto' => 'Debe agregar una fecha de inicio y fin para el reporte'
      ];
    }
    $this->filtros['fecha_desde'] = $this->FechaHora_Sel("fecha_BD", $filtros['fecha_desde']);
    $this->filtros['fecha_hasta'] = $this->FechaHora_Sel("fecha_BD", $filtros['fecha_hasta']);
    if ($this->filtros['fecha_desde'] > $this->filtros['fecha_hasta']) {
      return [
        'tipo' => 'simple',
        'icono' => 'error',
        'titulo' => 'Fecha de inicio mayor a la fecha de fin',
        'texto' => 'La fecha de inicio del reporte debe ser menor a la de fin'
      ];
    }

    return $this->reporteVentasP();
  }
  public function reporteCompras(array $filtros) {
    if ($filtros['fecha_desde'] == '' || $filtros['fecha_hasta'] == '') {
      return [
        'tipo' => 'simple',
        'icono' => 'error',
        'titulo' => 'Sin fecha específicada',
        'texto' => 'Debe agregar una fecha de inicio y fin para el reporte'
      ];
    }
    $this->filtros['fecha_desde'] = $this->FechaHora_Sel("fecha_BD", $filtros['fecha_desde']);
    $this->filtros['fecha_hasta'] = $this->FechaHora_Sel("fecha_BD", $filtros['fecha_hasta']);
    if ($this->filtros['fecha_desde'] > $this->filtros['fecha_hasta']) {
      return [
        'tipo' => 'simple',
        'icono' => 'error',
        'titulo' => 'Fecha de inicio mayor a la fecha de fin',
        'texto' => 'La fecha de inicio del reporte debe ser menor a la de fin'
      ];
    }

    return $this->reporteComprasP();
  }
  public function reporteCierre(array $filtros) {
    if (($filtros['fecha_cierre'] == '')) {
      return [
        'tipo' => 'simple',
        'icono' => 'error',
        'titulo' => 'Sin fecha específicada',
        'texto' => 'Debe agregar una fecha de cierre para el reporte'
      ];
    }

    $this->filtros['fecha_cierre'] = $this->FechaHora_Sel("fecha_BD", $filtros['fecha_cierre']);

    // Validar fecha
    $fecha_cierre = date('Y-m-d', strtotime($this->filtros['fecha_cierre']));
    $fecha_actual = date('Y-m-d');

    if ($fecha_cierre > $fecha_actual) {
      return [
        'tipo' => 'simple',
        'icono' => 'error',
        'titulo' => 'Fecha de cierre es mayor a la fecha actual',
        'texto' => 'La fecha de cierre del reporte debe ser menor o igual a la fecha actual'
      ];
    }
    return $this->reporteCierreP();
  }
  public function reporteServicios() {
    $instruccionesDB = [
      'tabla' => 'servicios as s',
      'campos' => '
        s.id_servicio, s.nombre_servicio, s.precio_servicio
      ',
      'ORDER' => 's.nombre_servicio ASC',
    ];
    $infoCeldas = $this->seleccionarDatos2($instruccionesDB)->fetchAll();

    if ($infoCeldas == []) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Sin registros existentes',
        'texto' => 'No hay registros dentro de la Base de Datos',
        'icono' => 'warning',
      ];
    }

    //Modificamos la fecha a formato AM/PM  
    foreach ($infoCeldas as &$fila) {
      $fila['precio_servicio'] .= ' Bs';
    }
    unset($fila);
    //Creación del PDF
    $objetoPDF = new pdfModel();
    $objetoPDF->SetTitle('REPORTE DE SERVICIOS');
    return $objetoPDF->crearPDF([
      "tituloReporte" => "REPORTE DE SERVICIOS",
      "configColumnas" => [
        'id_servicio' => ['CÓDIGO', 60],
        'nombre_servicio' => ['SERVICIO', 80],
        'precio_servicio' => ['PRECIO', 40],
      ],
      "infoBD" => $infoCeldas,
    ]);
  }
  public function reporteProductos() {
    $instruccionesDB = [
      'tabla' => 'productos as p',
      'campos' => '
        p.id_producto, p.nombre_producto, p.precio_producto, p.stock_producto, cp.nombre_categoria_producto
      ',
      'datosJoins' => [
        'categorias_productos AS cp' => 'p.id_categoria_producto = cp.id_categoria_producto',
      ],
      'ORDER' => 'cp.id_categoria_producto, p.nombre_producto  ASC',
    ];
    $infoCeldas = $this->seleccionarDatos2($instruccionesDB)->fetchAll();
    if ($infoCeldas == []) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Sin regisros existentes',
        'texto' => 'No hay registros dentro de la Base de Datos',
        'icono' => 'warning',
      ];
    }

    //Modificamos la fecha a formato AM/PM  
    foreach ($infoCeldas as &$fila) {
      $fila['precio_producto'] .= ' Bs';
    }
    unset($fila);

    //Creación del PDF
    $objetoPDF = new pdfModel();
    $objetoPDF->SetTitle('REPORTE DE PRODUCTOS');
    return $objetoPDF->crearPDF([
      "tituloReporte" => "REPORTE DE PRODUCTOS",
      "configColumnas" => [
        'id_producto' => ['CÓDIGO', 60],
        'nombre_producto' => ['PRODUCTO', 40],
        'precio_producto' => ['PRECIO', 30],
        'stock_producto' => ['STOCK', 20],
        'nombre_categoria_producto' => ['CATEGORÍA', 40],
      ],
      "infoBD" => $infoCeldas,
    ]);
  }
  public function reporteMateriaPrima() {
    $instruccionesDB = [
      'tabla' => 'materias_primas as mp',
      'campos' => '
        mp.id_materia_prima, mp.nombre_materia_prima, mp.stock_materia_prima, mp.precio_materia_prima, um.simbolo_unidad_medida
      ',
      'datosJoins' => [
        'unidades_medidas AS um' => 'mp.id_unidad_medida = um.id_unidad_medida',
      ],
      'ORDER' => 'mp.nombre_materia_prima ASC',
    ];
    $infoCeldas = $this->seleccionarDatos2($instruccionesDB)->fetchAll();
    if ($infoCeldas == []) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Sin regisros existentes',
        'texto' => 'No hay registros dentro de la Base de Datos',
        'icono' => 'warning',
      ];
    }

    //Modificamos la fecha a formato AM/PM  
    foreach ($infoCeldas as &$fila) {
      $fila['stock_materia_prima'] .= ' ' . $fila['simbolo_unidad_medida'];
      $fila['precio_materia_prima'] .= ' Bs';
    }
    unset($fila);

    //Creación del PDF
    $objetoPDF = new pdfModel();
    $objetoPDF->SetTitle('REPORTE DE MATERIAS PRIMAS');
    return $objetoPDF->crearPDF([
      "tituloReporte" => "REPORTE DE MATERIAS PRIMAS",
      "configColumnas" => [
        'id_materia_prima' => ['CÓDIGO', 60],
        'nombre_materia_prima' => ['MATERIA PRIMA', 60],
        'stock_materia_prima' => ['STOCK', 30],
        'precio_materia_prima' => ['PRECIO', 40],
      ],
      "infoBD" => $infoCeldas,
    ]);
  }

  private function reporteVentasP() {
    $instruccionesDB = [
      'tabla' => 'ordenes_entregas_presupuestos as f',
      'campos' => "
                    f.id_orden_entrega_presupuesto,
                    f.fecha_orden_entrega_presupuesto,
                    COALESCE(c.rif_cedula_cliente, 'Publico General') AS cliente,
                    COALESCE(SUM(pf.cantidad_producto * pr.precio_producto), 0) + 
                    COALESCE(SUM(sf.cantidad_servicio * s.precio_servicio), 0) + 
                    COALESCE(ci.monto_cambio_iva, 0) AS total_venta,
                    'Completada' AS estado_venta,
                CASE 
                   WHEN pf.id_producto_factura IS NOT NULL THEN pr.nombre_producto
                   WHEN sf.id_servicio_factura IS NOT NULL THEN s.nombre_servicio
                   ELSE 'N/A'
                END AS item,
                CASE 
                   WHEN pf.id_producto_factura IS NOT NULL THEN 'Producto'
                   WHEN sf.id_servicio_factura IS NOT NULL THEN 'Servicio'
                ELSE 'N/A'
                END AS tipo_item,
                   COALESCE(pf.cantidad_producto, sf.cantidad_servicio, 0) AS cantidad
      ",
      'datosJoins' => [
        'clientes as c' => 'f.rif_cedula_cliente = c.rif_cedula_cliente',
        'cambios_iva as ci' => 'f.id_cambio_iva = ci.id_cambio_iva',
        'productos_ordenes_entregas_presupuestos as pf' => 'f.id_orden_entrega_presupuesto = pf.id_producto_factura',
        'productos as pr' => 'pf.id_producto_factura = pr.id_producto',
        'servicios_ordenes_entregas_presupuestos as sf' => 'f.id_orden_entrega_presupuesto = sf.id_orden_entrega_presupuesto',
        'servicios as s' => 'sf.id_servicio_factura = s.id_servicio',
      ],
      'WHERE' => [
        'f.fecha_orden_entrega_presupuesto' => [
          '>=' => $this->filtros['fecha_desde'],
          '<=' => $this->filtros['fecha_hasta'],
        ]
      ]
    ];
    $infoCeldas = $this->seleccionarDatos2($instruccionesDB)->fetchAll();
    if ($infoCeldas == [] ||  $infoCeldas[0]['id_orden_entrega_presupuesto'] == [] || $infoCeldas[0]['fecha_orden_entrega_presupuesto'] == []) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Sin registros existentes',
        'texto' => 'No hay registros dentro de ese intervalo de tiempo',
        'icono' => 'warning',
      ];
    }

    //Modificamos la fecha a formato AM/PM  
    foreach ($infoCeldas as &$fila) {
      $fila['fecha_orden_entrega_presupuesto'] = $this->FechaHora_Sel('fecha_hora_AM_PM', $fila['fecha_orden_entrega_presupuesto']);
      //$fila['valor_moneda'] .= ' Bs';
    }
    unset($fila);

    //Creación del PDF
    $objetoPDF = new pdfModel();
    $objetoPDF->SetTitle('REPORTE DE VENTAS');
    return $objetoPDF->crearPDF([
      "tituloReporte" => "REPORTE DE VENTAS",
      "configColumnas" => [
        'id_orden_entrega_presupuesto' => ['NRO FACTURA', 40],
        'rif_cedula_cliente' => ['CLIENTE', 60],
        'fecha_orden_entrega_presupuesto' => ['FECHA', 60],
        'total_venta' => ['MONTO', 60],
      ],
      "infoBD" => $infoCeldas,
    ]);
  }
  private function reporteComprasP() {
    $instruccionesDB = [
      'tabla' => 'compras as c',
      'campos' => '
        c.id_compra,
        c.fecha_compra,
        p.razon_social_proveedor,
        mp.nombre_materia_prima,
        mp.precio_materia_prima,
        SUM(mpc.cantidad_materia_prima) AS cantidad,
        SUM(mpc.cantidad_materia_prima * mp.precio_materia_prima) AS total_compra
      ',
      'datosJoins' => [
        'materias_primas_compras as mpc' => 'c.id_compra = mpc.id_compra',
        'materias_primas as mp' => 'mpc.id_materia_prima = mp.id_materia_prima',
        'proveedores as p' => 'c.rif_proveedor = p.rif_proveedor',
      ],
      'WHERE' => [
        'c.fecha_compra' => [
          '>=' => $this->filtros['fecha_desde'],
          '<=' => $this->filtros['fecha_hasta'],
        ]
      ],
      'GROUP BY' => 'mp.id_materia_prima',
      'ORDER' => 'c.fecha_compra DESC'
    ];
    $infoCeldas = $this->seleccionarDatos2($instruccionesDB)->fetchAll();

    if ($infoCeldas == [] || ($infoCeldas[0]['id_compra'] ?? null) == null) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Sin registros existentes',
        'texto' => 'No hay registros dentro de ese intervalo de tiempo',
        'icono' => 'warning',
      ];
    }

    //Modificamos la fecha a formato AM/PM  
    foreach ($infoCeldas as &$fila) {
      $fila['fecha_compra'] = $this->FechaHora_Sel('fecha_hora_AM_PM', $fila['fecha_compra']);
      $fila['total_compra'] .= ' Bs';
    }
    unset($fila);

    //Creación del PDF
    $objetoPDF = new pdfModel('L');
    $objetoPDF->SetTitle('REPORTE DE COMPRAS');

    return $objetoPDF->crearPDF([
      "tituloReporte" => "REPORTE DE COMPRAS",
      "configColumnas" => [
        'id_compra' => ['CÓDIGO', 25],
        'fecha_compra' => ['FECHA', 25],
        'razon_social_proveedor' => ['PROVEEDOR', 30],
        'nombre_materia_prima' => ['MATERIA PRIMA', 40],
        'cantidad' => ['CANTIDAD', 25],
        'precio_materia_prima' => ['PRECIO', 25],
        'total_compra' => ['TOTAL', 25],
      ],
      "infoBD" => $infoCeldas,
    ]);
  }
  private function reporteCierreP() {
    $instruccionesDB = [
      'tabla' => 'ordenes_entregas_presupuestos as f',
      'campos' => '
                    f.id_orden_entrega_presupuesto,
                    c.rif_cedula_cliente,
                    f.fecha_orden_entrega_presupuesto
      ',
      'datosJoins' => [
        'clientes as c' => 'f.rif_cedula_cliente = c.rif_cedula_cliente',

      ],
      'WHERE' => [
        'f.fecha_orden_entrega_presupuesto' => [
          '>=' => $this->filtros['fecha_cierre'],
        ]
      ],
      'ORDER' => 'f.fecha_orden_entrega_presupuesto DESC'
    ];
    $infoCeldas = $this->seleccionarDatos2($instruccionesDB)->fetchAll();
    if ($infoCeldas == []) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Sin registros existentes',
        'texto' => 'No hay registros en la fecha seleccionada',
        'icono' => 'warning',
      ];
    }

    //Modificamos la fecha a formato AM/PM  
    foreach ($infoCeldas as &$fila) {
      $fila['fecha_orden_entrega_presupuesto'] = $this->FechaHora_Sel('fecha_hora_AM_PM', $fila['fecha_orden_entrega_presupuesto']);
      //$fila['valor_moneda'] .= ' Bs';
    }
    unset($fila);

    //Creación del PDF
    $objetoPDF = new pdfModel();
    $objetoPDF->SetTitle('REPORTE DE CIERRE DE CAJA');
    return $objetoPDF->crearPDF([
      "tituloReporte" => "REPORTE DE CIERRE DE CAJA",
      "configColumnas" => [
        'id_orden_entrega_presupuesto' => ['NRO FACTURA', 40],
        'rif_cedula_cliente' => ['CLIENTE', 60],
        'fecha_orden_entrega_presupuesto' => ['FECHA', 60],
      ],
      "infoBD" => $infoCeldas,
    ]);
  }
}
