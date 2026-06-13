<?php

namespace src\config\inc;

use src\config\connect\conexion;
use src\modelos\accesosModelo;

class componentesModelo extends conexion {
  private accesosModelo $objPermisos;

  public function __construct() {
    $this->objPermisos = new accesosModelo();
  }
  public function sidebar() {
    $sidebarHTML = '
      <li class="liSidebar">
        <a href="' . APP_URL . 'home" class="aSidebar">
          <i class="fi fi-rr-home"></i>
          <span>Inicio</span>
        </a>
      </li>
    ';

    if (!$this->objPermisos->validarPermisos('facturacion', 'ver')) {
      $sidebarHTML .= '
        <li class="liSidebar">
          <a href="' . APP_URL . 'facturacion" class="aSidebar">
            <i class="fi fi-rr-receipt"></i>
            <span>Facturación</span>
          </a>
        </li>
      ';
    };
    if (!$this->objPermisos->validarPermisos('pedidos', 'ver')) {
      $sidebarHTML .= '
        <li class="liSidebar">
          <a href="' . APP_URL . 'pedidos" class="aSidebar">
            <i class="fi fi-rr-order-food-mobile"></i>
            <span>Pedidos</span>
          </a>
        </li>
      ';
    };
    if (!$this->objPermisos->validarPermisos('pagos', 'ver')) {
      $sidebarHTML .= '
        <li class="liSidebar">
          <a href="' . APP_URL . 'pagos" class="aSidebar">
            <i class="fi fi-rr-credit-card"></i>
            <span>Pagos</span>
          </a>
        </li>
      ';
    };
    if (!$this->objPermisos->validarPermisos('inventario', 'ver inventario')) {
      $sidebarHTML .= '
        <li class="liSidebar">
          <a href="' . APP_URL . 'inventario" class="aSidebar">
            <i class="fi fi-rr-supplier-alt"></i>
            <span>Inventario</span>
          </a>
        </li>
      ';
    };
    if (!$this->objPermisos->validarPermisos('compras', 'ver')) {
      $sidebarHTML .= '
        <li class="liSidebar">
          <a href="' . APP_URL . 'compras" class="aSidebar">
            <i class="fi fi-br-shopping-cart-add"></i>
            <span>Compras</span>
          </a>
        </li>
      ';
    };
    if (!$this->objPermisos->validarPermisos('ordenesServicios', 'ver')) {
      $sidebarHTML .= '
        <li class="liSidebar">
          <a href="' . APP_URL . 'ordenesServicios" class="aSidebar">
            <i class="fi fi-rr-ballot"></i>
            <span>Órdenes de Servicios</span>
          </a>
        </li>
      ';
    };
    if (!$this->objPermisos->validarPermisos('producciones', 'ver')) {
      $sidebarHTML .= '
        <li class="liSidebar">
          <a href="' . APP_URL . 'producciones" class="aSidebar">
            <i class="fi fi-rr-hands-bubbles"></i>
            <span>Producciones</span>
          </a>
        </li>
      ';
    };
    if (!$this->objPermisos->validarPermisos('clientes', 'ver')) {
      $sidebarHTML .= '
        <li class="liSidebar">
          <a href="' . APP_URL . 'clientes" class="aSidebar">
            <i class="fi fi-rr-users-medical"></i>
            <span>Clientes</span>
          </a>
        </li>
      ';
    };
    if (!$this->objPermisos->validarPermisos('reportes', 'ver reportes')) {
      $sidebarHTML .= '
        <li class="liSidebar">
          <a href="' . APP_URL . 'reportes" class="aSidebar">
            <i class="fi fi-rr-chart-histogram"></i>
            <span>Reportes</span>
          </a>
        </li>
      ';
    };

    $puedeVerConfig = false;
    $modulosConfig = [
      'bancos' => 'ver',
      'bitacora' => 'ver',
      'empresasEnvios' => 'ver',
      'iva' => 'ver',
      'insumos' => 'ver',
      'materiasPrimas' => 'ver',
      'metodos-pagos' => 'ver',
      'monedas' => ['ver', 'ver historial de cambio'],
      'permisos' => 'ver',
      'presentaciones' => 'ver',
      'productos' => 'ver',
      'proveedores' => 'ver',
      'repartidores' => 'ver',
      'roles' => 'ver',
      'rutas' => 'ver',
      'servicios' => 'ver',
      'unidadesMedida' => 'ver',
      'usuarios' => 'ver',
    ];
    foreach ($modulosConfig as $modulo => $permiso) {
      if (is_array($permiso)) {
        foreach ($permiso as $per) {
          if (!$this->objPermisos->validarPermisos($modulo, $per)) {
            $puedeVerConfig = true;
          };
        }
      } else {
        if (!$this->objPermisos->validarPermisos($modulo, $permiso)) {
          $puedeVerConfig = true;
        };
      }
    };
    if ($puedeVerConfig == true) {
      $lisConfig = '';
      if (!$this->objPermisos->validarPermisos('productos', 'ver')) {
        $lisConfig .= '
          <li class="liSidebar">
            <a href="' . APP_URL . 'productos" class="aSidebar">
              <i class="fi fi-rr-bin-bottles"></i>
              <span>Productos</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('materiasPrimas', 'ver')) {
        $lisConfig .= '
          <li class="liSidebar">
            <a href="' . APP_URL . 'materiasPrimas" class="aSidebar">
              <i class="fi fi-rr-flask"></i>
              <span>Materias Primas</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('servicios', 'ver')) {
        $lisConfig .= '
          <li class="liSidebar">
            <a href="' . APP_URL . 'servicios" class="aSidebar">
              <i class="fi fi-rr-broom"></i>
              <span>Servicios</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('proveedores', 'ver')) {
        $lisConfig .= '
          <li class="liSidebar">
            <a href="' . APP_URL . 'proveedores" class="aSidebar">
              <i class="fi fi-rr-seller"></i>
              <span>Proveedores</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('presentaciones', 'ver')) {
        $lisConfig .= '
          <li class="liSidebar">
            <a href="' . APP_URL . 'presentaciones" class="aSidebar">
              <i class="fi fi-rr-soap"></i>
              <span>Presentaciones</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('cambios', 'ver historial de cambio')) {
        $lisConfig .= '
          <li class="liSidebar">
            <a href="' . APP_URL . 'monedas/cambios-monedas" class="aSidebar">
              <i class="fi fi-rs-money-transfer-coin-arrow"></i>
              <span>Cambio Monetario</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('repartidores', 'ver')) {
        $lisConfig .= '
          <li class="liSidebar">
            <a href="' . APP_URL . 'repartidores" class="aSidebar">
              <i class="fi fi-rr-person-carry-box"></i>
              <span>Repartidores</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('categoriasProductos', 'ver')) {
        $lisConfig .= '
          <li class="liSidebar">
            <a href="' . APP_URL . 'categoriasProductos" class="aSidebar">
              <i class="fi fi-rs-category"></i>
              <span>Categorías</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('unidadesMedidas', 'ver')) {
        $lisConfig .= '
          <li class="liSidebar">
            <a href="' . APP_URL . 'unidadesMedidas" class="aSidebar">
              <i class="fi fi-rr-ruler-horizontal"></i>
              <span>Unidades de Medida</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('metodos-pago', 'ver')) {
        $lisConfig .= '
          <li class="liSidebar">
            <a href="' . APP_URL . 'metodos-pago" class="aSidebar">
              <i class="fi fi-rr-credit-card"></i>
              <span>Métodos de Pago</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('monedas', 'ver')) {
        $lisConfig .= '
          <li class="liSidebar">
            <a href="' . APP_URL . 'monedas" class="aSidebar">
              <i class="fi fi-rr-money"></i>
              <span>Monedas</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('cambiosIva', 'ver historial de cambio del iva')) {
        $lisConfig .= '
          <li class="liSidebar">
            <a href="' . APP_URL . 'cambiosIva" class="aSidebar">
              <i class="fi fi-sr-tax-alt"></i>
              <span>IVA</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('empresasEnvios', 'ver')) {
        $lisConfig .= '
          <li class="liSidebar">
            <a href="' . APP_URL . 'empresasEnvios" class="aSidebar">
              <i class="fi fi-rr-shipping-fast"></i>
              <span>Empresas de Envíos</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('sucursalesEmpresasEnvios', 'ver')) {
        $lisConfig .= '
          <li class="liSidebar">
            <a href="' . APP_URL . 'sucursalesEmpresasEnvios" class="aSidebar">
              <i class="fi fi-rs-map-marker-home"></i>
              <span>Sucursales de Envíos</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('bancos', 'ver')) {
        $lisConfig .= '
          <li class="liSidebar">
            <a href="' . APP_URL . 'bancos" class="aSidebar">
              <i class="fi fi-rr-bank"></i>
              <span>Bancos</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('rutas', 'ver')) {
        $lisConfig .= '
          <li class="liSidebar">
            <a href="' . APP_URL . 'rutas" class="aSidebar">
              <i class="fi fi-br-route"></i>
              <span>Rutas</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('usuarios', 'ver')) {
        $lisConfig .= '
          <li class="liSidebar">
            <a href="' . APP_URL . 'usuarios" class="aSidebar">
              <i class="fi fi-rr-user"></i>
              <span>Usuarios</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('accesos', 'ver')) {
        $lisConfig .= '
          <li class="liSidebar">
            <a href="' . APP_URL . 'accesos" class="aSidebar">
              <i class="fi fi-br-lock"></i>
              <span>accesos</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('roles', 'ver')) {
        $lisConfig .= '
          <li class="liSidebar">
            <a href="' . APP_URL . 'roles" class="aSidebar">
              <i class="fi fi-br-organization-chart"></i>
              <span>Roles</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('modulos', 'ver')) {
        $lisConfig .= '
          <li class="liSidebar">
            <a href="' . APP_URL . 'modulos" class="aSidebar">
              <i class="fi fi-bs-module"></i>
              <span>Módulos</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('permisos', 'ver')) {
        $lisConfig .= '
          <li class="liSidebar">
            <a href="' . APP_URL . 'permisos" class="aSidebar">
              <i class="fi fi-rs-holding-hand-key"></i>
              <span>Permisos</span>
            </a>
          </li>
        ';
      };
      if (!$this->objPermisos->validarPermisos('bitacora', 'ver bitacora')) {
        $lisConfig .= '
          <li class="liSidebar">
            <a href="' . APP_URL . 'bitacora" class="aSidebar">
              <i class="fi fi-rr-file-spreadsheet"></i>
              <span>Bitácora</span>
            </a>
          </li>
        ';
      };
      $sidebarHTML .= '
        <li class="liSidebar">
          <a href="#multiNivelCollapse1" data-bs-toggle="collapse" aria-expanded="false" class="aSubSidebar">
            <div class="d-flex align-items-center w-100">
              <i class="fi fi-rr-settings me-2"></i>
              <span class="text-truncate">Configuraciones</span>
            </div>
            <i class="fi fi-rr-angle-small-down menu-arrow transition-transform text-muted ms-2"></i>
          </a>
          <div class="collapse" id="multiNivelCollapse1">
            <ul class="list-unstyled mt-1 border-start border-light ms-1 ps-1 pe-0">
              ' . $lisConfig . '
            </ul>
          </div>
        </li>
      ';
    }
    $sidebarHTML = '
      <div class="sidebar noselec">
        <div class="iconoNombre d-flex flex-column align-items-center justify-content-center text-center mt-2 px-3 w-100">
          <img src="' . APP_URL . 'src/assets/images/logo2.png" class="navbar-logo rounded m-0" alt="logo">
          <a class="navbar-brand m-0 text-dark fw-bolder fs-4 text-decoration-none w-100" href="#">J.LACRUZ C.A.</a>
        </div>
        <nav>
          <ul class="sidebar-menu list-unstyled px-0">
            ' . $sidebarHTML . '
            <li class="liSidebar">
              <a href="assets/manual_de_usuario.pdf" target="blank" class="aSidebar">
                <i class="fi fi-rr-info"></i>
                <span>Ayuda</span>
              </a>
            </li>
            <li class="sidebar-divider"></li>
            <li class="liSidebar">
              <a class="logout-btn btnCerrarSession" href="#" class="aSidebar">
                <i class="fi fi-rr-sign-out-alt"></i>
                <span>Cerrar sesión</span>
              </a>
            </li>
          </ul>
        </nav>
      </div>
      <div class="sidebarBackdrop"></div>
    ';
    return $sidebarHTML;
  }
  public function listaDataTable($instrucciones) {
    $encabezado = $instrucciones['encabezado'];
    $tituloBtnReg = $instrucciones['tituloBtnReg'] ?? null;
    $boton = !isset($tituloBtnReg) ? '' : '
      <button type="button" class="p-btn" data-bs-toggle="modal" data-bs-target=".modalRegistrar">
          <i class="fas fa-plus-circle"></i> ' . $tituloBtnReg . '
      </button>
    ';
    $listaDataTable = '
      <div class="main-content px-4" id="mainContent">
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
}
