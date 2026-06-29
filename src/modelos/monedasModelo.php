<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\mensajesWSModelo;
use src\modelos\bitacoraModelo;
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

  private function seleccionarMonedasP(array $info) {
    if ($this->idMoneda == null || $this->idMoneda == "") {;
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
      $objBitacora->registrarBitacora('monedas', 'registrar', 'Fallido', true);
      return [
        "tipo" => "simple",
        "titulo" => "Moneda no registrada",
        "texto" => "La moneda no ha sido registrada exitosamente",
        "icono" => "error",
      ];
    }

    $objBitacora->registrarBitacora('monedas', 'registrar', 'Éxito', true);

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

    // Datos antes
    $datosAntes = $this->seleccionarMonedas(['id_moneda' => $this->idMoneda]);

    $instruccionesBD = [
      "tabla" => "monedas",
      "datos" => [
        "valor_moneda" => $this->valorMoneda,
      ],
      "WHERE" => [
        "id_moneda" => $this->idMoneda,
      ]
    ];

    if ($tipoAct == 'completa') {
      $instruccionesBD['datos']['nombre_moneda'] = $this->nombreMoneda;
      $instruccionesBD['datos']['simbolo_moneda'] = $this->simboloMoneda;
    }

    $resultado = $this->actualizarDatos2($instruccionesBD);

    if ($resultado == false || $resultado <= 0) {
      $this->rollback();
      $objBitacora->registrarBitacora('monedas', 'actualizar', 'Sin cambios', true);
      return [
        "tipo" => "simple",
        "titulo" => "Sin cambios realizados",
        "texto" => "No se realizó ningún cambio en la moneda",
        "icono" => "warning",
      ];
    }

    $datosDespues = $this->seleccionarMonedas(['id_moneda' => $this->idMoneda]);

    $objBitacora->registrarBitacora(
      'monedas',
      'actualizar',
      'Éxito',
      true,
      $datosAntes,
      $datosDespues
    );

    $this->commit();
    $mensajeNotificacion = 'La moneda "' . $datosAntes['nombre_moneda'] . '" ha sido actualizada';

    if ($datosAntes['valor_moneda'] != $this->valorMoneda) {
      $mensajeNotificacion .= ' (valor: ' . $datosAntes['valor_moneda'] . ' → ' . $this->valorMoneda . ')';
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
            'titulo' => 'Moneda actualizada',
            'texto' => $mensajeNotificacion,
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

    $resultado = $this->eliminarDatos2([
      'tabla' => "monedas",
      'WHERE' => [
        "id_moneda" => $this->idMoneda
      ]
    ]);

    if ($resultado == 1) {
      $objBitacora->registrarBitacora('monedas', 'eliminar', 'Éxito', true);

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
              'texto' => 'La moneda ha sido eliminada del sistema',
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
      $objBitacora->registrarBitacora('monedas', 'eliminar', 'Fallido', true);
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
