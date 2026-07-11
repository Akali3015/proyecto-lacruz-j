<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use PDO;
use src\modelos\mensajesWSModelo;
use src\modelos\accesosModelo;

class pagosModelo extends conexion {

  private string $idPago = '';
  private string $idOrden = '';
  private array $pagos = [];
  private string $fechaPago = '';

  // -------------------------------------------------------------------------
  // MÉTODOS PÚBLICOS
  // -------------------------------------------------------------------------

  public function listarPagos(array $info) {
    $objAcceso = new accesosModelo();
    $v = $objAcceso->validarPermisos('pagos', 'listar');
    if ($v) return $v;
    return $this->listarPagosP();
  }

  public function listarOEPs(array $info) {
    $objAcceso = new accesosModelo();
    $v = $objAcceso->validarPermisos('pagos', 'registrar');
    if ($v) return $v;
    return $this->listarOEPsP();
  }

  public function obtenerDetallePago(array $info) {
    $objAcceso = new accesosModelo();
    $v = $objAcceso->validarPermisos('pagos', 'ver');
    if ($v) return $v;

    $this->idPago = $info['id_pago'] ?? '';
    if (empty($this->idPago)) return ['tipo' => 'simple', 'icono' => 'error', 'titulo' => 'Error', 'texto' => 'ID de pago no proporcionado'];

    return $this->obtenerDetallePagoP();
  }

  public function registrarPago(array $info) {
    $objAcceso = new accesosModelo();
    $v = $objAcceso->validarPermisos('pagos', 'registrar');
    if ($v) return $v;

    $esquema = [
      'tipo' => 'arrayA',
      'propiedades' => [
        'id_orden_entrega_presupuesto' => [
          ...molIdSeguro,
          "nombreAlerta" => "código de OEP",
          "nombreBD" => "id_orden_entrega_presupuesto",
          "tablaBD" => "ordenes_entregas_presupuestos",
          "debeExistirBD" => true,
        ],
      ],
      'requerido' => ['id_orden_entrega_presupuesto']
    ];

    $v = $this->limpiarValidar($info, $esquema);
    if (isset($v['error'])) return $v['error'];

    $pagos = isset($info['pagos']) && is_string($info['pagos']) ? json_decode($info['pagos'], true) : ($info['pagos'] ?? []);
    if (empty($pagos)) {
      return ['tipo' => 'simple', 'titulo' => 'Error', 'texto' => 'Debe ingresar al menos un detalle de pago.', 'icono' => 'warning'];
    }

    $this->idOrden = $info['id_orden_entrega_presupuesto'];
    $this->pagos   = $pagos;

    return $this->registrarPagoP();
  }

  public function actualizarPago(array $info) {
    $objAcceso = new accesosModelo();
    $v = $objAcceso->validarPermisos('pagos', 'actualizar');
    if ($v) return $v;

    $esquema = [
      'tipo' => 'arrayA',
      'propiedades' => [
        'id_pago' => [
          ...molIdSeguro,
          "nombreAlerta" => "código del pago",
          "nombreBD" => "id_pago",
          "tablaBD" => "pagos",
          "debeExistirBD" => true,
        ],
        'id_orden_entrega_presupuesto' => [
          ...molIdSeguro,
          "nombreAlerta" => "código de OEP",
          "nombreBD" => "id_orden_entrega_presupuesto",
          "tablaBD" => "ordenes_entregas_presupuestos",
          "debeExistirBD" => true,
        ],
      ],
      'requerido' => ['id_pago', 'id_orden_entrega_presupuesto']
    ];

    $v = $this->limpiarValidar($info, $esquema);
    if (isset($v['error'])) return $v['error'];

    $pagos = isset($info['pagos']) && is_string($info['pagos']) ? json_decode($info['pagos'], true) : ($info['pagos'] ?? []);
    if (empty($pagos)) {
      return ['tipo' => 'simple', 'titulo' => 'Error', 'texto' => 'Debe ingresar al menos un detalle de pago.', 'icono' => 'warning'];
    }

    $this->idPago = $info['id_pago'];
    $this->idOrden = $info['id_orden_entrega_presupuesto'];
    $this->pagos   = $pagos;

    return $this->actualizarPagoP();
  }

  public function eliminarPago(array $info) {
    $objAcceso = new accesosModelo();
    $v = $objAcceso->validarPermisos('pagos', 'eliminar');
    if ($v) return $v;

    $esquema = [
      'tipo' => 'arrayA',
      'propiedades' => [
        'id_pago' => [
          ...molIdSeguro,
          "nombreAlerta" => "código del pago",
          "nombreBD" => "id_pago",
          "tablaBD" => "pagos",
          "debeExistirBD" => true,
        ],
      ],
      'requerido' => ['id_pago']
    ];

    $v = $this->limpiarValidar($info, $esquema);
    if (isset($v['error'])) return $v['error'];

    $this->idPago = $info['id_pago'];

    return $this->eliminarPagoP();
  }

  public function eliminarComprobante(array $info) {
    $objAcceso = new accesosModelo();
    $v = $objAcceso->validarPermisos('pagos', 'actualizar');
    if ($v) return $v;

    $esquema = [
      'tipo' => 'arrayA',
      'propiedades' => [
        'id_comprobante_pago' => [
          ...molId,
          "nombreAlerta" => "ID del comprobante",
          "nombreBD" => "id_comprobante_pago",
          "tablaBD" => "comprobantes_pagos",
          "debeExistirBD" => true,
        ],
      ],
      'requerido' => ['id_comprobante_pago']
    ];

    $v = $this->limpiarValidar($info, $esquema);
    if (isset($v['error'])) return $v['error'];

    return $this->eliminarComprobanteP($info['id_comprobante_pago']);
  }

  // -------------------------------------------------------------------------
  // MÉTODOS PRIVADOS
  // -------------------------------------------------------------------------

  private function listarPagosP() {
    $resultado = $this->seleccionarDatos2([
      'campos' => "
        p.id_pago,
        p.id_orden_entrega_presupuesto,
        p.fecha_pago,
        p.status,
        c.razon_social_cliente AS CLIENTE,
        (SELECT COALESCE(SUM(
           CASE WHEN mo.nombre_moneda = 'BÓLIVAR' OR mo.nombre_moneda = 'BS' 
                THEN dp.monto_pago / (SELECT MAX(valor_moneda) FROM monedas WHERE nombre_moneda IN ('DÓLAR', 'DOLAR')) 
                ELSE dp.monto_pago END
         ), 0) 
         FROM detalles_pagos dp 
         JOIN monedas mo ON dp.id_moneda=mo.id_moneda 
         WHERE dp.id_pago = p.id_pago AND dp.status = 1) AS monto_total_dolares,
        (SELECT COUNT(*) FROM comprobantes_pagos cp WHERE cp.id_pago = p.id_pago AND cp.status = 1) AS cant_comprobantes
      ",
      'tabla' => 'pagos as p',
      'datosJoins' => [
        'ordenes_entregas_presupuestos as oep' => 'p.id_orden_entrega_presupuesto = oep.id_orden_entrega_presupuesto',
        'clientes as c' => 'oep.rif_cedula_cliente = c.rif_cedula_cliente'
      ],
      'WHERE' => ['p.status' => 1],
      'ORDER' => 'p.fecha_pago DESC',
    ]);

    return $resultado->fetchAll(PDO::FETCH_ASSOC);
  }

  private function listarOEPsP() {
    // Solo OEPs que no estén anuladas ni "Pagadas y Despachadas" ni "Procesada y Pagada" (Si están totalmente pagadas, no deberían salir acá por defecto, o sí, dependiendo de la regla, pero las listamos todas las que necesiten abonos)
    $resultado = $this->seleccionarDatos2([
      'campos' => "
        f.id_orden_entrega_presupuesto,
        c.razon_social_cliente AS CLIENTE,
        f.rif_cedula_cliente,
        f.fecha_orden_entrega_presupuesto,
        f.status,
        ci.monto_cambio_iva,
        (SELECT COALESCE(SUM(pf.cantidad_producto * p.precio_producto), 0) 
         FROM productos_ordenes_entregas_presupuestos pf 
         JOIN presentaciones_productos pp ON pf.id_presentacion_producto=pp.id_presentacion_producto 
         JOIN productos p ON pp.id_producto = p.id_producto 
         WHERE pf.id_orden_entrega_presupuesto = f.id_orden_entrega_presupuesto AND pf.status = 1) AS sub_prod,
        (SELECT COALESCE(SUM(sf.cantidad_servicio * CASE WHEN sf.es_precio_mapfre=1 THEN sf.precio_servicio_mapfre ELSE s.precio_servicio END), 0) 
         FROM servicios_ordenes_entregas_presupuestos sf 
         JOIN servicios s ON sf.id_servicio=s.id_servicio 
         WHERE sf.id_orden_entrega_presupuesto = f.id_orden_entrega_presupuesto AND sf.status = 1) AS sub_serv,
        (SELECT COALESCE(SUM(
           r.precio_ruta * 
           IF(lat.coordenada_latitud LIKE '%|%',
             CAST(SUBSTRING_INDEX(lat.coordenada_latitud, '|', -1) AS DECIMAL(10,2)),
             1
           )
         ), 0) 
         FROM deliveries d 
         JOIN direcciones dir ON d.id_direccion=dir.id_direccion 
         JOIN rutas r ON dir.id_ruta=r.id_ruta 
         LEFT JOIN latitudes_direcciones lat ON dir.id_latitud_direccion=lat.id_latitud_direccion
         WHERE d.id_orden_entrega_presupuesto = f.id_orden_entrega_presupuesto AND d.status = 1) AS sub_del,
        (SELECT COALESCE(SUM(
           CASE WHEN mo.nombre_moneda = 'BÓLIVAR' OR mo.nombre_moneda = 'BS' 
                THEN dp.monto_pago / (SELECT MAX(valor_moneda) FROM monedas WHERE nombre_moneda IN ('DÓLAR', 'DOLAR')) 
                ELSE dp.monto_pago END
         ), 0) 
         FROM pagos pa 
         JOIN detalles_pagos dp ON pa.id_pago=dp.id_pago 
         JOIN monedas mo ON dp.id_moneda=mo.id_moneda 
         WHERE pa.id_orden_entrega_presupuesto = f.id_orden_entrega_presupuesto AND dp.status = 1 AND pa.status=1) AS total_pagado
      ",
      'tabla' => 'ordenes_entregas_presupuestos as f',
      'datosJoins' => [
        'clientes as c' => 'f.rif_cedula_cliente = c.rif_cedula_cliente',
        'cambios_iva as ci' => 'f.id_cambio_iva = ci.id_cambio_iva',
      ],
      // Excluir OEPs anuladas (status 2), o presupuestos (status 0). Consideramos 1 (Procesada), 3 (Despachada), 4 (Despachada sin pago), 10 (Pagada), 11 (Pagada y despachada)
      'WHERE' => ['f.status' => '!= 2'], 
      'ORDER' => 'f.fecha_orden_entrega_presupuesto DESC',
    ]);

    $filas = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $ordenesActivas = [];

    foreach ($filas as &$fila) {
      if ($fila['status'] == 2) continue; // Anulada

      $iva = floatval($fila['monto_cambio_iva']) / 100;
      $subTotal = floatval($fila['sub_prod']) + floatval($fila['sub_serv']) + floatval($fila['sub_del']);
      $totalOrden = $subTotal + ($subTotal * $iva);
      $pagado = floatval($fila['total_pagado']);
      $restante = round($totalOrden - $pagado, 2);

      $fila['total_orden'] = $totalOrden;
      $fila['restante'] = $restante;
      
      // Solo mostramos las que tienen saldo pendiente
      if ($restante > 0) {
        $ordenesActivas[] = $fila;
      }
    }
    return $ordenesActivas;
  }

  private function obtenerDetallePagoP() {
    $resultado = $this->seleccionarDatos2([
      'campos' => "
        p.id_pago, p.id_orden_entrega_presupuesto, p.fecha_pago,
        c.razon_social_cliente AS CLIENTE,
        ci.monto_cambio_iva,
        (SELECT COALESCE(SUM(pf.cantidad_producto * pr.precio_producto), 0) 
         FROM productos_ordenes_entregas_presupuestos pf 
         JOIN presentaciones_productos pp ON pf.id_presentacion_producto=pp.id_presentacion_producto 
         JOIN productos pr ON pp.id_producto = pr.id_producto 
         WHERE pf.id_orden_entrega_presupuesto = p.id_orden_entrega_presupuesto AND pf.status = 1) AS sub_prod,
        (SELECT COALESCE(SUM(sf.cantidad_servicio * CASE WHEN sf.es_precio_mapfre=1 THEN sf.precio_servicio_mapfre ELSE s.precio_servicio END), 0) 
         FROM servicios_ordenes_entregas_presupuestos sf 
         JOIN servicios s ON sf.id_servicio=s.id_servicio 
         WHERE sf.id_orden_entrega_presupuesto = p.id_orden_entrega_presupuesto AND sf.status = 1) AS sub_serv,
        (SELECT COALESCE(SUM(
           r.precio_ruta * 
           IF(lat.coordenada_latitud LIKE '%|%', CAST(SUBSTRING_INDEX(lat.coordenada_latitud, '|', -1) AS DECIMAL(10,2)), 1)
         ), 0) 
         FROM deliveries d 
         JOIN direcciones dir ON d.id_direccion=dir.id_direccion 
         JOIN rutas r ON dir.id_ruta=r.id_ruta 
         LEFT JOIN latitudes_direcciones lat ON dir.id_latitud_direccion=lat.id_latitud_direccion
         WHERE d.id_orden_entrega_presupuesto = p.id_orden_entrega_presupuesto AND d.status = 1) AS sub_del,
        (SELECT COALESCE(SUM(
           CASE WHEN mo.nombre_moneda = 'BÓLIVAR' OR mo.nombre_moneda = 'BS' 
                THEN dp.monto_pago / (SELECT MAX(valor_moneda) FROM monedas WHERE nombre_moneda IN ('DÓLAR', 'DOLAR')) 
                ELSE dp.monto_pago END
         ), 0) 
         FROM pagos pa 
         JOIN detalles_pagos dp ON pa.id_pago=dp.id_pago 
         JOIN monedas mo ON dp.id_moneda=mo.id_moneda 
         WHERE pa.id_orden_entrega_presupuesto = p.id_orden_entrega_presupuesto AND dp.status = 1 AND pa.status=1 AND pa.id_pago != p.id_pago) AS total_pagado
      ",
      'tabla' => "pagos as p",
      'datosJoins' => [
        'ordenes_entregas_presupuestos as oep' => 'p.id_orden_entrega_presupuesto = oep.id_orden_entrega_presupuesto',
        'clientes as c' => 'oep.rif_cedula_cliente = c.rif_cedula_cliente',
        'cambios_iva as ci' => 'oep.id_cambio_iva = ci.id_cambio_iva'
      ],
      'WHERE' => ['p.id_pago' => $this->idPago, 'p.status' => 1]
    ]);
    $pagoInfo = $resultado->fetch(PDO::FETCH_ASSOC);

    if ($pagoInfo) {
      $iva = floatval($pagoInfo['monto_cambio_iva']) / 100;
      $subTotal = floatval($pagoInfo['sub_prod']) + floatval($pagoInfo['sub_serv']) + floatval($pagoInfo['sub_del']);
      $totalOrden = $subTotal + ($subTotal * $iva);
      $pagado = floatval($pagoInfo['total_pagado']);
      $restante = round($totalOrden - $pagado, 2);

      $pagoInfo['total_orden'] = $totalOrden;
      $pagoInfo['restante'] = $restante;
    }

    if (!$pagoInfo) return ['tipo' => 'simple', 'titulo' => 'Pago no encontrado', 'texto' => 'El pago no existe o ha sido eliminado', 'icono' => 'error'];

    $detalles = $this->seleccionarDatos2([
      'campos' => "
        dp.id_detalle_pago, dp.monto_pago, dp.id_metodo_pago, dp.id_moneda,
        (SELECT id_banco FROM bancos_detalles_pagos bdp WHERE bdp.id_detalle_pago = dp.id_detalle_pago AND bdp.es_emisor = 1 LIMIT 1) as id_banco_emisor,
        (SELECT id_banco FROM bancos_detalles_pagos bdp WHERE bdp.id_detalle_pago = dp.id_detalle_pago AND bdp.es_emisor = 0 LIMIT 1) as id_banco_receptor,
        (SELECT referencia_pago FROM referencias_detalles_pagos rdp WHERE rdp.id_detalle_pago = dp.id_detalle_pago LIMIT 1) as referencia_pago,
        mp.nombre_metodo_pago, mo.nombre_moneda, mo.simbolo_moneda
      ",
      'tabla' => "detalles_pagos as dp",
      'datosJoins' => [
        'metodos_pagos as mp' => 'dp.id_metodo_pago = mp.id_metodo_pago',
        'monedas as mo' => 'dp.id_moneda = mo.id_moneda'
      ],
      'WHERE' => ['dp.id_pago' => $this->idPago, 'dp.status' => 1]
    ])->fetchAll(PDO::FETCH_ASSOC);

    $comprobantes = $this->seleccionarDatos2([
      'campos' => "id_comprobante_pago, path_comprobante",
      'tabla' => "comprobantes_pagos",
      'WHERE' => ['id_pago' => $this->idPago, 'status' => 1]
    ])->fetchAll(PDO::FETCH_ASSOC);

    $pagoInfo['detalles'] = $detalles;
    $pagoInfo['comprobantes'] = $comprobantes;

    return $pagoInfo;
  }

  private function registrarPagoP() {
    $objBitacora = new bitacoraModelo();

    try {
      $idPago = $this->generarCodSeg([
        'tablaBD' => 'pagos',
        'prefijo' => 'PAG',
        'campoID' => 'id_pago',
      ]);

      $fechaPago = $this->FechaHora_Sel('fecha_hora_BD');

      $idPagoReg = $this->guardarDatos2([
        'tabla' => 'pagos',
        'datos' => [
          'id_pago'    => $idPago,
          'id_orden_entrega_presupuesto' => $this->idOrden,
          'fecha_pago' => $fechaPago,
          'status'     => 1
        ]
      ]);

      if (!$idPagoReg) {
        throw new \Exception("No se pudo registrar el pago principal.");
      }

      $stmtMo = $this->conectar()->query("SELECT id_moneda FROM monedas WHERE valor_moneda = 1 LIMIT 1");
      $mo = $stmtMo->fetch(PDO::FETCH_ASSOC);
      $idMonedaBolivar = $mo ? $mo['id_moneda'] : 2;

      foreach ($this->pagos as $pago) {
        $idMetodo = $pago['id_metodo_pago'] ?? '';
        $idMoneda = $pago['id_moneda'] ?? '';
        $monto    = floatval(str_replace(['.', ','], ['', '.'], $pago['monto_pago'] ?? 0));

        if (empty($idMoneda)) $idMoneda = $idMonedaBolivar;

        if (empty($idMetodo) || $monto <= 0) {
          throw new \Exception("Datos de pago inválidos.");
        }

        $idDetalleReg = $this->guardarDatos2([
          'tabla' => 'detalles_pagos',
          'datos' => [
            'id_pago'        => $idPago,
            'id_metodo_pago' => $idMetodo,
            'id_moneda'      => $idMoneda,
            'monto_pago'     => $monto,
            'status'         => 1
          ]
        ]);

        if (!$idDetalleReg) {
          throw new \Exception("No se pudo registrar el detalle de pago.");
        }

        if (($pago['id_banco_emisor'] ?? '') != '') {
          $this->guardarDatos2([
            'tabla' => 'bancos_detalles_pagos',
            'datos' => ['id_detalle_pago' => $idDetalleReg, 'id_banco' => $pago['id_banco_emisor'], 'es_emisor' => 1]
          ]);
        }
        if (($pago['id_banco_receptor'] ?? '') != '') {
          $this->guardarDatos2([
            'tabla' => 'bancos_detalles_pagos',
            'datos' => ['id_detalle_pago' => $idDetalleReg, 'id_banco' => $pago['id_banco_receptor'], 'es_emisor' => 0]
          ]);
        }
        if (($pago['referencia_pago'] ?? '') != '') {
          $this->guardarDatos2([
            'tabla' => 'referencias_detalles_pagos',
            'datos' => ['id_detalle_pago' => $idDetalleReg, 'referencia_pago' => $pago['referencia_pago']]
          ]);
        }
      }

      // Procesar Comprobantes
      if (isset($_FILES['comprobantes']) && !empty($_FILES['comprobantes']['name'][0])) {
        $dirComprobantes = DIR_FOTOS . "comprobantes_pagos/";
        if (!is_dir($dirComprobantes)) mkdir($dirComprobantes, 0777, true);
        
        $nuevos = count($_FILES['comprobantes']['name']);
        if ($nuevos > 3) throw new \Exception("Límite de comprobantes excedido (máximo 3).");

        for ($i = 0; $i < $nuevos; $i++) {
          $nombreFile = $_FILES['comprobantes']['name'][$i];
          $tmpFile = $_FILES['comprobantes']['tmp_name'][$i];
          $ext = strtolower(pathinfo($nombreFile, PATHINFO_EXTENSION));

          if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) continue;

          $nuevoNombre = $this->idOrden . "_pago_" . $idPago . "_" . time() . "_" . $i . "." . $ext;
          if (move_uploaded_file($tmpFile, $dirComprobantes . $nuevoNombre)) {
            $this->guardarDatos2([
              'tabla' => 'comprobantes_pagos',
              'datos' => ['id_pago' => $idPago, 'path_comprobante' => $nuevoNombre, 'status' => 1]
            ]);
          }
        }
      }

      // Recalcular estado de la orden (Procesada y Pagada, Pagada y Despachada, etc.)
      $this->recalcularStatusOEPP($this->idOrden);

      $objBitacora->registrarBitacora([
        'modulo' => 'pagos',
        'accion' => "Registrar Pago: " . $idPago,
        'resultado' => 'exitoso',
        'nuevo' => ['id_pago' => $idPago, 'id_orden' => $this->idOrden]
      ]);

      $this->commit();

      $objNot = new mensajesWSModelo();
      $objNot->enviarMensajesWS([
        "receptor" => ['tipo' => 'rol', 'rol' => 'ADMINISTRADOR'],
        'cuerpo' => [
          ['accion' => "borrarDataModuloSS", 'modulo' => 'pagos'],
          ['accion' => "borrarDataModuloSS", 'modulo' => 'ordenesEntregasPresupuestos'],
          ['accion' => 'alertar', 'alerta' => ['tipo' => 'simple', 'titulo' => 'Pago Recibido', 'texto' => "Se ha registrado un pago para la OEP {$this->idOrden}.", 'icono' => 'info', 'notifier' => true, 'tiempo' => 3000]],
          ['accion' => "actDT", 'modulo' => 'pagos'],
          ['accion' => "actDT", 'modulo' => 'ordenesEntregasPresupuestos']
        ]
      ]);

      return ['tipo' => 'limpiarYcerrar', 'titulo' => 'Pago Registrado', 'texto' => 'El pago ha sido procesado exitosamente.', 'icono' => 'success'];
    } catch (\Exception $e) {
      $this->rollback();
      $objBitacora->registrarBitacora([
        'modulo' => 'pagos',
        'accion' => "Registrar Pago",
        'resultado' => 'fallido',
        'commit' => true
      ]);
      return ['tipo' => 'simple', 'titulo' => 'Error', 'texto' => $e->getMessage(), 'icono' => 'error'];
    }
  }

  private function actualizarPagoP() {
    $objBitacora = new bitacoraModelo();

    try {
      $pagoExistente = $this->seleccionarDatos2([
        'campos' => '*', 'tabla' => 'pagos', 'WHERE' => ['id_pago' => $this->idPago, 'status' => 1]
      ])->fetch(PDO::FETCH_ASSOC);

      if (!$pagoExistente) throw new \Exception("Pago no encontrado.");

      $this->eliminarDatos2(['tabla' => 'detalles_pagos', 'WHERE' => ['id_pago' => $this->idPago], 'fisico' => true]);

      $stmtMo = $this->conectar()->query("SELECT id_moneda FROM monedas WHERE valor_moneda = 1 LIMIT 1");
      $mo = $stmtMo->fetch(PDO::FETCH_ASSOC);
      $idMonedaBolivar = $mo ? $mo['id_moneda'] : 2;

      foreach ($this->pagos as $pago) {
        $idMetodo = $pago['id_metodo_pago'] ?? '';
        $idMoneda = $pago['id_moneda'] ?? '';
        $monto    = floatval(str_replace(['.', ','], ['', '.'], $pago['monto_pago'] ?? 0));
        if (empty($idMoneda)) $idMoneda = $idMonedaBolivar;

        if (empty($idMetodo) || $monto <= 0) throw new \Exception("Datos de pago inválidos.");

        $idDetalleReg = $this->guardarDatos2([
          'tabla' => 'detalles_pagos',
          'datos' => ['id_pago' => $this->idPago, 'id_metodo_pago' => $idMetodo, 'id_moneda' => $idMoneda, 'monto_pago' => $monto, 'status' => 1]
        ]);

        if (($pago['id_banco_emisor'] ?? '') != '') {
          $this->guardarDatos2([
            'tabla' => 'bancos_detalles_pagos',
            'datos' => ['id_detalle_pago' => $idDetalleReg, 'id_banco' => $pago['id_banco_emisor'], 'es_emisor' => 1]
          ]);
        }
        if (($pago['id_banco_receptor'] ?? '') != '') {
          $this->guardarDatos2([
            'tabla' => 'bancos_detalles_pagos',
            'datos' => ['id_detalle_pago' => $idDetalleReg, 'id_banco' => $pago['id_banco_receptor'], 'es_emisor' => 0]
          ]);
        }
        if (($pago['referencia_pago'] ?? '') != '') {
          $this->guardarDatos2([
            'tabla' => 'referencias_detalles_pagos',
            'datos' => ['id_detalle_pago' => $idDetalleReg, 'referencia_pago' => $pago['referencia_pago']]
          ]);
        }
      }

      if (isset($_FILES['comprobantes']) && !empty($_FILES['comprobantes']['name'][0])) {
        $stmtC = $this->conectar()->prepare("SELECT COUNT(id_comprobante_pago) as total FROM comprobantes_pagos WHERE id_pago = :id AND status = 1");
        $stmtC->execute([':id' => $this->idPago]);
        $totalActual = (int)($stmtC->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

        $nuevos = count($_FILES['comprobantes']['name']);
        if ($totalActual + $nuevos > 3) throw new \Exception("Límite de comprobantes excedido (máximo 3).");

        $dirComprobantes = DIR_FOTOS . "comprobantes_pagos/";
        if (!is_dir($dirComprobantes)) mkdir($dirComprobantes, 0777, true);

        for ($i = 0; $i < $nuevos; $i++) {
          $nombreFile = $_FILES['comprobantes']['name'][$i];
          $tmpFile = $_FILES['comprobantes']['tmp_name'][$i];
          $ext = strtolower(pathinfo($nombreFile, PATHINFO_EXTENSION));

          if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) continue;

          $nuevoNombre = $this->idOrden . "_pago_" . $this->idPago . "_" . time() . "_" . $i . "." . $ext;
          if (move_uploaded_file($tmpFile, $dirComprobantes . $nuevoNombre)) {
            $this->guardarDatos2([
              'tabla' => 'comprobantes_pagos',
              'datos' => ['id_pago' => $this->idPago, 'path_comprobante' => $nuevoNombre, 'status' => 1]
            ]);
          }
        }
      }

      $this->recalcularStatusOEPP($this->idOrden);

      $objBitacora->registrarBitacora([
        'modulo' => 'pagos',
        'accion' => "Actualizar Pago: " . $this->idPago,
        'resultado' => 'exitoso'
      ]);

      $this->commit();

      $objNot = new mensajesWSModelo();
      $objNot->enviarMensajesWS([
        "receptor" => ['tipo' => 'rol', 'rol' => 'ADMINISTRADOR'],
        'cuerpo' => [
          ['accion' => "borrarDataModuloSS", 'modulo' => 'pagos'],
          ['accion' => "borrarDataModuloSS", 'modulo' => 'ordenesEntregasPresupuestos'],
          ['accion' => "actDT", 'modulo' => 'pagos'],
          ['accion' => "actDT", 'modulo' => 'ordenesEntregasPresupuestos']
        ]
      ]);

      return ['tipo' => 'limpiarYcerrar', 'titulo' => 'Pago Actualizado', 'texto' => 'El pago se actualizó correctamente.', 'icono' => 'success'];

    } catch (\Exception $e) {
      $this->rollback();
      $objBitacora->registrarBitacora(['modulo' => 'pagos', 'accion' => "Actualizar Pago", 'resultado' => 'fallido', 'commit' => true]);
      return ['tipo' => 'simple', 'titulo' => 'Error', 'texto' => $e->getMessage(), 'icono' => 'error'];
    }
  }

  private function eliminarPagoP() {
    $objBitacora = new bitacoraModelo();

    try {
      $pagoExistente = $this->seleccionarDatos2([
        'campos' => 'id_orden_entrega_presupuesto', 'tabla' => 'pagos', 'WHERE' => ['id_pago' => $this->idPago, 'status' => 1]
      ])->fetch(PDO::FETCH_ASSOC);

      if (!$pagoExistente) throw new \Exception("Pago no encontrado.");
      
      $idOrden = $pagoExistente['id_orden_entrega_presupuesto'];

      // Soft delete al pago y sus detalles
      $this->actualizarDatos2(['tabla' => 'pagos', 'datos' => ['status' => 0], 'WHERE' => ['id_pago' => $this->idPago]]);
      $this->actualizarDatos2(['tabla' => 'detalles_pagos', 'datos' => ['status' => 0], 'WHERE' => ['id_pago' => $this->idPago]]);
      
      // Recalcular estado de la orden (Procesada y sin pago, Despachada y sin pago, etc.)
      $this->recalcularStatusOEPP($idOrden);

      $objBitacora->registrarBitacora([
        'modulo' => 'pagos',
        'accion' => "Eliminar Pago: " . $this->idPago,
        'resultado' => 'exitoso'
      ]);

      $this->commit();

      $objNot = new mensajesWSModelo();
      $objNot->enviarMensajesWS([
        "receptor" => ['tipo' => 'rol', 'rol' => 'ADMINISTRADOR'],
        'cuerpo' => [
          ['accion' => "borrarDataModuloSS", 'modulo' => 'pagos'],
          ['accion' => "borrarDataModuloSS", 'modulo' => 'ordenesEntregasPresupuestos'],
          ['accion' => "actDT", 'modulo' => 'pagos'],
          ['accion' => "actDT", 'modulo' => 'ordenesEntregasPresupuestos']
        ]
      ]);

      return ['tipo' => 'simple', 'titulo' => 'Pago Eliminado', 'texto' => 'El pago fue eliminado correctamente y el estado de la OEP se ha actualizado.', 'icono' => 'success'];
    } catch (\Exception $e) {
      $this->rollback();
      $objBitacora->registrarBitacora(['modulo' => 'pagos', 'accion' => "Eliminar Pago", 'resultado' => 'fallido', 'commit' => true]);
      return ['tipo' => 'simple', 'titulo' => 'Error', 'texto' => $e->getMessage(), 'icono' => 'error'];
    }
  }

  private function eliminarComprobanteP($idComprobante) {
    try {
      $comp = $this->seleccionarDatos2(['campos' => '*', 'tabla' => 'comprobantes_pagos', 'WHERE' => ['id_comprobante_pago' => $idComprobante]])->fetch(PDO::FETCH_ASSOC);
      if (!$comp) throw new \Exception("Comprobante no encontrado.");

      $this->eliminarDatos2(['tabla' => 'comprobantes_pagos', 'WHERE' => ['id_comprobante_pago' => $idComprobante], 'fisico' => true]);
      $archivo = DIR_FOTOS . "comprobantes_pagos/" . $comp['path_comprobante'];
      if (file_exists($archivo)) unlink($archivo);

      $this->commit();
      return ['tipo' => 'simple', 'titulo' => 'Éxito', 'texto' => 'Comprobante eliminado', 'icono' => 'success'];
    } catch (\Exception $e) {
      $this->rollback();
      return ['tipo' => 'simple', 'titulo' => 'Error', 'texto' => $e->getMessage(), 'icono' => 'error'];
    }
  }

  private function recalcularStatusOEPP($idOrden) {
    $stmtCheck = $this->conectar()->prepare("
      SELECT f.status,
             (SELECT COALESCE(SUM(
               CASE WHEN mo.nombre_moneda = 'BÓLIVAR' OR mo.nombre_moneda = 'BS' 
                    THEN dp.monto_pago / (SELECT MAX(valor_moneda) FROM monedas WHERE nombre_moneda IN ('DÓLAR', 'DOLAR')) 
                    ELSE dp.monto_pago END
             ), 0) 
             FROM pagos pa 
             JOIN detalles_pagos dp ON pa.id_pago=dp.id_pago 
             JOIN monedas mo ON dp.id_moneda=mo.id_moneda 
             WHERE pa.id_orden_entrega_presupuesto = f.id_orden_entrega_presupuesto AND dp.status = 1 AND pa.status=1) AS total_pagado,
             (SELECT COALESCE(SUM(pf.cantidad_producto * p.precio_producto), 0) 
              FROM productos_ordenes_entregas_presupuestos pf 
              JOIN presentaciones_productos pp ON pf.id_presentacion_producto = pp.id_presentacion_producto 
              JOIN productos p ON pp.id_producto = p.id_producto 
              WHERE pf.id_orden_entrega_presupuesto = f.id_orden_entrega_presupuesto AND pf.status = 1) AS sub_prod,
             (SELECT COALESCE(SUM(sf.cantidad_servicio * CASE WHEN sf.es_precio_mapfre=1 THEN sf.precio_servicio_mapfre ELSE s.precio_servicio END), 0) 
              FROM servicios_ordenes_entregas_presupuestos sf 
              JOIN servicios s ON sf.id_servicio=s.id_servicio 
              WHERE sf.id_orden_entrega_presupuesto = f.id_orden_entrega_presupuesto AND sf.status = 1) AS sub_serv,
             (SELECT COALESCE(SUM(
                r.precio_ruta * 
                IF(lat.coordenada_latitud LIKE '%|%',
                  CAST(SUBSTRING_INDEX(lat.coordenada_latitud, '|', -1) AS DECIMAL(10,2)),
                  1
                )
              ), 0) 
               FROM deliveries d 
               JOIN direcciones dir ON d.id_direccion=dir.id_direccion 
               JOIN rutas r ON dir.id_ruta=r.id_ruta 
               LEFT JOIN latitudes_direcciones lat ON dir.id_latitud_direccion=lat.id_latitud_direccion
               WHERE d.id_orden_entrega_presupuesto = f.id_orden_entrega_presupuesto AND d.status = 1) AS sub_del,
             ci.monto_cambio_iva
      FROM ordenes_entregas_presupuestos f
      JOIN cambios_iva ci ON f.id_cambio_iva = ci.id_cambio_iva
      WHERE f.id_orden_entrega_presupuesto = :id
    ");
    $stmtCheck->execute([':id' => $idOrden]);
    $f = $stmtCheck->fetch(PDO::FETCH_ASSOC);
    if ($f) {
      $iva = floatval($f['monto_cambio_iva']) / 100;
      $sub = floatval($f['sub_prod']) + floatval($f['sub_serv']) + floatval($f['sub_del']);
      $tot = round($sub + ($sub * $iva), 2);
      $pag = floatval($f['total_pagado']);
      
      $statusActual = $f['status'];
      $nuevoStatus = $statusActual;

      if (round($tot - $pag, 2) <= 0.01) {
        // Está pagada
        $nuevoStatus = in_array($statusActual, [3, 4, 11]) ? 11 : 10;
      } else {
        // No está pagada totalmente
        $nuevoStatus = in_array($statusActual, [3, 4, 11]) ? 4 : 1;
      }

      if ($statusActual != $nuevoStatus) {
        $this->actualizarDatos2([
          'tabla' => 'ordenes_entregas_presupuestos',
          'datos' => ['status' => $nuevoStatus],
          'WHERE' => ['id_orden_entrega_presupuesto' => $idOrden]
        ]);
        $objBitacora = new bitacoraModelo();
        $objBitacora->registrarBitacora([
          'modulo' => 'ordenesEntregasPresupuestos',
          'accion' => "Cambio automático de estado a {$nuevoStatus} por recálculo de pagos en la orden: " . $idOrden,
          'resultado' => 'exitoso'
        ]);
      }
    }
  }

}
