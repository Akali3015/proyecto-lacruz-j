<?php
    use src\config\inc\componentesModelo;
    $componente = new componentesModelo();
?>

<input type="hidden" class="nombreVista" value="id_producto">
<?php
    $instruccionesLista = [
        'encabezado' => 'Gestionar Productos',
        'tituloBtnReg' => 'Registrar Producto',
    ];
    echo $componente->listaDataTable($instruccionesLista);
?>

<div class="modal fade modalRegistrar" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title">
                    <i class="fas fa-box me-2"></i> Registrar Producto
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="formularioAjax validar" method="POST" action="" novalidate>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="accion" value="registrar">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre del Producto</label>
                            <input type="text" class="form-control noRepetir" name="nombre_producto"
                                pattern="<?php echo regexNombreObj ?>"
                                minlength="<?php echo minRegexNombreObj ?>"
                                maxlength="<?php echo maxRegexNombreObj ?>"
                                required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stock Inicial</label>
                            <input type="number" class="form-control" name="stock_producto" pattern="<?php echo regexCantidadItem ?>" minlength="<?php echo minRegexCantidadItem ?>" maxlength="<?php echo maxRegexCantidadItem ?>" value="0" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Unidad de medida</label>
                            <select class="form-select selectUnidadMedida" name="id_unidad_medida" required></select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo de Producto</label>
                            <select class="form-select" name="producto_es_fabricado" id="tipo_producto" required>
                                <option value="0">Comprado/No Fabricado</option>
                                <option value="1">Fabricado</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3 campos-fabricado" style="display: none;">
                            <label class="form-label">Materias Primas para Fabricación</label>
                            <div class="form-text mb-2">Agregue las materias primas necesarias para fabricar este producto</div>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="tablaMateriasPrimas">
                                    <thead>
                                        <tr>
                                            <th>Materia Prima</th>
                                            <th>Cantidad Requerida</th>
                                            <th>Costo Unitario (Bs)</th>
                                            <th>Subtotal (Bs)</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cuerpoTablaMateriasPrimas">
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Total Costo:</strong></td>
                                            <td colspan="2">
                                                <strong id="totalCostoMaterias">0.00 Bs</strong>
                                                <input type="hidden" name="costo_total_materias" id="costoTotalMaterias" value="0">
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <button type="button" class="btn btn-primary btn-sm" id="btnAgregarMateriaPrima">
                                <i class="fas fa-plus me-1"></i> Agregar Materia Prima
                            </button>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Precio al Detal (Bs)</label>
                            <input type="number" step="0.01" class="form-control campo-no-fabricado"
                                name="precio_producto_detal"
                                pattern="<?php echo regexPrecio; ?>"
                                minlength="<?php echo minRegexPrecio ?>"
                                maxlength="<?php echo maxRegexPrecio ?>"
                                value="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Precio al Mayor (Bs)</label>
                            <input type="number" step="0.01" class="form-control campo-no-fabricado"
                                name="precio_producto_mayor"
                                pattern="<?php echo regexPrecio; ?>"
                                minlength="<?php echo minRegexPrecio ?>"
                                maxlength="<?php echo maxRegexPrecio ?>"
                                value="0" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Presentaciones Disponibles</label>
                            <div class="form-text mb-2">Seleccione las presentaciones para este producto</div>
                            <div class="contenedor-presentaciones row"></div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-outline-secondary btn-sm btn-seleccionar-todas">
                                    <i class="fas fa-check-square me-1"></i> Seleccionar todas
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm btn-deseleccionar-todas">
                                    <i class="fas fa-square me-1"></i> Deseleccionar todas
                                </button>
                            </div>
                        </div>

                        <div class="inputs-ocultos-presentaciones"></div>
                        <div id="inputsOcultosMaterias"></div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                        <i class="fas fa-save me-2"></i> Guardar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade modalActualizar" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i> Editar Producto
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="formularioAjax validar" method="POST" action="" novalidate>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="accion" value="actualizar">
                        <input type="hidden" name="id_producto" class="formularioActualizar">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre del Producto</label>
                            <input type="text" class="form-control noRepetir formularioActualizar" name="nombre_producto"
                                pattern="<?php echo regexNombreObj ?>"
                                minlength="<?php echo minRegexNombreObj ?>"
                                maxlength="<?php echo maxRegexNombreObj ?>"
                                required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stock Inicial</label>
                            <input type="number" class="form-control formularioActualizar" name="stock_producto" pattern="<?php echo regexCantidadItem ?>" minlength="<?php echo minRegexCantidadItem ?>" maxlength="<?php echo maxRegexCantidadItem ?>" value="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Unidad de medida</label>
                            <select class="form-select selectUnidadMedida formularioActualizar" name="id_unidad_medida" required></select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo de Producto</label>
                            <select class="form-select formularioActualizar" name="producto_es_fabricado" id="tipo_producto" required>
                                <option value="0">Comprado/No Fabricado</option>
                                <option value="1">Fabricado</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3 campos-fabricado" style="display: none;">
                            <label class="form-label">Materias Primas para Fabricación</label>
                            <div class="form-text mb-2">Agregue las materias primas necesarias para fabricar este producto</div>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="tablaMateriasPrimas">
                                    <thead>
                                        <tr>
                                            <th>Materia Prima</th>
                                            <th>Cantidad Requerida</th>
                                            <th>Costo Unitario (Bs)</th>
                                            <th>Subtotal (Bs)</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cuerpoTablaMateriasPrimas">
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Total Costo:</strong></td>
                                            <td colspan="2">
                                                <strong id="totalCostoMaterias">0.00 Bs</strong>
                                                <input type="hidden" name="costo_total_materias" id="costoTotalMaterias" value="0" class="formularioActualizar">
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <button type="button" class="btn btn-primary btn-sm" id="btnAgregarMateriaPrima">
                                <i class="fas fa-plus me-1"></i> Agregar Materia Prima
                            </button>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Precio al Detal (Bs)</label>
                            <input type="number" step="0.01" class="form-control campo-no-fabricado formularioActualizar"
                                name="precio_producto_detal"
                                pattern="<?php echo regexPrecio; ?>"
                                minlength="<?php echo minRegexPrecio ?>"
                                maxlength="<?php echo maxRegexPrecio ?>"
                                value="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Precio al Mayor (Bs)</label>
                            <input type="number" step="0.01" class="form-control campo-no-fabricado formularioActualizar"
                                name="precio_producto_mayor"
                                pattern="<?php echo regexPrecio; ?>"
                                minlength="<?php echo minRegexPrecio ?>"
                                maxlength="<?php echo maxRegexPrecio ?>"
                                value="0" required>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Presentaciones Disponibles</label>
                            <div class="form-text mb-2">Seleccione las presentaciones para este producto</div>
                            <div class="contenedor-presentaciones row"></div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-outline-secondary btn-sm btn-seleccionar-todas">
                                    <i class="fas fa-check-square me-1"></i> Seleccionar todas
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm btn-deseleccionar-todas">
                                    <i class="fas fa-square me-1"></i> Deseleccionar todas
                                </button>
                            </div>
                        </div>

                        <div class="inputs-ocultos-presentaciones"></div>
                        <div id="inputsOcultosMaterias"></div>
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