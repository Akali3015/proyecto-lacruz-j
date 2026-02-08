//#region [ IMPORTACIONES ] COMIENZO
import {
    enviarFormulario, eliminarRegistro, obtenerDatosRegistro,
    listarDataTable, cargarInputsActualizarQNR, extraerDatosAjax,
    pedirDatosAjax, instanciasDatatable,validarEnTiempoReal
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
//#endregion [ IMPORTACIONES ] FIN

//#region [VARIABLES O CONSTANTES GLOBALES] COMIENZO
let todasLasPresentaciones = [];
let presentacionesCache = {};
//#endregion [VARIABLES O CONSTANTES GLOBALES] FIN

//#region [FUNCIONES PROPIAS DEL MODULO] COMIENZO
function cargarTodasLasPresentacionesEnSegundoPlano() {
    setTimeout(async () => {
        try {
            const instruccionesPe = {
                'modulo': 'presentaciones',
                'datosPe': { 'accion': 'listar' }
            };

            const respuesta = await pedirDatosAjax(instruccionesPe);

            if (respuesta && !respuesta.tipo && Array.isArray(respuesta)) {
                todasLasPresentaciones = respuesta;
                console.log('Presentaciones precargadas en segundo plano:', todasLasPresentaciones.length);
            }
        } catch (error) {
            console.error('Error al precargar presentaciones:', error);
        }
    }, 500);
}
function actualizarDataTableMateriasPrimas() {
    if (instanciasDatatable && instanciasDatatable.length > 0) {
        instanciasDatatable.forEach(instancia => {
            if (instancia.table().node().classList.contains('tabla-ajax')) {
                instancia.ajax.reload(null, false);
                console.log('DataTable actualizado');
            }
        });
    }
}
async function cargarPresentacionesEnModal(modal, presentacionesSeleccionadas = []) {
    try {
        const contenedor = modal.find('.contenedor-presentaciones');
        const inputsOcultos = modal.find('.inputs-ocultos-presentaciones');

        contenedor.html(`
            <div class="col-12">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando presentaciones...</span>
                    </div>
                    <p class="mt-2 text-muted">Cargando presentaciones...</p>
                </div>
            </div>
        `);

        inputsOcultos.empty();

        if (todasLasPresentaciones.length === 0) {
            try {
                const instruccionesPe = {
                    'modulo': 'presentaciones',
                    'datosPe': { 'accion': 'listar' }
                };

                const respuesta = await pedirDatosAjax(instruccionesPe);

                if (respuesta && !respuesta.tipo && Array.isArray(respuesta)) {
                    todasLasPresentaciones = respuesta;
                } else {
                    throw new Error('No se pudieron cargar las presentaciones');
                }
            } catch (error) {
                console.error('Error al cargar presentaciones:', error);
                contenedor.html(`
                    <div class="col-12">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Error al cargar presentaciones. Por favor, recargue la página.
                        </div>
                    </div>
                `);
                return;
            }
        }

        contenedor.empty();

        if (todasLasPresentaciones.length === 0) {
            contenedor.html(`
                <div class="col-12">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        No hay presentaciones registradas.
                    </div>
                </div>
            `);
            return;
        }

        const idsSeleccionados = presentacionesSeleccionadas.map(p => String(p.id_presentacion));
        const fragment = document.createDocumentFragment();

        todasLasPresentaciones.forEach((presentacion) => {
            const idPresentacion = String(presentacion.id_presentacion);
            const nombrePresentacion = presentacion.nombre_presentacion;
            const cantidad = presentacion.cantidad_pmp;
            const unidadMedida = presentacion.nombre_unidad_medida || '';
            const estaSeleccionada = idsSeleccionados.includes(idPresentacion);

            const divCol = document.createElement('div');
            divCol.className = 'col-md-4 mb-3';
            divCol.innerHTML = `
                <div class="form-check card-presentacion ${estaSeleccionada ? 'border-primary bg-light' : ''} p-3 border rounded">
                    <input class="form-check-input checkbox-presentacion" 
                           type="checkbox" 
                           id="presentacion_${idPresentacion}"
                           value="${idPresentacion}"
                           ${estaSeleccionada ? 'checked' : ''}>
                    <label class="form-check-label w-100" for="presentacion_${idPresentacion}">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>${nombrePresentacion}</strong>
                            <span class="badge bg-info">${cantidad} ${unidadMedida}</span>
                        </div>
                    </label>
                </div>
            `;

            fragment.appendChild(divCol);

            const inputOculto = `
                <input type="hidden" 
                       name="presentaciones[]" 
                       class="hidden-presentacion"
                       id="hidden_presentacion_${idPresentacion}" 
                       value="${estaSeleccionada ? idPresentacion : ''}" 
                       ${estaSeleccionada ? '' : 'disabled'}>
            `;
            inputsOcultos.append(inputOculto);
        });

        contenedor.append(fragment);

    } catch (error) {
        console.error('Error al cargar presentaciones:', error);
        const contenedor = modal.find('.contenedor-presentaciones');
        contenedor.html(`
            <div class="col-12">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error al cargar presentaciones: ${error.message}
                </div>
            </div>
        `);
    }
}
async function obtenerPresentacionesSeleccionadas(idMateriaPrima) {
    if (presentacionesCache[idMateriaPrima]) {
        return presentacionesCache[idMateriaPrima];
    }

    try {
        const instrucciones = {
            'modulo': 'materiasPrimas',
            'datosPe': {
                'accion': 'listarPresentaciones',
                'id_materia_prima': idMateriaPrima
            }
        };

        const respuesta = await pedirDatosAjax(instrucciones);
        if (respuesta && !respuesta.tipo && Array.isArray(respuesta)) {
            presentacionesCache[idMateriaPrima] = respuesta;
            return respuesta;
        }
        return [];

    } catch (error) {
        console.error('Error al obtener presentaciones seleccionadas:', error);
        return [];
    }
}
function inicializarEventosPresentacionesModal(modal) {
    modal.off('change', '.checkbox-presentacion');

    modal.on('change', '.checkbox-presentacion', function () {
        const idPresentacion = $(this).val();
        const estaMarcado = $(this).is(':checked');
        const inputOculto = modal.find(`#hidden_presentacion_${idPresentacion}`);

        if (estaMarcado) {
            inputOculto.val(idPresentacion).prop('disabled', false);
            $(this).closest('.card-presentacion').addClass('bg-light border-primary');
        } else {
            inputOculto.val('').prop('disabled', true);
            $(this).closest('.card-presentacion').removeClass('bg-light border-primary');
        }
    });

    modal.find('.btn-seleccionar-todas').off('click').on('click', function () {
        modal.find('.checkbox-presentacion').prop('checked', true).trigger('change');
    });

    modal.find('.btn-deseleccionar-todas').off('click').on('click', function () {
        modal.find('.checkbox-presentacion').prop('checked', false).trigger('change');
    });
}
//#endregion [FUNCIONES PROPIAS DEL MODULO] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {
    await listarDataTable({
        encabezados: {
            "id_materia_prima": "ID",
            "nombre_unidad_medida": "UNIDAD DE MEDIDA",
            "nombre_materia_prima": "NOMBRE",
            "stock_materia_prima": "STOCK",
            "costo_materia_prima": "COSTO",
        },
        informacionPe: {
            'modulo': 'materiasPrimas',
            'datosPe': {
                'accion': 'listar'
            }
        },
        campoIdBtn: 'id_materia_prima',
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
    extraerDatosAjax(instrucciones);

    cargarTodasLasPresentacionesEnSegundoPlano();
})

$(document).off('submit', '.formularioAjax');
$(document).on('submit', '.formularioAjax', function (e) {
    e.preventDefault();

    enviarFormulario({
        'formulario': this,
        'modulo': 'materiasPrimas'
    }).then(() => {
        actualizarDataTableMateriasPrimas();
    }).catch(error => {
        console.error('Error al enviar formulario:', error);
    });
});

$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
    e.preventDefault();
    eliminarRegistro({
        boton: this,
        campoId: 'id_materia_prima',
        modulo: 'materiasPrimas',
    }).then(() => {
        actualizarDataTableMateriasPrimas();
    }).catch(error => {
        console.error('Error al eliminar:', error);
    });
});

$(document).on('show.bs.modal', '.modalRegistrar', async function () {
    const modal = $(this);

    await cargarPresentacionesEnModal(modal);
    inicializarEventosPresentacionesModal(modal);
});

$(document).on('show.bs.modal', '.modalActualizar', async function () {
    const modal = $(this);
    const idMateriaPrima = modal.find('input[name="id_materia_prima"]').val();

    if (idMateriaPrima) {
        const presentacionesSeleccionadas = await obtenerPresentacionesSeleccionadas(idMateriaPrima);
        await cargarPresentacionesEnModal(modal, presentacionesSeleccionadas);
        inicializarEventosPresentacionesModal(modal);
    }
});

$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
    e.preventDefault();
    const idMateriaPrima = $(this).attr('value');
    const modalTarget = $(this).attr('data-bs-target');
    const modal = $(modalTarget);
    modal.find('input[name="id_materia_prima"]').val(idMateriaPrima);
    await obtenerDatosRegistro({
        boton: this,
        campoId: 'id_materia_prima',
        modulo: 'materiasPrima',
    });
    delete presentacionesCache[idMateriaPrima];
    modal.modal('show');
    modal.one('shown.bs.modal', function () {
        cargarInputsActualizarQNR.call(modal.find('form'));
    });
});

$(document).on('hidden.bs.modal', '.modalActualizar', function () {
    const modal = $(this);
    const idMateriaPrima = modal.find('input[name="id_materia_prima"]').val();

    if (idMateriaPrima) {
        delete presentacionesCache[idMateriaPrima];
    }
});

//Evento para validar en tiempo real
$(document).off('input blur', '.validar input, .validar select')
$(document).on('input blur', '.validar input, .validar select', function () {
    validarEnTiempoReal(this,'materiasPrimas');
})
//#endregion [DELEGACIÓN DE EVENTOS] FIN
