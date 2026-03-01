//#region [ IMPORTACIONES ] COMIENZO
import {
    enviarFormulario, listarDataTable, pedirDatosAjax
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
//#endregion [ IMPORTACIONES ] FIN

//#region [VARIABLES GLOBALES] COMIENZO
let proveedoresOptionsCache = '';
let itemsCache = {
    'materia_prima': null,
    'producto': null,
    'insumo': null
};
let itemsCompra = []; // Array global para almacenar items temporales
//#endregion [VARIABLES GLOBALES] FIN

//#region [FUNCIONES PROPIAS DEL MODULO] COMIENZO

// Carga inicial de proveedores para cachear las opciones
async function cargarOpcionesProveedores() {
    try {
        let proveedores = await pedirDatosAjax({
            modulo: 'proveedores',
            noGuardarLocal: true,
            datosPe: { accion: 'listar' }
        });

        let opciones = '<option value="">Seleccionar proveedor...</option>';

        if (Array.isArray(proveedores)) {
            if (proveedores.length === 0) {
                // alert("Advertencia: No hay proveedores registrados.");
            }
            proveedores.forEach(p => {
                // Soporte para mayúsculas/minúsculas y diferentes llaves posibles
                let rif = p.rif_proveedor || p.RIF_PROVEEDOR || p.id_proveedor;
                let razon = p.razon_social_proveedor || p.RAZON_SOCIAL_PROVEEDOR || p.nombre_proveedor;

                if (rif && razon) {
                    opciones += `<option value="${rif}">${razon}</option>`;
                }
            });
        } else {
            console.error("Error: Proveedores no es array", proveedores);
            // alert("Error al cargar proveedores. Revise la consola.");
        }

        proveedoresOptionsCache = opciones;

        // Actualizar selects existentes (si el modal ya estaba abierto)
        $('.selectProveedorFila').html(opciones);

    } catch (e) {
        console.error("Error fatal en cargarOpcionesProveedores:", e);
        proveedoresOptionsCache = '<option value="">Error de conexión</option>';
    }
}

// Carga items por tipo y retorna las opciones HTML
// Carga items por tipo y retorna las opciones HTML
async function obtenerOpcionesItems(tipo, seleccionado = null) {
    // Si no está en caché, buscar datos
    if (!itemsCache[tipo]) {
        let modulo = tipo === 'producto' ? 'productos' :
            tipo === 'insumo' ? 'insumos' : 'materiasPrimas';

        let items = await pedirDatosAjax({
            modulo: modulo,
            noGuardarLocal: true,
            datosPe: { accion: 'listar' }
        });

        // Guardar en caché el array de datos, validando que sea array
        itemsCache[tipo] = Array.isArray(items) ? items : [];
    }

    // Construir HTML usando los datos en caché
    let opcionesHtml = '<option value="">Seleccione artículo</option>';
    let items = itemsCache[tipo];

    if (items.length > 0) {
        items.forEach(item => {
            let id, nombre, idUnidadMedida, nombreUnidad;

            switch (tipo) {
                case 'producto':
                    id = item.id_producto;
                    nombre = item.nombre_producto;
                    idUnidadMedida = item.id_unidad_medida;
                    nombreUnidad = item.nombre_unidad_medida;
                    break;
                case 'materia_prima':
                    id = item.id_materia_prima;
                    nombre = item.nombre_materia_prima;
                    idUnidadMedida = item.id_unidad_medida;
                    nombreUnidad = item.nombre_unidad_medida;
                    break;
                case 'insumo':
                    id = item.id_insumo;
                    nombre = item.nombre_insumo;
                    idUnidadMedida = 1;
                    nombreUnidad = 'UNIDAD';
                    break;
            }

            // Comparación laxa (==) por si string vs number
            let selectedAttr = (seleccionado && id == seleccionado) ? 'selected' : '';

            opcionesHtml += `<option value="${id}" 
                data-unidad-id="${idUnidadMedida}" 
                data-unidad-nombre="${nombreUnidad}" ${selectedAttr}>
                ${nombre}
            </option>`;
        });
    }

    return opcionesHtml;
}

// Agregar una nueva fila al grid (CSS Grid Version)
async function agregarFila() {
    let contenedor = $('#contenedorItems');
    let index = $('.fila-compra').length;

    // Asegurar que hay proveedores cargados
    if (!proveedoresOptionsCache) {
        await cargarOpcionesProveedores();
    }

    // HTML de la fila adaptado a CSS Grid
    let filaHtml = `
        <div class="grid-compras-row fila-compra">
            <!-- Proveedor -->
            <div>
                <select class="form-select selectProveedorFila" name="proveedor_${index}">
                    ${proveedoresOptionsCache}
                </select>
            </div>

            <!-- Tipo -->
            <div>
                <select class="form-select selectTipoFila">
                    <option value="materia_prima">Materia Prima</option>
                    <option value="producto">Producto</option>
                    <option value="insumo">Insumo</option>
                </select>
            </div>
            
            <!-- Artículo -->
            <div>
                <select class="form-select selectArticuloFila">
                    <option value="">Cargando...</option>
                </select>
            </div>
            
            <!-- Unidad -->
            <div>
                <input type="text" class="form-control inputUnidadFila" disabled 
                    style="background-color: #f8f9fa;">
                <input type="hidden" class="inputUnidadIdFila">
            </div>
            
            <!-- Cantidad -->
            <div>
                <input type="number" class="form-control inputCantidadFila" step="0.01" min="0.01" value="1">
            </div>
            
            <!-- Acciones -->
            <div class="text-center">
                <button type="button" class="btn-borrar btnEliminarFila" title="Eliminar línea">
                    <span style="font-size: 1.5rem; font-weight: bold; line-height: 1; margin-top: -3px;">&times;</span>
                </button>
            </div>
        </div>
    `;

    let nuevaFila = $(filaHtml);
    contenedor.append(nuevaFila);

    // Heredar el proveedor de la primera fila si ya hay alguno seleccionado
    let proveedorActual = $('.selectProveedorFila').not(nuevaFila.find('.selectProveedorFila')).first().val();
    if (proveedorActual) {
        nuevaFila.find('.selectProveedorFila').val(proveedorActual);
    }

    let itemSelect = nuevaFila.find('.selectArticuloFila');
    let opciones = await obtenerOpcionesItems('materia_prima');
    itemSelect.html(opciones);

    actualizarContador();
    actualizarBloqueoProveedor();

    // Auto-scroll al fondo
    let scrollContainer = $('.grid-compras-container');
    if (scrollContainer.length) {
        scrollContainer.scrollTop(scrollContainer[0].scrollHeight);
    }
}


// Renderizar Tabla de Items (Usado en Edición)
async function actualizarTablaItems() {
    let contenedor = $('#contenedorItems');
    contenedor.empty();

    if (!itemsCompra || itemsCompra.length === 0) {
        agregarFila(); // Si no hay items, agregar una fila vacía
        return;
    }

    // Asegurar proveedores cargados
    if (!proveedoresOptionsCache) {
        await cargarOpcionesProveedores();
    }

    // Iterar y crear filas
    for (let i = 0; i < itemsCompra.length; i++) {
        let item = itemsCompra[i];
        let index = i;

        // Pre-seleccionar Proveedor reemplazando string
        let pId = String(item.proveedorId || '').trim();
        let provOptions = proveedoresOptionsCache.replace(`value="${pId}"`, `value="${pId}" selected`);

        let filaHtml = `
            <div class="grid-compras-row fila-compra">
                <!-- Proveedor -->
                <div>
                    <select class="form-select selectProveedorFila" name="proveedor_${index}">
                        ${provOptions}
                    </select>
                </div>


                <!-- Tipo -->
                <div>
                    <select class="form-select selectTipoFila">
                        <option value="materia_prima" ${item.tipo === 'materia_prima' ? 'selected' : ''}>Materia Prima</option>
                        <option value="producto" ${item.tipo === 'producto' ? 'selected' : ''}>Producto</option>
                        <option value="insumo" ${item.tipo === 'insumo' ? 'selected' : ''}>Insumo</option>
                    </select>
                </div>
                
                <!-- Artículo -->
                <div>
                    <select class="form-select selectArticuloFila">
                        <!-- Se llenará dinámicamente -->
                        <option value="">Cargando...</option>
                    </select>
                </div>
                
                <!-- Unidad -->
                <div>
                    <input type="text" class="form-control inputUnidadFila" disabled 
                        style="background-color: #f8f9fa;" value="${item.nombre_unidad || ''}">
                    <input type="hidden" class="inputUnidadIdFila" value="${item.id_unidad_medida || ''}">
                </div>
                
                <!-- Cantidad -->
                <div>
                    <input type="number" class="form-control inputCantidadFila" step="0.01" min="0.01" value="${item.cantidad}">
                </div>
                
                <!-- Acciones -->
                <div class="text-center">
                    <button type="button" class="btn-borrar btnEliminarFila" title="Eliminar línea">
                        <span style="font-size: 1.5rem; font-weight: bold; line-height: 1; margin-top: -3px;">&times;</span>
                    </button>
                </div>
            </div>
        `;

        let nuevaFila = $(filaHtml);
        contenedor.append(nuevaFila);

        // Setear proveedor usando .val() después de append (más robusto que string replace)
        if (pId) {
            nuevaFila.find('.selectProveedorFila').val(pId);
        }

        // Cargar opciones del artículo ya seleccionadas (Robustez Extrema)
        let selectArticulo = nuevaFila.find('.selectArticuloFila');
        let aId = String(item.id || '').trim();

        // Pasamos el ID para que venga con 'selected' desde el principio
        let opciones = await obtenerOpcionesItems(item.tipo, aId);
        selectArticulo.html(opciones);
    }

    actualizarContador();

    // Auto-scroll al fondo
    let scrollContainer = $('.grid-compras-container');
    if (scrollContainer.length) {
        scrollContainer.scrollTop(scrollContainer[0].scrollHeight);
    }
}

function actualizarContador() {
    let count = $('.fila-compra').length;
    $('#contadorFilas').text(`Items: ${count}`);
}

// Bloquea/Desbloquea el select de proveedor según la cantidad de filas.
// Con 2+ filas el proveedor queda fijo; con 1 fila se puede cambiar.
function actualizarBloqueoProveedor() {
    let filas = $('.fila-compra').length;
    if (filas > 1) {
        // Bloquear todos los selects de proveedor
        $('.selectProveedorFila').prop('disabled', true)
            .css({ 'background-color': '#f8f9fa', 'cursor': 'not-allowed', 'opacity': '0.8' });
    } else {
        // Desbloquear cuando queda solo una fila
        $('.selectProveedorFila').prop('disabled', false)
            .css({ 'background-color': '', 'cursor': '', 'opacity': '' });
    }
}

function inicializarModal() {
    if ($('.modalRegistrar').length === 0) return;

    $('.modalRegistrar').off('show.bs.modal').on('show.bs.modal', async function () {
        let form = $(this).find('form');
        let accion = form.find('input[name="accion"]').val();

        if (accion !== 'actualizar') {
            $('#contenedorItems').empty();
            await cargarOpcionesProveedores();
            await obtenerOpcionesItems('materia_prima');
            await obtenerOpcionesItems('producto');
            await obtenerOpcionesItems('insumo');
            agregarFila();
        }
    });
}
//#endregion [FUNCIONES] FIN

//#region [EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function () {
    await listarDataTable({
        encabezados: {
            "id_compra": "ID",
            "fecha_compra": "FECHA",
            "PROVEEDOR": "PROVEEDOR"
        },
        informacionPe: {
            'modulo': 'compras',
            'datosPe': {
                'accion': 'listar'
            }
        },
        campoIdBtn: 'id_compra',
        botones: function (info) {
            let id = info['fila']['id_compra'];
            let boton = '<ul class="list-inline me-auto mb-0">';

            // Botón Editar (Solo Super Usuario)
            if (window.ROL_USUARIO == 1) {
                boton += `
                <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Editar">
                    <a href="#" value="${id}" class="botonEditar avtar avtar-xs btn-link-success btn-pc-default">
                        <i class="fi fi-rs-pen-circle fs-3 iconoCentrado"></i>
                    </a>
                </li>`;
            }

            // Botón Ver (Para todos)
            boton += `
            <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Ver Detalles">
                <a href="#" value="${id}" class="botonVer avtar avtar-xs btn-link-info btn-pc-default">
                    <i class="fi fi-rs-eye fs-3 iconoCentrado"></i>
                </a>
            </li>`;

            boton += '</ul>';
            return boton;
        }
    });

    inicializarModal();
});

$(document).on('click', '#btnAgregarFila', function () {
    agregarFila();
});

$(document).on('click', '#btnLimpiarFormulario', function () {
    resetearFormularioCompra();
});

$(document).on('change', '.selectTipoFila', async function () {
    let fila = $(this).closest('.fila-compra');
    let tipo = $(this).val();
    let selectArticulo = fila.find('.selectArticuloFila');
    let inputUnidad = fila.find('.inputUnidadFila');
    let inputUnidadId = fila.find('.inputUnidadIdFila');

    let opciones = await obtenerOpcionesItems(tipo);
    selectArticulo.html(opciones);

    inputUnidad.val('');
    inputUnidadId.val('');
});

$(document).on('change', '.selectArticuloFila', function () {
    let fila = $(this).closest('.fila-compra');
    let selectedOption = $(this).find('option:selected');

    let unidadId = selectedOption.data('unidad-id');
    let unidadNombre = selectedOption.data('unidad-nombre');

    fila.find('.inputUnidadFila').val(unidadNombre || '');
    fila.find('.inputUnidadIdFila').val(unidadId || '');
});

$(document).on('click', '.btnEliminarFila', function () {
    let filas = $('.fila-compra').length;
    if (filas > 1) {
        $(this).closest('.fila-compra').remove();
        actualizarContador();
        actualizarBloqueoProveedor();
    } else {
        let fila = $(this).closest('.fila-compra');
        fila.find('select').each(function () {
            if ($(this).hasClass('selectTipoFila')) {
                $(this).val('materia_prima');
            } else {
                $(this).val('');
            }
        });

        fila.find('.selectTipoFila').trigger('change');

        fila.find('.inputUnidadFila').val('');
        fila.find('.inputUnidadIdFila').val('');
        fila.find('.inputCantidadFila').val('1');
        // Con solo 1 fila, habilitar proveedor
        actualizarBloqueoProveedor();
    }
});

// Enviar formulario (RECOLECCIÓN DE DATOS DE LA GRID)
$(document).on('submit', '.formularioAjax', async function (e) {
    e.preventDefault();

    let detalles = [];
    let itemsInvalidos = 0;

    $('.fila-compra').each(function () {
        let fila = $(this);
        let proveedorId = fila.find('.selectProveedorFila').val();
        let tipo = fila.find('.selectTipoFila').val();
        let itemId = fila.find('.selectArticuloFila').val();
        let itemNombre = fila.find('.selectArticuloFila option:selected').text();
        let unidadId = fila.find('.inputUnidadIdFila').val();
        let cantidad = parseFloat(fila.find('.inputCantidadFila').val());

        if (!itemId) {
            itemsInvalidos++;
            return;
        }

        if (!proveedorId) {
            itemsInvalidos++;
            return;
        }

        if (cantidad <= 0) {
            itemsInvalidos++;
            return;
        }

        detalles.push({
            proveedorId: proveedorId,
            tipo: tipo,
            id: itemId,
            nombre: itemNombre,
            cantidad: cantidad,
            id_unidad_medida: unidadId
        });
    });

    if (itemsInvalidos > 0 && detalles.length === 0) {
        Swal.fire('Error', 'Verifique que todos los item tengan proveedor, artículo y cantidad válida.', 'warning');
        return;
    }

    if (detalles.length === 0) {
        Swal.fire('Error', 'Debe agregar al menos un artículo válido', 'warning');
        return;
    }

    $(this).find('input[name="rif_proveedor"]').remove();
    $(this).find('input[name="detalles"]').remove();

    $('<input>', {
        type: 'hidden',
        name: 'rif_proveedor',
        value: detalles[0].proveedorId
    }).appendTo(this);

    $('<input>', {
        type: 'hidden',
        name: 'detalles',
        value: JSON.stringify(detalles)
    }).appendTo(this);

    let respuesta = await enviarFormulario({
        'formulario': this,
        'modulo': 'compras'
    });

    // Limpiar grid si el registro fue exitoso
    if (respuesta && respuesta.icono === 'success') {
        $('#contenedorItems').empty();
        agregarFila(); // Agregar una fila vacía para la siguiente carga
    }
});
// Evento para cargar datos en el modal de registro (Edición)
$(document).on("click", ".botonEditar", function (e) {
    e.preventDefault();
    let id = $(this).attr("value"); // El ID viene en el atributo value del botón
    cargarDatosCompra(id, 'editar');
});

// Evento para ver datos (Modal dedicado de solo lectura)
$(document).on("click", ".botonVer", function (e) {
    e.preventDefault();
    let id = $(this).attr("value");
    verDetallesCompra(id);
});

// Restaurar estado al cerrar el modal (para que el próximo "Registrar" esté limpio)
$('.modalRegistrar').on('hidden.bs.modal', function () {
    resetearFormularioCompra();
});

async function cargarDatosCompra(id, modo = 'editar') {
    try {
        let respuesta = await pedirDatosAjax({
            modulo: 'compras',
            datosPe: {
                accion: 'seleccionarUno',
                id_compra: id
            }
        });

        if (!respuesta || respuesta.length === 0) {
            Swal.fire('Error', 'No se pudieron cargar los datos de la compra', 'error');
            return;
        }

        // 1. Preparar el modal para edición
        let modal = $('.modalRegistrar');
        let form = modal.find('form');

        // Cambiar títulos y acciones
        // Cambiar títulos y acciones según el modo
        if (modo === 'editar') {
            modal.find('.modal-title').html('<i class="fas fa-edit me-2"></i> Actualizar Compra');
            form.find('input[name="accion"]').val('actualizar');
            form.find('button[type="submit"]').show().html('<i class="fas fa-save me-2"></i> Actualizar Todo');
            $('#btnLimpiarFormulario').show();
        } else {
            // MODO VER
            modal.find('.modal-title').html('<i class="fi fi-rs-eye me-2"></i> Detalles de Compra');
            form.find('input[name="accion"]').val(''); // Sin acción
            form.find('button[type="submit"]').hide(); // Ocultar guardar
            $('#btnLimpiarFormulario').hide(); // Ocultar limpiar
        }

        // Habilitar todo inicialmente para asegurar que se puede escribir
        form.find('input, select, button').prop('disabled', false);
        $('#btnAgregarFila').prop('disabled', false);

        // Agregar ID de compra si no existe input hidden
        if (form.find('input[name="id_compra"]').length === 0) {
            $('<input>').attr({
                type: 'hidden',
                name: 'id_compra',
                class: 'formularioActualizar' // Para que se limpie si es necesario
            }).appendTo(form);
        }
        form.find('input[name="id_compra"]').val(id);

        // 2. Cargar Datos de Cabecera (del primer item)
        let header = respuesta[0];

        // Asignar Proveedor (Esperamos a que carguen las opciones si es necesario, pero asumimos que ya están en cache)
        // Si no están, las forzamos
        if ($('.selectProveedorAct').has('option[value="' + header.rif_proveedor + '"]').length == 0) {
            // Si el proveedor no esta en la lista (raro), lo agregamos temporalmente
            $('.selectProveedorAct').append(`<option value="${header.rif_proveedor}">${header.PROVEEDOR}</option>`);
        }
        $('.selectProveedorAct').val(header.rif_proveedor).trigger('change');
        $('.selectProveedorFila').val(header.rif_proveedor); // Sincronizar select de fila

        // Asignar Fecha (convertir formato si es necesario, datetime-local usa YYYY-MM-DDTHH:MM)
        // La DB devuelve 'YYYY-MM-DD HH:MM:SS', quitamos los segundos y reemplazamos espacio por T
        let fechaFormat = header.fecha_compra.replace(' ', 'T').substring(0, 16);
        form.find('input[name="fecha_compra"]').val(fechaFormat);

        // 3. Cargar Items
        itemsCompra = []; // Limpiar array global

        respuesta.forEach(item => {
            itemsCompra.push({
                proveedorId: item.rif_proveedor, // Usamos el del item o header
                proveedorNombre: item.PROVEEDOR,
                tipo: item.TIPO.toLowerCase(), // producto, insumo, materia_prima
                id: item.id_item,
                nombre: item.ARTICULO,
                cantidad: parseFloat(item.cantidad_raw), // Valor crudo numérico
                id_unidad_medida: item.id_unidad_medida,
                nombre_unidad: item.nombre_unidad_medida || 'UNID'
            });
        });

        // 4. Renderizar Tabla
        await actualizarTablaItems();

        // 5. Mostrar Modal
        modal.modal('show');

        // Si es modo ver, deshabilitar todo
        if (modo === 'ver') {
            form.find('input, select, textarea, button').prop('disabled', true);

            // Botones de grid también
            $('.btnEliminarFila').prop('disabled', true).hide();
            $('#btnAgregarFila').prop('disabled', true).hide();

            // Re-habilitar botón cerrar modal
            form.find('button[data-bs-dismiss="modal"]').prop('disabled', false);
        }

    } catch (error) {
        console.error(error);
        Swal.fire('Error', 'Hubo un problema al cargar la compra', 'error');
    }
}

function resetearFormularioCompra() {
    let modal = $('.modalRegistrar');
    let form = modal.find('form');

    // Restaurar a modo registro
    modal.find('.modal-title').html('<i class="fas fa-shopping-cart me-2"></i> Registrar Compra');
    form.find('input[name="accion"]').val('registrar');
    form.find('button[type="submit"]').show().html('<i class="fas fa-save me-2"></i> Guardar Todo');
    $('#btnLimpiarFormulario').show();
    $('#btnAgregarFila').prop('disabled', false);

    form.find('input, select, button').prop('disabled', false);
    $('#btnAgregarFila').prop('disabled', false).show();
    $('#btnLimpiarFormulario').show();

    // Limpiar tabla e items
    itemsCompra = [];
    $('#contenedorItems').empty();

    // Resetear form (valores)
    form[0].reset();

    // Reestablecer fecha actual
    let now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    form.find('input[name="fecha_compra"]').val(now.toISOString().slice(0, 16));

    // Agregar primera fila vacía
    agregarFila();
}

// Nueva función para ver detalles de compra (Modal dedicado de solo lectura)
async function verDetallesCompra(idCompra) {
    try {
        // 1. Obtener datos del backend
        let respuesta = await pedirDatosAjax({
            modulo: 'compras',
            noGuardarLocal: true,
            datosPe: {
                accion: 'seleccionarUno',
                id_compra: idCompra
            }
        });

        if (!respuesta || respuesta.length === 0) {
            Swal.fire('Error', 'No se encontraron datos de la compra', 'error');
            return;
        }

        // 2. Obtener datos del header (primer elemento)
        let header = respuesta[0];

        // 3. Poblar información general
        $('#verIdCompra').text(header.id_compra || '-');

        // Formatear fecha para mostrar
        let fechaFormat = header.fecha_compra ?
            new Date(header.fecha_compra.replace(' ', 'T')).toLocaleString('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }) : '-';
        $('#verFechaCompra').text(fechaFormat);

        // 4. Poblar grid de items (usando mismo estilo que registro)
        let tbody = $('#verItemsBody');
        tbody.empty();

        respuesta.forEach(item => {
            let tipoLabel = item.TIPO === 'materia_prima' ? 'Materia Prima' :
                item.TIPO === 'producto' ? 'Producto' : 'Insumo';

            let row = `
                <div class="grid-compras-row fila-compra" style="background-color: white;">
                    <div>
                        <div class="form-control" style="background-color: #f8f9fa; border: 1px solid #dee2e6;" disabled>
                            ${item.PROVEEDOR || '-'}
                        </div>
                    </div>
                    <div>
                        <div class="form-control" style="background-color: #f8f9fa; border: 1px solid #dee2e6;" disabled>
                            ${tipoLabel}
                        </div>
                    </div>
                    <div>
                        <div class="form-control" style="background-color: #f8f9fa; border: 1px solid #dee2e6;" disabled>
                            ${item.ARTICULO || '-'}
                        </div>
                    </div>
                    <div>
                        <div class="form-control" style="background-color: #f8f9fa; border: 1px solid #dee2e6;" disabled>
                            ${item.nombre_unidad_medida || 'UNID'}
                        </div>
                    </div>
                    <div>
                        <div class="form-control fw-bold" style="background-color: #f8f9fa; border: 1px solid #dee2e6;" disabled>
                            ${parseFloat(item.cantidad_raw).toFixed(2)}
                        </div>
                    </div>
                </div>
            `;
            tbody.append(row);
        });

        // 5. Mostrar modal
        let modal = new bootstrap.Modal(document.getElementById('modalVerCompra'));
        modal.show();

    } catch (error) {
        console.error('Error al cargar detalles de compra:', error);
        Swal.fire('Error', 'Hubo un problema al cargar los detalles de la compra', 'error');
    }
}
//#endregion [EVENTOS] FIN
