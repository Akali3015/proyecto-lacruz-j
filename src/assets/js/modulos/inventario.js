//#region [ IMPORTACIONES ] COMIENZO
import {
  pedirDatosAjax,
  españolDataTable,
  alertasAjax,
  instanciasDatatable,
  cambiarFormatos,
  rutaAbsoluta,
  validarEnTiempoReal,
  reiniciarDataModuloSS,
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
import { driverAyuda, mostrarAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"


//#endregion [ IMPORTACIONES ] FIN

//#region [ VARIABLES O CONSTANTES GLOBALES ] COMIENZO
let tablasInicializadas = {
  productos: false,
  materiasPrimas: false
};
let tablaMovimientosProducto = null;
let tablaMovimientosMateriaPrima = null;
let graficaProductos = null;
let graficaMateriasPrimas = null;
//#endregion [ VARIABLES O CONSTANTES GLOBALES ] FIN

//#region [ FUNCIONES PROPIAS DEL MODULO ] COMIENZO

// Función para registrar el tutorial
function registrarTutorial() {
  driverAyuda('inventario', {
    pasos: [
      {
        element: '#tablaProductos',
        popover: {
          title: 'Lista de Productos',
          description: 'Aquí puedes ver todos los productos registrados, su stock actual y precio.',
          side: 'top'
        }
      },

      {
        element: '.botonRegistrarCarga',
        popover: {
          title: 'Registrar Movimiento',
          description: 'Haz clic aquí para registrar una carga o descarga de stock (entradas/salidas anómalas).',
          side: 'left'
        }
      },
      {
        element: '.botonVerES',
        popover: {
          title: 'Ver Historial',
          description: 'Consulta el historial completo de movimientos (entradas y salidas) de cada producto o materia prima.',
          side: 'left'
        }
      },
      {
        element: '.botonImprimirES',
        popover: {
          title: 'Generar Reporte',
          description: 'Genera un reporte PDF de los movimientos en un rango de fechas específico.',
          side: 'left'
        }
      },
      {
        element: '#graficaStockProductos',
        popover: {
          title: 'Gráfica de Stock Crítico',
          description: 'Visualiza los productos con stock bajo o crítico para tomar acciones preventivas.',
          side: 'top',
          align: 'start'
        }
      },
      {
        popover: {
          title: '¡Ayuda completada!',
          description: 'Ya conoces la gestión de inventario. Puedes registrar movimientos, ver historial y generar reportes.',
          side: 'top'
        }
      }
    ]
  });
}

function colorStock(stockActual, stockMinimo) {
  const minimo = stockMinimo || 0;
  if (stockActual <= 0) return 'badge bg-danger';
  if (stockActual < minimo) return 'badge bg-danger';
  if (stockActual >= minimo && stockActual <= minimo * 2) return 'badge bg-warning text-dark';
  return 'badge bg-success';
}

function inicializarDataTable({ selectorTabla, datos, encabezados, campoIdBtn, infoTratoEspecial }) {
  if ($.fn.DataTable.isDataTable(selectorTabla)) {
    $(selectorTabla).DataTable().destroy();
  }

  const columnas = Object.keys(encabezados).map(key => {
    const columna = {
      data: key, title: encabezados[key]
    };
    if (infoTratoEspecial && infoTratoEspecial[key]) {
      columna.render = function (valor, type, fila) {
        return infoTratoEspecial[key]({ valor, fila });
      };
    }
    return columna;
  });

  columnas.push({
    data: null, title: 'ACCIONES', orderable: false,
    render: function (data, type, row) {
      return `
        <ul class="list-inline me-auto mb-0 d-flex justify-content-center gap-2">
          <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Ver Entradas y Salidas">
            <a href="#" value="${row[campoIdBtn]}" class="botonVerES avtar avtar-xs btn-link-info btn-pc-default">
              <i class="fi fi-rs-arrows-retweet fs-4 iconoCentrado"></i>
            </a>
          </li>
          <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Imprimir Entradas y Salidas">
            <a href="#" value="${row[campoIdBtn]}" class="botonImprimirES avtar avtar-xs btn-link-secondary btn-pc-default">
              <i class="fi fi-rs-print fs-4 iconoCentrado"></i>
            </a>
          </li>
          <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Registrar Carga/Descarga">
            <a href="#" value="${row[campoIdBtn]}" class="botonRegistrarCarga avtar avtar-xs btn-link-success btn-pc-default">
              <i class="fi fi-rs-boxes fs-4 iconoCentrado"></i>
            </a>
          </li>
        </ul>`;
    }
  });

  const tabla = $(selectorTabla).DataTable({
    data: datos, columns: columnas, autoWidth: false, order: [[0, 'desc']],
    language: españolDataTable,
    columnDefs: [{ className: 'dt-center alineado_vertical', targets: '_all' }]
  });

  instanciasDatatable.push(tabla);
  return tabla;
}

function filtrarStockCritico(datos, tipo) {
  if (!Array.isArray(datos)) return [];

  const campoStock = tipo === 'materiasPrimas' ? 'stock_materia_prima' : 'stock_producto';
  const campoStockMinimo = tipo === 'materiasPrimas' ? 'stock_minimo_materia_prima' : 'stock_minimo_producto';

  return datos.filter(item => {
    const stock = item[campoStock] || 0;
    const stockMinimo = item[campoStockMinimo] || 0;
    return stock <= stockMinimo;
  }).sort((a, b) => {
    return (a[campoStock] || 0) - (b[campoStock] || 0);
  }).slice(0, 10);
}

function crearGraficaStockCritico(canvasId, datos, tipoItem) {
  const ctx = document.getElementById(canvasId);
  const sinDatosId = 'sinDatosGrafica' + tipoItem;
  const sinDatosDiv = document.getElementById(sinDatosId);
  if (!ctx) return null;

  if (!datos || datos.length === 0) {
    ctx.style.display = 'none';
    if (sinDatosDiv) sinDatosDiv.style.display = 'block';
    return null;
  }
  ctx.style.display = 'block';
  if (sinDatosDiv) sinDatosDiv.style.display = 'none';

  const chartInstance = Chart.getChart(canvasId);
  if (chartInstance) chartInstance.destroy();

  const esMateriaPrima = tipoItem === 'MateriasPrimas';
  const campoNombre = esMateriaPrima ? 'nombre_materia_prima' : 'nombre_producto';
  const campoStock = esMateriaPrima ? 'stock_materia_prima' : 'stock_producto';
  const campoStockMinimo = esMateriaPrima ? 'stock_minimo_materia_prima' : 'stock_minimo_producto';

  const nombres = datos.map(item => {
    const nombre = item[campoNombre] || 'N/A';
    return nombre.length > 20 ? nombre.substring(0, 20) + '...' : nombre;
  });
  const stocks = datos.map(item => item[campoStock] || 0);
  const stocksMinimos = datos.map(item => item[campoStockMinimo] || 0);
  const colores = stocks.map(stock => stock <= 0 ? '#dc3545' : stock <= 5 ? '#fd7e14' : '#ffc107');

  return new Chart(ctx, {
    type: 'bar',
    data: {
      labels: nombres,
      datasets: [
        {
          label: 'Stock Actual',
          data: stocks,
          backgroundColor: colores,
          borderColor: colores,
          borderWidth: 1,
          borderRadius: 4,
          barPercentage: 0.6,
          categoryPercentage: 0.8
        },
        {
          label: 'Stock Minimo',
          data: stocksMinimos,
          type: 'line',
          borderColor: '#dc3545',
          backgroundColor: 'transparent',
          borderWidth: 2,
          borderDash: [5, 5],
          pointStyle: 'rectRounded',
          pointRadius: 4,
          pointBackgroundColor: '#dc3545',
          fill: false
        }
      ]
    },
    options: {
      indexAxis: 'y',
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: true, position: 'top', labels: { boxWidth: 12, padding: 8, font: { size: 11 } } },
        tooltip: {
          callbacks: {
            label: function (context) {
              return `${context.dataset.label}: ${context.raw}`;
            }
          }
        }
      },
      scales: {
        x: {
          beginAtZero: true,
          title: { display: true, text: 'Unidades', font: { size: 11 } },
          ticks: { font: { size: 10 }, stepSize: 5 },
          grid: { display: true, color: 'rgba(0,0,0,0.05)' }
        },
        y: { ticks: { font: { size: 10 } }, grid: { display: false } }
      }
    }
  });
}

async function verEntradasSalidas(boton) {
  const id = $(boton).attr('value');
  const tabActivo = $('#inventarioTabs .nav-link.active').attr('href');
  const tipo = tabActivo.replace('#pane-', '');
  if (tipo === 'productos') {
    await verMovimientosProducto(id);
  } else if (tipo === 'materiasPrimas') {
    await verMovimientosMateriaPrima(id);
  }
}

async function verMovimientosProducto(idProducto) {
  const modal = $('.modalVerMovimientosProducto');
  const tbody = modal.find('#tbodyMovimientosProducto');
  const sinMovimientos = modal.find('.sinMovimientosProducto');
  tbody.empty(); sinMovimientos.hide();
  if (tablaMovimientosProducto && $.fn.DataTable.isDataTable('#tablaMovimientosProducto')) {
    tablaMovimientosProducto.destroy();
    tablaMovimientosProducto = null;
  }
  try {
    modal.find('.textoNombreItemMovimientoProducto').text('Cargando...');
    modal.find('.textoIdItemMovimientoProducto').text('');
    modal.modal('show');

    const datos = await pedirDatosAjax({
      modulo: 'inventario',
      datosPe: {
        accion: 'verEntradasSalidas',
        tipo: 'productos',
        id_producto: idProducto
      }
    });

    if (datos && datos.icono === 'error') {
      Swal.fire({ icon: 'error', title: 'Error', text: datos.texto || 'Error al cargar movimientos', confirmButtonText: 'Aceptar' });
      modal.modal('hide'); return;
    }

    if (Array.isArray(datos) && datos.length > 0) {
      modal.find('.textoNombreItemMovimientoProducto').text(datos[0].nombre_producto || 'N/A');
      modal.find('.textoIdItemMovimientoProducto').text('ID: ' + idProducto);
      datos.forEach(movimiento => {
        const tipoBadge = movimiento.tipo_movimiento == 1 ? '<span class="badge bg-success">Carga (+)</span>' : '<span class="badge bg-danger">Descarga (-)</span>';
        const fechaCambio = movimiento.fecha_movimiento ? cambiarFormatos(movimiento.fecha_movimiento, 'fecha_hora') : 'N/A';
        tbody.append(`
          <tr>
            <td>${movimiento.id_movimiento_anomalo_producto || 'N/A'}</td>
            <td>${movimiento.nombre_presentacion || 'N/A'}</td>
            <td>${tipoBadge}</td>
            <td>${movimiento.cantidad_movimiento}</td>
            <td>${movimiento.motivo_movimiento || 'N/A'}</td>
            <td>${fechaCambio}</td>
          </tr>
        `);
      });
      sinMovimientos.hide();
      setTimeout(() => {
        tablaMovimientosProducto = $('#tablaMovimientosProducto').DataTable({
          order: [[0, 'desc']], language: españolDataTable,
          columnDefs: [{ className: 'dt-center alineado_vertical', targets: '_all' }],
          pageLength: 5, lengthMenu: [5, 10, 25, 50]
        });
      }, 200);
    } else {
      const producto = await pedirDatosAjax({
        modulo: 'productos',
        datosPe: {
          accion: 'seleccionarUno',
          id_producto: idProducto
        }
      });
      modal.find('.textoNombreItemMovimientoProducto').text(producto && !producto.icono ? producto.nombre_producto : 'Producto no encontrado');
      modal.find('.textoIdItemMovimientoProducto').text('ID: ' + idProducto);
      sinMovimientos.show();
    }
  } catch (error) {
    console.error('Error:', error);
    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudieron cargar los movimientos' });
    modal.modal('hide');
  }
}

async function verMovimientosMateriaPrima(idMateriaPrima) {
  const modal = $('.modalVerMovimientosMateriaPrima');
  const tbody = modal.find('#tbodyMovimientosMateriaPrima');
  const sinMovimientos = modal.find('.sinMovimientosMateriaPrima');
  tbody.empty(); sinMovimientos.hide();
  if (tablaMovimientosMateriaPrima && $.fn.DataTable.isDataTable('#tablaMovimientosMateriaPrima')) {
    tablaMovimientosMateriaPrima.destroy();
    tablaMovimientosMateriaPrima = null;
  }
  try {
    modal.find('.textoNombreMateriaPrimaMovimiento').text('Cargando...');
    modal.find('.textoIdMateriaPrimaMovimiento').text('');
    modal.modal('show');

    const datos = await pedirDatosAjax({
      modulo: 'inventario',
      datosPe: {
        accion: 'verEntradasSalidas',
        tipo: 'materiasPrimas',
        id_materia_prima: idMateriaPrima
      }
    });

    if (datos && datos.icono === 'error') {
      Swal.fire({ icon: 'error', title: 'Error', text: datos.texto || 'Error al cargar movimientos', confirmButtonText: 'Aceptar' });
      modal.modal('hide'); return;
    }

    if (Array.isArray(datos) && datos.length > 0) {
      modal.find('.textoNombreMateriaPrimaMovimiento').text(datos[0].nombre_materia_prima || 'N/A');
      modal.find('.textoIdMateriaPrimaMovimiento').text('ID: ' + idMateriaPrima);
      datos.forEach(movimiento => {
        const tipoBadge = movimiento.tipo_movimiento == 1 ? '<span class="badge bg-success">Carga (+)</span>' : '<span class="badge bg-danger">Descarga (-)</span>';
        const fechaCambio = movimiento.fecha_movimiento ? cambiarFormatos(movimiento.fecha_movimiento, 'fecha_hora') : 'N/A';
        tbody.append(`
          <tr>
            <td>${movimiento.id_movimiento_anomalo_materia_prima || 'N/A'}</td>
            <td>${movimiento.nombre_materia_prima || 'N/A'}</td>
            <td>${tipoBadge}</td>
            <td>${movimiento.cantidad_movimiento}</td>
            <td>${movimiento.motivo_movimiento || 'N/A'}</td>
            <td>${fechaCambio}</td>
          </tr>
        `);
      });
      sinMovimientos.hide();
      setTimeout(() => {
        tablaMovimientosMateriaPrima = $('#tablaMovimientosMateriaPrima').DataTable({
          order: [[0, 'desc']], language: españolDataTable,
          columnDefs: [{ className: 'dt-center alineado_vertical', targets: '_all' }],
          pageLength: 5, lengthMenu: [5, 10, 25, 50]
        });
      }, 200);
    } else {
      const materiaPrima = await pedirDatosAjax({
        modulo: 'materiasPrimas',
        datosPe: {
          accion: 'seleccionarUno',
          id_materia_prima: idMateriaPrima
        }
      });
      modal.find('.textoNombreMateriaPrimaMovimiento').text(materiaPrima && !materiaPrima.icono ? materiaPrima.nombre_materia_prima : 'Materia prima no encontrada');
      modal.find('.textoIdMateriaPrimaMovimiento').text('ID: ' + idMateriaPrima);
      sinMovimientos.show();
    }
  } catch (error) {
    console.error('Error:', error);
    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudieron cargar los movimientos' });
    modal.modal('hide');
  }
}

async function abrirModalRegistrarCarga(boton) {
  const id = $(boton).attr('value');
  const tabActivo = $('#inventarioTabs .nav-link.active').attr('href');
  const tipo = tabActivo.replace('#pane-', '');
  if (tipo === 'productos') {
    await abrirModalProducto(id);
  } else if (tipo === 'materiasPrimas') {
    await abrirModalMateriaPrima(id);
  }
}

async function abrirModalProducto(idProducto) {
  try {
    const producto = await pedirDatosAjax({
      modulo: 'productos',
      datosPe: {
        accion: 'seleccionarUno',
        id_producto: idProducto
      }
    });
    if (!producto || producto.icono === 'error') {
      Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo encontrar el producto' }); return;
    }
    const presentaciones = await pedirDatosAjax({
      modulo: 'productos',
      datosPe: {
        accion: 'listar',
        tipoConsulta: 'presentaciones',
        id_producto: idProducto
      }
    });

    const modal = $('.modalRegistrarAnomaliaProducto');
    const form = modal.find('form');
    form[0].reset();
    form.find('.mensajeError').remove();
    form.find('.error').removeClass('error');
    modal.find('.textoTipoItemModal').text('Producto');
    form.find('.inputNombreProductoAnomalia').val(producto.nombre_producto + ' (' + producto.id_producto + ')');
    const selectPresentacion = form.find('.selectPresentacionAnomalia');
    selectPresentacion.empty().append('<option value="">Seleccione una presentación</option>');
    if (Array.isArray(presentaciones) && presentaciones.length > 0) {
      presentaciones.forEach(pres => {
        selectPresentacion.append(`<option value="${pres.id_presentacion_producto}">${pres.nombre_presentacion} (${pres.cantidad_pmp} und.)</option>`);
      });
    } else {
      selectPresentacion.append('<option value="" disabled>No hay presentaciones disponibles</option>');
    }
    modal.modal('show');
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'No se pudo cargar la información del producto'
    });
  }
}

async function abrirModalMateriaPrima(idMateriaPrima) {
  try {
    const materiaPrima = await pedirDatosAjax({
      modulo: 'materiasPrimas',
      datosPe: {
        accion: 'seleccionarUno',
        id_materia_prima: idMateriaPrima
      }
    });
    if (!materiaPrima || materiaPrima.icono === 'error') {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'No se pudo encontrar la materia prima'
      }); return;
    }
    const modal = $('.modalRegistrarAnomaliaMateriaPrima');
    const form = modal.find('form');
    form[0].reset();
    form.find('.mensajeError').remove();
    form.find('.error').removeClass('error');
    form.find('.inputNombreMateriaPrimaAnomalia').val(materiaPrima.nombre_materia_prima + ' (' + materiaPrima.id_materia_prima + ')');
    form.find('.inputIdMateriaPrimaAnomalia').val(idMateriaPrima);
    modal.modal('show');
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'No se pudo cargar la información de la materia prima'
    });
  }
}

async function enviarFormularioAnomaliaProducto(e) {
  e.preventDefault();
  const form = $(this);
  const idPresentacion = form.find('.selectPresentacionAnomalia').val();

  if (!idPresentacion) {
    Swal.fire({
      icon: 'warning',
      title: 'Campo requerido',
      text: 'Debe seleccionar una presentación'
    });
    return;
  }

  // Confirmación simple antes de guardar
  const confirmacion = await Swal.fire({
    title: '¿Registrar movimiento?',
    text: '¿Está seguro de registrar este movimiento anómalo?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sí, registrar',
    cancelButtonText: 'Cancelar'
  });

  if (!confirmacion.isConfirmed) return;

  try {
    const resultado = await pedirDatosAjax({
      modulo: 'inventario',
      datosPe: {
        accion: 'registrarMovimientosProductos',
        id_presentacion_producto: idPresentacion,
        cantidad_movimiento: form.find('[name="cantidad_movimiento"]').val(),
        tipo_movimiento: form.find('[name="tipo_movimiento"]').val(),
        motivo_movimiento: form.find('[name="motivo_movimiento"]').val()
      }
    });
    if (resultado.icono === 'success') {
      $('.modalRegistrarAnomaliaProducto').modal('hide');
      reiniciarDataModuloSS(['inventario', 'productos', 'materiasPrimas']);
      recargarTablas();
    }
    await alertasAjax(resultado);
  } catch (error) {
    console.log(error);
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'No se pudo registrar la anomalía'
    });
  }
}

async function enviarFormularioAnomaliaMateriaPrima(e) {
  e.preventDefault();
  const form = $(this);
  const datosPe = {
    accion: 'registrarMovimientosMateriasPrimas',
    id_materia_prima: form.find('.inputIdMateriaPrimaAnomalia').val(),
    cantidad_movimiento: form.find('[name="cantidad_movimiento"]').val(),
    tipo_movimiento: form.find('[name="tipo_movimiento"]').val(),
    motivo_movimiento: form.find('[name="motivo_movimiento"]').val()
  };

  // Confirmación simple antes de guardar
  const confirmacion = await Swal.fire({
    title: '¿Registrar movimiento?',
    text: '¿Está seguro de registrar este movimiento anómalo?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sí, registrar',
    cancelButtonText: 'Cancelar'
  });

  if (!confirmacion.isConfirmed) return;

  try {
    const resultado = await pedirDatosAjax({
      modulo: 'inventario', datosPe: datosPe
    });
    if (resultado.icono === 'success') {
      $('.modalRegistrarAnomaliaMateriaPrima').modal('hide');
      reiniciarDataModuloSS(['inventario', 'productos', 'materiasPrimas']);
      recargarTablas();
    }
    await alertasAjax(resultado);
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'No se pudo registrar la anomalía'
    });
  }
}

function recargarTablas() {
  const tabActivo = $('#inventarioTabs .nav-link.active').attr('href');
  const tabName = tabActivo.replace('#pane-', '');
  if (tabName === 'productos' && graficaProductos) {
    graficaProductos.destroy();
    graficaProductos = null;
  }
  else if (tabName === 'materiasPrimas' && graficaMateriasPrimas) {
    graficaMateriasPrimas.destroy();
    graficaMateriasPrimas = null;
  }
  tablasInicializadas[tabName] = false;
  renderizarTablaPorTab(tabActivo);
}

async function renderizarTablaPorTab(hrefTab) {
  const tabName = hrefTab.replace('#pane-', '');
  if (!tabName || tablasInicializadas[tabName]) return;

  try {
    switch (tabName) {
      case 'productos': {
        const datos = await pedirDatosAjax({
          modulo: 'productos',
          datosPe: { accion: 'listar' }
        });
        const datosArray = Array.isArray(datos) ? datos : [];
        const datosCriticos = filtrarStockCritico(datosArray, 'productos');
        graficaProductos = crearGraficaStockCritico('graficaStockProductos', datosCriticos, 'Productos');
        inicializarDataTable({
          selectorTabla: '#tablaProductos', datos: datosArray,
          encabezados: {
            'id_producto': 'ID',
            'nombre_producto': 'Nombre',
            'stock_producto': 'Stock',
            'nombre_unidad_medida': 'Unidad',
            'precio_producto': 'Precio'
          },
          campoIdBtn: 'id_producto',
          infoTratoEspecial: { stock_producto: ({ valor, fila }) => `<span class="${colorStock(valor, fila.stock_minimo_producto)}">${valor}</span>` }
        });
        tablasInicializadas.productos = true;
        break;
      }
      case 'materiasPrimas': {
        const datos = await pedirDatosAjax({
          modulo: 'materiasPrimas',
          datosPe: {
            accion: 'listar'
          }
        });
        const datosArray = Array.isArray(datos) ? datos : [];
        const datosCriticos = filtrarStockCritico(datosArray, 'materiasPrimas');
        graficaMateriasPrimas = crearGraficaStockCritico('graficaStockMateriasPrimas', datosCriticos, 'MateriasPrimas');
        inicializarDataTable({
          selectorTabla: '#tablaMateriasPrimas', datos: datosArray,
          encabezados: {
            'id_materia_prima': 'ID',
            'nombre_materia_prima': 'Nombre',
            'stock_materia_prima': 'Stock',
            'nombre_unidad_medida': 'Unidad',
            'precio_materia_prima': 'Precio'
          },
          campoIdBtn: 'id_materia_prima',
          infoTratoEspecial: { stock_materia_prima: ({ valor, fila }) => `<span class="${colorStock(valor, fila.stock_minimo_materia_prima)}">${valor}</span>` }
        });
        tablasInicializadas.materiasPrimas = true;
        break;
      }
    }
  } catch (error) {
    console.error(`Error: ${tabName}:`, error);
  }
}
//#endregion [ FUNCIONES PROPIAS DEL MODULO ] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).ready(function () {

  // Registrar el tutorial
  registrarTutorial();

  let tabActivo = $('#inventarioTabs .nav-link.active').attr('href');
  if (tabActivo) renderizarTablaPorTab(tabActivo);

  $(document).on('shown.bs.tab', 'a[data-bs-toggle="tab"]', function (e) {
    let tabDestino = $(e.target).attr('href');
    renderizarTablaPorTab(tabDestino);
    setTimeout(() => {
      if ($.fn.dataTable.tables)
        $.fn.dataTable.tables({
          visible: true, api: true
        }).columns.adjust();
    }, 100);
  });

  $(document).on('click', '.botonRegistrarCarga', function (e) {
    e.preventDefault();
    abrirModalRegistrarCarga(this);
  });

  $(document).on('click', '.botonVerES', function (e) {
    e.preventDefault();
    verEntradasSalidas(this);
  });

  $(document).on('click', '.botonImprimirES', function (e) {
    e.preventDefault();
    const idItem = $(this).attr('value');
    const tabActivo = $('#inventarioTabs .nav-link.active').attr('href');
    const tipo = tabActivo.replace('#pane-', '');
    const modal = $('.modalFiltrosReporte');
    const form = modal.find('#formFiltrosReporte');
    form[0].reset();
    let tipoReporte = '', tituloReporte = '';
    switch (tipo) {
      case 'productos':
        tipoReporte = 'reporteProductos';
        tituloReporte = 'Productos';
        break;
      case 'materiasPrimas':
        tipoReporte = 'reporteMateriasPrimas';
        tituloReporte = 'Materias Primas';
        break;
    }
    modal.find('.textoTipoReporte').text(tituloReporte);
    modal.find('.inputTipoReporte').val(tipoReporte);
    modal.find('.inputIdItemReporte').val(idItem || '');
    const hoy = new Date(); const primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
    form.find('[name="fecha_desde"]').val(primerDia.toISOString().split('T')[0]);
    form.find('[name="fecha_hasta"]').val(hoy.toISOString().split('T')[0]);
    modal.modal('show');
  });

  $(document).on('click', '.btnGenerarReporte', async function () {
    const modal = $('.modalFiltrosReporte');
    const form = modal.find('#formFiltrosReporte');
    const fechaDesde = form.find('[name="fecha_desde"]').val();
    const fechaHasta = form.find('[name="fecha_hasta"]').val();
    const tipoReporte = form.find('.inputTipoReporte').val();
    const idItem = form.find('.inputIdItemReporte').val();

    const accion = tipoReporte === 'reporteMateriasPrimas' ? 'reporteMateriasPrimas' : 'reporteProductos';
    const datosPe = {
      accion,
      fecha_desde: fechaDesde,
      fecha_hasta: fechaHasta
    };
    if (idItem) {
      datosPe[tipoReporte === 'reporteMateriasPrimas' ? 'id_materia_prima' : 'id_producto'] = idItem;
    }
    try {
      const datos = await pedirDatosAjax({
        modulo: 'inventario',
        datosPe,
        noJSON: false
      });

      if (datos && datos.icono) {
        await alertasAjax(datos);
        if (datos.icono !== 'error') modal.modal('hide');
        return;
      }
      const formTemp = document.createElement('form');
      formTemp.method = 'POST';
      formTemp.action = rutaAbsoluta + 'inventario';
      formTemp.target = '_blank';
      for (const [key, value] of Object.entries(datosPe)) {
        const i = document.createElement('input');
        i.type = 'hidden';
        i.name = key; i.value = value;
        formTemp.appendChild(i);
      }
      document.body.appendChild(formTemp);
      formTemp.submit();
      document.body.removeChild(formTemp);
      modal.modal('hide');
    } catch (error) {
      const formTemp = document.createElement('form');
      formTemp.method = 'POST';
      formTemp.action = rutaAbsoluta + 'inventario';
      formTemp.target = '_blank';
      for (const [key, value] of Object.entries(datosPe)) {
        const i = document.createElement('input');
        i.type = 'hidden';
        i.name = key; i.value = value;
        formTemp.appendChild(i);
      }
      document.body.appendChild(formTemp); formTemp.submit();
      document.body.removeChild(formTemp);
      modal.modal('hide');
    }
  });

  $(document).off('submit', '.modalRegistrarAnomaliaProducto form');
  $(document).on('submit', '.modalRegistrarAnomaliaProducto form', enviarFormularioAnomaliaProducto);

  $(document).off('submit', '.modalRegistrarAnomaliaMateriaPrima form');
  $(document).on('submit', '.modalRegistrarAnomaliaMateriaPrima form', enviarFormularioAnomaliaMateriaPrima);

  $('.modalRegistrarAnomaliaProducto').on('hidden.bs.modal', function () {
    const form = $(this).find('form');
    form[0].reset();
    form.find('.selectPresentacionAnomalia').empty().append('<option value="">Seleccione una presentación</option>');
  });

  $('.modalRegistrarAnomaliaMateriaPrima').on('hidden.bs.modal', function () {
    $(this).find('form')[0].reset();
  });

  $(document).on('hidden.bs.modal', '.modalVerMovimientosProducto', function () {
    if (tablaMovimientosProducto && $.fn.DataTable.isDataTable('#tablaMovimientosProducto')) {
      tablaMovimientosProducto.destroy();
      tablaMovimientosProducto = null;
    }
    $('#tbodyMovimientosProducto').empty();
    $('.sinMovimientosProducto').hide();
  });

  $(document).on('hidden.bs.modal', '.modalVerMovimientosMateriaPrima', function () {
    if (tablaMovimientosMateriaPrima && $.fn.DataTable.isDataTable('#tablaMovimientosMateriaPrima')) {
      tablaMovimientosMateriaPrima.destroy();
      tablaMovimientosMateriaPrima = null;
    }
    $('#tbodyMovimientosMateriaPrima').empty();
    $('.sinMovimientosMateriaPrima').hide();
  });

  $('.modalFiltrosReporte').on('hidden.bs.modal', function () {
    $(this).find('form')[0].reset();
  });

  $(document).off('input', '.validar input, .validar select');
  $(document).on('input', '.validar input, .validar select', function () {
    validarEnTiempoReal(this, 'inventario');
  });

  // Verificar si hay un driver pendiente (redirección desde otro módulo)
  const driverPendiente = sessionStorage.getItem('driver_pendiente');
  if (driverPendiente === 'inventario') {
    sessionStorage.removeItem('driver_pendiente');
    setTimeout(() => {
      mostrarAyuda();
    }, 1000);
  }
});
//#endregion [DELEGACIÓN DE EVENTOS] FIN