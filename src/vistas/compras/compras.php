<?php
use src\config\inc\componentesModelo;
$componente = new componentesModelo();
?>

<input type="hidden" class="nombreVista" value="id_compra">
<?php
$instruccionesLista = [
    'encabezado' => 'Gestionar Compras',
    'tituloBtnReg' => 'Registrar Compra',
];
echo $componente->listaDataTable($instruccionesLista);
?>

<div class="modal fade modalRegistrar" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                <h5 class="modal-title">
                    <i class="fas fa-shopping-cart me-2"></i> Registrar Compra
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form class="formularioAjax validar" method="POST" action="" novalidate>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="accion" value="registrar">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Proveedor</label>
                            <select class="form-select selectProveedor" name="rif_proveedor" required>
                                <option value="">Seleccione Razón Social...</option>
                            </select>
                        </div>



                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha y Hora de Compra</label>
                            <input type="datetime-local" class="form-control" name="fecha_compra" required
                                value="<?php echo date('Y-m-d\TH:i'); ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo de Inventario</label>
                            <select class="form-select" id="tipoItem">
                                <option value="materia_prima">Materia Prima</option>
                                <option value="producto">Producto Terminado/Reventa</option>
                                <option value="insumo">Insumo Operativo</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Artículo a Comprar</label>
                            <select class="form-select" id="selectItem">
                                <option value="">Seleccione artículo</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Unidad de Medida</label>
                            <input type="text" class="form-control" id="unidadMedidaDisplay" disabled
                                placeholder="Auto">
                            <input type="hidden" id="idUnidadMedida" name="id_unidad_medida">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Cantidad</label>
                            <input type="number" class="form-control" id="cantidadItem" min="0.01" step="0.01"
                                value="1">
                        </div>

                        <div class="col-md-4 mb-3 d-flex align-items-end">
                            <button type="button" class="btn btn-success w-100" id="btnAgregarItem">
                                <i class="fas fa-plus me-2"></i> Agregar Artículo
                            </button>
                        </div>

                        <!-- Tabla de items agregados -->
                        <div class="col-md-12 mt-3">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-list me-2"></i> Artículos Agregados
                                        <span class="badge bg-primary" id="contadorItems">0</span>
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0" id="tablaItemsCompra">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="20%">Tipo</th>
                                                    <th width="35%">Artículo</th>
                                                    <th width="20%">Unidad</th>
                                                    <th width="15%">Cantidad</th>
                                                    <th width="10%" class="text-center">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="itemsAgregados">
                                                <tr id="filaVacia">
                                                    <td colspan="5" class="text-center text-muted py-4">
                                                        <i class="fas fa-inbox fa-2x mb-2"></i>
                                                        <p class="mb-0">No hay items agregados</p>
                                                        <small>Agregue items usando el formulario de arriba</small>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="inputsOcultosDetalles"></div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between" style="background-color: #f8f9fa;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="btnRegistrarCompra" style="display: none;
                        background: linear-gradient(135deg, #667eea, #764ba2); border: none;">
                        <i class="fas fa-save me-2"></i> Registrar Compra Completa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Actualizar Compra -->
<div class="modal fade modalActualizar" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i> Editar Compra
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form class="formularioAjax" method="POST" action="" novalidate>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="accion" value="actualizar">
                        <input type="hidden" name="id_compra" id="idCompraActualizar">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Proveedor</label>
                            <select class="form-select selectProveedorAct" name="rif_proveedor">
                                <option value="">Seleccione Razón Social...</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Responsable</label>
                            <select class="form-select selectResponsableAct" name="cedula_usuario">
                                <option value="">Seleccione responsable...</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha y Hora de Compra</label>
                            <input type="datetime-local" class="form-control" name="fecha_compra" id="fechaCompraAct">
                        </div>

                        <div class="col-12 mb-3">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Nota:</strong> Solo se puede editar la información general de la compra.
                                Los artículos ya registrados no pueden modificarse para mantener la integridad del
                                inventario.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning text-white"
                        style="background: linear-gradient(135deg, #667eea, #764ba2); border: none;">
                        <i class="fas fa-save me-2"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>