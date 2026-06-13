<input type="hidden" class="nombreVista" value="inventario">

<div class="main-content px-4" id="mainContent">
  <div class="container-fluid py-4">

    <div class="row mb-4">
      <div class="col-md-6">
        <h2 class="mb-0">Gestión de Inventario</h2>
        <p class="text-muted">Consulta el stock de productos y materias primas</p>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="row g-4">
          <div class="col-12">
            <ul class="nav nav-tabs profile-tabs" id="inventarioTabs" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="tab-productos" data-bs-toggle="tab" href="#pane-productos" role="tab">
                  <i class="fi fi-rs-boxes me-2"></i> Productos
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="tab-materiasPrimas" data-bs-toggle="tab" href="#pane-materiasPrimas" role="tab">
                  <i class="fi fi-rs-flask me-2"></i> Materias Primas
                </a>
              </li>
            </ul>
          </div>

          <div class="col-12">
            <div class="tab-content mt-2" id="inventarioTabContent">

              <!-- Productos -->
              <div class="tab-pane fade show active" id="pane-productos" role="tabpanel">
                <div class="row mb-3">
                  <div class="col-md-12">
                    <div class="card border-0 shadow-sm">
                      <div class="card-body py-2">
                        <div class="row align-items-center">
                          <div class="col-md-8">
                            <h6 class="mb-0">Stock Critico de Productos</h6>
                          </div>
                          <div class="col-md-4 text-end">
                            <small class="text-muted">Productos por debajo del stock minimo</small>
                          </div>
                        </div>
                        <div style="height: 200px;">
                          <canvas id="graficaStockProductos"></canvas>
                          <div id="sinDatosGraficaProductos" class="text-center py-5" style="display:none;">
                            <i class="fi fi-rs-check-circle fs-1 text-success mb-3"></i>
                            <h6 class="text-success">¡Todo en orden!</h6>
                            <small class="text-muted">No hay Productos con stock critico</small>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="table-responsive">
                  <table id="tablaProductos" class="tabla-ajax table table-striped table-bordered text-center">
                  </table>
                </div>
              </div>

              <!-- Materias Primas -->
              <div class="tab-pane fade" id="pane-materiasPrimas" role="tabpanel">
                <div class="row mb-3">
                  <div class="col-md-12">
                    <div class="card border-0 shadow-sm">
                      <div class="card-body py-2">
                        <div class="row align-items-center">
                          <div class="col-md-8">
                            <h6 class="mb-0">Stock Critico de Materias Primas</h6>
                          </div>
                          <div class="col-md-4 text-end">
                            <small class="text-muted">Materias Primas por debajo del stock minimo</small>
                          </div>
                        </div>
                        <div style="height: 200px;">
                          <canvas id="graficaStockMateriasPrimas"></canvas>
                          <div id="sinDatosGraficaMateriasPrimas" class="text-center py-5" style="display:none;">
                            <i class="fi fi-rs-check-circle fs-1 text-success mb-3"></i>
                            <h6 class="text-success">¡Todo en orden!</h6>
                            <small class="text-muted">No hay Materias Primas con stock crítico</small>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="table-responsive">
                  <table id="tablaMateriasPrimas" class="tabla-ajax table table-striped table-bordered text-center">
                  </table>
                </div>
              </div>

            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal para registrar movimientos de productos -->
<div class="modal fade modalRegistrarAnomaliaProducto" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
        <h5 class="modal-title">
          <i class="fas fa-user-plus me-2"></i>Registrar Movimiento Anómalo - <span class="textoTipoItemModal">Producto</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form class="formularioAjax validar" method="POST" action="" novalidate>
        <div class="modal-body">
          <div class="row">
            <input type="hidden" name="accion" value="registrarMovimientosProductos">

            <!-- Producto -->
            <div class="col-md-6 mb-3">
              <label class="form-label">Producto</label>
              <input type="text" class="form-control inputNombreProductoAnomalia" readonly>
            </div>

            <!-- Presentacion -->
            <div class="col-md-6 mb-3">
              <label class="form-label">Presentación</label>
              <select class="form-select selectPresentacionAnomalia" name="id_presentacion_producto" pattern="<?php echo regexIdSeguro ?>"
                minlength="<?php echo minRegexIdSeguro ?>"
                maxlength="<?php echo maxRegexIdSeguro ?>"
                required>
                <option value="">Seleccione una presentación</option>
              </select>
            </div>

            <!-- Tipo -->
            <div class="col-md-6 mb-3">
              <label class="form-label">Tipo de Movimiento</label>
              <select class="form-select" name="tipo_movimiento" pattern="<?php echo regexValorBoleano ?>"
                minlength="<?php echo minRegexValorBoleano ?>"
                maxlength="<?php echo maxRegexValorBoleano ?>"
                required>
                <option value="">Seleccione tipo</option>
                <option value="1">Carga</option>
                <option value="0">Descarga</option>
              </select>
            </div>

            <!-- Cantidad -->
            <div class="col-md-6 mb-3">
              <label for="cantidad" class="form-label">Cantidad</label>
              <input type="number" class="form-control" name="cantidad_movimiento"
                pattern="<?php echo regexCantidadItem ?>"
                minlength="<?php echo minRegexCantidadItem ?>"
                maxlength="<?php echo maxRegexCantidadItem ?>"
                required>
            </div>

            <!-- Motivo -->
            <div class="col-md-12 mb-3">
              <label for="motivo" class="form-label">Motivo del Movimiento</label>
              <textarea class="form-control" name="motivo_movimiento" rows="3"
                pattern="<?php echo regexDescripcion ?>"
                minlength="<?php echo minRegexDescripcion ?>"
                maxlength="<?php echo maxRegexDescripcion ?>"
                required
                placeholder="Describa el motivo del movimiento anómalo..."></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
            <i class="fas fa-exclamation-triangle me-2"></i> Registrar Anomalía
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal para Registrar movimientos de materias primas -->
<div class="modal fade modalRegistrarAnomaliaMateriaPrima" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
        <h5 class="modal-title">
          <i class="fas fa-user-plus me-2"></i>Registrar Movimiento Anómalo - Materia Prima
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form class="formularioAjax validar" method="POST" action="" novalidate>
        <div class="modal-body">
          <div class="row">
            <input type="hidden" name="accion" value="registrarMovimientosMateriasPrimas">
            <input type="hidden" name="id_materia_prima" value="" class="inputIdMateriaPrimaAnomalia">

            <!-- Materia Prima -->
            <div class="col-md-12 mb-3">
              <label class="form-label">Materia Prima</label>
              <input type="text" class="form-control inputNombreMateriaPrimaAnomalia" readonly>
            </div>

            <!-- Tipo de Movimiento -->
            <div class="col-md-6 mb-3">
              <label class="form-label">Tipo de Movimiento</label>
              <select class="form-select" name="tipo_movimiento" pattern="<?php echo regexValorBoleano ?>"
                minlength="<?php echo minRegexValorBoleano ?>"
                maxlength="<?php echo maxRegexValorBoleano ?>"
                required>
                <option value="">Seleccione tipo</option>
                <option value="1">Carga</option>
                <option value="0">Descarga</option>
              </select>
            </div>

            <!-- Cantidad -->
            <div class="col-md-6 mb-3">
              <label for="cantidad" class="form-label">Cantidad</label>
              <input type="number" class="form-control cantidad" name="cantidad_movimiento"
                pattern="<?php echo regexCantidadItem ?>"
                minlength="<?php echo minRegexCantidadItem ?>"
                maxlength="<?php echo maxRegexCantidadItem ?>"
                required>
            </div>

            <!-- Motivo -->
            <div class="col-md-12 mb-3">
              <label for="motivo" class="form-label">Motivo del Movimiento</label>
              <textarea class="form-control" name="motivo_movimiento" rows="3"
                pattern="<?php echo regexDescripcion ?>"
                minlength="<?php echo minRegexDescripcion ?>"
                maxlength="<?php echo maxRegexDescripcion ?>"
                required
                placeholder="Describa el motivo del movimiento anomalo..."></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
            <i class="fas fa-exclamation-triangle me-2"></i> Registrar Anomalía
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- modal de movimientos de productos -->
<div class="modal fade modalVerMovimientosProducto" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content border-0">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
        <h5 class="modal-title">
          Historial de Movimientos - Producto
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row mb-3">
          <div class="col-md-12">
            <div class="d-flex align-items-center">
              <div>
                <h6 class="mb-1 textoNombreItemMovimientoProducto" style="font-weight: 600;"></h6>
                <small class="text-muted textoIdItemMovimientoProducto"></small>
              </div>
            </div>
          </div>
        </div>

        <div class="table-responsive">
          <table id="tablaMovimientosProducto" class="table table-striped table-bordered text-center" style="width:100%">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Presentación</th>
                <th>Tipo</th>
                <th>Cantidad</th>
                <th>Motivo</th>
                <th>Fecha</th>
              </tr>
            </thead>
            <tbody id="tbodyMovimientosProducto">
            </tbody>
          </table>
        </div>
        <div class="text-center py-4 sinMovimientosProducto" style="display:none;">
          <i class="fi fi-rr-info fs-1 text-muted mb-3"></i>
          <h5 class="text-muted">Sin movimientos registrados</h5>
          <p class="text-muted">No se encontraron movimientos anómalos para este registro</p>
        </div>
      </div>
      <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-2"></i> Cerrar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- modal de movimientos para materias primas -->
<div class="modal fade modalVerMovimientosMateriaPrima" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content border-0">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
        <h5 class="modal-title">
          Historial de Movimientos - Materia Prima
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row mb-3">
          <div class="col-md-12">
            <div class="d-flex align-items-center">
              <div>
                <h6 class="mb-1 textoNombreMateriaPrimaMovimiento" style="font-weight: 600;"></h6>
                <small class="text-muted textoIdMateriaPrimaMovimiento"></small>
              </div>
            </div>
          </div>
        </div>

        <div class="table-responsive">
          <table id="tablaMovimientosMateriaPrima" class="table table-striped table-bordered text-center" style="width:100%">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Materia Prima</th>
                <th>Tipo</th>
                <th>Cantidad</th>
                <th>Motivo</th>
                <th>Fecha</th>
              </tr>
            </thead>
            <tbody id="tbodyMovimientosMateriaPrima">
            </tbody>
          </table>
        </div>
        <div class="text-center py-4 sinMovimientosMateriaPrima" style="display:none;">
          <i class="fi fi-rr-info fs-1 text-muted mb-3"></i>
          <h5 class="text-muted">Sin movimientos registrados</h5>
          <p class="text-muted">No se encontraron movimientos anómalos para este registro</p>
        </div>
      </div>
      <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-2"></i> Cerrar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal para reportes -->
<div class="modal fade modalFiltrosReporte" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content border-0">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
        <h5 class="modal-title">
          Generar Reporte - <span class="textoTipoReporte">Productos</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formFiltrosReporte" class="validar">
          <input type="hidden" name="tipo_reporte" class="inputTipoReporte" value="">
          <input type="hidden" name="id_item" class="inputIdItemReporte" value="">

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Fecha Desde</label>
              <input type="date" class="form-control" name="fecha_desde" required>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Fecha Hasta</label>
              <input type="date" class="form-control" name="fecha_hasta" required>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-2"></i> Cancelar
        </button>
        <button type="button" class="btn btn-primary btnGenerarReporte" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
          Generar Reporte
        </button>
      </div>
    </div>
  </div>
</div>