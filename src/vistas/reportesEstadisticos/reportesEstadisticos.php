
<style>
  #mainContent {
    background-color: #f7f9fc;
    min-height: 100vh;
    font-family: 'Inter', 'Segoe UI', sans-serif;
  }
  .dash-card {
    background: #ffffff;
    border-radius: 16px;
    border: none;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    transition: transform 0.2s ease-in-out;
  }
  .dash-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
  }
  .kpi-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #6c757d;
    letter-spacing: 1px;
  }
  .kpi-value {
    font-size: 3rem;
    font-weight: 700;
    margin-top: 10px;
  }
  .color-warning { color: #f39c12; }
  .color-success { color: #2ecc71; }
  .color-purple { color: #6f42c1; }
  .color-info { color: #00d2ff; }

  .dash-btn {
    background-color: #6554C0;
    border-color: #6554C0;
    color: #fff;
    border-radius: 8px;
    padding: 0.5rem 1.2rem;
    font-weight: 500;
  }
  .dash-btn:hover {
    background-color: #5543a0;
    color: #fff;
  }
  .dash-input {
    border-radius: 8px;
    border: 1px solid #e0e0e0;
    background-color: #fff;
  }
  
  .nav-tabs-custom {
    border-bottom: 2px solid #eef2f5;
    margin-bottom: 2rem;
  }
  .nav-tabs-custom .nav-link {
    border: none;
    color: #6c757d;
    font-weight: 600;
    padding: 1rem 1.5rem;
    background: transparent;
  }
  .nav-tabs-custom .nav-link.active {
    color: #2c3e50;
    border-bottom: 3px solid #6554C0;
    background: transparent;
  }

  /* Clases para manejo de estados vacíos */
  .chart-container {
    position: relative;
    height: 100%;
    width: 100%;
  }
  
  .empty-state {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    display: none;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background: rgba(255, 255, 255, 0.9);
    z-index: 10;
  }
  
  .empty-state.active {
    display: flex;
  }
  
  .empty-state i {
    font-size: 3rem;
    color: #dcdcdc;
    margin-bottom: 1rem;
  }
  
  .empty-state p {
    color: #6c757d;
    font-weight: 500;
    margin: 0;
  }

  .btn-export {
    background: transparent;
    border: none;
    font-size: 1.2rem;
    padding: 0;
    cursor: pointer;
    transition: transform 0.2s;
  }
  .btn-export:hover {
    transform: scale(1.1);
  }
</style>

<div class="container-fluid py-2">
  
  <!-- Header Premium -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="m-0 fw-bold" style="color: #1a1a1a;">Reportes Estadísticos</h2>
      <p class="text-muted m-0 mt-1" style="font-size: 0.95rem;">Visión general y estadísticas del sistema</p>
    </div>
      <div class="d-flex align-items-center gap-2">
        <select class="form-select dash-input shadow-sm" id="filtroTiempoDashboard" style="width: auto;">
          <option value="ultimos_30_dias">Últimos 30 días</option>
          <option value="ultimos_3_meses">Últimos 3 meses</option>
          <option value="personalizado">Rango Personalizado</option>
        </select>
        
        <div id="contenedorFechasPersonalizadas" class="d-flex gap-2 d-none">
          <input type="date" class="form-control dash-input shadow-sm" id="fechaInicioDash">
          <span class="align-self-center text-muted">-</span>
          <input type="date" class="form-control dash-input shadow-sm" id="fechaFinDash">
        </div>

        <button class="btn dash-btn shadow-sm" id="btnRecargarDashboard">
          <i class="fi fi-rr-search me-1"></i> Consultar
        </button>
      </div>
    </div>

    <!-- Navegación por pestañas -->
    <ul class="nav nav-tabs-custom" id="dashboardTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="resumen-tab" data-bs-toggle="tab" data-bs-target="#resumen" type="button" role="tab">Resumen General</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="finanzas-tab" data-bs-toggle="tab" data-bs-target="#finanzas" type="button" role="tab">Finanzas</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="ventas-tab" data-bs-toggle="tab" data-bs-target="#ventas" type="button" role="tab">Ventas y Clientes</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="produccion-tab" data-bs-toggle="tab" data-bs-target="#produccion" type="button" role="tab">Producción e Inventario</button>
      </li>
    </ul>

    <!-- Contenido de las pestañas -->
    <div class="tab-content" id="dashboardTabsContent">
      
      <!-- Pestaña 1: Resumen General -->
      <div class="tab-pane fade show active" id="resumen" role="tabpanel">
        <div class="row mb-4">
          <div class="col-md-3">
            <div class="card dash-card h-100">
              <div class="card-body text-center p-4">
                <h6 class="text-uppercase kpi-label">Cuentas Pendientes</h6>
                <div id="metricPendientes" class="kpi-value color-warning">0</div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card dash-card h-100">
              <div class="card-body text-center p-4">
                <h6 class="text-uppercase kpi-label">Cuentas Pagadas</h6>
                <div id="metricPagadas" class="kpi-value color-success">0</div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card dash-card h-100">
              <div class="card-body text-center p-4">
                <h6 class="text-uppercase kpi-label">Facturas de Productos</h6>
                <div id="metricProductos" class="kpi-value color-purple">0</div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card dash-card h-100">
              <div class="card-body text-center p-4">
                <h6 class="text-uppercase kpi-label">Facturas de Servicios</h6>
                <div id="metricServicios" class="kpi-value color-info">0</div>
              </div>
            </div>
          </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6 mb-4">
              <div class="card dash-card h-100">
              <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold m-0 text-dark">Actividad Reciente</h5>
                <a href="#" class="text-decoration-none" style="color: #6554C0; font-size: 0.85rem;">Ver todo</a>
              </div>
              <div class="card-body px-4" id="listaActividadReciente" style="height:350px; overflow-y:auto;">
                <!-- Se llena con JS -->
              </div>
            </div>
            </div>
            <div class="col-md-6 mb-4">
              <div class="card dash-card h-100">
              <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold m-0 text-dark">Top 5 Clientes</h5>
                <button class="btn-export text-primary" title="Exportar Gráfica" data-chart="chartTopClientes">
                  <i class="fi fi-rr-download"></i>
                </button>
              </div>
              <div class="card-body p-4 chart-container" style="height:350px;">
                <div class="empty-state" id="empty-chartTopClientes">
                  <i class="fi fi-rr-box-open"></i>
                  <p>Sin datos en este rango</p>
                </div>
                <canvas id="chartTopClientes"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pestaña 2: Finanzas -->
      <div class="tab-pane fade" id="finanzas" role="tabpanel">
        <div class="row mb-4">
          <div class="col-md-12 mb-4">
            <div class="card dash-card h-100">
              <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold m-0 text-dark">Flujo de Ingresos vs Gastos</h5>
                <button class="btn-export text-primary" title="Exportar Gráfica" data-chart="chartIngresosEgresos">
                  <i class="fi fi-rr-download"></i>
                </button>
              </div>
              <div class="card-body px-4 chart-container" style="height:450px;">
                <div class="empty-state" id="empty-chartIngresosEgresos">
                  <i class="fi fi-rr-box-open"></i>
                  <p>Sin datos en este rango de fechas</p>
                </div>
                <canvas id="chartIngresosEgresos"></canvas>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 mb-4">
            <div class="card dash-card h-100">
              <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold m-0 text-dark">Cuentas por Cobrar</h5>
                <button class="btn-export text-primary" title="Exportar Gráfica" data-chart="chartCuentasCobrar">
                  <i class="fi fi-rr-download"></i>
                </button>
              </div>
              <div class="card-body p-4 chart-container" style="height:350px;">
                <div class="empty-state" id="empty-chartCuentasCobrar">
                  <i class="fi fi-rr-box-open"></i>
                  <p>Sin datos en este rango de fechas</p>
                </div>
                <canvas id="chartCuentasCobrar"></canvas>
              </div>
            </div>
          </div>
          <div class="col-md-6 mb-4">
            <div class="card dash-card h-100">
              <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold m-0 text-dark">Ingresos: Prod. vs Servicios</h5>
                <button class="btn-export text-primary" title="Exportar Gráfica" data-chart="chartProdVsServ">
                  <i class="fi fi-rr-download"></i>
                </button>
              </div>
              <div class="card-body p-4 chart-container" style="height:350px;">
                <div class="empty-state" id="empty-chartProdVsServ">
                  <i class="fi fi-rr-box-open"></i>
                  <p>Sin datos en este rango de fechas</p>
                </div>
                <canvas id="chartProdVsServ"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pestaña 3: Ventas y Clientes -->
      <div class="tab-pane fade" id="ventas" role="tabpanel">
        <div class="row mb-4">
          <div class="col-md-12 mb-4">
            <div class="card dash-card h-100">
              <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold m-0 text-dark">Ventas por Día de la Semana</h5>
                <button class="btn-export text-primary" title="Exportar Gráfica" data-chart="chartVentasPorDia">
                  <i class="fi fi-rr-download"></i>
                </button>
              </div>
              <div class="card-body p-4 chart-container" style="height:350px;">
                <div class="empty-state" id="empty-chartVentasPorDia">
                  <i class="fi fi-rr-box-open"></i>
                  <p>Sin datos en este rango de fechas</p>
                </div>
                <canvas id="chartVentasPorDia"></canvas>
              </div>
            </div>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col-md-6 mb-4">
            <div class="card dash-card h-100">
              <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold m-0 text-dark">Top 5 Productos</h5>
                <button class="btn-export text-primary" title="Exportar Gráfica" data-chart="chartTopProductos">
                  <i class="fi fi-rr-download"></i>
                </button>
              </div>
              <div class="card-body p-4 chart-container" style="height:350px;">
                <div class="empty-state" id="empty-chartTopProductos">
                  <i class="fi fi-rr-box-open"></i>
                  <p>Sin datos en este rango</p>
                </div>
                <canvas id="chartTopProductos"></canvas>
              </div>
            </div>
          </div>
          <div class="col-md-6 mb-4">
            <div class="card dash-card h-100">
              <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold m-0 text-dark">Top 5 Servicios</h5>
                <button class="btn-export text-primary" title="Exportar Gráfica" data-chart="chartTopServicios">
                  <i class="fi fi-rr-download"></i>
                </button>
              </div>
              <div class="card-body p-4 chart-container" style="height:350px;">
                <div class="empty-state" id="empty-chartTopServicios">
                  <i class="fi fi-rr-box-open"></i>
                  <p>Sin datos en este rango</p>
                </div>
                <canvas id="chartTopServicios"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pestaña 4: Producción e Inventario -->
      <div class="tab-pane fade" id="produccion" role="tabpanel">
        <div class="row mb-4">
          <div class="col-md-12 mb-4">
            <div class="card dash-card h-100">
              <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold m-0 text-dark">Historial de Producción Diaria</h5>
                <button class="btn-export text-primary" title="Exportar Gráfica" data-chart="chartProduccion">
                  <i class="fi fi-rr-download"></i>
                </button>
              </div>
              <div class="card-body p-4 chart-container" style="height:350px;">
                <div class="empty-state" id="empty-chartProduccion">
                  <i class="fi fi-rr-box-open"></i>
                  <p>Sin datos en este rango de fechas</p>
                </div>
                <canvas id="chartProduccion"></canvas>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 mb-4">
            <div class="card dash-card h-100">
              <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold m-0 text-dark">Materia Prima más Consumida</h5>
                <button class="btn-export text-primary" title="Exportar Gráfica" data-chart="chartMateriasPrimas">
                  <i class="fi fi-rr-download"></i>
                </button>
              </div>
              <div class="card-body p-4 chart-container" style="height:350px;">
                <div class="empty-state" id="empty-chartMateriasPrimas">
                  <i class="fi fi-rr-box-open"></i>
                  <p>Sin datos en este rango de fechas</p>
                </div>
                <canvas id="chartMateriasPrimas"></canvas>
              </div>
            </div>
          </div>
          <div class="col-md-6 mb-4">
            <div class="card dash-card h-100">
              <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold m-0 text-dark">Top 5 Proveedores</h5>
                <button class="btn-export text-primary" title="Exportar Gráfica" data-chart="chartTopProveedores">
                  <i class="fi fi-rr-download"></i>
                </button>
              </div>
              <div class="card-body p-4 chart-container" style="height:350px;">
                <div class="empty-state" id="empty-chartTopProveedores">
                  <i class="fi fi-rr-box-open"></i>
                  <p>Sin datos en este rango de fechas</p>
                </div>
                <canvas id="chartTopProveedores"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
