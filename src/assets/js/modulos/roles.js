//#region [ IMPORTACIONES ] COMIENZO
import {
  enviarFormulario, eliminarRegistro, obtenerDatosRegistro,
  listarDataTable, cargarInputsActualizarQNR, validarEnTiempoReal
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
import { driverAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"

//#endregion [ IMPORTACIONES ] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {
  await listarDataTable({
    encabezados: {
      "id_rol": "ID DEL ROL",
      "nombre_rol": "NOMBRE DEL ROL",
    },
    informacionPe: {
      'modulo': 'roles',
      'datosPe': {
        'accion': 'listar'
      }
    },
    campoIdBtn: 'id_rol',
    botones: 'CRUD',
  });
  driverAyuda('roles', {
    pasos:
      [
        {
          element: 'button[data-bs-target=".modalRegistrar"]',
          popover: {
            title: 'Registrar Rol',
            description: 'Haz clic aquí para agregar un nuevo rol al sistema. Los roles son asignados a los usuarios',
            side: 'bottom',
            align: 'start'
          }
        },
        {
          element: '.tabla-ajax',
          popover: {
            title: 'Lista de Roles',
            description: 'Aquí puedes ver todos los roles registrados del sistema.',
            side: 'top'
          }
        },
        {
          element: '.botonEditar',
          popover: {
            title: 'Editar Roles',
            description: 'Modifica el nombre de cualquier rol haciendo clic en este botón.',
            side: 'left'
          }
        },
        {
          element: '.botonEliminar',
          popover: {
            title: 'Eliminar Roles',
            description: 'Elimina roles que ya no sean necesarios.',
            side: 'left'
          }
        },
        {
          popover: {
            title: '¡Ayuda completada!',
            description: 'Ya conoces la gestión de roles. Da click en finaliar para acabar la ayuda.',
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
    'modulo': 'roles'
  })
});

//Evento para el envío de formularios
$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
  e.preventDefault();
  eliminarRegistro({
    boton: this,
    campoId: 'id_rol',
    modulo: 'roles',
  });
});

//Evento para los botones de editar
$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
  e.preventDefault();
  await obtenerDatosRegistro({
    boton: this,
    campoId: 'id_rol',
    modulo: 'roles',
  });
  cargarInputsActualizarQNR.call($($(this).attr('data-bs-target')).find('form'));
});

//Evento para validar en tiempo real
$(document).off('input', '.validar input, .validar select')
$(document).on('input', '.validar input, .validar select', function () {
  validarEnTiempoReal(this, 'roles');
})
//#endregion [DELEGACIÓN DE EVENTOS] FIN