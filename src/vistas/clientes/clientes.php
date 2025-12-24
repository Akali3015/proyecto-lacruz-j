<?php

use src\config\inc\componentesModelo;

$componente = new componentesModelo();
?>

<input type="hidden" class="nombreVista" value="rif_cedula_cliente">

<?php
$instruccionesLista = [
    'encabezado' => 'Gestionar Clientes',
    'tituloBtnReg' => 'Registrar Cliente',
];
echo $componente->listaDataTable($instruccionesLista);
?>

<!-- [ FORMULARIO REGISTRAR ] COMIENZO -->
<div class="modal fade modalRegistrar" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title" id="registrarUsuarioModalLabel">
                    <i class="fas fa-user-plus me-2"></i> Registro de Nuevo Cliente
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form class="formularioAjax validar" method="POST" action="" novalidate>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="accion" value="registrar">

                        <div class="col-md-6 mb-3">
                            <label for="rif_cedula_cliente" class="form-label">RIF/Cédula</label>
                            <input type="text" class="form-control noRepetir" name="rif_cedula_cliente" pattern="<?php echo regexNombreObj ?>" minlength="<?php echo minRegexNombreObj ?>" maxlength="<?php echo maxRegexNombreObj ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="razon_social_cliente" class="form-label">Razón Social</label>
                            <input type="text" class="form-control noRepetir" name="razon_social_cliente" pattern="<?php echo regexNombreObj ?>" minlength="<?php echo minRegexNombreObj ?>" maxlength="<?php echo maxRegexNombreObj ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="telefono_cliente" class="form-label">Teléfono</label>
                            <input type="text" class="form-control noRepetir" name="telefono_cliente" pattern="<?php echo regexTelefono ?>" minlength="<?php echo minRegexTelefono ?>" maxlength="<?php echo maxRegexTelefono ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="correo_cliente" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control noRepetir" name="correo_cliente" pattern="<?php echo regexCorreo ?>" minlength="<?php echo minRegexCorreo ?>" maxlength="<?php echo maxRegexCorreo ?>" required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="direccion_cliente" class="form-label">Dirección</label>
                            <textarea class="form-control" name="direccion_cliente" rows="3" pattern="<?php echo regexDescripcion ?>" minlength="<?php echo minRegexDescripcion ?>" maxlength="<?php echo maxRegexDescripcion ?>" required></textarea>
                        </div>

                    </div>
                </div>
                <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                        <i class="fas fa-save me-2"></i> Guardar Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- [ FORMULARIO REGISTRAR ] FIN -->

<!-- FORMULARIO EDITAR COMIENZO -->
<div class="modal fade modalActualizar" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i> Editar Cliente
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="formularioAjax validar" method="POST" action="" novalidate>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="accion" value="actualizar">
                        <input type="hidden" name="rif_cedula_cliente" class="formularioActualizar">

                        <div class="col-md-6 mb-3">
                            <label for="razon_social_cliente" class="form-label">Razón Social</label>
                            <input type="text" class="form-control formularioActualizar noRepetir" name="razon_social_cliente" pattern="<?php echo regexNombreObj ?>" minlength="<?php echo minRegexNombreObj ?>" maxlength="<?php echo maxRegexNombreObj ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="telefono_cliente" class="form-label">Teléfono</label>
                            <input type="text" class="form-control formularioActualizar noRepetir" name="telefono_cliente" pattern="<?php echo regexTelefono ?>" minlength="<?php echo minRegexTelefono ?>" maxlength="<?php echo maxRegexTelefono ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="correo_cliente" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control formularioActualizar noRepetir" name="correo_cliente" pattern="<?php echo regexCorreo ?>" minlength="<?php echo minRegexCorreo ?>" maxlength="<?php echo maxRegexCorreo ?>" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="direccion_cliente" class="form-label">Dirección</label>
                            <textarea class="form-control formularioActualizar" name="direccion_cliente" rows="3" pattern="<?php echo regexDescripcion ?>" minlength="<?php echo minRegexDescripcion ?>" maxlength="<?php echo maxRegexDescripcion ?>" required></textarea>
                        </div>
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
</div>
<!-- FORMULARIO EDITAR FIN -->