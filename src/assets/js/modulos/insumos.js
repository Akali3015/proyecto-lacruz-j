//#region [ IMPORTACIONES ] COMIENZO
import {
    enviarFormulario, eliminarRegistro, obtenerDatosRegistro,
    listarDataTable, cargarInputsActualizarQNR, validarEnTiempoReal
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
//#endregion [ IMPORTACIONES ] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {
    await listarDataTable({
        encabezados: {
            "id_insumo": "ID",
            "nombre_insumo": "NOMBRE",
            "precio_insumo": "PRECIO UNITARIO",
            "stock_insumo": "STOCK",
        },
        informacionPe: {
            'modulo': 'insumos',
            'datosPe': {
                'accion': 'listar'
            }
        },
        campoIdBtn:'id_insumo',
        botones: 'CRUD',
    });
})

$(document).off('submit', '.formularioAjax');
$(document).on('submit', '.formularioAjax', function (e) {
    e.preventDefault();
    enviarFormulario({
        'formulario': this,
        'modulo': 'insumos'
    });
});

$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
    e.preventDefault();
    eliminarRegistro({
        boton: this,
        campoId: 'id_insumo',
        modulo: 'insumos',
    });
});

$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
    e.preventDefault();
    await obtenerDatosRegistro({
        boton: this,
        campoId: 'id_insumo',
        modulo: 'insumos',
    });
    cargarInputsActualizarQNR.call($($(this).attr('data-bs-target')).find('form'));
});

//Evento para validar en tiempo real
$(document).off('input blur', '.validar input, .validar select')
$(document).on('input blur', '.validar input, .validar select', function () {
    validarEnTiempoReal(this, 'insumos');
})
//#endregion [DELEGACIÓN DE EVENTOS] FIN