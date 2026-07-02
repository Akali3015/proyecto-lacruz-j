<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\clientesModelo;
use src\modelos\accesosModelo;

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

  public function validarUsuarios(string $permiso, null|array  &$info = null, null|array $requerido = null) {
    $objAcceso = new accesosModelo();
    $r = $objAcceso->validarPermisos('usuarios', $permiso);
    if ($r) return $r;

    //Concatenar los strings
    if (isset($info['prefijo_telefono_usuario'])) {
      $info['telefono_usuario'] = $info['prefijo_telefono_usuario'] . $info['telefono_usuario'];
    }
    if (isset($info['codigo_cedula_usuario'])) {
      $info['cedula_usuario'] = $info['codigo_cedula_usuario'] . $info['cedula_usuario'];
    }
    $esquema = [
      'tipo' => 'arrayA',
      'propiedades' => [
        "apellido_usuario" => [
          ...molNombrePer,
          "nombreAlerta" => "apellido",
        ],
        "cedula_usuario" => [
          ...molCedulaRifLetra,
          "nombreAlerta" => "cédula",
          "nombreBD" => "cedula_usuario",
          "tablaBD" => "usuarios",
          "debeSerUnicoBD" => true,
          "debeExistirBD" => true,
          "BD" => 'seguridad',
        ],
        "contrasena1_usuario" => [
          ...molContrasena,
          "nombreAlerta" => "contraseña",
        ],
        "contrasena2_usuario" => [
          ...molContrasena,
          "nombreAlerta" => "contraseña de confirmación",
        ],
        "correo_usuario" => [
          ...molCorreo,
          "nombreBD" => "correo_usuario",
          "nombreAlerta" => "correo",
          "tablaBD" => "usuarios",
          "BD" => 'seguridad',
          "debeSerUnicoBD" => true
        ],
        "direccion_usuario" => [
          ...molDescripcion,
          "nombreAlerta" => "dirección",
        ],
        "foto_usuario" => [
          ...molFotoInd,
          "nombreAlerta" => "foto de pérfil",
        ],
        "id_rol" => [
          ...molId,
          "nombreAlerta" => "rol",
          "nombreBD" => "id_rol",
          "tabla" => "roles",
          "BD" => 'seguridad',
          "debeExistir" => true
        ],
        "nombre_usuario" => [
          ...molNombrePer,
          "nombreAlerta" => "nombre",
        ],
        "telefono_usuario" => [
          ...molTelefono,
          "nombreBD" => 'telefono_usuario',
          "nombreAlerta" => "teléfono",
          "tabla" => "usuarios",
          "BD" => 'seguridad',
          "debeSerUnico" => true,
        ],
        "usuario_usuario" => [
          ...molUsuario,
          "nombreBD" => "usuario_usuario",
          "nombreAlerta" => "nombre de usuario",
          "tabla" => "usuarios",
          "BD" => 'seguridad',
          "debeSerUnico" => true,
        ],
      ],
      'requerido' => $requerido
    ];
    if (isset($info) && isset($requerido)) {

      if ($info['accion'] == 'eliminar') {
        $esquema['propiedades']['cedula_usuario']['debeSerDiferenteA'] = $this->seleccionarDatos2([
          'campos' => 'cedula_usuario',
          'tabla' => 'usuarios',
          'BD' => 'seguridad',
          'WHERE' => [
            'nombre_rol' => 'SUPER USUARIO'
          ]
        ])->fetch();
      }
      if ($info['accion'] == 'iniciarSesion') {
        unset($esquema['propiedades']['usuario_usuario']['debeSerUnicoBD']);
      }
      if ($info['accion'] == 'registrar') {
        unset($esquema['propiedades']['cedula_usuario']['debeExistirBD']);
        $esquema['propiedades']['contrasena1_usuario']['deberSerIgual'] = $info['contrasena2_usuario'] ?? '';
        $esquema['propiedades']['contrasena2_usuario']['deberSerIgual'] = $info['contrasena1_usuario'] ?? '';
      }

      //Para verificar que si se quiere act la contraseña esta sea igual
      if (
        $info['accion'] == 'actualizar' && (
          $info['contrasena1_usuario'] ?? '' != '' ||
          $info['contrasena2_usuario'] ?? '' != '')
      ) {
        $esquema['propiedades']['contrasena1_usuario']['deberSerIgual'] = $info['contrasena2_usuario'] ?? '';
        $esquema['propiedades']['contrasena2_usuario']['deberSerIgual'] = $info['contrasena1_usuario'] ?? '';
      }

      $r = $this->limpiarValidar($info, $esquema);
      if ($r) return $r;
    }
    return false;
  }
  public function seleccionarUsuarios(array $info) {
    if (($info['cedula_usuario'] ?? '') != '') {
      $r = $this->validarUsuarios('listar', $info, [
        'cedula_usuario' => 'cedula_usuario_act',
      ]);
      if ($r) return $r;
      $this->cedulaUsuario = $info['cedula_usuario'];
    } else {
      $r = $this->validarUsuarios('listar');
      if ($r) return $r;
    }
    return $this->seleccionarUsuariosP();
  }
  public function registrarUsuarios(array $info) {
    $r = $this->validarUsuarios('registrar', $info, [
      'apellido_usuario',
      'cedula_usuario',
      'contrasena1_usuario',
      'contrasena2_usuario',
      'correo_usuario',
      'id_rol',
      'nombre_usuario',
      'telefono_usuario',
      'usuario_usuario',
    ]);
    if ($r) return $r;
    [
      'apellido_usuario' => $this->apellidoUsuario,
      'cedula_usuario' => $this->cedulaUsuario,
      'contrasena1_usuario' => $this->contrasena1Usuario,
      'contrasena2_usuario' => $this->contrasena2Usuario,
      'id_rol' => $this->rolUsuario,
      'nombre_usuario' => $this->nombreUsuario,
      'telefono_usuario' => $this->telefonoUsuario,
      'usuario_usuario' => $this->usuarioUsuario,
    ] = $info;
    $this->direccionUsuario = $info['direccion_usuario'] ?? '';
    $this->fotoUsuario = $info['foto_usuario'] ?? [];
    $this->contrasena1Usuario = password_hash($this->contrasena1Usuario, PASSWORD_BCRYPT, ["cost" => 10]);
    return $this->registrarUsuariosP();
  }
  public function actualizarUsuarios(array $info) {
    $requerido = [
      'cedula_usuario' => 'cedula_usuario_act',
      'id_rol',
      'nombre_usuario',
      'apellido_usuario',
      'usuario_usuario',
      'telefono_usuario',
      'correo_usuario',
      'direccion_usuario',
    ];
    if (($info['contrasena1_usuario'] ?? '') != '' || ($info['contrasena2_usuario'] ?? '') != '') {
      array_push($requerido, 'contrasena1_usuario', 'contrasena2_usuario');
    }
    $r = $this->validarUsuarios('actualizar', $info, $requerido);
    if ($r) return $r;

    [
      'apellido_usuario' => $this->apellidoUsuario,
      'cedula_usuario' => $this->cedulaUsuario,
      'contrasena1_usuario' => $this->contrasena1Usuario,
      'contrasena2_usuario' => $this->contrasena2Usuario,
      'correo_usuario' => $this->correoUsuario,
      'rol_usuario' => $this->rolUsuario,
      'nombre_usuario' => $this->nombreUsuario,
      'telefono_usuario' => $this->telefonoUsuario,
      'usuario_usuario' => $this->usuarioUsuario,
      'direccion_usuario' => $this->direccionUsuario,
    ] = $info;

    //Usamos este metodo para procesar e incriptar la contraseña
    if ($this->contrasena1Usuario != '') {
      $this->contrasena1Usuario = password_hash($this->contrasena1Usuario, PASSWORD_BCRYPT, ["cost" => 10]);
    }
    return $this->actualizarUsuariosP();
  }
  public function eliminarUsuarios(array $info) {
    $r = $this->validarUsuarios('eliminar', $info, ['cedula_usuario']);
    if ($r) return $r;

    $this->cedulaUsuario = $info['cedula_usuario'];
    return $this->eliminarUsuariosP();
  }
  public function actualizarFotosUsuarios(array $info) {
    $r = $this->validarUsuarios('actualizar', $info, ['cedula_usuario', 'foto_usuario']);
    if ($r) return $r;
    $this->cedulaUsuario = $info['cedula_usuario'];
    $this->fotoUsuario = $info['foto_usuario'];
    return $this->actualizarFotosUsuariosP();
  }
  public function eliminarFotosUsuarios(array $info) {
    $r = $this->validarUsuarios('actualizar', $info, ['cedula_usuario']);
    if ($r) return $r;
    $this->cedulaUsuario = $info['cedula_usuario'];
    return $this->eliminarFotosUsuariosP();
  }
  public function iniciarSesionUsuarios(array $info) {

    // Enviar verificacion interna al servidor de Google
    /* if (empty($info['g-recaptcha-response'])) {
      return [
        "tipo" => "simple",
        "titulo" => "Seguridad",
        "texto" => "Falta completar la validación del puzle de seguridad.",
        "icono" => "error"
      ];
    }
    $resultadoCaptcha = $this->hacerPeticionesAPIs([
      'url' => 'https://www.google.com/recaptcha/api/siteverify',
      'datosPe' => [
        'secret'   => '6LdSVPgsAAAAAFAQ6_8Z-y0Q_0s-Xy3XVLGtydmw',
        'response' => $info['g-recaptcha-response'],
        'remoteip' => $_SERVER['REMOTE_ADDR']
      ]
    ]);
    if (($resultadoCaptcha['success'] ?? false) != true) {
      return [
        "tipo" => "simple",
        "titulo" => "Fallo de Seguridad",
        "texto" => "La verificación del captcha falló. Inténtelo nuevamente.",
        "icono" => "error"
      ];
    } */

    $r = $this->validarUsuarios('listar', $info, ['usuario_usuario', 'contrasena1_usuario']);
    if ($r) return $r;
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
          usuario_usuario, id_rol,contrasena_usuario,direccion_usuario,
          foto_usuario
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
    $objBitacora = new bitacoraModelo();

    $error = function () use ($fotoUsuario, $objBitacora) {
      $this->rollback();
      if (isset($_SESSION['cedula'])) {
        $objBitacora->registrarBitacora([
          'modulo' => 'usuarios',
          'accion' => 'registrar usuario con la cedula/rif: ' . $this->cedulaUsuario,
          'resultado' => 'Fallido',
          'commit' => true
        ]);
      }
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


    $objNot = new mensajesWSModelo();
    $resultado = $objNot->enviarMensajesWS([
      "receptor" => [
        'tipo' => 'todos',
      ],
      'cuerpo' => [
        [
          'accion' => "borrarDataModuloSS",
          'modulo' => 'usuarios'
        ],
        [
          'accion' => "actDT",
          'modulo' => 'pedidos'
        ],
      ],
      'noCommit' => true
    ]);
    unset($objNot);
    if (($resultado['icono'] ?? '') == 'error' && !isset($_COOKIE['TEMP']) && !isset($_ENV['MODO_TESTEO'])) return $resultado;


    if (isset($_SESSION['cedula'])) {
      $rb = $objBitacora->registrarBitacora([
        'modulo' => 'usuarios',
        'accion' => 'registrar usuario con la cedula/rif: ' . $this->cedulaUsuario,
        'resultado' => 'Éxito',
        'nuevo' => $datoNuevos,
      ]);
      if ($rb) return $rb;
    }
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

    $objBitacora = new bitacoraModelo();
    $rb = $objBitacora->registrarBitacora([
      'modulo' => 'usuarios',
      'accion' => 'Actualizar usuario con la cedula/rif: ' . $this->cedulaUsuario,
      'resultado' => 'Éxito',
      'viejo' => $datosActuales,
      'nuevo' => $datosAct
    ]);
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
    $objBitacora = new bitacoraModelo();
    $eliminarUsuario = $this->eliminarDatos2([
      'tabla' => "usuarios",
      'BD' => 'seguridad',
      'WHERE' => [
        "cedula_usuario" => $this->cedulaUsuario
      ]
    ]);
    if ($eliminarUsuario <= 0) {
      $rb = $objBitacora->registrarBitacora([
        'modulo' => 'usuarios',
        'accion' => 'Eliminar usuario con la cedula/rif: ' . $this->cedulaUsuario,
        'resultado' => 'Éxito',
        'commit' => true
      ]);
      if ($rb) return $rb;
      return [
        "tipo" => "simple",
        "titulo" => "Usuario no encontrado",
        "texto" => "El usuario no existe en la Base de Datos",
        "icono" => "error"
      ];
    }
    $rb = $objBitacora->registrarBitacora([
      'modulo' => 'usuarios',
      'accion' => 'Eliminar usuario con la cedula/rif: ' . $this->cedulaUsuario,
      'resultado' => 'Éxito',
    ]);
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
    $objBitacora = new bitacoraModelo();
    $usuariosActual = $this->seleccionarUsuarios([
      'cedula_usuario' => $this->cedulaUsuario
    ]);
    $rb = $objBitacora->registrarBitacora([
      'modulo' => 'usuarios',
      'accion' => 'Actualizar foto del usuario con la cedula/rif: ' . $this->cedulaUsuario,
      'resultado' => 'Éxito',
      'viejo' => ['fofo_usuario' => $usuariosActual['foto_usuario']],
      'nuevo' => ['foto_usuario' => $this->fotoUsuario],
    ]);
    if ($rb) return $rb;
    $resultado = $this->Imagenes_Act([
      'subCarpeta' => 'usuarios',
      'imagen' => $this->fotoUsuario,
      'tablaBD' => 'usuarios',
      'nombreCampoFoto' => 'foto_usuario',
      'nombreCampoId' => 'cedula_usuario',
      'valorId' => $this->cedulaUsuario,
      'BD' => 'seguridad',
    ]);
    if ($resultado['icono'] == 'success') $this->commit();
    return $resultado;
  }
  private function eliminarFotosUsuariosP() {
    $objBitacora = new bitacoraModelo();
    $rb = $objBitacora->registrarBitacora([
      'modulo' => 'usuarios',
      'accion' => 'Eliminar foto del usuario con la cedula/rif: ' . $this->cedulaUsuario,
      'resultado' => 'Éxito',
    ]);
    if ($rb) return $rb;

    $resultado = $this->Imagenes_Eli([
      'subCarpeta' => 'usuarios',
      'tablaBD' => 'usuarios',
      'nombreCampoFoto' => 'foto_usuario',
      'nombreCampoId' => 'cedula_usuario',
      'valorId' => $this->cedulaUsuario,
      'BD' => 'seguridad',
    ]);
    if ($resultado['icono'] == 'success') $this->commit();
    return $resultado;
  }
  private function iniciarSesionUsuariosP() {

    $instruccionesConsultaCom = [
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
    ];
    $datosUsAtuales = $this->seleccionarDatos2($instruccionesConsultaCom)->fetch();
    if (!isset($datosUsAtuales['cedula_usuario'])) {
      return [
        "tipo" => "simple",
        "titulo" => "Usuario incorrecto",
        "texto" => "El usuario que ha introducido es incorrecto, por favor verifique e intente nuevamente",
        "icono" => "error",
      ];
    }
    if ($datosUsAtuales['intentos_inicio_sesion_fallidos_usuario'] >= 5) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Demasiados intentos',
        'texto' => 'Ha sobrepasado el limite de intentos permitidos para iniciar sesión',
        'icono' => 'error',
      ];
    }
    if (!password_verify($this->contrasena1Usuario, $datosUsAtuales['contrasena_usuario'])) {
      $resultado = $this->actualizarDatos2([
        'tabla' => 'usuarios',
        'BD' => 'seguridad',
        'datos' => [
          'intentos_inicio_sesion_fallidos_usuario' => ((int)$datosUsAtuales['intentos_inicio_sesion_fallidos_usuario'] + 1)
        ],
        'WHERE' => [
          'usuario_usuario' => $datosUsAtuales['usuario_usuario']
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
        'usuario_usuario' => $datosUsAtuales['usuario_usuario']
      ],
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
    $_SESSION['cedula'] = $datosUsAtuales['cedula_usuario'];
    $_SESSION['nombre'] = $datosUsAtuales['nombre_usuario'];
    $_SESSION['apellido'] = $datosUsAtuales['apellido_usuario'];
    $_SESSION['usuario'] = $datosUsAtuales['usuario_usuario'];
    $_SESSION['rol'] = $datosUsAtuales['id_rol'];
    $_SESSION['nombreRol'] = $datosUsAtuales['nombre_rol'];
    $_SESSION['foto'] = $datosUsAtuales['foto_usuario'];
    $_SESSION['TOKEN_CSRF'] = bin2hex(random_bytes(32));
    $_SESSION['ultimo_inicio_sesion'] = $datosUsAtuales['ultimo_acceso_usuario'];

    $datosUsuarioDespues = $this->seleccionarDatos2($instruccionesConsultaCom)->fetch();
    $objBitacora = new bitacoraModelo();

    unset($datosUsAtuales['contrasena_usuario']);
    unset($datosUsuarioDespues['contrasena_usuario']);
    $rb = $objBitacora->registrarBitacora([
      'modulo' => 'usuarios',
      'accion' => 'Iniciar sesión',
      'resultado' => 'Éxito',
      'viejo' => $datosUsAtuales,
      'nuevo' => $datosUsuarioDespues
    ]);
    if ($rb) return $rb;
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
