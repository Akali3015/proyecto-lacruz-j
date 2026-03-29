<?php

namespace src\config\inc;

use src\config\connect\conexion;
use src\modelos\permisosModelo;

class componentesModelo extends conexion
{
  private $objPermisos;

  public function __construct()
  {
    $this->objPermisos = new permisosModelo();
  }
  public function listaDataTable($instrucciones)
  {
    $encabezado = $instrucciones['encabezado'];
    $tituloBtnReg = $instrucciones['tituloBtnReg'] ?? null;
    $boton = !isset($tituloBtnReg) ? '' : '
        <button type="button" class="p-btn" data-bs-toggle="modal" data-bs-target=".modalRegistrar">
            <i class="fas fa-plus-circle"></i> ' . $tituloBtnReg . '
        </button>';
    $listaDataTable = '
            <div class="main-content" id="mainContent">
                <dvi class="container-fluid py-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h2 class="mb-0">' . $encabezado . '</h2>
                        </div>
                        <div class="col-md-6 text-end">
                            ' . $boton . '
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover tabla-ajax">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        ';
    return $listaDataTable;
  }
  public function sidebar()
  {
    $sidebarHTML = '
      <li>
        <a href="' . APP_URL . 'home" title="Inicio">
          <i class="fi fi-rr-home"></i>
          <span>Inicio</span>
        </a>
      </li>
    ';
    if (!$this->objPermisos->validarPermisos('dashboard', 'ver dashboard')) {
      $sidebarHTML .= '
        <li>
          <a href="' . APP_URL . 'dashboard" title="Dashboard">
            <i class="fi fi-rr-chart-pie-alt"></i>
            <span>Dashboard</span>
          </a>
        </li>
      ';
    };
    if (!$this->objPermisos->validarPermisos('producciones', 'ver')) {
      $sidebarHTML .= '
        <li>
          <a href="' . APP_URL . 'producciones" title="Producciones">
            <i class="fi fi-rr-hands-bubbles"></i>
            <span>Producciones</span>
          </a>
        </li>
      ';
    };
    if (!$this->objPermisos->validarPermisos('ventas', 'ver')) {
      $sidebarHTML .= '
        <li>
          <a href="' . APP_URL . 'ventas" title="Ventas">
            <i class="fi fi-rr-receipt"></i>
            <span>Ventas</span>
          </a>
        </li>
      ';
    };
    if (!$this->objPermisos->validarPermisos('compras', 'ver')) {
      $sidebarHTML .= '
        <li>
          <a href="' . APP_URL . 'compras">
            <i class="fi fi-br-shopping-cart-add"></i>
            <span>Compras</span>
          </a>
        </li>
      ';
    };
    if (!$this->objPermisos->validarPermisos('clientes', 'ver')) {
      $sidebarHTML .= '
        <li>
          <a href="' . APP_URL . 'clientes" title="Clientes">
            <i class="fi fi-rr-users-medical"></i>
            <span>Clientes</span>
          </a>
        </li>
      ';
    };
    if (!$this->objPermisos->validarPermisos('reportes', 'ver reportes')) {
      $sidebarHTML .= '
        <li class="nav-item dropdown">
          <a href="' . APP_URL . 'reportes" title="Reportes">
            <i class="fi fi-rr-chart-histogram"></i>
            <span>Reportes</span>
          </a>
        </li>
      ';
    };
    $puedeVerConfig = false;
    $modulosConfig = [
      'usuarios' => 'ver',
      'proveedores' => 'ver',
      'roles' => 'ver',
      'iva' => 'ver',
      'monedas' => 'ver',
      'monedas' => 'ver historial de cambio',
      'metodos-pagos' => 'ver',
      'presentaciones' => 'ver',
      'permisos' => 'ver',
      'unidadesMedida' => 'ver',
      'servicios' => 'ver',
      'productos' => 'ver',
      'materiasPrimas' => 'ver',
      'insumos' => 'ver',
      'bitacora' => 'ver'
    ];
    foreach ($modulosConfig as $modulo => $permiso) {
      if (!$this->objPermisos->validarPermisos($modulo, $permiso)) {
        $puedeVerConfig = true;
      };
    };
    if ($puedeVerConfig == true) {
      $lisConfig = '';

      if (!$this->objPermisos->validarPermisos('usuarios', 'ver')) {
        $lisConfig .= '
          <li>
            <a href="'.APP_URL.'usuarios">
              <i class="fi fi-rr-user"></i>
              <span>Usuarios</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('proveedores', 'ver')) {
        $lisConfig .= '
          <li>
            <a href="' . APP_URL . 'proveedores" title="Proveedores">
              <i class="fi fi-rr-seller"></i>
              <span>Proveedores</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('roles', 'ver')) {
        $lisConfig .= '
          <li>
            <a href="' . APP_URL . 'roles">
              <i class="fi fi-br-organization-chart"></i>
              <span>Roles</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('cambiosIva', 'ver historial de cambio del iva')) {
        $lisConfig .= '
          <li>
            <a href="' . APP_URL . 'cambiosIva">
              <i class="fi fi-sr-tax-alt"></i>
              <span>IVA</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('cambios', 'ver historial de cambio')) {
        $lisConfig .= '
          <li>
            <a href="' . APP_URL . 'monedas/cambios-monedas">
              <i class="fi fi-rs-money-transfer-coin-arrow"></i>
              <span>Cambio Monetario</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('metodos-pago', 'ver')) {
        $lisConfig .= '
          <li>
            <a href="' . APP_URL . 'metodos-pago">
              <i class="fi fi-rr-credit-card"></i>
              <span>Métodos de Pago</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('presentaciones', 'ver')) {
        $lisConfig .= '
          <li>
            <a href="' . APP_URL . 'presentaciones">
              <i class="fi fi-rr-soap"></i>
              <span>Presentaciones</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('permisos', 'ver')) {
        $lisConfig .= '
          <li>
            <a href="' . APP_URL . 'permisos">
              <i class="fi fi-rr-user-key"></i>
              <span>Permisos</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('unidadesMedidas', 'ver')) {
        $lisConfig .= '
          <li>
            <a href="' . APP_URL . 'unidadesMedidas">
              <i class="fi fi-rr-ruler-horizontal"></i>
              <span>Unidades de Medida</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('monedas', 'ver')) {
        $lisConfig .= '
          <li>
            <a href="' . APP_URL . 'monedas">
              <i class="fi fi-rr-money"></i>
              <span>Monedas</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('servicios', 'ver')) {
        $lisConfig .= '
          <li>
            <a href="' . APP_URL . 'servicios" title="Servicios">
              <i class="fi fi-rr-broom"></i>
              <span>Servicios</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('productos', 'ver')) {
        $lisConfig .= '
          <li>
            <a href="' . APP_URL . 'productos" title="Productos">
              <i class="fi fi-rr-bin-bottles"></i>
              <span>Productos</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('materiasPrimas', 'ver')) {
        $lisConfig .= '
          <li>
            <a href="' . APP_URL . 'materiasPrimas" title="Materia Prima">
              <i class="fi fi-rr-flask"></i>
              <span>Materias Primas</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('insumos', 'ver')) {
        $lisConfig .= '
          <li>
            <a href="' . APP_URL . 'insumos" title="Insumos">
              <i class="fi fi-rr-jug-bottle"></i>
              <span>Insumos</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('bitacora', 'ver bitacora')) {
        $lisConfig .= '
          <li>
            <a href="' . APP_URL . 'bitacora" title="Bitácora">
              <i class="fi fi-rr-file-spreadsheet"></i>
              <span>Bitácora</span>
            </a>
          </li>
        ';
      };
      $sidebarHTML .= '
        <li class="subMenuSidebar" data-bs-toggle="collapse" data-bs-target="#subMenuConfiguraciones">
          <div class="d-flex">
            <i class="fi fi-rr-settings"></i>
            <span>Configuraciones</span>
          </div>
        </li>
        <div class="collapse bloqueSubMenu" id="subMenuConfiguraciones">
          <ul class="btn-toggle-nav list-unstyled ">
            '.$lisConfig.'
          </ul>
        </div>
      ';
    }
    $sidebarHTML .= '
      <li>
        <a href="assets/manual_de_usuario.pdf" title="Ayuda" target="blank">
          <i class="fi fi-rr-info"></i>
          <span>Ayuda</span>
        </a>
      </li>
      <li class="sidebar-divider"></li>
      <li>
        <a class="logout-btn btnCerrarSession" href="#">
          <i class="fi fi-rr-sign-out-alt"></i>
          <span>Cerrar sesión</span>
        </a>
      </li>
    ';
    $sidebarHTML2 = '
      <div class="sidebar noselec" id="sidebar">
        <nav>
          <ul class="sidebar-menu">
            '.$sidebarHTML.'
          </ul>
        </nav>
      </div>
    ';

    return $sidebarHTML2;
  }
}
