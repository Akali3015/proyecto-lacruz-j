<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\traitModelo;
use src\modelos\bitacoraModelo;
use src\modelos\accesosModelo;
use src\modelos\mensajesWSModelo;
use PDO;

class unidadesMedidasModelo extends conexion {
  use traitModelo;

  private int $idUnidadMedida = 0;
  private string $nombreUnidadMedida = '';
  private string $simboloUnidadMedida = '';
  private float $equivalenciaUB = 0;

  public function validarUnidadesMedidas(string $permiso, ?array &$info = null, ?array $requerido = null) {
    $objAcceso = new accesosModelo();
    $v = $objAcceso->validarPermisos('unidadesMedidas', $permiso);
    if ($v) return $v;

    if ($info === null) return false;

    $esquema = [
      'tipo' => 'arrayA',
      'propiedades' => [
        'id_unidad_medida' => [
          ...molId,
          "nombreAlerta" => "id de la unidad de medida",
          "nombreBD" => "id_unidad_medida",
          "tablaBD" => "unidades_medidas",
          "debeExistirBD" => true
        ],
        'nombre_unidad_medida' => [
          ...molNombreObj,
          "nombreAlerta" => "nombre de la unidad de medida",
          "nombreBD" => "nombre_unidad_medida",
          "tablaBD" => "unidades_medidas",
          "debeSerUnicoBD" => true
        ],
        'simbolo_unidad_medida' => [
          ...molNombreObj,
          "nombreAlerta" => "simbolo de la unidad de medida",
          "nombreBD" => "simbolo_unidad_medida",
          "tablaBD" => "unidades_medidas",
          "debeSerUnicoBD" => true
        ],
        'equivalencia_ub' => [
          ...molPrecioFormateado,
          "nombreAlerta" => "equivalencia de la unidad base"
        ]
      ],
      'requerido' => $requerido ?? []
    ];

    $v = $this->limpiarValidar($info, $esquema);
    if ($v) return $v;
    return false;
  }

  public function seleccionarUnidadesMedidas(array $info) {
    $requerido = [];
    if (($info['id_unidad_medida'] ?? '') != "") $requerido[] = 'id_unidad_medida';
    $r = $this->validarUnidadesMedidas('ver', $info, $requerido);
    if (($info['id_unidad_medida'] ?? '') != "") $this->idUnidadMedida = $info['id_unidad_medida'];
    if ($r) return $r;
    return $this->seleccionarUnidadesMedidasP();
  }

  public function registrarUnidadesMedidas(array $info) {
    $resultado = $this->validarUnidadesMedidas('registrar', $info, [
      'nombre_unidad_medida',
      'simbolo_unidad_medida',
      'equivalencia_ub',
    ]);
    if ($resultado) return $resultado;

    [
      'nombre_unidad_medida' => $this->nombreUnidadMedida,
      'simbolo_unidad_medida' => $this->simboloUnidadMedida,
      'equivalencia_ub' => $this->equivalenciaUB
    ] = $info;

    return $this->registrarUnidadesMedidasP();
  }

  public function actualizarUnidadesMedidas(array $info) {
    $resultado = $this->validarUnidadesMedidas('actualizar', $info, [
      'id_unidad_medida',
      'nombre_unidad_medida',
      'simbolo_unidad_medida',
      'equivalencia_ub',
    ]);
    if ($resultado) return $resultado;

    [
      'id_unidad_medida' => $this->idUnidadMedida,
      'nombre_unidad_medida' => $this->nombreUnidadMedida,
      'simbolo_unidad_medida' => $this->simboloUnidadMedida,
      'equivalencia_ub' => $this->equivalenciaUB
    ] = $info;

    return $this->actualizarUnidadesMedidasP();
  }

  public function eliminarUnidadesMedidas(array $info) {
    $resultado = $this->validarUnidadesMedidas('eliminar', $info, [
      'id_unidad_medida',
    ]);
    if ($resultado) return $resultado;

    $this->idUnidadMedida = $info['id_unidad_medida'];
    return $this->eliminarUnidadesMedidasP();
  }

  private function seleccionarUnidadesMedidasP() {
    if ($this->idUnidadMedida == 0) {
      return $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'v_unidades_medidas_todas',
      ])->fetchAll();
    } else {
      return $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'unidades_medidas',
        'WHERE' => [
          "id_unidad_medida" => $this->idUnidadMedida,
        ]
      ])->fetch(PDO::FETCH_ASSOC);
    }
  }

  private function registrarUnidadesMedidasP() {
    $ultimoId = $this->guardarDatos2([
      'tabla' => 'unidades_medidas',
      'datos' => [
        "nombre_unidad_medida" => $this->nombreUnidadMedida,
        "simbolo_unidad_medida" => $this->simboloUnidadMedida,
        "equivalencia_ub" => $this->equivalenciaUB,
      ]
    ]);

    $objBitacora = new bitacoraModelo();
    if ($ultimoId == false || $ultimoId <= 0) {
      $this->rollback();
      $objBitacora->registrarBitacora([
        'modulo' => 'unidadesMedidas',
        'accion' => 'registrar',
        'resultado' => 'Fallido',
        'viejo' => [],
        'nuevo' => [
          'nombre_unidad_medida' => $this->nombreUnidadMedida,
          'simbolo_unidad_medida' => $this->simboloUnidadMedida,
          'equivalencia_ub' => $this->equivalenciaUB,
        ]
      ]);
      return [
        "tipo" => "simple",
        "titulo" => "Unidad de medida no registrada",
        "texto" => "La unidad de medida no ha sido registrada exitosamente",
        "icono" => "error",
      ];
    }

    $objBitacora->registrarBitacora([
      'modulo' => 'unidadesMedidas',
      'accion' => 'registrar',
      'resultado' => 'Éxito',
      'viejo' => [],
      'nuevo' => [
        'id_unidad_medida' => $ultimoId,
        'nombre_unidad_medida' => $this->nombreUnidadMedida,
        'simbolo_unidad_medida' => $this->simboloUnidadMedida,
        'equivalencia_ub' => $this->equivalenciaUB
      ]
    ]);

    $objetoNot = new mensajesWSModelo();
    $objetoNot->enviarMensajesWS([
      "receptor" => [
        'tipo' => 'permisos',
        'permisos' => ['unidadesMedidas' => ['ver']]
      ],
      'cuerpo' => [
        ['accion' => "borrarDataModuloSS", 'modulo' => 'unidades_medidas'],
        ['accion' => "actDT", 'modulo' => 'unidades_medidas'],
        [
          'accion' => 'alertar',
          'alerta' => [
            'tipo' => 'simple',
            'titulo' => 'Unidades de Medida',
            'texto' => "Se ha registrado la unidad: {$this->nombreUnidadMedida}",
            'icono' => 'info',
            'notifier' => true,
          ]
        ]
      ],
      'noCommit' => true
    ]);

    $this->commit();
    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Unidad de medida registrada",
      "texto" => "La unidad de medida ha sido registrada exitosamente",
      "icono" => "success",
    ];
  }

  private function actualizarUnidadesMedidasP() {
    // Para ver los datos antes de actualizar
    $viejo = $this->seleccionarDatos2([
      'campos' => '*',
      'tabla' => 'unidades_medidas',
      'WHERE' => ['id_unidad_medida' => $this->idUnidadMedida]
    ])->fetch(PDO::FETCH_ASSOC);

    $resultado = $this->actualizarDatos2([
      "tabla" => "unidades_medidas",
      "datos" => [
        "nombre_unidad_medida" => $this->nombreUnidadMedida,
        "simbolo_unidad_medida" => $this->simboloUnidadMedida,
        "equivalencia_ub" => $this->equivalenciaUB,
      ],
      "WHERE" => [
        "id_unidad_medida" => $this->idUnidadMedida,
      ]
    ]);

    $objBitacora = new bitacoraModelo();
    if ($resultado == false || $resultado <= 0) {
      $objBitacora->registrarBitacora([
        'modulo' => 'unidadesMedidas',
        'accion' => 'actualizar',
        'resultado' => 'Fallido',
        'viejo' => $viejo,
        'nuevo' => [
          'id_unidad_medida' => $this->idUnidadMedida,
          'nombre_unidad_medida' => $this->nombreUnidadMedida,
          'simbolo_unidad_medida' => $this->simboloUnidadMedida,
          'equivalencia_ub' => $this->equivalenciaUB
        ]
      ]);
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Sin cambios realizados",
        "texto" => "No se realizó ningún cambio en la unidad de medida",
        "icono" => "warning",
      ];
    } else {
      $objBitacora->registrarBitacora([
        'modulo' => 'unidadesMedidas',
        'accion' => 'actualizar',
        'resultado' => 'Éxito',
        'viejo' => $viejo,
        'nuevo' => [
          'id_unidad_medida' => $this->idUnidadMedida,
          'nombre_unidad_medida' => $this->nombreUnidadMedida,
          'simbolo_unidad_medida' => $this->simboloUnidadMedida,
          'equivalencia_ub' => $this->equivalenciaUB
        ]
      ]);
      $alerta = [
        "tipo" => "limpiarYcerrar",
        "titulo" => "Unidad de medida actualizada",
        "texto" => "La unidad de medida ha sido actualizada exitosamente",
        "icono" => "success",
      ];

      $objetoNot = new mensajesWSModelo();
      $objetoNot->enviarMensajesWS([
        "receptor" => [
          'tipo' => 'permisos',
          'permisos' => ['unidadesMedidas' => ['ver']]
        ],
        'cuerpo' => [
          ['accion' => "borrarDataModuloSS", 'modulo' => 'unidades_medidas'],
          ['accion' => "actDT", 'modulo' => 'unidades_medidas'],
          [
            'accion' => 'alertar',
            'alerta' => [
              'tipo' => 'simple',
              'titulo' => 'Unidades de Medida',
              'texto' => "Se ha actualizado la unidad: {$this->nombreUnidadMedida}",
              'icono' => 'info',
              'notifier' => true,
            ]
          ]
        ],
        'noCommit' => true
      ]);

      $this->commit();
    }
    return $alerta;
  }

  private function eliminarUnidadesMedidasP() {
    $viejo = $this->seleccionarDatos2([
      'campos' => '*',
      'tabla' => 'unidades_medidas',
      'WHERE' => ['id_unidad_medida' => $this->idUnidadMedida]
    ])->fetch(PDO::FETCH_ASSOC);

    $eliminarUsuario = $this->eliminarDatos2([
      'tabla' => "unidades_medidas",
      'WHERE' => [
        "id_unidad_medida" => $this->idUnidadMedida
      ]
    ]);

    $objBitacora = new bitacoraModelo();
    if ($eliminarUsuario == 1) {
      $objBitacora->registrarBitacora([
        'modulo' => 'unidadesMedidas',
        'accion' => 'eliminar',
        'resultado' => 'Éxito',
        'viejo' => $viejo,
        'nuevo' => []
      ]);
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Unidad de medida eliminada",
        "texto" => "La unidad de medida ha sido eliminada con éxito",
        "icono" => "success"
      ];

      $objetoNot = new mensajesWSModelo();
      $objetoNot->enviarMensajesWS([
        "receptor" => [
          'tipo' => 'permisos',
          'permisos' => ['unidadesMedidas' => ['ver']]
        ],
        'cuerpo' => [
          ['accion' => "borrarDataModuloSS", 'modulo' => 'unidades_medidas'],
          ['accion' => "actDT", 'modulo' => 'unidades_medidas'],
          [
            'accion' => 'alertar',
            'alerta' => [
              'tipo' => 'simple',
              'titulo' => 'Unidades de Medida',
              'texto' => "Se ha eliminado la unidad: " . ($viejo['nombre_unidad_medida'] ?? ''),
              'icono' => 'info',
              'notifier' => true,
            ]
          ]
        ],
        'noCommit' => true
      ]);

      $this->commit();
    } else {
      $objBitacora->registrarBitacora([
        'modulo' => 'unidadesMedidas',
        'accion' => 'eliminar',
        'resultado' => 'Fallido',
        'viejo' => ['id_unidad_medida' => $this->idUnidadMedida],
        'nuevo' => []
      ]);
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Unidad de medida no encontrada",
        "texto" => "La unidad de medida no existe en la Base de Datos",
        "icono" => "error"
      ];
    }
    return $alerta;
  }
}
