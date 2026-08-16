<?php
echo '<link rel="stylesheet" href="/proyecto-lacruz-j/src/assets/css/ordenesServicios.css">';

use src\config\inc\componentesModelo;

$componente = new componentesModelo();
$instruccionesLista = [
    'encabezado'   => 'Gestión de Órdenes de Servicio',
];
echo $componente->listaDataTable($instruccionesLista);
?>
<input type="hidden" class="nombreVista" value="ordenesServicios">

<div class="modal fade modalDetallesOrden" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title"><i class="fi fi-rs-clipboard-list me-2"></i> Detalles de la Orden de Servicio</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <ul class="nav nav-tabs" id="ordenTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="tab-info-orden" data-bs-toggle="tab" data-bs-target="#infoOrden" type="button" role="tab">
                                    <i class="fi fi-rs-info me-1"></i> Información
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-cliente-orden" data-bs-toggle="tab" data-bs-target="#clienteOrden" type="button" role="tab">
                                    <i class="fi fi-rs-user me-1"></i> Datos del Cliente
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-ubicacion-orden" data-bs-toggle="tab" data-bs-target="#ubicacionOrden" type="button" role="tab">
                                    <i class="fi fi-rs-marker me-1"></i> Ubicación
                                </button>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="tab-content col-12">
                        <div class="tab-pane fade show active" id="infoOrden" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold text-muted">N° Orden:</label>
                                    <p class="id_servicio_factura mb-0 fs-5 fw-semibold">-</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold text-muted">N° Factura:</label>
                                    <p class="id_orden_entrega_presupuesto mb-0">-</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold text-muted">Servicio:</label>
                                    <p class="nombre_servicio mb-0">-</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold text-muted">Cantidad:</label>
                                    <p class="cantidad_servicio mb-0">-</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold text-muted">Precio Unitario:</label>
                                    <p class="precio_servicio mb-0">-</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold text-muted">Subtotal:</label>
                                    <p class="subtotal_servicio mb-0">-</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold text-muted">Fecha de Ejecución:</label>
                                    <p class="fecha_ejecucion mb-0">-</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold text-muted">Estado:</label>
                                    <span class="badge status_badge_orden fs-6 p-2">-</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="clienteOrden" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold text-muted">RIF/Cédula:</label>
                                    <p class="rif_cedula_cliente mb-0">-</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold text-muted">Razón Social:</label>
                                    <p class="razon_social_cliente mb-0">-</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold text-muted">Teléfono:</label>
                                    <p class="telefono_cliente mb-0">-</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold text-muted">Correo:</label>
                                    <p class="correo_cliente mb-0">-</p>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="fw-bold text-muted">Dirección:</label>
                                    <p class="direccion_cliente mb-0">-</p>
                                </div>
                            </div>
                        </div>
           
                        <div class="tab-pane fade" id="ubicacionOrden" role="tabpanel">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="fw-bold text-muted">Ruta:</label>
                                    <p class="nombre_ruta mb-0">-</p>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="fw-bold text-muted">Coordenadas:</label>
                                    <p class="coordenadas mb-0">-</p>
                                </div>
                                <div class="col-12">
                                    <a href="#" target="_blank" class="btn btn-outline-primary url_direccion_btn">
                                        <i class="fi fi-rs-map-marker me-2"></i>Ver en Google Maps
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
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

<div class="modal fade modalGestionarOrden" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title"><i class="fi fi-rs-settings me-2"></i> Gestionar Orden de Servicio</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form class="formularioGestionarOrden">
                <div class="modal-body">
                    <input type="hidden" name="id_servicio_factura" class="id_orden_gestion">
                    <input type="hidden" name="accion" value="actualizar">

                    <div class="alert alert-info mb-3">
                        <div class="d-flex justify-content-between">
                            <span><strong>Orden #:</strong> <span class="info_id_orden">-</span></span>
                            <span><strong>Servicio:</strong> <span class="info_nombre_servicio">-</span></span>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <span><strong>Cliente:</strong> <span class="info_razon_social_cliente">-</span></span>
                            <span><strong>Status Actual:</strong> <span class="badge info_status_actual">-</span></span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nuevo Estado</label>
                        <select class="form-select selectNuevoStatus" name="status" required>
                            <option value="">Seleccione un estado...</option>
                            <option value="1">Pendiente</option>
                            <option value="2">Ejecutado</option>
                            <option value="4">Cancelado</option>
                        </select>
                        <small class="text-muted" id="mensajeStatusInfo"></small>
                    </div>
                    
                    <div class="mb-3" id="contenedorFechaEjecucion">
                        <label class="form-label fw-semibold">Fecha de Ejecución</label>
                        <input type="date" class="form-control nueva_fecha_ejecucion" name="fecha_ejecucion">
                    </div>
                </div>
                <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                        <i class="fas fa-save me-2"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>