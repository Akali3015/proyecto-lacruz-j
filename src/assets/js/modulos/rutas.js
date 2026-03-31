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
      "id_ruta": "ID",
      "nombre_ruta": "NOMBRE",
      "precio_ruta": "PRECIO",
      "minimo_km_ruta": "MÍNIMO KM",
      "maximo_km_ruta": "MÁXIMO KM",
    },
    informacionPe: {
      'modulo': 'rutas',
      'datosPe': {
        'accion': 'listar'
      }
    },
    campoIdBtn: 'id_ruta',
    botones: 'CRUD',
  });
})

//Evento para el envío de formularios
$(document).off('submit', '.formularioAjax');
$(document).on('submit', '.formularioAjax', function (e) {
  e.preventDefault();
  enviarFormulario({
    'formulario': this,
    'modulo': 'rutas'
  })
});

//Evento para el envío de formularios
$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
  e.preventDefault();
  eliminarRegistro({
    boton: this,
    campoId: 'id_ruta',
    modulo: 'rutas',
  });
});

//Evento para los botones de editar
$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
  e.preventDefault();
  await obtenerDatosRegistro({
    boton: this,
    campoId: 'id_ruta',
    modulo: 'rutas',
  });
  cargarInputsActualizarQNR.call($($(this).attr('data-bs-target')).find('form'));
});

//Evento para validar en tiempo real
$(document).off('input blur', '.validar input, .validar select')
$(document).on('input blur', '.validar input, .validar select', function () {
  validarEnTiempoReal(this, 'rutas');
})
//#endregion [DELEGACIÓN DE EVENTOS] FIN