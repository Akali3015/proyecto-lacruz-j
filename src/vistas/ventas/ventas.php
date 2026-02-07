<input type="hidden" class="nombreVista" value="id_venta">
<?php
    use src\config\inc\componentesModelo;
    $componente = new componentesModelo();
    $instruccionesLista = [
        'encabezado' => 'Gestionar Ventas',
        'tituloBtnReg' => 'Nueva Venta',
    ];
    echo $componente->listaDataTable($instruccionesLista);
?>

<!-- MODAL REGISTRAR VENTA -->
<div class="modal fade modalRegistrar" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4369d3, #039ab4);">
                <h5 class="modal-title">
                    <i class="fas fa-shopping-cart me-2"></i> 
                    <span id="tituloModalVenta">Nueva Venta</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form class="formularioAjax validar" method="POST" action="" novalidate>
                <div class="modal-body">
                    <input type="hidden" name="id_venta" id="id_venta_form">
                    <input type="hidden" name="accion" id="accion_form" value="registrar">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cliente <span class="text-danger">*</span></label>
                            <select name="rif_cedula_cliente" class="form-select" id="selectCliente" required>
                                <option value="">Seleccionar cliente...</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" id="estadoVenta" disabled>
                                <option value="1">Pendiente</option>
                                <option value="2">Confirmada</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Total Venta <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   name="total_venta" 
                                   id="total_venta" 
                                   readonly 
                                   required 
                                   pattern="[0-9]+(\.[0-9]{1,2})?"
                                   min="0.01">
                        </div>
                    </div>

                    <!-- TABS PRODUCTOS/SERVICIOS -->
                    <ul class="nav nav-tabs mt-3" id="tabsVenta" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="productos-tab-btn" data-bs-toggle="tab" data-bs-target="#productos-tab" type="button" role="tab">
                                <i class="fas fa-box me-1"></i> Productos Directos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="servicios-tab-btn" data-bs-toggle="tab" data-bs-target="#servicios-tab" type="button" role="tab">
                                <i class="fas fa-cogs me-1"></i> Servicios
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content mt-3" id="tabContentVenta">
                        <!-- PRODUCTOS DIRECTOS -->
                        <div class="tab-pane fade show active p-3 border rounded" id="productos-tab" role="tabpanel">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <button type="button" class="btn btn-outline-primary" id="btnAgregarProductoDirecto">
                                        <i class="fas fa-plus me-2"></i> Agregar Producto Directo
                                    </button>
                                </div>
                            </div>
                            <div id="contenedorProductosDirectos">
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-info-circle me-2"></i>
                                    No hay productos directos agregados
                                </div>
                            </div>
                        </div>

                        <!-- SERVICIOS -->
                        <div class="tab-pane fade p-3 border rounded" id="servicios-tab" role="tabpanel">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <button type="button" class="btn btn-outline-success" id="btnAgregarServicio">
                                        <i class="fas fa-plus me-2"></i> Agregar Servicio
                                    </button>
                                </div>
                            </div>
                            <div id="contenedorServicios">
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-info-circle me-2"></i>
                                    No hay servicios agregados
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BOTONES DE ACCIÓN -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-warning" id="btnConfirmarVenta" style="display: none;">
                                <i class="fas fa-check-circle me-2"></i> Confirmar Venta (Reducir Stock)
                            </button>
                        </div>
                        <div class="col-md-6 text-end">
                            <button type="button" class="btn btn-secondary me-2" id="btnCancelarVenta">
                                <i class="fas fa-times me-2"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-success" id="btnGuardarVenta" disabled>
                                <i class="fas fa-save me-2"></i> Guardar Venta
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL CONFIRMAR PRODUCTOS SERVICIO -->
<div class="modal fade" id="modalProductosServicio" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-cubes me-2"></i> Confirmar Productos del Servicio
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="accion" value="confirmarProductosServicio">
                <input type="hidden" name="id_servicio_venta" id="id_servicio_confirmar">
                <div id="contenidoProductosServicio">
                    <!-- Contenido dinámico -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnConfirmarProductosServicio" disabled>
                    <i class="fas fa-check me-2"></i> Confirmar Productos
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DETALLES VENTA -->
<div class="modal fade modalDetalles" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Detalles Venta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenidoDetallesVenta">
                <!-- Contenido dinámico -->
            </div>
        </div>
    </div>
</div>
