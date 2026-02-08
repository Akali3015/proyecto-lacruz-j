//#region [ IMPORTACIONES ] COMIENZO
import {
    listarDataTable, enviarFormulario,validarEnTiempoReal
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
//#endregion [ IMPORTACIONES ] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {
    listarDataTable({
        encabezados: {
            "id_cambio_iva": "ID",
            "monto_cambio_iva": "PORCENTAJE (%)",
            "fecha_cambio_iva": "FECHA",
        },
        informacionPe: {
            'modulo': 'cambiosIva',
            'datosPe': {
                'accion': 'listar'
            }
        },
    });
})

$(document).off('submit', '.formularioAjax');
$(document).on('submit', '.formularioAjax', function (e) {
    e.preventDefault();
    enviarFormulario({
        'formulario': this,
        'modulo': 'cambiosIva'
    });
});

//Evento para validar en tiempo real
$(document).off('input blur', '.validar input, .validar select')
$(document).on('input blur', '.validar input, .validar select', function () {
    validarEnTiempoReal(this,'cambiosIva');
})
//#endregion [DELEGACIÓN DE EVENTOS] FIN