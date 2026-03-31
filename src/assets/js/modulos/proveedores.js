//#region [ IMPORTACIONES ] COMIENZO
import {
  enviarFormulario, eliminarRegistro, obtenerDatosRegistro,
  listarDataTable, cargarInputsActualizarQNR, validarEnTiempoReal
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
//#endregion [ IMPORTACIONES ] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {
  await listarDataTable({
    encabezados: {
      "rif_proveedor": "RIF",
      "razon_social_proveedor": "RAZON SOCIAL",
      "telefono_proveedor": "TELÉFONO",
      "correo_proveedor": "CORREO ELECTRÓNICO",
      "direccion_proveedor": "DIRECCIÓN",
    },
    informacionPe: {
      'modulo': 'proveedores',
      'datosPe': {
        'accion': 'listar'
      }
    },
    campoIdBtn: 'rif_proveedor',
    botones: 'CRUD',
  });
})

//Evento para el envío de formularios
$(document).off('submit', '.formularioAjax');
$(document).on('submit', '.formularioAjax', function (e) {
  e.preventDefault();
  enviarFormulario({
    'formulario': this,
    'modulo': 'proveedores'
  })
});

//Evento para el envío de formularios
$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
  e.preventDefault();
  eliminarRegistro({
    boton: this,
    campoId: 'rif_proveedor',
    modulo: 'proveedores',
  });
});

//Evento para los botones de editar
$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
  e.preventDefault();
  await obtenerDatosRegistro({
    boton: this,
    campoId: 'rif_proveedor',
    modulo: 'proveedores',
  });
  cargarInputsActualizarQNR.call($($(this).attr('data-bs-target')).find('form'));
});

//Evento para validar en tiempo real
$(document).off('input blur', '.validar input, .validar select, .validar textarea')
$(document).on('input blur', '.validar input, .validar select, .validar textarea', function () {
  validarEnTiempoReal(this, 'proveedores');
})
//#endregion [DELEGACIÓN DE EVENTOS] FIN