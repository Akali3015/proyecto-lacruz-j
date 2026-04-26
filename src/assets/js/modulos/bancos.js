//#region [ IMPORTACIONES ] COMIENZO
import {
  enviarFormulario, eliminarRegistro, obtenerDatosRegistro,
  listarDataTable, cargarInputsActualizarQNR, validarEnTiempoReal
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
//#endregion [ IMPORTACIONES ] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {
  // Inicializar el DataTable con los datos de bancos
  await listarDataTable({
    encabezados: {
      "id_banco": "ID",
      "nombre_banco": "NOMBRE DEL BANCO",
    },
    informacionPe: {
      'modulo': 'bancos',
      'datosPe': {
        'accion': 'listar'
      }
    },
    campoIdBtn: 'id_banco',
    botones: 'CRUD',
  });

  // Agregamos la clase 'noRepetir' al campo de nombre para activar la validación de unicidad
  $('input[name="nombre_banco"]').addClass('noRepetir');
});

// Evento para el envío de formularios (Registro y Actualización)
$(document).off('submit', '.formularioAjax');
$(document).on('submit', '.formularioAjax', function (e) {
  e.preventDefault();
  enviarFormulario({
    'formulario': this,
    'modulo': 'bancos',
  });
});

// Evento para la eliminación de registros
$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
  e.preventDefault();
  eliminarRegistro({
    boton: this,
    campoId: 'id_banco',
    modulo: 'bancos',
  });
});

// Evento para los botones de editar (Cargar datos en el modal)
$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
  e.preventDefault();
  const modalTarget = $(this).attr('data-bs-target');
  const form = $(modalTarget).find('form');

  await obtenerDatosRegistro({
    boton: this,
    campoId: 'id_banco',
    modulo: 'bancos',
  });
  // Aseguramos que en el formulario de edición también tenga las clases necesarias
  form.find('input[name="nombre_banco"]').addClass('noRepetir formularioActualizar');
  // Prepara la validación para campos que no deben repetirse en la actualización
  cargarInputsActualizarQNR.call(form);
  cargarInputsActualizarQNR.call($($(this).attr('data-bs-target')).find('form'));
});

// Evento para validar en tiempo real según los patrones definidos
$(document).off('input blur', '.validar input, .validar select');
$(document).on('input blur', '.validar input, .validar select', function () {
  validarEnTiempoReal(this, 'bancos');
});
//#endregion [DELEGACIÓN DE EVENTOS] FIN