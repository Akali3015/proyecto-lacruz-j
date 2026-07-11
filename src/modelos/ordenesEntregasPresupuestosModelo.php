<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use src\modelos\accesosModelo;
use src\modelos\mensajesWSModelo;
use PDO;
use Exception;

class ordenesEntregasPresupuestosModelo extends conexion {
  private string $idOrden = '';
  private string $cedulaUsuario = '';
  private string $rifCliente = '';
  private string $fechaOrden = '';
  private int $idCambioIva = 0;
  private int $status = 1;
  private array $productos = [];
  private array $servicios = [];
  private array $delivery = [];
  private array $pagos = [];

  // PUBLICOS

  public function validarOrdenes(string $permiso, array &$info = [], array $requerido = []) {
    $objAcceso = new accesosModelo();
    $v = $objAcceso->validarPermisos('ordenesEntregasPresupuestos', $permiso);
    if ($v) return $v;

    $esquema = [
      'tipo' => 'arrayA',
      'propiedades' => [
        'id_orden_entrega_presupuesto' => [
          ...molIdSeguro,
          "nombreAlerta" => "ID de la Orden",
          "nombreBD" => "id_orden_entrega_presupuesto",
          "tablaBD" => "ordenes_entregas_presupuestos",
          "debeExistirBD" => true,
        ],
        'rif_cedula_cliente' => [
          ...molCedulaRifLetra,
          "nombreAlerta" => "Cliente",
          "nombreBD" => "rif_cedula_cliente",
          "tablaBD" => "clientes",
          "debeExistirBD" => true,
        ],
        'estadoSeleccionado' => [
          'tipo' => 'int',
          'minL' => 1,
          'maxL' => 4,
          'regex' => '^\d{1}$',
          'nombreAlerta' => "estado seleccionado"
        ],
      ],
      'requerido' => $requerido,
    ];

    $v = $this->limpiarValidar($info, $esquema);
    if ($v) return $v;

    return false;
  }

  public function ListarOrdenes(array $info = []) {
    $v = $this->validarOrdenes('listar', $info);
    if ($v) return $v;
    return $this->ListarOrdenesP();
  }

  public function ObtenerOrden(array $info) {
    $v = $this->validarOrdenes('listar', $info, ['id_orden_entrega_presupuesto']);
    if ($v) return $v;

    $this->idOrden = $info['id_orden_entrega_presupuesto'];
    return $this->ObtenerOrdenP();
  }

  public function ObtenerDetalleOrden(array $info) {
    $v = $this->validarOrdenes('listar', $info, ['id_orden_entrega_presupuesto']);
    if ($v) return $v;

    $this->idOrden = $info['id_orden_entrega_presupuesto'];
    return $this->ObtenerDetalleOrdenP();
  }

  public function RegistrarOrden(array $info) {
    $v = $this->validarOrdenes('registrar', $info, ['rif_cedula_cliente']);
    if ($v) return $v;

    $prods = isset($info['productos']) && is_string($info['productos']) ? json_decode($info['productos'], true) : ($info['productos'] ?? []);
    $servs = isset($info['servicios']) && is_string($info['servicios']) ? json_decode($info['servicios'], true) : ($info['servicios'] ?? []);
    $deli  = isset($info['delivery'])  && is_string($info['delivery'])  ? json_decode($info['delivery'], true)  : ($info['delivery'] ?? []);

    if (empty($prods) && empty($servs)) {
      return [
        'tipo'   => 'simple',
        'titulo' => 'Sin artículos',
        'texto'  => 'Debe agregar al menos un producto o servicio',
        'icono'  => 'warning',
      ];
    }

    $this->rifCliente    = $info['rif_cedula_cliente'];
    $this->productos     = $prods;
    $this->servicios     = $servs;
    $this->delivery      = $deli;
    $this->cedulaUsuario = $_SESSION['cedula'] ?? '';
    $this->fechaOrden  = $this->FechaHora_Sel('fecha_hora_BD');

    $estadoSel = intval($info['estadoSeleccionado'] ?? 1);
    if ($estadoSel == 3 || $estadoSel == 4) {
      $this->status = 3;
    } else {
      $this->status = 1;
    }

    return $this->RegistrarOrdenP();
  }
  
  public function DespacharOrden(array $info) {
    $v = $this->validarOrdenes('despachar orden', $info, ['id_orden_entrega_presupuesto']);
    if ($v) return $v;

    $this->idOrden = $info['id_orden_entrega_presupuesto'];
    return $this->DespacharOrdenP();
  }

  public function AnularOrden(array $info) {
    $v = $this->validarOrdenes('anular', $info, ['id_orden_entrega_presupuesto']);
    if ($v) return $v;

    $this->idOrden = $info['id_orden_entrega_presupuesto'];
    return $this->AnularOrdenP();
  }

  public function ListarMetodosPago(array $info = []) {
    $v = $this->validarOrdenes('listar', $info);
    if ($v) return $v;
    return $this->ListarMetodosPagoP();
  }

  public function RegistrarPago(array $info) {
    $v = $this->validarOrdenes('agregar pago', $info, ['id_orden_entrega_presupuesto']);
    if ($v) return $v;

    $pagos = isset($info['pagos']) && is_string($info['pagos']) ? json_decode($info['pagos'], true) : ($info['pagos'] ?? []);

    $this->idOrden = $info['id_orden_entrega_presupuesto'];
    $this->pagos   = $pagos;
    return $this->RegistrarPagoP();
  }

  // PRIVADOS

  private function ListarOrdenesP() {
    $resultado = $this->seleccionarDatos2([
      'campos' => "
        f.id_orden_entrega_presupuesto,
        c.razon_social_cliente AS CLIENTE,
        f.rif_cedula_cliente,
        f.fecha_orden_entrega_presupuesto,
        f.status,
        (SELECT COUNT(*) FROM productos_ordenes_entregas_presupuestos pf WHERE pf.id_orden_entrega_presupuesto = f.id_orden_entrega_presupuesto AND pf.status = 1) AS cant_productos,
        (SELECT COUNT(*) FROM servicios_ordenes_entregas_presupuestos sf WHERE sf.id_orden_entrega_presupuesto = f.id_orden_entrega_presupuesto AND sf.status = 1) AS cant_servicios,
        (SELECT COUNT(*) FROM deliveries d WHERE d.id_orden_entrega_presupuesto = f.id_orden_entrega_presupuesto AND d.status = 1) AS tiene_delivery,
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
      'ORDER' => 'f.fecha_orden_entrega_presupuesto DESC',
    ]);

    $filas = $resultado->fetchAll(PDO::FETCH_ASSOC);

    foreach ($filas as &$fila) {
      if ($fila['status'] == 2) {
        $fila['estado_dinamico'] = 'Anulada';
        $fila['estado_num'] = 5;
        continue;
      }

      $iva = floatval($fila['monto_cambio_iva']) / 100;
      $subTotal = floatval($fila['sub_prod']) + floatval($fila['sub_serv']) + floatval($fila['sub_del']);
      $totalOrden = $subTotal + ($subTotal * $iva);

      if ($fila['status'] == 10) {
        $fila['estado_dinamico'] = 'Procesada y Pagada';
        $fila['estado_num'] = 1;
        $pagado = $totalOrden; // Si está pagada por completo, el monto pagado ya es definitivo
        $restante = 0;
      } elseif ($fila['status'] == 11) {
        $fila['estado_dinamico'] = 'Pagada y Despachada';
        $fila['estado_num'] = 3;
        $pagado = $totalOrden; // Si está pagada por completo, el monto pagado ya es definitivo
        $restante = 0;
      } else {
        $pagado = floatval($fila['total_pagado']);
        $restante = round($totalOrden - $pagado, 2);

        if ($restante <= 0) {
          // Si se pagó todo de una vez
          if ($fila['status'] == 3) {
            $fila['estado_dinamico'] = 'Pagada y Despachada';
            $fila['estado_num'] = 3;
          } else {
            $fila['estado_dinamico'] = 'Procesada y Pagada';
            $fila['estado_num'] = 1;
          }
        } else {
          // Si todavía deben algo
          if ($fila['status'] == 3) {
            $fila['estado_dinamico'] = 'Despachada y sin Pago';
            $fila['estado_num'] = 4;
          } else {
            $fila['estado_dinamico'] = 'Procesada y sin Pago';
            $fila['estado_num'] = 2;
          }
        }
      }
      $fila['total_orden'] = $totalOrden;
      $fila['total_pagado'] = $pagado;
    }
    return $filas;
  }
  private function ObtenerOrdenP() {
    $resultado = $this->seleccionarDatos2([
      'campos' => "
        f.id_orden_entrega_presupuesto, f.rif_cedula_cliente,
        f.fecha_orden_entrega_presupuesto, f.status,
        c.razon_social_cliente AS CLIENTE
      ",
      'tabla'  => 'ordenes_entregas_presupuestos as f',
      'datosJoins' => [
        'clientes as c' => 'f.rif_cedula_cliente = c.rif_cedula_cliente',
      ],
      'WHERE' => ['f.id_orden_entrega_presupuesto' => $this->idOrden],
      'eliminadosYVigentes' => true,
    ]);

    if ($resultado->rowCount() <= 0) {
      return [
        'tipo'   => 'simple',
        'titulo' => 'Orden no encontrada',
        'texto'  => 'La orden no existe en el sistema',
        'icono'  => 'error',
      ];
    }

    return $resultado->fetch(PDO::FETCH_ASSOC);
  }
  private function ObtenerDetalleOrdenP() {
    $conexion = $this->conectar();

    // Primero buscamos los datos principales de la orden
    $stmtCab = $this->conectar()->prepare("
      SELECT f.id_orden_entrega_presupuesto, f.fecha_orden_entrega_presupuesto, f.status,
             c.razon_social_cliente AS CLIENTE,
             f.rif_cedula_cliente,
             ci.monto_cambio_iva AS IVA,
             (SELECT COALESCE(SUM(
                CASE WHEN mo.nombre_moneda = 'BÓLIVAR' OR mo.nombre_moneda = 'BS' 
                     THEN dp.monto_pago / (SELECT MAX(valor_moneda) FROM monedas WHERE nombre_moneda IN ('DÓLAR', 'DOLAR')) 
                     ELSE dp.monto_pago END
              ), 0) 
              FROM pagos pa 
              JOIN detalles_pagos dp ON pa.id_pago=dp.id_pago 
              JOIN monedas mo ON dp.id_moneda=mo.id_moneda 
              WHERE pa.id_orden_entrega_presupuesto = f.id_orden_entrega_presupuesto AND dp.status = 1 AND pa.status=1) AS total_pagado
      FROM ordenes_entregas_presupuestos f
      JOIN clientes c ON f.rif_cedula_cliente = c.rif_cedula_cliente
      JOIN cambios_iva ci ON f.id_cambio_iva = ci.id_cambio_iva
      WHERE f.id_orden_entrega_presupuesto = :id
    ");
    $stmtCab->execute([':id' => $this->idOrden]);
    $cabecera = $stmtCab->fetch(PDO::FETCH_ASSOC);

    if (!$cabecera) return [];

    // Luego buscamos qué productos se vendieron
    $stmtProd = $this->conectar()->prepare("
      SELECT pf.id_producto_factura, pf.cantidad_producto,
             p.nombre_producto, p.precio_producto,
             pr.nombre_presentacion,
             pp.id_presentacion_producto
      FROM productos_ordenes_entregas_presupuestos pf
      JOIN presentaciones_productos pp
        ON pf.id_presentacion_producto = pp.id_presentacion_producto
      JOIN productos p ON pp.id_producto = p.id_producto
      JOIN presentaciones pr ON pp.id_presentacion = pr.id_presentacion
      WHERE pf.id_orden_entrega_presupuesto = :id AND pf.status = 1
    ");
    $stmtProd->execute([':id' => $this->idOrden]);
    $productos = $stmtProd->fetchAll(PDO::FETCH_ASSOC);

    // También los servicios prestados
    $stmtServ = $this->conectar()->prepare("
      SELECT sf.id_servicio_factura, sf.id_servicio, sf.cantidad_servicio,
             sf.es_precio_mapfre, sf.precio_servicio_mapfre,
             s.nombre_servicio, s.precio_servicio,
             lat.coordenada_latitud, lon.coordenada_longitud
      FROM servicios_ordenes_entregas_presupuestos sf
      JOIN servicios s ON sf.id_servicio = s.id_servicio
      LEFT JOIN direcciones dir ON sf.id_direccion = dir.id_direccion
      LEFT JOIN latitudes_direcciones lat ON dir.id_latitud_direccion = lat.id_latitud_direccion
      LEFT JOIN longitudes_direcciones lon ON dir.id_longitud_direccion = lon.id_longitud_direccion
      WHERE sf.id_orden_entrega_presupuesto = :id AND sf.status = 1
    ");
    $stmtServ->execute([':id' => $this->idOrden]);
    $servicios = $stmtServ->fetchAll(PDO::FETCH_ASSOC);

    // Ojo, para cada servicio necesitamos saber cuantos materiales gast�
    $stmtMat = $this->conectar()->prepare("
      SELECT ps.id_producto, ps.cantidad_producto,
             p.nombre_producto, um.nombre_unidad_medida
      FROM productos_servicios ps
      JOIN productos p ON ps.id_producto = p.id_producto
      LEFT JOIN unidades_medidas um ON p.id_unidad_medida = um.id_unidad_medida
      WHERE ps.id_servicio = :serv AND ps.status = 1
    ");
    foreach ($servicios as &$serv) {
      $stmtMat->execute([':serv' => $serv['id_servicio']]);
      $serv['materiales'] = $stmtMat->fetchAll(PDO::FETCH_ASSOC);
    }
    unset($serv);

    // Y por último vemos si tiene algún viaje de delivery asignado
    $stmtDel = $this->conectar()->prepare("
      SELECT d.id_delivery, d.cedula_repartidor,
             r.nombre_ruta, r.precio_ruta,
             lat.coordenada_latitud, lon.coordenada_longitud,
             CONCAT(rep.nombre_repartidor,' ',rep.apellido_repartidor)
               AS REPARTIDOR
      FROM deliveries d
      JOIN direcciones dir ON d.id_direccion = dir.id_direccion
      JOIN rutas r ON dir.id_ruta = r.id_ruta
      LEFT JOIN latitudes_direcciones lat
        ON dir.id_latitud_direccion = lat.id_latitud_direccion
      LEFT JOIN longitudes_direcciones lon
        ON dir.id_longitud_direccion = lon.id_longitud_direccion
      LEFT JOIN repartidores rep
        ON d.cedula_repartidor = rep.cedula_repartidor
      WHERE d.id_orden_entrega_presupuesto = :id AND d.status = 1
    ");
    $stmtDel->execute([':id' => $this->idOrden]);
    $delivery = $stmtDel->fetch(PDO::FETCH_ASSOC);

    // Acomodamos cómo se va a ver el estado de la orden basándonos en si ya pagaron o no
    if ($cabecera['status'] == 2) {
      $cabecera['estado_dinamico'] = 'Anulada';
      $cabecera['estado_num'] = 5;
    } else {
      $iva = floatval($cabecera['IVA']) / 100;

      $subProd = 0;
      foreach ($productos as $p) $subProd += ($p['cantidad_producto'] * $p['precio_producto']);

      $subServ = 0;
      foreach ($servicios as $s) {
        $precio = $s['es_precio_mapfre'] == 1 ? $s['precio_servicio_mapfre'] : $s['precio_servicio'];
        $subServ += ($s['cantidad_servicio'] * $precio);
      }
      $subDel = 0;
      if ($delivery) {
        $precioRuta = floatval($delivery['precio_ruta']);
        $parts = explode('|', $delivery['coordenada_latitud']);
        if (count($parts) > 1) {
          $distancia = floatval($parts[1]);
          $subDel = $precioRuta * $distancia;
          $delivery['coordenada_latitud'] = $parts[0]; // Le quitamos la parte extra a las coordenadas para que el mapa no se vuelva loco
        } else {
          $subDel = $precioRuta;
        }
      }

      $subTotal = $subProd + $subServ + $subDel;
      $totalOrden = $subTotal + ($subTotal * $iva);

      if ($cabecera['status'] == 10) {
        $cabecera['estado_dinamico'] = 'Procesada y Pagada';
        $cabecera['estado_num'] = 1;
        $pagado = $totalOrden;
        $restante = 0;
      } elseif ($cabecera['status'] == 11) {
        $cabecera['estado_dinamico'] = 'Pagada y Despachada';
        $cabecera['estado_num'] = 3;
        $pagado = $totalOrden;
        $restante = 0;
      } else {
        $pagado = floatval($cabecera['total_pagado']);
        $restante = round($totalOrden - $pagado, 2);

        if ($restante <= 0) {
          if ($cabecera['status'] == 3) {
            $cabecera['estado_dinamico'] = 'Pagada y Despachada';
            $cabecera['estado_num'] = 3;
          } else {
            $cabecera['estado_dinamico'] = 'Procesada y Pagada';
            $cabecera['estado_num'] = 1;
          }
        } else {
          if ($cabecera['status'] == 3) {
            $cabecera['estado_dinamico'] = 'Despachada y sin Pago';
            $cabecera['estado_num'] = 4;
          } else {
            $cabecera['estado_dinamico'] = 'Procesada y sin Pago';
            $cabecera['estado_num'] = 2;
          }
        }
      }
      $cabecera['total_orden'] = $totalOrden;
      $cabecera['total_pagado'] = $pagado;
      $cabecera['restante'] = $restante;
    }

    // Guardamos cuánto costó de verdad el delivery sumando kilómetros
    if ($delivery) {
      $delivery['costo_delivery_total'] = $subDel ?? floatval($delivery['precio_ruta']);
    }

    return [
      'cabecera'  => $cabecera,
      'productos' => $productos,
      'servicios' => $servicios,
      'delivery'  => $delivery ?: null,
    ];
  }
  private function RegistrarOrdenP() {
    $objBitacora = new bitacoraModelo();

    try {
      // Buscamos cuánto es el IVA en este momento
      $this->idCambioIva = $this->obtenerIVAActualP();

      // Armamos un código bonito y único para esta orden
      $this->idOrden = $this->generarCodSeg([
        'tablaBD' => 'ordenes_entregas_presupuestos',
        'prefijo' => 'OEP',
        'campoID' => 'id_orden_entrega_presupuesto',
      ]);

      // Guardamos los datos principales en la base de datos primero
      $idFact = $this->guardarDatos2([
        'tabla' => 'ordenes_entregas_presupuestos',
        'datos' => [
          'id_orden_entrega_presupuesto'        => $this->idOrden,
          'cedula_usuario'    => $this->cedulaUsuario,
          'id_cambio_iva'     => $this->idCambioIva,
          'rif_cedula_cliente' => $this->rifCliente,
          'fecha_orden_entrega_presupuesto'     => $this->fechaOrden,
          'status'            => $this->status,
        ],
        'WHERE' => ['id_orden_entrega_presupuesto' => $this->idOrden],
      ]);

      if (!$idFact) {
        throw new Exception("No se pudo crear la orden principal");
      }

      // Ahora sí, vamos guardando uno por uno los productos
      foreach ($this->productos as $p) {
        $idPresentacion = $p['id_presentacion_producto'] ?? '';
        $cantidad       = (float)($p['cantidad'] ?? 0);

        if (empty($idPresentacion) || $cantidad <= 0) continue;

        // Trait v2 para consultar stock de la presentación
        $resultadoStock = $this->seleccionarDatos2([
          'campos'     => 'p.stock_producto, pp.id_producto, pre.cantidad_pmp',
          'tabla'      => 'presentaciones_productos AS pp',
          'datosJoins' => [
            'productos p' => 'pp.id_producto = p.id_producto',
            'presentaciones pre' => 'pp.id_presentacion = pre.id_presentacion'
          ],
          'WHERE'      => [
            'pp.id_presentacion_producto' => $idPresentacion,
            'pp.status'                   => 1
          ]
        ]);
        $datosProd = $resultadoStock->fetch(PDO::FETCH_ASSOC);

        if (!$datosProd) {
          throw new Exception(
            "Presentación de producto no encontrada: $idPresentacion"
          );
        }

        $capacidad = (float)($datosProd['cantidad_pmp'] ?? 1);
        $volumenRequerido = $cantidad * $capacidad;

        if ($datosProd['stock_producto'] < $volumenRequerido) {
          throw new Exception(
            "Stock insuficiente para el producto ID: "
              . $datosProd['id_producto'] . " (Se requieren $volumenRequerido)"
          );
        }

        // Trait v2 para insertar en productos_ordenes_entregas_presupuestos
        $this->guardarDatos2([
          'tabla' => 'productos_ordenes_entregas_presupuestos',
          'datos' => [
            'id_orden_entrega_presupuesto'                => $this->idOrden,
            'id_presentacion_producto' => $idPresentacion,
            'cantidad_producto'         => $cantidad,
            'status'                    => 1
          ]
        ]);

        // Nota: Mantenemos raw SQL debido al uso de la función de base de datos GREATEST() para prevenir stock negativo.
        $this->conectar()->prepare("
          UPDATE productos SET stock_producto = GREATEST(stock_producto - :cant, 0)
          WHERE id_producto = :id
        ")->execute([
          ':cant' => $volumenRequerido,
          ':id'   => $datosProd['id_producto'],
        ]);
      }

      // Toca guardar los servicios de la misma manera
      foreach ($this->servicios as $s) {
        $idServicio    = $s['id_servicio']         ?? '';
        $cantidad      = (float)($s['cantidad']    ?? 1);
        $esMapfre      = (int)($s['es_mapfre']     ?? 0);
        $precioMapfre  = (float)($s['precio_mapfre'] ?? 0);

        // Datos de ubicación y fecha del servicio
        $fechaEjecucion = $s['fecha_ejecucion'] ?? date('Y-m-d H:i:s');
        $latitud        = $s['latitud'] ?? '';
        $longitud       = $s['longitud'] ?? '';
        $idRuta         = (int)($s['id_ruta'] ?? 0);

        if (empty($idServicio)) continue;

        // Si el servicio tiene coordenadas y ruta asignadas, creamos su dirección
        $idDireccion = null;
        if ($idRuta > 0 && $latitud !== '' && $longitud !== '') {
          $idLat = $this->guardarDatos2(['tabla' => 'latitudes_direcciones', 'datos' => ['coordenada_latitud' => $latitud, 'status' => 1]]);
          $idLng = $this->guardarDatos2(['tabla' => 'longitudes_direcciones', 'datos' => ['coordenada_longitud' => $longitud, 'status' => 1]]);
          
          $idDireccion = $this->guardarDatos2([
            'tabla' => 'direcciones',
            'datos' => [
              'id_latitud_direccion' => $idLat,
              'id_longitud_direccion' => $idLng,
              'id_ruta' => $idRuta,
              'status' => 1
            ]
          ]);
        }

        // Trait v2 para insertar en servicios_ordenes_entregas_presupuestos
        $this->guardarDatos2([
          'tabla' => 'servicios_ordenes_entregas_presupuestos',
          'datos' => [
            'id_orden_entrega_presupuesto' => $this->idOrden,
            'id_servicio'                  => $idServicio,
            'id_direccion'                 => $idDireccion,
            'fecha_ejecucion'              => $fechaEjecucion,
            'cantidad_servicio'            => $cantidad,
            'es_precio_mapfre'             => $esMapfre,
            'precio_servicio_mapfre'       => $esMapfre ? $precioMapfre : 0,
            'status'                       => 1
          ]
        ]);

        // Trait v2 para buscar productos del servicio
        $resultadoMP = $this->seleccionarDatos2([
          'campos' => 'id_producto, cantidad_producto',
          'tabla'  => 'productos_servicios',
          'WHERE'  => [
            'id_servicio' => $idServicio,
            'status'      => 1
          ]
        ]);
        $materialesProd = $resultadoMP->fetchAll(PDO::FETCH_ASSOC);

        foreach ($materialesProd as $mp) {
          // Nota: Mantenemos raw SQL debido al uso de GREATEST() para prevenir stock negativo.
          $this->conectar()->prepare("
            UPDATE productos
            SET stock_producto = GREATEST(stock_producto - :cant, 0)
            WHERE id_producto = :id
          ")->execute([
            ':cant' => $mp['cantidad_producto'] * $cantidad,
            ':id'   => $mp['id_producto'],
          ]);
        }
      }

      // Revisamos si además pidieron delivery
      if (!empty($this->delivery) && !empty($this->delivery['id_ruta'])) {
        $idRuta = (int)($this->delivery['id_ruta'] ?? 0);
        $latitud = $this->delivery['latitud'] ?? '';
        $longitud = $this->delivery['longitud'] ?? '';

        if ($idRuta > 0 && $latitud !== '' && $longitud !== '') {
          // Trait v2 para guardar latitudes_direcciones
          $idLatitud = $this->guardarDatos2([
            'tabla' => 'latitudes_direcciones',
            'datos' => [
              'coordenada_latitud' => $latitud,
              'status'             => 1
            ]
          ]);

          // Trait v2 para guardar longitudes_direcciones
          $idLongitud = $this->guardarDatos2([
            'tabla' => 'longitudes_direcciones',
            'datos' => [
              'coordenada_longitud' => $longitud,
              'status'              => 1
            ]
          ]);

          // Trait v2 para guardar direcciones
          $idDireccion = $this->guardarDatos2([
            'tabla' => 'direcciones',
            'datos' => [
              'id_latitud_direccion'  => $idLatitud,
              'id_longitud_direccion' => $idLongitud,
              'id_ruta'               => $idRuta,
              'status'                => 1
            ]
          ]);

          // Le hacemos también su propio código de control al viaje
          $idDelivery = $this->generarCodSeg([
            'tablaBD' => 'deliveries',
            'prefijo' => 'DLVR',
            'campoID' => 'id_delivery',
          ]);

          $cedulaRepartidor = $this->delivery['cedula_repartidor'] ?? null;

          // Trait v2 para guardar deliveries
          $this->guardarDatos2([
            'tabla' => 'deliveries',
            'datos' => [
              'id_delivery'       => $idDelivery,
              'id_orden_entrega_presupuesto'        => $this->idOrden,
              'id_direccion'      => $idDireccion,
              'cedula_repartidor' => $cedulaRepartidor,
              'status'            => 1
            ]
          ]);
        }
      }

      // Dejamos registro en la bitácora de que todo salió súper bien
      $objBitacora->registrarBitacora([
        'modulo' => 'ordenesEntregasPresupuestos',
        'accion' => "Registrar Orden",
        'resultado' => 'exitoso'
      ]);

      $this->commit();

      $objNot = new mensajesWSModelo();
      $objNot->enviarMensajesWS([
        "receptor" => [
          'tipo' => 'rol',
          'rol' => 'ADMINISTRADOR'
        ],
        'cuerpo' => [
          [
            'accion' => "borrarDataModuloSS",
            'modulo' => 'ordenesEntregasPresupuestos'
          ],
          [
            'accion' => 'alertar',
            'alerta' => [
              'tipo' => 'simple',
              'titulo' => 'Nueva Orden',
              'texto' => "Se ha registrado la orden {$this->idOrden}",
              'icono' => 'info',
              'notifier' => true,
              'tiempo' => 3000
            ]
          ],
          [
            'accion' => "actDT",
            'modulo' => 'ordenesEntregasPresupuestos'
          ],
        ]
      ]);

      return [
        'tipo'        => 'limpiar',
        'titulo'      => 'Orden Registrada',
        'texto'       => "Orden {$this->idOrden} registrada correctamente.",
        'icono'       => 'success',
        'id_orden_entrega_presupuesto'  => $this->idOrden,
      ];
    } catch (Exception $e) {
      // Si algo se rompió, anotamos en la bitácora el error para revisarlo luego
      $objBitacora->registrarBitacora([
        'modulo' => 'ordenesEntregasPresupuestos',
        'accion' => "Registrar Orden",
        'resultado' => 'error'
      ]);

      $this->rollback();

      return [
        'tipo'   => 'simple',
        'titulo' => 'Error al Registrar',
        'texto'  => "No se pudo registrar la orden: " . $e->getMessage(),
        'icono'  => 'error',
      ];
    }
  }
  private function DespacharOrdenP() {
    $resultado = $this->seleccionarDatos2([
      'campos' => 'status',
      'tabla'  => 'ordenes_entregas_presupuestos',
      'WHERE'  => ['id_orden_entrega_presupuesto' => $this->idOrden]
    ]);
    $fila = $resultado->fetch(PDO::FETCH_ASSOC);

    if (!$fila) {
      return ['tipo' => 'simple', 'titulo' => 'Error', 'texto' => 'La orden no existe', 'icono' => 'error'];
    }

    $statusActual = intval($fila['status']);

    // Verificamos que tenga un estado válido antes de cambiar a despachada
    if ($statusActual == 2) {
      return ['tipo' => 'simple', 'titulo' => 'No permitido', 'texto' => 'No se puede despachar una orden anulada', 'icono' => 'warning'];
    }
    if ($statusActual == 3 || $statusActual == 11) {
      return ['tipo' => 'simple', 'titulo' => 'Ya despachada', 'texto' => 'Esta orden ya fue despachada anteriormente', 'icono' => 'info'];
    }

    // Si no estaba pagada completa, pasa a despachada sin pagar
    // Pero si ya habían pagado todo, la marcamos como despachada y pagada
    $nuevoStatus = ($statusActual == 10) ? 11 : 3;

    try {
      $conexion = $this->conectar();

      $this->actualizarDatos2([
        'tabla' => 'ordenes_entregas_presupuestos',
        'datos' => ['status' => $nuevoStatus],
        'WHERE' => ['id_orden_entrega_presupuesto' => $this->idOrden]
      ]);

      $objBitacora = new bitacoraModelo();
      $estadoTexto = ($nuevoStatus == 11) ? 'Pagada y Despachada' : 'Despachada y sin Pago';
      $objBitacora->registrarBitacora([
        'modulo' => 'ordenesEntregasPresupuestos',
        'accion' => "Despachar Orden",
        'resultado' => 'exitoso'
      ]);

      $this->commit();

      $objNot = new mensajesWSModelo();
      $objNot->enviarMensajesWS([
        "receptor" => [
          'tipo' => 'rol',
          'rol' => 'ADMINISTRADOR'
        ],
        'cuerpo' => [
          [
            'accion' => "borrarDataModuloSS",
            'modulo' => 'ordenesEntregasPresupuestos'
          ],
          [
            'accion' => 'alertar',
            'alerta' => [
              'tipo' => 'simple',
              'titulo' => 'Orden Despachada',
              'texto' => "La orden {$this->idOrden} fue despachada.",
              'icono' => 'info',
              'notifier' => true,
              'tiempo' => 3000
            ]
          ],
          [
            'accion' => "actDT",
            'modulo' => 'ordenesEntregasPresupuestos'
          ],
        ]
      ]);

      return [
        'tipo'   => 'simple',
        'titulo' => 'Orden Despachada',
        'texto'  => "La orden {$this->idOrden} fue marcada como \"{$estadoTexto}\".",
        'icono'  => 'success',
      ];
    } catch (\Exception $e) {
      $this->rollback();
      $objBitacora = new bitacoraModelo();
      $objBitacora->registrarBitacora([
        'modulo' => 'ordenesEntregasPresupuestos',
        'accion' => "Despachar Orden",
        'resultado' => 'error'
      ]);
      return [
        'tipo'   => 'simple',
        'titulo' => 'Error al Despachar',
        'texto'  => 'No se pudo despachar la orden: ' . $e->getMessage(),
        'icono'  => 'error',
      ];
    }
  }
  private function AnularOrdenP() {
    $objBitacora = new bitacoraModelo();

    try {
      // No podemos anular algo que ya está anulado, ¿cierto?
      $stmt = $this->conectar()->prepare(
        "SELECT status FROM ordenes_entregas_presupuestos WHERE id_orden_entrega_presupuesto = :id"
      );
      $stmt->execute([':id' => $this->idOrden]);
      $fila = $stmt->fetch(PDO::FETCH_ASSOC);

      if (!$fila || $fila['status'] == 2) {
        return [
          'tipo'   => 'simple',
          'titulo' => 'Orden ya anulada',
          'texto'  => 'Esta orden ya fue anulada anteriormente',
          'icono'  => 'warning',
        ];
      }

      // Le cambiamos el estado al número 2, que es nuestro código secreto para anulada
      $resultado = $this->actualizarDatos2([
        'tabla' => 'ordenes_entregas_presupuestos',
        'datos' => ['status' => 2],
        'WHERE' => ['id_orden_entrega_presupuesto' => $this->idOrden],
      ]);

      if (!$resultado || $resultado <= 0) {
        throw new Exception("No se pudo anular la orden");
      }

      // Devolvemos al inventario todos los productos que se habían llevado
      $stmtProd = $this->conectar()->prepare("
        SELECT pf.cantidad_producto, pp.id_producto, pre.cantidad_pmp
        FROM productos_ordenes_entregas_presupuestos pf
        JOIN presentaciones_productos pp
          ON pf.id_presentacion_producto = pp.id_presentacion_producto
        JOIN presentaciones pre
          ON pp.id_presentacion = pre.id_presentacion
        WHERE pf.id_orden_entrega_presupuesto = :id AND pf.status = 1
      ");
      $stmtProd->execute([':id' => $this->idOrden]);
      $prods = $stmtProd->fetchAll(PDO::FETCH_ASSOC);

      foreach ($prods as $p) {
        $this->conectar()->prepare("
          UPDATE productos
          SET stock_producto = stock_producto + :cant
          WHERE id_producto = :id
        ")->execute([
          ':cant' => $p['cantidad_producto'] * ($p['cantidad_pmp'] ?? 1),
          ':id'   => $p['id_producto'],
        ]);
      }

      // Hacemos lo mismo con los materiales que se usaron en los servicios
      $stmtServ = $this->conectar()->prepare("
        SELECT sf.id_servicio, sf.cantidad_servicio
        FROM servicios_ordenes_entregas_presupuestos sf
        WHERE sf.id_orden_entrega_presupuesto = :id AND sf.status = 1
      ");
      $stmtServ->execute([':id' => $this->idOrden]);
      $servs = $stmtServ->fetchAll(PDO::FETCH_ASSOC);

      foreach ($servs as $s) {
        $stmtMP = $this->conectar()->prepare("
          SELECT id_producto, cantidad_producto
          FROM productos_servicios
          WHERE id_servicio = :serv AND status = 1
        ");
        $stmtMP->execute([':serv' => $s['id_servicio']]);
        $mps = $stmtMP->fetchAll(PDO::FETCH_ASSOC);

        foreach ($mps as $mp) {
          $this->conectar()->prepare("
            UPDATE productos
            SET stock_producto = stock_producto + :cant
            WHERE id_producto = :id
          ")->execute([
            ':cant' => $mp['cantidad_producto'] * $s['cantidad_servicio'],
            ':id'   => $mp['id_producto'],
          ]);
        }
      }

      // Guardar en bitácora
      $objBitacora->registrarBitacora([
        'modulo' => 'ordenesEntregasPresupuestos',
        'accion' => "Anular Orden",
        'resultado' => 'exitoso'
      ]);

      $this->commit();

      $objNot = new mensajesWSModelo();
      $objNot->enviarMensajesWS([
        "receptor" => [
          'tipo' => 'rol',
          'rol' => 'ADMINISTRADOR'
        ],
        'cuerpo' => [
          [
            'accion' => "borrarDataModuloSS",
            'modulo' => 'ordenesEntregasPresupuestos'
          ],
          [
            'accion' => 'alertar',
            'alerta' => [
              'tipo' => 'simple',
              'titulo' => 'Orden Anulada',
              'texto' => "La orden {$this->idOrden} fue anulada.",
              'icono' => 'warning',
              'notifier' => true,
              'tiempo' => 3000
            ]
          ],
          [
            'accion' => "actDT",
            'modulo' => 'ordenesEntregasPresupuestos'
          ],
        ]
      ]);

      return [
        'tipo'   => 'simple',
        'titulo' => 'Orden Anulada',
        'texto'  => "La orden {$this->idOrden} fue anulada y el stock restaurado.",
        'icono'  => 'success',
      ];
    } catch (Exception $e) {
      $objBitacora->registrarBitacora([
        'modulo' => 'ordenesEntregasPresupuestos',
        'accion' => "Anular Orden",
        'resultado' => 'error'
      ]);
      $this->rollback();

      return [
        'tipo'   => 'simple',
        'titulo' => 'Error al Anular',
        'texto'  => "No se pudo anular la orden: " . $e->getMessage(),
        'icono'  => 'error',
      ];
    }
  }
  private function ListarMetodosPagoP() {
    $resultado = $this->seleccionarDatos2([
      'campos' => '*',
      'tabla'  => 'metodos_pagos',
      'WHERE'  => ['status' => 1]
    ]);
    return $resultado->fetchAll(PDO::FETCH_ASSOC);
  }
  private function obtenerIVAActualP(): int {
    $resultado = $this->seleccionarDatos2([
      'campos' => 'id_cambio_iva',
      'tabla'  => 'cambios_iva',
      'WHERE'  => ['status' => 1],
      'ORDER'  => 'id_cambio_iva DESC',
      'LIMIT'  => 1
    ]);
    $fila = $resultado->fetch(PDO::FETCH_ASSOC);
    return $fila ? (int)$fila['id_cambio_iva'] : 1;
  }
  private function RegistrarPagoP() {
    if (empty($this->pagos)) {
      return ['tipo' => 'simple', 'titulo' => 'Error', 'texto' => 'Debe ingresar al menos un detalle de pago.', 'icono' => 'warning'];
    }

    try {
      $conexion = $this->conectar();

      $idPago = $this->generarCodSeg([
        'tablaBD' => 'pagos',
        'prefijo' => 'PAG',
        'campoID' => 'id_pago',
      ]);

      $fechaPago = $this->FechaHora_Sel('fecha_hora_BD');

      // Usamos el trait v2 para insertar en pagos
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
        throw new Exception("No se pudo registrar el pago principal.");
      }

      // Obtener el ID de la moneda principal (identificada por tener valor 1.00 base)
      $stmtMo = $this->conectar()->query("SELECT id_moneda FROM monedas WHERE valor_moneda = 1 LIMIT 1");
      $mo = $stmtMo->fetch(PDO::FETCH_ASSOC);
      $idMonedaBolivar = $mo ? $mo['id_moneda'] : 2;

      foreach ($this->pagos as $pago) {
        $idMetodo = $pago['id_metodo_pago'] ?? '';
        $idMoneda = $pago['id_moneda'] ?? '';
        $monto    = floatval($pago['monto_pago'] ?? 0);

        // Métodos como Pago Móvil no requieren selección de moneda; por defecto es Bolívar
        if (empty($idMoneda)) {
          $idMoneda = $idMonedaBolivar;
        }

        if (empty($idMetodo) || $monto <= 0) {
          throw new Exception("Datos de pago inválidos.");
        }

        // Usamos el trait v2 para insertar detalles_pagos
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
          throw new Exception("No se pudo registrar el detalle de pago.");
        }
      }

      $objBitacora = new bitacoraModelo();
      $objBitacora->registrarBitacora([
        'modulo' => 'ordenesEntregasPresupuestos',
        'accion' => "Registrar Pago",
        'resultado' => 'exitoso'
      ]);

      // Verificar si con este pago se salda la orden por completo (Query compleja, se mantiene cruda)
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
      $stmtCheck->execute([':id' => $this->idOrden]);
      $f = $stmtCheck->fetch(PDO::FETCH_ASSOC);
      if ($f) {
        $iva = floatval($f['monto_cambio_iva']) / 100;
        $sub = floatval($f['sub_prod']) + floatval($f['sub_serv']) + floatval($f['sub_del']);
        $tot = round($sub + ($sub * $iva), 2);
        $pag = floatval($f['total_pagado']);
        if (round($tot - $pag, 2) <= 0.01) {
          $newStatus = ($f['status'] == 3 || $f['status'] == 11) ? 11 : 10;
          $this->actualizarDatos2([
            'tabla' => 'ordenes_entregas_presupuestos',
            'datos' => ['status' => $newStatus],
            'WHERE' => ['id_orden_entrega_presupuesto' => $this->idOrden]
          ]);
        }
      }

      // --- Comprobantes de Pago ---
      if (isset($_FILES['comprobantes']) && !empty($_FILES['comprobantes']['name'][0])) {
        // Verificar cuántos comprobantes tiene ya la orden
        $stmtC = $this->conectar()->prepare("
          SELECT COUNT(cp.id_comprobante_pago) as total 
          FROM comprobantes_pagos cp 
          JOIN pagos p ON cp.id_pago = p.id_pago 
          WHERE p.id_orden_entrega_presupuesto = :id AND cp.status = 1
        ");
        $stmtC->execute([':id' => $this->idOrden]);
        $rowC = $stmtC->fetch(PDO::FETCH_ASSOC);
        $totalActual = (int)($rowC['total'] ?? 0);

        $nuevos = count($_FILES['comprobantes']['name']);
        if ($totalActual + $nuevos > 3) {
          throw new Exception("Límite de comprobantes excedido. La orden permite un máximo de 3 comprobantes (actuales: $totalActual, intentando subir: $nuevos).");
        }

        $dirComprobantes = DIR_FOTOS . "comprobantes_pagos/";
        if (!is_dir($dirComprobantes)) {
          mkdir($dirComprobantes, 0777, true);
        }

        for ($i = 0; $i < $nuevos; $i++) {
          $nombreFile = $_FILES['comprobantes']['name'][$i];
          $tmpFile = $_FILES['comprobantes']['tmp_name'][$i];
          $errorFile = $_FILES['comprobantes']['error'][$i];
          $ext = strtolower(pathinfo($nombreFile, PATHINFO_EXTENSION));

          if ($errorFile !== UPLOAD_ERR_OK) {
            throw new Exception("Error al subir el archivo $nombreFile");
          }
          if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
            throw new Exception("Formato no permitido para $nombreFile. Solo se permiten JPG y PNG.");
          }

          $nuevoNombre = $this->idOrden . "_pago_" . $idPago . "_" . time() . "_" . $i . "." . $ext;
          if (!move_uploaded_file($tmpFile, $dirComprobantes . $nuevoNombre)) {
            throw new Exception("No se pudo guardar el archivo $nombreFile");
          }

          $idComprobante = $this->guardarDatos2([
            'tabla' => 'comprobantes_pagos',
            'datos' => [
              'id_pago' => $idPago,
              'path_comprobante' => $nuevoNombre,
              'status' => 1
            ]
          ]);
          if (!$idComprobante) {
            throw new Exception("Error al registrar en BD el comprobante $nombreFile");
          }
        }
      }
      // --- Fin Comprobantes de Pago ---

      $this->commit();

      $objNot = new mensajesWSModelo();
      $objNot->enviarMensajesWS([
        "receptor" => [
          'tipo' => 'rol',
          'rol' => 'ADMINISTRADOR'
        ],
        'cuerpo' => [
          [
            'accion' => "borrarDataModuloSS",
            'modulo' => 'ordenesEntregasPresupuestos'
          ],
          [
            'accion' => 'alertar',
            'alerta' => [
              'tipo' => 'simple',
              'titulo' => 'Pago Recibido',
              'texto' => "Se ha abonado un pago a la orden {$this->idOrden}.",
              'icono' => 'info',
              'notifier' => true,
              'tiempo' => 3000
            ]
          ],
          [
            'accion' => "actDT",
            'modulo' => 'ordenesEntregasPresupuestos'
          ],
        ]
      ]);

      return [
        'tipo'   => 'simple',
        'titulo' => 'Pago Registrado',
        'texto'  => 'El pago ha sido procesado exitosamente.',
        'icono'  => 'success',
      ];
    } catch (Exception $e) {
      $this->rollback();
      $objBitacora = new bitacoraModelo();
      $objBitacora->registrarBitacora([
        'modulo' => 'ordenesEntregasPresupuestos',
        'accion' => "Registrar Pago",
        'resultado' => 'error'
      ]);
      return [
        'tipo'   => 'simple',
        'titulo' => 'Error Interno',
        'texto'  => 'Hubo un problema al registrar el pago: ' . $e->getMessage(),
        'icono'  => 'error',
      ];
    }
  }
}

