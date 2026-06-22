<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\clientesModelo;

class usuariosModelo extends conexion {
  private string $cedulaUsuario = '';
  private int $rolUsuario = 0;
  private string $nombreUsuario = '';
  private string $apellidoUsuario = '';
  private string $usuarioUsuario = '';
  private string $contrasena1Usuario = '';
  private string $contrasena2Usuario = '';
  private string $telefonoUsuario = '';
  private string $correoUsuario = '';
  private string $direccionUsuario = '';
  private array $fotoUsuario = [];

  public function validarUsuarios(array $instruccionesVal) {
    [
      'infoVal' => &$infoVal,
      'camposVal' => &$camposVal,
    ] = $instruccionesVal;

    $arrayValidaciones = [
      "apellido_usuario" => [
        "formulario_nombre" => "apellido",
        "requerido" => true,
        "minimo" => minRegexNombrePer,
        "maximo" => maxRegexNombrePer,
        "expresion_re" => regexNombrePer,
      ],
      "cedula_usuario" => [
        "campo_nombre" => "cedula_usuario",
        "formulario_nombre" => "cédula",
        "requerido" => true,
        "minimo" => minRegexCedulaRifLetra,
        "maximo" => maxRegexCedulaRifLetra,
        "expresion_re" => regexCedulaRifLetra,
        "tabla" => "usuarios",
        "BD" => 'seguridad',
        "debeSerUnico" => true,
      ],
      "contrasena_usuario" => [
        "formulario_nombre" => "contraseña",
        "minimo" => minRegexContrasena,
        "maximo" => maxRegexContrasena,
        "expresion_re" => regexContrasena,
      ],
      "correo_usuario" => [
        "campo_nombre" => "correo_usuario",
        "formulario_nombre" => "correo",
        "requerido" => true,
        "minimo" => minRegexCorreo,
        "maximo" => maxRegexCorreo,
        "expresion_re" => regexCorreo,
        "tabla" => "usuarios",
        "BD" => 'seguridad',
        "debeSerUnico" => true
      ],
      "direccion_usuario" => [
        "formulario_nombre" => "dirección",
        "requerido" => true,
        "minimo" => minRegexDescripcion,
        "maximo" => maxRegexDescripcion,
        "expresion_re" => regexDescripcion,
      ],
      "id_rol" => [
        "campo_nombre" => "id_rol",
        "formulario_nombre" => "rol",
        "minimo" => minRegexId,
        "maximo" => maxRegexId,
        "expresion_re" => regexId,
        "tabla" => "roles",
        "BD" => 'seguridad',
        "debeExistir" => true
      ],
      "nombre_usuario" => [
        "formulario_nombre" => "nombre",
        "requerido" => true,
        "minimo" => minRegexNombrePer,
        "maximo" => maxRegexNombrePer,
        "expresion_re" => regexNombrePer,
      ],
      "telefono_usuario" => [
        "campo_nombre" => 'telefono_usuario',
        "formulario_nombre" => "teléfono",
        "requerido" => true,
        "minimo" => minRegexTelefono,
        "maximo" => maxRegexTelefono,
        "expresion_re" => regexTelefono,
        "tabla" => "usuarios",
        "BD" => 'seguridad',
        "debeSerUnico" => true,
      ],
      "usuario_usuario" => [
        "campo_nombre" => "usuario_usuario",
        "formulario_nombre" => "nombre de usuario",
        "requerido" => true,
        "minimo" => minRegexUsuario,
        "maximo" => maxRegexUsuario,
        "expresion_re" => regexUsuario,
        "tabla" => "usuarios",
        "BD" => 'seguridad',
        "debeSerUnico" => true,
      ],
    ];
    $totalValidaciones = [];
    foreach ($camposVal as $valorForm  => $campoVal) {
      if (is_numeric($valorForm)) $valorForm = $campoVal;
      $validacion = [];
      switch ($campoVal) {
        case 'contrasena1_usuario':
        case 'contrasena2_usuario':
          $validacion = $arrayValidaciones['contrasena_usuario'];
          $validacion['campo_valor'] = &$infoVal[$valorForm];
          if ($campoVal == 'contrasena1_usuario') $validacion['camposIguales'] = &$infoVal['contrasena2_usuario'];
          break;
        case 'cedula_usuario':
        case 'cedula_usuario_act':
        case 'cedula_usuario_eli':
          $validacion = $arrayValidaciones['cedula_usuario'];

          if ($campoVal === 'cedula_usuario') {
            if (($infoVal['codigo_cedula_usuario'] ?? '') == '') {
              return [
                'tipo' => 'simple',
                'titulo' => 'Prefijo de cédula vacío',
                'texto' => 'No puede enviar el formulario sin seleccionar el prefijo de la cédula del usuario',
                'icono' => 'error'
              ];
            }
            if (($infoVal['cedula_usuario'] ?? '') == '') {
              return [
                'tipo' => 'simple',
                'titulo' => 'Cédula vacía',
                'texto' => 'No puede enviar el formulario sin escribir la cédula del usuario',
                'icono' => 'error'
              ];
            }
            $infoVal['cedula_usuario'] = $infoVal['codigo_cedula_usuario'] . $infoVal['cedula_usuario'];
          }

          $validacion['campo_valor'] = &$infoVal['cedula_usuario'];
          if ($campoVal == 'cedula_usuario_act' || $campoVal == 'cedula_usuario_eli') $validacion['debeExistir'] = true;
          if ($campoVal == 'cedula_usuario_eli') {
            $cedulasSuperUsuarios = $this->seleccionarDatos2([
              'campos' => 'cedula_usuario',
              'tabla' => 'usuarios',
              'BD' => 'seguridad',
              'WHERE' => [
                'nombre_rol' => 'SUPER USUARIO'
              ]
            ])->fetch();
            $validacion['camposDiferentes'] = $cedulasSuperUsuarios;
          }
          break;
        case 'usuario_usuario_iniS':
          $validacion = $arrayValidaciones['usuario_usuario'];
          $validacion['campo_valor'] = &$infoVal[$valorForm];
          unset($validacion['debeSerUnico']);
          break;
        case 'telefono_usuario':
          if (isset($infoVal['prefijo_telefono_usuario'])) {
            $infoVal['telefono_usuario'] = $infoVal['prefijo_telefono_usuario'] . $infoVal['telefono_usuario'];
          }
          if (($infoVal['telefono_usuario'] ?? '') == '') {
            return [
              'tipo' => 'simple',
              'titulo' => 'Telefono vacío',
              'texto' => 'No puede enviar el formulario sin escribir el telefono',
              'icono' => 'error'
            ];
          }
          $validacion = $arrayValidaciones[$campoVal];
          $validacion['campo_valor'] = &$infoVal[$valorForm];
          break;
        default:
          $validacion = $arrayValidaciones[$campoVal];
          $validacion['campo_valor'] = &$infoVal[$valorForm];
          break;
      }
      $totalValidaciones[] = $validacion;
    }
    return $this->limpiar_Verificar($totalValidaciones);
  }
  public function seleccionarUsuarios(array $info) {
    if (($info['cedula_usuario'] ?? '') != '') {
      $resultado = $this->validarUsuarios([
        'infoVal' => &$info,
        'camposVal' => [
          'cedula_usuario' => 'cedula_usuario_act',
        ],
      ]);
      if ($resultado) return $resultado;
      $this->cedulaUsuario = $info['cedula_usuario'];
    }
    return $this->seleccionarUsuariosP();
  }
  public function registrarUsuarios(array $info) {
    $resultado = $this->validarUsuarios([
      'infoVal' => &$info,
      'camposVal' => [
        'cedula_usuario',
        'id_rol',
        'nombre_usuario',
        'apellido_usuario',
        'usuario_usuario',
        'contrasena1_usuario',
        'contrasena2_usuario',
        'telefono_usuario',
        'correo_usuario',
        'direccion_usuario',
      ],
    ]);
    if ($resultado) return $resultado;
    $this->cedulaUsuario = $info['cedula_usuario'];
    $this->rolUsuario = $info['id_rol'];
    $this->nombreUsuario = $info['nombre_usuario'];
    $this->apellidoUsuario = $info['apellido_usuario'];
    $this->usuarioUsuario = $info['usuario_usuario'];
    $this->contrasena1Usuario = $info['contrasena1_usuario'];
    $this->contrasena2Usuario = $info['contrasena2_usuario'];
    $this->telefonoUsuario = $info['telefono_usuario'];
    $this->correoUsuario = $info['correo_usuario'];
    $this->fotoUsuario = $info['foto_usuario'] ?? [];
    $this->direccionUsuario = $info['direccion_usuario'] ?? [];

    if ($this->contrasena1Usuario != $this->contrasena2Usuario) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Contraseñas diferentes',
        'texto' => 'Ambas contraseñas deben ser iguales',
        'icono' => 'warning',
      ];
    }
    $this->contrasena1Usuario = password_hash($this->contrasena1Usuario, PASSWORD_BCRYPT, ["cost" => 10]);

    return $this->registrarUsuariosP();
  }
  public function actualizarUsuarios(array $info) {
    $campos = [
      'cedula_usuario' => 'cedula_usuario_act',
      'id_rol',
      'nombre_usuario',
      'apellido_usuario',
      'usuario_usuario',
      'telefono_usuario',
      'correo_usuario',
      'direccion_usuario',
    ];
    if (($info['contrasena1_usuario'] ?? '') != '') {
      array_push($campos, 'contrasena1_usuario', 'contrasena2_usuario');
    }
    $resultado = $this->validarUsuarios([
      'infoVal' => &$info,
      'camposVal' => $campos,
    ]);

    if ($resultado) return $resultado;
    $this->cedulaUsuario = $info['cedula_usuario'];
    $this->rolUsuario = $info['id_rol'];
    $this->nombreUsuario = $info['nombre_usuario'];
    $this->apellidoUsuario = $info['apellido_usuario'];
    $this->usuarioUsuario = $info['usuario_usuario'];
    $this->contrasena1Usuario = $info['contrasena1_usuario'];
    $this->contrasena2Usuario = $info['contrasena2_usuario'];
    $this->telefonoUsuario = $info['telefono_usuario'];
    $this->correoUsuario = $info['correo_usuario'];
    $this->direccionUsuario = $info['direccion_usuario'];

    //Usamos este metodo para procesar e incriptar la contraseña
    if ($this->contrasena1Usuario != '') {
      $this->contrasena1Usuario = password_hash($this->contrasena1Usuario, PASSWORD_BCRYPT, ["cost" => 10]);
    }
    return $this->actualizarUsuariosP();
  }
  public function eliminarUsuarios(array $info) {
    $resultado = $this->validarUsuarios([
      'infoVal' => &$info,
      'camposVal' => [
        'cedula_usuario' => 'cedula_usuario_act',
      ],
    ]);
    if ($resultado) return $resultado;
    $this->cedulaUsuario = $info['cedula_usuario'];
    return $this->eliminarUsuariosP();
  }
  public function actualizarFotosUsuarios(array $info) {
    $resultado = $this->validarUsuarios([
      'infoVal' => &$info,
      'camposVal' => [
        'cedula_usuario' => 'cedula_usuario_act',
      ],
    ]);
    if ($resultado) return $resultado;
    $this->cedulaUsuario = $info['cedula_usuario'];
    $this->fotoUsuario = $info['foto_usuario'];

    return $this->actualizarFotosUsuariosP();
  }
  public function eliminarFotosUsuarios(array $info) {
    $resultado = $this->validarUsuarios([
      'infoVal' => &$info,
      'camposVal' => [
        'cedula_usuario' => 'cedula_usuario_act',
      ],
    ]);
    if ($resultado) return $resultado;
    $this->cedulaUsuario = $info['cedula_usuario'];
    return $this->eliminarFotosUsuariosP();
  }
  public function iniciarSesionUsuarios(array $info) {

    // Enviar verificacion interna al servidor de Google
    // if (empty($info['g-recaptcha-response'])) {
    //   return [
    //     "tipo" => "simple",
    //     "titulo" => "Seguridad",
    //     "texto" => "Falta completar la validación del puzle de seguridad.",
    //     "icono" => "error"
    //   ];
    // }
    // $resultadoCaptcha = $this->hacerPeticionesAPIs([
    //   'url' => 'https://www.google.com/recaptcha/api/siteverify',
    //   'datosPe' => [
    //     'secret'   => '6LdSVPgsAAAAAFAQ6_8Z-y0Q_0s-Xy3XVLGtydmw',
    //     'response' => $info['g-recaptcha-response'],
    //     'remoteip' => $_SERVER['REMOTE_ADDR']
    //   ]
    // ]);
    // if (($resultadoCaptcha['success'] ?? false) != true) {
    //   return [
    //     "tipo" => "simple",
    //     "titulo" => "Fallo de Seguridad",
    //     "texto" => "La verificación del captcha falló. Inténtelo nuevamente.",
    //     "icono" => "error"
    //   ];
    // }

    $resultado = $this->validarUsuarios([
      'infoVal' => &$info,
      'camposVal' => [
        'usuario_usuario' => 'usuario_usuario_iniS',
        'contrasena1_usuario',
      ],
    ]);
    if ($resultado) return $resultado;
    $this->usuarioUsuario = $info['usuario_usuario'];
    $this->contrasena1Usuario = $info['contrasena1_usuario'];

    return $this->iniciarSesionUsuariosP();
  }
  public function cerrarSesionUsuarios() {
    return $this->cerrarSesionUsuariosP();
  }

  //-- PRIVADOS [ ENCAPSULAMIENTO ] --//
  private function seleccionarUsuariosP() {
    if ($this->cedulaUsuario == null || $this->cedulaUsuario == "") {
      return  $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'v_usuarios_todos',
        "BD" => 'seguridad',
        'WHERE' => [
          "cedula_usuario" => [
            "!=" => [30485684, $_SESSION['cedula']]
          ]
        ],
      ])->fetchAll();
    } else {
      return $this->seleccionarDatos2([
        'campos' => '
          cedula_usuario, nombre_usuario,
          apellido_usuario, telefono_usuario, correo_usuario,
          usuario_usuario, id_rol,contrasena_usuario,direccion_usuario
        ',
        'tabla' => 'usuarios',
        'BD' => 'seguridad',
        'WHERE' => [
          "cedula_usuario" => $this->cedulaUsuario,
        ]
      ])->fetch();
    }
  }
  private function registrarUsuariosP() {
    $fotoUsuario = '';
    $error = function () use ($fotoUsuario) {
      $this->rollback();
      if ($fotoUsuario != '') $this->Imagenes_Eli2('usuarios', $fotoUsuario);
    };
    if ($this->fotoUsuario != []) {
      $fotoUsuario = $this->Imagenes_Reg('usuarios', $this->fotoUsuario, 'usuarios');
    }
    $datoNuevos = [
      "cedula_usuario" => $this->cedulaUsuario,
      "nombre_usuario" => $this->nombreUsuario,
      "apellido_usuario" => $this->apellidoUsuario,
      "correo_usuario" => $this->correoUsuario,
      "telefono_usuario" => $this->telefonoUsuario,
      "id_rol" => $this->rolUsuario,
      "usuario_usuario" => $this->usuarioUsuario,
      "contrasena_usuario" => $this->contrasena1Usuario,
      "foto_usuario" => $fotoUsuario,
      "direccion_usuario" => $this->direccionUsuario,
    ];
    $resultado = $this->guardarDatos2([
      'datos' => $datoNuevos,
      'tabla' => 'usuarios',
      'BD' => 'seguridad',
      'WHERE' => [
        "cedula_usuario" => $this->cedulaUsuario
      ]
    ]);
    if ($resultado <= 0) {
      $error();
      return [
        "tipo" => "simple",
        "titulo" => "Usuario no registrado",
        "texto" => "El usuario no ha sido registrado exitosamente",
        "icono" => "error",
      ];
    }

    $clienteObj = new clientesModelo();
    $cliente = $clienteObj->seleccionarClientes(['rif_cedula_cliente' => $this->cedulaUsuario]);

    if (!isset($cliente['rif_cedula_cliente'])) {
      $resultado = $clienteObj->registrarClientes([
        "rif_cedula_cliente" => $this->cedulaUsuario,
        "razon_social_cliente" => $this->nombreUsuario . ' ' . $this->apellidoUsuario,
        "telefono_cliente" => $this->telefonoUsuario,
        "correo_cliente" => $this->correoUsuario,
        "direccion_cliente" => $this->direccionUsuario,
        "sinCommit" => true,
      ]);
      if (($resultado['icono'] ?? '') != 'success') return $resultado;
    }

    $diferencias = $this->sacarDiferenciaBitacora($datoNuevos, [], 'usuarios');
    if ($diferencias['icono'] ?? '' == 'error') return $diferencias;
    $objBitacora = new bitacoraModelo();
    $rb = $objBitacora->registrarBitacora('usuarios', 'registrar', 'éxito');
    if ($rb) return $rb;
    $this->commit();

    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Usuario registrado",
      "texto" => "El usuario ha sido registrado exitosamente",
      "icono" => "success"
    ];
  }
  private function actualizarUsuariosP() {
    $datosActuales = $this->seleccionarUsuarios([
      "cedula_usuario" => $this->cedulaUsuario,
    ]);
    if ($this->contrasena1Usuario == "") $this->contrasena1Usuario = $datosActuales['contrasena_usuario'];
    if ($this->rolUsuario == '') $this->rolUsuario = $datosActuales['id_rol'];

    $datosAct = [
      "nombre_usuario" => $this->nombreUsuario,
      "apellido_usuario" => $this->apellidoUsuario,
      "telefono_usuario" => $this->telefonoUsuario,
      "id_rol" => $this->rolUsuario,
      "usuario_usuario" => $this->usuarioUsuario,
      "contrasena_usuario" => $this->contrasena1Usuario,
      "direccion_usuario" => $this->direccionUsuario,
      "correo_usuario" => $this->correoUsuario,
    ];
    $resultado = $this->actualizarDatos2([
      "datos" => $datosAct,
      "tabla" => "usuarios",
      'BD' => 'seguridad',
      "WHERE" => [
        "cedula_usuario" => $this->cedulaUsuario,
      ]
    ]);
    if ($resultado == false || $resultado <= 0) {
      return [
        "tipo" => "simple",
        "titulo" => "Sin cambios realizados",
        "texto" => "No se realizó ningún cambio en el usuario",
        "icono" => "warning",
      ];
    }

    $clienteObj = new clientesModelo();
    $cliente = $clienteObj->seleccionarClientes(['rif_cedula_cliente' => $this->cedulaUsuario]);
    if (isset($cliente['rif_cedula_cliente'])) {
      $resultado = $clienteObj->actualizarClientes([
        "rif_cedula_cliente" => $this->cedulaUsuario,
        "razon_social_cliente" => $this->nombreUsuario . ' ' . $this->apellidoUsuario,
        "telefono_cliente" => $this->telefonoUsuario,
        "correo_cliente" => $this->correoUsuario,
        "direccion_cliente" => $this->direccionUsuario,
        "sinCommit" => true,
      ]);
      if (($resultado['icono'] ?? '') != 'success') return $resultado;
    }

    if ($this->cedulaUsuario == $_SESSION['cedula']) {
      $_SESSION['nombre'] = $this->nombreUsuario;
      $_SESSION['apellido'] = $this->apellidoUsuario;
      $_SESSION['telefono'] = $this->telefonoUsuario;
      $_SESSION['usuario'] = $this->usuarioUsuario;
      $_SESSION['rol'] = $this->rolUsuario;
    }

    $datosAct['cedula_usuario'] = $this->cedulaUsuario;
    $diferencias = $this->sacarDiferenciaBitacora($datosAct, $datosActuales, 'usuarios');
    if ($diferencias['icono'] ?? '' == 'error') return $diferencias;
    $objBitacora = new bitacoraModelo();
    $rb = $objBitacora->registrarBitacora('usuarios', 'actualizar', 'éxito');
    if ($rb) return $rb;

    $this->commit();
    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Usuario actualizado",
      "texto" => "El usuario ha sido actualizado exitosamente",
      "icono" => "success",
    ];
  }
  private function eliminarUsuariosP() {
    $eliminarUsuario = $this->eliminarDatos2([
      'tabla' => "usuarios",
      'BD' => 'seguridad',
      'WHERE' => [
        "cedula_usuario" => $this->cedulaUsuario
      ]
    ]);
    $objBitacora = new bitacoraModelo();

    if ($eliminarUsuario <= 0) {
      $rb = $objBitacora->registrarBitacora('usuarios', 'eliminar', 'éxito', true);
      if ($rb) return $rb;
      return [
        "tipo" => "simple",
        "titulo" => "Usuario no encontrado",
        "texto" => "El usuario no existe en la Base de Datos",
        "icono" => "error"
      ];
    }
    $rb = $objBitacora->registrarBitacora('usuarios', 'eliminar', 'éxito');
    if ($rb) return $rb;
    $this->commit();
    return [
      "tipo" => "simple",
      "titulo" => "Usuario eliminado",
      "texto" => "El usuario ha sido eliminado con éxito",
      "icono" => "success"
    ];
  }
  private function actualizarFotosUsuariosP() {

    $diferencias = null;
    $usuarioAct = $this->seleccionarUsuarios(['cedula_usuario' => $this->cedulaUsuario]);
    if ($usuarioAct['foto_usuario'] != $this->fotoUsuario) {
      $diferencias = ['foto_usuario' => ['modificado' => $this->fotoUsuario]];
    }
    $objBitacora = new bitacoraModelo();
    $objBitacora->registrarBitacora('usuarios', 'actualizar', 'éxito', $diferencias);

    return $this->Imagenes_Act([
      'subCarpeta' => 'usuarios',
      'imagen' => $this->fotoUsuario,
      'tablaBD' => 'usuarios',
      'nombreCampoFoto' => 'foto_usuario',
      'nombreCampoId' => 'cedula_usuario',
      'valorId' => $this->cedulaUsuario,
      'BD' => 'seguridad',
    ]);
  }
  private function eliminarFotosUsuariosP() {
    $diferencias = ['foto_usuario' => ['eliminado' => true]];
    $objBitacora = new bitacoraModelo();
    $rb = $objBitacora->registrarBitacora('usuarios', 'actualizar', 'éxito');
    if (($rb['icono'] ?? '') == 'error') return $rb;

    $this->Imagenes_Eli([
      'subCarpeta' => 'usuarios',
      'tablaBD' => 'usuarios',
      'nombreCampoFoto' => 'foto_usuario',
      'nombreCampoId' => 'cedula_usuario',
      'valorId' => $this->cedulaUsuario,
      'BD' => 'seguridad',
    ]);
  }
  private function iniciarSesionUsuariosP() {
    $check_usuario = $this->seleccionarDatos2([
      'campos' => "
        us.cedula_usuario, us.nombre_usuario, us.apellido_usuario,
        ro.id_rol, ro.nombre_rol, us.usuario_usuario, us.contrasena_usuario,
        us.foto_usuario,us.ultimo_acceso_usuario, us.intentos_inicio_sesion_fallidos_usuario
      ",
      'tabla' =>  "usuarios as us",
      'BD' => 'seguridad',
      'datosJoins' => [
        'roles as ro' => 'us.id_rol = ro.id_rol'
      ],
      'WHERE' => [
        "us.usuario_usuario" => $this->usuarioUsuario,
      ],
    ])->fetch();
    if (!isset($check_usuario['cedula_usuario'])) {
      return [
        "tipo" => "simple",
        "titulo" => "Usuario incorrecto",
        "texto" => "El usuario que ha introducido es incorrecto, por favor verifique e intente nuevamente",
        "icono" => "error",
      ];
    }
    if ($check_usuario['intentos_inicio_sesion_fallidos_usuario'] >= 5) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Demasiados intentos',
        'texto' => 'Ha sobrepasado el limite de intentos permitidos para iniciar sesión',
        'icono' => 'error',
      ];
    }
    if (!password_verify($this->contrasena1Usuario, $check_usuario['contrasena_usuario'])) {
      $resultado = $this->actualizarDatos2([
        'tabla' => 'usuarios',
        'BD' => 'seguridad',
        'datos' => [
          'intentos_inicio_sesion_fallidos_usuario' => ((int)$check_usuario['intentos_inicio_sesion_fallidos_usuario'] + 1)
        ],
        'WHERE' => [
          'usuario_usuario' => $check_usuario['usuario_usuario']
        ]
      ]);
      $this->commit();
      if ($resultado <= 0 || $resultado == false) {
        return [
          "tipo" => "simple",
          "titulo" => "Error",
          "texto" => "No se pudo confirmar los intentos de inicio de sesión",
          "icono" => "error",
        ];
      }

      return [
        "tipo" => "simple",
        "titulo" => "Contraseña incorrecta",
        "texto" => "La contraseña que ha introducido es incorrecta, por favor verifique e intente nuevamente",
        "icono" => "error",
      ];
    }
    $resultado = $this->actualizarDatos2([
      'tabla' => 'usuarios',
      'BD' => 'seguridad',
      'datos' => [
        'ultimo_acceso_usuario' => $this->FechaHora_Sel('fecha_hora_BD'),
        'intentos_inicio_sesion_fallidos_usuario' => 0,
      ],
      'WHERE' => [
        'usuario_usuario' => $check_usuario['usuario_usuario']
      ]
    ]);
    if ($resultado == false) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Error al iniciar sesión',
        'texto' => 'No se pudo iniciar sesión',
        'icono' => 'error',
      ];
    }

    /*Creamos las variables de sesión */
    $_SESSION['cedula'] = $check_usuario['cedula_usuario'];
    $_SESSION['nombre'] = $check_usuario['nombre_usuario'];
    $_SESSION['apellido'] = $check_usuario['apellido_usuario'];
    $_SESSION['usuario'] = $check_usuario['usuario_usuario'];
    $_SESSION['rol'] = $check_usuario['id_rol'];
    $_SESSION['nombreRol'] = $check_usuario['nombre_rol'];
    $_SESSION['foto'] = $check_usuario['foto_usuario'];
    $_SESSION['TOKEN_CSRF'] = bin2hex(random_bytes(32));
    $_SESSION['ultimo_inicio_sesion'] = $check_usuario['ultimo_acceso_usuario'];

    $objBitacora = new bitacoraModelo();
    $rb = $objBitacora->registrarBitacora('usuarios', 'iniciar sesión', 'éxito');
    if (($rb['icono'] ?? '') == 'error') return $rb;
    $this->commit();
    return [
      "tipo" => "redireccionar",
      "url" => $this->redireccionarUsuario(true)
    ];
  }
  private function cerrarSesionUsuariosP() {
    session_destroy();
    return [
      "tipo" => "redireccionar",
      "url" => APP_URL . "usuarios/login"
    ];
  }
}
