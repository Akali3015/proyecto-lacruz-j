<?php

use src\config\inc\componentesModelo;

$componente = new componentesModelo();
?>

<input type="hidden" class="nombreVista" value="repartidores">

<?php
$instruccionesLista = [
  'encabezado' => 'Gestionar Repartidores',
  'tituloBtnReg' => 'Registrar Repartidores',
];
echo $componente->listaDataTable($instruccionesLista);
?>

<!-- [ FORMULARIO REGISTRAR ] COMIENZO -->
<div class="modal fade modalRegistrar" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
        <h5 class="modal-title" id="registrarRepartidoresModalLabel">
          <i class="fas fa-user-plus me-2"></i> Registro de Nuevo Repartidor
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form class="formularioAjax validar" method="POST" action="" novalidate>
        <div class="modal-body">
          <div class="row">
            <input type="hidden" name="accion" value="registrar">
            <div class="col-md-6 mb-3">
              <label for="cedula_repartidor" class="form-label">Cédula</label>
              <div class="input-group">
                <select class="input-group-text selectCodigoRIF" name="codigo_rif_cedula_repartidor" required>
                  <option value="V">V</option>
                  <option value="E">E</option>
                  <option value="J">J</option>
                  <option value="G">G</option>
                  <option value="C">C</option>
                  <option value="P">P</option>
                </select>
                <input type="text" class=" form-control noRepetir" name="cedula_repartidor" pattern="<?php echo regexCedulaRif ?>" minlength="<?php echo minRegexCedulaRif ?>" maxlength="<?php echo maxRegexCedulaRif ?>" required>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="nombre_repartidor" class="form-label">Nombre</label>
              <input type="text" class="form-control noRepetir" name="nombre_repartidor" pattern="<?php echo regexNombreObj ?>" minlength="<?php echo minRegexNombreObj ?>" maxlength="<?php echo maxRegexNombreObj ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="apellido_repartidor" class="form-label">Apellido</label>
              <input type="text" class="form-control noRepetir" name="apellido_repartidor" pattern="<?php echo regexNombreObj ?>" minlength="<?php echo minRegexNombreObj ?>" maxlength="<?php echo maxRegexNombreObj ?>" required>
            </div>
            <div class="col-md-12 mb-3">
              <label for="telefono_repartidor" class="form-label">Teléfono</label>
              <input type="text" class="form-control noRepetir" name="telefono_repartidor" pattern="<?php echo regexTelefono ?>" minlength="<?php echo minRegexTelefono ?>" maxlength="<?php echo maxRegexTelefono ?>" required>
            </div>
            <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="fas fa-times me-2"></i> Cancelar
              </button>
              <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                <i class="fas fa-save me-2"></i> Guardar Repartidor
              </button>
            </div>
          </div>
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
          <i class="fas fa-edit me-2"></i> Editar Repartidor
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form class="formularioAjax validar" method="POST" action="" novalidate>
        <div class="modal-body">
          <div class="row">
            <input type="hidden" name="accion" value="actualizar">
            <div class="col-md-6 mb-3 d-none">
              <label for="cedula_repartidor" class="form-label">Cédula</label>
              <div class="input-group">
                <select class="input-group-text selectCodigoRIF" name="codigo_rif_cedula_repartidor" required>
                  <option value="V">V</option>
                  <option value="E">E</option>
                  <option value="J">J</option>
                  <option value="G">G</option>
                  <option value="C">C</option>
                  <option value="P">P</option>
                </select>
                <input type="text" class=" form-control noRepetir formularioActualizar" name="cedula_repartidor" pattern="<?php echo regexCedulaRif ?>" minlength="<?php echo minRegexCedulaRif ?>" maxlength="<?php echo maxRegexCedulaRif ?>" required>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="nombre_repartidor" class="form-label">Nombre</label>
              <input type="text" class="form-control formularioActualizar noRepetir" name="nombre_repartidor" pattern="<?php echo regexNombreObj ?>" minlength="<?php echo minRegexNombreObj ?>" maxlength="<?php echo maxRegexNombreObj ?>" required>
            </div>
            <div class="col-md-12 mb-3">
              <label for="apellido_repartidor" class="form-label">Apellido</label>
              <input type="text" class="form-control formularioActualizar noRepetir" name="apellido_repartidor" pattern="<?php echo regexNombreObj ?>" minlength="<?php echo minRegexNombreObj ?>" maxlength="<?php echo maxRegexNombreObj ?>" required>
            </div>
            <div class="col-md-12 mb-3">
              <label for="telefono_repartidor" class="form-label">Teléfono</label>
              <input type="text" class="form-control formularioActualizar noRepetir" name="telefono_repartidor" pattern="<?php echo regexTelefono ?>" minlength="<?php echo minRegexTelefono ?>" maxlength="<?php echo maxRegexTelefono ?>" required>
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
<!-- FORMULARIO EDITAR FIN -->