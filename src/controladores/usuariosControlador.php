<?php

use src\modelos\usuariosModelo;
use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $accion = $_POST["accion"];

  $objetoUsuarios = new usuariosModelo();
  $resultado = [
    'icono' => 'error',
    'titulo' => 'accion no reconocida'
  ];
  ob_clean();
  if (!empty($_SESSION['cedula'])) {
    switch ($accion) {
      case "listar":
        $resultado = $objetoUsuarios->seleccionarUsuarios($_POST);
        break;
      case "registrar":
        $resultado = $objetoUsuarios->registrarUsuarios($_POST);
        break;
      case "eliminar":
        $resultado = $objetoUsuarios->eliminarUsuarios($_POST);
        break;
      case "seleccionarUno":
        $resultado = $objetoUsuarios->seleccionarUsuarios($_POST);
        break;
      case "actualizar":
        $resultado = $objetoUsuarios->actualizarUsuarios($_POST);
        break;
      case "cerrarSesion":
        $resultado = $objetoUsuarios->cerrarSesionUsuarios();
        break;
      case "actualizarFoto":
        $resultado = $objetoUsuarios->actualizarFotosUsuarios($_POST);
        break;
      case "eliminarFoto":
        $resultado = $objetoUsuarios->eliminarFotosUsuarios($_POST);
        break;
    }
  } elseif (isset($accion)) {
    switch ($accion) {
      case 'iniciarSesion':
        $resultado = $objetoUsuarios->iniciarSesionUsuarios($_POST);
        break;
      case 'registrar':
        $resultado = $objetoUsuarios->registrarUsuarios($_POST);
        if (($resultado['icono']) == 'success') {
          $resultado['tipo'] = 'alertarYredireccionar';
          $resultado['url'] = APP_URL;
        }
        break;
    }
  }
  $objetoUsuarios->DECORE($resultado);
  exit();
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
  if (isset($url2) && $url2 != "") {
    if (is_file("src/vistas/usuarios/" . $url2 . ".php")) {
      if ($url2 == "dashboard" && isset($_SESSION['rol'])) {
        $objComponentes = new componentesModelo();
        require_once "src/config/inc/header.php";
        echo $objComponentes->sidebar();
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
    $objComponentes = new componentesModelo();
    require_once "src/config/inc/header.php";
    echo $objComponentes->sidebar();
    require_once "src/vistas/usuarios/usuarios.php";
    $_SESSION['vistaActual'] = 'usuarios';
  } else {
    require_once "src/vistas/others/404.php";
    $_SESSION['vistaActual'] = '';
  }
}
