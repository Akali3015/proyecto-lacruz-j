//#region [ IMPORTACIONES ] COMIENZO
import {
    enviarFormulario, eliminarRegistro, obtenerDatosRegistro,
    listarDataTable, cargarInputsActualizarQNR, extraerDatosAjax,validarEnTiempoReal
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
//#endregion [ IMPORTACIONES ] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {
    listarDataTable({
        encabezados: {
            id_servicio: 'ID',
            nombre_servicio: 'NOMBRE',
            costo_servicio: 'COSTO',
            nombre_unidad_medida: 'UNIDAD DE MEDIDA',
        },
        informacionPe: {
            'modulo': 'servicios',
            'datosPe': {
                'accion': 'listar'
            }
        },
        campoIdBtn: 'id_servicio',
        botones: 'CRUD',
    });
    extraerDatosAjax({
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
    })
})

$(document).off('submit', '.formularioAjax');
$(document).on('submit', '.formularioAjax', function (e) {
    e.preventDefault();
    enviarFormulario({
        'formulario': this,
        'modulo': 'servicios'
    })
});

$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
    e.preventDefault();
    eliminarRegistro({
        boton: this,
        campoId: 'id_servicio',
        modulo: 'servicios',
    });
});

$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
    e.preventDefault();
    await obtenerDatosRegistro({
        boton: this,
        campoId: 'id_servicio',
        modulo: 'servicios',
    });
    cargarInputsActualizarQNR.call($($(this).attr('data-bs-target')).find('form'));
});

//Evento para validar en tiempo real
$(document).off('input blur', '.validar input, .validar select')
$(document).on('input blur', '.validar input, .validar select', function () {
    validarEnTiempoReal(this,'servicios');
})
//#endregion [DELEGACIÓN DE EVENTOS] FIN

