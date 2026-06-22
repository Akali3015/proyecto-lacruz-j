<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use src\modelos\mensajesWSModelo;
use PDO;

class monedasModelo extends conexion {

  private int $idMoneda = 0;
  private string $nombreMoneda = '';
  private string $simboloMoneda = '';
  private float $valorMoneda = 0;

  public function validarMonedas(array $instruccionesVal) {
    [
      'infoVal' => &$infoVal,
      'camposVal' => &$camposVal,
    ] = $instruccionesVal;
    $funcionAsignadora = function ($nombreCampo, &$valor) {
      $claveVal = [
        'id_moneda' => [
          "campo_nombre" => "id_moneda",
          "campo_valor" => &$valor,
          "formulario_nombre" => "id",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "monedas",
          "debeSerUnico" => true,
          "debeExistir" => true,
        ],
        'valor_moneda' => [
          "campo_valor" => &$valor,
          "formulario_nombre" => "valor",
          "requerido" => true,
          "minimo" => minRegexPrecio,
          "maximo" => maxRegexPrecio,
          "expresion_re" => regexPrecio,
          'comaPunto' => true
        ],
        'nombre_moneda' => [
          "campo_nombre" => "nombre_moneda",
          "campo_valor" => &$valor,
          "formulario_nombre" => "nombre de la moneda",
          "requerido" => true,
          "minimo" => minRegexNombreObj,
          "maximo" => maxRegexNombreObj,
          "expresion_re" => regexNombreObj,
          "tabla" => "monedas",
          "debeSerUnico" => true,
        ],
        'simbolo_moneda' => [
          "campo_nombre" => "simbolo_moneda",
          "campo_valor" => &$valor,
          "formulario_nombre" => "simbolo de la moneda",
          "requerido" => true,
          "minimo" => minRegexSimboloMoneda,
          "maximo" => maxRegexSimboloMoneda,
          "expresion_re" => regexSimboloMoneda,
          "tabla" => "monedas",
          "debeSerUnico" => true,
        ],
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

        default:
          $campos[] = $funcionAsignadora($campo, $infoVal[$campo]);
          break;
      }
    }
    return $this->limpiar_Verificar($campos);
  }

  public function seleccionarMonedas(array $info) {
    if (($info['id_moneda'] ?? '') != '') {
      $resultado = $this->validarMonedas([
        'infoVal' => &$info,
        'camposVal' => [
          'id_moneda',
        ],
      ]);
      if ($resultado) return $resultado;
      $this->idMoneda = $info['id_moneda'];
    }
    return $this->seleccionarMonedasP($info);
  }

  public function seleccionarCambiosMonedas() {
    return $this->seleccionarCambiosMonedasP();
  }

  public function registrarMonedas(array $info) {
    $resultado = $this->validarMonedas([
      'infoVal' => &$info,
      'camposVal' => [
        'valor_moneda',
        'nombre_moneda',
        'simbolo_moneda'
      ],
    ]);
    if ($resultado) return $resultado;

    $this->nombreMoneda = $info['nombre_moneda'];
    $this->simboloMoneda = $info['simbolo_moneda'];
    $this->valorMoneda = $info['valor_moneda'];

    return $this->registrarMonedasP();
  }

  public function actualizarMonedas(array $info) {
    $campos = ['id_moneda', 'valor_moneda'];
    if (($info['tipoAct'] ?? '') == 'completa') {
      array_push($campos, 'nombre_moneda', 'simbolo_moneda');
    }
    $resultado = $this->validarMonedas([
      'infoVal' => &$info,
      'camposVal' => $campos,
    ]);
    if ($resultado) return $resultado;

    $this->idMoneda = $info['id_moneda'];
    $this->nombreMoneda = $info['nombre_moneda'] ?? '';
    $this->simboloMoneda = $info['simbolo_moneda'] ?? '';
    $this->valorMoneda = $info['valor_moneda'];

    return $this->actualizarMonedasP($info['tipoAct'] ?? NULL);
  }

  public function eliminarMonedas(array &$info) {
    $resultado = $this->validarMonedas([
      'infoVal' => &$info,
      'camposVal' => [
        'id_moneda',
      ],
    ]);
    if ($resultado) return $resultado;
    $this->idMoneda = $info['id_moneda'];
    return $this->eliminarMonedasP();
  }

  // ─── PRIVADOS ─────────────────────────────────────────────────────────

  private function seleccionarMonedasP(array $info) {
    if ($this->idMoneda == null || $this->idMoneda == "") {
      switch ($info['tipoConsulta'] ?? '') {
        case 'divisasPorFecha':
          return $this->seleccionarDatos2([
            'campos' => '
              mo.id_moneda,mo.nombre_moneda,mo.simbolo_moneda,
              (
                SELECT cm.valor_moneda 
                FROM cambios_monedas as cm 
                WHERE cm.id_moneda= mo.id_moneda
                ORDER BY cm.id_cambio_moneda DESC
                LIMIT 1
              ) as valor_fecha_moneda
            ',
            'tabla' => 'monedas as mo',
            'datosJoins' => [
              'cambios_monedas as cm' => 'mo.id_moneda = cm.id_moneda'
            ],
            'WHERE' => [
              'cm.fecha_cambio' => '<= ' . $info['fecha'],
            ],
            'GROUP' => 'mo.id_moneda',
            'ORDER' => 'cm.id_cambio_moneda DESC'
          ])->fetchAll(PDO::FETCH_UNIQUE);
        case 'indexadosPorId':
          return $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'monedas',
          ])->fetchAll(PDO::FETCH_UNIQUE);
        default:
          return $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'monedas',
            'ORDER' => 'nombre_moneda'
          ])->fetchAll();
      }
    } else {
      return $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'monedas',
        'WHERE' => [
          'id_moneda' => $this->idMoneda,
        ],
        'ORDER' => 'nombre_moneda'
      ])->fetch();
    }
  }

  private function seleccionarCambiosMonedasP() {
    return $this->seleccionarDatos2([
      'campos' => '*',
      'tabla' => 'v_cambios_monedas_todos',
    ])->fetchAll();
  }

  private function registrarMonedasP() {
    $objBitacora = new bitacoraModelo();

    $datosBitacora = [
      'nombre_moneda' => $this->nombreMoneda,
      'simbolo_moneda' => $this->simboloMoneda,
      'valor_moneda' => $this->valorMoneda,
      'fecha_registro' => date('Y-m-d H:i:s')
    ];

    $ultimoId = $this->guardarDatos2([
      'tabla' => 'monedas',
      'datos' => [
        "nombre_moneda" => $this->nombreMoneda,
        "simbolo_moneda" => $this->simboloMoneda,
        "valor_moneda" => $this->valorMoneda,
      ]
    ]);

    if ($ultimoId == false || $ultimoId <= 0) {
      $this->rollback();
      $objBitacora->registrarBitacora('monedas', 'Registrar', 'Error', $datosBitacora, true);
      return [
        "tipo" => "simple",
        "titulo" => "Moneda no registrada",
        "texto" => "La moneda no ha sido registrado exitosamente",
        "icono" => "error",
      ];
    }

    $objBitacora->registrarBitacora('monedas', 'Registrar', 'Éxito', $datosBitacora, true);
    $this->commit();

    $objetoNot = new mensajesWSModelo();
    $objetoNot->enviarMensajesWS([
      "receptor" => [
        'tipo' => 'todosSinExcepcion',
      ],
      'cuerpo' => [
        [
          'accion' => "borrarDataModuloSS",
          'modulo' => 'monedas'
        ],
        [
          'accion' => "actDT",
          'modulo' => 'monedas'
        ],
        [
          'accion' => 'alertar',
          'alerta' => [
            'tipo' => 'simple',
            'titulo' => 'Nueva moneda registrada',
            'texto' => 'Se ha registrado la moneda "' . $this->nombreMoneda . '" con valor ' . $this->valorMoneda,
            'icono' => 'info',
            'notifier' => true,
          ]
        ],
      ],
      'noCommit' => true
    ]);

    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Moneda registrada",
      "texto" => "La moneda ha sido registrada exitosamente",
      "icono" => "success",
    ];
  }

  private function actualizarMonedasP($tipoAct = null) {
    $objBitacora = new bitacoraModelo();

    $monedaAntes = $this->seleccionarMonedas(['id_moneda' => $this->idMoneda]);
    if (isset($monedaAntes['icono'])) {
      return $monedaAntes;
    }

    $instruccionesBD = [
      "tabla" => "monedas",
      "datos" => [
        "valor_moneda" => $this->valorMoneda,
      ],
      "WHERE" => [
        "id_moneda" => $this->idMoneda,
      ]
    ];

    $datosBitacora = [
      'id_moneda' => $this->idMoneda,
      'nombre_moneda' => $monedaAntes['nombre_moneda'],
      'cambios' => []
    ];

    $mensajeNotificacion = '';

    if ($monedaAntes['valor_moneda'] != $this->valorMoneda) {
      $datosBitacora['cambios']['valor_moneda'] = [
        'anterior' => $monedaAntes['valor_moneda'],
        'nuevo' => $this->valorMoneda
      ];
      $mensajeNotificacion .= ' valor de ' . $monedaAntes['valor_moneda'] . ' a ' . $this->valorMoneda;
    }

    if ($tipoAct == 'completa') {
      $instruccionesBD['datos']['nombre_moneda'] = $this->nombreMoneda;
      $instruccionesBD['datos']['simbolo_moneda'] = $this->simboloMoneda;

      if ($monedaAntes['nombre_moneda'] != $this->nombreMoneda) {
        $datosBitacora['cambios']['nombre_moneda'] = [
          'anterior' => $monedaAntes['nombre_moneda'],
          'nuevo' => $this->nombreMoneda
        ];
        $mensajeNotificacion .= ' nombre de "' . $monedaAntes['nombre_moneda'] . '" a "' . $this->nombreMoneda . '"';
      }
      if ($monedaAntes['simbolo_moneda'] != $this->simboloMoneda) {
        $datosBitacora['cambios']['simbolo_moneda'] = [
          'anterior' => $monedaAntes['simbolo_moneda'],
          'nuevo' => $this->simboloMoneda
        ];
        $mensajeNotificacion .= ' símbolo de "' . $monedaAntes['simbolo_moneda'] . '" a "' . $this->simboloMoneda . '"';
      }
    }

    $resultado = $this->actualizarDatos2($instruccionesBD);

    if ($resultado == false || $resultado <= 0) {
      $this->rollback();
      $datosBitacora['error'] = 'No se realizaron cambios';
      $objBitacora->registrarBitacora('monedas', 'Actualizar', 'Sin cambios', $datosBitacora, true);
      return [
        "tipo" => "simple",
        "titulo" => "Sin cambios realizados",
        "texto" => "No se realizó ningún cambio en la moneda",
        "icono" => "warning",
      ];
    }

    $datosBitacora['fecha_actualizacion'] = date('Y-m-d H:i:s');
    $objBitacora->registrarBitacora('monedas', 'Actualizar', 'Éxito', $datosBitacora, true);
    $this->commit();

    $tituloNotificacion = 'Moneda actualizada';
    $textoNotificacion = 'La moneda "' . $monedaAntes['nombre_moneda'] . '" ha sido actualizada:';
    if (!empty($mensajeNotificacion)) {
      $textoNotificacion .= $mensajeNotificacion;
    } else {
      $textoNotificacion = 'La moneda "' . $monedaAntes['nombre_moneda'] . '" ha sido actualizada';
    }

    $objetoNot = new mensajesWSModelo();
    $objetoNot->enviarMensajesWS([
      "receptor" => [
        'tipo' => 'todosSinExcepcion',
      ],
      'cuerpo' => [
        [
          'accion' => "borrarDataModuloSS",
          'modulo' => 'monedas'
        ],
        [
          'accion' => "actDT",
          'modulo' => 'monedas'
        ],
        [
          'accion' => 'alertar',
          'alerta' => [
            'tipo' => 'simple',
            'titulo' => $tituloNotificacion,
            'texto' => $textoNotificacion,
            'icono' => 'info',
            'notifier' => true,
          ]
        ],
      ],
      'noCommit' => true
    ]);

    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Moneda actualizada",
      "texto" => "La moneda ha sido actualizada exitosamente",
      "icono" => "success",
    ];
  }

  private function eliminarMonedasP() {
    $objBitacora = new bitacoraModelo();

    $monedaAntes = $this->seleccionarMonedas(['id_moneda' => $this->idMoneda]);
    if (isset($monedaAntes['icono'])) {
      return $monedaAntes;
    }

    $datosBitacora = [
      'id_moneda' => $this->idMoneda,
      'nombre_moneda' => $monedaAntes['nombre_moneda'],
      'simbolo_moneda' => $monedaAntes['simbolo_moneda'],
      'valor_moneda' => $monedaAntes['valor_moneda'],
      'fecha_eliminacion' => date('Y-m-d H:i:s'),
    ];

    $resultado = $this->eliminarDatos2([
      'tabla' => "monedas",
      'WHERE' => [
        "id_moneda" => $this->idMoneda
      ]
    ]);

    if ($resultado == 1) {
      $objBitacora->registrarBitacora('monedas', 'Eliminar', 'Éxito', $datosBitacora, true);
      
      $objetoNot = new mensajesWSModelo();
      $objetoNot->enviarMensajesWS([
        "receptor" => [
          'tipo' => 'todosSinExcepcion',
        ],
        'cuerpo' => [
          [
            'accion' => "borrarDataModuloSS",
            'modulo' => 'monedas'
          ],
          [
            'accion' => "actDT",
            'modulo' => 'monedas'
          ],
          [
            'accion' => 'alertar',
            'alerta' => [
              'tipo' => 'simple',
              'titulo' => 'Moneda eliminada',
              'texto' => 'La moneda "' . $monedaAntes['nombre_moneda'] . '" ha sido eliminada del sistema',
              'icono' => 'info',
              'notifier' => true,
            ]
          ],
        ],
        'noCommit' => true
      ]);
      
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Moneda eliminada",
        "texto" => "La moneda ha sido eliminada con éxito",
        "icono" => "success"
      ];
      $this->commit();
    } else {
      $datosBitacora['error'] = 'La moneda no existe en la base de datos';
      $objBitacora->registrarBitacora('monedas', 'Eliminar', 'Error', $datosBitacora, true);
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Moneda no encontrada",
        "texto" => "La moneda no existe en la Base de Datos",
        "icono" => "error"
      ];
    }
    return $alerta;
  }
}