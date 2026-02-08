<div class="sidebar noselec" id="sidebar">
    <nav>
        <ul class="sidebar-menu">
            <li>
                <a href="<?php echo APP_URL ?>" title="Inicio">
                    <i class="fi fi-rr-home"></i>
                    <span>Inicio</span>
                </a>
            </li>
            <li>
                <a href="<?php echo APP_URL ?>ventas" title="Ventas">
                    <i class="fi fi-rr-sell"></i>
                    <span>Ventas</span>
                </a>
            </li>
            <li>
                <a href="<?php echo APP_URL ?>compras">
                    <i class="fi fi-rr-user"></i>
                    <span>Compras</span>
                </a>
            </li>
            <li>
                <a href="<?php echo APP_URL ?>clientes" title="Clientes">
                    <i class="fi fi-rr-users-medical"></i>
                    <span>Clientes</span>
                </a>
            </li>
            <li class="nav-item dropdown">
                <a href="<?php echo APP_URL ?>reportes" title="Reportes">
                    <i class="fi fi-rr-chart-histogram"></i>
                    <span>Reportes</span>
                </a>
            </li>
            <li class="subMenuSidebar" data-bs-toggle="collapse" data-bs-target="#subMenuConfiguraciones">
                <div class="d-flex">
                    <i class="fi fi-rr-settings"></i>
                    <span>Configuraciones</span>
                </div>
            </li>
            <div class="collapse bloqueSubMenu" id="subMenuConfiguraciones">
                <ul class="btn-toggle-nav list-unstyled ">
                    <li>
                        <a href="<?php echo APP_URL ?>usuarios">
                            <i class="fi fi-rr-user"></i>
                            <span>Usuarios</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo APP_URL ?>proveedores" title="Proveedores">
                            <i class="fi fi-rr-seller"></i>
                            <span>Proveedores</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo APP_URL ?>roles">
                            <i class="fi fi-br-organization-chart"></i>
                            <span>Roles</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo APP_URL ?>cambiosIva">
                            <i class="fi fi-sr-tax-alt"></i>
                            <span>IVA</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo APP_URL ?>monedas/cambios-monedas">
                            <i class="fi fi-rs-money-transfer-coin-arrow"></i>
                            <span>Cambio Monetario</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo APP_URL ?>metodos-pago">
                            <i class="fi fi-rr-credit-card"></i>
                            <span>Métodos de Pago</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo APP_URL ?>presentaciones">
                            <i class="fi fi-rr-soap"></i>
                            <span>Presentaciones</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo APP_URL ?>permisos">
                            <i class="fi fi-rr-user-key"></i>
                            <span>Permisos</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo APP_URL ?>unidadesMedidas">
                            <i class="fi fi-rr-ruler-horizontal"></i>
                            <span>Unidades de Medida</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo APP_URL ?>monedas">
                            <i class="fi fi-rr-money"></i>
                            <span>Monedas</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo APP_URL ?>servicios" title="Servicios">
                            <i class="fi fi-rr-box-open"></i>
                            <span>Servicios</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo APP_URL ?>productos" title="Productos">
                            <i class="fi fi-rr-box-open"></i>
                            <span>Productos</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo APP_URL ?>materiasPrimas" title="Materia Prima">
                            <i class="fi fi-rr-flask"></i>
                            <span>Materias Primas</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo APP_URL ?>insumos" title="Insumos">
                            <i class="fi fi-rr-flask"></i>
                            <span>Insumos</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo APP_URL ?>bitacora" title="Bitácora">
                            <i class="fi fi-rr-flask"></i>
                            <span>Bitácora</span>
                        </a>
                    </li>
                </ul>
            </div>
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
        </ul>
    </nav>
</div>