import {
    obtenerDatosRegistro, encabezados, ListarDataTable, cargarInputsActualizarQNR, extraerDatosAjax
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';

$(document).on('DOMContentLoaded', async function (e) {
    let instruccionesLista = {
        'encabezados': encabezados,
        'modulo': 'bitacora'
    }
    await ListarDataTable(instruccionesLista);
})

