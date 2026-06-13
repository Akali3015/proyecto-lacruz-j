<input type="hidden" class="nombreVista" value="reportes">

<div class="main-content px-4" id="mainContent">
  <div class="container py-4">
    <div class="formbold-main-wrapper">
      <div class="formbold-form-wrapper">
        <form class="formAjax" action="/proyecto-lacruz-j/src/controladores/reportesControlador.php" method="POST" novalidate>
          <input type="hidden" name="reporte" value="reporte_productos">
          <div class="formbold-form-title">
        </form>
      </div>
    </div>
    <div class="container-fluid">
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
              <i class="fas fa-calendar-alt me-2"></i>
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
                <i class="fas fa-filter me-2"></i>
                Reportes de Ventas
              </h5>
            </div>
            <div class="card-body">
              <form class="formularioAjax" method="POST">
                <input type="hidden" name="reporte" value="reporteVentas">
                <div class="row">
                  <div class="col-lg-12 mb-3">
                    <label for="tipo_producto_ventas" class="form-label">Tipo de Item</label>
                    <select class="form-select" id="tipo_producto_ventas" name="tipo_producto" onchange="cargarItemsVentas()">
                      <option value="todos">Todos los Items</option>
                      <option value="productos">Solo Productos</option>
                      <option value="servicios">Solo Servicios</option>
                    </select>
                  </div>
                  <div class="form-group col-lg-12">
                    <div class="input-daterange" id="Datepicker0">
                      <label class="form-label">Intervalo de tiempo</label>
                      <div class="input-group">
                        <input type="text" name="fecha_desde" class="form-control text-start" placeholder="DESDE EL...">
                        <span class="input-group-text"> - </span>
                        <input type="text" name="fecha_hasta" class="form-control text-end" placeholder="HASTA EL...">
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
                <i class="fas fa-filter me-2"></i>
                Reportes de Compras
              </h5>
            </div>
            <div class="card-body">
              <form class="formularioAjax" method="POST">
                <input type="hidden" name="reporte" value="reporteCompras">
                <div class="row">
                  <div class="col-lg-12 mb-3">
                    <label class="form-label">Tipo de Item</label>
                    <select class="form-select" name="materia_prima">
                      <option value="">Todos</option>
                      <option value="">Materias primas</option>
                      <option value="">Productos</option>
                      <option value="">Insumos</option>
                    </select>
                  </div>
                  <div class="form-group col-lg-12">
                    <div class="input-daterange" id="Datepicker1">
                      <label class="form-label">Intervalo de tiempo</label>
                      <div class="input-group">
                        <input type="text" name="fecha_desde" class="form-control text-start" placeholder="DESDE EL...">
                        <span class="input-group-text"> - </span>
                        <input type="text" name="fecha_hasta" class="form-control text-end" placeholder="HASTA EL...">
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
              <form id="formReporteServicios" method="POST" target="_blank">
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
                <i class="fas fa-boxes me-2"></i>
                Inventario de Productos
              </h5>
            </div>
            <div class="card-body">
              <p class="card-text">
                Genera un listado completo de todos los productos disponibles en el sistema.
              </p>
            </div>
            <div class="card-footer bg-transparent">
              <form id="formReporteProductos" method="POST" target="_blank">
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
              <form id="formReporteMateriaPrima" method="POST" target="_blank">
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
</div>