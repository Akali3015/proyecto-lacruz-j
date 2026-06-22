//#region [ IMPORTACIONES ] COMIENZO
import {
    listarDataTable, cambiarFormatos, pedirDatosAjax,
    alertasAjax, reiniciarDataTables,
    mostrarOcultarSpinnerCarga
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
import { driverAyuda, mostrarAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"

//#endregion [ IMPORTACIONES ] FIN

//#region [ FUNCIONES PROPIAS DEL MODULO ] COMIENZO

function registrarTutorial() {
    driverAyuda('ordenesServicios', {
        pasos: [
            {
                element: '.tabla-ajax',
                popover: {
                    title: 'Listado de Órdenes',
                    description: 'Aquí puedes ver todas las órdenes de servicio generadas desde las facturas de los clientes.',
                    side: 'top'
                }
            },
            {
                element: '.btnVerOrdenServicio',
                popover: {
                    title: 'Ver Detalles',
                    description: 'Haz clic aquí para ver todos los detalles de la orden (solo lectura).',
                    side: 'left'
                }
            },
            {
                element: '.btnGestionarOrden',
                popover: {
                    title: 'Gestionar Orden',
                    description: 'Desde aquí puedes cambiar el estado y/o reprogramar la fecha de ejecución.',
                    side: 'left'
                }
            },
            {
                element: '.dataTables_filter input',
                popover: {
                    title: 'Buscador',
                    description: 'Puedes buscar órdenes por número de orden, cliente o servicio.',
                    side: 'bottom',
                    align: 'start'
                }
            },
            {
                popover: {
                    title: '¡Ayuda completada!',
                    description: 'Ya conoces la gestión de órdenes de servicio.',
                    side: 'top'
                }
            }
        ]
    });
}

function getStatusInfo(status, estaRetrasado = false) {
    if (estaRetrasado) status = 3;
    const statusMap = {
        1: { texto: 'Pendiente', color: 'bg-warning text-dark' },
        2: { texto: 'Ejecutado', color: 'bg-success' },
        3: { texto: 'Retrasado', color: 'bg-secondary' },
        4: { texto: 'Cancelado', color: 'bg-danger' }
    };
    return statusMap[status] || { texto: 'Desconocido', color: 'bg-secondary' };
}

let ordenActualGlobal = null;

function renderizarProductosRequeridos(productos, cantidadServicio) {
    if (!productos || productos.length === 0) {
        return '<tr><td colspan="5" class="text-center">Este servicio no requiere productos</td></tr>';
    }
    
    let html = '';
    for (const prod of productos) {
        const cantidadTotal = parseFloat(prod.cantidad_producto) * parseFloat(cantidadServicio);
        const stockActual = parseFloat(prod.stock_producto || 0);
        const hayStock = stockActual >= cantidadTotal;
        const estadoStock = hayStock 
            ? '<span class="badge bg-success">Stock suficiente</span>'
            : '<span class="badge bg-danger">Stock insuficiente</span>';
        
        html += `
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="me-2"><i class="fi fi-rs-box fs-5"></i></div>
                        <div><strong>${prod.nombre_producto || prod.id_producto}</strong></div>
                    </div>
                </td>
                <td class="text-center">${parseFloat(prod.cantidad_producto).toFixed(2)}</td>
                <td class="text-center">${cantidadTotal.toFixed(2)}</td>
                <td class="text-center">${stockActual.toFixed(2)}</td>
                <td class="text-center">${estadoStock}</td>
            </tr>
        `;
    }
    return html;
}

async function verDetallesOrden() {
    const idOrden = $(this).attr('value');
    const modal = $('.modalDetallesOrden');
    
    mostrarOcultarSpinnerCarga('mostrar');
    
    const orden = await pedirDatosAjax({
        modulo: 'ordenesServicios',
        datosPe: {
            accion: 'listar',
            id_servicio_factura: idOrden
        }
    });
    
    mostrarOcultarSpinnerCarga('ocultar');
    
    if (orden.icono === 'error') {
        alertasAjax(orden);
        return;
    }
    
    modal.find('.id_servicio_factura').text(orden.id_servicio_factura);
    modal.find('.id_orden_entrega_presupuesto').text(orden.id_orden_entrega_presupuesto);
    modal.find('.nombre_servicio').text(orden.nombre_servicio);
    modal.find('.cantidad_servicio').text(orden.cantidad_servicio);
    modal.find('.precio_servicio').text(`$${parseFloat(orden.precio_unitario_real).toFixed(2)}`);
    
    const subtotal = parseFloat(orden.subtotal_real);
    modal.find('.subtotal_servicio').text(`$${subtotal.toFixed(2)}`);
    modal.find('.fecha_ejecucion').text(cambiarFormatos(orden.fecha_ejecucion, 'fecha'));
    
    const statusInfo = getStatusInfo(orden.status_orden, orden.esta_retrasado);
    modal.find('.status_badge_orden')
        .removeClass('bg-warning bg-success bg-secondary bg-danger')
        .addClass(statusInfo.color)
        .text(statusInfo.texto);
    
    // Datos del cliente
    modal.find('.rif_cedula_cliente').text(orden.rif_cedula_cliente);
    modal.find('.razon_social_cliente').text(orden.razon_social_cliente);
    modal.find('.telefono_cliente').text(orden.telefono_cliente);
    modal.find('.correo_cliente').text(orden.correo_cliente);
    modal.find('.direccion_cliente').text(orden.direccion_cliente || 'No especificada');
    
    // Ubicación
    modal.find('.nombre_ruta').text(orden.nombre_ruta || 'No asignada');
    modal.find('.coordenadas').text(`${orden.coordenada_latitud}, ${orden.coordenada_longitud}`);
    modal.find('.url_direccion_btn').attr('href', orden.url_direccion);
    
    // Productos
    const productosHTML = renderizarProductosRequeridos(orden.productos_requeridos, orden.cantidad_servicio);
    modal.find('.cuerpoProductosRequeridos').html(productosHTML);
    
    modal.modal('show');
}

async function abrirModalGestion() {
    const idOrden = $(this).attr('value');
    
    mostrarOcultarSpinnerCarga('mostrar');
    
    const orden = await pedirDatosAjax({
        modulo: 'ordenesServicios',
        datosPe: {
            accion: 'listar',
            id_servicio_factura: idOrden
        }
    });
    
    mostrarOcultarSpinnerCarga('ocultar');
    
    if (orden.icono === 'error') {
        alertasAjax(orden);
        return;
    }
    
    ordenActualGlobal = orden;
    
    const modal = $('.modalGestionarOrden');
    const statusActual = orden.esta_retrasado ? 3 : orden.status_orden;
    
    // Información resumen
    modal.find('.info_id_orden').text(orden.id_servicio_factura);
    modal.find('.info_nombre_servicio').text(orden.nombre_servicio);
    modal.find('.info_razon_social_cliente').text(orden.razon_social_cliente);
    
    const statusInfoActual = getStatusInfo(orden.status_orden, orden.esta_retrasado);
    modal.find('.info_status_actual')
        .removeClass('bg-warning bg-success bg-secondary bg-danger')
        .addClass(statusInfoActual.color)
        .text(statusInfoActual.texto);
  
    const selectStatus = modal.find('.selectNuevoStatus');
    selectStatus.empty();
    selectStatus.append('<option value="">Seleccione un estado...</option>');
    selectStatus.prop('disabled', false);
    
    if (statusActual === 1 || statusActual === 3) {
        selectStatus.append('<option value="1">Pendiente</option>');
        selectStatus.append('<option value="2">Ejecutado</option>');
        selectStatus.append('<option value="4">Cancelado</option>');
    } else if (statusActual === 2) {
        selectStatus.append('<option value="2" selected>Ejecutado</option>');
        selectStatus.prop('disabled', true);
    } else if (statusActual === 4) {
        selectStatus.append('<option value="4" selected>Cancelado</option>');
        selectStatus.prop('disabled', true);
    }
    
    const contenedorFecha = modal.find('#contenedorFechaEjecucion');
    const inputFecha = modal.find('.nueva_fecha_ejecucion');
    contenedorFecha.hide();
    inputFecha.prop('disabled', true);
    inputFecha.val('');
    
    modal.find('.id_orden_gestion').val(orden.id_servicio_factura);
    modal.modal('show');
}

function toggleFechaPorStatus() {
    const contenedorFecha = $('#contenedorFechaEjecucion');
    const inputFecha = $('.nueva_fecha_ejecucion');
    const statusSeleccionado = $(this).val();
    
    if (statusSeleccionado === '1') { 
        contenedorFecha.show();
        inputFecha.prop('disabled', false);
        const hoy = new Date().toISOString().split('T')[0];
        inputFecha.attr('min', hoy);
        if (ordenActualGlobal && ordenActualGlobal.fecha_ejecucion) {
            const fechaActual = ordenActualGlobal.fecha_ejecucion.split(' ')[0];
            inputFecha.val(fechaActual);
        }
        $('#mensajeStatusInfo').text('');
    } else {
        contenedorFecha.hide();
        inputFecha.prop('disabled', true);
        inputFecha.val('');
        if (statusSeleccionado === '2') {
            $('#mensajeStatusInfo').text('Al marcar como ejecutado, se descontará el stock automáticamente.');
        } else if (statusSeleccionado === '4') {
            $('#mensajeStatusInfo').text('Al cancelar, se devolverá el stock automáticamente.');
        } else {
            $('#mensajeStatusInfo').text('');
        }
    }
}

async function enviarActualizacionOrden(e) {
    e.preventDefault();
    
    const formulario = $(this);
    const idOrden = formulario.find('.id_orden_gestion').val();
    const nuevoStatus = formulario.find('.selectNuevoStatus').val();
    const nuevaFecha = formulario.find('.nueva_fecha_ejecucion').val();
    
    if (!nuevoStatus) {
        alertasAjax({
            tipo: 'simple',
            titulo: 'Estado requerido',
            texto: 'Debe seleccionar un nuevo estado para la orden',
            icono: 'warning'
        });
        return;
    }
    
    if (parseInt(nuevoStatus) === 2 && ordenActualGlobal && ordenActualGlobal.productos_requeridos && ordenActualGlobal.productos_requeridos.length > 0) {
        let stockInsuficiente = false;
        let mensajeProductos = '';
        
        for (const prod of ordenActualGlobal.productos_requeridos) {
            const cantidadTotal = parseFloat(prod.cantidad_producto) * parseFloat(ordenActualGlobal.cantidad_servicio);
            const stockActual = parseFloat(prod.stock_producto || 0);
            if (stockActual < cantidadTotal) {
                stockInsuficiente = true;
                mensajeProductos += `\n- ${prod.nombre_producto}: Stock ${stockActual}, Necesario ${cantidadTotal}`;
            }
        }
        
        if (stockInsuficiente) {
            alertasAjax({
                tipo: 'simple',
                titulo: 'Stock insuficiente',
                texto: 'No hay suficiente stock para ejecutar este servicio:' + mensajeProductos,
                icono: 'warning'
            });
            return;
        }
    }
    
    let titulo = '';
    let texto = '';
    
    if (nuevoStatus == 4) {
        titulo = '¿Cancelar orden?';
        texto = 'Al cancelar la orden, se devolverá el stock de los productos asociados. ¿Estás seguro?';
    } else if (nuevoStatus == 2) {
        titulo = '¿Marcar como ejecutada?';
        texto = 'Al marcar la orden como ejecutada, se descontará el stock de los productos. ¿Estás seguro de que el servicio fue realizado?';
    } else {
        titulo = '¿Actualizar orden?';
        texto = '¿Estás seguro de que deseas actualizar esta orden?';
    }
    
    const resultado = await alertasAjax({
        tipo: 'preguntar',
        titulo: titulo,
        texto: texto
    });
    
    if (resultado.isConfirmed) {
        mostrarOcultarSpinnerCarga('mostrar');
        
        const datos = {
            accion: 'actualizar',
            id_servicio_factura: idOrden,
            status: nuevoStatus
        };
        
        if (nuevaFecha) {
            datos.fecha_ejecucion = nuevaFecha;
        }
        
        const respuesta = await pedirDatosAjax({
            modulo: 'ordenesServicios',
            datosPe: datos
        });
        
        mostrarOcultarSpinnerCarga('ocultar');
        
        if (respuesta.icono === 'success') {
            $('.modalGestionarOrden').modal('hide');
            reiniciarDataTables();
        }
        
        alertasAjax(respuesta);
    }
}

//#endregion [ FUNCIONES PROPIAS DEL MODULO ] FIN

//#region [ DELEGACIÓN DE EVENTOS ] COMIENZO

$(document).on('DOMContentLoaded', async function () {
    const permisos = await pedirDatosAjax({
        modulo: 'accesos',
        datosPe: { accion: 'listarPorRol' }
    });
    
    const puedeVer = permisos.ordenesServicios && permisos.ordenesServicios.includes('ver');
    const puedeActualizar = permisos.ordenesServicios && permisos.ordenesServicios.includes('actualizar');
    
    if (puedeVer) {
        await listarDataTable({
            encabezados: {
                "id_servicio_factura": "N° ORDEN",
                "nombre_servicio": "SERVICIO",
                "cantidad_servicio": "CANTIDAD",
                "fecha_ejecucion": "FECHA EJECUCIÓN",
                "status_orden": "ESTADO",
                "razon_social_cliente": "CLIENTE"
            },
            informacionPe: {
                'modulo': 'ordenesServicios',
                'datosPe': { 'accion': 'listar' }
            },
            infoTratoEspecial: {
                fecha_ejecucion: (info) => cambiarFormatos(info.valor, 'fecha'),
                status_orden: (info) => {
                    const statusInfo = getStatusInfo(info.valor, info.fila.esta_retrasado);
                    return `<span class="badge ${statusInfo.color}">${statusInfo.texto}</span>`;
                },
                razon_social_cliente: (info) => info.valor || info.fila.rif_cedula_cliente || 'N/A'
            },
            botones: (info) => {
                let botones = '<ul class="list-inline me-auto mb-0">';

                botones += `
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" data-bs-placement="top" title="Ver detalles">
                        <a href="#" value="${info.fila.id_servicio_factura}" class="btnVerOrdenServicio avtar avtar-xs btn-link-success btn-pc-default">
                            <i class="fi fi-rs-eye fs-3 iconoCentrado"></i>
                        </a>
                    </li>
                `;
                
                const puedeGestionar = puedeActualizar && (info.fila.status_orden === 1 || info.fila.status_orden === 3);
                if (puedeGestionar) {
                    botones += `
                        <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" data-bs-placement="top" title="Gestionar orden">
                            <a href="#" value="${info.fila.id_servicio_factura}" class="btnGestionarOrden avtar avtar-xs btn-link-primary btn-pc-default">
                                <i class="fi fi-rs-settings fs-3 iconoCentrado"></i>
                            </a>
                        </li>
                    `;
                }
                
                botones += '</ul>';
                return botones;
            }
        });
        
        registrarTutorial();
        
        const driverPendiente = sessionStorage.getItem('driver_pendiente');
        if (driverPendiente === 'ordenesServicios') {
            sessionStorage.removeItem('driver_pendiente');
            setTimeout(() => {
                mostrarAyuda();
            }, 1000);
        }
    }
});

// Ver detalles
$(document).off('click', '.btnVerOrdenServicio');
$(document).on('click', '.btnVerOrdenServicio', function () {
    verDetallesOrden.call(this);
});

$(document).off('click', '.btnGestionarOrden');
$(document).on('click', '.btnGestionarOrden', function () {
    abrirModalGestion.call(this);
});

$(document).off('change', '.selectNuevoStatus');
$(document).on('change', '.selectNuevoStatus', function () {
    toggleFechaPorStatus.call(this);
});

$(document).off('submit', '.formularioGestionarOrden');
$(document).on('submit', '.formularioGestionarOrden', function (e) {
    enviarActualizacionOrden.call(this, e);
});

//#endregion [ DELEGACIÓN DE EVENTOS ] FIN