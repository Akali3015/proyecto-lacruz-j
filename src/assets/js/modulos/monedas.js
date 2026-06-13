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
  driverAyuda('monedas', {
    pasos:
      [
        {
          element: 'button[data-bs-target=".modalRegistrar"]',
          popover: {
            title: 'Registrar Monedas',
            description: 'Haz clic aquí para registrar una nueva moneda al sistema.',
            side: 'bottom',
            align: 'start'
          }
        },
        {
          element: '.tabla-ajax',
          popover: {
            title: 'Lista de Monedas',
            description: 'Aquí puedes ver todas las monedas registradas en el sistema.',
            side: 'top'
          }
        },
        {
          element: '.botonEditar',
          popover: {
            title: 'Editar Monedas',
            description: 'Modifica el nombre de cualquier moneda haciendo clic en este botón.',
            side: 'left'
          }
        },
        {
          element: '.botonEliminar',
          popover: {
            title: 'Eliminar Monedas',
            description: 'Elimina monedas que ya no sean necesarias en el sistema.',
            side: 'left'
          }
        },
        {
          popover: {
            title: '¡Ayuda completada!',
            description: 'Ya conoces la gestión de monedas. Da click en finaliar para acabar la ayuda.',
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
$(document).off('input', '.validar input, .validar select')
$(document).on('input', '.validar input, .validar select', function () {
  validarEnTiempoReal(this, 'monedas');
})

//#endregion [DELEGACIÓN DE EVENTOS] FIN
