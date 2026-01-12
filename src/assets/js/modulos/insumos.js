import {
    enviarFormulario, eliminarRegistro, obtenerDatosRegistro, 
    ListarDataTable, cargarInputsActualizarQNR,
    
    encabezados
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';

$(document).on('DOMContentLoaded', async function (e) {
    let instruccionesLista = {
        'encabezados' : encabezados,
        'modulo' : 'insumos'
    }
    await ListarDataTable(instruccionesLista);
})

$(document).off('submit', '.formularioAjax');
$(document).on('submit', '.formularioAjax', function (e) {
    e.preventDefault();
    enviarFormulario.call(this);
});

$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
    e.preventDefault();
    eliminarRegistro.call(this);
});

$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
    e.preventDefault();
    await obtenerDatosRegistro.call(this);
    cargarInputsActualizarQNR.call($($(this).attr('data-bs-target')).find('form'));
});