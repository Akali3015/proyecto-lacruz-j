//#region [ IMPORTACIONES ] COMIENZO
import {
    enviarFormulario, obtenerDatosRegistro,
    listarDataTable, cargarInputsActualizarQNR, extraerDatosAjax,
    pedirDatosAjax, instanciasDatatable, alertasAjax, reiniciarDataModuloSS,
    reiniciarDataTables, validarEnTiempoReal
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
//#endregion [ IMPORTACIONES ] FIN

//#region [VARIABLES O CONSTANTES GLOBALES] COMIENZO
let productosDirectos = [];
let serviciosSeleccionados = [];
//#endregion [VARIABLES O CONSTANTES GLOBALES] FIN

//#region [FUNCIONES PROPIAS DEL MODULO] COMIENZO
async function eliminarVentaCustom(e) {
    e.preventDefault();
    let resultado = await Swal.fire({
        title: '¿Estás seguro?',
        text: "¿Estás seguro de eliminar el registro?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar'
    });

    if (resultado.isConfirmed) {
        let idRegistro = $(this).attr('value');
        let instruccionesPe = {
            'noGuardarLocal': true,
            'modulo': 'ventas',
            'datosPe': {
                'accion': 'eliminar',
                'id_venta': idRegistro
            }
        };

        let respuesta = await pedirDatosAjax(instruccionesPe);
        if (respuesta['icono'] == 'success') {
            reiniciarDataModuloSS('ventas');
        }
        // Recargar DataTable
        reiniciarDataTables()
        return alertasAjax(respuesta);
    }
}
function sobrescribirEventosVentas() {
    // Eliminar eventos previos del global.js
    $(document).off('click', '.botonEliminar');

    // Agregar nuevo evento SOLO para el <a> dentro de .botonEliminar (para que no se buguee :/)
    $(document).on('click', '.botonEliminar a', eliminarVentaCustom);
}
async function inicializarVenta() {
    await cargarClientesConExtraerDatosAjax();
    setTimeout(inicializarVentaModal, 300);
}
async function cargarClientesConExtraerDatosAjax() {
    let selectCliente = $('.modalRegistrar select[name="rif_cedula_cliente"]');

    await extraerDatosAjax({
        modulosPeticion: ['clientes'],
        accionesPeticion: [{ accion: 'listar' }],
        tipoElemento: ['select'],
        elementosDestino: [selectCliente],
        datosInsertar: [{
            value: 'rif_cedula_cliente',
            texto: 'razon_social_cliente',
            textoDefault: 'Seleccionar cliente...'
        }]
    });
}
function inicializarVentaModal() {

    if ($('.modalRegistrar').length === 0) return;

    $(document)
        .off('click', '#btnAgregarProductoDirecto, #btnAgregarServicio')
        .on('click', '#btnAgregarProductoDirecto, #btnAgregarServicio', abrirSelectorItems);

    $(document)
        .off('click', '.botonEditar')
        .on('click', '.botonEditar', async function (e) {
            e.preventDefault();
            await obtenerDatosRegistro.call(this);
            cargarInputsActualizarQNR.call(
                $($(this).data('bs-target')).find('form')
            );
        });
}
async function abrirSelectorItems(e) {

    let esProducto = e.target.id === 'btnAgregarProductoDirecto';
    let modulo = esProducto ? 'productos' : 'servicios';

    let items = await pedirDatosAjax({
        modulo: modulo,
        noGuardarLocal: true,
        datosPe: { accion: 'listar' }
    });

    if (!Array.isArray(items) || items.length === 0) {
        Swal.fire('Info', 'No hay datos disponibles', 'info');
        return;
    }

    let filas = items.map(item => {
        let precio = parseFloat(item.precio || item.precio_producto_detal || 0);
        let nombre = item.nombre_producto || item.nombre_servicio || item.nombre;
        let id = item.id_producto || item.id_servicio || item.id;
        let stock = item.stock_producto ?? 999;

        return `
            <tr>
                <td>${nombre}</td>
                <td>${precio.toFixed(2)}</td>
                ${esProducto ? `<td>${stock}</td>` : ``}
                <td>
                    <button class="btn btn-primary agregarItemVenta"
                        data-tipo="${esProducto ? 'producto' : 'servicio'}"
                        data-id="${id}"
                        data-nombre="${nombre}"
                        data-precio="${precio}"
                        ${esProducto ? `data-stock="${stock}"` : ''}>
                        Agregar
                    </button>
                </td>
            </tr>`;
    }).join('');

    $('body').append(`
        <div class="modal fade" id="modalSelectorItems">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Seleccionar ${esProducto ? 'Productos' : 'Servicios'}</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-hover">
                            <tbody>${filas}</tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `);

    $('#modalSelectorItems').modal('show');
}
function actualizarVistas() {
    actualizarTotalGeneral();
}
function actualizarTotalGeneral() {
    let total =
        productosDirectos.reduce((s, p) => s + p.cantidad * p.precio_unitario, 0) +
        serviciosSeleccionados.reduce((s, p) => s + p.cantidad * p.precio_unitario, 0);

    $('#total_venta').val(total.toFixed(2));
    $('#btnGuardarVenta').prop('disabled', total <= 0);
}
//#endregion [FUNCIONES PROPIAS DEL MODULO] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {
    await listarDataTable({
        encabezados: {
            "id_venta": "ID VENTA",
            "CLIENTE": "CLIENTE",
            "fecha_venta": "FECHA",
            "TOTAL": "TOTAL",
            "productos": "Nº de PRODUCTOS",
            "servicios": "Nº de SERVICIOS"
        },
        informacionPe: {
            'modulo': 'ventas',
            'datosPe': {
                'accion': 'listar'
            }
        },
        campoIdBtn: 'id_venta',
        botones: 'CRUD',
    });
    await inicializarVenta();

    // Sobrescribir inmediatamente después de cargar
    sobrescribirEventosVentas();

    // También sobrescribir cada vez que DataTable se redibuje
    if (instanciasDatatable.length > 0) {
        instanciasDatatable[0].on('draw', function () {
            sobrescribirEventosVentas();
        });
    }

})
$(document).on('click', '.agregarItemVenta', function () {

    let tipo = $(this).data('tipo');

    let item = {
        id_producto: tipo === 'producto' ? $(this).data('id') : null,
        id_servicio: tipo === 'servicio' ? $(this).data('id') : null,
        nombre: $(this).data('nombre'),
        precio_unitario: parseFloat($(this).data('precio')),
        cantidad: 1,
        stock: $(this).data('stock') ?? 999
    };

    tipo === 'producto'
        ? productosDirectos.push(item)
        : serviciosSeleccionados.push(item);

    actualizarVistas();
    $('#modalSelectorItems').modal('hide').remove();
});
$(document).on('submit', '.formularioAjax', async function (e) {
    e.preventDefault();

    $('<input>', { type: 'hidden', name: 'productos', value: JSON.stringify(productosDirectos) }).appendTo(this);
    $('<input>', { type: 'hidden', name: 'servicios', value: JSON.stringify(serviciosSeleccionados) }).appendTo(this);

    await enviarFormulario({
        'formulario': this,
        'modulo': 'ventas'
    })

    productosDirectos = [];
    serviciosSeleccionados = [];
    actualizarTotalGeneral();
});


//Evento para validar en tiempo real
$(document).off('input blur', '.validar input, .validar select')
$(document).on('input blur', '.validar input, .validar select', function () {
    validarEnTiempoReal(this, 'ventas');
})
//#endregion [DELEGACIÓN DE EVENTOS] FIN
