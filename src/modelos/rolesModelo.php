<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\traitModelo;
use src\modelos\bitacoraModelo;
use src\modelos\accesosModelo;
use src\modelos\mensajesWSModelo;
use PDO;

class rolesModelo extends conexion {
  use traitModelo;

  private int $idRol = 0;
  private string $nombreRol = '';

  public function validarRoles(string $permiso, ?array &$info = null, ?array $requerido = null) {
    $objAcceso = new accesosModelo();
    $v = $objAcceso->validarPermisos('roles', $permiso);
    if ($v) return $v;

    if ($info === null) return false;

    $esquema = [
      'tipo' => 'arrayA',
      'propiedades' => [
        'id_rol' => [
          ...molId,
          "nombreAlerta" => "id del rol",
          "nombreBD" => "id_rol",
          "tablaBD" => "roles",
          "BD" => "seguridad",
          "debeExistirBD" => true
        ],
        'nombre_rol' => [
          ...molNombreObj,
          "nombreAlerta" => "nombre del rol",
          "nombreBD" => "nombre_rol",
          "tablaBD" => "roles",
          "BD" => "seguridad",
          "debeSerUnicoBD" => true
        ],
      ],
      'requerido' => $requerido ?? []
    ];

    $v = $this->limpiarValidar($info, $esquema);
    if ($v) return $v;
    return false;
  }

  public function seleccionarRoles(array $info = []) {
    $requerido = [];
    if (($info['id_rol'] ?? '') != "") $requerido[] = 'id_rol';
    $r = $this->validarRoles('ver', $info, $requerido);
    if (($info['id_rol'] ?? '') != "") $this->idRol = $info['id_rol'];
    if ($r) return $r;
    return $this->seleccionarRolesP();
  }

  public function registrarRoles(array $info) {
    $resultado = $this->validarRoles('registrar', $info, [
      'nombre_rol',
    ]);
    if ($resultado) return $resultado;

    [
      'nombre_rol' => $this->nombreRol
    ] = $info;

    return $this->registrarRolesP();
  }

  public function actualizarRoles(array $info) {
    $resultado = $this->validarRoles('actualizar', $info, [
      'id_rol',
      'nombre_rol',
    ]);
    if ($resultado) return $resultado;

    [
      'id_rol' => $this->idRol,
      'nombre_rol' => $this->nombreRol
    ] = $info;

    return $this->actualizarRolesP();
  }

  public function eliminarRoles(array $info) {
    $resultado = $this->validarRoles('eliminar', $info, [
      'id_rol',
    ]);
    if ($resultado) return $resultado;

    $this->idRol = $info['id_rol'];
    return $this->eliminarRolesP();
  }

  //-- PRIVADOS [ ENCAPSULAMIENTO ]--//
  private function seleccionarRolesP() {
    if ($this->idRol == 0) {
      $resultado = $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'roles',
        'BD' => 'seguridad',
      ]);
      return $resultado->fetchAll(PDO::FETCH_ASSOC);
    } else {
      $resultado = $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'roles',
        'BD' => 'seguridad',
        'WHERE' => [
          "id_rol" => $this->idRol,
        ]
      ]);
      if ($resultado->rowCount() <= 0) {
        return [
          "tipo" => "simple",
          "titulo" => "Rol no encontrado",
          "texto" => "El rol que ha intentado buscar no se encuentra en la base de datos",
          "icono" => "error"
        ];
      }
      return $resultado->fetch(PDO::FETCH_ASSOC);
    }
  }

  private function registrarRolesP() {
    $objBitacora = new bitacoraModelo();
    $ultimoId = $this->guardarDatos2([
      'tabla' => 'roles',
      'BD' => 'seguridad',
      'datos' => [
        "nombre_rol" => $this->nombreRol,
      ]
    ]);

    if ($ultimoId !== false && $ultimoId > 0) {
      $alerta = [
        "tipo" => "limpiarYcerrar",
        "titulo" => "Rol registrado",
        "texto" => "El rol ha sido registrado exitosamente",
        "icono" => "success",
      ];
      $resultadoB = $objBitacora->registrarBitacora([
        'modulo' => 'roles',
        'accion' => 'registrar',
        'resultado' => 'Éxito',
        'viejo' => [],
        'nuevo' => [
          'id_rol' => $ultimoId,
          'nombre_rol' => $this->nombreRol
        ]
      ]);

      $objetoNot = new mensajesWSModelo();
      $objetoNot->enviarMensajesWS([
        "receptor" => [
          'tipo' => 'permisos',
          'permisos' => ['roles' => ['ver']]
        ],
        'cuerpo' => [
          ['accion' => "borrarDataModuloSS", 'modulo' => 'roles'],
          ['accion' => "actDT", 'modulo' => 'roles'],
          [
            'accion' => 'alertar',
            'alerta' => [
              'tipo' => 'simple',
              'titulo' => 'Roles',
              'texto' => "Se ha registrado el rol: {$this->nombreRol}",
              'icono' => 'info',
              'notifier' => true,
            ]
          ]
        ],
        'noCommit' => true
      ]);

      $this->commit();
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Rol no registrado",
        "texto" => "El rol no ha sido registrado exitosamente",
        "icono" => "error",
      ];
      $resultadoB = $objBitacora->registrarBitacora([
        'modulo' => 'roles',
        'accion' => 'registrar',
        'resultado' => 'Fallido',
        'viejo' => [],
        'nuevo' => [
          'nombre_rol' => $this->nombreRol
        ]
      ]);
      $this->rollback();
    }

    if ($resultadoB != false) {
      $this->rollback();
      return $resultadoB;
    }
    return $alerta;
  }

  private function actualizarRolesP() {
    $objBitacora = new bitacoraModelo();
    $viejo = $this->seleccionarDatos2([
      'campos' => '*',
      'tabla' => 'roles',
      'BD' => 'seguridad',
      'WHERE' => ['id_rol' => $this->idRol]
    ])->fetch(PDO::FETCH_ASSOC);

    $resultado = $this->actualizarDatos2([
      'tabla' => 'roles',
      'BD' => 'seguridad',
      'datos' => [
        "nombre_rol" => $this->nombreRol,
      ],
      "WHERE" => [
        "id_rol" => $this->idRol,
      ]
    ]);

    if ($resultado == false || $resultado <= 0) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Sin cambios realizados",
        "texto" => "No se realizó ningún cambio en el rol",
        "icono" => "warning",
      ];
      $resultadoB = $objBitacora->registrarBitacora([
        'modulo' => 'roles',
        'accion' => 'actualizar',
        'resultado' => 'Fallido',
        'viejo' => $viejo,
        'nuevo' => [
          'id_rol' => $this->idRol,
          'nombre_rol' => $this->nombreRol
        ]
      ]);
    } else {
      $alerta = [
        "tipo" => "limpiarYcerrar",
        "titulo" => "Rol actualizado",
        "texto" => "El rol ha sido actualizado exitosamente",
        "icono" => "success",
      ];
      $resultadoB = $objBitacora->registrarBitacora([
        'modulo' => 'roles',
        'accion' => 'actualizar',
        'resultado' => 'Éxito',
        'viejo' => $viejo,
        'nuevo' => [
          'id_rol' => $this->idRol,
          'nombre_rol' => $this->nombreRol
        ]
      ]);

      $objetoNot = new mensajesWSModelo();
      $objetoNot->enviarMensajesWS([
        "receptor" => [
          'tipo' => 'permisos',
          'permisos' => ['roles' => ['ver']]
        ],
        'cuerpo' => [
          ['accion' => "borrarDataModuloSS", 'modulo' => 'roles'],
          ['accion' => "actDT", 'modulo' => 'roles'],
          [
            'accion' => 'alertar',
            'alerta' => [
              'tipo' => 'simple',
              'titulo' => 'Roles',
              'texto' => "Se ha actualizado el rol: {$this->nombreRol}",
              'icono' => 'info',
              'notifier' => true,
            ]
          ]
        ],
        'noCommit' => true
      ]);

      $this->commit();
    }

    if ($resultadoB != false) {
      $this->rollback();
      return $resultadoB;
    }
    return $alerta;
  }

  private function eliminarRolesP() {
    $objBitacora = new bitacoraModelo();
    $viejo = $this->seleccionarDatos2([
      'campos' => '*',
      'tabla' => 'roles',
      'BD' => 'seguridad',
      'WHERE' => ['id_rol' => $this->idRol]
    ])->fetch(PDO::FETCH_ASSOC);

    $eliminarUsuario = $this->eliminarDatos2([
      "tabla" => "roles",
      'BD' => 'seguridad',
      "WHERE" => [
        "id_rol" => $this->idRol
      ]
    ]);

    if ($eliminarUsuario == 1) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Rol eliminado",
        "texto" => "El rol ha sido eliminado con éxito",
        "icono" => "success"
      ];
      $resultadoB = $objBitacora->registrarBitacora([
        'modulo' => 'roles',
        'accion' => 'eliminar',
        'resultado' => 'Éxito',
        'viejo' => $viejo,
        'nuevo' => []
      ]);

      $objetoNot = new mensajesWSModelo();
      $objetoNot->enviarMensajesWS([
        "receptor" => [
          'tipo' => 'permisos',
          'permisos' => ['roles' => ['ver']]
        ],
        'cuerpo' => [
          ['accion' => "borrarDataModuloSS", 'modulo' => 'roles'],
          ['accion' => "actDT", 'modulo' => 'roles'],
          [
            'accion' => 'alertar',
            'alerta' => [
              'tipo' => 'simple',
              'titulo' => 'Roles',
              'texto' => "Se ha eliminado el rol: " . ($viejo['nombre_role'] ?? $viejo['nombre_rol'] ?? ''),
              'icono' => 'info',
              'notifier' => true,
            ]
          ]
        ],
        'noCommit' => true
      ]);

      $this->commit();
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Rol no encontrado",
        "texto" => "El rol no existe en la Base de Datos",
        "icono" => "error"
      ];
      $resultadoB = $objBitacora->registrarBitacora([
        'modulo' => 'roles',
        'accion' => 'eliminar',
        'resultado' => 'Fallido',
        'viejo' => ['id_rol' => $this->idRol],
        'nuevo' => []
      ]);
    }

    if ($resultadoB != false) {
      $this->rollback();
      return $resultadoB;
    }
    return $alerta;
  }
}
