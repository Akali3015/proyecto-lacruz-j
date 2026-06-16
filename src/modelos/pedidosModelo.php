<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use src\modelos\metodosPagoModelo;
use src\modelos\presentacionesModelo;
use src\modelos\rutasModelo;
use src\modelos\cambiosIvaModelo;
use PDO;

class pedidosModelo extends conexion {
  private string $idPedido = '';
  private array $productosPedido = [];
  private array $deliveryPedido = [];
  private array $pagosPedido = [];
  private array $comprobantesPagos = [];
  private int $statusPedido = 0;

  public function validarPedidos(array $instruccionesVal) {
    [
      'infoVal' => &$infoVal,
      'camposVal' => &$camposVal,
    ] = $instruccionesVal;
    $funcionAsignadora = function ($nombreCampo, &$valor) {
      $claveVal = [
        'cantidad_producto' => [
          "campo_valor" => &$valor,
          "formulario_nombre" => "cantidad del producto",
          "requerido" => true,
          "minimo" => minRegexCantidadItem,
          "maximo" => maxRegexCantidadItem,
          "expresion_re" => regexCantidadItem,
        ],
        'cedula_repartidor' => [
          "campo_nombre" => "cedula_repartidor",
          "campo_valor" => &$valor,
          "formulario_nombre" => "cédula del repartidor",
          "requerido" => true,
          "minimo" => minRegexCedulaRifLetra,
          "maximo" => maxRegexCedulaRifLetra,
          "expresion_re" => regexCedulaRifLetra,
          "tabla" => "repartidores",
          "debeExistir" => true,
        ],
        'comprobantes_pago' => [
          "imagen" => &$valor,
          "formulario_nombre" => "comprobantes del pago",
          "requerido" => true,
        ],
        'status_pedido' => [
          "campo_valor" => &$valor,
          "formulario_nombre" => "status de pedido",
          "requerido" => true,
          "minimo" => minRegexStatus,
          "maximo" => maxRegexStatus,
          "expresion_re" => regexStatus,
        ],
        'id_banco_emisor' => [
          "campo_nombre" => "id_banco",
          "campo_valor" => &$valor,
          "formulario_nombre" => "id del banco emisor",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "bancos",
          "debeExistir" => true,
        ],
        'id_banco_receptor' => [
          "campo_nombre" => "id_banco",
          "campo_valor" => &$valor,
          "formulario_nombre" => "id del banco receptor",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "bancos",
          "debeExistir" => true,
        ],
        'id_delivery' => [
          "campo_nombre" => "id_delivery",
          "campo_valor" => &$valor,
          "formulario_nombre" => "id del delivery",
          "requerido" => true,
          "minimo" => minRegexIdSeguro,
          "maximo" => maxRegexIdSeguro,
          "expresion_re" => regexIdSeguro,
          "tabla" => "deliveries",
          "debeExistir" => true,
        ],
        'id_metodo_pago' => [
          "campo_nombre" => "id_metodo_pago",
          "campo_valor" => &$valor,
          "formulario_nombre" => "id del método de pago",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "metodos_pagos",
          "debeExistir" => true,
        ],
        'id_moneda' => [
          "campo_nombre" => "id_moneda",
          "campo_valor" => &$valor,
          "formulario_nombre" => "id de la moneda",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "monedas",
          "debeExistir" => true,
        ],
        'id_pedido' => [
          "campo_nombre" => "id_orden_entrega_presupuesto",
          "campo_valor" => &$valor,
          "formulario_nombre" => "id del pedido",
          "requerido" => true,
          "minimo" => minRegexIdSeguro,
          "maximo" => maxRegexIdSeguro,
          "expresion_re" => regexIdSeguro,
          "tabla" => "ordenes_entregas_presupuestos",
          "debeExistir" => true,
          "debeSerUnico" => true,
        ],
        'id_presentacion' => [
          "campo_nombre" => "id_presentacion",
          "campo_valor" => &$valor,
          "formulario_nombre" => "id de la presentación",
          "requerido" => true,
          "minimo" => minRegexIdSeguro,
          "maximo" => maxRegexIdSeguro,
          "expresion_re" => regexIdSeguro,
          "tabla" => "presentaciones",
          "debeExistir" => true,
        ],
        'id_producto' => [
          "campo_nombre" => "id_producto",
          "campo_valor" => &$valor,
          "formulario_nombre" => "id del producto",
          "requerido" => true,
          "minimo" => minRegexIdSeguro,
          "maximo" => maxRegexIdSeguro,
          "expresion_re" => regexIdSeguro,
          "tabla" => "productos",
          "debeExistir" => true,
        ],
        'latitud' => [
          "campo_valor" => &$valor,
          "formulario_nombre" => "latitud",
          "requerido" => true,
          "minimo" => minRegexCoordenadas,
          "maximo" => maxRegexCoordenadas,
          "expresion_re" => regexCoordenadas,
        ],
        'longitud' => [
          "campo_valor" => &$valor,
          "formulario_nombre" => "longitud",
          "requerido" => true,
          "minimo" => minRegexCoordenadas,
          "maximo" => maxRegexCoordenadas,
          "expresion_re" => regexCoordenadas,
        ],
        'monto_pago' => [
          "campo_valor" => &$valor,
          "formulario_nombre" => "monto del pago",
          "requerido" => true,
          "minimo" => minRegexPrecio,
          "maximo" => maxRegexPrecio,
          "expresion_re" => regexPrecio,
        ],
        'necesita_referencia' => [
          "campo_valor" => &$valor,
          "formulario_nombre" => "referencia del pago",
          "requerido" => true,
          "minimo" => minRegexCantidadItem,
          "maximo" => maxRegexCantidadItem,
          "expresion_re" => regexCantidadItem,
        ],
        'necesita_banco_emisor' =>  [
          "campo_nombre" => "id_banco",
          "campo_valor" => &$valor,
          "formulario_nombre" => "id del banco emisor",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "bancos",
          "debeExistir" => true,
        ],
        'necesita_banco_receptor' =>  [
          "campo_nombre" => "id_banco",
          "campo_valor" => &$valor,
          "formulario_nombre" => "id del banco receptor",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "bancos",
          "debeExistir" => true,
        ],
        'referencia_pago' => [
          "campo_valor" => &$valor,
          "formulario_nombre" => "referencia del pago",
          "requerido" => true,
          "minimo" => minRegexReferencia,
          "maximo" => maxRegexReferencia,
          "expresion_re" => regexReferencia,
        ],
      ];
      return $claveVal[$nombreCampo];
    };
    $campos = [];
    foreach ($camposVal as $campo) {
      switch ($campo) {
        case 'delivery':
          $campos[] = $funcionAsignadora('latitud', $infoVal['delivery']['latitud']);
          $campos[] = $funcionAsignadora('longitud', $infoVal['delivery']['longitud']);
          break;
        case 'productos':
          if (($infoVal['productos'] ?? []) == []) {
            return [
              'tipo' => 'simple',
              'titulo' => 'Sin productos agregados',
              'texto' => 'No ha seleccionado ningún producto aún',
              'icono' => 'warning',
            ];
          }
          foreach ($infoVal['productos'] as &$pro) {
            $campos[] = $funcionAsignadora('id_producto', $pro['id_producto']);
            $campos[] = $funcionAsignadora('id_presentacion', $pro['id_presentacion']);
            $campos[] = $funcionAsignadora('cantidad_producto', $pro['cantidad']);
          }
          unset($pro);
          break;
        case 'pagos':
          if (($infoVal['pagos'] ?? []) == []) {
            return [
              'tipo' => 'simple',
              'titulo' => 'Sin detalles de pago agregados',
              'texto' => 'No ha seleccionado ningún producto aún',
              'icono' => 'warning',
            ];
          }
          foreach ($infoVal['pagos'] as &$pago) {
            $pago['monto_pago'] = str_replace('.', '', $pago['monto_pago']);
            $pago['monto_pago'] = (float)(str_replace(',', '.', $pago['monto_pago']));
            if ($pago['id_metodo_pago'] == '') {
              return [
                'tipo' => 'simple',
                'titulo' => 'Metodo de pago obligatorio',
                'texto' => 'No puedes enviar el pedido sin expecificar el método de pago del detalle del pago',
                'icono' => 'warning'
              ];
            }
            $campos[] = $funcionAsignadora('id_metodo_pago', $pago['id_metodo_pago']);
            $campos[] = $funcionAsignadora('monto_pago', $pago['monto_pago']);
            $campos[] = $funcionAsignadora('id_moneda', $pago['id_moneda']);
            $objMP = new metodosPagoModelo();
            $MPBD = $objMP->seleccionarMetodosPagos(['id_metodo_pago' => $pago['id_metodo_pago']]);
            if ($MPBD['necesita_referencia'] == 1) {
              $campos[] = $funcionAsignadora('referencia_pago', $pago['referencia_pago']);
            }
            if ($MPBD['necesita_banco_emisor'] == 1) {
              $campos[] = $funcionAsignadora('id_banco_emisor', $pago['id_banco_emisor']);
            }
            if ($MPBD['necesita_banco_receptor'] == 1) {
              $campos[] = $funcionAsignadora('id_banco_receptor', $pago['id_banco_receptor']);
            }
          };
          unset($pago);
          break;
        case 'comprobantes_pago':
          if (!isset($_COOKIE['TEMP']) && !isset($_ENV['MODO_TESTEO'])) {
            $campos[] = $funcionAsignadora($campo, $infoVal[$campo]);
          }
          break;
        default:
          $campos[] = $funcionAsignadora($campo, $infoVal[$campo]);
          break;
      }
    }
    return $this->limpiar_Verificar($campos);
  }
  public function listarPedidos(array $info) {
    if (($info['id_pedido'] ?? '') != "") {
      $resultado = $this->validarPedidos([
        'infoVal' => &$info,
        'camposVal' => [
          'id_pedido',
        ],
      ]);
      if ($resultado) return $resultado;
      $this->idPedido = $info['id_pedido'];
    }
    return $this->listarPedidosP($info);
  }
  public function registrarPedidos(array $info) {
    $resultado = $this->validarPedidos([
      'infoVal' => &$info,
      'camposVal' => [
        'comprobantes_pago',
        'productos',
        'pagos',
        'delivery'
      ],
    ]);
    if ($resultado) return $resultado;

    [
      'productos' => $this->productosPedido,
      'pagos' => $this->pagosPedido,
      'delivery' => $this->deliveryPedido,
    ] = $info;
    $this->comprobantesPagos = $info['comprobantes_pago'] ?? [];

    return $this->registrarPedidosP();
  }
  public function asignarRepartidoresPedidos(array $info) {
    $resultado = $this->validarPedidos([
      'infoVal' => &$info,
      'camposVal' => [
        'id_delivery',
        'id_pedido',
        'cedula_repartidor',
      ],
    ]);
    if ($resultado) return $resultado;

    $this->deliveryPedido['id_delivery'] = $info['id_delivery'];
    $this->idPedido = $info['id_pedido'];
    $this->deliveryPedido['cedula_repartidor'] = $info['cedula_repartidor'];
    return $this->asignarRepartidoresPedidosP();
  }
  public function actualizarPedidos(array $info) {
    $resultado = $this->validarPedidos([
      'infoVal' => &$info,
      'camposVal' => [
        'id_pedido',
        'status_pedido',
      ],
    ]);
    if ($resultado) return $resultado;
    $this->idPedido = $info['id_pedido'];
    $this->statusPedido = $info['status_pedido'];

    return $this->actualizarPedidoP();
  }

  private function listarPedidosP(array $info) {
    if ($this->idPedido != '') {

      //Generales
      $datosGenerales = $this->seleccionarDatos2([
        'campos' => '*, fa.status as status_pedido,fa.cedula_usuario',
        'tabla' => 'ordenes_entregas_presupuestos as fa',
        'datosJoins' => [
          'clientes as cl' => 'fa.rif_cedula_cliente = cl.rif_cedula_cliente',
          'cambios_iva as ci' => 'fa.id_cambio_iva = ci.id_cambio_iva',
        ],
        'WHERE' => [
          'fa.id_orden_entrega_presupuesto' => $this->idPedido,
        ]
      ])->fetch();

      // IVA
      $IVA = $datosGenerales['monto_cambio_iva'];

      //Usuario - Vendedor
      $vendedor = false;
      if ($datosGenerales['cedula_usuario'] != '') {
        $objUsuario = new usuariosModelo();
        $vendedor = $objUsuario->seleccionarUsuarios(['cedula_usuario' => $datosGenerales['cedula_usuario']]);
      }

      // Las divisas de esa fecha
      $objMonedas = new monedasModelo();
      $monedas = $objMonedas->seleccionarMonedas([
        'tipoConsulta' => 'divisasPorFecha',
        'fecha' => $datosGenerales['fecha_orden_entrega_presupuesto']
      ]);

      // Si es delivery
      $medioEnvio = $this->seleccionarDatos2([
        'tabla' => 'deliveries as de',
        'campos' => '*',
        'datosJoins' => [
          'LEFT repartidores as re' => 'de.cedula_repartidor = re.cedula_repartidor',
        ],
        'WHERE' => [
          'de.id_orden_entrega_presupuesto' => $this->idPedido
        ]
      ])->fetch();
      $medioEnvio['tipo_medio_envio'] = 'delivery';

      //Si es de envio de terceros
      if (($medioEnvio['id_delivery'] ?? []) == []) {
        $medioEnvio = $this->seleccionarDatos2([
          'tabla' => 'envios_terceros as et',
          'campos' => '*',
          'datosJoins' => [
            'sucursales_empresas_envios as see' => 'et.id_sucursal_empresa_envios = see.id_sucursal_empresa_envios',
            'empresas_envios as ee' => 'see.id_empresa_envios = ee.id_empresa_envios',
          ],
          'WHERE' => [
            'et.id_orden_entrega_presupuesto' => $this->idPedido
          ]
        ])->fetch();
        $medioEnvio['tipo_medio_envio'] = 'deTercero';
      }

      //Dirección
      $medioEnvio += $this->seleccionarDatos2([
        'tabla' => 'direcciones as di',
        'campos' => '*',
        'datosJoins' => [
          'rutas as ru' => 'di.id_ruta = ru.id_ruta',
          'longitudes_direcciones as lond' => 'di.id_longitud_direccion = lond.id_longitud_direccion',
          'latitudes_direcciones as latd' => 'di.id_latitud_direccion = latd.id_latitud_direccion',
        ],
        'WHERE' => [
          'di.id_direccion' => $medioEnvio['id_direccion']
        ]
      ])->fetch();
      $medioEnvio['url_direccion'] = "https://maps.google.com/?q={$medioEnvio['coordenada_latitud']},{$medioEnvio['coordenada_longitud']}";

      // La ruta del delivery
      $totalEnvio = 0;
      if ($medioEnvio['tipo_medio_envio'] == 'delivery') {
        $objRutas = new rutasModelo();
        $rutaFecha = $objRutas->seleccionarRutas([
          'tipoConsulta' => 'porFecha',
          'id_ruta' => $medioEnvio['id_ruta'],
          'fecha' => $datosGenerales['fecha_orden_entrega_presupuesto']
        ]);

        $medioEnvio['precio_ruta_factura'] = $rutaFecha['precio_ruta_fecha'];
        $infoRuta = $this->calcularKmPorCarretera([
          'partida' => [
            'latitud' => coorJLACRUZ['latitud'],
            'longitud' => coorJLACRUZ['longitud'],
          ],
          'llegada' => [
            'latitud' => $medioEnvio['coordenada_latitud'],
            'longitud' => $medioEnvio['coordenada_longitud'],
          ]
        ]);
        if (isset($_COOKIE['TEMP']) || isset($_ENV['MODO_TESTEO'])) $infoRuta['km_recorrido'] = 48;
        if (isset($infoRuta['icono'])) return $infoRuta;

        $medioEnvio['km_recorrido'] = $infoRuta['km_recorrido'];
        $totalEnvio = $medioEnvio['precio_ruta_factura'] * $infoRuta['km_recorrido'];
      } else {
        $totalEnvio = $medioEnvio['precio_envio_tercero'];
      }

      // Productos
      $objProductos = new productosModelo();
      $productos = $objProductos->seleccionarProductos([
        'tipoConsulta' => 'productosFactura',
        'id_orden_entrega_presupuesto' => $this->idPedido,
      ]);

      $totalProductos = 0;
      foreach ($productos as &$prodInd) {
        $subtotal = $prodInd['cantidad_bruta'] * $prodInd['precio_producto_factura'];
        if ($prodInd['cantidad_bruta'] >= 20) {
          $subtotal -= $subtotal * 0.10;
        }
        $prodInd += [
          'subtotal_factura' => $subtotal,
        ];
        $totalProductos += (float)$subtotal;
      }
      unset($prodInd);

      // Detalles - pagos
      $detallesPagos = $this->seleccionarDatos2([
        'tabla' => 'pagos as p',
        'campos' => '
          p.id_pago, dp.id_detalle_pago, dp.id_metodo_pago,
          dp.id_moneda,dp.monto_pago, rdp.referencia_pago
        ',
        'datosJoins' => [
          'detalles_pagos as dp' => 'p.id_pago = dp.id_pago',
          'LEFT ' . 'referencias_detalles_pagos as rdp' => 'dp.id_detalle_pago = rdp.id_detalle_pago',
        ],
        'WHERE' => [
          'id_orden_entrega_presupuesto' => $this->idPedido
        ],
      ])->fetchAll();

      $idsPagos = $this->indexarArrays([
        "indice" => 'id_pago',
        'camposAgrupar' => 'id_pago',
        'indicesNumericos'=>true,
        'array' => $detallesPagos,
      ]);

      $objMetodosPagos = new metodosPagoModelo();
      $metodosPagos = $objMetodosPagos->seleccionarMetodosPagos([
        'tipoConsulta' => 'indexadosPorId',
      ]);
      $objBancos = new bancosModelo();
      $bancos = $objBancos->seleccionarBancos([
        'tipoConsulta' => 'indexadosPorId'
      ]);

      $totalPagos = 0;
      foreach ($detallesPagos as &$detalle) {
        //Pasamos a dolar
        $valorMoneda = $monedas[$detalle['id_moneda']]['valor_fecha_moneda'];
        $subTotal = $valorMoneda * $detalle['monto_pago'];
        $totalPagos += $subTotalDolar = $subTotal / $monedas[1]['valor_fecha_moneda'];
        $detalle['equivalencia_fecha_factura'] = $subTotalDolar;
        $detalle['nombre_metodo_pago'] = $metodosPagos[$detalle['id_metodo_pago']]['nombre_metodo_pago'];
        $detalle['nombre_moneda'] = $monedas[$detalle['id_moneda']]['nombre_moneda'];

        //Bancos del pago
        $bancosDetalles = $this->seleccionarDatos2([
          'tabla' => 'bancos_detalles_pagos',
          'campos' => '*',
          'WHERE' => [
            'id_detalle_pago' => $detalle['id_detalle_pago']
          ]
        ])->fetchAll();

        //Bancos
        $detalle['nombre_banco_emisor'] = 'N/A';
        $detalle['nombre_banco_receptor'] = 'N/A';
        $detalle['referencia_pago'] = $detalle['referencia_pago'] ? $detalle['referencia_pago'] : 'N/A';
        if (!empty($bancosDetalles)) {
          foreach ($bancosDetalles as $bancoD) {
            if ($bancoD['es_emisor'] == 1) {
              $detalle['id_banco_emisor'] = $bancoD['id_banco'];
              $detalle['nombre_banco_emisor'] = $bancos[$bancoD['id_banco']]['nombre_banco'];
            } else {
              $detalle['id_banco_receptor'] = $bancoD['id_banco'];
              $detalle['nombre_banco_receptor'] = $bancos[$bancoD['id_banco']]['nombre_banco'];
            }
          }
        }
      }
      unset($detalle);

      //Captures del pago
      $capturesPagos = [];
      foreach ($idsPagos as $idPago) {
        $capturesPagos += $this->seleccionarDatos2([
          'tabla' => 'comprobantes_pagos',
          'campos' => 'path_comprobante',
          'WHERE' => [
            'id_pago' => $idPago
          ]
        ])->fetchAll(PDO::FETCH_COLUMN);
      }
      $cliente = $this->intersArray($datosGenerales, [
        'rif_cedula_cliente',
        'telefono_cliente',
        'razon_social_cliente',
        'direccion_cliente',
        'correo_cliente',
      ]);
      $datosFactura = $this->intersArray($datosGenerales, [
        'fecha_orden_entrega_presupuesto',
        'status_pedido',
      ]);
      $cargos = ($totalProductos + $totalEnvio) + (($totalProductos + $totalEnvio) * ($IVA / 100));
      return $datosFactura += [
        'vendedor' => $vendedor,
        'cliente' => $cliente,
        'productos' => $productos,
        'pagos' => $detallesPagos,
        'capturesPagos' => $capturesPagos,
        'medioEnvio' => $medioEnvio,
        'calculos' => [
          'dolar' => $monedas[1],
          'porcentaje_IVA' => $IVA,
          'totalProductos' => $totalProductos,
          'totalEnvio' => $totalEnvio,
          'total_IVA' => ($totalProductos + $totalEnvio) + (($totalProductos + $totalEnvio) * ($IVA / 100)),
          'totalPagos' => $totalPagos,
          'totalGeneral' => round(($totalPagos - $cargos), 2)
        ]
      ];
    } else {
      if (($info['tipoConsulta'] ?? '') == 'clienteInicioSesion') {
        return $this->seleccionarDatos2([
          'campos' => '
            cl.rif_cedula_cliente, cl.razon_social_cliente,
            fa.id_orden_entrega_presupuesto, fa.fecha_orden_entrega_presupuesto, fa.status as status_pedido
          ',
          'tabla' => 'ordenes_entregas_presupuestos as fa',
          'datosJoins' => [
            'clientes as cl' => 'fa.rif_cedula_cliente = cl.rif_cedula_cliente'
          ],
          'WHERE' => [
            'cl.rif_cedula_cliente' => $_SESSION['cedula']
          ]
        ])->fetchAll();
      } else {
        $pedidos = $this->seleccionarDatos2([
          'campos' => '
            cl.rif_cedula_cliente, cl.razon_social_cliente,
            fa.id_orden_entrega_presupuesto, fa.fecha_orden_entrega_presupuesto, fa.status as status_pedido
          ',
          'tabla' => 'ordenes_entregas_presupuestos as fa',
          'datosJoins' => [
            'clientes as cl' => 'fa.rif_cedula_cliente = cl.rif_cedula_cliente'
          ],
        ])->fetchAll();
        $mapPedidos = [
          'porConfirmar' => [],
          'confirmados' => [],
          'rechazados' => [],
          'entregados' => [],
        ];
        foreach ($pedidos as $pedido) {
          switch ($pedido['status_pedido']) {
            case 5: //pendiente
              $mapPedidos['porConfirmar'][] = $pedido;
              break;
            case 6: //rechazados
              $mapPedidos['rechazados'][] = $pedido;
              break;
            case 7: //confirmados
              $mapPedidos['confirmados'][] = $pedido;
              break;
            case 8: //entregados
              $mapPedidos['entregados'][] = $pedido;
              break;
            default:
              # code...
              break;
          }
        }
        return $mapPedidos;
      }
    }
  }
  private function registrarPedidosP() {

    $objBitacora = new bitacoraModelo();
    $comprobantesPagos = [];
    $error = function () use ($objBitacora, $comprobantesPagos) {
      $this->rollback();
      $objBitacora->registrarBitacora('pedidos', 'registrar', 'Fallido', true);
      if ($comprobantesPagos != []) {
        $this->Imagenes_Eli2('comprobantes_pagos', $comprobantesPagos);
      }
    };

    // #region VALIDACIONES DE LOGICA DE NEGOCIO
    $totalPagar = 0;
    $totalPagado = 0;

    //Pagos
    $objMon = new monedasModelo();
    $monedasBD = $objMon->seleccionarMonedas([
      'tipoConsulta' => 'indexadosPorId'
    ]);
    $dolar = $monedasBD[1];
    foreach ($this->pagosPedido as $pago) {
      $totalPagado += $pago['monto_pago'] * $monedasBD[$pago['id_moneda']]['valor_moneda'];
    };

    //Productos
    $objProd = new productosModelo();
    $productosBD = $objProd->seleccionarProductos([
      'tipoConsulta' => 'indexadosPorId'
    ]);
    $objPresentacion = new presentacionesModelo();
    $presentacionesBD = $objPresentacion->seleccionarPresentaciones([
      'tipoConsulta' => 'indexadosPorId'
    ]);

    $descuentoStocks = [];
    foreach ($this->productosPedido as $producto) {
      $productoBD = $productosBD[$producto['id_producto']];
      $presentacionBD = $presentacionesBD[$producto['id_presentacion']];
      $cantidadBruta = $producto['cantidad'] * $presentacionBD['cantidad_pmp'];
      $subtotalBase = $producto['cantidad'] * $productoBD['precio_producto'];
      $subTotalDescuento = $subtotalBase;
      if ($cantidadBruta >= 20) {
        $subTotalDescuento = $subtotalBase - ($subtotalBase * 0.1);
      }
      $totalPagar += $subTotalDescuento;

      // Validamos que el stock disponible sea suficiente
      if ($cantidadBruta > $productoBD['stock_producto']) {
        $error();
        return [
          'tipo' => 'simple',
          'titulo' => 'Cantidad no disponible',
          'texto' => '
            Actualmente no contamos con esa cantidad de producto, 
            por favor expecifique una cantidad menor',
          'icono' => 'warning',
        ];
      }
      $descuentoStocks[$producto['id_producto']] = [
        'cantidadDescontar' => $cantidadBruta,
        'cantidadActual' => $productoBD['stock_producto']
      ];
    }

    // Delivery
    $infoRuta = $this->calcularKmPorCarretera([
      'partida' => [
        'latitud' => coorJLACRUZ['latitud'],
        'longitud' => coorJLACRUZ['longitud'],
      ],
      'llegada' => [
        'latitud' => $this->deliveryPedido['latitud'],
        'longitud' => $this->deliveryPedido['longitud'],
      ]
    ]);
    if (isset($_COOKIE['TEMP']) || isset($_ENV['MODO_TESTEO'])) $distanciaKM = 48;
    if (isset($infoRuta['icono'])) {
      $error();
      return $infoRuta;
    }

    $objRutas = new rutasModelo();
    $rutaBD = $objRutas->seleccionarRutas([
      'tipoConsulta' => 'porKm',
      'km_recorrido' => $infoRuta['km_recorrido']
    ]);
    if (isset($rutaBD['icono'])) return $rutaBD;
    $totalPagar += ($rutaBD['precio_ruta'] * $infoRuta['km_recorrido']) * $dolar['valor_moneda'];

    //Validamos que haya pagado completo
    if ($totalPagado < $totalPagar) {
      $error();
      return [
        'tipo' => 'simple',
        'titulo' => 'Pago Incompleto',
        'texto' => 'No puede registrar un pedido sin cancelar la totalidad del mismo',
        'icono' => 'warning',
      ];
    }

    // #endregion VALIDACIONES DE LOGICA DE NEGOCIO

    // #region TRANSACCIÓN

    // Cambio IVA
    $objIva = new cambiosIvaModelo();
    $cambioIva = $objIva->seleccionarCambiosIva([
      'tipoConsulta' => 'ivaActual'
    ]);

    // Pedido
    $idPedido = $this->generarCodSeg([
      'tablaBD' => 'ordenes_entregas_presupuestos',
      'prefijo' => 'FACT',
      'campoID' => 'id_orden_entrega_presupuesto'
    ]);
    $resultado = $this->guardarDatos2([
      'tabla' => 'ordenes_entregas_presupuestos',
      'datos' => [
        'id_orden_entrega_presupuesto' => $idPedido,
        'rif_cedula_cliente' => $_SESSION['cedula'],
        'id_cambio_iva' => $cambioIva['id_cambio_iva'],
        'fecha_orden_entrega_presupuesto' => $this->FechaHora_Sel('fecha_hora_BD'),
        'status' => 5,
      ],
    ]);
    if ($resultado == false || $resultado <= 0) {
      $error();
      return [
        'tipo' => 'simple',
        'titulo' => 'Error',
        'texto' => 'Ha ocurrido un error registrando el pedido',
        'icono' => 'error',
      ];
    }

    // Productos
    foreach ($this->productosPedido as $producto) {
      $idDetalle = $this->guardarDatos2([
        'tabla' => 'productos_ordenes_entregas_presupuestos',
        'datos' => [
          'id_presentacion_producto' => $producto['id_presentacion_producto'],
          'id_orden_entrega_presupuesto' => $idPedido,
          'cantidad_producto' => $producto['cantidad'],
        ]
      ]);
      if ($idDetalle == false || $idDetalle <= 0) {
        $error();
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'Ha ocurrido un error registrando los productos del pedido',
          'icono' => 'error',
        ];
      }
    }
    foreach ($descuentoStocks as $idProducto => $cantidades) {
      $resultado = $this->actualizarDatos2([
        'tabla' => 'productos',
        'datos' => [
          'stock_producto' => $cantidades['cantidadActual'] - $cantidades['cantidadDescontar']
        ],
        'WHERE' => [
          'id_producto' => $idProducto
        ]
      ]);
      if ($resultado <= 0 || $resultado == false) {
        $error();
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'Ha ocurrido un error actualizando el stock de los productos del pedido',
          'icono' => 'error',
        ];
      }
    }

    // Pago
    $idPago = $this->generarCodSeg([
      'tablaBD' => 'pagos',
      'prefijo' => 'PAG',
      'campoID' => 'id_pago'
    ]);
    $resultado = $this->guardarDatos2([
      'tabla' => 'pagos',
      'datos' => [
        'id_pago' => $idPago,
        'id_orden_entrega_presupuesto' => $idPedido,
        'fecha_pago' => $this->FechaHora_Sel('Fecha_Actual_BD'),
      ]
    ]);
    if ($resultado == false || $resultado <= 0) {
      $error();
      return [
        'tipo' => 'simple',
        'titulo' => 'Error al registrar el pago',
        'texto' => 'Ha ocurrido un error registrando el Pago',
        'icono' => 'error',
      ];
    }

    // Detalles - pagos
    $this->pagosPedido = $this->indexarArrays([
      'indice' => [
        'id_metodo_pago',
        'id_moneda',
        'id_banco_emisor',
        'id_banco_receptor',
        'referencia_pago'
      ],
      'camposAgrupar' => [
        'id_metodo_pago',
        'id_moneda',
        'id_banco_emisor',
        'id_banco_receptor',
        'referencia_pago'
      ],
      'camposSumar' => ['monto_pago'],
      'array' => $this->pagosPedido,
    ]);

    foreach ($this->pagosPedido as $pago) {
      $idDetallePago = $this->guardarDatos2([
        'tabla' => 'detalles_pagos',
        'datos' => [
          'id_pago' => $idPago,
          'id_metodo_pago' => $pago['id_metodo_pago'],
          'id_moneda' => $pago['id_moneda'],
          'monto_pago' => $pago['monto_pago'],
        ]
      ]);
      if ($idDetallePago == false || $idDetallePago <= 0) {
        $error();
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'Ha ocurrido un error registrando los detalles del pago',
          'icono' => 'error',
        ];
      }
      if (($pago['id_banco_emisor'] ?? '') != '') {
        $idDetalleBancoEmisor = $this->guardarDatos2([
          'tabla' => 'bancos_detalles_pagos',
          'datos' => [
            'id_detalle_pago' => $idDetallePago,
            'id_banco' => $pago['id_banco_emisor'],
            'es_emisor' => 1,
          ]
        ]);
        if ($idDetalleBancoEmisor == false || $idDetalleBancoEmisor <= 0) {
          $error();
          return [
            'tipo' => 'simple',
            'titulo' => 'Error',
            'texto' => 'Ha ocurrido un error registrando el banco emisor del detalle del pago',
            'icono' => 'error',
          ];
        }
      }
      if (($pago['id_banco_receptor'] ?? '') != '') {
        $idDetalleBancoReceptor = $this->guardarDatos2([
          'tabla' => 'bancos_detalles_pagos',
          'datos' => [
            'id_detalle_pago' => $idDetallePago,
            'id_banco' => $pago['id_banco_receptor'],
            'es_emisor' => 0,
          ]
        ]);
        if ($idDetalleBancoReceptor == false || $idDetalleBancoReceptor <= 0) {
          $error();
          return [
            'tipo' => 'simple',
            'titulo' => 'Error',
            'texto' => 'Ha ocurrido un error registrando el banco receptor del detalle del pago',
            'icono' => 'error',
          ];
        }
      }
      if (($pago['referencia_pago'] ?? '') != '') {
        $idDetalleReferencia = $this->guardarDatos2([
          'tabla' => 'referencias_detalles_pagos',
          'datos' => [
            'id_detalle_pago' => $idDetallePago,
            'referencia_pago' => $pago['referencia_pago'],
          ]
        ]);
        if ($idDetalleReferencia == false || $idDetalleReferencia <= 0) {
          $error();
          return [
            'tipo' => 'simple',
            'titulo' => 'Error',
            'texto' => 'Ha ocurrido un error registrando la referencia del detalle del pago',
            'icono' => 'error',
          ];
        }
      }
    }

    //Comprobantes del pago
    
    $comprobantesPagos= $this->Imagenes_Reg('comprobantes_pagos',$this->comprobantesPagos,'comprobantes_pagos');
    $this->imgTrans = [
      'subCarpeta' => 'comprobantes_pagos',
      'imagenes' => $comprobantesPagos
    ];
    if (count($comprobantesPagos) != count(($this->comprobantesPagos['name'] ?? []))) {
      $error();
      return [
        'tipo' => 'simple',
        'titulo' => 'Error de guardado',
        'texto' => 'No se han podido guardar todos los comprobantes del pago!!! '.count($comprobantesPagos),
        'icono' => 'error',
      ];
    }
    foreach ($comprobantesPagos as $comprobante) {
      $resultado = $this->guardarDatos2([
        'tabla' => 'comprobantes_pagos',
        'datos' => [
          'id_pago' => $idPago,
          'path_comprobante' => $comprobante,
        ]
      ]);
      if ($resultado == false || $resultado <= 0) {
        $error();
        return [
          'tipo' => 'simple',
          'titulo' => 'Error al registrar los comprobantes del pago',
          'texto' => 'Ha ocurrido un error registrando los comprobantes del pago',
          'icono' => 'error',
        ];
      }
    }

    // Latitud
    $idLatitud = $this->VEYSNEC([
      'tabla' => 'latitudes_direcciones',
      'campos' => 'id_latitud_direccion',
      'WHERE' => [
        'coordenada_latitud' => $this->deliveryPedido['latitud'],
      ]
    ]);

    // Longitud
    $idLongitud = $this->VEYSNEC([
      'tabla' => 'longitudes_direcciones',
      'campos' => 'id_longitud_direccion',
      'WHERE' => [
        'coordenada_longitud' => $this->deliveryPedido['longitud'],
      ]
    ]);

    // Direccion - Ruta
    $idDireccion = $this->VEYSNEC([
      'tabla' => 'direcciones',
      'campos' => 'id_direccion',
      'WHERE' => [
        'id_ruta' => $rutaBD['id_ruta'],
        'id_latitud_direccion' => $idLatitud,
        'id_longitud_direccion' => $idLongitud,
      ]
    ]);

    // Delivery
    $idDelivery = $this->generarCodSeg([
      'tablaBD' => 'deliveries',
      'prefijo' => 'DELI',
      'campoID' => 'id_delivery'
    ]);
    $resultado = $this->guardarDatos2([
      'tabla' => 'deliveries',
      'datos' => [
        'id_delivery' => $idDelivery,
        'id_orden_entrega_presupuesto' => $idPedido,
        'id_direccion' => $idDireccion,
      ]
    ]);
    if ($resultado == false || $resultado <= 0) {
      $error();
      return [
        'tipo' => 'simple',
        'titulo' => 'Error',
        'texto' => 'Ha ocurrido un error registrando el pedido',
        'icono' => 'error',
      ];
    }

    $objetoNot = new mensajesWSModelo();
    $resultado = $objetoNot->enviarMensajesWS([
      "receptor" => [
        'tipo' => 'porPermisos',
        'permisos' => [
          'pedidos' => 'ver pedidos de los clientes',
        ]
      ],
      'cuerpo' => [
        [
          'accion' => "borrarDataModuloSS",
          'modulo' => 'pedidos'
        ],
        [
          'accion' => 'alertar',
          'alerta' => [
            'tipo' => 'simple',
            'titulo' => 'Pedido nuevo',
            'texto' => 'Acaba de llegar un pedido nuevo',
            'icono' => 'info',
            'notifier' => true,
          ]
        ],
        [
          'accion' => "actDT",
          'modulo' => 'pedidos'
        ],
      ],
      'noCommit' => true
    ]);
    if (($resultado['icono'] ?? '') == 'error' && !isset($_COOKIE['TEMP']) && !isset($_ENV['MODO_TESTEO'])) return $resultado;
    $objBitacora->registrarBitacora('pedidos', 'registrar', 'Éxito');
    $this->commit();
    return [
      'tipo' => 'simple',
      'titulo' => 'Pedido Registrado',
      'texto' => 'El pedido ha sido registrado con exito!!!',
      'icono' => 'success',
    ];
    // #endregion TRANSACCIÓN
  }
  private function asignarRepartidoresPedidosP() {
    $objBitacora = new bitacoraModelo();
    $error = function () use ($objBitacora) {
      $this->rollback();
      $objBitacora->registrarBitacora('pedidos', 'Asignar Repartidor', 'Fallido', true);
    };

    //Delivery
    $resultado = $this->actualizarDatos2([
      'tabla' => 'deliveries',
      'datos' => [
        'cedula_repartidor' => $this->deliveryPedido['cedula_repartidor']
      ],
      'WHERE' => [
        'id_delivery' => $this->deliveryPedido['id_delivery']
      ]
    ]);
    if ($resultado <= 0 || $resultado == false) {
      $error();
      return [
        'tipo' => 'simple',
        'texto' => 'No se ha podido asignar el repartidor al delivery',
        'titulo' => 'Asiganción fallida',
        'icono' => 'error'
      ];
    }

    //status
    $resultado = $this->actualizarDatos2([
      'tabla' => 'ordenes_entregas_presupuestos',
      'datos' => [
        'status' => 7,
        'cedula_usuario' => $_SESSION['cedula'],
      ],
      'WHERE' => [
        'id_orden_entrega_presupuesto' => $this->idPedido
      ]
    ]);
    if ($resultado <= 0 || $resultado == false) {
      $error();
      return [
        'tipo' => 'simple',
        'texto' => 'No se ha podido actualizar el estado del pedido',
        'titulo' => 'Asiganción fallida',
        'icono' => 'error'
      ];
    }

    $objBitacora->registrarBitacora('pedidos', 'Asignar Repartidor', 'Éxito');
    $this->commit();
    return [
      'tipo' => 'limpiarYcerrar',
      'texto' => 'Repartidor asignado correctamente',
      'titulo' => 'Asiganción Exitosa',
      'icono' => 'success'
    ];
  }
  private function actualizarPedidoP() {
    $objBitacora = new bitacoraModelo();

    $error = function () use ($objBitacora) {
      $this->rollback();
      $objBitacora->registrarBitacora('pedidos', 'actualizar', 'Fallido', true);
    };
    $dataActualProducto = $this->listarPedidos(['id_pedido' => $this->idPedido]);
    return $dataActualProducto;
    [
      "productos" => $productos,
    ] = $dataActualProducto;

    //Acciones expecificas
    $cedulaVendedor = '';
    switch ($this->statusPedido) {
      case 6: //Cancelado
        //Devolver stock de los productos
        foreach ($productos as $producto) {
          $stockActual = $this->seleccionarDatos2([
            'tabla' => 'productos',
            'campos' => 'stock_producto',
            'WHERE' => [
              'id_producto' => $producto['id_producto']
            ]
          ])->fetch(PDO::FETCH_COLUMN);
          $cantidadBruta = $producto['cantidad_pmp'] * $producto['cantidad_producto'];
          $resultado = $this->actualizarDatos2([
            'tabla' => 'productos',
            'datos' => [
              'stock_producto' => $stockActual + $cantidadBruta,
            ],
            'WHERE' => [
              'id_producto' => $producto['id_producto']
            ]
          ]);
          if ($resultado <= 0 || $resultado == false) {
            $error();
            return [
              'tipo' => 'simple',
              'titulo' => 'Error en la actualización',
              'texto' => 'Ocurrió un error en la actualización del stock de los productos',
              'icono' => 'error'
            ];
          }
        }
        //Borrar los supuestos "pagos"
        $resultado = $this->eliminarDatos2([
          'tabla' => 'pagos',
          'WHERE' => [
            'id_orden_entrega_presupuesto' => $this->idPedido
          ]
        ]);
        if ($resultado <= 0 || $resultado == false) {
          $error();
          return [
            'tipo' => 'simple',
            'titulo' => 'Error en la actualización',
            'texto' => 'Ocurrió un error eliminando los pagos del pedido',
            'icono' => 'error'
          ];
        }
        break;
      case 7:
        $cedulaVendedor = $_SESSION['cedula'];
        break;
    }

    //Estado
    $resultado = $this->actualizarDatos2([
      'tabla' => 'ordenes_entregas_presupuestos',
      'datos' => [
        'cedula_usuario' => $cedulaVendedor,
        'status' => $this->statusPedido,
      ],
      'WHERE' => [
        'id_orden_entrega_presupuesto' => $this->idPedido
      ]
    ]);

    if ($resultado <= 0 || $resultado == false) {
      $error();
      return [
        'tipo' => 'simple',
        'titulo' => 'Error en la actualización',
        'texto' => 'No se pudo actualizar el estado del pedido',
        'icono' => 'error'
      ];
    }
    $objBitacora->registrarBitacora('pedidos', 'actualizar', 'Éxito');
    $this->commit();
    return [
      'tipo' => 'simple',
      'titulo' => 'Actualización exitosa',
      'texto' => 'Se actualizó correctamente el estado del pedido',
      'icono' => 'success'
    ];
  }
}

/*
  Estados del Pedido
  5: Pendiente de confirmar
  6: Rechazado
  7: Confirmado
  8: Entregado
*/
