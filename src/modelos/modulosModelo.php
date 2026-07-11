<?php

namespace src\modelos;

use src\config\connect\conexion;
use PDO;
use src\modelos\bitacoraModelo;
use src\modelos\accesosModelo;

class modulosModelo extends conexion {
  private string $idModulo = '';
  private string $nombreModulo = '';

  public function validarModulos(string|null $permiso, null|array &$info, $requerido = []) {
    $objAcceso = new accesosModelo();
    $v = $objAcceso->validarPermisos('modulos', $permiso);
    if ($v) return $v;

    $esquemaModulo = [
      'tipo' => 'arrayA',
      'propiedades' => [
        "id_modulo" => [
          ...molId,
          "nombreAlerta" => "id del modulo",
          "nombreBD" => "id_modulo",
          "tablaBD" => "modulos",
          "requerido" => true,
          "BD" => 'seguridad',
          "debeExistir" => true,
          "debeSerUnico" => true
        ],
        "nombre_modulo" => [
          ...molNombreObj,
          "nombreAlerta" => "nombre del modulo",
          "nombreBD" => "nombre_modulo",
          "tablaBD" => "modulos",
          "requerido" => true,
          "BD" => 'seguridad',
          "debeSerUnico" => true
        ],
      ],
      'requerido' => $requerido
    ];

    $v = $this->limpiarValidar($info, $esquemaModulo);

    if ($v) return $v;
    return false;
  }
  public function seleccionarModulos(array $info) {
    $v = $this->validarModulos('listar', $info);
    if ($v) return $v;
    $this->idModulo = $info['id_modulo'] ?? '';

    return $this->seleccionarModulosP();
  }
  public function registrarModulos(array $info) {
    $v = $this->validarModulos('registrar', $info, [
      'nombre_modulo',
    ]);
    if ($v !== false) return $v;

    $this->nombreModulo = $info['nombre_modulo'];

    return $this->registrarModulosP();
  }
  public function actualizarModulos(array $info) {
    $v = $this->validarModulos('actualizar', $info, [
      'id_modulo',
      'nombre_modulo',
    ]);
    if ($v) return $v;

    $this->idModulo = $info['id_modulo'];
    $this->nombreModulo = $info['nombre_modulo'];

    return $this->actualizarModulosP();
  }
  public function eliminarModulos(array $info) {
    $v = $this->validarModulos('eliminar', $info, [
      'id_modulo',
    ]);

    if ($v) return $v;
    $this->idModulo = $info['id_modulo'];
    return $this->eliminarModulosP();
  }

  private function seleccionarModulosP() {
    if ($this->idModulo == null || $this->idModulo == "") {
      $resultado = $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'modulos',
        'BD' => 'seguridad',
      ]);
      return $resultado->fetchAll(PDO::FETCH_ASSOC);
    } else {
      $resultado = $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'modulos',
        'BD' => 'seguridad',
        'WHERE' => [
          "id_modulo" => $this->idModulo,
        ]
      ]);
      if ($resultado->rowCount() <= 0) {
        $alerta = [
          "tipo" => "simple",
          "titulo" => "Módulo no encontrado",
          "texto" => "El módulo que ha intentado buscar no se encuentra en la base de datos",
          "icono" => "error"
        ];
        return $alerta;
        exit();
      } else {
        $modulos = $resultado->fetch(PDO::FETCH_ASSOC);
      }
      return $modulos;
    }
  }
  private function registrarModulosP() {
    $objBitacora = new bitacoraModelo();
    $datoNuevos = [
      'nombre_modulo' => $this->nombreModulo,
    ];


    $ultimoId = $this->guardarDatos2([
      'datos' => $datoNuevos,
      'tabla' => 'modulos',
      'BD' => 'seguridad',
      'WHERE' => [
        "id_modulo" => $this->idModulo,
      ]
    ]);

    $objNot = new mensajesWSModelo();
    $r = $objNot->enviarMensajesWS([
      "receptor" => ['tipo' => 'todos'],
      'cuerpo' => [
        [
          'accion' => "borrarDataModuloSS",
          'modulo' => 'modulos'
        ],
        [
          'accion' => "actDT",
          'modulo' => 'modulos'
        ],
      ],
      'noCommit' => true,
    ]);

    if ($ultimoId !== false && $ultimoId > 0) {
      $alerta = [
        "tipo" => "limpiarYcerrar",
        "titulo" => "Módulo registrado",
        "texto" => "El módulo ha sido registrado exitosamente",
        "icono" => "success",
      ];
      $resultadoB = $objBitacora->registrarBitacora([
        'modulo' => 'modulos',
        'accion' => 'Registrar',
        'resultado' => 'Éxito',
        'commit' => true
      ]);
      //$this->commit();
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Módulo no registrado",
        "texto" => "El módulo no ha sido registrado exitosamente",
        "icono" => "error",
      ];
      $resultadoB = $objBitacora->registrarBitacora([
        'modulo' => 'modulos',
        'accion' => 'Registrar',
        'resultado' => 'Error',
        'commit' => false
      ]);
      // $this->rollback();
    }
    if ($resultadoB != false) {
      $this->rollback();
      return $resultadoB;
    }
    return $alerta;
  }
  private function actualizarModulosP() {
    $objBitacora = new bitacoraModelo();
    $datosActuales = $this->seleccionarModulos(['id_modulo' => $this->idModulo]);
    $datosAct = [
      "nombre_modulo" => $datosActuales['nombre_modulo'],
    ];
    $datoNuevos = [
      'nombre_modulo' => $this->nombreModulo
    ];

    $resultado = $this->actualizarDatos2([
      "datos" => $datoNuevos,
      "tabla" => "modulos",
      'BD' => 'seguridad',
      "WHERE" => [
        "id_modulo" => $this->idModulo,
      ]
    ]);
    if ($resultado == false || $resultado <= 0) {
      $objBitacora->registrarBitacora([
        'modulo' => 'modulos',
        'accion' => 'actualizar módulo con id: ' . $this->idModulo,
        'resultado' => 'Error',
        'commit' => true
      ]);
      return [
        "tipo" => "simple",
        "titulo" => "Sin cambios realizados",
        "texto" => "No se realizó ningún cambio en el módulo",
        "icono" => "warning",
      ];
    }

    $objNot = new mensajesWSModelo();
    $r = $objNot->enviarMensajesWS([
      "receptor" => ['tipo' => 'todos'],
      'cuerpo' => [
        [
          'accion' => "borrarDataModuloSS",
          'modulo' => 'modulos'
        ],
        [
          'accion' => "actDT",
          'modulo' => 'usuarios'
        ],
      ],
      'noCommit' => true,
    ]);
    if (isset($r['error'])) return $r;

    $rb = $objBitacora->registrarBitacora([
      'modulo' => 'modulos',
      'accion' => 'actualizar módulo con id: ' . $this->idModulo,
      'resultado' => 'Éxito',
      'nuevo' => $datoNuevos,
      'viejo' => $datosAct,
    ]);
    if ($rb) return $rb;
    $this->commit();
    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Módulo actualizado",
      "texto" => "El módulo ha sido actualizado exitosamente",
      "icono" => "success",
    ];
  }
  private function eliminarModulosP() {
    $objBitacora = new bitacoraModelo();
    $eliminarUsuario = $this->eliminarDatos2([
      "tabla" => "modulos",
      'BD' => 'seguridad',
      "WHERE" => [
        "id_modulo" => $this->idModulo
      ]
    ]);
    if ($eliminarUsuario == 1) { /*Para verificar si se hizo la eliminación o no */
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Módulo eliminado",
        "texto" => "El módulo ha sido eliminado con éxito",
        "icono" => "success"
      ];
      $resultadoB = $objBitacora->registrarBitacora([
        'modulo' => 'modulos',
        'accion' => 'eliminar módulo con id: ' . $this->idModulo,
        'resultado' => 'Éxito',
        'commit' => true
      ]);
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Módulo no encontrado",
        "texto" => "El módulo no existe en la Base de Datos",
        "icono" => "error"
      ];
      $resultadoB = $objBitacora->registrarBitacora([
        'modulo' => 'modulos',
        'accion' => 'eliminar módulo con id: ' . $this->idModulo,
        'resultado' => 'Error',
        'commit' => false
      ]);
    }

    $objNot = new mensajesWSModelo();
    $r = $objNot->enviarMensajesWS([
      "receptor" => ['tipo' => 'todos'],
      'cuerpo' => [
        [
          'accion' => "borrarDataModuloSS",
          'modulo' => 'usuarios'
        ],
        [
          'accion' => "actDT",
          'modulo' => 'modulos'
        ],
      ],
      'noCommit' => true,
    ]);

    if ($resultadoB != false) {
      $this->rollback();
      return $resultadoB;
    }

    return $alerta;
  }
}
