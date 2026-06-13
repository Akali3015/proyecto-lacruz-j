<link rel="stylesheet" href="/proyecto-lacruz-j/src/assets/css/facturacion.css">
<input type="hidden" class="nombreVista" value="facturacion">

<?php
use src\config\inc\componentesModelo;
$componente = new componentesModelo();
$instruccionesLista = [
  'encabezado'    => 'Gestionar Facturación',
  'tituloBtnReg'  => 'Nueva Factura',
];
echo $componente->listaDataTable($instruccionesLista);
?>

<!-- ================================================================
     Ventana principal para registrar una nueva factura
     ================================================================ -->
<div class="modal fade modalRegistrar" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg">

      <div class="modal-header text-white fact-header-grad">
        <h5 class="modal-title d-flex align-items-center gap-2">
          <i class="fi fi-rs-file-invoice-dollar"></i>
          <span id="tituloModalFactura">Nueva Factura</span>
        </h5>
        <button type="button" class="btn-close btn-close-white"
          data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form id="formFactura" method="POST" novalidate>
          <input type="hidden" name="accion" value="registrar">

          <!-- SECCIÓN: Datos generales del cliente y la factura -->
          <div class="row g-3 mb-3">

            <!-- Campo para buscar por cédula o RIF del cliente -->
            <div class="col-md-4">
              <label class="form-label fw-semibold">
                Cédula / RIF del Cliente
                <span class="text-danger">*</span>
              </label>
              <input type="text"
                class="form-control"
                id="inputCedulaClienteFact"
                name="rif_cedula_cliente"
                placeholder="Ej: V12345678"
                autocomplete="off"
                maxlength="11"
                required>
            </div>

            <!-- Este campo es de solo lectura y se llena solito con el nombre del cliente -->
            <div class="col-md-4">
              <label class="form-label fw-semibold">Nombre del Cliente</label>
              <input type="text"
                class="form-control"
                id="nombreClienteFact"
                readonly
                placeholder="Se completa automáticamente">
            </div>

            <!-- Fecha actual (la ponemos automáticamente al abrir) -->
            <div class="col-md-2">
              <label class="form-label fw-semibold">Fecha</label>
              <input type="text"
                class="form-control"
                id="fechaFacturaDisplay"
                readonly>
            </div>

            <!-- Total a pagar (se va sumando solo) -->
            <div class="col-md-2">
              <label class="form-label fw-semibold">Total General</label>
              <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="text"
                  class="form-control fw-bold text-success"
                  id="totalGeneralFact"
                  name="total_general"
                  readonly
                  value="0.00">
              </div>
            </div>

          </div>

          <!-- Pestañas para movernos entre productos, servicios y delivery -->
          <ul class="nav nav-tabs" id="tabsFactura" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" data-bs-toggle="tab"
                data-bs-target="#tabProductosFact" type="button">
                <i class="fi fi-rs-box me-1"></i>
                Productos
                <span class="badge bg-primary ms-1"
                  style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;"
                  id="badgeProdFact">0</span>
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" data-bs-toggle="tab"
                data-bs-target="#tabServiciosFact" type="button">
                <i class="fi fi-tr-room-service"></i>
                Servicios
                <span class="badge bg-success ms-1"
                  style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;"
                  id="badgeServFact">0</span>
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" data-bs-toggle="tab"
                data-bs-target="#tabDeliveryFact" type="button">
                <i class="fi fi-rs-truck-side me-1"></i>
                Delivery - Ubicación
                <span class="badge bg-secondary ms-1"
                  style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;"
                  id="badgeDelFact">No</span>
              </button>
            </li>
          </ul>

          <div class="tab-content mt-3 border rounded p-3">

            <!-- Contenido de la pestaña de productos -->
            <div class="tab-pane fade show active"
              id="tabProductosFact" role="tabpanel">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 text-primary">
                  <i class="fi fi-rs-box me-2"></i>Productos
                </h6>
                <button type="button"
                  class="btn text-white btn-sm"
                  style="background: linear-gradient(135deg, #4e54c8, #8f94fb);"
                  id="btnAgregarProductoFact">
                  <i class="fi fi-rs-plus me-1"></i>Agregar Producto
                </button>
              </div>
              <div id="contenedorProductosFact">
                <div class="fact-empty-state">
                  <i class="fi fi-rs-box-open"></i>
                  <p>No hay productos agregados</p>
                </div>
              </div>
              <div class="d-flex justify-content-end mt-2">
                <span class="badge bg-light text-dark border">
                  Subtotal productos:
                  <strong id="subtotalProdFact">$0.00</strong>
                </span>
              </div>
            </div>

            <!-- Contenido de la pestaña de servicios -->
            <div class="tab-pane fade"
              id="tabServiciosFact" role="tabpanel">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 text-primary">
                  <i class="fi fi-tr-room-service"></i>Servicios
                </h6>
                <button type="button"
                  class="btn text-white btn-sm"
                  style="background: linear-gradient(135deg, #4e54c8, #8f94fb);"
                  id="btnAgregarServicioFact">
                  <i class="fi fi-rs-plus me-1"></i>Agregar Servicio
                </button>
              </div>
              <div id="contenedorServiciosFact">
                <div class="fact-empty-state">
                  <i class="fi fi-tr-room-service"></i>
                  <p>No hay servicios agregados</p>
                </div>
              </div>
              <div class="d-flex justify-content-end mt-2">
                <span class="badge bg-light text-dark border">
                  Subtotal servicios:
                  <strong id="subtotalServFact">$0.00</strong>
                </span>
              </div>
            </div>

            <!-- Contenido de la pestaña para solicitar delivery -->
            <div class="tab-pane fade"
              id="tabDeliveryFact" role="tabpanel">
              <div class="mb-3">
                <div class="form-check form-switch">
                  <input class="form-check-input"
                    type="checkbox"
                    id="chkDeliveryFact"
                    name="incluye_delivery">
                  <label class="form-check-label fw-semibold"
                    for="chkDeliveryFact">
                    ¿Incluir Delivery?
                  </label>
                </div>
              </div>

              <div id="seccionDeliveryFact" class="d-none">
                <div class="row g-3">
                  <!-- Contenedor del mapita para elegir la ubicación -->
                  <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <label class="form-label fw-semibold mb-0">
                        <i class="fi fi-rs-marker me-1"></i>Seleccione la ubicación de entrega
                      </label>
                      <button type="button" class="btn btn-outline-primary btn-sm" id="btnMiUbicacionFact">
                        <i class="fi fi-rs-navigation me-1"></i>Mi ubicación
                      </button>
                    </div>
                    <div id="mapaDeliveryFact" class="rounded border" style="height: 350px; z-index: 1;"></div>
                  </div>
                  <!-- Datos que calculamos automáticamente con el mapa -->
                  <div class="col-md-12">
                    <label class="form-label fw-semibold">Dirección detectada</label>
                    <input type="text" class="form-control form-control-sm" id="direccionDeliveryFact" readonly placeholder="Haga clic en el mapa...">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label fw-semibold">Distancia</label>
                    <div class="input-group input-group-sm">
                      <input type="text" class="form-control" id="distanciaDeliveryFact" readonly value="0">
                      <span class="input-group-text">km</span>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label fw-semibold">Ruta asignada</label>
                    <input type="text" class="form-control form-control-sm" id="rutaAsignadaDeliveryFact" readonly placeholder="—">
                    <input type="hidden" id="idRutaDeliveryFact" value="">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label fw-semibold">Costo Delivery</label>
                    <div class="input-group input-group-sm">
                      <span class="input-group-text">$</span>
                      <input type="text" class="form-control" id="costoDeliveryFact" readonly value="0.00">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label fw-semibold">Repartidor (opcional)</label>
                    <input type="text" class="form-control form-control-sm text-uppercase" id="inputCedulaRepartidorFact" placeholder="V12345678" maxlength="15">
                    <div id="feedbackRepartidorFact" class="small mt-2 text-center"></div>
                    <input type="hidden" id="selectRepartidorFact" value="">
                  </div>
                  <!-- Estos campos ocultos guardan las coordenadas para la base de datos -->
                  <input type="hidden" id="latDeliveryFact" value="">
                  <input type="hidden" id="lngDeliveryFact" value="">
                </div>
              </div>
            </div>

          </div><!-- /Fin del contenedor de pestañas -->

          <!-- Área de totales donde mostramos el resumen de la factura -->
          <div class="fact-totales-panel mt-3">
            <div class="fact-total-row">
              <span>Subtotal Productos</span>
              <span id="resumenProdFact">$0.00</span>
            </div>
            <div class="fact-total-row">
              <span>Subtotal Servicios</span>
              <span id="resumenServFact">$0.00</span>
            </div>
            <div class="fact-total-row" id="rowDeliveryResumen"
              style="display:none;">
              <span>Delivery</span>
              <span id="resumenDeliveryFact">$0.00</span>
            </div>
            <div class="fact-total-row fact-total-grande">
              <span>TOTAL GENERAL</span>
              <span id="resumenTotalFact">$0.00</span>
            </div>
          </div>

          <input type="hidden" id="estadoSeleccionadoFactura" name="estado_factura" value="2">

        </form>
      </div><!-- /Fin del cuerpo de la ventana -->

      <div class="modal-footer">
        <button type="button"
          class="btn btn-danger"
          data-bs-dismiss="modal">
          <i class="fi fi-rs-cross me-1"></i>Cancelar
        </button>
        <button type="button"
          class="btn btn-primary px-4"
          style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;"
          id="btnGuardarFactura"
          disabled>
          <i class="fi fi-rs-credit-card me-1"></i>Ir a Pagos / Guardar
        </button>
      </div>

    </div>
  </div>
</div>

<!-- ================================================================
     El modal de productos se arma con Javascript cuando lo necesitamos
     ================================================================ -->

<!-- ================================================================
     Ventana para mirar con calma todos los detalles de una factura
     ================================================================ -->
<div class="modal fade modalDetallesFactura" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg">

      <div class="modal-header fact-header-grad text-white">
        <h5 class="modal-title d-flex align-items-center gap-2">
          <i class="fi fi-rs-eye"></i>
          Detalle de Factura
        </h5>
        <button type="button" class="btn-close btn-close-white"
          data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" id="contenidoDetalleFactura">
        <!-- Todo esto lo llenamos usando Javascript dependiendo de la factura -->
      </div>

      <div class="modal-footer">
        <div id="botonesExtraDetalle" class="me-auto"></div>
        <button type="button"
          class="btn btn-secondary"
          data-bs-dismiss="modal">Cerrar</button>
        <button type="button"
          class="btn btn-danger"
          id="btnAnularFacturaModal">
          <i class="fi fi-rs-ban me-1"></i>Anular Factura
        </button>
      </div>

    </div>
  </div>
</div>

<!-- ================================================================
     Ventana pequeñita para elegir el estado en que quedará la factura
     ================================================================ -->
<div class="modal fade" id="modalEstadosFactura" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header text-white fact-pagos-grad">
        <h6 class="modal-title"><i class="fi fi-rs-settings me-2"></i>Estado de la Factura</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted small mb-3">Seleccione el estado de la factura antes de guardar:</p>
        <div class="d-grid gap-2">
          <button type="button" class="btn btn-outline-success btn-estado-factura text-start" data-estado="1">
            <i class="fi fi-rs-check-circle me-2"></i>1. Procesada y Pagada
          </button>
          <button type="button" class="btn btn-outline-warning btn-estado-factura text-start" data-estado="2">
            <i class="fi fi-rs-time-fast me-2"></i>2. Procesada y sin pagar
          </button>
          <button type="button" class="btn btn-outline-success btn-estado-factura text-start" data-estado="3">
            <i class="fi fi-rs-truck-side me-2"></i>3. Pagada y despachada
          </button>
          <button type="button" class="btn btn-outline-info btn-estado-factura text-start" data-estado="4">
            <i class="fi fi-rs-box-alt me-2"></i>4. Despachada y sin pagar
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal para Seleccionar Producto -->
<div class="modal fade" id="modalSelProdFact" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header text-white fact-purple-blue-grad">
        <h5 class="modal-title">Seleccionar Producto</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-hover table-striped w-100" id="dtSelProdFact">
          <thead>
            <tr>
              <th>Producto</th>
              <th>Precio</th>
              <th>Stock</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <!-- Se llena dinámicamente mediante JS -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal para Seleccionar Servicio -->
<div class="modal fade" id="modalSelServFact" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header text-white fact-purple-blue-grad">
        <h5 class="modal-title">Seleccionar Servicio</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-hover table-striped w-100" id="dtSelServFact">
          <thead>
            <tr>
              <th>Servicio</th>
              <th>Precio</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <!-- Se llena dinámicamente mediante JS -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal para Registrar Nuevo Repartidor -->
<div class="modal fade" id="modalRegistroRepartidorFact" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header text-white fact-repartidor-grad">
        <h5 class="modal-title"><i class="fi fi-rs-motorcycle me-2"></i>Registrar Nuevo Repartidor</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formRegistroRepartidorFact">
          <div class="mb-3">
            <label class="form-label">Cédula</label>
            <input type="text" class="form-control" name="cedula_repartidor" readonly>
            <input type="hidden" name="codigo_rif_cedula_repartidor" value="">
          </div>
          <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" class="form-control text-uppercase" name="nombre_repartidor" id="inputNombreRepartidorReg" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Apellido</label>
            <input type="text" class="form-control text-uppercase" name="apellido_repartidor" id="inputApellidoRepartidorReg" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Teléfono <small class="text-muted">(11 dígitos)</small></label>
            <input type="text" class="form-control" name="telefono_repartidor" id="inputTelefonoRepartidorReg" maxlength="11" required>
            <div id="feedbackTelefonoRepartidorReg" class="mt-2 text-center"></div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn text-white fact-purple-blue-grad" id="btnGuardarRepartidorFact" disabled>Guardar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal para Detalles del Pago -->
<div class="modal fade" id="modalPagosFactura" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header text-white fact-pagos-grad">
        <h5 class="modal-title" id="tituloModalPagosFactura"><i class="fi fi-rs-credit-card me-2"></i>Detalles del Pago</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body bg-light">
        <!-- Totales -->
        <div class="d-flex justify-content-between text-white p-3 rounded mb-4 fact-pagos-grad">
          <div>
            <small class="d-block mb-1"><i class="fi fi-rs-box me-1"></i>TOTAL A PAGAR</small>
            <h5 class="mb-0" id="pagoTotalPagar">$0.00</h5>
          </div>
          <div>
            <small class="d-block mb-1"><i class="fi fi-rs-check-circle me-1"></i>CANCELADO</small>
            <h5 class="mb-0 text-white" id="pagoCancelado">$0.00</h5>
          </div>
          <div>
            <small class="d-block mb-1"><i class="fi fi-rs-info me-1"></i>RESTANTE</small>
            <h5 class="mb-0 text-warning" id="pagoRestante">$0.00</h5>
          </div>
        </div>

        <!-- Detalles de Pago -->
        <div id="contenedorDetallesPago">
          <div class="card border-0 shadow-sm mb-3 fila-pago">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 text-primary"><i class="fi fi-rs-receipt me-2"></i>Detalle de Pago</h6>
                <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-pago d-none"><i class="fi fi-rs-trash"></i></button>
              </div>
              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label text-muted small">MÉTODO DE PAGO</label>
                  <select class="form-select form-select-sm sel-metodo-pago">
                    <!-- Se llena dinámicamente mediante JS -->
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label text-muted small">MONEDA</label>
                  <select class="form-select form-select-sm sel-moneda-pago">
                    <!-- Se llena dinámicamente mediante JS -->
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label text-muted small">MONTO PAGADO</label>
                  <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fi fi-rs-money"></i></span>
                    <input type="number" step="0.01" min="0" class="form-control input-monto-pago" value="0.00">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <button type="button" class="btn btn-outline-primary w-100 border-dashed mb-3" id="btnAgregarOtroPago">
          <i class="fi fi-rs-plus me-1"></i>Agregar otro detalle de pago
        </button>

      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Volver</button>
        <button type="button" class="btn btn-primary px-4 fact-pagos-grad" id="btnConfirmarPago">Confirmar Pago</button>
      </div>
    </div>
  </div>
</div>