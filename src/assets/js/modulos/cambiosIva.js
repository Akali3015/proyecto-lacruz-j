//#region [ IMPORTACIONES ] COMIENZO
import {
  listarDataTable, enviarFormulario, validarEnTiempoReal, cambiarFormatos
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
import { driverAyuda, mostrarAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"

//#endregion [ IMPORTACIONES ] FIN

//#region [FUNCIONES PROPIAS DEL MÓDULO] COMIENZO

// Función para registrar el tutorial
function registrarTutorial() {
  driverAyuda('cambiosIva', {
    pasos: [
      {
        element: 'button[data-bs-target=".modalRegistrar"]',
        popover: {
          title: 'Actualizar Porcentaje de IVA',
          description: 'Haz clic aquí para actualizar el porcentaje del Impuesto al Valor Agregado (IVA). Este valor afecta los cálculos en facturación y ventas.',
          side: 'bottom',
          align: 'start'
        }
      },
      {
        element: '.tabla-ajax',
        popover: {
          title: 'Historial de Cambios del IVA',
          description: 'Aquí puedes ver todo el historial de cambios realizados al porcentaje del IVA, incluyendo la fecha y el valor aplicado.',
          side: 'top'
        }
      },
      {
        element: '.dataTables_filter input',
        popover: {
          title: 'Buscador',
          description: 'Puedes buscar en el historial de cambios por fecha o porcentaje específico.',
          side: 'bottom',
          align: 'start'
        }
      },
      {
        popover: {
          title: '¡Ayuda completada!',
          description: 'Ya conoces la gestión del IVA. Recuerda que cada cambio queda registrado en el historial para tu referencia.',
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
      "id_cambio_iva": "ID",
      "monto_cambio_iva": "PORCENTAJE",
      "fecha_cambio_iva": "FECHA",
    },
    informacionPe: {
      'modulo': 'cambiosIva',
      'datosPe': {
        'accion': 'listar'
      }
    },
    infoTratoEspecial: {
      monto_cambio_iva: (info) => { return info.valor + '%'; },
      fecha_cambio_iva: (info) => { return cambiarFormatos(info.valor, 'fecha_hora') },
    },
  });
  
  registrarTutorial();
  
  const driverPendiente = sessionStorage.getItem('driver_pendiente');
  if (driverPendiente === 'cambiosIva') {
    sessionStorage.removeItem('driver_pendiente');
    setTimeout(() => {
      mostrarAyuda();
    }, 1000);
  }
});

$(document).off('submit', '.formularioAjax');
$(document).on('submit', '.formularioAjax', function (e) {
  e.preventDefault();
  enviarFormulario({
    'formulario': this,
    'modulo': 'cambiosIva'
  });
});

// Evento para validar en tiempo real
$(document).off('input', '.validar input, .validar select');
$(document).on('input', '.validar input, .validar select', function () {
  validarEnTiempoReal(this, 'cambiosIva');
});
//#endregion [DELEGACIÓN DE EVENTOS] FIN