<?php

namespace src\modelos;

use PDO;
use src\config\connect\conexion;
use src\modelos\rolesModelo;

class accesosModelo extends conexion {
  private int $idPermiso = 0;
  private int $idRol = 0;
  private int $idModulo = 0;
  private int $cambio = 0;
  private string $moduloVal = '';
  private string $permisoVal = '';

  public function listarPermisos(int $idRol) {
    $this->idRol = $idRol;
    if ($this->idRol != '') {
      $respuesta = $this->limpiar_Verificar([
        [
          "campo_nombre" => "id_rol",
          "campo_valor" => &$this->idRol,
          "formulario_nombre" => "id del rol",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "roles",
          'BD' => 'seguridad',
          "debeExistir" => true
        ]
      ]);
      if ($respuesta !== false) return $respuesta;
    } else {
      $objRoles = new rolesModelo();
      $this->idRol = $objRoles->seleccionarRoles()[0]['id_rol'];
    }
    return $this->listarPermisosP();
  }
  public function seleccionarPermisosPorRol($idRol = null) {
    $this->idRol = $idRol ?? $_SESSION['rol'] ?? 6;

    if ($this->idRol != "") {
      $campos = [
        [
          "campo_nombre" => "id_rol",
          "campo_valor" => &$this->idRol,
          "formulario_nombre" => "id del rol",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "roles",
          'BD' => 'seguridad',
          "debeExistir" => true
        ]
      ];
      $respuesta = $this->limpiar_Verificar($campos);
      if ($respuesta !== false) {
        return $respuesta;
      }
    }
    return $this->seleccionarPermisosPorRolP();
  }
  public function actualizarPermisos(int $rol, int $modulo, int $permiso, int $cambio) {
    $this->idRol = $rol;
    $this->idModulo = $modulo;
    $this->idPermiso = $permiso;
    $this->cambio = $cambio;

    $campos = [
      [
        "campo_nombre" => "id_rol",
        "campo_valor" => &$this->idRol,
        "formulario_nombre" => "id del rol",
        "requerido" => true,
        "minimo" => minRegexId,
        "maximo" => maxRegexId,
        "expresion_re" => regexId,
        "tabla" => "roles",
        'BD' => 'seguridad',
        "debeExistir" => true
      ],
      [
        "campo_nombre" => "id_modulo",
        "campo_valor" => &$this->idModulo,
        "formulario_nombre" => "id del módulo",
        "requerido" => true,
        "minimo" => minRegexId,
        "maximo" => maxRegexId,
        "expresion_re" => regexId,
        "tabla" => "modulos",
        'BD' => 'seguridad',
        "debeExistir" => true
      ],
      [
        "campo_nombre" => "id_permiso",
        "campo_valor" => &$this->idPermiso,
        "formulario_nombre" => "id del permiso",
        "requerido" => true,
        "minimo" => minRegexId,
        "maximo" => maxRegexId,
        "expresion_re" => regexId,
        "tabla" => "permisos",
        'BD' => 'seguridad',
        "debeExistir" => true
      ],
      [
        "campo_valor" => &$this->cambio,
        "formulario_nombre" => "cambio del permiso",
        "requerido" => true,
        "minimo" => minRegexId,
        "maximo" => maxRegexId,
        "expresion_re" => regexId,
      ],
    ];

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) return $respuesta;
    return $this->actualizarPermisosP();
  }
  public function validarPermisos(string $modulo, string $permiso) {
    $this->moduloVal = $modulo;

    switch ($permiso) {
      case 'listar':
      case 'seleccionarUno':
      case 'seleccionarDeuda':
      case 'listarPorRol':
      case 'listarPorCategoria':
      case 'listarDetalles':
      case 'listarDespachadas':
      case 'listarSinPago':
      case 'listarPedidosEnEspera':
      case 'listarPedidosRechazados':
      case 'cerrarSesion':
      case 'listarParaPedido':
      case 'calcularInsumosDelivery':
      case 'seleccionarTasaActual':
      case 'listarPedidosCliente':
      case 'listarNotificaciones':
      case 'listarAccionesResagadas':
      case 'listarPedidosRechazadosDelCliente':
      case 'listarCambios':
      case 'listarEcommerce':
        $this->permisoVal = 'listar';
        break;
      case 'actualizarFoto':
      case 'eliminarFoto':
      case 'cambiarEstado':
      case 'eliminarAccionResagada':
      case 'registrarPago':
      case 'cambiarTemaInterfaz':
      case 'confirmarPedido':
      case 'rechazarPedido':
      case 'marcarTodasNotComoLeidas':
      case 'actualizarValor':
        $this->permisoVal = 'actualizar';
        break;
      case 'registrarToken':
      case 'registrarPedido':
        $this->permisoVal = 'registrar';
        break;
      case 'consultaDashboard':
        $this->moduloVal = 'dashboard';
        $this->permisoVal = 'ver dashboard';
        break;
      case 'reporte_materia_prima':
      case 'reporte_servicios':
      case 'reporte_cierre':
      case 'reporte_compras':
      case 'reporte_ventas':
      case 'reporte_cierre_caja':
        $this->permisoVal = 'imprimir reportes de ventas';
        break;
      case 'reporte_productos':
        $this->permisoVal = 'imprimir reportes de productos';
        break;
      case 'comanda_venta':
        $this->permisoVal = 'imprimir comandas';
        break;
      default:
        $this->permisoVal = $permiso;
        break;
    }
    switch ($modulo) {
      case 'bitacora':
        $this->permisoVal = 'ver bitácora';
        break;
      case 'cambios':
        $this->permisoVal = 'ver historial de cambio';
        break;
      case 'reportes':
        $this->permisoVal = 'ver reportes';
        break;
      case 'mensajesWS':
        $this->moduloVal = 'usuarios';
        $this->permisoVal = 'ver notificaciones';
        break;
      default:

        break;
    }
    $campos = [
      [
        "campo_valor" => &$this->moduloVal,
        "formulario_nombre" => "modulo a validar",
        "requerido" => true,
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj,
      ],
      [
        "campo_valor" => &$this->permisoVal,
        "formulario_nombre" => "permiso a validar",
        "requerido" => true,
        "minimo" => minRegexDescripcion,
        "maximo" => maxRegexDescripcion,
        "expresion_re" => regexDescripcion,
      ],
    ];
    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta) return $respuesta;
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
    $idPermisoVer = $this->VEYSNEC([
      'RSEN' => true,
      'campos' => 'id_permiso',
      'tabla' => 'permisos',
      'BD' => 'seguridad',
      'WHERE' => [
        'nombre_permiso' => 'ver'
      ]
    ]);
    $idPermisoListar = $this->VEYSNEC([
      'RSEN' => true,
      'campos' => 'id_permiso',
      'tabla' => 'permisos',
      'BD' => 'seguridad',
      'WHERE' => [
        'nombre_permiso' => 'listar'
      ]
    ]);
    $idPermisoRegistrar = $this->VEYSNEC([
      'RSEN' => true,
      'campos' => 'id_permiso',
      'tabla' => 'permisos',
      'BD' => 'seguridad',
      'WHERE' => [
        'nombre_permiso' => 'registrar'
      ]
    ]);
    $idPermisoActualizar = $this->VEYSNEC([
      'RSEN' => true,
      'campos' => 'id_permiso',
      'tabla' => 'permisos',
      'BD' => 'seguridad',
      'WHERE' => [
        'nombre_permiso' => 'actualizar'
      ]
    ]);
    $idPermisoEliminar = $this->VEYSNEC([
      'RSEN' => true,
      'campos' => 'id_permiso',
      'tabla' => 'permisos',
      'BD' => 'seguridad',
      'WHERE' => [
        'nombre_permiso' => 'eliminar'
      ]
    ]);

    //Backup automatico de los permisos especiales
    $respaldoPerEsp = [
      'dashboard' => [
        'ver dashboard'
      ],
      'cambios' => [
        'ver historial de cambio',
        'actualizar cambio de divisas'
      ],
      'cambiosIva' => [
        'ver historial de cambio del iva',
        'actualizar cambio del iva'
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
      'inventario' => [
        'ver inventario',
      ],
      'promociones' => ['ver detalles de promociones'],
      'bitacora' => ['ver bitácora'],
      'pedidos' => [
        'ver detalles de pedidos propios',
        'cambiar estado de los pedidos',
        'cancelar pedidos',
        'despachar pedidos',
        'ver pedidos propios',
        'ver pedidos de los clientes',
        'imprimir pedidos',
      ]
    ];
    $permisosEspecialesRol = [];
    foreach ($respaldoPerEsp as $modulo => $permisos) {
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
        if (isset($idPermiso['error'])) {
          return $idModulo;
        }
        $permisosEspecialesRol[] = [
          'id_modulo' => $idModulo,
          'id_permiso' => $idPermiso,
          'nombre_permiso' => $permiso,
          'status' => isset($permisosRolIndexados[$idPermiso]) ? 1 : 0
        ];
      }
    }

    //Obtenemos todos los modulos
    $resultado = $this->seleccionarDatos2([
      'campos' => '*',
      'tabla' => 'modulos',
      'BD' => 'seguridad',
    ]);
    $modulosTotales = $resultado->fetchAll(PDO::FETCH_ASSOC);

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

    $arrayPermisosDef = [
      'generales' => [],
      'especiales' => $permisosEspecialesRol,
    ];
    $modulosFuera = ['reportes', 'cambios', 'bitacora', 'imagenes', 'inventario'];
    foreach ($modulosTotales as $modulo) {
      if (!in_array($modulo['nombre_modulo'], $modulosFuera)) {
        $permisos = [];
        foreach ($permisosGenerales as $permiso) {
          $llave2 = [
            'id_modulo' => $modulo['id_modulo'],
            'id_permiso' => $permiso['id_permiso']
          ];
          ksort($llave2);
          $llave2 = json_encode($llave2);
          $permisos[] = [
            'id' => $permiso['id_permiso'],
            'nombre' => $permiso['nombre_permiso'],
            'activo' => isset($arrayConfirmacion[$llave2]) ? true : false
          ];
        }
        $arrayPermisosDef['generales'][] = [
          'modulo' => [
            'id' => $modulo['id_modulo'],
            'nombre' => $modulo['nombre_modulo']
          ],
          'permisos' => $permisos
        ];
      }
    }
    $this->commit();
    return $arrayPermisosDef;
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
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Acción no autorizada",
        "texto" => "No posee permisos para realizar la acción solicitada",
        "icono" => "error"
      ];
      return $alerta;
    }

    if (!in_array($this->permisoVal, $permisos[$this->moduloVal])) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Acción no autorizada",
        "texto" => "No posee permisos para realizar la acción solicitada",
        "icono" => "error",
        'permisos totales' => $permisos,
        "modulo" => $this->moduloVal,
        "permisos del modulo" => $permisos[$this->moduloVal],
        "permiso recibido" => $this->permisoVal
      ];
      return $alerta;
    }
    return false;
  }
}
