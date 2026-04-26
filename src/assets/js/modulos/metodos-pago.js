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
      "id_metodo_pago": "ID",
      "nombre_metodo_pago": "NOMBRE",
      "necesita_moneda": "MONEDA",
      "necesita_banco_emisor": "B. EMISOR",
      "necesita_banco_receptor": "B. RECEPTOR",
      "necesita_referencia": "REFERENCIA",
    },
    informacionPe: {
      'modulo': 'metodos-pago',
      'datosPe': { 'accion': 'listar' }
    },
    campoIdBtn: 'id_metodo_pago',
    botones: 'CRUD',
    infoTratoEspecial: {
      necesita_moneda: (info) => { return (info.valor == 1 ? 'SI' : 'NO') },
      necesita_banco_emisor: (info) => { return (info.valor == 1 ? 'SI' : 'NO') },
      necesita_banco_receptor: (info) => { return (info.valor == 1 ? 'SI' : 'NO') },
      necesita_referencia: (info) => { return (info.valor == 1 ? 'SI' : 'NO') },
    },
  });
})

//Evento para el envío de formularios
$(document).off('submit', '.formularioAjax');
$(document).on('submit', '.formularioAjax', function (e) {
  e.preventDefault();
  enviarFormulario({
    'formulario': this,
    'modulo': 'metodos-pago'
  })
});

//Evento para el envío de formularios
$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
  e.preventDefault();
  eliminarRegistro({
    boton: this,
    campoId: 'id_metodo_pago',
    modulo: 'metodos-pago',
  });
});

//Evento para los botones de editar
$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
  e.preventDefault();
  
  // Obtener los datos (esta función ya llena los inputs de texto automáticamente)
  const datos = await obtenerDatosRegistro({ boton: this, campoId: 'id_metodo_pago', modulo: 'metodos-pago' });
  const form = $($(this).attr('data-bs-target')).find('form');
  
  /* // Sincronizar switches
  form.find('input[type="checkbox"]').each(function() {
    const name = $(this).attr('name');
    $(this).prop('checked', datos[name] == 1);
  }); */

  cargarInputsActualizarQNR.call($($(this).attr('data-bs-target')).find('form'));
});

//Evento para validar en tiempo real
$(document).off('input blur', '.validar input, .validar select')
$(document).on('input blur', '.validar input, .validar select', function () {
  validarEnTiempoReal(this, 'metodos-pago');
})
//#endregion [DELEGACIÓN DE EVENTOS] FIN
