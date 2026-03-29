<?php

namespace src\modelos;

use PDO;
use src\config\connect\conexion;

class mensajesWSModelo extends conexion
{

  private $idNotificacion;
  private $cedulaUsuario;
  private $instruccionesNoti;
  private $instruccionesAccion;

  /*Métodos para tomas datos de las views y asignarlos a los atributos*/
  public function Notificaciones_Sel($id = null)
  {
    $this->idNotificacion = $id;
    $this->cedulaUsuario = $_SESSION['cedula'];

    $campos = [[
      "campo_nombre" => "cedula_usuario",
      "campo_valor" => &$this->cedulaUsuario,
      "formulario_nombre" => "cédula",
      "requerido" => true,
      "minimo" => minRegexCedula,
      "maximo" => maxRegexCedula,
      "expresion_re" => regexCedula,
      "tabla" => "usuarios",
      "debeExistir" => true,
    ]];
    if ($this->idNotificacion != "") {
      $campos[] = [
        "campo_nombre" => "id_notificacion_usuario",
        "campo_valor" => &$this->idNotificacion,
        "formulario_nombre" => "id de la notificación",
        "requerido" => true,
        "minimo" => minRegexId,
        "maximo" => maxRegexId,
        "expresion_re" => regexId,
        "tabla" => "notificaciones_usuarios",
        "debeExistir" => true,
      ];
    }

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
      exit();
    } else {
      return $this->seleccionarNotificaciones();
    }
  }
  public function Notificaciones_Reg($instruccionesNoti)
  {
    if (is_string($instruccionesNoti)) {
      $instruccionesNoti = json_decode($instruccionesNoti, true);
    }

    $this->instruccionesNoti = $instruccionesNoti;
    [
      "tipo" => &$tipo,
      "receptor" => &$receptor,
      "titulo" => &$titulo,
      "texto" => &$texto,
      "icono" => &$icono,
      "tiempo" => &$tiempo,
      "cedula" => &$cedula,
      "rol" => &$rol,
    ] = $this->instruccionesNoti;

    $campos = [
      [
        "campo_valor" => &$receptor,
        "formulario_nombre" => "receptor de la notificación",
        "requerido" => true,
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj,
      ],
      [
        "campo_valor" => &$titulo,
        "formulario_nombre" => "título de la notificación",
        "requerido" => true,
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj,
      ],
      [
        "campo_valor" => &$texto,
        "formulario_nombre" => "texto de la notificación",
        "requerido" => true,
        "minimo" => minRegexDescripcion,
        "maximo" => maxRegexDescripcion,
        "expresion_re" => regexDescripcion,
      ],
      [
        "campo_valor" => &$icono,
        "formulario_nombre" => "icono de la notificación",
        "requerido" => true,
        "minimo" => minRegexDescripcion,
        "maximo" => maxRegexDescripcion,
        "expresion_re" => regexDescripcion
      ],
      [
        "campo_valor" => &$tipo,
        "formulario_nombre" => "tipo de la notificación",
        "requerido" => true,
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj
      ],
      [
        "campo_valor" => &$tiempo,
        "formulario_nombre" => "tiempo de la notificación",
        "requerido" => true,
        "minimo" => minRegexId,
        "maximo" => maxRegexId,
        "expresion_re" => regexId,
      ],
    ];
    if ($cedula) {
      $campos[] = [
        "campo_nombre" => "cedula_usuario",
        "campo_valor" => &$cedula,
        "formulario_nombre" => "cedula",
        "requerido" => true,
        "minimo" => minRegexCedula,
        "maximo" => maxRegexCedula,
        "expresion_re" => regexCedula,
        "tabla" => "usuarios",
        "debeExistir" => true
      ];
    }
    if ($rol) {
      $campos[] = [
        "campo_nombre" => "nombre_rol",
        "campo_valor" => &$rol,
        "formulario_nombre" => "rol de los usuarios",
        "requerido" => true,
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj,
        "tabla" => "roles",
        "debeExistir" => true
      ];
    }

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
      exit();
    } else {
      return $this->registrarNotificaciones();
    }
  }
  public function Notificaciones_Act($cambio)
  {
    $campos = [
      [
        "campo_valor" => $cambio,
        "formulario_nombre" => "cambio de la notificación",
        "requerido" => true,
        "minimo" => minRegexDescripcion,
        "maximo" => maxRegexDescripcion,
        "expresion_re" => regexDescripcion
      ]
    ];

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
      exit();
    } else {
      return $this->actualizarNotificaciones($cambio);
    }
  }
  public function Notificaciones_Eli($id = null)
  {
    if ($id != null) {
      $this->idNotificacion = $id;
      $campos = [
        [
          "campo_nombre" => "id_notificacion_usuario",
          "campo_valor" => &$this->idNotificacion,
          "formulario_nombre" => "id de la notificación",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "notificaciones_usuarios",
          "debeExistir" => true,
        ]
      ];
      $respuesta = $this->limpiar_Verificar($campos);
    } else {
      $respuesta = false;
    }

    if ($respuesta !== false) {
      return $respuesta;
      exit();
    } else {
      return $this->eliminarNotificaciones();
    }
  }
  public function Acciones_Resagadas_Sel()
  {
    $campos = [
      [
        "campo_nombre" => "cedula_usuario",
        "campo_valor" => &$_SESSION['cedula'],
        "formulario_nombre" => "cédula",
        "requerido" => true,
        "minimo" => minRegexCedula,
        "maximo" => maxRegexCedula,
        "expresion_re" => regexCedula,
        "tabla" => "usuarios",
        "debeExistir" => true
      ],
    ];

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
      exit();
    } else {
      return $this->seleccionarAccionesResagadas();
    }
  }
  public function Acciones_Resagadas_Reg($datosAccion)
  {
    $this->instruccionesAccion = $datosAccion;

    [
      'accion' => &$accion,
      'modulo' => &$modulo,
      'receptor' => &$tipoReceptor,
      'cedula' => &$cedulaReceptor,
      'rol' => &$rol
    ] = $this->instruccionesAccion;

    $campos = [
      [
        "campo_valor" => &$accion,
        "formulario_nombre" => "accion resagada",
        "requerido" => true,
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj,
      ],
      [
        "campo_valor" => &$modulo,
        "formulario_nombre" => "modulo de la acción resagada",
        "requerido" => true,
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj,
      ],
      [
        "campo_valor" => &$tipoReceptor,
        "formulario_nombre" => "tipo de receptor resagada",
        "requerido" => true,
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj,
      ],
      [
        "campo_nombre" => "cedula_usuario",
        "campo_valor" => &$cedulaReceptor,
        "formulario_nombre" => "cedula",
        "minimo" => minRegexCedula,
        "maximo" => maxRegexCedula,
        "expresion_re" => regexCedula,
        "tabla" => "usuarios",
        "debeExistir" => true
      ],
      [
        "campo_valor" => &$rol,
        "formulario_nombre" => "rol",
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj,
      ],
    ];
    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
      exit();
    } else {
      return $this->Registrar_Acciones_Resagadas();
    }
  }
  public function Acciones_Eli($AON)
  {
    $this->instruccionesAccion = $AON;
    $accion = $this->instruccionesAccion['accion'];
    $modulo = $this->instruccionesAccion['modulo'] ?? '';

    $campos = [
      [
        "campo_valor" => &$accion,
        "formulario_nombre" => "accion resagada",
        "requerido" => true,
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj,
      ],
      [
        "campo_valor" => &$modulo,
        "formulario_nombre" => "modulo de la acción resagada",
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj,
      ],
    ];
    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
      exit();
    } else {
      return $this->eliminarAcciones();
    }
  }

  private function seleccionarNotificaciones()
  {
    if ($this->idNotificacion != null && $this->idNotificacion != "") {
      $resultado = $this->seleccionarDatos2([
        'campos' => '
                    no.titulo_notificacion, no.texto_notificacion, 
                    nu.fecha_creacion_notificacion, no.tipo_notificacion
                ',
        'tabla' => 'notificaciones as no',
        'datosJoins' => [
          'notificaciones_usuarios as nu' => 'id_notificacion = id_notificacion',
        ],
        'WHERE' => [
          'no.id_notificacion_usuario' =>  $this->idNotificacion,
          'nu.cedula_usuario' =>  $this->cedulaUsuario,
          'nu.status' => ' != ' .  0,
        ],
      ]);

      $notificacion = $resultado->fetch(PDO::FETCH_ASSOC);
      return $notificacion;
    } else {
      $resultado = $this->seleccionarDatos2([
        'campos' => '
                    id_notificacion_usuario, no.titulo_notificacion,
                    no.tipo_notificacion,
                    no.texto_notificacion, nu.fecha_creacion_notificacion, 
                    nu.status
                ',
        'tabla' => 'notificaciones as no',
        'datosJoins' => [
          'notificaciones_usuarios as nu' => 'no.id_notificacion = nu.id_notificacion',
        ],
        'WHERE' => [
          'nu.cedula_usuario' => $this->cedulaUsuario,
          'nu.status' => '!= ' . 0
        ],
        'ORDER' => 'nu.fecha_creacion_notificacion DESC'
      ]);

      if ($resultado->rowCount() <= 0) {
        $alerta = [
          "tipo" => "simple",
          "titulo" => "Sin notificaciones",
          "texto" => "El usuario no posee notificaciones registradas en la Base de Datos",
          "icono" => "info"
        ];
        return $alerta;
        exit();
      } else {
        $notificaciones = $resultado->fetchAll(PDO::FETCH_ASSOC);
      }
      return $notificaciones;
    }
  }
  private function registrarNotificaciones()
  {
    [
      "tipo" => &$tipo,
      "receptor" => &$receptor,
      "titulo" => &$titulo,
      "texto" => &$texto,
      "icono" => &$icono,
      "tiempo" => &$tiempo,
      "cedula" => &$cedula,
      "rol" => &$rol,
    ] = $this->instruccionesNoti;

    //LA NOTIFICACIÓN
    $idNotificacion = $this->VEYSNEC([
      'campos' => 'id_notificacion',
      'tabla' => 'notificaciones',
      'WHERE' => [
        'titulo_notificacion' => $titulo,
        'tipo_notificacion' => $icono,
        'tiempo_notificacion' => $tiempo,
        'icono_notificacion' => $icono,
        'texto_notificacion' => $texto,
      ],
    ]);

    // BÚSQUEDA DE LOS USUARIOS
    $cedulasUsuarios = false;
    switch ($receptor) {
      case 'todosSinExcepcion':
        $instruccionesBD = [
          'campos' => 'cedula_usuario',
          'tabla' => 'usuarios as us',
        ];
        break;
      case 'todos':
        $instruccionesBD = [
          'campos' => 'cedula_usuario',
          'tabla' => 'usuarios as us',
          'WHERE' => [
            'cedula_usuario' => '!= ' . $_SESSION['cedula']
          ],
        ];
        break;
      case 'rol':
        $instruccionesBD = [
          'campos' => 'us.cedula_usuario',
          'tabla' => 'usuarios as us',
          'datosJoins' => [
            'roles as ro' => 'us.id_rol = ro.id_rol',
          ],
          'WHERE' => [
            'ro.nombre_rol' => $rol,
          ],
        ];
        break;
      case 'cedula':
        $cedulasUsuarios = $cedula;
        break;
      default:
        break;
    }

    // PROCESO DE ASOCIAR LA NOTIFICACION A EL O LOS USUARIOS
    if ($cedulasUsuarios == false) {
      $resultado = $this->seleccionarDatos2($instruccionesBD);
      $cedulasUsuarios = $resultado->fetchAll(PDO::FETCH_COLUMN);
    }
    $asociarCedulaANotificacion = function ($cedula, $idNotificacion) {
      $ultimoId = $this->guardarDatos2([
        'tabla' => 'notificaciones_usuarios',
        'datos' => [
          "id_notificacion" => $idNotificacion,
          "cedula_usuario" => $cedula,
          "fecha_creacion_notificacion" => $this->FechaHora_Sel('fecha_hora_BD'),
        ]
      ]);
      if ($ultimoId == false && $ultimoId == 0) {
        $alertaError = [
          "tipo" => "simple",
          "titulo" => "Notificación no registrada",
          "texto" => "La notificación no ha sido registrada al usuario",
          "icono" => "error",
        ];
        return $alertaError;
      } else {
        return false;
      }
    };
    if (is_array($cedulasUsuarios)) {
      foreach ($cedulasUsuarios as $cedula) {
        $resultado = $asociarCedulaANotificacion($cedula, $idNotificacion);
        if ($resultado != false) {
          return $resultado;
        }
      }
    } else {
      $resultado = $asociarCedulaANotificacion($cedulasUsuarios, $idNotificacion);
      if ($resultado != false) {
        return $resultado;
      }
    }

    if (!isset($alertaError)) {
      return false;
    } else {
      return $alertaError;
    }
  }
  private function actualizarNotificaciones($cambio)
  {
    if ($cambio == 'marcarTodasComoLeidas') {
      $resultado = $this->actualizarDatos2([
        "tabla" => "notificaciones_usuarios",
        "datos" => [
          "status" => 2
        ],
        "condiciones" => [
          "cedula_usuario" => $_SESSION['cedula'],
          "status" => '!= ' . 0,
        ]
      ]);
      if ($resultado == false || $resultado <= 0) {
        $alerta = [
          "tipo" => "simple",
          "titulo" => "Error al marcar como leídas",
          "texto" => "No se logró marcar las notificaciones como leídas",
          "icono" => "error",
        ];
      } else {
        $alerta = [
          "tipo" => "simple",
          "titulo" => "Listo",
          "texto" => "Notificaciones correctamente marcadas como leídas",
          "icono" => "success",
        ];
        $this->commit();
      }
      return $alerta;
    }
  }
  private function eliminarNotificaciones()
  {
    $resultado = $this->eliminarDatos2([
      'tabla' => "notificaciones_usuarios",
      'WHERE' => [
        "cedula_usuario" => $_SESSION['cedula']
      ]
    ]);
    if ($resultado->rowCount() > 0) {

      $alerta = [
        "tipo" => "simple",
        "titulo" => "Notificaciones eliminadas",
        "texto" => "Las notificaciones del buzón han sido eliminadas del buzón",
        "icono" => "success"
      ];
      $this->commit();
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Notificaciones no eliminadas",
        "texto" => "No se ha podido eliminar las notificaciones del buzón",
        "icono" => "error"
      ];
    }
    return $alerta;
  }
  private function seleccionarAccionesResagadas()
  {
    $resultado = $this->seleccionarDatos2([
      'tabla' => 'acciones_resagadas_usuarios as aru',
      'campos' => '
                ac.nombre_accion, mo.nombre_modulo
            ',
      'datosJoins' => [
        'acciones as ac' => 'aru.id_accion = ac.id_accion',
        'modulos as mo' => 'aru.id_modulo = mo.id_modulo',
      ],
      'WHERE' => [
        'aru.cedula_usuario' => $_SESSION['cedula']
      ]
    ]);


    $resultado = $resultado->fetchAll(PDO::FETCH_ASSOC);
    return $resultado;
  }
  private function Registrar_Acciones_Resagadas()
  {
    [
      'accion' => $accion,
      'receptor' => $tipoReceptor,
      'cedula' => $cedulaReceptor,
      'rol' => $rol
    ] = $this->instruccionesAccion;
    $modulo = $this->instruccionesAccion['modulo'] ?? 'cualquiera';

    //LA ACCIÓN 
    $idAccion = $this->VEYSNEC([
      'campos' => 'id_accion',
      'tabla' => 'acciones',
      'WHERE' => [
        'nombre_accion' => $accion
      ],
    ]);
    //EL MODULO 
    $idModulo = $this->VEYSNEC([
      'campos' => 'id_modulo',
      'tabla' => 'modulos',
      'WHERE' => [
        'nombre_modulo' => $modulo
      ],
    ]);



    // BÚSQUEDA DE LOS USUARIOS
    $cedulasUsuarios = false;
    switch ($tipoReceptor) {
      case 'todosSinExcepcion':
        $instruccionesBD = [
          'campos' => 'cedula_usuario',
          'tabla' => 'usuarios',
        ];
        break;
      case 'todos':
        $instruccionesBD = [
          'campos' => 'cedula_usuario',
          'tabla' => 'usuarios',
          'WHERE' => [
            'cedula_usuario' => $_SESSION['cedula'],
          ],
        ];
        break;
      case 'rol':
        $instruccionesBD = [
          'campos' => 'us.cedula_usuario',
          'tabla' => 'usuarios as us',
          'datosJoins' => [
            'roles as ro' => 'us.id_rol = ro.id_rol',
          ],
          'WHERE' => [
            'ro.nombre_rol' => $rol,
          ],
        ];
        break;
      case 'cedula':
        $cedulasUsuarios = $cedulaReceptor;
        break;
      default:

        break;
    }

    // PROCESO DE ASOCIAR LA NOTIFICACION A EL O LOS USUARIOS
    if ($cedulasUsuarios == false) {
      $resultado = $this->seleccionarDatos2($instruccionesBD);

      $cedulasUsuarios = $resultado->fetchAll(PDO::FETCH_COLUMN);
    }


    $asociarAccion = function ($cedula, $idModulo, $idAccion) {
      //Registramos la accion solo si ya no hay una vigente
      $idAccionResagada = $this->VEYSNEC([
        'campos' => 'id_accion_resagada',
        'tabla' => 'acciones_resagadas_usuarios',
        'WHERE' => [
          'id_modulo' => $idModulo,
          'id_accion' => $idAccion,
          'cedula_usuario' => $cedula,
        ],
      ]);
      if ($idAccionResagada == false && $idAccionResagada == 0) {
        $alertaError = [
          "tipo" => "simple",
          "titulo" => "Acción no registrada",
          "texto" => "La acción no ha sido registrada al usuario",
          "icono" => "error",
        ];
        return $alertaError;
      } else {
        return false;
      }
    };
    if (is_array($cedulasUsuarios)) {
      foreach ($cedulasUsuarios as $cedula) {
        $resultado = $asociarAccion($cedula, $idModulo, $idAccion);
        if ($resultado != false) {
          return $resultado;
        }
      }
    } else {
      $resultado = $asociarAccion($cedulasUsuarios, $idModulo, $idAccion);
      if ($resultado != false) {
        return $resultado;
      }
    }

    if (!isset($alertaError)) {
      return false;
    } else {
      return $alertaError;
    }
  }
  private function eliminarAcciones()
  {
    $resultado = $this->seleccionarDatos2([
      'tabla' => 'modulos',
      'campos' => 'id_modulo',
      'WHERE' => [
        'nombre_modulo' => $this->instruccionesAccion['modulo'] ?? 'cualquiera'
      ]
    ]);

    $idModulo = $resultado->fetch(PDO::FETCH_COLUMN);

    $resultado = $this->seleccionarDatos2([
      'tabla' => 'acciones',
      'campos' => 'id_accion',
      'WHERE' => [
        'nombre_accion' => $this->instruccionesAccion['accion']
      ]
    ]);

    $idAccion = $resultado->fetch(PDO::FETCH_COLUMN);
    $resultado = $this->eliminarDatos2([
      'tabla' => 'acciones_resagadas_usuarios',
      'fisico' => true,
      'WHERE' => [
        'cedula_usuario' => $_SESSION['cedula'],
        'id_accion' => $idAccion,
        'id_modulo' => $idModulo,
      ]
    ]);
    if ($resultado->rowCount() > 0) {
      $this->commit();
      return 'Éxito';
    } else {
      $this->rollback();
      return 'Ocurrió un error';
    }
  }
  public function enviarMensajesWS($instruccionesMsj)
  {
    #region [REGISTRO DE LA ACCION O LA NOTIFICACIÓN EN LA BD]
    $procesarInstrucciones = function ($instruccion) {
      $registrarBD = function ($cuerpo, $receptor) {
        if ($cuerpo['accion'] == 'alertar') {
          return $this->Notificaciones_Reg([
            'tipo' => $cuerpo['alerta']['tipo'],
            'titulo' => $cuerpo['alerta']['titulo'],
            'texto' => $cuerpo['alerta']['texto'],
            'icono' => $cuerpo['alerta']['icono'],
            'notifier' => $cuerpo['alerta']['notifier'],
            'tiempo' => $cuerpo['alerta']['tiempo'] ?? 0,
            'receptor' => $receptor['tipo'],
            'cedula' => $receptor['cedula'] ?? false,
            'rol' => $receptor['rol'] ?? false
          ]);
        } else {
          return $this->Acciones_Resagadas_Reg([
            'accion' => $cuerpo['accion'],
            'modulo' => $cuerpo['modulo'],
            'receptor' => $receptor['tipo'],
            'cedula' => $receptor['cedula'] ?? false,
            'rol' => $receptor['rol'] ?? false
          ]);
        }
      };
      if (count($instruccion['cuerpo']) > 1) {
        foreach ($instruccion['cuerpo'] as $cuerpoInd) {
          $resultado = $registrarBD($cuerpoInd, $instruccion['receptor']);
          if ($resultado != false) {
            return $resultado;
          }
        }
      } else {
        $resultado = $registrarBD($instruccion['cuerpo'], $instruccion['receptor']);
        if ($resultado != false) {
          return $resultado;
        }
      }
    };
    if (isset($instruccionesMsj['receptor'])) {
      $resultado = $procesarInstrucciones($instruccionesMsj);

      if ($resultado != false) {
        return $resultado;
      };
    } else {
      foreach ($instruccionesMsj as $instruccionInd) {
        $resultado = $procesarInstrucciones($instruccionInd);

        if ($resultado != false) {
          return $resultado;
        };
      }
    }
    #endregion [REGISTRO DE LA ACCION O LA NOTIFICACIÓN EN LA BD]

    #region [DIFUSIÓN POR EL WEBSOCKET]
    $emisor = [
      "emisor" => [
        "cedula" => $_SESSION['cedula'],
        "rol" => $_SESSION['nombreRol']
      ]
    ];
    $estructuraEnvio = [];
    if (isset($instruccionesMsj['receptor'])) {
      $estructuraEnvio[] = $emisor + $instruccionesMsj;
    } else {
      foreach ($instruccionesMsj as $instruccion) {
        $estructuraEnvio[] = $emisor + $instruccion;
      }
    }

    $resultado = $this->hacerPeticionesAPIs([
      "url" =>
      // "http://localhost:1234/api/enviar-mensajes-ws",
      "https://api-the-vina-node.onrender.com/api/enviar-mensajes-ws",

      "metodo" => "POST",
      "datosPe" => $estructuraEnvio,
      "enviarComoJSON" => true
    ]);
    #endregion [DIFUSIÓN POR EL WEBSOCKET]

    if (!isset($resultado['error'])) {
      $this->commit();
      return ['resultado' => $resultado];
    } else {
      $this->rollback();
      return ['error' => $resultado];
    }
  }
}
