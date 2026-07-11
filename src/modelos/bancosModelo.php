<?php

namespace src\modelos;

use src\modelos\accesosModelo;
use src\modelos\bitacoraModelo;
use src\modelos\traitModelo;
use src\config\connect\conexion;
use src\modelos\mensajesWSModelo;
use PDO;

class bancosModelo extends conexion {
  use traitModelo;

  private int $idBanco = 0;
  private string $nombreBanco = '';

  public function validarBancos(string $permiso, ?array &$info = null, ?array $requerido = null) {
    $objAcceso = new accesosModelo();
    $v = $objAcceso->validarPermisos('bancos', $permiso);
    if ($v) return $v;
    if ($info === null) return false;
    $esquema = [
      'tipo' => 'arrayA',
      'propiedades' => [
        'id_banco' => [
          ...molId,
          "nombreAlerta" => "id del banco",
          "nombreBD" => "id_banco",
          "tablaBD" => "bancos",
          "debeExistirBD" => true
        ],
        'nombre_banco' => [
          ...molNombreObj,
          "nombreAlerta" => "nombre del banco",
          "nombreBD" => "nombre_banco",
          "tablaBD" => "bancos",
          "debeSerUnicoBD" => true
        ],
      ],
      'requerido' => $requerido ?? []
    ];
    $v = $this->limpiarValidar($info, $esquema);
    if ($v) return $v;
    return false;
  }
  public function seleccionarBancos(array $info) {
    $requerido = [];
    if (($info['id_banco'] ?? '') != "") $requerido[] = 'id_banco';
    $r = $this->validarBancos('ver', $info, $requerido);
    if (($info['id_banco'] ?? '') != "") $this->idBanco = $info['id_banco'];
    if ($r) return $r;
    return $this->seleccionarBancosP($info);
  }
  public function registrarBancos(array $info) {
    $resultado = $this->validarBancos('registrar', $info, ['nombre_banco']);
    if ($resultado) return $resultado;

    [
      'nombre_banco' => $this->nombreBanco
    ] = $info;

    return $this->registrarBancosP();
  }
  public function actualizarBancos(array $info) {
    $resultado = $this->validarBancos('actualizar', $info, ['id_banco', 'nombre_banco']);
    if ($resultado) return $resultado;

    [
      'id_banco' => $this->idBanco,
      'nombre_banco' => $this->nombreBanco
    ] = $info;

    return $this->actualizarBancosP();
  }
  public function eliminarBancos(array $info) {
    $resultado = $this->validarBancos('eliminar', $info, ['id_banco']);
    if ($resultado) return $resultado;

    $this->idBanco = $info['id_banco'];
    return $this->eliminarBancosP();
  }

  private function seleccionarBancosP(array $info) {
    if ($this->idBanco != 0) {
      return $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'bancos',
        'WHERE' => [
          'id_banco' => $this->idBanco
        ]
      ])->fetch();
    } else {
      switch (($info['tipoConsulta'] ?? '')) {
        case 'indexadosPorId':
          return $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'bancos',
          ])->fetchAll(PDO::FETCH_UNIQUE);
        default:
          return $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'bancos',
          ])->fetchAll();
      }
    }
  }
  private function registrarBancosP() {
    $ultimoId = $this->guardarDatos2([
      'tabla' => 'bancos',
      'datos' =>  [
        "nombre_banco" => $this->nombreBanco
      ]
    ]);
    $objBitacora = new bitacoraModelo();

    if ($ultimoId <= 0) {
      $this->rollback();
      $objBitacora->registrarBitacora([
        'modulo' => 'bancos',
        'accion' => 'registrar',
        'resultado' => 'Fallido',
        'viejo' => [],
        'nuevo' => [
          'nombre_banco' => $this->nombreBanco
        ]
      ]);
      return [
        "tipo" => "simple",
        "titulo" => "Error",
        "texto" => "No se pudo registrar",
        "icono" => "error"
      ];
    }

    $objBitacora->registrarBitacora([
      'modulo' => 'bancos',
      'accion' => 'registrar',
      'resultado' => 'Éxito',
      'viejo' => [],
      'nuevo' => [
        'id_banco' => $ultimoId,
        'nombre_banco' => $this->nombreBanco
      ]
    ]);

    $objetoNot = new mensajesWSModelo();
    $objetoNot->enviarMensajesWS([
      "receptor" => [
        'tipo' => 'permisos',
        'permisos' => ['bancos' => ['ver']]
      ],
      'cuerpo' => [
        ['accion' => "borrarDataModuloSS", 'modulo' => 'bancos'],
        ['accion' => "actDT", 'modulo' => 'bancos'],
        [
          'accion' => 'alertar',
          'alerta' => [
            'tipo' => 'simple',
            'titulo' => 'Bancos',
            'texto' => "Se ha registrado el banco: {$this->nombreBanco}",
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
      "titulo" => "Registro Exitoso",
      "texto" => "El banco ha sido registrado",
      "icono" => "success"
    ];
  }
  private function actualizarBancosP() {
    $objBitacora = new bitacoraModelo();
    $viejo = $this->seleccionarDatos2([
      'campos' => '*',
      'tabla' => 'bancos',
      'WHERE' => ['id_banco' => $this->idBanco]
    ])->fetch(PDO::FETCH_ASSOC);

    $resultado = $this->actualizarDatos2([
      'tabla' => 'bancos',
      'datos' => [
        "nombre_banco" => $this->nombreBanco
      ],
      'WHERE' => [
        "id_banco" => $this->idBanco
      ]
    ]);

    if ($resultado > 0) {
      $objBitacora->registrarBitacora([
        'modulo' => 'bancos',
        'accion' => 'actualizar',
        'resultado' => 'Éxito',
        'viejo' => $viejo,
        'nuevo' => [
          'id_banco' => $this->idBanco,
          'nombre_banco' => $this->nombreBanco
        ]
      ]);

      $objetoNot = new mensajesWSModelo();
      $objetoNot->enviarMensajesWS([
        "receptor" => [
          'tipo' => 'permisos',
          'permisos' => ['bancos' => ['ver']]
        ],
        'cuerpo' => [
          ['accion' => "borrarDataModuloSS", 'modulo' => 'bancos'],
          ['accion' => "actDT", 'modulo' => 'bancos'],
          [
            'accion' => 'alertar',
            'alerta' => [
              'tipo' => 'simple',
              'titulo' => 'Bancos',
              'texto' => "Se ha actualizado el banco: {$this->nombreBanco}",
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
        "titulo" => "Actualización Exitosa",
        "texto" => "El banco ha sido actualizado",
        "icono" => "success"
      ];
    }

    $objBitacora->registrarBitacora([
      'modulo' => 'bancos',
      'accion' => 'actualizar',
      'resultado' => 'Fallido',
      'viejo' => $viejo,
      'nuevo' => [
        'id_banco' => $this->idBanco,
        'nombre_banco' => $this->nombreBanco
      ]
    ]);
    $this->rollback();
    return [
      "tipo" => "simple",
      "titulo" => "Error",
      "texto" => "No hubo cambios",
      "icono" => "info"
    ];
  }
  private function eliminarBancosP() {
    $objBitacora = new bitacoraModelo();
    $viejo = $this->seleccionarDatos2([
      'campos' => '*',
      'tabla' => 'bancos',
      'WHERE' => ['id_banco' => $this->idBanco]
    ])->fetch(PDO::FETCH_ASSOC);

    $resultado = $this->eliminarDatos2([
      'tabla' => 'bancos',
      'WHERE' => [
        "id_banco" => $this->idBanco
      ]
    ]);

    if ($resultado <= 0) {
      $objBitacora->registrarBitacora([
        'modulo' => 'bancos',
        'accion' => 'eliminar',
        'resultado' => 'Fallido',
        'viejo' => ['id_banco' => $this->idBanco],
        'nuevo' => []
      ]);
      $this->rollback();
      return [
        "tipo" => "simple",
        "titulo" => "Eliminación Fallida",
        "texto" => "El banco no pudo ser eliminado",
        "icono" => "error"
      ];
    }

    $objBitacora->registrarBitacora([
      'modulo' => 'bancos',
      'accion' => 'eliminar',
      'resultado' => 'Éxito',
      'viejo' => $viejo,
      'nuevo' => []
    ]);

    $objetoNot = new mensajesWSModelo();
    $objetoNot->enviarMensajesWS([
      "receptor" => [
        'tipo' => 'permisos',
        'permisos' => ['bancos' => ['ver']]
      ],
      'cuerpo' => [
        ['accion' => "borrarDataModuloSS", 'modulo' => 'bancos'],
        ['accion' => "actDT", 'modulo' => 'bancos'],
        [
          'accion' => 'alertar',
          'alerta' => [
            'tipo' => 'simple',
            'titulo' => 'Bancos',
            'texto' => "Se ha eliminado el banco: " . ($viejo['nombre_banco'] ?? ''),
            'icono' => 'info',
            'notifier' => true,
          ]
        ]
      ],
      'noCommit' => true
    ]);

    $this->commit();
    return [
      "tipo" => "simple",
      "titulo" => "Banco eliminado",
      "texto" => "El banco ha sido eliminado exitosamente",
      "icono" => "success"
    ];
  }
}
