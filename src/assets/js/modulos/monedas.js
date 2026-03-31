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
      "id_moneda": "ID",
      "nombre_moneda": "NOMBRE",
      "simbolo_moneda": "SÍMBOLO",
      "valor_moneda": "VALOR (Bs)",
    },
    informacionPe: {
      'modulo': 'monedas',
      'datosPe': {
        'accion': 'listar'
      }
    },
    campoIdBtn: 'id_moneda',
    botones: 'CRUD',
    infoTratoEspecial: {
      valor_moneda: (info) => { return info.valor + ' Bs'; },
    },
  });
})

//Evento para el envío de formularios
$(document).off('submit', '.formularioAjax');
$(document).on('submit', '.formularioAjax', function (e) {
  e.preventDefault();
  enviarFormulario({
    'formulario': this,
    'modulo': 'monedas'
  })
});

//Evento para el envío de formularios
$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
  e.preventDefault();
  eliminarRegistro({
    boton: this,
    campoId: 'id_moneda',
    modulo: 'monedas',
  });
});

//Evento para los botones de editar
$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
  e.preventDefault();
  await obtenerDatosRegistro({
    boton: this,
    campoId: 'id_moneda',
    modulo: 'monedas',
  });
  cargarInputsActualizarQNR.call($($(this).attr('data-bs-target')).find('form'));
});

//Evento para validar en tiempo real
$(document).off('input blur', '.validar input, .validar select')
$(document).on('input blur', '.validar input, .validar select', function () {
  validarEnTiempoReal(this, 'monedas');
})

//#endregion [DELEGACIÓN DE EVENTOS] FIN
