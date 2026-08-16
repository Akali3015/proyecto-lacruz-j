<input type="hidden" class="nombreVista" value="reportes">

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

<div class="main-content px-4" id="mainContent">
  <div class="container-fluid py-4">
    <!-- TOP LEVEL TABS -->
    <ul class="nav nav-pills mb-4" id="mainReportesTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="doc-reports-tab" data-bs-toggle="pill" data-bs-target="#doc-reports" type="button" role="tab">Reportes Generales</button>
      </li>
      <?php
      use src\modelos\accesosModelo;
      $objAcceso = new accesosModelo();
      if (!$objAcceso->validarPermisos('reportesEstadisticos', 'ver reportes estadísticos')):
      ?>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="stats-reports-tab" data-bs-toggle="pill" data-bs-target="#stats-reports" type="button" role="tab">Reportes Estadísticos</button>
      </li>
      <?php endif; ?>
    </ul>

    <div class="tab-content" id="mainReportesTabsContent">
      <!-- TAB 1: REPORTES DOCUMENTALES -->
      <div class="tab-pane fade show active" id="doc-reports" role="tabpanel">
        <div class="container-fluid py-2">
          <div class="row">
            <div class="col-12">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="card rounded-lg text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;" style="width: 150px">
                  <i class="fas fa-file-pdf me-2"></i>
                  <div class="row mt-3">
                    <div class="col-12">
                      <h2 class="">Reportes</h2>
                    </div>
                  </div>
                </div>
                <div class="text-muted">
                  <?php echo date('d/m/Y'); ?>
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-5">
            <div class="col-6">
              <div class="card border-0 shadow-sm">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;" style="width: 150px">
                  <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Reportes de Ventas
                  </h5>
                </div>
                <div class="card-body">
                  <form class="formular" id="formReporteVentas">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="form-group mb-3">
                          <label for="tipo_producto_ventas">Tipo de Item</label>
                          <select class="form-control" id="tipo_producto_ventas" name="tipo_producto">
                            <option value="todos">Todos los Items</option>
                            <option value="productos">Solo Productos</option>
                            <option value="servicios">Solo Servicios</option>
                            <option value="especifico">Item Específico</option>
                          </select>
                        </div>
                      </div>

                      <div class="col-md-12" id="div_item_especifico_ventas" style="display: none;">
                        <div class="form-group mb-3">
                          <label for="filtro_items_ventas">Filtrar Items</label>
                          <select class="form-control mb-2" id="filtro_items_ventas">
                            <option value="todos">Todos los tipos</option>
                            <option value="productos">Solo Productos</option>
                            <option value="servicios">Solo Servicios</option>
                          </select>

                          <label for="id_item_ventas">Seleccionar Item</label>
                          <select class="form-control" id="id_item_ventas" name="id_item">
                            <option value="">Cargando items...</option>
                          </select>
                        </div>
                      </div>

                      <div class="col-md-12">
                        <label for="tipo_producto_ventas">Intervalo de tiempo</label>
                        <div class="input-daterange" id="Datepicker0">
                          <div class="input-group">
                            <input type="text" name="fecha_desde" class="form-control text-start" placeholder="DESDE EL...">
                            <span class="input-group-text"> - </span>
                            <input type="text" name="fecha_hasta" class="form-control text-start" placeholder="HASTA EL...">
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row mt-3">
                      <div class="col-12">
                        <button type="submit" class="btn btn-primary w-100" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                          <i class="fas fa-file-pdf me-2"></i>
                          Generar Reporte de Ventas
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <div class="col-6">
              <div class="card border-0 shadow-sm">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;" style="width: 150px">
                  <h5 class="card-title mb-0">
                    <i class="fas fa-shopping-cart me-2"></i>
                    Reportes de Compras
                  </h5>
                </div>
                <div class="card-body">
                  <form class="formulario" id="formReporteCompras">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="form-group mb-3">
                          <label for="tipo_materia">Tipo de Item</label>
                          <select class="form-control" id="tipo_materia" name="tipo_item">
                            <option value="todos">Todos</option>
                            <option value="especifico">Materia Prima Específica</option>
                          </select>
                        </div>
                      </div>

                      <div class="col-md-12" id="div_materia_especifica" style="display: none;">
                        <div class="form-group mb-3">
                          <label for="id_materia">Seleccionar Materia Prima</label>
                          <select class="form-control" id="id_materia" name="id_materia">
                            <option value="">Seleccione una materia prima</option>
                          </select>
                        </div>
                      </div>

                      <div class="col-md-12">
                        <label for="tipo_materia">Intervalo de tiempo</label>
                        <div class="input-daterange" id="Datepicker1">
                          <div class="input-group">
                            <input type="text" name="fecha_desde" class="form-control text-start" placeholder="DESDE EL...">
                            <span class="input-group-text"> - </span>
                            <input type="text" name="fecha_hasta" class="form-control text-start" placeholder="HASTA EL...">
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row mt-3">
                      <div class="col-12">
                        <button type="submit" class="btn btn-primary w-100" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                          <i class="fas fa-file-pdf me-2"></i>
                          Generar Reporte de Compras
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-5">
            <div class="col-3">
              <div class="card border-0 shadow-sm">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;" style="width: 150px">
                  <h5 class="card-title mb-0">
                    <i class="fas fa-filter me-2"></i>
                    Cierre de Caja
                  </h5>
                </div>
                <div class="card-body">
                  <form class="formularioAjax" method="POST">
                    <input type="hidden" name="reporte" value="reporteCierre">
                    <div class="row">
                      <div class="form-group col-lg-12">
                        <div class="input-daterange" id="Datepicker2">
                          <label class="form-label">Seleccionar Fecha</label>
                          <div class="input-group">
                            <input type="text" name="fecha_cierre" class="form-control text-start" placeholder="Fecha Cierre">
                            <span class="input-group-text"> - </span>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="row mt-3">
                      <div class="col-12">
                        <button type="submit" class="btn btn-primary w-100" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                          <i class="fas fa-file-pdf me-2"></i>
                          Generar Cierre de Caja
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-4">
              <div class="card h-100 shadow-sm border-0">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;" style="width: 150px">
                  <h5 class="card-title mb-0">
                    <i class="fas fa-concierge-bell me-2"></i>
                    Reporte de Servicios
                  </h5>
                </div>
                <div class="card-body">
                  <p class="card-text">
                    Genera un listado completo de todos los servicios disponibles en el sistema.
                  </p>
                </div>
                <div class="card-footer bg-transparent">
                  <form class="formularioAjax" id="formReporteServicios" method="POST" target="_blank">
                    <input type="hidden" name="reporte" value="reporteServicios">
                    <div class="row mt-3">
                      <div class="col-12">
                        <button type="submit" class="btn btn-primary w-100" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                          <i class="fas fa-file-pdf me-2"></i>
                          Generar Reporte de Servicios
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-4">
              <div class="card h-100 shadow-sm border-0">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;" style="width: 150px">
                  <h5 class="card-title mb-0">
                    <i class="fas fa-box me-2"></i>
                    Inventario de Productos
                  </h5>
                </div>
                <div class="card-body">
                  <p class="card-text">
                    Genera un listado completo de todos los productos disponibles en el sistema.
                  </p>
                </div>
                <div class="card-footer bg-transparent">
                  <form class="formularioAjax" id="formReporteProductos" method="POST" target="_blank">
                    <input type="hidden" name="reporte" value="reporteProductos">
                    <div class="row mt-3">
                      <div class="col-12">
                        <button type="submit" class="btn btn-primary w-100" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                          <i class="fas fa-file-pdf me-2"></i>
                          Generar Reporte de Productos
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-4">
              <div class="card h-100 shadow-sm border-0">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;" style="width: 150px">
                  <h5 class="card-title mb-0">
                    <i class="fas fa-flask me-2"></i>
                    Inventario de Materias Primas
                  </h5>
                </div>
                <div class="card-body">
                  <p class="card-text">
                    Genera un listado completo de todas las materias primas disponibles en el sistema.
                  </p>
                </div>
                <div class="card-footer bg-transparent">
                  <form class="formularioAjax" id="formReporteMateriaPrima" method="POST" target="_blank">
                    <input type="hidden" name="reporte" value="reporteMateriaPrima">
                    <div class="row mt-3">
                      <div class="col-12">
                        <button type="submit" class="btn btn-primary w-100" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                          <i class="fas fa-file-pdf me-2"></i>
                          Generar Reporte de Materias Primas
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- TAB 2: REPORTES ESTADISTICOS -->
      <?php if (!$objAcceso->validarPermisos('reportesEstadisticos', 'ver reportes estadísticos')): ?>
      <div class="tab-pane fade" id="stats-reports" role="tabpanel">
        <?php include_once "src/vistas/reportesEstadisticos/reportesEstadisticos.php"; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>