<link rel="stylesheet" href="<?php echo APP_URL; ?>src/assets/css/compras.css">
<?php
    use src\config\inc\componentesModelo;
    $componente = new componentesModelo();
    $instruccionesLista = [
        'encabezado'   => 'Gestionar Compras',
        'tituloBtnReg' => 'Registrar Compra',
    ];
    echo $componente->listaDataTable($instruccionesLista);
?>
<input type="hidden" class="nombreVista" value="compras">

<!-- Modal registro y edicion -->
<div class="modal fade modalRegistrar" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header modal-header-custom">
                <h5 class="modal-title text-white fw-bold">
                    <i class="fas fa-shopping-cart me-2"></i>
                    Registrar Compra
                </h5>
                <button type="button" class="btn-close btn-close-white"
                    data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form class="formularioAjax" method="POST" action="" novalidate>
                <input type="hidden" name="accion"    value="registrar">
                <input type="hidden" name="id_compra" value="">

                <div class="modal-body p-0 modal-body-compras modal-body-custom">

                    <!-- Cabecera -->
                    <div class="header-global">

                        <!-- Proveedor en edicion -->
                        <div class="d-none modo-edicion flex-grow-1 me-3 header-global-item">
                            <label for="selectProveedorEdicion" class="form-label small text-muted fw-semibold mb-1">
                                Proveedor
                            </label>
                            <select class="form-select selectProveedorAct"
                                name="rif_proveedor"
                                id="selectProveedorEdicion"
                                disabled>
                                <option value="">Seleccione proveedor...</option>
                            </select>
                        </div>

                        <!-- Fecha -->
                        <div class="d-flex align-items-center">
                            <i class="fas fa-clock me-2 text-primary icon-primary-lg"></i>
                            <span class="fw-bold text-dark text-uppercase text-uppercase-spaced">
                                Fecha de Registro
                            </span>
                        </div>
                        <div class="input-date-container">
                            <input type="datetime-local"
                                class="form-control fw-bold text-primary border"
                                name="fecha_compra"
                                value="<?php echo date('Y-m-d\TH:i'); ?>">
                        </div>
                    </div>

                    <!-- Tabla de items -->
                    <div class="grid-compras-container">

                        <!-- Cabecera tabla -->
                        <div class="grid-compras-header">
                            <div>Proveedor</div>
                            <div>Tipo</div>
                            <div>Artículo</div>
                            <div>Unidad</div>
                            <div>Cant.</div>
                            <div class="text-center">Del</div>
                        </div>

                        <!-- Filas dinámicas -->
                        <div id="contenedorItems">
                            <!-- JS insertará filas aquí -->
                        </div>
                    </div>

                    <!-- Footer tabla -->
                    <div class="p-3 bg-light border-top
                        d-flex align-items-center justify-content-between">
                        <span class="small text-muted fw-semibold"
                            id="contadorFilas">Items: 0</span>
                        <div class="d-flex flex-column align-items-center mx-auto">
                            <button type="button"
                                class="btn btn-primary rounded-circle shadow btn-add-item"
                                id="btnAgregarFila">
                                +
                            </button>
                            <div class="small text-muted mt-1 fw-semibold">
                                Agregar artículo
                            </div>
                        </div>
                        <span class="px-5"></span><!-- spacer para equilibrar el contador -->
                    </div>
                </div>

                <div class="modal-footer-custom">
                    <button type="button"
                        class="btn btn-light px-4 btn-cancel-custom"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="button"
                        class="btn btn-outline-secondary px-4 btn-cancel-custom"
                        id="btnLimpiarFormulario">
                        Limpiar
                    </button>
                    <button type="submit"
                        class="btn px-4 btn-save-custom">
                        <i class="fas fa-save me-2"></i>
                        <span class="textoSubmit">Guardar Todo</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Modal detalles -->
<div class="modal fade"
    id="modalVerCompra" tabindex="-1"
    aria-labelledby="modalVerCompraLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header modal-header-custom">
                <h5 class="modal-title text-white fw-bold"
                    id="modalVerCompraLabel">
                    <i class="fas fa-eye me-2"></i>
                    Detalles de Compra
                </h5>
                <button type="button" class="btn-close btn-close-white"
                    data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body p-0 modal-body-compras modal-body-custom">

                <!-- Datos de compra -->
                <div class="header-global">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-hashtag me-2 text-primary icon-hashtag-sm"></i>
                        <span class="fw-bold text-dark text-uppercase text-uppercase-spaced">
                            # Compra
                        </span>
                    </div>
                    <div class="input-date-container">
                        <div class="form-control fw-bold text-primary info-display-box"
                            id="verIdCompra">-</div>
                    </div>
                </div>

                <!-- Fecha -->
                <div class="header-global border-top-0">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-clock me-2 text-primary icon-hashtag-sm"></i>
                        <span class="fw-bold text-dark text-uppercase text-uppercase-spaced">
                            Fecha de Registro
                        </span>
                    </div>
                    <div class="input-date-container">
                        <div class="form-control fw-bold text-primary info-display-box"
                            id="verFechaCompra">-</div>
                    </div>
                </div>

                <!-- Items -->
                <div class="grid-compras-container">
                    <div class="grid-compras-header">
                        <div>Proveedor</div>
                        <div>Tipo</div>
                        <div>Artículo</div>
                        <div>Unidad</div>
                        <div>Cant.</div>
                    </div>
                    <div id="verItemsBody">
                        <!-- JS llenará esto -->
                    </div>
                </div>

            </div>

            <div class="modal-footer-custom">
                <button type="button"
                    class="btn btn-light px-4 btn-cancel-custom"
                    data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cerrar
                </button>
            </div>

        </div>
    </div>
</div>