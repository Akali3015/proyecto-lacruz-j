import {
    ListarDataTable, encabezados,
    pedirDatosAjax, instanciasDatatable
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';

// Inicializar DataTable al cargar el documento
$(document).on('DOMContentLoaded', async function (e) {
    let instruccionesLista = {
        'encabezados': encabezados,
        'modulo': 'compras'
    }
    await ListarDataTable(instruccionesLista);

    // Cargar unidades de medida
    await cargarUnidadesMedida();

    // Cargar usuarios (responsables)
    await cargarUsuarios();
});

// Cargar unidades de medida
async function cargarUnidadesMedida() {
    try {
        const instrucciones = {
            'modulo': 'unidadesMedidas',
            'datosPe': { 'accion': 'listar' }
        };

        const unidades = await pedirDatosAjax(instrucciones);

        const selectUnidad = $(".selectUnidadMedida");
        selectUnidad.empty();
        selectUnidad.append('<option value="">Seleccione unidad...</option>');

        if (unidades && Array.isArray(unidades)) {
            unidades.forEach(unidad => {
                selectUnidad.append(
                    `<option value="${unidad.id_unidad_medida}">${unidad.nombre_unidad_medida}</option>`
                );
            });
        }
    } catch (error) {
        console.error("Error al cargar unidades de medida:", error);
    }
}

// Cargar usuarios (responsables)
async function cargarUsuarios() {
    try {
        const instrucciones = {
            'modulo': 'usuarios',
            'datosPe': { 'accion': 'listar' }
        };

        const usuarios = await pedirDatosAjax(instrucciones);

        const selectResponsable = $(".selectResponsable");
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

// Cargar proveedores al abrir el modal
$(".modalRegistrar").on("show.bs.modal", async function () {
    await cargarProveedores();
    // Cargar items del tipo por defecto (Materia Prima)
    await cargarItems("materia_prima");
});

// Cargar proveedores
async function cargarProveedores() {
    try {
        const instrucciones = {
            'modulo': 'compras',
            'datosPe': { 'accion': 'obtenerProveedores' }
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
$("#tipoItem").on("change", async function () {
    const tipo = $(this).val();
    await cargarItems(tipo);
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

        if (items && Array.isArray(items)) {
            // Agregar items según el tipo
            items.forEach(item => {
                let id, nombre;

                switch (tipo) {
                    case 'producto':
                        id = item.id_producto;
                        nombre = item.nombre_producto;
                        break;
                    case 'insumo':
                        id = item.id_insumo;
                        nombre = item.nombre_insumo;
                        break;
                    case 'materia_prima':
                        id = item.id_materia_prima;
                        nombre = item.nombre_materia_prima;
                        break;
                }

                selectItem.append(
                    `<option value="${id}">${nombre}</option>`
                );
            });
        }
    } catch (error) {
        console.error("Error al cargar items:", error);
    }
}

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

    // Formulario de registro (código original)
    const tipo = $("#tipoItem").val();
    const idItem = $("#selectItem").val();
    const cantidad = $("#cantidadItem").val();
    const rifProveedor = $(".selectProveedor").val();
    const fechaCompra = $("input[name='fecha_compra']").val();
    const cedulaUsuario = $(".selectResponsable").val();
    const idUnidadMedida = $(".selectUnidadMedida").val();

    if (!rifProveedor) {
        Swal.fire("Error", "Debe seleccionar un proveedor", "error");
        return;
    }

    if (!cedulaUsuario) {
        Swal.fire("Error", "Debe seleccionar un responsable", "error");
        return;
    }

    if (!idItem) {
        Swal.fire("Error", "Debe seleccionar un artículo", "error");
        return;
    }

    if (!idUnidadMedida) {
        Swal.fire("Error", "Debe seleccionar una unidad de medida", "error");
        return;
    }

    if (cantidad <= 0) {
        Swal.fire("Error", "La cantidad debe ser mayor a 0", "error");
        return;
    }

    // Preparar datos
    const datos = {
        accion: "registrar",
        rif_proveedor: rifProveedor,
        cedula_usuario: cedulaUsuario,
        fecha_compra: fechaCompra,
        detalles: [
            {
                tipo: tipo,
                id: idItem,
                cantidad: cantidad,
                id_unidad_medida: idUnidadMedida
            }
        ]
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
                $(".modalRegistrar").modal("hide");
                $(".formularioAjax")[0].reset();
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
