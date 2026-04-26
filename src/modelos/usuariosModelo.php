<?php

namespace src\modelos;

use src\config\connect\conexion;
use PDO;
use PDOException;
use Exception;

class usuariosModelo extends conexion
{
  private $cedulaUsuario;
  private $rolUsuario;
  private $nombreUsuario;
  private $apellidoUsuario;
  private $usuarioUsuario;
  private $contrasena1Usuario;
  private $contrasena2Usuario;
  private $contrasena3Usuario;
  private $telefonoUsuario;
  private $correoUsuario;
  private $fotoUsuario;

  public function seleccionarUsuarios($cedula = null)
  {
    $this->cedulaUsuario = $cedula;
    if ($this->cedulaUsuario != null && $this->cedulaUsuario != "") {
      //Arrays para las validaciones
      $campos = [
        [
          "campo_valor" => $this->cedulaUsuario,
          "formulario_nombre" => "cédula",
          "requerido" => true,
          "minimo" => minRegexCedulaRif,
          "maximo" => maxRegexCedulaRif,
          "expresion_re" => regexCedulaRif,
        ]
      ];

      $respuesta = $this->limpiar_Verificar($campos);
      if ($respuesta !== false) {
        return $respuesta;
        exit();
      }
    }
    return $this->seleccionarUsuariosP();
  }
  public function registrarUsuarios($cedula, $id_rol, $nombre, $apellido, $telefono, $correo, $usuario, $contrasena1, $contrasena2)
  {
    try {
      $this->cedulaUsuario = $cedula;
      $this->rolUsuario = $id_rol;
      $this->nombreUsuario = $nombre;
      $this->apellidoUsuario = $apellido;
      $this->usuarioUsuario = $usuario;
      $this->contrasena1Usuario = $contrasena1;
      $this->contrasena2Usuario = $contrasena2;
      $this->telefonoUsuario = $telefono;
      $this->correoUsuario = $correo;

      //Arrays para las validaciones
      $campos = [
        [
          "campo_nombre" => "cedula_usuario",
          "campo_valor" => $this->cedulaUsuario,
          "formulario_nombre" => "cédula",
          "requerido" => true,
          "minimo" => minRegexCedulaRif,
          "maximo" => maxRegexCedulaRif,
          "expresion_re" => regexCedulaRif,
          "tabla" => "usuarios",
          "BD" => 'seguridad',
          "debeSerUnico" => true,
        ],
        [
          "campo_nombre" => "id_rol",
          "campo_valor" => $this->rolUsuario,
          "formulario_nombre" => "rol del usuario",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "roles",
          "BD" => 'seguridad',
          "debeExistir" => true,
        ],
        [
          "campo_valor" => $this->nombreUsuario,
          "formulario_nombre" => "nombre",
          "requerido" => true,
          "minimo" => minRegexNombrePer,
          "maximo" => maxRegexNombrePer,
          "expresion_re" => regexNombrePer,
        ],
        [
          "campo_valor" => $this->apellidoUsuario,
          "formulario_nombre" => "apellido",
          "requerido" => true,
          "minimo" => minRegexNombrePer,
          "maximo" => maxRegexNombrePer,
          "expresion_re" => regexNombrePer,
        ],
        [
          "campo_nombre" => "correo_usuario",
          "campo_valor" => $this->correoUsuario,
          "formulario_nombre" => "correo",
          "requerido" => true,
          "minimo" => minRegexCorreo,
          "maximo" => maxRegexCorreo,
          "expresion_re" => regexCorreo,
          "tabla" => "usuarios",
          "BD" => 'seguridad',
          "debeSerUnico" => true
        ],
        [
          "campo_valor" => $this->telefonoUsuario,
          "formulario_nombre" => "teléfono",
          "requerido" => true,
          "minimo" => minRegexTelefono,
          "maximo" => maxRegexTelefono,
          "expresion_re" => regexTelefono,
        ],
        [
          "campo_nombre" => "usuario_usuario",
          "campo_valor" => $this->usuarioUsuario,
          "formulario_nombre" => "nombre de usuario",
          "requerido" => true,
          "minimo" => minRegexUsuario,
          "maximo" => maxRegexUsuario,
          "expresion_re" => regexUsuario,
          "tabla" => "usuarios",
          "BD" => 'seguridad',
          "debeSerUnico" => true
        ],
        [
          "campo_valor" => $this->contrasena1Usuario,
          "formulario_nombre" => "contraseña",
          "requerido" => true,
          "minimo" => minRegexContrasena,
          "maximo" => maxRegexContrasena,
          "expresion_re" => regexContrasena,
          "camposIguales" => $this->contrasena2Usuario,
        ],
      ];

      $respuesta = $this->limpiar_Verificar($campos);
      if ($respuesta !== false) {
        return $respuesta;
        exit();
      } else {
        //Usamos este metodo para procesar e incriptar la contraseña
        $this->contrasena1Usuario = password_hash($this->contrasena1Usuario, PASSWORD_BCRYPT, ["cost" => 10]);
      }
      return $this->registrarUsuariosP();
    } catch (PDOException $e) {
      error_log("Error en Usuario->registrar(): " . $e->getMessage());
      throw new Exception("Error al registrar el usuario en la base de datos: " . $e->getMessage());
    }
  }
  public function actualizarUsuarios($cedula,$nombre,$apellido, $correo, $telefono, $rol, $usuario, $contrasena1, $contrasena2, $contrasena3)
  {
    $this->cedulaUsuario = $cedula;
    $this->nombreUsuario = $nombre;
    $this->apellidoUsuario = $apellido;
    $this->correoUsuario = $correo;
    $this->telefonoUsuario = $telefono;
    $this->rolUsuario = $rol;
    $this->usuarioUsuario = $usuario;
    $this->contrasena1Usuario = $contrasena1;
    $this->contrasena2Usuario = $contrasena2;
    $this->contrasena3Usuario = $contrasena3;

    //Arrays para las validaciones
    $campos = [
      [
        "campo_nombre" => "cedula_usuario",
        "campo_valor" => &$this->cedulaUsuario,
        "formulario_nombre" => "cédula",
        "requerido" => true,
        "minimo" => minRegexCedulaRif,
        "maximo" => maxRegexCedulaRif,
        "expresion_re" => regexCedulaRif,
        "tabla" => "usuarios",
        "BD" => 'seguridad',
        "debeExistir" => true,
        "debeSerUnico" => true
      ],
      [
        "campo_valor" => &$this->nombreUsuario,
        "formulario_nombre" => "nombre",
        "requerido" => true,
        "minimo" => minRegexNombrePer,
        "maximo" => maxRegexNombrePer,
        "expresion_re" => regexNombrePer,
      ],
      [
        "campo_valor" => &$this->apellidoUsuario,
        "formulario_nombre" => "apellido",
        "requerido" => true,
        "minimo" => minRegexNombrePer,
        "maximo" => maxRegexNombrePer,
        "expresion_re" => regexNombrePer,
      ],
      [
        "campo_nombre" => "correo_usuario",
        "campo_valor" => &$this->correoUsuario,
        "formulario_nombre" => "correo",
        "requerido" => true,
        "minimo" => minRegexCorreo,
        "maximo" => maxRegexCorreo,
        "expresion_re" => regexCorreo,
        "tabla" => "usuarios",
        "BD" => 'seguridad',
        "debeSerUnico" => true
      ],
      [
        "campo_valor" => &$this->telefonoUsuario,
        "formulario_nombre" => "teléfono",
        "requerido" => true,
        "minimo" => minRegexTelefono,
        "maximo" => maxRegexTelefono,
        "expresion_re" => regexTelefono
      ],
      [
        "campo_nombre" => "usuario_usuario",
        "campo_valor" => &$this->usuarioUsuario,
        "formulario_nombre" => "nombre de usuario",
        "requerido" => true,
        "minimo" => minRegexUsuario,
        "maximo" => maxRegexUsuario,
        "expresion_re" => regexUsuario,
        "tabla" => "usuarios",
        "BD" => 'seguridad',
        "debeSerUnico" => true
      ],
      [
        "campo_valor" => &$this->contrasena1Usuario,
        "formulario_nombre" => "contraseña",
        "minimo" => minRegexContrasena,
        "maximo" => maxRegexContrasena,
        "expresion_re" => regexContrasena,
        "camposIguales" => $this->contrasena2Usuario
      ],
      [
        "campo_valor" => &$this->contrasena3Usuario,
        "formulario_nombre" => "contraseña",
        "minimo" => minRegexContrasena,
        "maximo" => maxRegexContrasena,
        "expresion_re" => regexContrasena,
      ],
      [
        "campo_nombre" => "id_rol",
        "campo_valor" => &$this->rolUsuario,
        "formulario_nombre" => "rol",
        "minimo" => minRegexId,
        "maximo" => maxRegexId,
        "expresion_re" => regexId,
        "tabla" => "roles",
        "BD" => 'seguridad',
        "debeExistir" => true
      ],
    ];

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
      exit();
    } else {
      //Usamos este metodo para procesar e incriptar la contraseña
      if ($this->contrasena1Usuario != '') {
        $this->contrasena1Usuario = password_hash($this->contrasena1Usuario, PASSWORD_BCRYPT, ["cost" => 10]);
      }
      return $this->actualizarUsuariosP();
    }
  }
  public function eliminarUsuarios($cedula)
  {
    /*Limpiar Inyección de SQL */
    $this->cedulaUsuario = $cedula;

    //Arrays para las validaciones
    $campos = [
      [
        "campo_nombre" => "cedula_usuario",
        "campo_valor" => $this->cedulaUsuario,
        "formulario_nombre" => "cédula",
        "requerido" => true,
        "minimo" => minRegexCedulaRif,
        "maximo" => maxRegexCedulaRif,
        "expresion_re" => regexCedulaRif,
        "debeExistir" => true,
        "camposDiferentes" => 30485684,
        "tabla" => "usuarios",
        "BD" => 'seguridad',
      ]
    ];

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
      exit();
    } else {
      return $this->eliminarUsuariosP();
    }
  }
  public function iniciarSesionUsuarios($usuario, $contrasena)
  {

    $this->usuarioUsuario = $usuario;
    $this->contrasena1Usuario = $contrasena;

    //Arrays para las validaciones
    $campos = [
      [
        "campo_nombre" => "usuario_usuario",
        "campo_valor" => $this->usuarioUsuario,
        "formulario_nombre" => "nombre de usuario",
        "requerido" => true,
        "minimo" => minRegexUsuario,
        "maximo" => maxRegexUsuario,
        "expresion_re" => regexUsuario,
        "tabla" => "usuarios",
        "BD" => 'seguridad',
        "debeExistir" => true
      ],
      [
        "campo_valor" => $this->contrasena1Usuario,
        "formulario_nombre" => "contraseña",
        "requerido" => true,
        "minimo" => minRegexContrasena,
        "maximo" => maxRegexContrasena,
        "expresion_re" => regexContrasena,
      ],
    ];

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
      exit();
    }
    return $this->iniciarSesionUsuariosP();
  }
  public function cerrarSesionUsuarios()
  {
    return $this->cerrarSesionUsuariosP();
  }
  public function actualizarFotosUsuarios($cedula, $foto)
  {
    $this->cedulaUsuario = $cedula;
    $this->fotoUsuario = $foto;

    //Arrays para las validaciones
    $campos = [
      [
        "campo_nombre" => "cedula_usuario",
        "campo_valor" => &$this->cedulaUsuario,
        "formulario_nombre" => "cédula",
        "requerido" => true,
        "minimo" => minRegexCedulaRif,
        "maximo" => maxRegexCedulaRif,
        "expresion_re" => regexCedulaRif,
        "debeExistir" => true,
        "tabla" => "usuarios",
        "BD" => 'seguridad',
      ]
    ];

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
      exit();
    } else {
      return $this->actualizarFotosUsuariosP();
    }
  }
  public function eliminarFotosUsuarios($cedula)
  {
    $this->cedulaUsuario = $cedula;
    //Arrays para las validaciones
    $campos = [
      [
        "campo_nombre" => "cedula_usuario",
        "campo_valor" => &$this->cedulaUsuario,
        "formulario_nombre" => "cédula",
        "requerido" => true,
        "minimo" => minRegexCedulaRif,
        "maximo" => maxRegexCedulaRif,
        "expresion_re" => regexCedulaRif,
        "debeExistir" => true,
        "tabla" => "usuarios",
        "BD" => 'seguridad',
      ]
    ];

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
      exit();
    } else {
      return $this->eliminarFotosUsuariosP();
    }
  }

  //-- PRIVADOS [ ENCAPSULAMIENTO ]--//
  private function seleccionarUsuariosP()
  {
    if ($this->cedulaUsuario == null || $this->cedulaUsuario == "") {
      //campos específicos para la consulta
      $datos = $this->seleccionarDatos2([
        'campos' => '
          u.cedula_usuario, ro.nombre_rol, u.nombre_usuario,
          u.apellido_usuario, u.telefono_usuario, u.correo_usuario,
          u.usuario_usuario, u.foto_usuario
        ',
        'tabla' => 'usuarios AS u',
        "BD" => 'seguridad',
        'datosJoins' => [
          "roles AS ro"=>"u.id_rol = ro.id_rol"
        ],
        'WHERE' => [
          "cedula_usuario"=>[
            "!=" =>[30485684,$_SESSION['cedula']]
          ]
        ],
      ]);
      $datos = $datos->fetchAll(PDO::FETCH_ASSOC);
      return $datos; /*Devolvemos*/
    } else {
      $resultado = $this->seleccionarDatos2([
        'campos' => '
          cedula_usuario, nombre_usuario,
          apellido_usuario, telefono_usuario, correo_usuario,
          usuario_usuario, id_rol
        ',
        'tabla' => 'usuarios',
        'BD'=>'seguridad',
        'WHERE' => [
          "cedula_usuario"=> $this->cedulaUsuario,
        ]
      ]);
      if ($resultado->rowCount() <= 0) {
        $alerta = [
          "tipo" => "simple",
          "titulo" => "Usuario no encontrado",
          "texto" => "El usuario que ha intentado actualizar no se encuentra en la base de datos",
          "icono" => "error"
        ];
        return $alerta;
        exit();
      } else {
        $usuario = $resultado->fetch(PDO::FETCH_ASSOC);
      }
      return $usuario;
    }
  }
  private function registrarUsuariosP()
  {
    $resultado = $this->guardarDatos2([
      'datos'=>[
        "cedula_usuario"=> $this->cedulaUsuario,
        "nombre_usuario"=> $this->nombreUsuario,
        "apellido_usuario"=> $this->apellidoUsuario,
        "correo_usuario"=> $this->correoUsuario,
        "telefono_usuario"=> $this->telefonoUsuario,
        "id_rol"=> $this->rolUsuario,
        "usuario_usuario"=> $this->usuarioUsuario,
        "contrasena_usuario"=> $this->contrasena1Usuario,
      ],
      'tabla'=>'usuarios',
      'BD'=>'seguridad',
      'WHERE'=>[
        "cedula_usuario" => $this->cedulaUsuario
      ]
    ]);
    if ($resultado == 1) {
      $alerta = [
        "tipo" => "limpiarYcerrar",
        "titulo" => "Usuario registrado",
        "texto" => "El usuario ha sido registrado exitosamente",
        "icono" => "success"
      ];
      $this->commit();
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Usuario no registrado",
        "texto" => "El usuario no ha sido registrado exitosamente",
        "icono" => "error",
      ];
    }
    return $alerta;
  }
  private function actualizarUsuariosP()
  {
    $resultado = $this->seleccionarDatos2([
      "campos" => "contrasena_usuario, id_rol",
      "tabla" => "usuarios",
      'BD'=>'seguridad',
      'WHERE' => [
        "cedula_usuario"=> $this->cedulaUsuario,
      ]
    ]);
    $usuariosExistente = $resultado->fetch(PDO::FETCH_ASSOC);

    if (
      $this->contrasena3Usuario != '' &&
      !password_verify($this->contrasena3Usuario, $usuariosExistente['contrasena_usuario'])
    ) {
      return [
        'tipo' => 'simple',
        'icono' => 'error',
        'titulo' => 'Contraseña actual incorrecta',
        'texto' => 'El valor introducido dentro de la contraseña actual es incorrecto',
      ];
    }
    if ($this->contrasena1Usuario != '' && $this->contrasena2Usuario != '') {
      if ($this->contrasena3Usuario == '') {
        return [
          'tipo' => 'simple',
          'icono' => 'error',
          'titulo' => 'Contraseña actual incorrecta',
          'texto' => 'Si desea actualizar la contraseña debe introducir su actual valor',
        ];
      }
    }

    if ($this->contrasena1Usuario == "") {
      $this->contrasena1Usuario = $usuariosExistente['contrasena_usuario'];
    };
    if ($this->rolUsuario == '') {
      $this->rolUsuario = $usuariosExistente['id_rol'];
    }

    $resultado = $this->actualizarDatos2([
      "datos" => [
        "nombre_usuario"=>$this->nombreUsuario,
        "apellido_usuario"=>$this->apellidoUsuario,
        "telefono_usuario"=>$this->telefonoUsuario,
        "id_rol"=>$this->rolUsuario,
        "usuario_usuario"=>$this->usuarioUsuario,
        "contrasena_usuario"=>$this->contrasena1Usuario,
      ],
      "tabla" => "usuarios",
      'BD'=>'seguridad',
      "WHERE" => [
        "cedula_usuario"=>$this->cedulaUsuario,
      ]
    ]);

    if ($resultado == false || $resultado <= 0) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Sin cambios realizados",
        "texto" => "No se realizó ningún cambio en el usuario",
        "icono" => "warning",
      ];
    } else {
      if ($this->cedulaUsuario == $_SESSION['cedula']) {
        $_SESSION['nombre'] = $this->nombreUsuario;
        $_SESSION['apellido'] = $this->apellidoUsuario;
        $_SESSION['telefono'] = $this->telefonoUsuario;
        $_SESSION['usuario'] = $this->usuarioUsuario;
        $_SESSION['rol'] = $this->rolUsuario;
      }
      $alerta = [
        "tipo" => "limpiarYcerrar",
        "titulo" => "Usuario actualizado",
        "texto" => "El usuario ha sido actualizado exitosamente",
        "icono" => "success",
      ];
      $this->commit();
    }

    return $alerta;
  }
  private function eliminarUsuariosP()
  {
    $eliminarUsuario = $this->eliminarDatos2([
      'tabla'=>"usuarios",
      'BD'=>'seguridad',
      'WHERE'=>[
        "cedula_usuario"=> $this->cedulaUsuario
      ]
    ]);
    if ($eliminarUsuario->rowCount() == 1) { /*Para verificar si se hizo la eliminación o no */
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Usuario eliminado",
        "texto" => "El usuario ha sido eliminado con éxito",
        "icono" => "success"
      ];
      $this->commit();
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Usuario no encontrado",
        "texto" => "El usuario no existe en la Base de Datos",
        "icono" => "error"
      ];
    }
    return $alerta;
  }
  private function iniciarSesionUsuariosP()
  {
    $check_usuario = $this->seleccionarDatos2([
      'campos' => "
        us.cedula_usuario, us.nombre_usuario, us.apellido_usuario,
        ro.id_rol, ro.nombre_rol, us.usuario_usuario, us.contrasena_usuario,
        us.foto_usuario
      ",
      'tabla' =>  "usuarios as us",
      'BD' => 'seguridad',
      'datosJoins' => [
        'roles as ro'=> 'us.id_rol = ro.id_rol'
      ],
      'WHERE' => [
        "us.usuario_usuario"=> $this->usuarioUsuario,
      ]
    ]);

    if ($check_usuario->rowCount() < 1) {
      return [
        "tipo" => "simple",
        "titulo" => "Usuario incorrecto",
        "texto" => "El usuario que ha introducido es incorrecto, por favor verifique e intente nuevamente",
        "icono" => "error",
      ];
    }

    $check_usuario = $check_usuario->fetch();
    if (
      ($this->usuarioUsuario != $check_usuario['usuario_usuario']) ||
      (!password_verify($this->contrasena1Usuario, $check_usuario['contrasena_usuario']))
    ) {
      return [
        "tipo" => "simple",
        "titulo" => "Contraseña incorrecta",
        "texto" => "La contraseña que ha introducido es incorrecta, por favor verifique e intente nuevamente",
        "icono" => "error",
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
  
    return [
      "tipo" => "redireccionar",
      "url" => $this->redireccionarUsuario(true)
    ];
  }
  private function cerrarSesionUsuariosP()
  {
    session_destroy();
    return [
      "tipo" => "redireccionar",
      "url" => APP_URL . "usuarios/login"
    ];
  }
  private function actualizarFotosUsuariosP()
  {
    return $this->Imagenes_Act([
      'subCarpeta'=>'usuarios',
      'imagen'=>$this->fotoUsuario,
      'tablaBD'=>'usuarios',
      'nombreCampoFoto'=>'foto_usuario',
      'nombreCampoId'=>'cedula_usuario',
      'valorId'=>$this->cedulaUsuario,
      'BD'=>'seguridad',
    ]);
  }
  private function eliminarFotosUsuariosP()
  {
    return $this->Imagenes_Eli([
      'subCarpeta'=>'usuarios',
      'tablaBD'=>'usuarios',
      'nombreCampoFoto'=>'foto_usuario',
      'nombreCampoId'=>'cedula_usuario',
      'valorId'=>$this->cedulaUsuario,
      'BD'=>'seguridad',
    ]);
  }
}
