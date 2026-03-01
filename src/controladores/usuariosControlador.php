<?php

use src\modelos\usuariosModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $accion = $_POST["accion"];
  $cedula = $_POST['cedula_usuario'] ?? "";
  $nombre = $_POST['nombre_usuario'] ?? "";
  $apellido = $_POST['apellido_usuario'] ?? "";
  $correo = $_POST['correo_usuario'] ?? "";
  $telefono = $_POST['telefono_usuario'] ?? "";
  $rol = $_POST['id_rol'] ?? "";
  $usuario = $_POST['usuario_usuario'] ?? "";
  $contrasena1 = $_POST['contrasena1_usuario'] ?? "";
  $contrasena2 = $_POST['contrasena2_usuario'] ?? "";
  $contrasena3 = $_POST['contrasena3_usuario'] ?? "";
  $foto = $_FILES['foto_usuario'] ?? "";

  $objetoUsuarios = new usuariosModelo();
  ob_clean();
  if (!empty($_SESSION['cedula'])) {
    switch ($accion) {
      case "listar":
        $resultado = $objetoUsuarios->seleccionarUsuarios();
        $objetoUsuarios->DECORE($resultado);
        exit();
      case "registrar":
        $resultado = $objetoUsuarios->registrarUsuarios(
          $cedula,
          $rol,
          $nombre,
          $apellido,
          $telefono,
          $correo,
          $usuario,
          $contrasena1,
          $contrasena2
        );
        $objetoUsuarios->DECORE($resultado);
        exit();
      case "eliminar":
        $resultado = $objetoUsuarios->eliminarUsuarios($cedula);
        $objetoUsuarios->DECORE($resultado);
        exit();
      case "seleccionarUno":
        $resultado = $objetoUsuarios->seleccionarUsuarios($cedula);
        $objetoUsuarios->DECORE($resultado);
        exit();
      case "actualizar":
        $resultado = $objetoUsuarios->actualizarUsuarios(
          $cedula,
          $correo,
          $telefono,
          $rol,
          $usuario,
          $contrasena1,
          $contrasena2,
          $contrasena3,
        );
        $objetoUsuarios->DECORE($resultado);
        exit();
      case "cerrarSesion":
        $resultado = $objetoUsuarios->cerrarSesionUsuarios();
        $objetoUsuarios->DECORE($resultado);
        exit();
      default:
        $objetoUsuarios->DECORE(["error" => "Acción no reconocida"]);
        exit();
    }
  } elseif (isset($accion)) {
    switch ($accion) {
      case 'iniciarSesion':
        $resultado = $objetoUsuarios->iniciarSesionUsuarios($usuario, $contrasena1);
        $objetoUsuarios->DECORE($resultado);
        exit();
      case 'registrar':
        $resultado = $objetoUsuarios->registrarUsuarios(
          $cedula,
          $rol,
          $nombre,
          $apellido,
          $telefono,
          $correo,
          $usuario,
          $contrasena1,
          $contrasena2
        );
        if ($resultado['icono'] == 'success') {
          $resultado['tipo'] = 'alertarYredireccionar';
          $resultado['url'] = APP_URL;
        }
        $objetoUsuarios->DECORE($resultado);
        exit();
      default:
        json_encode('Accion no reconocida');
        exit();
    }
  } else {
    json_encode('Accion no reconocida');
    exit();
  }
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
  if (isset($url2) && $url2 != "") {
    if (is_file("src/vistas/usuarios/" . $url2 . ".php")) {
      if ($url2 == "dashboard" && isset($_SESSION['rol'])) {
        require_once "src/config/inc/header.php";
        require_once "src/config/inc/sidebar.php";
        require_once "src/vistas/usuarios/" . $url2 . ".php";
      } else {
        require_once "src/vistas/usuarios/" . $url2 . ".php";
      }
      $_SESSION['vistaActual'] = $url2;
    } else {
      require_once "src/vistas/others/404.php";
      $_SESSION['vistaActual'] = '';
    }
  } elseif (isset($_SESSION['cedula'])) {
    //Cuando el usuario ya inicio sesión y va al modulo de usuarios
    require_once "src/config/inc/header.php";
    require_once "src/config/inc/sidebar.php";
    require_once "src/vistas/usuarios/usuarios.php";
    $_SESSION['vistaActual'] = 'usuarios';
  } else {
    require_once "src/vistas/others/404.php";
    $_SESSION['vistaActual'] = '';
  }
}
