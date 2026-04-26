//#region [ IMPORTACIONES ] COMIENZO
import {
  enviarFormulario, eliminarRegistro, obtenerDatosRegistro,
  listarDataTable, cargarInputsActualizarQNR, validarEnTiempoReal,

} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
//#endregion [ IMPORTACIONES ] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {
  await listarDataTable({
    encabezados: {
      "cedula_repartidor": "CÉDULA",
      "nombre_repartidor": "NOMBRE",
      "apellido_repartidor": "APELLIDO",
      "telefono_repartidor": "TELÉFONO",
    },
    informacionPe: {
      'modulo': 'repartidores',
      'datosPe': {
        'accion': 'listar'
      }
    },
    campoIdBtn: 'cedula_repartidor',
    botones: 'CRUD',
  });
})

//Evento para el envío de formularios
$(document).off('submit', '.formularioAjax');
$(document).on('submit', '.formularioAjax', function (e) {
  e.preventDefault();
  enviarFormulario({
    'formulario': this,
    'modulo': 'repartidores',
  });
});

//Evento para el envío de formularios
$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
  e.preventDefault();
  eliminarRegistro({
    boton: this,
    campoId: 'cedula_repartidor',
    modulo: 'repartidores',
  });
});

//Evento para los botones de editar
$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
  e.preventDefault();
  await obtenerDatosRegistro({
    boton: this,
    campoId: 'cedula_repartidor',
    modulo: 'repartidores',
  });
  cargarInputsActualizarQNR.call($($(this).attr('data-bs-target')).find('form'));

});

//Evento para validar en tiempo real
$(document).off('input blur', '.validar input, .validar select')
$(document).on('input blur', '.validar input, .validar select', function () {
  // validarEnTiempoReal(this, 'repartidores');
})
//#endregion [DELEGACIÓN DE EVENTOS] FIN