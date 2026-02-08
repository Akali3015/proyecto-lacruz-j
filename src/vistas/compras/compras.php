<?php
use src\config\inc\componentesModelo;
$componente = new componentesModelo();
?>

<input type="hidden" class="nombreVista" value="compras">
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
                            <label class="form-label">Responsable</label>
                            <select class="form-select selectResponsable" name="cedula_usuario" required>
                                <option value="">Seleccione responsable...</option>
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

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Artículo a Comprar</label>
                            <select class="form-select" id="selectItem">
                                <option value="">Seleccione artículo</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Unidad de Medida</label>
                            <select class="form-select selectUnidadMedida" name="id_unidad_medida" required>
                                <option value="">Seleccione unidad...</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cantidad</label>
                            <input type="number" class="form-control" id="cantidadItem" min="1" value="1">
                        </div>

                        <div id="inputsOcultosDetalles"></div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary"
                        style="background: linear-gradient(135deg, #667eea, #764ba2); border: none;">
                        <i class="fas fa-save me-2"></i> Registrar en Inventario
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