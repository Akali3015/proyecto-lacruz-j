<?php

namespace src\modelos;

use PDO;
use src\config\connect\conexion;
use src\modelos\rolesModelo;

class permisosModelo extends conexion
{
  private $idPermiso;
  private $idRol;
  private $idModulo;
  private $cambio;
  private $moduloVal;
  private $permisoVal;

  public function listarPermisos($idRol)
  {
    $this->idRol = $idRol;
    if ($this->idRol != '') {
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
          "debeExistir" => true
        ]
      ];
      $respuesta = $this->limpiar_Verificar($campos);
      if ($respuesta !== false) {
        return $respuesta;
        exit();
      }
    } else {
      $objRoles = new rolesModelo();
      $this->idRol = $objRoles->seleccionarRoles()[0]['id_rol'];
    }
    return $this->listarPermisosP();
  }
  public function seleccionarPermisosPorRol()
  {
    $this->idRol = $_SESSION['rol'] ?? 6;

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
          "debeExistir" => true
        ]
      ];
      $respuesta = $this->limpiar_Verificar($campos);
      if ($respuesta !== false) {
        return $respuesta;
        exit();
      }
    }
    return $this->seleccionarPermisosPorRolP();
  }
  public function actualizarPermisos($rol, $modulo, $permiso, $cambio)
  {
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
    if ($respuesta !== false) {
      return $respuesta;
      exit();
    }
    return $this->actualizarPermisosP();
  }
  public function validarPermisos($modulo, $permiso)
  {
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
      case 'reporte_productos':
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
      case 'reportes':
        $this->permisoVal = 'ver reportes';
        break;
      case 'cambios':
        $this->permisoVal = 'ver historial de cambio';
        break;
      case 'reportes':
        $this->permisoVal = 'ver reportes';
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
    if ($respuesta !== false) {
      return $respuesta;
      exit();
    }
    return $this->validarPermisosP();
  }

  private function listarPermisosP()
  {
    // Los permisos del rol
    $resultado = $this->seleccionarDatos2([
      'campos' => 'id_permiso, id_modulo',
      'tabla' => 'accesos',
      'WHERE' => [
        "id_rol" => $this->idRol,
      ],
    ]);
    $permisosRol = $resultado->fetchAll(PDO::FETCH_ASSOC);


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
      'ventas' => [
        'ver detalles de las ventas',
        'ver ventas despachadas',
        'ver ventas sin cancelar',
        'ver pedidos en espera',
        'ver pedidos rechazados',
        'gestionar ventas',
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
      'promociones' => ['ver detalles de promociones'],
      'bitacora' => ['ver bitácora'],
    ];
    $permisosEspecialesRol = [];
    foreach ($respaldoPerEsp as $modulo => $permisos) {
      $idModulo = $this->VEYSNEC([
        'RSEN' => true,
        'campos' => 'id_modulo',
        'tabla' => 'modulos',
        'WHERE' => [
          'nombre_modulo' => $modulo
        ]
      ]);
      if (isset($idModulo['error'])) {
        return $idModulo;
      }
      foreach ($permisos as $permiso) {
        $idPermiso = $this->VEYSNEC([
          'RSEN' => true,
          'campos' => 'id_permiso',
          'tabla' => 'permisos',
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
    ]);
    $modulosTotales = $resultado->fetchAll(PDO::FETCH_ASSOC);

    //Todos los permisos generales
    $resultado = $this->seleccionarDatos2([
      'campos' => '*',
      'tabla' => 'permisos',
      'WHERE' => [
        'id_permiso' => [
          '=' => [1, 2, 3, 4, 5],
        ]
      ]
    ]);
    $permisosGenerales = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $arrayPermisosDef = [
      'generales' => [],
      'especiales' => $permisosEspecialesRol,
    ];
    $modulosFuera = ['dashboard', 'reportes', 'cambios', 'bitacora', 'imagenes'];
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
  private function seleccionarPermisosPorRolP()
  {
    //Obtenemos los permisos totales del rol en todos los modulos
    $resultado = $this->seleccionarDatos2([
      'campos' => 'ro.nombre_rol, mo.nombre_modulo, pe.nombre_permiso',
      'tabla' => 'accesos as ac',
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

    if ($resultado->rowCount() == 0) {
      $permisosRol = [];
      $ArrayPermisos = [];
    } else {
      $permisosRol = $resultado->fetchAll(PDO::FETCH_ASSOC);

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
  private function actualizarPermisosP()
  {
    $acceso = $this->VEYSNEC([
      'eliminadosYVigentes' => true,
      'campos' => 'status, id_acceso',
      'tabla' => 'accesos',
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
      "datos" => [
        "status" => $this->cambio
      ],
      "condiciones" => [
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
  private function validarPermisosP()
  {
    $permisos = $this->seleccionarPermisosPorRol();
    if (!isset($permisos[$this->moduloVal])) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Acción no autorizada",
        "texto" => "No posee permisos para realizar la acción solicitada",
        "icono" => "error"
      ];
      return $alerta;
      exit();
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
    } else {
      return false;
    }
  }
}
