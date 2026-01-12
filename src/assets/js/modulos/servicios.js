import {
    enviarFormulario, eliminarRegistro, obtenerDatosRegistro, encabezados,
    ListarDataTable, cargarInputsActualizarQNR, extraerDatosAjax
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';

$(document).on('DOMContentLoaded', async function (e) {
    let instruccionesLista = {
        'encabezados': encabezados,
        'modulo': 'servicios'
    }
    await ListarDataTable(instruccionesLista);

    let instrucciones = {
        'modulosPeticion': ['unidadesMedidas'],
        'accionesPeticion': [{ 'accion': 'listar' }],
        'tipoElemento': ['select'],
        'elementosDestino': [$('.selectUnidadMedida')],
        'datosInsertar': [
            {
                'value': 'id_unidad_medida',
                'texto': 'nombre_unidad_medida',
                'textoDefault': 'Seleccione una unidad de medida'
            }
        ]
    }
    extraerDatosAjax(instrucciones)
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
