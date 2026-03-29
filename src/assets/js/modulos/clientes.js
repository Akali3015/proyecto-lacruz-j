//#region [ IMPORTACIONES ] COMIENZO
import {
    enviarFormulario, eliminarRegistro, obtenerDatosRegistro,
    listarDataTable, cargarInputsActualizarQNR,validarEnTiempoReal
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
//#endregion [ IMPORTACIONES ] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {
    await listarDataTable({
        encabezados: {
            "rif_cedula_cliente": "CÉDULA",
            "razon_social_cliente": "RAZÓN SOCIAL",
            "telefono_cliente": "TELÉFONO",
            "correo_cliente": "CORREO ELECTRÓNICO",
            "direccion_cliente": "DIRECCIÓN",
        },
        informacionPe: {
            'modulo': 'clientes',
            'datosPe': {
                'accion': 'listar'
            }
        },
        campoIdBtn: 'rif_cedula_cliente',
        botones: 'CRUD',
    });
})

//Evento para el envío de formularios
$(document).off('submit', '.formularioAjax');
$(document).on('submit', '.formularioAjax', function (e) {
    e.preventDefault();
    enviarFormulario({
        'formulario': this,
        'modulo': 'clientes',
    });
});

//Evento para el envío de formularios
$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
    e.preventDefault();
    eliminarRegistro({
        boton: this,
        campoId: 'rif_cedula_cliente',
        modulo: 'clientes',
    });
});

//Evento para los botones de editar
$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
    e.preventDefault();
    await obtenerDatosRegistro({
        boton: this,
        campoId: 'rif_cedula_cliente',
        modulo: 'clientes',
    });
    cargarInputsActualizarQNR.call($($(this).attr('data-bs-target')).find('form'));
});

//Evento para validar en tiempo real
$(document).off('input blur', '.validar input, .validar select, .validar textarea')
$(document).on('input blur', '.validar input, .validar select, .validar textarea', function () {
    validarEnTiempoReal(this,'clientes');
})
//#endregion [DELEGACIÓN DE EVENTOS] FIN