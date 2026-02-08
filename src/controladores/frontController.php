<?php

namespace src\controladores;

class frontController
{
    private $url;
    private $controladores;
    private $vistasEstaticas;
    private $archivo;

    public function __construct()
    {
        if (isset($_GET["views"]) && $_GET["views"] != "") {

            if (!isset($_SESSION['cedula']) || $_SESSION['cedula'] == "") {
                $this->archivo = "src/controladores/usuariosControlador.php";
            } else {
                $this->url = explode("/", $_GET['views']);
                $this->url = $this->url[0];

                $this->controladores = [
                    'bitacora',
                    'cambiosIva',
                    'clientes',
                    'compras',
                    'config',
                    'cuentasCobrar',
                    'facturas',
                    'insumos',
                    'iva',
                    'login',
                    'materiasPrimas',
                    'metodos-pago',
                    'monedas',
                    'permisos',
                    'presentaciones',
                    'presupuestos',
                    'productos',
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
                if (in_array($this->url, $this->controladores)) {
                    $archivo = "src/controladores/" . $this->url . "Controlador.php";
                    if (is_file($archivo)) {
                        $this->archivo = $archivo;
                        $_SESSION['vistaActual'] = $this->url;
                    }
                }
                if (in_array($this->url, $this->vistasEstaticas)) {
                    $archivo = "src/controladores/othersControlador.php";
                    if (is_file($archivo)) {
                        $this->archivo = $archivo;
                        $_SESSION['vistaActual'] = $this->url;
                    }
                }
            }
            $this->llamarArchivo();
        } elseif ($this->url == "" || $this->url == "home" || $this->url = null) {

            if (isset($_SESSION['cedula'])) {
                require_once "src/config/inc/header.php";
                require_once "src/config/inc/sidebar.php";
                require_once "src/vistas/others/home.php";
                $_SESSION['vistaActual'] = 'home';
            } else {
                require_once "src/vistas/usuarios/login.php";
                $_SESSION['vistaActual'] = 'login';
            }
        }
    }
    private function llamarArchivo()
    {
        if (file_exists($this->archivo)) {
            $urlActual = explode("/", $_GET['views']);
            $url1 =  $urlActual[0] ?? "";
            $url2 =  $urlActual[1] ?? "";
            require_once $this->archivo;
        } elseif (isset($_SESSION['cedula'])) {
            require_once "src/config/inc/header.php";
            require_once "src/config/inc/sidebar.php";
            require_once "src/vistas/others/home.php";
            $_SESSION['vistaActual'] = 'home';
        } else {
            require_once "src/vistas/usuarios/login.php";
            $_SESSION['vistaActual'] = 'login';
        }
    }
}
