//#region [ IMPORTACIONES ] COMIENZO
import {
  enviarFormulario, eliminarRegistro, obtenerDatosRegistro,
  listarDataTable, cargarInputsActualizarQNR, validarEnTiempoReal
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
import { driverAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"

//#endregion [ IMPORTACIONES ] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {
  listarDataTable({
    encabezados: {
      "id_permiso": "ID DEL PERMISO",
      "nombre_permiso": "NOMBRE DEL PERMISO",
    },
    informacionPe: {
      'modulo': 'permisos',
      'datosPe': {
        'accion': 'listar'
      }
    },
    campoIdBtn: 'id_permiso',
    botones: 'CRUD',
  });
  driverAyuda('permisos', {
    pasos:
      [
        {
          element: 'button[data-bs-target=".modalRegistrar"]',
          popover: {
            title: 'Registrar Permiso',
            description: 'Haz clic aquí para agregar un nuevo permiso al sistema. Los permisos controlan qué acciones pueden realizar los usuarios.',
            side: 'bottom',
            align: 'start'
          }
        },
        {
          element: '.tabla-ajax',
          popover: {
            title: 'Lista de Permisos',
            description: 'Aquí puedes ver todos los permisos registrados. Cada permiso representa una acción específica (ver, listar, registrar, actualizar, eliminar).',
            side: 'top'
          }
        },
        {
          element: '.botonEditar',
          popover: {
            title: 'Editar Permiso',
            description: 'Modifica el nombre o descripción de cualquier permiso haciendo clic en este botón.',
            side: 'left'
          }
        },
        {
          element: '.botonEliminar',
          popover: {
            title: 'Eliminar Permiso',
            description: 'Elimina permisos que ya no sean necesarios. Ten cuidado porque esto afectará los accesos de los roles.',
            side: 'left'
          }
        },
        {
          popover: {
            title: '¡Ayuda completado!',
            description: 'Ya conoces la gestión de permisos. Recuerda que los permisos se asignan a los roles desde el módulo de Accesos.',
            side: 'top'
          }
        }
      ]
  });
})

//Evento para el envío de formularios
$(document).off('submit', '.formularioAjax');
$(document).on('submit', '.formularioAjax', function (e) {
  e.preventDefault();
  enviarFormulario({
    'formulario': this,
    'modulo': 'permisos'
  })
});

$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
  e.preventDefault();
  eliminarRegistro({
    boton: this,
    campoId: 'id_permiso',
    modulo: 'permisos',
  });
});

//Evento para los botones de editar
$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
  e.preventDefault();
  await obtenerDatosRegistro({
    boton: this,
    campoId: 'id_permiso',
    modulo: 'permisos',
  });
  cargarInputsActualizarQNR.call($($(this).attr('data-bs-target')).find('form'));
});

//Evento para validar en tiempo real
$(document).off('input', '.validar input, .validar select')
$(document).on('input', '.validar input, .validar select', function () {
  validarEnTiempoReal(this, 'permisos');
})
//#endregion [DELEGACIÓN DE EVENTOS] FIN