<link rel="stylesheet" href="<?php echo APP_URL; ?>src/assets/css/compras.css">
<?php
    use src\config\inc\componentesModelo;
    $componente = new componentesModelo();
    $instruccionesLista = [
        'encabezado' => 'Gestionar Compras',
        'tituloBtnReg' => 'Registrar Compra',
    ];
    echo $componente->listaDataTable($instruccionesLista);
?>
<input type="hidden" class="nombreVista" value="id_compra">

<!-- Modal Registrar Compra -->
<div class="modal fade modalRegistrar" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                <h5 class="modal-title text-white fw-bold">
                    <i class="fas fa-shopping-cart me-2"></i> Registrar Compra
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form class="formularioAjax validar" method="POST" action="" novalidate>
                <div class="modal-body p-0 modal-body-compras" style="background-color: #f8f9fa;">
                    <input type="hidden" name="accion" value="registrar">

                    <!-- Cabecera Global -->
                    <div class="header-global">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-clock me-2 text-primary" style="font-size: 1.2rem;"></i>
                            <span class="fw-bold text-dark text-uppercase" style="letter-spacing: 0.5px;">Fecha de
                                Registro</span>
                        </div>
                        <div style="flex-grow: 1; max-width: 300px; min-width: 200px;">
                            <input type="datetime-local" class="form-control fw-bold text-primary" name="fecha_compra"
                                required value="<?php echo date('Y-m-d\TH:i'); ?>" style="border: 1px solid #dee2e6;">
                        </div>
                    </div>

                    <!-- Contenedor Grid con Scroll -->
                    <div class="grid-compras-container">
                        <!-- Header Fijo -->
                        <div class="grid-compras-header">
                            <div>Proveedor</div>
                            <div>Tipo</div>
                            <div>Artículo</div>
                            <div>Unidad</div>
                            <div>Cant.</div>
                            <div class="text-center">Del</div>
                        </div>

                        <!-- Filas Dinámicas -->
                        <div id="contenedorItems">
                            <!-- JS insertará filas aquí -->
                        </div>
                    </div>

                    <!-- Botón Agregar (Flotante-ish al final, centrado y grande) -->
                    <div class="p-3 bg-light border-top text-center">
                        <button type="button" class="btn btn-primary rounded-circle shadow-lg" id="btnAgregarFila"
                            style="width: 56px; height: 56px; font-size: 1.8rem; padding: 0; line-height: 1; background-color: #2196F3; border: none; transition: transform 0.2s;">
                            +
                        </button>
                        <div class="small text-muted mt-2 fw-semibold">Agregar Articulo</div>
                    </div>
                </div>

                <div class="modal-footer border-0 bg-white p-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal"
                        style="border-radius: 8px; font-weight: 600;">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-outline-secondary px-4" id="btnLimpiarFormulario"
                        style="border-radius: 8px; font-weight: 600;">
                        Limpiar
                    </button>
                    <button type="submit" class="btn px-4"
                        style="background: linear-gradient(135deg, #2196F3, #21CBF3); color: white; border: none; border-radius: 8px; font-weight: 600;">
                        <i class="fas fa-save me-2"></i> Guardar Todo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Actualizar Compra -->
<div class="modal fade modalActualizar" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                <h5 class="modal-title text-white fw-bold">
                    <i class="fas fa-edit me-2"></i> Actualizar Compra
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form class="formularioAjax validar" method="POST" action="" novalidate>
                <div class="modal-body p-4" style="background-color: #f8f9fa;">
                    <input type="hidden" name="accion" value="actualizar">
                    <input type="hidden" name="id_compra" class="formularioActualizar">

                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small text-muted fw-semibold">Proveedor</label>
                                    <select class="form-select selectProveedorAct formularioActualizar"
                                        name="rif_proveedor" style="border-radius: 8px;">
                                        <option value="">Seleccione Razón Social...</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted fw-semibold">Fecha y Hora de
                                        Compra</label>
                                    <input type="datetime-local" class="form-control formularioActualizar"
                                        name="fecha_compra" required style="border-radius: 8px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Grid de Items -->
                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2 px-2">
                            <h6 class="fw-bold text-muted mb-0"><i class="fas fa-boxes me-2"></i>Detalle de la Compra
                            </h6>
                            <button type="button" class="btn btn-sm btn-primary" id="btnAgregarFila"
                                style="background: linear-gradient(135deg, #667eea, #764ba2); border: none;">
                                <i class="fas fa-plus me-1"></i> Agregar Item
                            </button>
                        </div>

                        <div class="grid-compras-header rounded-top border bg-white">
                            <div>Proveedor</div>
                            <div>Tipo</div>
                            <div>Artículo</div>
                            <div>Unidad</div>
                            <div>Cantidad</div>
                            <div class="text-center">Acción</div>
                        </div>

                        <div class="grid-compras-container border border-top-0 rounded-bottom" id="contenedorItems">
                            <!-- JS fills this -->
                        </div>

                        <div class="mt-2 text-end text-muted small" id="contadorFilas">
                            Items: 0
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 bg-white p-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal"
                        style="border-radius: 8px; font-weight: 600;">
                        Cancelar
                    </button>
                    <button type="submit" class="btn px-4"
                        style="background: linear-gradient(135deg, #4e54c8, #8f94fb); color: white; border: none; border-radius: 8px; font-weight: 600;">
                        <i class="fas fa-save me-2"></i> Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ver Detalles de Compra (Solo Lectura) -->
<div class="modal fade" id="modalVerCompra" tabindex="-1" aria-labelledby="modalVerCompraLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow-lg">

            <!-- Header con mismo estilo que registro -->
            <div class="modal-header" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                <h5 class="modal-title text-white fw-bold" id="modalVerCompraLabel">
                    <i class="fas fa-eye me-2"></i> Ver Detalles de Compra
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <!-- Body con mismo fondo -->
            <div class="modal-body p-0 modal-body-compras" style="background-color: #f8f9fa;">

                <!-- Cabecera Global (similar al de registro) -->
                <div class="header-global">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle me-2 text-primary" style="font-size: 1.2rem;"></i>
                        <span class="fw-bold text-dark text-uppercase" style="letter-spacing: 0.5px;">ID de
                            Compra</span>
                    </div>
                    <div style="flex-grow: 1; max-width: 300px; min-width: 200px;">
                        <div class="form-control fw-bold text-primary"
                            style="border: 1px solid #dee2e6; background-color: white;" id="verIdCompra">-</div>
                    </div>
                </div>

                <div class="header-global" style="border-top: none;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-clock me-2 text-primary" style="font-size: 1.2rem;"></i>
                        <span class="fw-bold text-dark text-uppercase" style="letter-spacing: 0.5px;">Fecha de
                            Registro</span>
                    </div>
                    <div style="flex-grow: 1; max-width: 300px; min-width: 200px;">
                        <div class="form-control fw-bold text-primary"
                            style="border: 1px solid #dee2e6; background-color: white;" id="verFechaCompra">-</div>
                    </div>
                </div>

                <!-- Grid con mismo estilo -->
                <div class="grid-compras-container">
                    <!-- Header Fijo -->
                    <div class="grid-compras-header">
                        <div>Proveedor</div>
                        <div>Tipo</div>
                        <div>Artículo</div>
                        <div>Unidad</div>
                        <div>Cant.</div>
                    </div>

                    <!-- Filas Dinámicas -->
                    <div id="verItemsBody">
                        <!-- JS llenará esto -->
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <div class="modal-footer border-0 bg-white p-4">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal"
                    style="border-radius: 8px; font-weight: 600;">
                    <i class="fas fa-times me-2"></i> Cerrar
                </button>
            </div>

        </div>
    </div>
</div>