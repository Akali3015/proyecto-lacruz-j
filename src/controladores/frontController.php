<?php

namespace src\controladores;

use src\modelos\permisosModelo;

class frontController
{
  private $url;
  private $vistasEstaticas;
  private $controladores;
  private $archivo;
  private $objPermisos;

  public function __construct()
  {

    $this->objPermisos = new permisosModelo();
    $salidasFueraDeSesion = ['productos'];

    $this->controladores = [
      'bitacora',
      'cambiosIva',
      'clientes',
      'compras',
      'insumos',
      'iva',
      'materiasPrimas',
      'mensajesWS',
      'metodos-pago',
      'monedas',
      'permisos',
      'presentaciones',
      'presupuestos',
      'productos',
      'producciones',
      'proveedores',
      'reportes',
      'recepciones',
      'roles',
      'servicios',
      'unidadesMedidas',
      'usuarios',
      'ventas',
    ];
    $this->vistasEstaticas = [
      'dashboard',
      'home',
      '404'
    ];

    $metodo = $_SERVER["REQUEST_METHOD"];
    $this->transformarCuerpoAPost();
    $accion = $_POST['accion'] ?? $_POST['reporte'] ?? '';
    if (isset($_GET["views"]) && $_GET["views"] != "") {
      $urlCompleta = $this->url = explode("/", $_GET['views']);
      $_SESSION['vistaActual'] = $this->url[0];
      $this->url = $this->url[0];

      if (in_array($this->url, $this->controladores) && isset($_SESSION['cedula'])) {

        if ($metodo == 'POST') {
          $validacion = $this->objPermisos->validarPermisos($this->url, $accion);
          $this->validarTokens();
        } else {
          $validacion = $this->objPermisos->validarPermisos($this->url, 'ver');
        }
        // if (isset($validacion['icono'])) {
        //   $this->redireccionarUsuario();
        //   return;
        // }

        $vistasNoFuSe = [
          'login',
          'home',
          'registrar-usuario',
          'olvidar-contrasena-1',
          'olvidar-contrasena-2'
        ];
        $vista = $urlCompleta[1] ?? $urlCompleta[0];
        if (isset($_SESSION['cedula']) && in_array($vista, $vistasNoFuSe)) {
          $this->redireccionarUsuario();
          return;
        }
        if (is_file("src/controladores/" . $this->url . "Controlador.php")) {
          $this->archivo = "src/controladores/" . $this->url . "Controlador.php";
          $_SESSION['vistaActual'] = $this->url;
        }
      } elseif (in_array($this->url, $this->vistasEstaticas)) {
        $this->archivo = "src\controladores\othersControlador.php";
        $_SESSION['vistaActual'] = $this->url;
      } elseif (in_array($this->url, $salidasFueraDeSesion) && $accion == 'listar') {
        $this->archivo = "src/controladores/" . $this->url . "Controlador.php";
      } elseif (isset($_SESSION['cedula'])) {
        $this->redireccionarUsuario();
        return;
      } elseif (
        $_SESSION['vistaActual'] == 'usuarios' && (
          $accion == 'iniciarSesion' ||
          $accion == 'obtenerUrlOPG' ||
          $accion == 'registrar' ||
          $accion == 'verificarToken' ||
          $accion == 'restaurarContraseña'
        )
      ) {
        $this->archivo = "src/controladores/usuariosControlador.php";
        $_SESSION['vistaActual'] = 'usuarios';
      } else {
        $this->archivo = "src/vistas/usuarios/login.php";
        $_SESSION['vistaActual'] = 'login';
      }
    } else {
      $this->archivo = "src/vistas/usuarios/login.php";
      $_SESSION['vistaActual'] = 'login';
    }
    $this->llamarArchivo();
  }
  private function llamarArchivo()
  {
    if (file_exists($this->archivo)) {
      $urlActual = explode("/", ($_GET['views'] ?? ''));
      $url1 = $urlActual[0] ?? "";
      $url2 = $urlActual[1] ?? "";
      require_once $this->archivo;
    } else {
      $this->redireccionarUsuario();
    }
  }
  private function redireccionarUsuario()
  {
    $urlRedireccion = '';
    if (!empty($_SESSION['cedula'])) {
      $validacion = true;
      if (
        $validacion &&
        !isset($this->objPermisos->validarPermisos('dashboard', 'ver dashboard')['icono'])
      ) {
        $validacion = false;
        $urlRedireccion = 'dashboard';
        $_SESSION['vistaActual'] = 'dashboard';
      };
      if (
        $validacion &&
        !isset($this->objPermisos->validarPermisos('ventas', 'ver')['icono'])
      ) {
        $validacion = false;
        $urlRedireccion = 'ventas/';
        $_SESSION['vistaActual'] = 'ventas';
      }
      if (
        $validacion &&
        !isset($this->objPermisos->validarPermisos('pedidos', 'ver')['icono'])
      ) {
        $validacion = false;
        $urlRedireccion = 'pedidos/';
        $_SESSION['vistaActual'] = 'pedidos';
      }
    } else {
      $urlRedireccion = 'usuarios/login';
      $_SESSION['vistaActual'] = 'usuarios';
    }
    http_response_code(403);
    ob_end_clean();
    header('Location: ' . APP_URL . $urlRedireccion);
    exit();
  }
  private function transformarCuerpoAPost()
  {
    if ($_SERVER["REQUEST_METHOD"] == 'POST') {
      if (isset($_POST['metadatos'])) {
        foreach (json_decode($_POST['metadatos'], true) as $clave => $valor) {
          $_POST[$clave] = $valor;
        }
      } elseif (!empty(file_get_contents('php://input'))) {
        $datos = json_decode(file_get_contents('php://input'), true);
        if (is_array($datos)) {
          foreach ($datos as $clave => $valor) {
            $_POST[$clave] = $valor;
          }
        }
      }
    }
  }
  private function validarTokens()
  {
    $tokenCSRFRecibido = $_SERVER['HTTP_X_TOKEN_CSRF'] ?? '';
    $tokenCSRFSesion = $_SESSION['TOKEN_CSRF'] ?? '';
    if (empty($tokenCSRFSesion) || !hash_equals($tokenCSRFSesion, $tokenCSRFRecibido)) {
      $this->redireccionarUsuario();
      exit;
    }
  }
}
