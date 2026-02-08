//#region [ IMPORTACIONES ] COMIENZO
import {
    enviarFormulario, eliminarRegistro, obtenerDatosRegistro,
    listarDataTable, cargarInputsActualizarQNR, extraerDatosAjax,validarEnTiempoReal
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
//#endregion [ IMPORTACIONES ] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {

    await listarDataTable({
        encabezados: {
            "id_presentacion": "ID",
            "nombre_presentacion": "NOMBRE",
            "cantidad_pmp": "CANTIDAD",
            "nombre_unidad_medida": "UNIDAD DE MEDIDA",
        },
        informacionPe: {
            'modulo': 'presentaciones',
            'datosPe': {
                'accion': 'listar'
            }
        },
        campoIdBtn: 'id_presentacion',
        botones: 'CRUD',
    });

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

//Evento para el envío de formularios
$(document).off('submit', '.formularioAjax');
$(document).on('submit', '.formularioAjax', function (e) {
    e.preventDefault();
    enviarFormulario({
        'formulario': this,
        'modulo': 'presentaciones'
    })
});

//Evento para el envío de formularios
$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
    e.preventDefault();
    eliminarRegistro({
        boton: this,
        campoId: 'id_presentacion',
        modulo: 'presentaciones',
    });
});

//Evento para los botones de editar
$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
    e.preventDefault();
    await obtenerDatosRegistro({
        boton: this,
        campoId: 'id_presentaciones',
        modulo: 'presentaciones',
    });
    cargarInputsActualizarQNR.call($($(this).attr('data-bs-target')).find('form'));
});

//Evento para validar en tiempo real
$(document).off('input blur', '.validar input, .validar select')
$(document).on('input blur', '.validar input, .validar select', function () {
    validarEnTiempoReal(this,'presentaciones');
})
//#endregion [DELEGACIÓN DE EVENTOS] FIN

