<?php

namespace src\modelos;

use src\config\connect\conexion;
use PDO;

class metodosPagoModelo extends conexion {
  private int $idMetodoPago = 0;
  private string $nombreMetodoPago = '';
  private int $necesitaMoneda = 0;
  private int $necesitaBancoEmisor = 0;
  private int $necesitaBancoReceptor = 0;
  private int $necesitaReferencia = 0;
  private int $mostrarEcommerce = 0;

  public function validarMetodosPagos(array $instruccionesVal) {
    [
      'infoVal' => &$infoVal,
      'camposVal' => &$camposVal,
    ] = $instruccionesVal;
    $funcionAsignadora = function ($nombreCampo, &$valor) {
      $claveVal = [
        'id_metodo_pago' => [
          "campo_nombre" => "id_metodo_pago",
          "campo_valor" => &$valor,
          "formulario_nombre" => "ID",
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "metodos_pagos",
          "requerido" => true,
          "debeSerUnico" => true,
          "debeExistir" => true,
        ],
        "nombre_metodo_pago" => [
          "campo_nombre" => "nombre_metodo_pago",
          "campo_valor" => &$valor,
          "formulario_nombre" => "nombre",
          "requerido" => true,
          "minimo" => minRegexNombreObj,
          "maximo" => maxRegexNombreObj,
          "expresion_re" => regexNombreObj,
          "tabla" => "metodos_pagos",
          "debeSerUnico" => true
        ],
        "necesita_moneda" => [
          "campo_valor" => &$valor,
          "formulario_nombre" => "si necesita moneda",
          "requerido" => true,
          "minimo" => minRegexValorBoleano,
          "maximo" => maxRegexValorBoleano,
          "expresion_re" => regexValorBoleano,
        ],
        "necesita_banco_emisor" => [
          "campo_valor" => &$valor,
          "formulario_nombre" => "si necesita banco emisor",
          "requerido" => true,
          "minimo" => minRegexValorBoleano,
          "maximo" => maxRegexValorBoleano,
          "expresion_re" => regexValorBoleano,
        ],
        "necesita_banco_receptor" => [
          "campo_valor" => &$valor,
          "formulario_nombre" => "si necesita banco receptor",
          "requerido" => true,
          "minimo" => minRegexValorBoleano,
          "maximo" => maxRegexValorBoleano,
          "expresion_re" => regexValorBoleano,
        ],
        "necesita_referencia" => [
          "campo_valor" => &$valor,
          "formulario_nombre" => "si necesita referencia",
          "requerido" => true,
          "minimo" => minRegexValorBoleano,
          "maximo" => maxRegexValorBoleano,
          "expresion_re" => regexValorBoleano,
        ],
        "mostrar_ecommerce" => [
          "campo_valor" => &$valor,
          "formulario_nombre" => "si necesita referencia",
          "requerido" => true,
          "minimo" => minRegexValorBoleano,
          "maximo" => maxRegexValorBoleano,
          "expresion_re" => regexValorBoleano,
        ]
      ];
      return $claveVal[$nombreCampo];
    };
    $campos = [];
    foreach ($camposVal as $campo) {
      switch ($campo) {
        case 'presentaciones':
          if (($infoVal['presentaciones'] ?? []) == []) {
            return [
              'tipo' => 'simple',
              'titulo' => 'Sin presentaciones',
              'texto' => 'No has enviado las presentaciones de la materia prima',
              'icono' => 'warning',
            ];
          }
          foreach ($infoVal['presentaciones'] as &$pre) {
            $campos[] = $funcionAsignadora('id_presentacion', $pre);
          }
          unset($idPre);
          break;
        case 'necesita_banco_emisor':
        case 'necesita_banco_receptor':
        case 'necesita_moneda':
        case 'necesita_referencia':
        case 'mostrar_ecommerce':
          $infoVal[$campo] = $infoVal[$campo] ?? 0;
          $campos[] = $funcionAsignadora($campo, $infoVal[$campo]);
          break;
        default:
          $campos[] = $funcionAsignadora($campo, $infoVal[$campo]);
          break;
      }
    }
    return $this->limpiar_Verificar($campos);
  }
  public function seleccionarMetodosPagos(array $info) {

    if (($info['id_metodo_pago'] ?? '') != '') {
      $resultado = $this->validarMetodosPagos([
        'infoVal' => &$info,
        'camposVal' => [
          'id_metodo_pago',
        ],
      ]);
      if ($resultado) return $resultado;
      $this->idMetodoPago = $info['id_metodo_pago'];
    }
    return $this->seleccionarMetodosPagosP($info);
  }
  public function registrarMetodosPagos(array $info) {
    $resultado = $this->validarMetodosPagos([
      'infoVal' => &$info,
      'camposVal' => [
        'nombre_metodo_pago',
        'necesita_moneda',
        'necesita_banco_emisor',
        'necesita_banco_receptor',
        'necesita_referencia',
        'mostrar_ecommerce'
      ],
    ]);
    if ($resultado) return $resultado;

    $this->nombreMetodoPago = $info['nombre_metodo_pago'];
    $this->necesitaMoneda = $info['necesita_moneda'];
    $this->necesitaBancoEmisor = $info['necesita_banco_emisor'];
    $this->necesitaBancoReceptor = $info['necesita_banco_receptor'];;
    $this->necesitaReferencia = $info['necesita_referencia'];
    $this->mostrarEcommerce = $info['mostrar_ecommerce'];

    return $this->registrarMetodosPagosP();
  }
  public function actualizarMetodosPagos(array $info) {
    $resultado = $this->validarMetodosPagos([
      'infoVal' => &$info,
      'camposVal' => [
        'id_metodo_pago',
        'nombre_metodo_pago',
        'necesita_moneda',
        'necesita_banco_emisor',
        'necesita_banco_receptor',
        'necesita_referencia',
        'mostrar_ecommerce'
      ],
    ]);
    if ($resultado) return $resultado;

    $this->idMetodoPago = $info['id_metodo_pago'];
    $this->nombreMetodoPago = $info['nombre_metodo_pago'];
    $this->necesitaMoneda = $info['necesita_moneda'];
    $this->necesitaBancoEmisor = $info['necesita_banco_emisor'];
    $this->necesitaBancoReceptor = $info['necesita_banco_receptor'];;
    $this->necesitaReferencia = $info['necesita_referencia'];
    $this->mostrarEcommerce = $info['mostrar_ecommerce'];

    return $this->actualizarMetodosPagosP();
  }
  public function eliminarMetodosPagos(array $info) {
    $resultado = $this->validarMetodosPagos([
      'infoVal' => &$info,
      'camposVal' => [
        'id_metodo_pago',
      ],
    ]);
    if ($resultado) return $resultado;

    $this->idMetodoPago = $info['id_metodo_pago'];

    return $this->eliminarMetodosPagosP();
  }

  private function seleccionarMetodosPagosP(array $info) {
    if ($this->idMetodoPago != 0 && $this->idMetodoPago != '') {
      return $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'metodos_pagos',
        'WHERE' => [
          'id_metodo_pago' => $this->idMetodoPago
        ],
      ])->fetch();
    } else {
      switch ($info['tipoConsulta'] ?? "") {
        case 'paraEcommerce':
          return $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'metodos_pagos',
            'WHERE' => [
              'mostrar_ecommerce' => 1
            ],
          ])->fetchAll();
        case 'indexadosPorId':

          return $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'metodos_pagos',
          ])->fetchAll(PDO::FETCH_UNIQUE);
        default:
          return $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'metodos_pagos',
          ])->fetchAll();
      }
    }
  }
  private function registrarMetodosPagosP() {
    $objBitacora = new bitacoraModelo();
    $resultado = $this->guardarDatos2([
      'tabla' => 'metodos_pagos',
      'datos' => [
        "id_metodo_pago" => $this->idMetodoPago,
        "nombre_metodo_pago" => $this->nombreMetodoPago,
        "necesita_moneda" => $this->necesitaMoneda,
        "necesita_banco_emisor" => $this->necesitaBancoEmisor,
        "necesita_banco_receptor" => $this->necesitaBancoReceptor,
        "necesita_referencia" => $this->necesitaReferencia,
        "mostrar_ecommerce" => $this->mostrarEcommerce,
      ]
    ]);
    if ($resultado <= 0) {
      $rb = $objBitacora->registrarBitacora('metodos-pagos', 'registrar', 'fallido', true);
      if (($rb['icono'] ?? '') == 'error') return $rb;
      return [
        "tipo" => "simple",
        "titulo" => "Error",
        "texto" => "No se pudo registrar",
        "icono" => "error"
      ];
    }
    $rb = $objBitacora->registrarBitacora('metodos-pagos', 'registrar', 'éxito');
    if ($rb) return $rb;

    $this->commit();
    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Registro Exitoso",
      "texto" => "Método de pago registrado",
      "icono" => "success"
    ];
  }
  private function actualizarMetodosPagosP() {
    $resultado = $this->actualizarDatos2([
      'tabla' => 'metodos_pagos',
      'datos' => [
        "nombre_metodo_pago" => $this->nombreMetodoPago,
        "necesita_moneda" => $this->necesitaMoneda,
        "necesita_banco_emisor" => $this->necesitaBancoEmisor,
        "necesita_banco_receptor" => $this->necesitaBancoReceptor,
        "necesita_referencia" => $this->necesitaReferencia,
        "mostrar_ecommerce" => $this->mostrarEcommerce,
      ],
      'WHERE' => ["id_metodo_pago" => $this->idMetodoPago]
    ]);

    $objBitacora = new bitacoraModelo();
    if ($resultado == false || $resultado <= 0) {
      $rb = $objBitacora->registrarBitacora('metodos-pagos', 'actualizar', 'error', true);
      if (($rb['icono'] ?? '') == 'error') return $rb;
      return [
        "tipo" => "simple",
        "titulo" => "Error",
        "texto" => "Ocurrió un error al procesar la solicitud",
        "icono" => "error"
      ];
    }
    $rb = $objBitacora->registrarBitacora('metodos-pagos', 'actualizar', 'éxito');
    if ($rb) return $rb;

    $this->commit();
    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Actualización Exitosa",
      "texto" => "Método actualizado",
      "icono" => "success"
    ];
  }
  private function eliminarMetodosPagosP() {
    $resultado = $this->eliminarDatos2([
      'tabla' => 'metodos_pagos',
      'WHERE' => [
        "id_metodo_pago" => $this->idMetodoPago
      ]
    ]);

    $objBitacora = new bitacoraModelo();
    if ($resultado <= 0) {
      $rb = $objBitacora->registrarBitacora('metodos-pagos', 'eliminar', 'error', true);
      if (($rb['icono'] ?? '') == 'error') return $rb;
      return [
        "tipo" => "simple",
        "titulo" => "Error",
        "texto" => "No se pudo eliminar",
        "icono" => "error"
      ];
    }

    $rb = $objBitacora->registrarBitacora('metodos-pagos', 'eliminar', 'éxito');
    if ($rb) return $rb;
    $this->commit();
    return [
      "tipo" => "simple",
      "titulo" => "Eliminado",
      "texto" => "El método ha sido desactivado",
      "icono" => "success"
    ];
  }
}
