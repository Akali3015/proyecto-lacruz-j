<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use PDO;
use Exception;

class facturacionModelo extends conexion {
  private string $idFactura = '';
  private string $cedulaUsuario = '';
  private string $rifCliente = '';
  private string $fechaFactura = '';
  private int $idCambioIva = 0;
  private int $status = 1;
  private array $productos = [];
  private array $servicios = [];
  private array $delivery = [];
  private array $pagos = [];

  public function ListarFacturas() {
    return $this->ListarFacturasP();
  }
  public function ObtenerFactura($id) {
    $alerta = $this->limpiar_Verificar([
      [
        'campo_nombre'    => 'id_factura',
        'campo_valor'     => &$id,
        'formulario_nombre' => 'ID de Factura',
        'requerido'       => true,
        'minimo'          => 3,
        'maximo'          => 20,
      ],
    ]);
    if ($alerta) return $alerta;

    $this->idFactura = $id;
    return $this->ObtenerFacturaP();
  }
  public function ObtenerDetalleFactura($id) {
    $alerta = $this->limpiar_Verificar([
      [
        'campo_nombre'    => 'id_factura',
        'campo_valor'     => &$id,
        'formulario_nombre' => 'ID de Factura',
        'requerido'       => true,
        'minimo'          => 3,
        'maximo'          => 20,
      ],
    ]);
    if ($alerta) return $alerta;

    $this->idFactura = $id;
    return $this->ObtenerDetalleFacturaP();
  }
  public function RegistrarFactura(
    string $rifCliente,
    array $productos,
    array $servicios,
    array $delivery,
    int $estadoSel = 1
  ) {
    $alerta = $this->limpiar_Verificar([
      [
        'campo_nombre'     => 'rif_cedula_cliente',
        'campo_valor'      => &$rifCliente,
        'formulario_nombre' => 'Cliente',
        'requerido'        => true,
        'minimo'           => minRegexCedulaRifLetra,
        'maximo'           => maxRegexCedulaRifLetra,
        'expresion_re'     => regexCedulaRifLetra,
        'tabla'            => 'clientes',
        'debeExistir'      => true,
      ],
    ]);
    if ($alerta) return $alerta;

    // No tiene sentido hacer una factura vacía, ¿verdad? Así que revisamos que lleve algo
    if (empty($productos) && empty($servicios)) {
      return [
        'tipo'   => 'simple',
        'titulo' => 'Sin artículos',
        'texto'  => 'Debe agregar al menos un producto o servicio',
        'icono'  => 'warning',
      ];
    }

    $this->rifCliente    = $rifCliente;
    $this->productos     = $productos;
    $this->servicios     = $servicios;
    $this->delivery      = $delivery;
    $this->cedulaUsuario = $_SESSION['cedula'] ?? '';
    $this->fechaFactura  = $this->FechaHora_Sel('fecha_hora_BD');

    // Dependiendo de lo que elijan, la guardamos como procesada normal o ya en camino
    if ($estadoSel == 3 || $estadoSel == 4) {
      $this->status = 3;
    } else {
      $this->status = 1;
    }

    return $this->RegistrarFacturaP();
  }
  public function DespacharFactura($id) {
    $alerta = $this->limpiar_Verificar([
      [
        'campo_nombre'      => 'id_factura',
        'campo_valor'       => &$id,
        'formulario_nombre' => 'ID de Factura',
        'requerido'         => true,
        'minimo'            => 3,
        'maximo'            => 20,
        'tabla'             => 'facturas',
        'debeExistir'       => true,
      ],
    ]);
    if ($alerta) return $alerta;

    $this->idFactura = $id;
    return $this->DespacharFacturaP();
  }
  public function AnularFactura($id) {
    $alerta = $this->limpiar_Verificar([
      [
        'campo_nombre'     => 'id_factura',
        'campo_valor'      => &$id,
        'formulario_nombre' => 'ID de Factura',
        'requerido'        => true,
        'minimo'           => 3,
        'maximo'           => 20,
        'tabla'            => 'facturas',
        'debeExistir'      => true,
      ],
    ]);
    if ($alerta) return $alerta;

    $this->idFactura = $id;
    return $this->AnularFacturaP();
  }
  public function ListarMetodosPago() {
    return $this->ListarMetodosPagoP();
  }
  public function RegistrarPago($idFactura, $pagosArray) {
    $alerta = $this->limpiar_Verificar([
      [
        'campo_nombre'      => 'id_factura',
        'campo_valor'       => &$idFactura,
        'formulario_nombre' => 'ID de Factura',
        'requerido'         => true,
        'minimo'            => 3,
        'maximo'            => 20,
        'tabla'             => 'facturas',
        'debeExistir'       => true,
      ],
    ]);
    if ($alerta) return $alerta;

    $this->idFactura = $idFactura;
    $this->pagos     = $pagosArray;
    return $this->RegistrarPagoP();
  }

  private function ListarFacturasP() {
    $resultado = $this->seleccionarDatos2([
      'campos' => "
        f.id_factura,
        c.razon_social_cliente AS CLIENTE,
        f.rif_cedula_cliente,
        f.fecha_factura,
        f.status,
        (SELECT COUNT(*) FROM productos_facturas pf WHERE pf.id_factura = f.id_factura AND pf.status = 1) AS cant_productos,
        (SELECT COUNT(*) FROM servicios_facturas sf WHERE sf.id_factura = f.id_factura AND sf.status = 1) AS cant_servicios,
        (SELECT COUNT(*) FROM deliveries d WHERE d.id_factura = f.id_factura AND d.status = 1) AS tiene_delivery,
        ci.monto_cambio_iva,
        (SELECT COALESCE(SUM(pf.cantidad_producto * p.precio_producto), 0) 
         FROM productos_facturas pf 
         JOIN presentaciones_productos pp ON pf.id_presentacion_producto=pp.id_presentacion_producto 
         JOIN productos p ON pp.id_producto = p.id_producto 
         WHERE pf.id_factura = f.id_factura AND pf.status = 1) AS sub_prod,
        (SELECT COALESCE(SUM(sf.cantidad_servicio * CASE WHEN sf.es_precio_mapfre=1 THEN sf.precio_servicio_mapfre ELSE s.precio_servicio END), 0) 
         FROM servicios_facturas sf 
         JOIN servicios s ON sf.id_servicio=s.id_servicio 
         WHERE sf.id_factura = f.id_factura AND sf.status = 1) AS sub_serv,
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
         WHERE d.id_factura = f.id_factura AND d.status = 1) AS sub_del,
        (SELECT COALESCE(SUM(
           CASE WHEN mo.nombre_moneda = 'BÓLIVAR' OR mo.nombre_moneda = 'BS' 
                THEN dp.monto_pago / (SELECT MAX(valor_moneda) FROM monedas WHERE nombre_moneda IN ('DÓLAR', 'DOLAR')) 
                ELSE dp.monto_pago END
         ), 0) 
         FROM pagos pa 
         JOIN detalles_pagos dp ON pa.id_pago=dp.id_pago 
         JOIN monedas mo ON dp.id_moneda=mo.id_moneda 
         WHERE pa.id_factura = f.id_factura AND dp.status = 1 AND pa.status=1) AS total_pagado
      ",
      'tabla' => 'facturas as f',
      'datosJoins' => [
        'clientes as c' => 'f.rif_cedula_cliente = c.rif_cedula_cliente',
        'cambios_iva as ci' => 'f.id_cambio_iva = ci.id_cambio_iva',
      ],
      'ORDER' => 'f.fecha_factura DESC',
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
      $totalFactura = $subTotal + ($subTotal * $iva);

      if ($fila['status'] == 10) {
        $fila['estado_dinamico'] = 'Procesada y Pagada';
        $fila['estado_num'] = 1;
        $pagado = $totalFactura; // Si está pagada por completo, el monto pagado ya es definitivo
        $restante = 0;
      } elseif ($fila['status'] == 11) {
        $fila['estado_dinamico'] = 'Pagada y Despachada';
        $fila['estado_num'] = 3;
        $pagado = $totalFactura; // Si está pagada por completo, el monto pagado ya es definitivo
        $restante = 0;
      } else {
        $pagado = floatval($fila['total_pagado']);
        $restante = round($totalFactura - $pagado, 2);

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
      $fila['total_factura'] = $totalFactura;
      $fila['total_pagado'] = $pagado;
    }
    return $filas;
  }
  private function ObtenerFacturaP() {
    $resultado = $this->seleccionarDatos2([
      'campos' => "
        f.id_factura, f.rif_cedula_cliente,
        f.fecha_factura, f.status,
        c.razon_social_cliente AS CLIENTE
      ",
      'tabla'  => 'facturas as f',
      'datosJoins' => [
        'clientes as c' => 'f.rif_cedula_cliente = c.rif_cedula_cliente',
      ],
      'WHERE' => ['f.id_factura' => $this->idFactura],
      'eliminadosYVigentes' => true,
    ]);

    if ($resultado->rowCount() <= 0) {
      return [
        'tipo'   => 'simple',
        'titulo' => 'Factura no encontrada',
        'texto'  => 'La factura no existe en el sistema',
        'icono'  => 'error',
      ];
    }

    return $resultado->fetch(PDO::FETCH_ASSOC);
  }
  private function ObtenerDetalleFacturaP() {
    $this->conectar();

    // Primero buscamos los datos principales de la factura
    $stmtCab = self::$conexion->prepare("
      SELECT f.id_factura, f.fecha_factura, f.status,
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
              WHERE pa.id_factura = f.id_factura AND dp.status = 1 AND pa.status=1) AS total_pagado
      FROM facturas f
      JOIN clientes c ON f.rif_cedula_cliente = c.rif_cedula_cliente
      JOIN cambios_iva ci ON f.id_cambio_iva = ci.id_cambio_iva
      WHERE f.id_factura = :id
    ");
    $stmtCab->execute([':id' => $this->idFactura]);
    $cabecera = $stmtCab->fetch(PDO::FETCH_ASSOC);

    if (!$cabecera) return [];

    // Luego buscamos qué productos se vendieron
    $stmtProd = self::$conexion->prepare("
      SELECT pf.id_producto_factura, pf.cantidad_producto,
             p.nombre_producto, p.precio_producto,
             pr.nombre_presentacion,
             pp.id_presentacion_producto
      FROM productos_facturas pf
      JOIN presentaciones_productos pp
        ON pf.id_presentacion_producto = pp.id_presentacion_producto
      JOIN productos p ON pp.id_producto = p.id_producto
      JOIN presentaciones pr ON pp.id_presentacion = pr.id_presentacion
      WHERE pf.id_factura = :id AND pf.status = 1
    ");
    $stmtProd->execute([':id' => $this->idFactura]);
    $productos = $stmtProd->fetchAll(PDO::FETCH_ASSOC);

    // También los servicios prestados
    $stmtServ = self::$conexion->prepare("
      SELECT sf.id_servicio_factura, sf.id_servicio, sf.cantidad_servicio,
             sf.es_precio_mapfre, sf.precio_servicio_mapfre,
             s.nombre_servicio, s.precio_servicio
      FROM servicios_facturas sf
      JOIN servicios s ON sf.id_servicio = s.id_servicio
      WHERE sf.id_factura = :id AND sf.status = 1
    ");
    $stmtServ->execute([':id' => $this->idFactura]);
    $servicios = $stmtServ->fetchAll(PDO::FETCH_ASSOC);

    // Ojo, para cada servicio necesitamos saber qué materiales gastó
    $stmtMat = self::$conexion->prepare("
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
    $stmtDel = self::$conexion->prepare("
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
      WHERE d.id_factura = :id AND d.status = 1
    ");
    $stmtDel->execute([':id' => $this->idFactura]);
    $delivery = $stmtDel->fetch(PDO::FETCH_ASSOC);

    // Acomodamos cómo se va a ver el estado de la factura basándonos en si ya pagaron o no
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
      $totalFactura = $subTotal + ($subTotal * $iva);

      if ($cabecera['status'] == 10) {
        $cabecera['estado_dinamico'] = 'Procesada y Pagada';
        $cabecera['estado_num'] = 1;
        $pagado = $totalFactura;
        $restante = 0;
      } elseif ($cabecera['status'] == 11) {
        $cabecera['estado_dinamico'] = 'Pagada y Despachada';
        $cabecera['estado_num'] = 3;
        $pagado = $totalFactura;
        $restante = 0;
      } else {
        $pagado = floatval($cabecera['total_pagado']);
        $restante = round($totalFactura - $pagado, 2);

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
      $cabecera['total_factura'] = $totalFactura;
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
  private function RegistrarFacturaP() {
    $objBitacora = new bitacoraModelo();

    try {
      // Buscamos cuánto es el IVA en este momento
      $this->idCambioIva = $this->obtenerIVAActualP();

      // Armamos un código bonito y único para esta factura
      $this->idFactura = $this->generarCodSeg([
        'tablaBD' => 'facturas',
        'prefijo' => 'FACT',
        'campoID' => 'id_factura',
      ]);

      // Guardamos los datos principales en la base de datos primero
      $idFact = $this->guardarDatos2([
        'tabla' => 'facturas',
        'datos' => [
          'id_factura'        => $this->idFactura,
          'cedula_usuario'    => $this->cedulaUsuario,
          'id_cambio_iva'     => $this->idCambioIva,
          'rif_cedula_cliente' => $this->rifCliente,
          'fecha_factura'     => $this->fechaFactura,
          'status'            => $this->status,
        ],
        'WHERE' => ['id_factura' => $this->idFactura],
      ]);

      if (!$idFact) {
        throw new Exception("No se pudo crear la factura principal");
      }

      // Ahora sí, vamos guardando uno por uno los productos
      foreach ($this->productos as $p) {
        $idPresentacion = $p['id_presentacion_producto'] ?? '';
        $cantidad       = (float)($p['cantidad'] ?? 0);

        if (empty($idPresentacion) || $cantidad <= 0) continue;

        // Trait v2 para consultar stock de la presentación
        $resultadoStock = $this->seleccionarDatos2([
          'campos'     => 'p.stock_producto, pp.id_producto',
          'tabla'      => 'presentaciones_productos AS pp',
          'datosJoins' => [
            'productos p' => 'pp.id_producto = p.id_producto'
          ],
          'WHERE' => [
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

        if ($datosProd['stock_producto'] < $cantidad) {
          throw new Exception(
            "Stock insuficiente para el producto ID: "
              . $datosProd['id_producto']
          );
        }

        // Trait v2 para insertar en productos_facturas
        $this->guardarDatos2([
          'tabla' => 'productos_facturas',
          'datos' => [
            'id_factura'                => $this->idFactura,
            'id_presentacion_producto' => $idPresentacion,
            'cantidad_producto'         => $cantidad,
            'status'                    => 1
          ]
        ]);

        // Nota: Mantenemos raw SQL debido al uso de la función de base de datos GREATEST() para prevenir stock negativo.
        self::$conexion->prepare("
          UPDATE productos SET stock_producto = GREATEST(stock_producto - :cant, 0)
          WHERE id_producto = :id
        ")->execute([
          ':cant' => $cantidad,
          ':id'   => $datosProd['id_producto'],
        ]);
      }

      // Toca guardar los servicios de la misma manera
      foreach ($this->servicios as $s) {
        $idServicio    = $s['id_servicio']         ?? '';
        $cantidad      = (float)($s['cantidad']    ?? 1);
        $esMapfre      = (int)($s['es_mapfre']     ?? 0);
        $precioMapfre  = (float)($s['precio_mapfre'] ?? 0);

        if (empty($idServicio)) continue;

        // Trait v2 para insertar en servicios_facturas
        $this->guardarDatos2([
          'tabla' => 'servicios_facturas',
          'datos' => [
            'id_factura'             => $this->idFactura,
            'id_servicio'            => $idServicio,
            'cantidad_servicio'      => $cantidad,
            'es_precio_mapfre'       => $esMapfre,
            'precio_servicio_mapfre' => $esMapfre ? $precioMapfre : 0,
            'status'                 => 1
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
          self::$conexion->prepare("
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
              'id_factura'        => $this->idFactura,
              'id_direccion'      => $idDireccion,
              'cedula_repartidor' => $cedulaRepartidor,
              'status'            => 1
            ]
          ]);
        }
      }

      // Dejamos registro en la bitácora de que todo salió súper bien
      $objBitacora->registrarBitacora(
        'facturacion',
        "Registrar Factura",
        'exitoso'
      );

      $this->commit();

      return [
        'tipo'        => 'limpiar',
        'titulo'      => 'Factura Registrada',
        'texto'       => "Factura #{$this->idFactura} registrada correctamente.",
        'icono'       => 'success',
        'id_factura'  => $this->idFactura,
      ];
    } catch (Exception $e) {
      // Si algo se rompió, anotamos en la bitácora el error para revisarlo luego
      $objBitacora->registrarBitacora(
        'facturacion',
        "Registrar Factura",
        'error'
      );

      $this->rollback();

      return [
        'tipo'   => 'simple',
        'titulo' => 'Error al Registrar',
        'texto'  => "No se pudo registrar la factura: " . $e->getMessage(),
        'icono'  => 'error',
      ];
    }
  }
  private function DespacharFacturaP() {
    $resultado = $this->seleccionarDatos2([
      'campos' => 'status',
      'tabla'  => 'facturas',
      'WHERE'  => ['id_factura' => $this->idFactura]
    ]);
    $fila = $resultado->fetch(PDO::FETCH_ASSOC);

    if (!$fila) {
      return ['tipo' => 'simple', 'titulo' => 'Error', 'texto' => 'La factura no existe', 'icono' => 'error'];
    }

    $statusActual = intval($fila['status']);

    // Verificamos que tenga un estado válido antes de cambiar a despachada
    if ($statusActual == 2) {
      return ['tipo' => 'simple', 'titulo' => 'No permitido', 'texto' => 'No se puede despachar una factura anulada', 'icono' => 'warning'];
    }
    if ($statusActual == 3 || $statusActual == 11) {
      return ['tipo' => 'simple', 'titulo' => 'Ya despachada', 'texto' => 'Esta factura ya fue despachada anteriormente', 'icono' => 'info'];
    }

    // Si no estaba pagada completa, pasa a despachada sin pagar
    // Pero si ya habían pagado todo, la marcamos como despachada y pagada
    $nuevoStatus = ($statusActual == 10) ? 11 : 3;

    try {
      $this->conectar();
      if (self::$conexion->inTransaction()) {
        self::$conexion->rollBack();
      }
      self::$conexion->beginTransaction();

      $this->actualizarDatos2([
        'tabla' => 'facturas',
        'datos' => ['status' => $nuevoStatus],
        'WHERE' => ['id_factura' => $this->idFactura]
      ]);

      $objBitacora = new bitacoraModelo();
      $estadoTexto = ($nuevoStatus == 11) ? 'Pagada y Despachada' : 'Despachada y sin Pago';
      $objBitacora->registrarBitacora(
        'facturacion',
        "Despachar Factura",
        'exitoso'
      );

      $this->commit();

      return [
        'tipo'   => 'simple',
        'titulo' => 'Factura Despachada',
        'texto'  => "La factura #{$this->idFactura} fue marcada como \"{$estadoTexto}\".",
        'icono'  => 'success',
      ];
    } catch (\Exception $e) {
      if (self::$conexion->inTransaction()) {
        self::$conexion->rollBack();
      }
      $objBitacora = new bitacoraModelo();
      $objBitacora->registrarBitacora(
        'facturacion',
        "Despachar Factura",
        'error'
      );
      return [
        'tipo'   => 'simple',
        'titulo' => 'Error al Despachar',
        'texto'  => 'No se pudo despachar la factura: ' . $e->getMessage(),
        'icono'  => 'error',
      ];
    }
  }
  private function AnularFacturaP() {
    $objBitacora = new bitacoraModelo();

    try {
      // No podemos anular algo que ya está anulado, ¿cierto?
      $stmt = self::$conexion->prepare(
        "SELECT status FROM facturas WHERE id_factura = :id"
      );
      $stmt->execute([':id' => $this->idFactura]);
      $fila = $stmt->fetch(PDO::FETCH_ASSOC);

      if (!$fila || $fila['status'] == 2) {
        return [
          'tipo'   => 'simple',
          'titulo' => 'Factura ya anulada',
          'texto'  => 'Esta factura ya fue anulada anteriormente',
          'icono'  => 'warning',
        ];
      }

      // Le cambiamos el estado al número 2, que es nuestro código secreto para anulada
      $resultado = $this->actualizarDatos2([
        'tabla' => 'facturas',
        'datos' => ['status' => 2],
        'WHERE' => ['id_factura' => $this->idFactura],
      ]);

      if (!$resultado || $resultado <= 0) {
        throw new Exception("No se pudo anular la factura");
      }

      // Devolvemos al inventario todos los productos que se habían llevado
      $stmtProd = self::$conexion->prepare("
        SELECT pf.cantidad_producto, pp.id_producto
        FROM productos_facturas pf
        JOIN presentaciones_productos pp
          ON pf.id_presentacion_producto = pp.id_presentacion_producto
        WHERE pf.id_factura = :id AND pf.status = 1
      ");
      $stmtProd->execute([':id' => $this->idFactura]);
      $prods = $stmtProd->fetchAll(PDO::FETCH_ASSOC);

      foreach ($prods as $p) {
        self::$conexion->prepare("
          UPDATE productos
          SET stock_producto = stock_producto + :cant
          WHERE id_producto = :id
        ")->execute([
          ':cant' => $p['cantidad_producto'],
          ':id'   => $p['id_producto'],
        ]);
      }

      // Hacemos lo mismo con los materiales que se usaron en los servicios
      $stmtServ = self::$conexion->prepare("
        SELECT sf.id_servicio, sf.cantidad_servicio
        FROM servicios_facturas sf
        WHERE sf.id_factura = :id AND sf.status = 1
      ");
      $stmtServ->execute([':id' => $this->idFactura]);
      $servs = $stmtServ->fetchAll(PDO::FETCH_ASSOC);

      foreach ($servs as $s) {
        $stmtMP = self::$conexion->prepare("
          SELECT id_producto, cantidad_producto
          FROM productos_servicios
          WHERE id_servicio = :serv AND status = 1
        ");
        $stmtMP->execute([':serv' => $s['id_servicio']]);
        $mps = $stmtMP->fetchAll(PDO::FETCH_ASSOC);

        foreach ($mps as $mp) {
          self::$conexion->prepare("
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
      $objBitacora->registrarBitacora(
        'facturacion',
        "Anular Factura",
        'exitoso'
      );

      $this->commit();

      return [
        'tipo'   => 'simple',
        'titulo' => 'Factura Anulada',
        'texto'  => "La factura #{$this->idFactura} fue anulada y el stock restaurado.",
        'icono'  => 'success',
      ];
    } catch (Exception $e) {
      $objBitacora->registrarBitacora(
        'facturacion',
        "Anular Factura",
        'error'
      );
      $this->rollback();

      return [
        'tipo'   => 'simple',
        'titulo' => 'Error al Anular',
        'texto'  => "No se pudo anular la factura: " . $e->getMessage(),
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
      $this->conectar();
      if (self::$conexion->inTransaction()) {
        self::$conexion->rollBack();
      }
      self::$conexion->beginTransaction();

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
          'id_factura' => $this->idFactura,
          'fecha_pago' => $fechaPago,
          'status'     => 1
        ]
      ]);

      if (!$idPagoReg) {
        throw new Exception("No se pudo registrar el pago principal.");
      }

      foreach ($this->pagos as $pago) {
        $idMetodo = $pago['id_metodo_pago'] ?? '';
        $idMoneda = $pago['id_moneda'] ?? '';
        $monto    = floatval($pago['monto_pago'] ?? 0);

        if (empty($idMetodo) || empty($idMoneda) || $monto <= 0) {
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
      $objBitacora->registrarBitacora(
        'facturacion',
        "Registrar Pago",
        'exitoso'
      );

      // Verificar si con este pago se salda la factura por completo (Query compleja, se mantiene cruda)
      $stmtCheck = self::$conexion->prepare("
        SELECT f.status,
               (SELECT COALESCE(SUM(
                 CASE WHEN mo.nombre_moneda = 'BÓLIVAR' OR mo.nombre_moneda = 'BS' 
                      THEN dp.monto_pago / (SELECT MAX(valor_moneda) FROM monedas WHERE nombre_moneda IN ('DÓLAR', 'DOLAR')) 
                      ELSE dp.monto_pago END
               ), 0) 
               FROM pagos pa 
               JOIN detalles_pagos dp ON pa.id_pago=dp.id_pago 
               JOIN monedas mo ON dp.id_moneda=mo.id_moneda 
               WHERE pa.id_factura = f.id_factura AND dp.status = 1 AND pa.status=1) AS total_pagado,
               (SELECT COALESCE(SUM(pf.cantidad_producto * p.precio_producto), 0) 
                FROM productos_facturas pf 
                JOIN presentaciones_productos pp ON pf.id_presentacion_producto = pp.id_presentacion_producto 
                JOIN productos p ON pp.id_producto = p.id_producto 
                WHERE pf.id_factura = f.id_factura AND pf.status = 1) AS sub_prod,
               (SELECT COALESCE(SUM(sf.cantidad_servicio * CASE WHEN sf.es_precio_mapfre=1 THEN sf.precio_servicio_mapfre ELSE s.precio_servicio END), 0) 
                FROM servicios_facturas sf 
                JOIN servicios s ON sf.id_servicio=s.id_servicio 
                WHERE sf.id_factura = f.id_factura AND sf.status = 1) AS sub_serv,
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
                 WHERE d.id_factura = f.id_factura AND d.status = 1) AS sub_del,
               ci.monto_cambio_iva
        FROM facturas f
        JOIN cambios_iva ci ON f.id_cambio_iva = ci.id_cambio_iva
        WHERE f.id_factura = :id
      ");
      $stmtCheck->execute([':id' => $this->idFactura]);
      $f = $stmtCheck->fetch(PDO::FETCH_ASSOC);
      if ($f) {
        $iva = floatval($f['monto_cambio_iva']) / 100;
        $sub = floatval($f['sub_prod']) + floatval($f['sub_serv']) + floatval($f['sub_del']);
        $tot = round($sub + ($sub * $iva), 2);
        $pag = floatval($f['total_pagado']);
        if (round($tot - $pag, 2) <= 0.01) {
          $newStatus = ($f['status'] == 3 || $f['status'] == 11) ? 11 : 10;
          $this->actualizarDatos2([
            'tabla' => 'facturas',
            'datos' => ['status' => $newStatus],
            'WHERE' => ['id_factura' => $this->idFactura]
          ]);
        }
      }

      self::$conexion->commit();

      return [
        'tipo'   => 'simple',
        'titulo' => 'Pago Registrado',
        'texto'  => 'El pago ha sido procesado exitosamente.',
        'icono'  => 'success',
      ];
    } catch (Exception $e) {
      if (self::$conexion->inTransaction()) {
        self::$conexion->rollBack();
      }
      $objBitacora = new bitacoraModelo();
      $objBitacora->registrarBitacora(
        'facturacion',
        "Registrar Pago",
        'error'
      );
      return [
        'tipo'   => 'simple',
        'titulo' => 'Error Interno',
        'texto'  => 'Hubo un problema al registrar el pago: ' . $e->getMessage(),
        'icono'  => 'error',
      ];
    }
  }
}
