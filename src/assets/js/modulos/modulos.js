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
      "id_modulo": "ID DEL MÓDULO",
      "nombre_modulo": "NOMBRE DEL MÓDULO",
    },
    informacionPe: {
      'modulo': 'modulos',
      'datosPe': {
        'accion': 'listar'
      }
    },
    campoIdBtn: 'id_modulo',
    botones: 'CRUD',
  });
  driverAyuda('modulos', {
    pasos: [
      {
        element: 'button[data-bs-target=".modalRegistrar"]',
        popover: {
          title: 'Registrar Módulo',
          description: 'Haz clic aquí para agregar un nuevo módulo al sistema. Los módulos representan las secciones principales de la aplicación.',
          side: 'bottom',
          align: 'start'
        }
      },
      {
        element: '.tabla-ajax',
        popover: {
          title: 'Lista de Módulos',
          description: 'Aquí puedes ver todos los módulos registrados en el sistema. Cada módulo puede tener permisos asignados a diferentes roles.',
          side: 'top'
        }
      },
      {
        element: '.botonEditar',
        popover: {
          title: 'Editar Módulo',
          description: 'Modifica el nombre de cualquier módulo haciendo clic en este botón.',
          side: 'left'
        }
      },
      {
        element: '.botonEliminar',
        popover: {
          title: 'Eliminar Módulo',
          description: 'Elimina módulos que ya no sean necesarios. Ten cuidado porque esto puede afectar los permisos asignados.',
          side: 'left'
        }
      },
      {
        popover: {
          title: '¡Ayuda completada!',
          description: 'Ya conoces la gestión de módulos. Los módulos son la base para la asignación de permisos en el sistema.',
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
    'modulo': 'modulos'
  })
});

//Evento para el envío de formularios
$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
  e.preventDefault();
  eliminarRegistro({
    boton: this,
    campoId: 'id_modulo',
    modulo: 'modulos',
  });
});

//Evento para los botones de editar
$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
  e.preventDefault();
  await obtenerDatosRegistro({
    boton: this,
    campoId: 'id_modulo',
    modulo: 'modulos',
  });
  cargarInputsActualizarQNR.call($($(this).attr('data-bs-target')).find('form'));
});

//Evento para validar en tiempo real
$(document).off('input', '.validar input, .validar select')
$(document).on('input', '.validar input, .validar select', function () {
  validarEnTiempoReal(this, 'modulos');
})
//#endregion [DELEGACIÓN DE EVENTOS] FIN