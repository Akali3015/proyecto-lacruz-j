//#region [ IMPORTACIONES ] COMIENZO
import {
  listarDataTable, enviarFormulario, extraerDatosAjax, validarEnTiempoReal,
  cambiarFormatos, formateoCampos
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
import { driverAyuda, mostrarAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"

//#endregion [ IMPORTACIONES ] FIN

//#region [FUNCIONES PROPIAS DEL MÓDULO] COMIENZO

function registrarTutorial() {
  driverAyuda('cambios-monedas', {
    pasos: [
      {
        element: 'button[data-bs-target=".modalRegistrar"]',
        popover: {
          title: 'Actualizar Cambio de Moneda',
          description: 'Haz clic aquí para actualizar el valor de una moneda con respecto al Bolívar.',
          side: 'bottom',
          align: 'start'
        }
      },
      {
        element: '.tabla-ajax',
        popover: {
          title: 'Historial de Cambios',
          description: 'Aquí puedes ver todo el historial de cambios realizados a las monedas, incluyendo la fecha y el valor aplicado.',
          side: 'top'
        }
      },
      {
        element: '.dataTables_filter input',
        popover: {
          title: 'Buscador',
          description: 'Puedes buscar en el historial por nombre de moneda, valor o fecha específica.',
          side: 'bottom',
          align: 'start'
        }
      },
      {
        popover: {
          title: '¡Ayuda completada!',
          description: 'Ya conoces la gestión de cambios de monedas. Recuerda que cada cambio queda registrado en el historial.',
          side: 'top'
        }
      }
    ]
  });
}

//#endregion [FUNCIONES PROPIAS DEL MÓDULO] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {
  await listarDataTable({
    encabezados: {
      "id_cambio_moneda": "ID",
      "nombre_moneda": "MONEDA",
      "valor_moneda": "VALOR DEL CAMBIO",
      "fecha_cambio": "FECHA DEL CAMBIO",
    },
    informacionPe: {
      'modulo': 'monedas',
      'datosPe': {
        'accion': 'listarCambios'
      }
    },
    infoTratoEspecial: {
      valor_moneda: (info) => { return info.valor + ' Bs'; },
      fecha_cambio: (info) => { return cambiarFormatos(info.valor, 'fecha_hora') },
    }
  });
  
  await extraerDatosAjax({
    'modulosPeticion': ['monedas'],
    'accionesPeticion': [{ 'accion': 'listar' }],
    'tipoElemento': ['select'],
    'elementosDestino': [$('.selectMonedas')],
    'datosInsertar': [
      {
        'value': 'id_moneda',
        'texto': 'nombre_moneda',
        'textoDefault': 'Seleccione una moneda'
      }
    ]
  });
  
  registrarTutorial();

  const driverPendiente = sessionStorage.getItem('driver_pendiente');
  if (driverPendiente === 'cambios-monedas') {
    sessionStorage.removeItem('driver_pendiente');
    setTimeout(() => {
      mostrarAyuda();
    }, 1000);
  }
});

// Evento para el envío de formularios
$(document).off('submit', '.formularioAjax');
$(document).on('submit', '.formularioAjax', function (e) {
  e.preventDefault();
  enviarFormulario({
    'formulario': this,
    'modulo': 'monedas'
  });
});

// Evento para validar en tiempo real
$(document).off('input', '.validar input, .validar select');
$(document).on('input', '.validar input, .validar select', function () {
  if ($(this).hasClass('inputMonto')) {
    formateoCampos($(this), 'dinero');
  }
  validarEnTiempoReal(this, 'monedas');
});

//#endregion [DELEGACIÓN DE EVENTOS] FIN