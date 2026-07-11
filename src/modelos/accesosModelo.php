<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\rolesModelo;

class accesosModelo extends conexion {
  private int $idPermiso = 0;
  private int $idRol = 0;
  private int $idModulo = 0;
  private int $cambio = 0;
  private string $moduloVal = '';
  private string $permisoVal = '';

  public function validarAccesos(string $permiso = '', array &$info = [], array $requerido = []) {
    if ($permiso != '') {
      $v = $this->validarPermisos('accesos', $permiso);
      if ($v) return $v;
    }

    $esquema = [
      'tipo' => 'arrayA',
      'propiedades' => [
        'id_rol' => [
          ...molId,
          "nombreAlerta" => "id del rol",
          "nombreBD" => "id_rol",
          "tablaBD" => "roles",
          'BD' => 'seguridad',
          "debeExistirBD" => true
        ],
        'id_modulo' => [
          ...molId,
          "nombreAlerta" => "id del módulo",
          "nombreBD" => "id_modulo",
          "tablaBD" => "modulos",
          'BD' => 'seguridad',
          "debeExistirBD" => true
        ],
        'id_permiso' => [
          ...molId,
          "nombreAlerta" => "id del permiso",
          "nombreBD" => "id_permiso",
          "tablaBD" => "permisos",
          'BD' => 'seguridad',
          "debeExistirBD" => true
        ],
        'cambio' => [
          ...molId,
          "nombreAlerta" => "cambio del permiso",
        ],
        'nombre_modulo' => [
          ...molNombreObj,
          "formulario_nombre" => "modulo a validar",
        ],
        'nombre_permiso' => [
          ...molDescripcion,
          "formulario_nombre" => "permiso a validar",
        ],
      ],
      'requerido' => $requerido,
    ];
    $v = $this->limpiarValidar($info, $esquema);
    if ($v) return $v;
    return false;
  }
  public function listarPermisos(int $idRol) {
    if ($idRol != '') {
      $info = ['id_rol' => $idRol];
      $v = $this->validarAccesos('listar permisos', $info, ['id_rol']);
      if ($v) return $v;
      $this->idRol = $info['id_rol'];
    } else {
      $objRoles = new rolesModelo();
      $this->idRol = $objRoles->seleccionarRoles()[0]['id_rol'];
    }
    return $this->listarPermisosP();
  }
  public function seleccionarPermisosPorRol($idRol = null) {
    $this->idRol = $idRol ?? $_SESSION['rol'] ?? 6;
    if ($idRol != null) {
      $info = ['id_rol' => $idRol];
      $v = $this->validarAccesos('listar permisos', $info, ['id_rol']);
      if ($v) return $v;
      $this->idRol = $info['id_rol'];
    }
    return $this->seleccionarPermisosPorRolP();
  }
  public function actualizarPermisos(int $rol, int $modulo, int $permiso, int $cambio) {
    $info = [
      'id_rol' => $rol,
      'id_modulo' => $modulo,
      'id_permiso' => $permiso,
      'cambio' => $cambio,
    ];
    $v = $this->validarAccesos('actualizar permisos', $info, [
      'id_rol',
      'id_modulo',
      'id_permiso',
      'cambio'
    ]);
    if ($v) return $v;

    $this->idRol = $info['id_rol'];
    $this->idModulo = $info['id_modulo'];
    $this->idPermiso = $info['id_permiso'];
    $this->cambio = $info['cambio'];
    return $this->actualizarPermisosP();
  }
  public function validarPermisos(string $modulo, string $permiso) {
    $info = [
      'nombre_modulo' => $modulo,
      'nombre_permiso' => $permiso,
    ];
    // $v = $this->validarAccesos('', $info, [
    //   'nombre_modulo',
    //   'nombre_permiso',
    // ]);
    // if ($v) return $v;
    $this->moduloVal = $info['nombre_modulo'];
    $this->permisoVal = $info['nombre_permiso'];
    return $this->validarPermisosP();
  }

  private function listarPermisosP() {
    // Los permisos del rol
    $permisosRol = $this->seleccionarDatos2([
      'campos' => 'id_permiso, id_modulo',
      'tabla' => 'accesos',
      'BD' => 'seguridad',
      'WHERE' => [
        "id_rol" => $this->idRol,
      ],
    ])->fetchAll();

    // INDEXACIÓN PARA LA BUSQUEDA DE PERMISOS SI ESTÁN O NO HABILITADOS PARA EL ROL
    $permisosRolIndexados = [];
    foreach ($permisosRol as $permiso) {
      $permisosRolIndexados[$permiso['id_permiso']] = $permiso;
    }
    $arrayConfirmacion = [];
    foreach ($permisosRol as $permiso) {
      ksort($permiso);
      $llave1 = json_encode($permiso);
      $arrayConfirmacion[$llave1] = true;
    }

    //Backup de los permisos generales
    $mapeoPermisosGenerales = [];
    $idPermisoVer = $this->VEYSNEC([
      'RSEN' => true,
      'campos' => 'id_permiso',
      'tabla' => 'permisos',
      'BD' => 'seguridad',
      'WHERE' => [
        'nombre_permiso' => 'ver'
      ]
    ]);
    $mapeoPermisosGenerales[$idPermisoVer] = 'ver';
    $idPermisoListar = $this->VEYSNEC([
      'RSEN' => true,
      'campos' => 'id_permiso',
      'tabla' => 'permisos',
      'BD' => 'seguridad',
      'WHERE' => [
        'nombre_permiso' => 'listar'
      ]
    ]);
    $mapeoPermisosGenerales[$idPermisoListar] = 'listar';
    $idPermisoRegistrar = $this->VEYSNEC([
      'RSEN' => true,
      'campos' => 'id_permiso',
      'tabla' => 'permisos',
      'BD' => 'seguridad',
      'WHERE' => [
        'nombre_permiso' => 'registrar'
      ]
    ]);
    $mapeoPermisosGenerales[$idPermisoRegistrar] = 'registrar';
    $idPermisoActualizar = $this->VEYSNEC([
      'RSEN' => true,
      'campos' => 'id_permiso',
      'tabla' => 'permisos',
      'BD' => 'seguridad',
      'WHERE' => [
        'nombre_permiso' => 'actualizar'
      ]
    ]);
    $mapeoPermisosGenerales[$idPermisoActualizar] = 'actualizar';
    $idPermisoEliminar = $this->VEYSNEC([
      'RSEN' => true,
      'campos' => 'id_permiso',
      'tabla' => 'permisos',
      'BD' => 'seguridad',
      'WHERE' => [
        'nombre_permiso' => 'eliminar'
      ]
    ]);
    $mapeoPermisosGenerales[$idPermisoEliminar] = 'eliminar';

    //Backup automatico de los permisos especiales
    $respaldoPerEsp = [
      'accesos' => [
        'ver',
        'listar permisos',
        'actualizar permisos',
      ],
      'bitacora' => [
        'ver bitácora'
      ],
      'cambios' => [
        'ver historial de cambio',
        'actualizar cambio de divisas'
      ],
      'cambiosIva' => [
        'ver historial de cambio del iva',
        'actualizar cambio del iva'
      ],
      'dashboard' => [
        'ver dashboard'
      ],
      'inventario' => [
        'ver inventario',
        'registrar cargas o descargas de productos',
        'ver historial de e/s de los productos',
        'imprimir reportes de anomalias de productos',
        'registrar cargas o descargas de materias primas',
        'ver historial de e/s de las materias primas',
        'imprimir reportes de anomalias de materias primas'
      ],
      'monedas' => [
        'ver historial de cambio de las divisas'
      ],
      'ordenesEntregasPresupuestos' => [
        'anular',
        'despachar orden',
        'agregar pago'
      ],
      'pedidos' => [
        'asignar repartidores a pedidos',
        'cambiar estado de los pedidos',
        'cancelar pedidos',
        'despachar pedidos',
        'imprimir pedidos',
        'ver detalles de pedidos propios',
        'ver pedidos de los clientes',
        'ver pedidos propios',
      ],
      'productos' => [
        'ver detalles de los productos',
      ],
      'reportes' => [
        'ver reportes',
        'imprimir reportes de ventas',
        'imprimir reportes de productos',
        'imprimir comandas',
      ],
      'usuarios' => [
        'asignar roles a usuarios',
        'ver el precio del dólar',
        'ver notificaciones',
        'ver modal de ayuda',
        'ver carrito de compra',
      ],
    ];
    $permisosTotales = [];
    foreach ($respaldoPerEsp as $modulo => $permisos) {
      if (!isset($permisosTotales[$modulo])) {
        $idModulo = $this->VEYSNEC([
          'RSEN' => true,
          'campos' => 'id_modulo',
          'tabla' => 'modulos',
          'BD' => 'seguridad',
          'WHERE' => [
            'nombre_modulo' => $modulo
          ]
        ]);
        if (isset($idModulo['error'])) return $idModulo;
        if (!isset($permisosTotales[$modulo])) {
          $permisosTotales[$modulo] = [
            'id_modulo' => $idModulo,
            'pe' => []
          ];
        }
      }
      foreach ($permisos as $permiso) {
        $idPermiso = $this->VEYSNEC([
          'RSEN' => true,
          'campos' => 'id_permiso',
          'tabla' => 'permisos',
          'BD' => 'seguridad',
          'WHERE' => [
            'nombre_permiso' => $permiso
          ]
        ]);
        if (isset($idPermiso['error'])) return $idPermiso;
        $llave = [
          "id_modulo" => $permisosTotales[$modulo]['id_modulo'],
          'id_permiso' => $idPermiso
        ];
        ksort($llave);
        $llave = json_encode($llave);

        $permisosTotales[$modulo]['pe'][] = [
          'id_permiso' => $idPermiso,
          'nombre_permiso' => $permiso,
          'status' => isset($arrayConfirmacion[$llave]) ? 1 : 0
        ];
      }
    }

    //Obtenemos todos los modulos
    $modulosTotales = $this->seleccionarDatos2([
      'campos' => '*',
      'tabla' => 'modulos',
      'BD' => 'seguridad',
    ])->fetchAll();

    //Todos los permisos generales
    $permisosGenerales = $this->seleccionarDatos2([
      'campos' => '*',
      'tabla' => 'permisos',
      'BD' => 'seguridad',
      'WHERE' => [
        'id_permiso' => [
          '=' => [
            $idPermisoVer,
            $idPermisoListar,
            $idPermisoRegistrar,
            $idPermisoActualizar,
            $idPermisoEliminar
          ],
        ]
      ]
    ])->fetchAll();

    $modulosSinPermisosGenerales = [
      'accesos',
      'reportes',
      'cambios',
      'bitacora',
      'imagenes',
      'inventario'
    ];
    foreach ($modulosTotales as $modulo) {
      if (!in_array($modulo['nombre_modulo'], $modulosSinPermisosGenerales)) {
        foreach ($permisosGenerales as $permiso) {
          $llave2 = [
            'id_modulo' => $modulo['id_modulo'],
            'id_permiso' => $permiso['id_permiso']
          ];
          ksort($llave2);
          $llave2 = json_encode($llave2);
          if (!isset($permisosTotales[$modulo['nombre_modulo']]['pg'])) {
            $permisosTotales[$modulo['nombre_modulo']]['pg'] = [];
          }
          if (!isset($permisosTotales[$modulo['nombre_modulo']]['id_modulo'])) {
            $permisosTotales[$modulo['nombre_modulo']]['id_modulo'] = $modulo['id_modulo'];
          }
          $permisosTotales[$modulo['nombre_modulo']]['pg'][$permiso['id_permiso']] = isset($arrayConfirmacion[$llave2]) ? 1 : 0;
        }
      }
    }
    $this->commit();
    return [
      'permisos' => $permisosTotales,
      'mapeoPG' => $mapeoPermisosGenerales,
    ];
  }
  private function seleccionarPermisosPorRolP() {
    //Obtenemos los permisos totales del rol en todos los modulos
    $resultado = $this->seleccionarDatos2([
      'campos' => 'ro.nombre_rol, mo.nombre_modulo, pe.nombre_permiso',
      'tabla' => 'accesos as ac',
      'BD' => 'seguridad',
      'datosJoins' => [
        'roles as ro' => 'ac.id_rol = ro.id_rol',
        'permisos as pe' => 'ac.id_permiso = pe.id_permiso',
        'modulos as mo' => 'ac.id_modulo = mo.id_modulo',
      ],
      'WHERE' => [
        'ro.id_rol' => $this->idRol
      ],
      'ORDER' => 'mo.nombre_modulo asc'
    ]);

    $ArrayPermisos = [];
    if ($resultado->rowCount() > 1) {
      $permisosRol = $resultado->fetchAll();

      //Construimos la estructura sintetizada
      $ArrayPermisos = [];
      $nombreModulo = '';
      foreach ($permisosRol as $permiso) {
        if ($permiso['nombre_modulo'] != $nombreModulo) {
          $ArrayPermisos[$permiso['nombre_modulo']] = [$permiso['nombre_permiso']];
          $nombreModulo = $permiso['nombre_modulo'];
        } else {
          $ArrayPermisos[$permiso['nombre_modulo']][] = $permiso['nombre_permiso'];
        }
      }
    }
    return $ArrayPermisos;
  }
  private function actualizarPermisosP() {
    $acceso = $this->VEYSNEC([
      'eliminadosYVigentes' => true,
      'campos' => 'status, id_acceso',
      'tabla' => 'accesos',
      'BD' => 'seguridad',
      'WHERE' => [
        "id_rol" => $this->idRol,
        "id_modulo" => $this->idModulo,
        "id_permiso" => $this->idPermiso,
      ],
    ]);
    $estado = $acceso['status'];
    $idAcceso = $acceso['id_acceso'];
    $resultado = $this->actualizarDatos2([
      "tabla" => "accesos",
      'BD' => 'seguridad',
      "datos" => [
        "status" => $this->cambio
      ],
      "WHERE" => [
        "id_acceso" => $idAcceso,
      ]
    ]);

    if (($estado == $this->cambio) || ($resultado != false && $resultado >= 1)) {
      $this->commit();
      return [
        'tipo' => 'simple',
        'titulo' => 'Acceso actualizado',
        'texto' => 'El permiso ha sido actualizado',
        'icono' => 'success',
      ];
    } else {
      $this->rollback();
      return [
        'tipo' => 'simple',
        'titulo' => 'Acceso no actualizado',
        'texto' => 'El permiso no ha sido actualizado',
        'icono' => 'error',
      ];
    }
  }
  private function validarPermisosP() {
    $permisos = $this->seleccionarPermisosPorRol();
    if (!isset($permisos[$this->moduloVal])) {
      return [
        "tipo" => "simple",
        "titulo" => "Acción no autorizada",
        "texto" => "No posee permisos para realizar la acción solicitada",
        "icono" => "error"
      ];
    }

    if (!in_array($this->permisoVal, $permisos[$this->moduloVal])) {
      return [
        "tipo" => "simple",
        "titulo" => "Acción no autorizada",
        "texto" => "No posee permisos para realizar la acción solicitada",
        "icono" => "error",
      ];
    }
    return false;
  }
}
