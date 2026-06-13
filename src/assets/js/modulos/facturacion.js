//#region [ IMPORTACIONES ] COMIENZO
import {
  listarDataTable, pedirDatosAjax, enviarFormulario,
  alertasAjax, reiniciarDataTables, validarEnTiempoReal,
  cambiarFormatos
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
import { driverAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"

//#endregion [ IMPORTACIONES ] FIN

//#region [VARIABLES GLOBALES] COMIENZO
let productosFactura = [];
let serviciosFactura = [];
let cachePresentaciones = null;
let cacheServicios = null;
let cacheRutas = null;
let timerCedula = null; // debounce para búsqueda de cliente
let mapaDelivery = null;
let marcadorDelivery = null;
let tasaBolivar = 1;
let ivaActivo = 0;
const CENTRO_JLACRUZ = { lat: 10.063276, lng: -69.31708 };
const TOMTOM_API_KEY = 'plFhQVWfX5abG1DPt7jja56Syrqh7rY2';
//#endregion [VARIABLES GLOBALES] FIN

//#region [FUNCIONES DEL MODULO] COMIENZO

// Nos encargamos de poner la fecha y hora actual en la pantallita de la factura
function mostrarFechaActual() {
  let ahora = new Date();
  let dia = String(ahora.getDate()).padStart(2, '0');
  let mes = String(ahora.getMonth() + 1).padStart(2, '0');
  let anio = ahora.getFullYear();
  let h = String(ahora.getHours()).padStart(2, '0');
  let m = String(ahora.getMinutes()).padStart(2, '0');
  let ampm = ahora.getHours() >= 12 ? 'PM' : 'AM';
  let h12 = ahora.getHours() % 12 || 12;
  let h12s = String(h12).padStart(2, '0');
  $('#fechaFacturaDisplay').val(`${dia}-${mes}-${anio} ${h12s}:${m} ${ampm}`);
}

async function cargarDatosFinancieros() {
  let reqMonedas = await pedirDatosAjax({
    modulo: 'monedas', noGuardarLocal: true,
    datosPe: { accion: 'listar' }
  });
  if (Array.isArray(reqMonedas)) {
    let usd = reqMonedas.find(m => m.nombre_moneda.toUpperCase() === 'DÓLAR' || m.nombre_moneda.toUpperCase() === 'DOLAR');
    if (usd) tasaBolivar = parseFloat(usd.valor_moneda);
  }

  let reqIva = await pedirDatosAjax({
    modulo: 'cambiosIva', noGuardarLocal: true,
    datosPe: { accion: 'listar' }
  });
  if (Array.isArray(reqIva) && reqIva.length > 0) {
    let activo = [...reqIva].reverse().find(i => i.status == 1);
    if (activo) ivaActivo = parseFloat(activo.monto_cambio_iva);
  }
}

// Esta función revisa al instante si el cliente está registrado en el sistema mientras escribimos su cédula
async function validarCedulaCliente(cedula) {
  let input = $('#inputCedulaClienteFact');
  let feedback = $('#feedbackClienteFact');
  let nombre = $('#nombreClienteFact');

  cedula = cedula.trim();

  // Primero dejamos todo en blanco por si había un mensaje de antes
  nombre.val('');
  feedback.html('');
  input.removeClass('is-valid is-invalid').css({ 'border-color': '', 'background-color': '' });

  if (!cedula) return;

  // Mostramos un mensajito diciendo que estamos buscando...
  feedback.html('<span class="text-muted"><i class="fi fi-rs-loading me-1"></i>Buscando...</span>');

  let resultado = await pedirDatosAjax({
    modulo: 'clientes',
    noGuardarLocal: true,
    datosPe: { accion: 'seleccionarUno', rif_cedula_cliente: cedula }
  });

  // Si el servidor nos devuelve un error o no trae nada bueno, es porque el cliente no existe
  if (!resultado || resultado.icono === 'error' || Array.isArray(resultado)) {
    input.addClass('is-invalid');
    feedback.html('<span class="text-danger"><i class="fi fi-rs-cross-circle me-1"></i>Cliente no encontrado</span>');
    nombre.val('');
    return;
  }

  //  Encontramos al cliente, así que pintamos la cajita de verde 
  let razon = resultado.razon_social_cliente || resultado.CLIENTE || '';
  input.addClass('is-valid').css({ 'border-color': '#42ba96', 'background-color': '#f2fcf5' });
  feedback.html(`<span class="text-success"><i class="fi fi-rs-check-circle me-1"></i>${razon}</span>`);
  nombre.val(razon);
}

// Ya no usamos cargarClientes() porque ahora verificamos la cédula directamente mientras el usuario escribe

async function cargarPresentaciones() {
  if (cachePresentaciones) return cachePresentaciones;
  let items = await pedirDatosAjax({
    modulo: 'productos', noGuardarLocal: true,
    datosPe: { accion: 'listar', tipoConsulta: 'ecommerce' }
  });
  cachePresentaciones = Array.isArray(items) ? items : [];
  return cachePresentaciones;
}

async function cargarServicios() {
  if (cacheServicios) return cacheServicios;
  let items = await pedirDatosAjax({
    modulo: 'servicios', noGuardarLocal: true,
    datosPe: { accion: 'listar' }
  });
  cacheServicios = Array.isArray(items) ? items : [];
  return cacheServicios;
}

async function cargarRutas() {
  if (cacheRutas) return cacheRutas;
  let items = await pedirDatosAjax({
    modulo: 'rutas', noGuardarLocal: true,
    datosPe: { accion: 'listar' }
  });
  cacheRutas = Array.isArray(items) ? items : [];
  return cacheRutas;
}

async function cargarRepartidores() {
  let items = await pedirDatosAjax({
    modulo: 'repartidores', noGuardarLocal: true,
    datosPe: { accion: 'listar' }
  });
  let opts = '<option value="">Sin asignar</option>';
  if (Array.isArray(items)) {
    items.forEach(r => {
      let nombre = (r.nombre_repartidor || '') + ' ' + (r.apellido_repartidor || '');
      opts += `<option value="${r.cedula_repartidor}">${nombre.trim()}</option>`;
    });
  }
  $('#selectRepartidorFact').html(opts);
}

// === A partir de aquí tenemos todo lo que hace funcionar el mapita del delivery ===

async function inicializarMapaDelivery() {
  // Por si acaso había un mapa de antes, lo borramos para no sobrecargar
  destruirMapaDelivery();

  // Le pedimos permiso al navegador para saber dónde está el usuario
  try {
    let permiso = await navigator.permissions.query({ name: 'geolocation' });
    if (permiso.state === 'denied') {
      Swal.fire('Permiso denegado', 'Active la geolocalización para usar el mapa de delivery', 'warning');
      $('#chkDeliveryFact').prop('checked', false).trigger('change');
      return;
    }
  } catch (e) { /* Navegadores sin API permissions */ }

  let ubicacion;
  try {
    ubicacion = await new Promise((resolve, reject) => {
      navigator.geolocation.getCurrentPosition(resolve, reject, { timeout: 10000 });
    });
  } catch (e) {
    Swal.fire('Error GPS', 'No se pudo obtener su ubicación. Verifique los permisos de geolocalización.', 'warning');
    $('#chkDeliveryFact').prop('checked', false).trigger('change');
    return;
  }

  let lat = ubicacion.coords.latitude;
  let lng = ubicacion.coords.longitude;

  // Levantamos el mapa centrado en la ubicación actual
  mapaDelivery = L.map('mapaDeliveryFact').setView([lat, lng], 15);
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="#">OpenStreetMap</a>'
  }).addTo(mapaDelivery);

  // Ponemos el marcador del local (nuestro punto de partida)
  const iconoJLACRUZ = L.divIcon({ className: 'iconoHamburguesa' });
  L.marker([CENTRO_JLACRUZ.lat, CENTRO_JLACRUZ.lng], { icon: iconoJLACRUZ })
    .addTo(mapaDelivery).bindPopup('J. LACRUZ C.A.');

  // Y este es el marcador de a dónde vamos a llevar el pedido
  marcadorDelivery = L.marker([lat, lng]).addTo(mapaDelivery).bindPopup('Ubicación de entrega');

  // Hacemos el cálculo inicial de cuántos kilómetros hay
  await actualizarDeliveryPorUbicacion({ lat, lng });

  // Si tocan otra parte del mapa, movemos el marcador para allá y recalculamos todo
  mapaDelivery.on('click', async function (e) {
    if (marcadorDelivery) mapaDelivery.removeLayer(marcadorDelivery);
    marcadorDelivery = L.marker([e.latlng.lat, e.latlng.lng]).addTo(mapaDelivery);
    mapaDelivery.panTo([e.latlng.lat, e.latlng.lng]);
    await actualizarDeliveryPorUbicacion(e.latlng);
  });
}
function destruirMapaDelivery() {
  if (mapaDelivery) {
    mapaDelivery.remove();
    mapaDelivery = null;
    marcadorDelivery = null;
  }
}
async function actualizarDeliveryPorUbicacion(latlng) {
  // Guardamos la latitud y longitud en unos inputs ocultos para usarlos luego
  $('#latDeliveryFact').val(latlng.lat);
  $('#lngDeliveryFact').val(latlng.lng);

  // Usamos una API (TomTom) para calcular la ruta en auto hasta allá
  let distanciaKM = 0;
  try {
    let resp = await fetch(
      `https://api.tomtom.com/routing/1/calculateRoute/${CENTRO_JLACRUZ.lat},${CENTRO_JLACRUZ.lng}:${latlng.lat},${latlng.lng}/json?key=${TOMTOM_API_KEY}&travelMode=car`
    );
    let infoRuta = await resp.json();
    if (infoRuta.routes && infoRuta.routes[0]?.summary?.lengthInMeters) {
      distanciaKM = Math.ceil(infoRuta.routes[0].summary.lengthInMeters / 1000);
    } else {
      throw new Error('Sin ruta');
    }
  } catch (e) {
    console.warn('Usando distancia lineal como respaldo:', e);
    let centroLL = L.latLng(CENTRO_JLACRUZ.lat, CENTRO_JLACRUZ.lng);
    let puntoLL = L.latLng(latlng.lat, latlng.lng);
    distanciaKM = Math.ceil(centroLL.distanceTo(puntoLL) / 1000);
  }
  $('#distanciaDeliveryFact').val(distanciaKM);

  // Revisamos en nuestras rutas guardadas cuál encaja con esta distancia
  let rutas = await cargarRutas();
  let rutaEncontrada = rutas.find(r => {
    let min = parseFloat(r.minimo_km_ruta);
    let max = parseFloat(r.maximo_km_ruta);
    return distanciaKM >= min && distanciaKM <= max;
  });

  if (rutaEncontrada) {
    let precioPorKm = parseFloat(rutaEncontrada.precio_ruta);
    let costoTotal = precioPorKm * distanciaKM;
    $('#rutaAsignadaDeliveryFact').val(`${rutaEncontrada.nombre_ruta} ($${precioPorKm.toFixed(2)}/km)`);
    $('#idRutaDeliveryFact').val(rutaEncontrada.id_ruta);
    $('#costoDeliveryFact').val(costoTotal.toFixed(2));
  } else {
    $('#rutaAsignadaDeliveryFact').val('Fuera de cobertura');
    $('#idRutaDeliveryFact').val('');
    $('#costoDeliveryFact').val('0.00');
  }
  calcularTotales();

  // Traducimos las coordenadas a una dirección de calle normal (texto)
  try {
    let geoResp = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latlng.lat}&lon=${latlng.lng}`);
    let geoData = await geoResp.json();
    $('#direccionDeliveryFact').val(geoData.display_name || `${latlng.lat.toFixed(6)}, ${latlng.lng.toFixed(6)}`);
  } catch (e) {
    $('#direccionDeliveryFact').val(`${latlng.lat.toFixed(6)}, ${latlng.lng.toFixed(6)}`);
  }
}
function calcularTotales() {
  let subProd = productosFactura.reduce((s, p) => s + (p.cantidad * p.precio), 0);
  let subServ = serviciosFactura.reduce((s, s2) => {
    let precio = s2.es_mapfre ? s2.precio_mapfre : s2.precio;
    return s + (s2.cantidad * precio);
  }, 0);
  let delivery = parseFloat($('#costoDeliveryFact').val()) || 0;
  if (!$('#chkDeliveryFact').is(':checked')) delivery = 0;

  let subtotalGeneral = subProd + subServ + delivery;
  let montoIva = subtotalGeneral * (ivaActivo / 100);
  let total = subtotalGeneral + montoIva;
  let totalBs = total * tasaBolivar;

  // Aquí revisamos que tengamos suficiente de cada cosa antes de dejar facturar
  let consumoPorProducto = {};

  // Contamos cuántos productos sueltos pusimos en la factura
  productosFactura.forEach(p => {
    let id = p.id_presentacion_producto;
    if (!consumoPorProducto[id]) consumoPorProducto[id] = { nombre: p.nombre, cantidad: 0, stock: p.stock };
    consumoPorProducto[id].cantidad += p.cantidad;
  });

  // Y le sumamos los materiales que gastan los servicios, para tener el total real
  serviciosFactura.forEach(s => {
    if (s.materiales) {
      s.materiales.forEach(m => {
        let id = m.id_producto;
        if (!consumoPorProducto[id]) consumoPorProducto[id] = { nombre: m.nombre, cantidad: 0, stock: m.stock };
        consumoPorProducto[id].cantidad += (m.cantidad_requerida * s.cantidad);
      });
    }
  });

  let errorStock = false;
  let htmlErrores = '';

  // Limpiamos las marcas de error rojo de antes para empezar frescos
  $('.cantProdFact').removeClass('is-invalid');
  $('.fila-material-consumo').removeClass('table-danger text-danger');

  Object.keys(consumoPorProducto).forEach(id => {
    let cons = consumoPorProducto[id];
    if (cons.cantidad > cons.stock) {
      errorStock = true;
      htmlErrores += `<li>${cons.nombre}: Stock ${cons.stock}, Requiere ${cons.cantidad % 1 === 0 ? cons.cantidad : cons.cantidad.toFixed(2)}</li>`;

      // Pintamos de rojo el producto si no nos alcanza el stock
      $(`.cantProdFact`).filter(function () {
        let i = $(this).data('index');
        return productosFactura[i].id_presentacion_producto == id;
      }).addClass('is-invalid');

      // Hacemos lo mismo para los materiales que van dentro de los servicios
      $(`.fila-material-consumo[data-id="${id}"]`).addClass('table-danger text-danger');
    }
  });

  if (errorStock) {
    if ($('.alertaStockFactura').length === 0) {
      $('#contenedorProductosFact').before(`<div class="alert alert-danger p-2 mb-3 alertaStockFactura"><small><strong>¡Stock Insuficiente!</strong><ul class="mb-0 ps-3 listaErroresStockFact"></ul></small></div>`);
      $('#contenedorServiciosFact').before(`<div class="alert alert-danger p-2 mb-3 alertaStockFactura"><small><strong>¡Stock Insuficiente!</strong><ul class="mb-0 ps-3 listaErroresStockFact"></ul></small></div>`);
    }
    $('.listaErroresStockFact').html(htmlErrores);
  } else {
    $('.alertaStockFactura').remove();
  }

  $('#subtotalProdFact').text('$' + subProd.toFixed(2));
  $('#subtotalServFact').text('$' + subServ.toFixed(2));
  $('#resumenProdFact').text('$' + subProd.toFixed(2));
  $('#resumenServFact').text('$' + subServ.toFixed(2));
  $('#resumenDeliveryFact').text('$' + delivery.toFixed(2));
  $('#resumenTotalFact').html(`$${total.toFixed(2)} <br><small class="text-muted fs-6">Bs ${totalBs.toFixed(2)} (IVA ${ivaActivo}%: $${montoIva.toFixed(2)})</small>`);
  $('#totalGeneralFact').val(total.toFixed(2));
  $('#badgeProdFact').text(productosFactura.length);
  $('#badgeServFact').text(serviciosFactura.length);
  $('#btnGuardarFactura').prop('disabled', total <= 0 || errorStock);
}
function renderProductos() {
  let cont = $('#contenedorProductosFact');
  if (productosFactura.length === 0) {
    cont.html(`<div class="fact-empty-state"><i class="fi fi-rs-box-open"></i><p>No hay productos agregados</p></div>`);
  } else {
    let html = `<table class="fact-items-table"><thead><tr>
      <th>Producto</th><th>Precio</th><th>Cant.</th><th>Subtotal</th><th></th>
    </tr></thead><tbody>`;
    productosFactura.forEach((p, i) => {
      let sub = (p.cantidad * p.precio).toFixed(2);
      html += `<tr>
        <td><span class="fact-item-nombre">${p.nombre}</span></td>
        <td>$${p.precio.toFixed(2)}</td>
        <td><input type="number" class="fact-qty-input cantProdFact" data-index="${i}" value="${p.cantidad}" min="1" step="1"></td>
        <td class="fw-bold text-primary">$${sub}</td>
        <td><button type="button" class="fact-btn-borrar quitarProdFact" data-index="${i}">&times;</button></td>
      </tr>`;
    });
    html += '</tbody></table>';
    cont.html(html);
  }
  calcularTotales();
}
function renderServicios() {
  let cont = $('#contenedorServiciosFact');
  if (serviciosFactura.length === 0) {
    cont.html(`<div class="fact-empty-state"><i class="fi fi-tr-room-service"></i><p>No hay servicios agregados</p></div>`);
  } else {
    let html = `<table class="fact-items-table"><thead><tr>
      <th>Servicio</th><th>Precio</th><th>Mapfre</th><th>Cant.</th><th>Subtotal</th><th></th>
    </tr></thead><tbody>`;
    serviciosFactura.forEach((s, i) => {
      let precio = s.es_mapfre ? s.precio_mapfre : s.precio;
      let sub = (s.cantidad * precio).toFixed(2);

      // Si es Mapfre y ya tiene un precio puesto, mostramos ese en verde y el original tachado
      let precioColHTML = s.es_mapfre && s.precio_mapfre > 0
        ? `<span class="text-success fw-bold">$${s.precio_mapfre.toFixed(2)}</span> <br><small class="text-muted text-decoration-line-through">$${s.precio.toFixed(2)}</small>`
        : `$${s.precio.toFixed(2)}`;

      let btnToggleMat = s.materiales && s.materiales.length > 0
        ? `<button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1 me-1 btnToggleMatFactura" data-idx="${i}" title="Ver productos a descontar"><i class="fi fi-rs-plus-small"></i></button>`
        : '';

      // El toggle Mapfre siempre está habilitado para que el usuario pueda activarlo
      // Cuando se activa, aparece un campito para escribir el precio que cubre Mapfre
      html += `<tr>
        <td>${btnToggleMat}<span class="fact-item-nombre">${s.nombre}</span></td>
        <td>${precioColHTML}</td>
        <td>
          <div class="form-check form-switch" title="Activar precio Mapfre">
            <input class="form-check-input toggleMapfre" type="checkbox" data-index="${i}" ${s.es_mapfre ? 'checked' : ''}>
          </div>
          ${s.es_mapfre ? `<div class="input-group input-group-sm mt-1" style="max-width:120px;">
            <span class="input-group-text p-1">$</span>
            <input type="number" class="form-control form-control-sm precioMapfreFact" data-index="${i}" value="${s.precio_mapfre > 0 ? s.precio_mapfre.toFixed(2) : ''}" min="0" step="0.01" placeholder="0.00">
          </div>` : ''}
        </td>
        <td><input type="number" class="fact-qty-input cantServFact" data-index="${i}" value="${s.cantidad}" min="1" step="1"></td>
        <td class="fw-bold text-success">$${sub}</td>
        <td><button type="button" class="fact-btn-borrar quitarServFact" data-index="${i}">&times;</button></td>
      </tr>`;

      // Sub-tabla de materiales: el "Total a descontar" se calcula multiplicando por la cantidad del servicio
      if (s.materiales && s.materiales.length > 0) {
        html += `<tr class="fact-materiales-row-factura d-none" data-serv-idx="${i}">
          <td colspan="6" class="p-0 ps-3 pe-3 pb-2">
            <div class="bg-light rounded p-2 mt-1">
              <small class="text-muted fw-semibold d-block mb-1"><i class="fi fi-rs-box me-1"></i>Productos a descontar del stock:</small>
              <table class="table table-sm table-borderless mb-0">
                <thead><tr class="text-muted"><th style="font-size:.78rem">Producto</th><th style="font-size:.78rem">Cant/Unid (x1)</th><th style="font-size:.78rem">Total a descontar (x${s.cantidad})</th></tr></thead>
                <tbody>`;
        s.materiales.forEach(m => {
          let cantTotal = m.cantidad_requerida * s.cantidad;
          html += `<tr class="fila-material-consumo" data-id="${m.id_producto}">
            <td><small>${m.nombre}</small></td>
            <td><small>${m.cantidad_requerida} ${m.unidad}</small></td>
            <td><small class="fw-semibold cant-descontar-mat">${cantTotal % 1 === 0 ? cantTotal : cantTotal.toFixed(2)} ${m.unidad}</small></td>
          </tr>`;
        });
        html += `</tbody></table></div></td></tr>`;
      }
    });
    html += '</tbody></table>';
    cont.html(html);
  }
  calcularTotales();
}
async function abrirSelectorProductos() {
  let items = await cargarPresentaciones();
  if (!items.length) { Swal.fire('Info', 'No hay productos', 'info'); return; }

  let filas = items.map(p => {
    let id = p.id_presentacion_producto || p.id_producto;
    let nombre = p.nombre_producto || p.nombre;
    if (p.nombre_presentacion) {
      nombre += ` (${p.nombre_presentacion})`;
    }
    let precio = parseFloat(p.precio_producto || p.precio || 0);
    let stock = parseInt(p.stock_producto ?? 0);
    let stockMinimo = parseInt(p.stock_minimo_producto ?? 0);

    // Vemos cómo andamos de inventario para este producto
    let esStockCritico = stock <= stockMinimo;
    let sinStock = stock <= 0;
    let clasesFila = esStockCritico ? 'table-danger' : '';
    let badgeStock = '';
    if (sinStock) {
      badgeStock = `<span class="badge bg-danger ms-1">Sin stock</span>`;
    } else if (esStockCritico) {
      badgeStock = `<span class="badge bg-warning text-dark ms-1"><i class="fi fi-rs-triangle-warning me-1"></i>Bajo</span>`;
    }

    return `<tr class="${clasesFila}">
      <td>${nombre}</td><td>$${precio.toFixed(2)}</td>
      <td>${stock} ${badgeStock}</td>
      <td><button class="btn btn-sm text-white selProdFact fact-purple-blue-grad"
        data-id="${id}" data-nombre="${nombre}"
        data-precio="${precio}" data-stock="${stock}"
        ${sinStock ? 'disabled' : ''}>
        <i class="fi fi-rs-plus me-1"></i>${sinStock ? 'Agotado' : 'Agregar'}
      </button></td>
    </tr>`;
  }).join('');

  if ($.fn.DataTable.isDataTable('#dtSelProdFact')) {
    $('#dtSelProdFact').DataTable().destroy();
  }
  $('#dtSelProdFact tbody').html(filas);

  let modalEl = document.getElementById('modalSelProdFact');
  let modalInst = bootstrap.Modal.getOrCreateInstance(modalEl);

  // Le ponemos un fondito oscuro al modal principal para que resalte este nuevo
  $('.modalRegistrar').addClass('fact-modal-dimmed');

  // Limpiamos y registramos los eventos una única vez para evitar duplicidad de callbacks
  $(modalEl).off('hidden.bs.modal').on('hidden.bs.modal', () => {
    $('.modalRegistrar').removeClass('fact-modal-dimmed');
  });

  $(modalEl).off('shown.bs.modal').on('shown.bs.modal', function () {
    $('#dtSelProdFact').DataTable({
      paging: true,
      pageLength: 5,
      lengthChange: false,
      ordering: true,
      info: false,
      autoWidth: false,
      language: {
        search: 'Buscar:',
        zeroRecords: 'No se encontraron productos',
        paginate: { previous: '‹', next: '›' }
      },
      columnDefs: [
        { orderable: false, targets: 3 }
      ]
    });
  });

  modalInst.show();
}
async function abrirSelectorServicios() {
  let items = await cargarServicios();
  if (!items.length) { Swal.fire('Info', 'No hay servicios', 'info'); return; }

  let filas = items.map(s => {
    let id = s.id_servicio;
    let nombre = s.nombre_servicio || s.nombre;
    let precio = parseFloat(s.precio_servicio || s.precio || 0);
    let precioMapfre = parseFloat(s.precio_servicio_mapfre || 0);

    let mapfreBadge = precioMapfre > 0 ? `<br><small class="text-success">Mapfre: $${precioMapfre.toFixed(2)}</small>` : '';

    return `<tr>
      <td>${nombre}</td><td>$${precio.toFixed(2)} ${mapfreBadge}</td>
      <td><button class="btn btn-sm btn-success selServFact fact-purple-blue-grad"
        data-id="${id}" data-nombre="${nombre}" data-precio="${precio}" data-precio_mapfre="${precioMapfre}">
        <i class="fi fi-rs-plus me-1"></i>Agregar
      </button></td>
    </tr>`;
  }).join('');

  if ($.fn.DataTable.isDataTable('#dtSelServFact')) {
    $('#dtSelServFact').DataTable().destroy();
  }
  $('#dtSelServFact tbody').html(filas);

  let modalEl = document.getElementById('modalSelServFact');
  let modalInst = bootstrap.Modal.getOrCreateInstance(modalEl);

  // Le ponemos un fondito oscuro al modal principal para que resalte este nuevo
  $('.modalRegistrar').addClass('fact-modal-dimmed');

  // Limpiamos y registramos los eventos una única vez para evitar duplicidad de callbacks
  $(modalEl).off('hidden.bs.modal').on('hidden.bs.modal', () => {
    $('.modalRegistrar').removeClass('fact-modal-dimmed');
  });

  $(modalEl).off('shown.bs.modal').on('shown.bs.modal', function () {
    $('#dtSelServFact').DataTable({
      paging: true,
      pageLength: 5,
      lengthChange: false,
      ordering: true,
      info: false,
      autoWidth: false,
      language: {
        search: 'Buscar:',
        zeroRecords: 'No se encontraron servicios',
        paginate: { previous: '‹', next: '›' }
      },
      columnDefs: [
        { orderable: false, targets: 2 }
      ]
    });
  });

  modalInst.show();
}
async function verDetalleFactura(idFactura) {
  let data = await pedirDatosAjax({
    modulo: 'facturacion', noGuardarLocal: true,
    datosPe: { accion: 'obtenerDetalle', id_factura: idFactura }
  });
  if (!data || !data.cabecera) {
    Swal.fire('Error', 'No se pudo obtener el detalle', 'error');
    return;
  }
  let c = data.cabecera;

  let estado = '';
  if (c.estado_num == 5) estado = '<span class="badge bg-danger">Anulada</span>';
  else if (c.estado_num == 1) estado = '<span class="badge bg-success">Procesada y Pagada</span>';
  else if (c.estado_num == 2) estado = '<span class="badge bg-warning text-dark">Procesada y sin Pago</span>';
  else if (c.estado_num == 3) estado = '<span class="badge bg-success"><i class="fi fi-rs-check-circle me-1"></i>Pagada y Despachada (Cancelada)</span>';
  else if (c.estado_num == 4) estado = '<span class="badge bg-info">Despachada y sin Pago</span>';
  else estado = `<span class="badge bg-secondary">${c.estado_dinamico || 'Activa'}</span>`;

  let prodHtml = '';
  let subProd = 0;
  if (data.productos && data.productos.length) {
    prodHtml = '<table class="table table-sm"><thead><tr><th>Producto</th><th>Presentación</th><th>Cant.</th><th>Precio</th><th>Subtotal</th></tr></thead><tbody>';
    data.productos.forEach(p => {
      let sub = p.cantidad_producto * p.precio_producto;
      subProd += sub;
      prodHtml += `<tr><td>${p.nombre_producto}</td><td>${p.nombre_presentacion}</td><td>${p.cantidad_producto}</td><td>$${parseFloat(p.precio_producto).toFixed(2)}</td><td>$${sub.toFixed(2)}</td></tr>`;
    });
    prodHtml += '</tbody></table>';
  } else {
    prodHtml = '<p class="text-muted">Sin productos</p>';
  }

  let servHtml = '';
  let subServ = 0;
  let resumenMateriales = {}; // Aquí vamos a ir sumando todos los materiales que usamos en los distintos servicios
  if (data.servicios && data.servicios.length) {
    servHtml = '<table class="table table-sm mb-0"><thead><tr><th>Servicio</th><th>Cant.</th><th>Precio</th><th>Mapfre</th><th>Subtotal</th></tr></thead><tbody>';
    data.servicios.forEach((s, idx) => {
      let precio = s.es_precio_mapfre == 1 ? s.precio_servicio_mapfre : s.precio_servicio;
      let sub = s.cantidad_servicio * precio;
      subServ += sub;

      // Vamos sumando lo que gastamos para el total al final
      if (s.materiales && s.materiales.length) {
        s.materiales.forEach(m => {
          let key = m.id_producto;
          let cantUsada = parseFloat(m.cantidad_producto) * parseFloat(s.cantidad_servicio);
          if (!resumenMateriales[key]) {
            resumenMateriales[key] = {
              nombre: m.nombre_producto,
              unidad: m.nombre_unidad_medida || '',
              cantidad: 0
            };
          }
          resumenMateriales[key].cantidad += cantUsada;
        });
      }

      // Armamos la tablita de los materiales, pero la dejamos escondida hasta que le den al botón
      let matHtml = '';
      if (s.materiales && s.materiales.length) {
        matHtml = `<tr class="fact-materiales-row d-none" data-serv-idx="${idx}">
          <td colspan="5" class="p-0 ps-3 pe-3 pb-2">
            <div class="bg-light rounded p-2 mt-1">
              <small class="text-muted fw-semibold d-block mb-1"><i class="fi fi-rs-box me-1"></i>Productos utilizados:</small>
              <table class="table table-sm table-borderless mb-0">
                <thead><tr class="text-muted"><th style="font-size:.78rem">Producto</th><th style="font-size:.78rem">Cant/Unid (x1)</th><th style="font-size:.78rem">Total usado (x${s.cantidad_servicio})</th></tr></thead>
                <tbody>`;
        s.materiales.forEach(m => {
          let cantTotal = (parseFloat(m.cantidad_producto) * parseFloat(s.cantidad_servicio));
          matHtml += `<tr>
            <td><small>${m.nombre_producto}</small></td>
            <td><small>${parseFloat(m.cantidad_producto)} ${m.nombre_unidad_medida || ''}</small></td>
            <td><small class="fw-semibold">${cantTotal % 1 === 0 ? cantTotal : cantTotal.toFixed(2)} ${m.nombre_unidad_medida || ''}</small></td>
          </tr>`;
        });
        matHtml += '</tbody></table></div></td></tr>';
      }

      // Mostramos el precio de forma clara: si fue Mapfre, el original tachado y el Mapfre en verde
      let precioDetHTML = s.es_precio_mapfre == 1
        ? `<span class="text-success fw-bold">$${parseFloat(s.precio_servicio_mapfre).toFixed(2)}</span><br><small class="text-muted text-decoration-line-through">$${parseFloat(s.precio_servicio).toFixed(2)}</small>`
        : `$${parseFloat(s.precio_servicio).toFixed(2)}`;

      let mapfreBadge = s.es_precio_mapfre == 1
        ? `<span class="badge bg-success">Sí — $${parseFloat(s.precio_servicio_mapfre).toFixed(2)}</span>`
        : '<span class="badge bg-danger">No</span>';

      servHtml += `<tr>
        <td>
          ${s.materiales && s.materiales.length ? `<button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1 me-1 btnToggleMat" data-idx="${idx}" title="Ver productos"><i class="fi fi-rs-plus-small"></i></button>` : ''}
          ${s.nombre_servicio}
        </td>
        <td>${s.cantidad_servicio}</td>
        <td>${precioDetHTML}</td>
        <td>${mapfreBadge}</td>
        <td>$${sub.toFixed(2)}</td>
      </tr>${matHtml}`;
    });
    servHtml += '</tbody></table>';

    // Al final de la lista de servicios, mostramos el gran total de materiales consumidos
    let clavesMat = Object.keys(resumenMateriales);
    if (clavesMat.length) {
      servHtml += `<div class="fact-resumen-materiales mt-2 p-2 rounded">
        <small class="fw-bold text-black d-block mb-1"><i class="fi fi-rs-resources me-1"></i>Total materiales consumidos:</small>
        <div class="d-flex flex-wrap gap-2">`;
      clavesMat.forEach(k => {
        let m = resumenMateriales[k];
        let cantDisplay = m.cantidad % 1 === 0 ? m.cantidad : m.cantidad.toFixed(2);
        servHtml += `<span class="badge bg-light text-dark border">${m.nombre} — <strong>${cantDisplay} ${m.unidad}</strong></span>`;
      });
      servHtml += '</div></div>';
    }
  } else {
    servHtml = '<p class="text-muted">Sin servicios</p>';
  }

  let delHtml = '';
  let costoDel = 0;
  let tieneCoordsDel = false;
  let delLat = 0, delLng = 0;
  if (data.delivery) {
    costoDel = parseFloat(data.delivery.costo_delivery_total || data.delivery.precio_ruta || 0);
    delLat = parseFloat(data.delivery.coordenada_latitud || 0);
    delLng = parseFloat(data.delivery.coordenada_longitud || 0);
    tieneCoordsDel = delLat !== 0 && delLng !== 0;
    delHtml = `<div class="row mb-2">
      <div class="col-md-4"><strong>Ruta:</strong> ${data.delivery.nombre_ruta}</div>
      <div class="col-md-4"><strong>Costo:</strong> $${costoDel.toFixed(2)}</div>
      <div class="col-md-4"><strong>Repartidor:</strong> ${data.delivery.REPARTIDOR || 'Sin asignar'}</div>
    </div>`;
    if (tieneCoordsDel) {
      delHtml += `<div id="mapaDetalleDel" class="rounded border mt-2" style="height: 250px; z-index: 1;"></div>`;
    }
  } else {
    delHtml = '<p class="text-muted">Sin delivery</p>';
  }

  let total = subProd + subServ + costoDel;
  let ivaPorcentaje = parseFloat(c.IVA) || 0;
  let montoIva = total * (ivaPorcentaje / 100);
  let totalConIva = total + montoIva;
  let totalBs = totalConIva * tasaBolivar;

  $('#contenidoDetalleFactura').html(`
    <div class="fact-detalle-seccion">
      <h6><i class="fi fi-rs-file-invoice-dollar me-2"></i>Información General</h6>
      <div class="row">
        <div class="col-md-3"><strong>Factura:</strong> ${c.id_factura}</div>
        <div class="col-md-3"><strong>Cliente:</strong> ${c.CLIENTE}</div>
        <div class="col-md-3"><strong>Fecha:</strong> ${cambiarFormatos(c.fecha_factura, 'fecha_hora')}</div>
        <div class="col-md-3"><strong>Estado:</strong> ${estado}</div>
      </div>
    </div>
    <div class="fact-detalle-seccion fact-seccion-productos">
      <h6><i class="fi fi-rs-box me-2"></i>Productos</h6>${prodHtml}
    </div>
    <div class="fact-detalle-seccion fact-seccion-servicios">
      <h6><i class="fi fi-rs-cogs me-2"></i>Servicios</h6>${servHtml}
    </div>
    <div class="fact-detalle-seccion fact-seccion-delivery">
      <h6><i class="fi fi-rs-truck-side me-2"></i>Delivery</h6>${delHtml}
    </div>
    <div class="fact-totales-panel">
      <div class="fact-total-row"><span>Subtotal Productos</span><span>$${subProd.toFixed(2)}</span></div>
      <div class="fact-total-row"><span>Subtotal Servicios</span><span>$${subServ.toFixed(2)}</span></div>
      ${costoDel > 0 ? `<div class="fact-total-row"><span>Delivery</span><span>$${costoDel.toFixed(2)}</span></div>` : ''}
      <div class="fact-total-row fact-total-grande">
        <span>TOTAL GENERAL</span>
        <span class="text-end">$${totalConIva.toFixed(2)} <br><small class="text-muted fs-6" style="font-weight: 500;">Bs ${totalBs.toFixed(2)} (IVA ${ivaPorcentaje}%: $${montoIva.toFixed(2)})</small></span>
      </div>
      <div class="fact-total-row bg-light rounded mt-2 px-2 py-1">
        <span class="text-success"><i class="fi fi-rs-money me-1"></i>Abonado</span>
        <span class="text-success text-end">$${parseFloat(c.total_pagado || 0).toFixed(2)}<br><small class="text-muted fs-6" style="font-weight: 500;">Bs ${(parseFloat(c.total_pagado || 0) * tasaBolivar).toFixed(2)}</small></span>
      </div>
      <div class="fact-total-row bg-light rounded mt-1 px-2 py-1">
        <span class="text-danger fw-bold"><i class="fi fi-rs-exclamation me-1"></i>Restante</span>
        <span class="text-danger fw-bold text-end">$${((c.restante !== null && c.restante !== undefined) ? parseFloat(c.restante) : totalConIva).toFixed(2)}<br><small class="text-muted fs-6" style="font-weight: 500;">Bs ${(((c.restante !== null && c.restante !== undefined) ? parseFloat(c.restante) : totalConIva) * tasaBolivar).toFixed(2)}</small></span>
      </div>
    </div>
  `);

  $('#btnAnularFacturaModal').data('id', c.id_factura);
  if (c.status != 1) $('#btnAnularFacturaModal').hide();
  else $('#btnAnularFacturaModal').show();

  let botonesHtml = '';
  // Si la factura no está pagada por completo, dejamos que puedan meterle un pago
  if (c.status == 1 || c.status == 3) {
    let cantRestante = (c.restante !== null && c.restante !== undefined) ? parseFloat(c.restante) : totalConIva;
    if (cantRestante > 0.01) {
      botonesHtml += `<button type="button" class="btn btn-success btnAbrirPagoDesdeDetalle" data-id="${c.id_factura}"><i class="fi fi-rs-credit-card me-1"></i>Añadir Pago</button> `;
    }
  }
  // Obviamente, solo mostramos "Despachar" si lleva delivery y todavía no ha salido
  if (data.delivery && c.estado_num != 3 && c.estado_num != 4 && c.estado_num != 5) {
    botonesHtml += `<button type="button" class="btn btn-info text-white btnDespacharFactura" data-id="${c.id_factura}"><i class="fi fi-rs-truck-side me-1"></i>Despachar</button> `;
  }
  $('#botonesExtraDetalle').html(botonesHtml);

  let modalDetalle = new bootstrap.Modal('.modalDetallesFactura');
  modalDetalle.show();

  // Si la factura tiene coordenadas guardadas, le dibujamos un mapita chiquito para que vean a dónde fue
  if (tieneCoordsDel) {
    $('.modalDetallesFactura').off('shown.bs.modal.mapaDetalle').on('shown.bs.modal.mapaDetalle', function () {
      let contenedor = L.DomUtil.get('mapaDetalleDel');
      if (contenedor && contenedor._leaflet_id) return; // ya renderizado
      let mapaDet = L.map('mapaDetalleDel').setView([delLat, delLng], 15);
      L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
      }).addTo(mapaDet);
      L.marker([delLat, delLng]).addTo(mapaDet).bindPopup('Ubicación de entrega').openPopup();
      const iconoJL = L.divIcon({ className: 'iconoHamburguesa' });
      L.marker([CENTRO_JLACRUZ.lat, CENTRO_JLACRUZ.lng], { icon: iconoJL }).addTo(mapaDet).bindPopup('J. LACRUZ C.A.');
      setTimeout(() => mapaDet.invalidateSize(), 200);
    });
  }
}
function resetFormFactura() {
  productosFactura = [];
  serviciosFactura = [];
  cachePresentaciones = null;
  cacheServicios = null;
  cacheRutas = null;
  // Limpiar campo de cliente
  $('#inputCedulaClienteFact').val('').removeClass('is-valid is-invalid');
  $('#feedbackClienteFact').html('');
  $('#nombreClienteFact').val('');
  renderProductos();
  renderServicios();
  $('#chkDeliveryFact').prop('checked', false);
  $('#seccionDeliveryFact').addClass('d-none');
  $('#rowDeliveryResumen').hide();
  $('#costoDeliveryFact').val('0.00');
  $('#badgeDelFact').text('No');
  destruirMapaDelivery();
  $('#latDeliveryFact, #lngDeliveryFact, #idRutaDeliveryFact').val('');
  $('#direccionDeliveryFact, #rutaAsignadaDeliveryFact').val('');
  $('#distanciaDeliveryFact').val('0');
  calcularTotales();
  // Mostrar fecha actual
  mostrarFechaActual();
}
//#endregion [FUNCIONES DEL MODULO] FIN

//#region [EVENTOS] COMIENZO

// Inicialización DataTable
$(document).on('DOMContentLoaded', async function () {
  mostrarFechaActual();
  driverAyuda('facturacion', {
    pasos: [
      {
        element: 'button[data-bs-target=".modalRegistrar"]',
        popover: {
          title: 'Nueva Factura',
          description: 'Haz clic aquí para crear una nueva factura. Podrás agregar productos, servicios y delivery.',
          side: 'bottom',
          align: 'start'
        }
      },
      {
        element: '.tabla-ajax',
        popover: {
          title: 'Lista de Facturas',
          description: 'Aquí puedes ver todas las facturas generadas, su estado y opciones para ver detalles, pagar o anular.',
          side: 'top'
        }
      },
      {
        element: '.botonVerFactura',
        popover: {
          title: 'Ver Detalles',
          description: 'Haz clic aquí para ver el detalle completo de la factura, incluyendo productos, servicios y delivery.',
          side: 'left'
        }
      },
      {
        element: '.botonAnularFactura',
        popover: {
          title: 'Anular Factura',
          description: 'Anula la factura y restaura el stock de los productos. Esta acción no se puede deshacer.',
          side: 'left'
        }
      },
      {
        popover: {
          title: '¡Ayuda completada!',
          description: 'Ya conoces el proceso de facturación. Puedes crear facturas con productos, servicios y delivery, y gestionar pagos.',
          side: 'top'
        }
      }
    ]
  });
  await cargarDatosFinancieros();
  await listarDataTable({
    encabezados: {
      'id_factura': 'N° FACTURA',
      'CLIENTE': 'CLIENTE',
      'fecha_factura': 'FECHA',
      'cant_productos': 'PRODUCTOS',
      'cant_servicios': 'SERVICIOS',
      'tiene_delivery': 'DELIVERY',
      'estado_dinamico': 'ESTADO',
    },
    informacionPe: {
      modulo: 'facturacion',
      noGuardarLocal: true,
      datosPe: { accion: 'listar' }
    },
    campoIdBtn: 'id_factura',
    infoTratoEspecial: {
      id_factura: (info) => `<span class="badge bg-light text-dark border fw-bold px-2">${info.valor}</span>`,
      fecha_factura: (info) => {
        if (!info.valor) return '-';
        return cambiarFormatos(info.valor, 'fecha_hora');
      },
      estado_dinamico: (info) => {
        let estadoNum = info.fila.estado_num;
        if (estadoNum == 5) return '<span class="badge bg-danger">Anulada</span>';
        if (estadoNum == 1) return '<span class="badge bg-success">Procesada y Pagada</span>';
        if (estadoNum == 2) return '<span class="badge bg-warning text-dark">Procesada y sin Pago</span>';
        if (estadoNum == 3) return '<span class="badge bg-success"><i class="fi fi-rs-check-circle me-1"></i>Pagada y Despachada (Cancelada)</span>';
        if (estadoNum == 4) return '<span class="badge bg-info">Despachada y sin Pago</span>';
        return `<span class="badge bg-secondary">${info.valor}</span>`;
      },
      tiene_delivery: (info) => {
        return parseInt(info.valor) > 0
          ? '<span class="badge bg-info"><i class="fi fi-rs-truck-side me-1"></i>Sí</span>'
          : '<span class="badge bg-secondary">No</span>';
      },
    },
    botones: function (info) {
      let id = info.fila.id_factura;
      let btns = '<ul class="list-inline mb-0">';
      btns += `<li class="list-inline-item"><a href="#" value="${id}" class="botonVerFactura avtar avtar-xs btn-link-info"><i class="fi fi-rs-eye fs-3 iconoCentrado"></i></a></li>`;
      if (info.fila.status == 1) {
        btns += `<li class="list-inline-item"><a href="#" value="${id}" class="botonAnularFactura avtar avtar-xs btn-link-danger"><i class="fi fi-rs-ban fs-3 iconoCentrado"></i></a></li>`;
      }
      btns += '</ul>';
      return btns;
    }
  });

});

// Abrir modal registrar — resetear y mostrar fecha
$('.modalRegistrar').on('show.bs.modal', function () {
  resetFormFactura();
});

// Validación en tiempo real de la cédula del cliente (con debounce 500ms)
$(document).on('input', '#inputCedulaClienteFact', function () {
  let cedula = $(this).val().trim();
  clearTimeout(timerCedula);
  // Limpiar estado mientras escribe
  $(this).removeClass('is-valid is-invalid');
  $('#feedbackClienteFact').html('');
  $('#nombreClienteFact').val('');
  if (!cedula) return;
  timerCedula = setTimeout(() => validarCedulaCliente(cedula), 500);
});

// Agregar producto
$(document).on('click', '#btnAgregarProductoFact', abrirSelectorProductos);

// Seleccionar producto del modal
$(document).on('click', '.selProdFact', function () {
  let idProducto = $(this).data('id');
  let existente = productosFactura.find(p => p.id_presentacion_producto == idProducto);

  if (existente) {
    existente.cantidad += 1;
  } else {
    productosFactura.push({
      id_presentacion_producto: idProducto,
      nombre: $(this).data('nombre'),
      precio: parseFloat($(this).data('precio')),
      cantidad: 1,
      stock: parseInt($(this).data('stock'))
    });
  }
  renderProductos();
  $('#modalSelProdFact').modal('hide');
});

// Cambiar cantidad producto
$(document).on('input', '.cantProdFact', function () {
  let i = $(this).data('index');
  let val = parseInt($(this).val()) || 1;
  productosFactura[i].cantidad = val;
  calcularTotales();
});

// Quitar producto
$(document).on('click', '.quitarProdFact', function () {
  productosFactura.splice($(this).data('index'), 1);
  renderProductos();
});

// Agregar servicio
$(document).on('click', '#btnAgregarServicioFact', abrirSelectorServicios);

// Seleccionar servicio — si ya existe, solo sumamos +1 a la cantidad (como con productos)
$(document).on('click', '.selServFact', async function () {
  let idServicio = $(this).data('id');
  let nombreServicio = $(this).data('nombre');
  let precioServicio = parseFloat($(this).data('precio'));

  // Si ya lo tenemos en la lista, solo le sumamos 1 a la cantidad y listo
  let existente = serviciosFactura.find(s => s.id_servicio == idServicio);
  if (existente) {
    existente.cantidad += 1;
    renderServicios();
    $('#modalSelServFact').modal('hide');
    return;
  }

  let btn = $(this);
  let oldHtml = btn.html();
  btn.html('<i class="fi fi-rs-loading me-1 spinner-border spinner-border-sm"></i>Agregando').prop('disabled', true);

  let result = await pedirDatosAjax({
    modulo: 'servicios', noGuardarLocal: true,
    datosPe: { accion: 'seleccionarUno', id_servicio: idServicio }
  });

  btn.html(oldHtml).prop('disabled', false);

  let materiales = [];
  if (result && result.detallesExtra && result.detallesExtra.productos_servicio) {
    let presentaciones = await cargarPresentaciones();
    result.detallesExtra.productos_servicio.forEach(prod => {
      let pres = presentaciones.find(p => p.id_producto == prod.id_producto);
      if (pres) {
        let nombrePres = pres.nombre_producto || pres.nombre;
        if (pres.nombre_presentacion) nombrePres += ` (${pres.nombre_presentacion})`;
        materiales.push({
          id_producto: prod.id_producto,
          nombre: nombrePres,
          unidad: pres.nombre_unidad_medida || '',
          cantidad_requerida: parseFloat(prod.cantidad_producto),
          stock: parseInt(pres.stock_producto ?? 0)
        });
      }
    });
  }

  // El precio Mapfre empieza en 0 porque el usuario lo ingresa manualmente al activar el toggle
  serviciosFactura.push({
    id_servicio: idServicio,
    nombre: nombreServicio,
    precio: precioServicio,
    cantidad: 1,
    es_mapfre: false,
    precio_mapfre: 0,
    materiales: materiales
  });

  renderServicios();
  $('#modalSelServFact').modal('hide');
});

// Cambiar cantidad servicio — re-renderizamos todo para actualizar el encabezado (x{cantidad})
$(document).on('input', '.cantServFact', function () {
  let i = $(this).data('index');
  serviciosFactura[i].cantidad = parseInt($(this).val()) || 1;
  renderServicios();
});

// Toggle Mapfre
$(document).on('change', '.toggleMapfre', function () {
  let i = $(this).data('index');
  serviciosFactura[i].es_mapfre = $(this).is(':checked');
  renderServicios();
});

// Precio Mapfre
$(document).on('input', '.precioMapfreFact', function () {
  let i = $(this).data('index');
  serviciosFactura[i].precio_mapfre = parseFloat($(this).val()) || 0;
  calcularTotales();
});

// Toggle materiales de servicio en detalle de factura
$(document).on('click', '.btnToggleMat', function () {
  let idx = $(this).data('idx');
  let $row = $(`.fact-materiales-row[data-serv-idx="${idx}"]`);
  let $icon = $(this).find('i');
  $row.toggleClass('d-none');
  if ($row.hasClass('d-none')) {
    $icon.removeClass('fi-rs-minus-small').addClass('fi-rs-plus-small');
  } else {
    $icon.removeClass('fi-rs-plus-small').addClass('fi-rs-minus-small');
  }
});

// Toggle materiales de servicio en registro de factura
$(document).on('click', '.btnToggleMatFactura', function () {
  let idx = $(this).data('idx');
  let $row = $(`.fact-materiales-row-factura[data-serv-idx="${idx}"]`);
  let $icon = $(this).find('i');
  $row.toggleClass('d-none');
  if ($row.hasClass('d-none')) {
    $icon.removeClass('fi-rs-minus-small').addClass('fi-rs-plus-small');
  } else {
    $icon.removeClass('fi-rs-plus-small').addClass('fi-rs-minus-small');
  }
});

// Quitar servicio
$(document).on('click', '.quitarServFact', function () {
  serviciosFactura.splice($(this).data('index'), 1);
  renderServicios();
});

// Toggle delivery
$(document).on('change', '#chkDeliveryFact', async function () {
  if ($(this).is(':checked')) {
    $('#seccionDeliveryFact').removeClass('d-none');
    $('#rowDeliveryResumen').show();
    $('#badgeDelFact').text('Sí').removeClass('bg-secondary').addClass('bg-info');
    await cargarRepartidores();
    // Inicializar mapa tras un breve delay para que el DOM se renderice
    setTimeout(async () => {
      await inicializarMapaDelivery();
    }, 300);
  } else {
    $('#seccionDeliveryFact').addClass('d-none');
    $('#rowDeliveryResumen').hide();
    $('#badgeDelFact').text('No').removeClass('bg-info').addClass('bg-secondary');
    $('#costoDeliveryFact').val('0.00');
    destruirMapaDelivery();
  }
  calcularTotales();
});

// Botón "Mi ubicación" del mapa delivery
$(document).on('click', '#btnMiUbicacionFact', async function () {
  if (!mapaDelivery) return;
  try {
    let ubicacion = await new Promise((resolve, reject) => {
      navigator.geolocation.getCurrentPosition(resolve, reject, { timeout: 10000 });
    });
    let lat = ubicacion.coords.latitude;
    let lng = ubicacion.coords.longitude;
    mapaDelivery.flyTo([lat, lng], 16, { animate: true, duration: 2 });
    if (marcadorDelivery) mapaDelivery.removeLayer(marcadorDelivery);
    marcadorDelivery = L.marker([lat, lng]).addTo(mapaDelivery);
    await actualizarDeliveryPorUbicacion({ lat, lng });
  } catch (e) {
    Swal.fire('Error', 'No se pudo obtener su ubicación', 'warning');
  }
});

// Enviar formulario manejado por el evento de #formFactura al final del archivo.

// Ver detalle
$(document).on('click', '.botonVerFactura', function (e) {
  e.preventDefault();
  verDetalleFactura($(this).attr('value'));
});

// Anular factura (tabla)
$(document).on('click', '.botonAnularFactura', async function (e) {
  e.preventDefault();
  let id = $(this).attr('value');
  let confirm = await Swal.fire({
    title: '¿Anular factura?',
    text: `Se anulará la factura ${id} y se restaurará el stock`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    confirmButtonText: 'Sí, anular',
    cancelButtonText: 'Cancelar'
  });
  if (confirm.isConfirmed) {
    let resp = await pedirDatosAjax({
      modulo: 'facturacion', noGuardarLocal: true,
      datosPe: { accion: 'anular', id_factura: id }
    });
    alertasAjax(resp);
    reiniciarDataTables();
  }
});

// Anular desde modal detalle
$(document).on('click', '#btnAnularFacturaModal', async function () {
  let id = $(this).data('id');
  let confirm = await Swal.fire({
    title: '¿Anular factura?',
    text: `Se anulará la factura ${id}`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    confirmButtonText: 'Sí, anular',
    cancelButtonText: 'Cancelar'
  });
  if (confirm.isConfirmed) {
    let resp = await pedirDatosAjax({
      modulo: 'facturacion', noGuardarLocal: true,
      datosPe: { accion: 'anular', id_factura: id }
    });
    alertasAjax(resp);
    reiniciarDataTables();
    $('.modalDetallesFactura').modal('hide');
  }
});

// Validación en tiempo real
$(document).off('input', '.validar input, .validar select');
$(document).on('input', '.validar input, .validar select', function () {
  validarEnTiempoReal(this, 'facturacion');
});

async function validarCedulaRepartidorFact(cedula) {
  let input = $('#inputCedulaRepartidorFact');
  let feedback = $('#feedbackRepartidorFact');
  let hiddenInput = $('#selectRepartidorFact');

  feedback.html('<span class="text-muted"><i class="fi fi-rs-loading me-1"></i>Buscando...</span>');

  let resultado = await pedirDatosAjax({
    modulo: 'repartidores',
    noGuardarLocal: true,
    datosPe: { accion: 'seleccionarUno', cedula_repartidor: cedula }
  });

  if (!resultado || resultado.icono === 'error' || Array.isArray(resultado)) {
    input.addClass('is-invalid');
    feedback.html(`
      <span class="text-danger d-block mb-1"><i class="fi fi-rs-cross-circle me-1"></i>No existe</span>
      <button type="button" class="btn btn-sm text-white" 
      style="background: linear-gradient(135deg, #4e54c8, #8f94fb); 
      border: none;" id="btnAbrirRegistroRepartidorFact">Registrar Repartidor</button>
    `);
  } else {
    input.addClass('is-valid').css({ 'border-color': '#42ba96', 'background-color': '#f2fcf5' });
    hiddenInput.val(resultado.cedula_repartidor);
    feedback.html(`<span class="text-success"><i class="fi fi-rs-check-circle me-1"></i>${resultado.nombre_repartidor} ${resultado.apellido_repartidor}</span>`);
  }
}

let timerRepartidor = null;
$(document).on('input', '#inputCedulaRepartidorFact', function () {
  // Formatear cédula: primera letra V, E, J, G, P seguida de números
  let val = $(this).val().toUpperCase().replace(/[^VEJGP0-9]/g, '');
  if (val.length > 0) {
    if (/^[0-9]/.test(val)) {
      val = 'V' + val; // Si empieza por número, asume V por defecto
    } else if (val.length > 1) {
      let letra = val.charAt(0);
      let numeros = val.substring(1).replace(/[^0-9]/g, '');
      val = letra + numeros;
    }
  }
  $(this).val(val);

  let cedula = val.trim();
  clearTimeout(timerRepartidor);

  $('#feedbackRepartidorFact').html('');
  $('#selectRepartidorFact').val('');
  $(this).removeClass('is-valid is-invalid').css({ 'border-color': '', 'background-color': '' });

  // Solo buscamos si tiene al menos una letra y algunos números (ej: V1234)
  if (cedula.length < 5) return;

  timerRepartidor = setTimeout(() => {
    validarCedulaRepartidorFact(cedula);
  }, 500);
});

// Modal para registrar repartidor
$(document).on('click', '#btnAbrirRegistroRepartidorFact', function () {
  let cedulaActual = $('#inputCedulaRepartidorFact').val().trim();

  // Reseteamos el formulario
  let form = document.getElementById('formRegistroRepartidorFact');
  if (form) {
    form.reset();
  }

  // Limpiamos estados de validación y feedback
  $('#formRegistroRepartidorFact input').removeClass('is-valid is-invalid');
  $('#feedbackTelefonoRepartidorReg').html('');
  $('#btnGuardarRepartidorFact').prop('disabled', true);

  // Seteamos la cédula actual
  $('#modalRegistroRepartidorFact input[name="cedula_repartidor"]').val(cedulaActual);

  // Oscurecemos el modal de factura para que resalte este
  $('.modalRegistrar').addClass('fact-modal-dimmed');

  let modalEl = document.getElementById('modalRegistroRepartidorFact');
  let modal = bootstrap.Modal.getOrCreateInstance(modalEl);

  $(modalEl).off('hidden.bs.modal').on('hidden.bs.modal', () => {
    $('.modalRegistrar').removeClass('fact-modal-dimmed');
  });

  modal.show();
});

// Validación en tiempo real del formulario de registro de repartidor
function validarCamposRegistroRepartidor() {
  let nombreOk = $('#inputNombreRepartidorReg').hasClass('is-valid');
  let apellidoOk = $('#inputApellidoRepartidorReg').hasClass('is-valid');
  let telefonoOk = $('#inputTelefonoRepartidorReg').hasClass('is-valid');
  $('#btnGuardarRepartidorFact').prop('disabled', !(nombreOk && apellidoOk && telefonoOk));
}

// Funciones de validación del formulario de registro de repartidores
function validarNombreRepartidor() {
  let el = $(this);
  el.val(el.val().replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, ''));
  let val = el.val().trim();
  if (val.length < 3) {
    el.removeClass('is-valid').addClass('is-invalid');
  } else {
    el.removeClass('is-invalid').addClass('is-valid');
  }
  validarCamposRegistroRepartidor();
}

function validarApellidoRepartidor() {
  let el = $(this);
  el.val(el.val().replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, ''));
  let val = el.val().trim();
  if (val.length < 3) {
    el.removeClass('is-valid').addClass('is-invalid');
  } else {
    el.removeClass('is-invalid').addClass('is-valid');
  }
  validarCamposRegistroRepartidor();
}

let timerTelefonoReg = null;
function validarTelefonoRepartidor() {
  let el = $(this);
  let val = el.val().replace(/\D/g, '');
  el.val(val); // Solo números
  let fb = $('#feedbackTelefonoRepartidorReg');
  clearTimeout(timerTelefonoReg);
  el.removeClass('is-valid is-invalid');
  fb.html('');

  if (val.length !== 11) {
    el.addClass('is-invalid');
    fb.html(`<small class="text-danger"><i class="fi fi-rs-cross-circle me-1"></i>Debe tener 11 dígitos (${val.length}/11)</small>`);
    validarCamposRegistroRepartidor();
    return;
  }

  fb.html('<small class="text-muted"><i class="fi fi-rs-loading me-1"></i>Verificando...</small>');
  timerTelefonoReg = setTimeout(async () => {
    let resultado = await pedirDatosAjax({
      modulo: 'repartidores',
      noGuardarLocal: true,
      datosPe: { accion: 'listar' }
    });
    let existe = false;
    if (resultado) {
      let lista = Array.isArray(resultado) ? resultado : (resultado.data || []);
      existe = lista.some(r => r.telefono_repartidor === val);
    }
    if (existe) {
      $('#inputTelefonoRepartidorReg').removeClass('is-valid').addClass('is-invalid');
      fb.html('<small class="text-danger"><i class="fi fi-rs-cross-circle me-1"></i>Este teléfono ya está registrado</small>');
    } else {
      $('#inputTelefonoRepartidorReg').removeClass('is-invalid').addClass('is-valid');
      fb.html('<small class="text-success"><i class="fi fi-rs-check-circle me-1"></i>Disponible</small>');
    }
    validarCamposRegistroRepartidor();
  }, 400);
}

// Delegaciones de eventos para validación en tiempo real del repartidor
$(document).on('input', '#inputNombreRepartidorReg', validarNombreRepartidor);
$(document).on('input', '#inputApellidoRepartidorReg', validarApellidoRepartidor);
$(document).on('input', '#inputTelefonoRepartidorReg', validarTelefonoRepartidor);

$(document).on('click', '#btnGuardarRepartidorFact', async function () {
  let form = document.getElementById('formRegistroRepartidorFact');
  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  let btn = $(this);
  btn.prop('disabled', true).html('<i class="fi fi-rs-loading spinner-border spinner-border-sm me-1"></i>Guardando...');

  let formArray = $(form).serializeArray();
  let datosObj = { accion: 'registrar' };
  formArray.forEach(item => datosObj[item.name] = item.value);

  // El backend espera codigo_rif_cedula_repartidor y cedula_repartidor separados
  let cedCompleta = datosObj.cedula_repartidor.toUpperCase();
  let regex = /^([VEJGP])(\d+)$/;
  let match = cedCompleta.match(regex);
  if (match) {
    datosObj.codigo_rif_cedula_repartidor = match[1];
    datosObj.cedula_repartidor = match[2];
  } else {
    datosObj.codigo_rif_cedula_repartidor = 'V';
  }

  let res = await pedirDatosAjax({
    modulo: 'repartidores',
    datosPe: datosObj
  });

  if (res && res.icono === 'success') {
    let cedulaRegistrada = $('#formRegistroRepartidorFact input[name="cedula_repartidor"]').val().trim();
    $('#modalRegistroRepartidorFact').modal('hide');
    Swal.fire({
      title: "Repartidor registrado",
      text: "Se ha registrado exitosamente.",
      icon: "success",
      confirmButtonText: "Continuar"
    }).then(() => {
      // Colocar la cédula en el input y validar automáticamente
      $('#inputCedulaRepartidorFact').val(cedulaRegistrada);
      validarCedulaRepartidorFact(cedulaRegistrada);
    });
  } else {
    btn.prop('disabled', false).html('Guardar');
    Swal.fire({
      title: res.titulo || "Error de registro",
      text: res.texto || "Ocurrió un error al intentar registrar el repartidor.",
      icon: res.icono || "error"
    });
  }
});

//#endregion [EVENTOS] FIN

//#region [LOGICA DE ESTADOS Y PAGOS] COMIENZO
$(document).on('click', '#btnGuardarFactura', function () {
  if (!$('#formFactura')[0].checkValidity()) {
    $('#formFactura')[0].reportValidity();
    return;
  }

  if ($('#chkDeliveryFact').is(':checked')) {
    $('.btn-estado-factura[data-estado="3"]').show();
    $('.btn-estado-factura[data-estado="4"]').show();
  } else {
    $('.btn-estado-factura[data-estado="3"]').hide();
    $('.btn-estado-factura[data-estado="4"]').hide();
  }

  // Oscurecemos el modal de atrás para que se vea bien
  $('.modalRegistrar').addClass('fact-modal-dimmed');
  $('#modalEstadosFactura').modal('show');
});

// Si cierran el modal de estados sin elegir, restauramos el modal de atrás
$('#modalEstadosFactura').on('hidden.bs.modal', function () {
  $('.modalRegistrar').removeClass('fact-modal-dimmed');
});

$(document).on('click', '.btn-estado-factura', async function () {
  let estado = $(this).data('estado');
  $('#estadoSeleccionadoFactura').val(estado);
  $('#modalEstadosFactura').modal('hide');
  $('.modalRegistrar').removeClass('fact-modal-dimmed');
  $('#formFactura').trigger('submit');
});

$(document).on('submit', '#formFactura', async function (e) {
  e.preventDefault();

  let cedula = $('#inputCedulaClienteFact').val().trim();
  if (!cedula || !$('#inputCedulaClienteFact').hasClass('is-valid')) {
    Swal.fire('Atención', 'Ingrese una cédula/RIF de cliente válido', 'warning');
    return;
  }
  if (productosFactura.length === 0 && serviciosFactura.length === 0) {
    Swal.fire('Error', 'Agregue al menos un producto o servicio', 'warning');
    return;
  }

  // Si hay servicios, el delivery es obligatorio
  if (serviciosFactura.length > 0 && !$('#chkDeliveryFact').is(':checked')) {
    Swal.fire('Atención', 'Las facturas con servicios requieren un Delivery obligatorio, ya que los servicios se aplican en una ubicación.', 'warning');
    return;
  }
  if (serviciosFactura.length > 0 && $('#chkDeliveryFact').is(':checked') && !$('#idRutaDeliveryFact').val()) {
    Swal.fire('Atención', 'Seleccione una ubicación en el mapa para asignar la ruta del Delivery.', 'warning');
    return;
  }

  let deliveryInfo = {};
  if ($('#chkDeliveryFact').is(':checked') && $('#idRutaDeliveryFact').val()) {
    let distanciaKM = $('#distanciaDeliveryFact').val() || 1;
    deliveryInfo = {
      id_ruta: $('#idRutaDeliveryFact').val(),
      cedula_repartidor: $('#selectRepartidorFact').val() || null,
      latitud: $('#latDeliveryFact').val() + '|' + distanciaKM,
      longitud: $('#lngDeliveryFact').val()
    };
  }

  $('#btnGuardarFactura').html(`<i class="fi fi-rs-loading spinner-border spinner-border-sm me-1"></i>Guardando...`).prop('disabled', true);

  // Agregar inputs hidden con las matrices
  $(this).find('input[name="productos"], input[name="servicios"], input[name="delivery"], input[name="estadoSeleccionado"]').remove();
  $('<input>', { type: 'hidden', name: 'productos', value: JSON.stringify(productosFactura) }).appendTo(this);
  $('<input>', { type: 'hidden', name: 'servicios', value: JSON.stringify(serviciosFactura) }).appendTo(this);
  $('<input>', { type: 'hidden', name: 'delivery', value: JSON.stringify(deliveryInfo) }).appendTo(this);
  $('<input>', { type: 'hidden', name: 'estadoSeleccionado', value: $('#estadoSeleccionadoFactura').val() }).appendTo(this);

  let resp = await enviarFormulario({ formulario: this, modulo: 'facturacion' });

  $('#btnGuardarFactura').html(`<i class="fi fi-rs-credit-card me-1"></i>Ir a Pagos / Guardar`).prop('disabled', false);

  if (resp && resp.icono === 'success') {
    $('.modalRegistrar').modal('hide');
    resetFormFactura();
    reiniciarDataTables();

    let estado = $('#estadoSeleccionadoFactura').val();
    if (estado == 1 || estado == 3) {
      abrirModalPagos(resp.id_factura);
    }
  }
});

async function abrirModalPagos(idFactura) {
  let [resMetodos, resMonedas, resFactura] = await Promise.all([
    pedirDatosAjax({ modulo: 'facturacion', noGuardarLocal: true, datosPe: { accion: 'listarMetodosPago' } }),
    pedirDatosAjax({ modulo: 'monedas', noGuardarLocal: true, datosPe: { accion: 'listar' } }),
    pedirDatosAjax({ modulo: 'facturacion', noGuardarLocal: true, datosPe: { accion: 'obtenerDetalle', id_factura: idFactura } })
  ]);

  if (!resFactura || !resFactura.cabecera) {
    Swal.fire('Error', 'No se pudo cargar la factura', 'error');
    return;
  }

  let c = resFactura.cabecera;
  let totalFactura = parseFloat(c.total_factura || 0);
  let totalPagado = parseFloat(c.total_pagado || 0);
  let restante = (c.restante !== null && c.restante !== undefined) ? parseFloat(c.restante) : totalFactura;

  let bsRate = 1;
  let monDolar = resMonedas.find(m => m.nombre_moneda.toUpperCase() === 'DÓLAR' || m.nombre_moneda.toUpperCase() === 'DOLAR');
  if (monDolar) bsRate = parseFloat(monDolar.valor_moneda);

  let metodosOpt = resMetodos.filter(m => m.status == 1).map(m => `<option value="${m.id_metodo_pago}">${m.nombre_metodo_pago}</option>`).join('');
  let monedasOpt = resMonedas.filter(m => m.status == 1).map(m => `<option value="${m.id_moneda}" data-valor="${m.valor_moneda}">${m.nombre_moneda}</option>`).join('');

  // Seteamos la información en el modal estático
  $('#modalPagosFactura #tituloModalPagosFactura').html(`<i class="fi fi-rs-credit-card me-2"></i>Detalles del Pago (Factura ${idFactura})`);
  $('#modalPagosFactura #pagoTotalPagar').text(`$${totalFactura.toFixed(2)}`);
  $('#modalPagosFactura #pagoCancelado').html(`$${totalPagado.toFixed(2)} <small class="text-white fw-normal fs-6"> / Bs ${(totalPagado * bsRate).toFixed(2)}</small>`);
  $('#modalPagosFactura #pagoRestante').html(`$${restante.toFixed(2)} <small class="text-white fw-normal fs-6"> / Bs ${(restante * bsRate).toFixed(2)}</small>`);

  // Dejamos una sola fila de pago limpia y cargamos las opciones dinámicamente
  let firstRow = $('#modalPagosFactura #contenedorDetallesPago .fila-pago').first();
  firstRow.find('.btn-eliminar-pago').addClass('d-none');
  firstRow.find('.input-monto-pago').val('0.00');
  firstRow.find('.sel-metodo-pago').html(metodosOpt).prop('selectedIndex', 0);
  firstRow.find('.sel-moneda-pago').html(monedasOpt).prop('selectedIndex', 0);

  // Removemos cualquier otra fila de pago agregada previamente
  $('#modalPagosFactura #contenedorDetallesPago .fila-pago').not(':first').remove();

  let modalEl = document.getElementById('modalPagosFactura');
  let m = bootstrap.Modal.getOrCreateInstance(modalEl);
  m.show();

  let calcularRestanteModal = () => {
    let sumPagadoEnModal = 0;
    $('#modalPagosFactura .fila-pago').each(function () {
      let valInput = parseFloat($(this).find('.input-monto-pago').val() || 0);
      let monedaOpt = $(this).find('.sel-moneda-pago option:selected');
      let tasa = parseFloat(monedaOpt.data('valor') || 1);
      let nombreMoneda = monedaOpt.text().toUpperCase();

      if (nombreMoneda === 'BÓLIVAR' || nombreMoneda === 'BS') {
        sumPagadoEnModal += (valInput / bsRate);
      } else {
        sumPagadoEnModal += valInput;
      }
    });

    let nuevoRestante = restante - sumPagadoEnModal;
    let excede = nuevoRestante < -0.01;

    if (excede) {
      $('#pagoRestante').html(`<span class="text-danger">$${nuevoRestante.toFixed(2)}</span> <small class="text-danger fw-normal fs-6"> / Bs ${(nuevoRestante * bsRate).toFixed(2)} — ¡Excede el monto!</small>`);
      $('#btnConfirmarPago').prop('disabled', true).addClass('btn-secondary').removeClass('btn-primary');
    } else {
      $('#pagoRestante').html(`$${nuevoRestante.toFixed(2)} <small class="text-white fw-normal fs-6"> / Bs ${(nuevoRestante * bsRate).toFixed(2)}</small>`);
      $('#btnConfirmarPago').prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
    }
  };

  // Remueve eventos previos para evitar ejecuciones múltiples
  $(document).off('input', '#modalPagosFactura .input-monto-pago');
  $(document).off('change', '#modalPagosFactura .sel-moneda-pago');
  $(document).off('click', '#btnAgregarOtroPago');
  $(document).off('click', '#modalPagosFactura .btn-eliminar-pago');
  $(document).off('click', '#btnConfirmarPago');

  $(document).on('input', '#modalPagosFactura .input-monto-pago', calcularRestanteModal);
  $(document).on('change', '#modalPagosFactura .sel-moneda-pago', calcularRestanteModal);

  $(document).on('click', '#btnAgregarOtroPago', function () {
    let clone = $('#modalPagosFactura .fila-pago').first().clone();
    clone.find('.btn-eliminar-pago').removeClass('d-none');
    clone.find('.input-monto-pago').val('0.00');
    clone.find('.sel-metodo-pago').prop('selectedIndex', 0);
    clone.find('.sel-moneda-pago').prop('selectedIndex', 0);
    $('#modalPagosFactura #contenedorDetallesPago').append(clone);
  });

  $(document).on('click', '#modalPagosFactura .btn-eliminar-pago', function () {
    $(this).closest('.fila-pago').remove();
    calcularRestanteModal();
  });

  $(document).on('click', '#btnConfirmarPago', async function () {
    let pagosEnvio = [];
    let valido = true;
    $('#modalPagosFactura .fila-pago').each(function () {
      let metodo = $(this).find('.sel-metodo-pago').val();
      let moneda = $(this).find('.sel-moneda-pago').val();
      let monto = parseFloat($(this).find('.input-monto-pago').val());

      if (isNaN(monto) || monto <= 0) {
        valido = false;
      }
      pagosEnvio.push({
        id_metodo_pago: metodo,
        id_moneda: moneda,
        monto_pago: monto
      });
    });

    if (!valido || pagosEnvio.length === 0) {
      Swal.fire('Atención', 'Asegúrese de ingresar montos válidos mayores a 0', 'warning');
      return;
    }

    // Validar que la suma no exceda el restante
    let sumEnvio = 0;
    pagosEnvio.forEach(p => {
      let monedaOpt = $('#modalPagosFactura .fila-pago').eq(pagosEnvio.indexOf(p)).find('.sel-moneda-pago option:selected');
      let nombreMoneda = monedaOpt.text().toUpperCase();
      if (nombreMoneda === 'BÓLIVAR' || nombreMoneda === 'BS') {
        sumEnvio += (p.monto_pago / bsRate);
      } else {
        sumEnvio += p.monto_pago;
      }
    });
    if (sumEnvio > restante + 0.01) {
      Swal.fire('Atención', 'El monto total de los pagos excede el restante de la factura.', 'warning');
      return;
    }

    let resp = await pedirDatosAjax({
      modulo: 'facturacion',
      datosPe: {
        accion: 'registrarPago',
        id_factura: idFactura,
        pagos: JSON.stringify(pagosEnvio)
      }
    });

    if (resp.icono === 'success') {
      m.hide();
      reiniciarDataTables();
      Swal.fire(resp.titulo, resp.texto, resp.icono);
    } else {
      Swal.fire(resp.titulo, resp.texto, resp.icono);
    }
  });
}

$(document).on('click', '.btnAbrirPagoDesdeDetalle', function () {
  let id = $(this).data('id');
  $('.modalDetallesFactura').modal('hide');
  setTimeout(() => abrirModalPagos(id), 400);
});

// Despachar factura desde el detalle
$(document).on('click', '.btnDespacharFactura', async function () {
  let id = $(this).data('id');
  let confirm = await Swal.fire({
    title: '¿Despachar factura?',
    html: `Se marcará la factura <strong>${id}</strong> como despachada.<br><small class="text-muted">Si la factura ya está pagada, pasará a "Pagada y Despachada". 
    De lo contrario, quedará como "Despachada y sin Pago".</small>`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#17a2b8',
    confirmButtonText: '<i class="fi fi-rs-truck-side me-1"></i>Sí, despachar',
    cancelButtonText: 'Cancelar'
  });
  if (confirm.isConfirmed) {
    let resp = await pedirDatosAjax({
      modulo: 'facturacion', noGuardarLocal: true,
      datosPe: { accion: 'despachar', id_factura: id }
    });
    if (resp && resp.icono === 'success') {
      $('.modalDetallesFactura').modal('hide');
      reiniciarDataTables();
    }
    Swal.fire(resp.titulo, resp.texto, resp.icono);
  }
});
//#endregion [LOGICA DE ESTADOS Y PAGOS] FIN
