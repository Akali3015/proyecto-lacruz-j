//#region [ IMPORTACIONES ] COMIENZO
import {
  listarDataTable, enviarFormulario, extraerDatosAjax, validarEnTiempoReal,
  cambiarFormatos, formateoCampos
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
//#endregion [ IMPORTACIONES ] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {
  listarDataTable({
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
  extraerDatosAjax({
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
  })
})

//Evento para el envío de formularios
$(document).off('submit', '.formularioAjax');
$(document).on('submit', '.formularioAjax', function (e) {
  e.preventDefault();
  enviarFormulario({
    'formulario': this,
    'modulo': 'monedas'
  });
});

//Evento para validar en tiempo real
$(document).off('input', '.validar input, .validar select')
$(document).on('input', '.validar input, .validar select', function () {
  if($(this).hasClass('inputMonto')){
    formateoCampos($(this), 'dinero')
  }
  validarEnTiempoReal(this, 'monedas');
})

//#endregion [DELEGACIÓN DE EVENTOS] FIN
