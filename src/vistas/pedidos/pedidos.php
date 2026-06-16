<link rel="stylesheet" href="<?php echo APP_URL; ?>src/assets/css/pedidos.css">
<input type="hidden" class="nombreVista" value="pedidos">
<div class="main-content px-4" id="mainContent">
  <div class="container-fluid py-4">
    <ul class="nav nav-tabs mb-4 botonesTaps" role="tablist">

      <li class="btnTapPedidosPendientes nav-item d-none" role="presentation">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tapPedidosPendientes" type="button" role="tab" aria-controls="pedidos-pane" aria-selected="false">
          Pedidos Pendientes por atender
        </button>
      </li>
      <li class="btnTapPedidosRechazados nav-item d-none" role="presentation">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tapPedidosRechazados" type="button" role="tab" aria-controls="pedidos-pane" aria-selected="false">
          Pedidos Rechazados
        </button>
      </li>
      <li class="btnTapPedidosConfirmados nav-item d-none" role="presentation">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tapPedidosConfirmados" type="button" role="tab" aria-controls="pedidos-pane" aria-selected="false">
          Pedidos Confirmados
        </button>
      </li>
      <li class="btnTapPedidosEntregados nav-item d-none" role="presentation">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tapPedidosEntregados" type="button" role="tab" aria-controls="pedidos-pane" aria-selected="false">
          Pedidos Entregados
        </button>
      </li>

      <li class="btnTapCatalogoProductos nav-item d-none" role="presentation">
        <button class="nav-link active" id="catalogo-tab" data-bs-toggle="tab" data-bs-target="#tapCatalogoProductos" type="button" role="tab" aria-controls="catalogo-pane" aria-selected="true">
          Catálogo de Productos
        </button>
      </li>
      <li class="btnTapPedidosRealizados nav-item d-none" role="presentation">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tapPedidosRealizados" type="button" role="tab" aria-controls="pedidos-pane" aria-selected="false">
          Tus Pedidos
        </button>
      </li>
    </ul>
    <div class="tab-content contenidoTaps">
      <div class="tab-pane fade d-none" id="tapPedidosPendientes" role="tabpanel" aria-labelledby="pedidos-tab" tabindex="0">
        <div class="row mb-4">
          <div class="col-md-12">
            <h2 class="mb-0">PEDIDOS PENDIENTES POR ATENDER</h2>
          </div>
        </div>
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped table-hover tablaPedidosPendientes">
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="tab-pane fade d-none" id="tapPedidosRechazados" role="tabpanel" aria-labelledby="pedidos-tab" tabindex="0">
        <div class="row mb-4">
          <div class="col-md-12">
            <h2 class="mb-0">PEDIDOS RECHAZADOS</h2>
          </div>
        </div>
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped table-hover tablaPedidosRechazados">
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="tab-pane fade d-none" id="tapPedidosConfirmados" role="tabpanel" aria-labelledby="pedidos-tab" tabindex="0">
        <div class="row mb-4">
          <div class="col-md-12">
            <h2 class="mb-0">PEDIDOS CONFIRMADOS</h2>
          </div>
        </div>
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped table-hover tablaPedidosConfirmados">
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="tab-pane fade d-none" id="tapPedidosEntregados" role="tabpanel" aria-labelledby="pedidos-tab" tabindex="0">
        <div class="row mb-4">
          <div class="col-md-12">
            <h2 class="mb-0">PEDIDOS ENTREGADOS</h2>
          </div>
        </div>
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped table-hover tablaPedidosEntregados">
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="tab-pane fade show active d-none" id="tapCatalogoProductos" role="tabpanel" aria-labelledby="catalogo-tab" tabindex="0">
        <div class="row mb-4">
          <div class="col-md-12">
            <h2 class="mb-0">CATÁLOGO DE PRODUCTOS</h2>
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
      <div class="tab-pane fade d-none" id="tapPedidosRealizados" role="tabpanel" aria-labelledby="pedidos-tab" tabindex="0">
        <div class="row mb-4">
          <div class="col-md-12">
            <h2 class="mb-0">TUS PEDIDOS</h2>
          </div>
        </div>
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped table-hover tablaPedidosRealizados">
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal detalles del pedido -->
<div class="modal fade modalDetallesPedido" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content border-0">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
        <h5 class="modal-title"><i class="fas fa-edit me-2"></i> Presentaciones del producto</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul class="nav nav-tabs mb-4" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="btnTapProductos nav-link active" data-bs-toggle="tab" data-bs-target="#tapProductosPedido" type="button" role="tab" aria-controls="tapProductosPedido" aria-selected="true">
              Productos
            </button>
          </li>
          <li class="btnTapMedioEnvio nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tapMedioEnvio" type="button" role="tab" aria-controls="tapMedioEnvio" aria-selected="false">
              Delivery
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tapDetallesPago" type="button" role="tab" aria-controls="tapDrtallesPago" aria-selected="false">
              Detalles del pago
            </button>
          </li>
          <li class="nav-item btnTapCliente" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tapClientePedido" type="button" role="tab" aria-controls="tapClientePedido" aria-selected="false">
              Cliente
            </button>
          </li>
          <li class="btnTapVendedor nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tapVendedorPedido" type="button" role="tab" aria-controls="tapVendedorPedido" aria-selected="false">
              Vendedor
            </button>
          </li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane fade show active" id="tapProductosPedido" role="tabpanel" aria-labelledby="catalogo-tab" tabindex="0">
            <div class="row mb-4">
              <div class="col-md-12">
                <h2 class="mb-0">PRODUCTOS</h2>
              </div>
            </div>
            <div class="card">
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped table-hover tablaProductosPedido">
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane fade" id="tapDetallesPago" role="tabpanel" aria-labelledby="pedidos-tab" tabindex="0">
            <div class="row mb-4">
              <div class="col-md-12">
                <h2 class="mb-0">DETALLES DEL PAGO</h2>
              </div>
            </div>
            <div class="card">
              <div class="card-body">
                <div class="table-responsive mb-3">
                  <table class="table table-striped table-hover tablaDetallesPagosPedido">
                  </table>
                </div>
                <div class="contenedorCaptures">
                  <div class="accordion" id="acordionCaptures">
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                          Comprobante del pago #1
                        </button>
                      </h2>
                      <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#acordionCaptures">
                        <div class="accordion-body">
                          <img src="<?php echo APP_URL . DIR_FOTOS ?>comprobantes_pagos/comprobantes_pagos_2026_05_17_11_26_55_42.jpg" alt="" class="capturePagos">
                        </div>
                      </div>
                    </div>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                          Accordion Item #2
                        </button>
                      </h2>
                      <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#acordionCaptures">
                        <div class="accordion-body">
                          <img src="<?php echo APP_URL . DIR_FOTOS ?>comprobantes_pagos/comprobantes_pagos_2026_05_17_11_26_55_42.jpg" alt="" class="capturePagos">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane fade" id="tapClientePedido" role="tabpanel" aria-labelledby="cliente-tab" tabindex="0">
            <div class="row mb-4">
              <div class="col-md-12">
                <h2 class="mb-0">CLIENTE</h2>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body d-flex align-items-center">
                    <div class="me-3 text-primary" style="font-size: 2rem;"><i class="fi fi-rr-id-badge"></i></div>
                    <div>
                      <h6 class="mb-0 text-muted">Cédula</h6>
                      <h5 class="mb-0 tap_cedula_cliente">V-12345678</h5>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body d-flex align-items-center">
                    <div class="me-3 text-primary" style="font-size: 2rem;"><i class="fi fi-rr-user"></i></div>
                    <div>
                      <h6 class="mb-0 text-muted">Nombre y Apellido</h6>
                      <h5 class="mb-0">
                        <span class="tap_razon_social_cliente">Juan</span>
                      </h5>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body d-flex align-items-center">
                    <div class="me-3 text-primary" style="font-size: 2rem;"><i class="fi fi-rr-phone-call"></i></div>
                    <div>
                      <h6 class="mb-0 text-muted">Teléfono</h6>
                      <h5 class="mb-0 tap_telefono_cliente">0414-1234567</h5>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body d-flex align-items-center">
                    <div class="me-3 text-primary" style="font-size: 2rem;"><i class="fi fi-rr-envelope"></i></div>
                    <div class="overflow-hidden">
                      <h6 class="mb-0 text-muted">Correo Electrónico</h6>
                      <h5 class="mb-0 text-truncate tap_correo_cliente">juan.perez@example.com</h5>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane fade" id="tapVendedorPedido" role="tabpanel" aria-labelledby="vendedor-tab" tabindex="0">
            <div class="row mb-4">
              <div class="col-md-12">
                <h2 class="mb-0">VENDEDOR</h2>
              </div>
            </div>
            <div class="row">
              <div class="col-md-4 mb-3 d-flex justify-content-center align-items-center">
                <img class="tap_foto_usuario rounded-circle img-fluid shadow" src="" alt="Foto del Vendedor" style="width: 150px; height: 150px; object-fit: cover;">
              </div>
              <div class="col-md-8">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <div class="card h-100 border-0 shadow-sm">
                      <div class="card-body d-flex align-items-center">
                        <div class="me-3 text-info" style="font-size: 2rem;"><i class="fi fi-rr-id-badge"></i></div>
                        <div>
                          <h6 class="mb-0 text-muted">Cédula</h6>
                          <h5 class="mb-0 tap_cedula_usuario">V-87654321</h5>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 mb-3">
                    <div class="card h-100 border-0 shadow-sm">
                      <div class="card-body d-flex align-items-center">
                        <div class="me-3 text-info" style="font-size: 2rem;"><i class="fi fi-rr-user"></i></div>
                        <div>
                          <h6 class="mb-0 text-muted">Nombre y Apellido</h6>
                          <h5 class="mb-0">
                            <span class="tap_nombre_usuario mx-1">María</span>
                            <span class="tap_apellido_usuario">Gómez</span>
                          </h5>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 mb-3">
                    <div class="card h-100 border-0 shadow-sm">
                      <div class="card-body d-flex align-items-center">
                        <div class="me-3 text-info" style="font-size: 2rem;"><i class="fi fi-rr-phone-call"></i></div>
                        <div>
                          <h6 class="mb-0 text-muted">Teléfono</h6>
                          <h5 class="mb-0 tap_telefono_usuario">0424-7654321</h5>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 mb-3">
                    <div class="card h-100 border-0 shadow-sm">
                      <div class="card-body d-flex align-items-center">
                        <div class="me-3 text-info" style="font-size: 2rem;"><i class="fi fi-rr-envelope"></i></div>
                        <div class="overflow-hidden">
                          <h6 class="mb-0 text-muted">Correo Electrónico</h6>
                          <h5 class="mb-0 text-truncate tap_correo_usuario"></h5>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane fade" id="tapMedioEnvio" role="tabpanel" aria-labelledby="delivery-tab" tabindex="0">
            <div class="row mb-4">
              <div class="col-md-12">
                <h2 class="mb-0 tituloMedioEnvio">DELIVERY</h2>
              </div>
            </div>
            <!-- Formulario de Asignación de Repartidor -->
            <div id="seccionAsignarRepartidor" class="card border-0 shadow-sm mb-4">
              <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                <h5 class="text-primary"><i class="fi fi-rr-user-add me-2"></i> Asignar Repartidor</h5>
              </div>
              <div class="card-body">
                <form class="formularioAjax validar" method="POST" action="" novalidate enctype="multipart/form-data">
                  <input type="hidden" name="accion" value="asignarRepartidor">
                  <input type="hidden" class="idDeliveryPedido" name="id_delivery">
                  <input type="hidden" class="idPedido" name="id_pedido">
                  <div class="row align-items-end">
                    <div class="col-md-8 mb-3 mb-md-0">
                      <label for="selectRepartidor" class="form-label text-muted">Seleccione un Repartidor</label>
                      <select class="form-select selectRepartidor" name="cedula_repartidor" required>
                      </select>
                    </div>
                    <div class="col-md-4">
                      <button type="submit" class="btn btn-primary w-100">
                        <i class="fi fi-rr-disk me-2"></i> Guardar Asignación
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
            <!-- Datos del Repartidor Asignado -->
            <div id="seccionDatosRepartidor" class="card border-0 shadow-sm mb-4">
              <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                <h5 class="text-secondary">Datos del Repartidor</h5>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-center">
                      <div class="me-3 text-success" style="font-size: 2rem;"><i class="fi fi-rr-id-badge"></i></div>
                      <div>
                        <h6 class="mb-0 text-muted">Cédula</h6>
                        <h5 class="mb-0 tap_cedula_repartidor">V-11223344</h5>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-center">
                      <div class="me-3 text-success" style="font-size: 2rem;"><i class="fi fi-rr-user"></i></div>
                      <div>
                        <h6 class="mb-0 text-muted">Nombre y Apellido</h6>
                        <h5 class="mb-0">
                          <span class="tap_nombre_repartidor mx-1">Carlos</span>
                          <span class="tap_apellido_repartidor">Rodríguez</span>
                        </h5>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-center">
                      <div class="me-3 text-success" style="font-size: 2rem;"><i class="fi fi-rr-phone-call"></i></div>
                      <div>
                        <h6 class="mb-0 text-muted">Teléfono</h6>
                        <h5 class="mb-0 tap_telefono_repartidor">0412-9876543</h5>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div id="seccionDatosRuta" class="card border-0 shadow-sm">
              <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                <h5 class="text-secondary">Detalles de la Ruta</h5>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-center">
                      <div>
                        <h6 class="mb-0 text-muted">Ruta</h6>
                        <h5 class="mb-0 tap_nombre_ruta">Centro - Zona Norte</h5>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-center">
                      <div class="me-3 text-success" style="font-size: 2rem;"><i class="fi fi-rr-money-bill-wave"></i></div>
                      <div>
                        <h6 class="mb-0 text-muted">Precio de la Ruta</h6>
                        <h5 class="mb-0 tap_precio_ruta_factura">$ 5.00</h5>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-center">
                      <div class="me-3 text-success" style="font-size: 2rem;"><i class="fi fi-rr-money-bill-wave"></i></div>
                      <div>
                        <h6 class="mb-0 text-muted">Total Envío</h6>
                        <h5 class="mb-0 tap_totalEnvio">$ 5.00</h5>
                      </div>
                    </div>
                  </div>

                </div>
              </div>
            </div>
            <div id="seccionSucursalEmpresaEnvios" class="card border-0 shadow-sm">
              <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                <h5 class="text-secondary">Detalles de la Sucursal de envío</h5>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-center">
                      <div>
                        <h6 class="mb-0 text-muted">Empresa</h6>
                        <h5 class="mb-0 tap_nombre_empresa_envios"></h5>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-center">
                      <div>
                        <h6 class="mb-0 text-muted">Sucursal</h6>
                        <h5 class="mb-0 tap_nombre_sucursal_empresa_envios"></h5>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-center">
                      <div class="me-3 text-success" style="font-size: 2rem;"><i class="fi fi-rr-money-bill-wave"></i></div>
                      <div>
                        <h6 class="mb-0 text-muted">Precio del envío</h6>
                        <h5 class="mb-0 tap_precio_envio_tercero">$ 5.00</h5>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12 mt-2">
              <a
                href="https://maps.google.com/?q=10.4806,-66.9036"
                target="_blank"
                class="tap_url_direccion btn btn-outline-primary w-100">
                <i class="fi fi-rr-map-marker me-2"></i> Ver dirección en Google Maps
              </a>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i> Cerrar</button>
      </div>
    </div>
  </div>
</div>