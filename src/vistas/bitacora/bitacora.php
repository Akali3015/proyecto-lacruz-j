<?php

use src\config\inc\componentesModelo;

$componente = new componentesModelo();
?>
<input type="hidden" class="nombreVista" value="bitacora">
<?php
$instruccionesLista = [
  'encabezado' => 'Bitácora',
];
echo $componente->listaDataTable($instruccionesLista);
?>

<!-- [ MODAL VER CAMBIOS ] COMIENZO -->
<div class="modal fade modalVerCambiosBitacora" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content border-0">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
        <h5 class="modal-title">
          <i class="fi fi-rs-eye me-2"></i> Detalles de los Cambios
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="contenedorCambiosBitacora">
        <div class="text-center text-muted py-5">
          <i class="fi fi-rs-info fs-1 mb-3 d-block"></i>
          <p class="fs-5">Cargando información...</p>
        </div>
      </div>
      <div class="modal-footer justify-content-center" style="background-color: #f8f9fa; border-top: 1px solid #dee2e6;">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-2"></i> Cerrar
        </button>
      </div>
    </div>
  </div>
</div>
<!-- [ MODAL VER CAMBIOS ] FIN -->