import {
    listarDataTable,pedirDatosAjax, instanciasDatatable,enviarFormulario
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';




// Array global para almacenar items de la compra
let itemsCompra = [];
let itemsData = []; // Almacena datos completos de items cargados (debe ser array)

// Inicializar DataTable al cargar el documento
$(document).on('DOMContentLoaded', async function (e) {
    await listarDataTable({
        'encabezados': {
            "id_compra": "ID",
            "fecha_compra": "FECHA",
            "razon_social_proveedor": "PROVEEDOR",
            "tipo": "TIPO",
            "articulo": "ARTÍCULO",
            "cantidad": "CANTIDAD",
        },
        informacionPe:{
            'modulo': 'compras',
            'datosPe':{
                'accion':'listar'
            }
        }
    });
});


// Cargar proveedores al abrir el modal
$(".modalRegistrar").on("show.bs.modal", async function () {
    // Limpiar array de items
    itemsCompra = [];
    actualizarTablaItems();

    await cargarProveedores();
    // Cargar items del tipo por defecto (Materia Prima)
    await cargarItems("materia_prima");
});

// Cargar proveedores
async function cargarProveedores() {
    try {
        const instrucciones = {
            'modulo': 'compras',
            'datosPe': { 
                'accion': 'seleccionarUno', id_producto:'4' 
            }
        };
 
        const proveedores = await pedirDatosAjax(instrucciones);

        const selectProveedor = $(".selectProveedor");
        selectProveedor.empty();
        selectProveedor.append('<option value="">Seleccione Razon Social...</option>');

        if (proveedores && Array.isArray(proveedores)) {
            proveedores.forEach(proveedor => {
                selectProveedor.append(
                    `<option value="${proveedor.rif_proveedor}">${proveedor.razon_social_proveedor}</option>`
                );
            });
        }
    } catch (error) {
        console.error("Error al cargar proveedores:", error);
    }
}

// Cambiar items según tipo seleccionado
$("#formularioAjax").on("submit", async function () {
    enviarFormulario({
        formulario: this,
        modulo:'compras',
        convertirJSON:true,
    });
});

// Cambiar items según tipo seleccionado
$("#tipoItem").on("change", async function () {
    const tipo = $(this).val();
    await cargarItems(tipo);
    // Limpiar campos cuando cambia el tipo
    $("#unidadMedidaDisplay").val("");
    $("#idUnidadMedida").val("");
});

// Cargar items según tipo
async function cargarItems(tipo) {
    try {
        const instrucciones = {
            'modulo': 'compras',
            'datosPe': {
                'accion': 'obtenerItems',
                'tipo': tipo
            }
        };

        const items = await pedirDatosAjax(instrucciones);

        const selectItem = $("#selectItem");
        selectItem.empty();
        selectItem.append('<option value="">Seleccione artículo</option>');

        // Limpiar datos anteriores
        itemsData = [];

        if (items && Array.isArray(items)) {
            // Agregar items según el tipo
            items.forEach(item => {
                console.log("Item del backend:", item); // DEBUG: ver estructura completa
                let id, nombre, idUnidadMedida, nombreUnidad;

                switch (tipo) {
                    case 'producto':
                        id = item.id_producto;
                        nombre = item.nombre_producto;
                        idUnidadMedida = item.id_unidad_medida;
                        nombreUnidad = item.nombre_unidad_medida || item.simbolo_unidad_medida || 'UNIDAD';
                        break;
                    case 'insumo':
                        id = item.id_insumo;
                        nombre = item.nombre_insumo;
                        // Insumos no tienen id_unidad_medida en la BD, usar valor por defecto
                        idUnidadMedida = 1; // ID de "UNIDAD" por defecto
                        nombreUnidad = 'UNIDAD';
                        break;
                    case 'materia_prima':
                        id = item.id_materia_prima;
                        nombre = item.nombre_materia_prima;
                        idUnidadMedida = item.id_unidad_medida;
                        nombreUnidad = item.nombre_unidad_medida || item.simbolo_unidad_medida || 'UNIDAD';
                        break;
                }

                console.log("Procesado:", { id, nombre, idUnidadMedida, nombreUnidad }); // DEBUG

                // Guardar datos completos del item
                itemsData.push({
                    id: id,
                    nombre: nombre,
                    id_unidad_medida: idUnidadMedida,
                    nombre_unidad: nombreUnidad
                });

                selectItem.append(
                    `<option value="${id}">${nombre}</option>`
                );
            });

            console.log("itemsData cargados:", itemsData.length, "items", itemsData);
        }
    } catch (error) {
        console.error("Error al cargar items:", error);
    }
}

// Cuando se selecciona un artículo, mostrar su unidad de medida
$("#selectItem").on("change", function () {
    const itemId = $(this).val();
    console.log("Item seleccionado ID:", itemId);
    console.log("itemsData disponibles:", itemsData);

    if (itemId) {
        // Buscar el item en los datos almacenados
        const item = itemsData.find(i => i.id == itemId);
        console.log("Item encontrado:", item);

        if (item) {
            // Mostrar la unidad de medida
            $("#unidadMedidaDisplay").val(item.nombre_unidad);
            $("#idUnidadMedida").val(item.id_unidad_medida);
            console.log("Unidad asignada:", item.nombre_unidad, item.id_unidad_medida);
        } else {
            console.warn("Item no encontrado en itemsData");
        }
    } else {
        // Limpiar si no hay selección
        $("#unidadMedidaDisplay").val("");
        $("#idUnidadMedida").val("");
    }
});

// Actualizar DataTable
function actualizarDataTableCompras() {
    if (instanciasDatatable && instanciasDatatable.length > 0) {
        instanciasDatatable.forEach(instancia => {
            if (instancia.table().node().classList.contains('tabla-ajax')) {
                instancia.ajax.reload(null, false);
                console.log('DataTable actualizado');
            }
        });
    }
}

// Enviar formulario
$(document).off('submit', '.formularioAjax');
$(document).on("submit", ".formularioAjax", async function (e) {
    e.preventDefault();

    const form = $(this);
    const accion = form.find("input[name='accion']").val();

    // Si es el formulario de actualización, usar los selectores correctos
    if (accion === "actualizar") {
        const idCompra = form.find("#idCompraActualizar").val() || form.find("input[name='id_compra']").val();
        const rifProveedor = form.find(".selectProveedorAct").val();
        const cedulaUsuario = form.find(".selectResponsableAct").val();
        const fechaCompra = form.find("#fechaCompraAct").val() || form.find("input[name='fecha_compra']").val();

        if (!rifProveedor) {
            Swal.fire("Error", "Debe seleccionar un proveedor", "error");
            return;
        }

        if (!cedulaUsuario) {
            Swal.fire("Error", "Debe seleccionar un responsable", "error");
            return;
        }

        // Preparar datos para actualización
        const datos = {
            accion: "actualizar",
            id_compra: idCompra,
            rif_proveedor: rifProveedor,
            cedula_usuario: cedulaUsuario,
            fecha_compra: fechaCompra
        };

        try {
            const instrucciones = {
                'modulo': 'compras',
                'datosPe': datos
            };

            const resultado = await pedirDatosAjax(instrucciones);

            if (resultado.tipo === "recargar") {
                Swal.fire({
                    title: resultado.titulo,
                    text: resultado.texto,
                    icon: resultado.icono,
                    confirmButtonText: "Aceptar"
                }).then(() => {
                    $(".modalActualizar").modal("hide");
                    actualizarDataTableCompras();
                });
            } else {
                Swal.fire(resultado.titulo, resultado.texto, resultado.icono);
            }
        } catch (error) {
            console.error("Error al actualizar:", error);
            Swal.fire("Error", "Error al procesar la solicitud: " + error.message, "error");
        }
        return;
    }
});

// ========== FUNCIONES PARA MÚLTIPLES ITEMS ==========

// Función auxiliar para obtener el label del tipo
function getTipoLabel(tipo) {
    const labels = {
        'producto': 'Producto',
        'insumo': 'Insumo',
        'materia_prima': 'Materia Prima'
    };
    return labels[tipo] || tipo;
}

// Función para limpiar campos del formulario de item
function limpiarCamposItem() {
    $("#selectItem").val("").trigger('change');
    $("#unidadMedidaDisplay").val("");
    $("#idUnidadMedida").val("");
    $("#cantidadItem").val("1");
}

// Función para actualizar la tabla de items
function actualizarTablaItems() {
    const tbody = $("#itemsAgregados");
    tbody.empty();

    if (itemsCompra.length === 0) {
        // Mostrar fila vacía
        tbody.append(`
            <tr id="filaVacia">
                <td colspan="5" class="text-center text-muted py-4">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p class="mb-0">No hay items agregados</p>
                    <small>Agregue items usando el formulario de arriba</small>
                </td>
            </tr>
        `);
        $("#btnRegistrarCompra").hide();
    } else {
        // Mostrar items
        itemsCompra.forEach((item, index) => {
            const fila = `
                <tr>
                    <td>${getTipoLabel(item.tipo)}</td>
                    <td>${item.nombre}</td>
                    <td>${item.unidad}</td>
                    <td>${item.cantidad}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-danger btnEliminarItemTabla" data-index="${index}" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(fila);
        });
        $("#btnRegistrarCompra").show();
    }

    // Actualizar contador
    $("#contadorItems").text(itemsCompra.length);
}

// Event handler para agregar item
$(document).on("click", "#btnAgregarItem", function (e) {
    e.preventDefault();

    // Obtener valores
    const tipo = $("#tipoItem").val();
    const idItem = $("#selectItem").val();
    const nombreItem = $("#selectItem option:selected").text();
    const unidad = $("#unidadMedidaDisplay").val();
    const idUnidad = $("#idUnidadMedida").val();
    const cantidad = parseFloat($("#cantidadItem").val());

    // Validaciones
    if (!tipo) {
        Swal.fire("Error", "Debe seleccionar un tipo de inventario", "error");
        return;
    }

    if (!idItem) {
        Swal.fire("Error", "Debe seleccionar un artículo", "error");
        return;
    }

    if (!idUnidad) {
        Swal.fire("Error", "La unidad de medida no está disponible", "error");
        return;
    }

    if (!cantidad || cantidad <= 0) {
        Swal.fire("Error", "La cantidad debe ser mayor a 0", "error");
        return;
    }

    // Crear objeto item
    const item = {
        tipo: tipo,
        id: idItem,
        nombre: nombreItem,
        unidad: unidad,
        id_unidad_medida: idUnidad,
        cantidad: cantidad
    };

    // Agregar al array
    itemsCompra.push(item);

    // Actualizar tabla
    actualizarTablaItems();

    // Limpiar campos
    limpiarCamposItem();

    // Mensaje de éxito
    Swal.fire({
        icon: 'success',
        title: 'Item agregado',
        text: `${nombreItem} agregado correctamente`,
        timer: 1500,
        showConfirmButton: false
    });
});

// Event handler para eliminar item de la tabla
$(document).on("click", ".btnEliminarItemTabla", function () {
    const index = $(this).data("index");
    const item = itemsCompra[index];

    Swal.fire({
        title: '¿Eliminar item?',
        text: `¿Está seguro de eliminar "${item.nombre}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            itemsCompra.splice(index, 1);
            actualizarTablaItems();
            Swal.fire({
                icon: 'success',
                title: 'Eliminado',
                text: 'Item eliminado correctamente',
                timer: 1500,
                showConfirmButton: false
            });
        }
    });
});

// Event handler para registrar compra completa
$(document).on("click", "#btnRegistrarCompra", async function (e) {
    e.preventDefault();

    // Validar que haya items
    if (itemsCompra.length === 0) {
        Swal.fire("Error", "Debe agregar al menos un artículo", "error");
        return;
    }

    // Validar proveedor y fecha
    const rifProveedor = $(".selectProveedor").val();
    const fechaCompra = $("input[name='fecha_compra']").val();

    if (!rifProveedor) {
        Swal.fire("Error", "Debe seleccionar un proveedor", "error");
        return;
    }

    if (!fechaCompra) {
        Swal.fire("Error", "Debe ingresar la fecha de compra", "error");
        return;
    }

    // Preparar datos
    const datos = {
        accion: "registrar",
        rif_proveedor: rifProveedor,
        fecha_compra: fechaCompra,
        detalles: itemsCompra
    };

    try {
        const instrucciones = {
            'modulo': 'compras',
            'datosPe': datos
        };

        const resultado = await pedirDatosAjax(instrucciones);

        if (resultado.tipo === "recargar") {
            Swal.fire({
                title: resultado.titulo,
                text: resultado.texto,
                icon: resultado.icono,
                confirmButtonText: "Aceptar"
            }).then(() => {
                // Limpiar todo
                itemsCompra = [];
                actualizarTablaItems();
                $(".formularioAjax")[0].reset();
                $(".modalRegistrar").modal("hide");
                actualizarDataTableCompras();
            });
        } else {
            Swal.fire(resultado.titulo, resultado.texto, resultado.icono);
        }
    } catch (error) {
        console.error("Error completo:", error);
        console.error("Mensaje:", error.message);
        if (error.response) {
            console.error("Response:", error.response);
        }
        Swal.fire("Error", "Error al procesar la solicitud: " + error.message, "error");
        console.error("Error:", error);
    }
});

// Limpiar formulario al cerrar modal
$(".modalRegistrar").on("hidden.bs.modal", function () {
    $(".formularioAjax")[0].reset();
    $("#selectItem").empty().append('<option value="">Seleccione artículo</option>');
});

// Cargar datos en modal de edición cuando se hace clic en el botón editar
$(document).on("click", ".botonEditar", async function (e) {
    e.preventDefault();
    const idCompra = $(this).attr("value");

    try {
        // Obtener datos de la compra primero
        const instrucciones = {
            'modulo': 'compras',
            'datosPe': {
                'accion': 'obtener',
                'id_compra': idCompra
            }
        };

        const compra = await pedirDatosAjax(instrucciones);

        if (compra && !compra.tipo) {
            // Cargar proveedores y usuarios en el modal de actualización
            await cargarProveedoresActualizar();
            await cargarUsuariosActualizar();

            // Pequeño delay para asegurar que el DOM se actualice
            setTimeout(() => {
                // Llenar el formulario con los datos
                $("#idCompraActualizar").val(compra.id_compra);
                $(".selectProveedorAct").val(compra.rif_proveedor).trigger('change');
                $(".selectResponsableAct").val(compra.cedula_usuario).trigger('change');

                // Formatear fecha para datetime-local
                if (compra.fecha_compra) {
                    // Formato: YYYY-MM-DDTHH:MM
                    const fechaOriginal = compra.fecha_compra.replace(' ', 'T');
                    if (fechaOriginal.length > 16) {
                        $("#fechaCompraAct").val(fechaOriginal.slice(0, 16));
                    } else {
                        $("#fechaCompraAct").val(fechaOriginal);
                    }
                }
            }, 100);
        } else if (compra && compra.tipo === "simple") {
            Swal.fire(compra.titulo, compra.texto, compra.icono);
        }
    } catch (error) {
        console.error("Error al cargar datos de la compra:", error);
        Swal.fire("Error", "No se pudieron cargar los datos de la compra", "error");
    }
});

// Cargar proveedores para modal actualizar
async function cargarProveedoresActualizar() {
    try {
        const instrucciones = {
            'modulo': 'compras',
            'datosPe': { 'accion': 'obtenerProveedores' }
        };

        const proveedores = await pedirDatosAjax(instrucciones);

        const selectProveedor = $(".selectProveedorAct");
        selectProveedor.empty();
        selectProveedor.append('<option value="">Seleccione Razon Social...</option>');

        if (proveedores && Array.isArray(proveedores)) {
            proveedores.forEach(proveedor => {
                selectProveedor.append(
                    `<option value="${proveedor.rif_proveedor}">${proveedor.razon_social_proveedor}</option>`
                );
            });
        }
    } catch (error) {
        console.error("Error al cargar proveedores:", error);
    }
}

// Cargar usuarios para modal actualizar
async function cargarUsuariosActualizar() {
    try {
        const instrucciones = {
            'modulo': 'usuarios',
            'datosPe': { 'accion': 'listar' }
        };

        const usuarios = await pedirDatosAjax(instrucciones);

        const selectResponsable = $(".selectResponsableAct");
        selectResponsable.empty();
        selectResponsable.append('<option value="">Seleccione responsable...</option>');

        if (usuarios && Array.isArray(usuarios)) {
            usuarios.forEach(usuario => {
                selectResponsable.append(
                    `<option value="${usuario.cedula_usuario}">${usuario.usuario_usuario}</option>`
                );
            });
        }
    } catch (error) {
        console.error("Error al cargar usuarios:", error);
    }
}

// Limpiar modal de actualización al cerrar
$(".modalActualizar").on("hidden.bs.modal", function () {
    $(this).find("form")[0].reset();
});

// ========== ELIMINAR COMPRAS DESHABILITADO ==========
// REQUISITO: Las compras NO se pueden eliminar, solo editar
// Esto mantiene la integridad del historial de compras
/*
// Handler para eliminar compra
$(document).on("click", ".botonEliminar", async function (e) {
    e.preventDefault();
    const idCompra = $(this).attr("value");

    // Mostrar confirmación con SweetAlert2
    const result = await Swal.fire({
        title: '¿Está seguro?',
        text: "Esta acción eliminará la compra. Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
        try {
            const instrucciones = {
                'modulo': 'compras',
                'datosPe': {
                    'accion': 'eliminar',
                    'id_compra': idCompra
                }
            };

            const resultado = await pedirDatosAjax(instrucciones);

            if (resultado.tipo === "recargar") {
                Swal.fire({
                    title: resultado.titulo,
                    text: resultado.texto,
                    icon: resultado.icono,
                    confirmButtonText: "Aceptar"
                }).then(() => {
                    actualizarDataTableCompras();
                });
            } else {
                Swal.fire(resultado.titulo, resultado.texto, resultado.icono);
            }
        } catch (error) {
            console.error("Error al eliminar:", error);
            Swal.fire("Error", "Error al eliminar la compra: " + error.message, "error");
        }
    }
});
*/

