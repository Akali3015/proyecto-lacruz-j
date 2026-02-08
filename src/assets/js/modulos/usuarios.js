//#region [ IMPORTACIONES ] COMIENZO
import {
    enviarFormulario, eliminarRegistro, obtenerDatosRegistro,
    listarDataTable, extraerDatosAjax, cargarInputsActualizarQNR,validarEnTiempoReal
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
//#endregion [ IMPORTACIONES ] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {
    await listarDataTable({
        encabezados: {
            "cedula_usuario": "CÉDULA",
            "nombre_rol": "ROL",
            "nombre_usuario": "NOMBRE",
            "apellido_usuario": "APELLIDO",
            "telefono_usuario": "TELÉFONO",
            "correo_usuario": "CORREO",
            "usuario_usuario": "USUARIO",
        },
        informacionPe: {
            'modulo': 'usuarios',
            'datosPe': {
                'accion': 'listar'
            }
        },
        campoIdBtn: 'cedula_usuario',
        botones: 'CRUD',
    });
    let instrucciones = {
        'modulosPeticion': ['roles'],
        'accionesPeticion': [{ 'accion': 'listar' }],
        'tipoElemento': ['select'],
        'elementosDestino': [$('.selectRoles')],
        'datosInsertar': [
            {
                'value': 'id_rol',
                'texto': 'nombre_rol',
                'textoDefault': 'Seleccione un rol'
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
        'modulo': 'usuarios'
    })
});

//Evento para el envío de formularios
$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
    e.preventDefault();
    eliminarRegistro({
        boton: this,
        campoId: 'cedula_usuario',
        modulo: 'usuarios',
    });
});

//Evento para los botones de editar
$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
    e.preventDefault();
    await obtenerDatosRegistro({
        boton: this,
        campoId: 'cedula_usuario',
        modulo: 'usuarios',
    });
    cargarInputsActualizarQNR.call($($(this).attr('data-bs-target')).find('form'));
});

//Evento para validar en tiempo real
$(document).off('input blur', '.validar input, .validar select')
$(document).on('input blur', '.validar input, .validar select', function () {
    validarEnTiempoReal(this,'usuarios');
})

//#endregion [DELEGACIÓN DE EVENTOS] FIN
