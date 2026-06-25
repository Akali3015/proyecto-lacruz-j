import { alertasAjax, encabezadosPeticiones, rutaAbsoluta } from './global.js';

const DashboardApp = (function() {
  
  // Diccionario interno para manejar instancias de las gráficas
  const chartsRegistry = {};
  
  // Colores corporativos
  const brandColors = ['#6554C0', '#00d2ff', '#8f94fb', '#4e54c8', '#17a2b8', '#007bff', '#6610f2'];

  // Cargar dependencias de jsPDF si no existen
  const loadPDFDependencies = () => {
    if (typeof window.jspdf === 'undefined') {
      const script = document.createElement('script');
      script.src = window.location.origin + '/proyecto-lacruz-j/src/assets/js/jspdf.umd.min.js';
      document.head.appendChild(script);
    }
  };

  // Setup Inicial
  const init = () => {
    loadPDFDependencies();
    configurarChartJS();
    vincularEventos();
    cargarDatos();
  };

  const configurarChartJS = () => {
    try {
      Chart.defaults.font.family = "'Inter', 'Segoe UI', Roboto, Helvetica, Arial, sans-serif";
      Chart.defaults.color = '#6c757d';
      if (!Chart.defaults.plugins) Chart.defaults.plugins = {};
      if (!Chart.defaults.plugins.tooltip) Chart.defaults.plugins.tooltip = {};
      Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(0, 0, 0, 0.8)';
      Chart.defaults.plugins.tooltip.padding = 10;
      Chart.defaults.plugins.tooltip.cornerRadius = 8;
      if (!Chart.defaults.animation) Chart.defaults.animation = {};
      Chart.defaults.animation.duration = 1500;
      Chart.defaults.animation.easing = 'easeOutQuart';
    } catch (e) {
      console.warn("No se pudieron configurar los defaults de Chart.js", e);
    }
  };

  const vincularEventos = () => {
    $('#btnRecargarDashboard').on('click', cargarDatos);

    $('#filtroTiempoDashboard').on('change', function () {
      if ($(this).val() === 'personalizado') {
        $('#contenedorFechasPersonalizadas').removeClass('d-none');
        if ($('#fechaInicioDash').val() && $('#fechaFinDash').val()) cargarDatos();
      } else {
        $('#contenedorFechasPersonalizadas').addClass('d-none');
        cargarDatos();
      }
    });

    $('#fechaInicioDash, #fechaFinDash').on('change', function() {
      if ($('#fechaInicioDash').val() && $('#fechaFinDash').val()) cargarDatos();
    });

    $('.btn-export').on('click', function(e) {
      e.preventDefault();
      const canvasId = $(this).data('chart');
      const filename = $(this).closest('.card').find('.card-title').text().trim() || 'Reporte';
      exportarAPDF(canvasId, filename);
    });

    let resizeTimer;
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(() => {
        Object.values(chartsRegistry).forEach(chart => {
          try {
            if (chart && typeof chart.resize === 'function') {
              if (chart.canvas && chart.canvas.offsetParent !== null) {
                chart.resize();
                if (typeof chart.update === 'function') chart.update();
              }
            }
          } catch (e) {
            console.warn("Resize Warning:", e);
          }
        });
      }, 150);
    });
  };

  const cargarDatos = async () => {
    const rango = $('#filtroTiempoDashboard').val();
    const fInicio = $('#fechaInicioDash').val();
    const fFin = $('#fechaFinDash').val();

    if (rango === 'personalizado' && (!fInicio || !fFin)) {
      alertasAjax({ icono: 'warning', titulo: 'Aviso', texto: 'Seleccione un rango de fechas válido.' });
      return;
    }

    const formData = new FormData();
    formData.append('accion', 'obtenerDatosDashboard');
    formData.append('rango', rango);
    if (rango === 'personalizado') {
      formData.append('fecha_inicio', fInicio);
      formData.append('fecha_fin', fFin);
    }

    try {
      const response = await fetch(rutaAbsoluta + 'reportesEstadisticos', {
        method: 'POST',
        headers: encabezadosPeticiones,
        body: formData
      });

      const data = await response.json();
      if (data.tipo === 'datos') {
        renderizar(data.datos);
      } else {
        alertasAjax(data);
      }
    } catch (error) {
      console.error("Error al cargar dashboard:", error);
      alertasAjax({ icono: 'error', titulo: 'Error', texto: 'No se pudo conectar con el servidor.' });
    }
  };

  // Destruir una gráfica existente para evitar "ghosting"
  const clearChart = (id) => {
    if (chartsRegistry[id]) {
      chartsRegistry[id].destroy();
      delete chartsRegistry[id];
    }
  };

  // Manejador de estado vacío
  const toggleEmptyState = (canvasId, isEmpty) => {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    const container = canvas.closest('.chart-container');
    const emptyState = container.querySelector('.empty-state');
    
    if (isEmpty) {
      canvas.style.display = 'none';
      if (emptyState) emptyState.classList.add('active');
    } else {
      canvas.style.display = 'block';
      if (emptyState) emptyState.classList.remove('active');
    }
  };



  // Plugin dinámico para texto central de la Dona
  const centerTextPlugin = (defaultText) => {
    return {
      id: 'centerTextPlugin_' + Math.random().toString(36).substring(2, 9),
      beforeDraw(chart) {
        try {
          if (!chart.data.datasets.length) return;
          const ctx = chart.ctx;
          let total = 0;
          chart.data.datasets[0].data.forEach((val, i) => {
            if (chart.getDataVisibility(i)) total += parseFloat(val);
          });

          let activeIndex = -1;
          let maxVal = -1;

          if (chart.getActiveElements().length > 0) {
            activeIndex = chart.getActiveElements()[0].index;
          } else {
            chart.data.datasets[0].data.forEach((val, i) => {
              if (chart.getDataVisibility(i) && parseFloat(val) > maxVal) {
                maxVal = parseFloat(val);
                activeIndex = i;
              }
            });
          }

          let mainText = defaultText;
          let subText = total > 0 ? "100%" : "0%";

          if (activeIndex !== -1 && total > 0) {
            const val = parseFloat(chart.data.datasets[0].data[activeIndex]);
            subText = Math.round((val / total) * 100) + "%";
            mainText = (chart.data.labels[activeIndex] || '').toString();
            if(mainText.length > 15) mainText = mainText.substring(0, 15) + "...";
          }

          const meta = chart.getDatasetMeta(0);
          let innerRadius = meta.data[0] ? meta.data[0].innerRadius : 0;
          let centerX = (meta.data[0] && !isNaN(meta.data[0].x)) ? meta.data[0].x : chart.chartArea.left + (chart.chartArea.right - chart.chartArea.left) / 2;
          let centerY = (meta.data[0] && !isNaN(meta.data[0].y)) ? meta.data[0].y : chart.chartArea.top + (chart.chartArea.bottom - chart.chartArea.top) / 2;

          if (innerRadius > 0 && !isNaN(centerX) && !isNaN(centerY)) {
            ctx.save();
            ctx.textBaseline = "middle";
            
            ctx.font = "bold " + (innerRadius / 35).toFixed(2) + "em Inter";
            ctx.fillStyle = "#1a1a1a";
            ctx.fillText(subText, centerX - (ctx.measureText(subText).width / 2), centerY - (innerRadius * 0.15));
            
            ctx.font = "600 " + (innerRadius / 70).toFixed(2) + "em Inter";
            ctx.fillStyle = "#6c757d";
            ctx.fillText(mainText.toUpperCase(), centerX - (ctx.measureText(mainText.toUpperCase()).width / 2), centerY + (innerRadius * 0.25));
            ctx.restore();
          }
        } catch (err) {
          console.warn("centerTextPlugin error:", err);
        }
      }
    };
  };

  const renderizar = (datos) => {
    // KPIs
    $('#metricPendientes').text(datos.cuentasPorCobrar?.pendientes || 0);
    $('#metricPagadas').text(datos.cuentasPorCobrar?.pagadas || 0);
    $('#metricProductos').text(datos.productosVsServicios?.productos || 0);
    $('#metricServicios').text(datos.productosVsServicios?.servicios || 0);

    // Actividad Reciente
    if (datos.actividadReciente && datos.actividadReciente.length > 0) {
      let htmlActividad = '';
      datos.actividadReciente.forEach(act => {
        let icono = act.tipo === 'Venta' ? '<i class="fi fi-rr-receipt text-primary"></i>' : '<i class="fi fi-rr-box text-info"></i>';
        htmlActividad += `
          <div class="d-flex align-items-center mb-3">
            <div class="rounded-circle d-flex justify-content-center align-items-center me-3 shadow-sm" style="width: 40px; height: 40px; background-color: #f8f9fc; color: #6554C0;">
              ${icono}
            </div>
            <div class="flex-grow-1">
              <h6 class="m-0 fw-bold text-dark" style="font-size: 0.9rem;">${act.tipo} Registrada</h6>
              <small class="text-muted">${act.referencia} - N° ${act.id}</small>
            </div>
            <div class="text-end">
              <small class="text-muted d-block" style="font-size: 0.75rem;">${act.fecha.split(' ')[0]}</small>
            </div>
          </div>`;
      });
      $('#listaActividadReciente').html(htmlActividad);
    } else {
      $('#listaActividadReciente').html('<p class="text-muted text-center mt-4">No hay actividad reciente</p>');
    }

    // Gráfica: Ingresos vs Egresos (Líneas)
    clearChart('chartIngresosEgresos');
    const ieEmpty = !datos.ingresosEgresos || datos.ingresosEgresos.fechas.length === 0;
    toggleEmptyState('chartIngresosEgresos', ieEmpty);
    if (!ieEmpty) {
      const ctx = $('#chartIngresosEgresos')[0].getContext('2d');
      const gradIngresos = ctx.createLinearGradient(0,0,0,400); gradIngresos.addColorStop(0, 'rgba(101, 84, 192, 0.5)'); gradIngresos.addColorStop(1, 'rgba(101, 84, 192, 0.0)');
      const gradEgresos = ctx.createLinearGradient(0,0,0,400); gradEgresos.addColorStop(0, 'rgba(0, 210, 255, 0.5)'); gradEgresos.addColorStop(1, 'rgba(0, 210, 255, 0.0)');
      
      chartsRegistry['chartIngresosEgresos'] = new Chart(ctx, {
        type: 'line',
        data: {
          labels: datos.ingresosEgresos.fechas,
          datasets: [
            { label: 'Ventas', data: datos.ingresosEgresos.ingresos, borderColor: '#6554C0', backgroundColor: gradIngresos, fill: true, tension: 0.4, borderWidth: 3 },
            { label: 'Compras', data: datos.ingresosEgresos.egresos, borderColor: '#00d2ff', backgroundColor: gradEgresos, fill: true, tension: 0.4, borderWidth: 3 }
          ]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top' } } }
      });
    }

    // Gráfica: Cuentas por Cobrar (Dona)
    clearChart('chartCuentasCobrar');
    const ccEmpty = !datos.cuentasPorCobrar || datos.cuentasPorCobrar.vacio;
    toggleEmptyState('chartCuentasCobrar', ccEmpty);
    if (!ccEmpty) {
      chartsRegistry['chartCuentasCobrar'] = new Chart($('#chartCuentasCobrar'), {
        type: 'doughnut',
        data: { labels: ['Pendientes', 'Pagadas'], datasets: [{ data: [datos.cuentasPorCobrar.pendientes, datos.cuentasPorCobrar.pagadas], backgroundColor: ['#6554C0', '#00d2ff'], borderWidth: 0 }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { position: 'bottom' } } },
        plugins: [centerTextPlugin("FINANZAS")]
      });
    }

    // Gráfica: Prod vs Serv (Barra)
    clearChart('chartProdVsServ');
    const pvsEmpty = !datos.productosVsServicios || datos.productosVsServicios.vacio;
    toggleEmptyState('chartProdVsServ', pvsEmpty);
    if (!pvsEmpty) {
      chartsRegistry['chartProdVsServ'] = new Chart($('#chartProdVsServ'), {
        type: 'bar',
        data: { labels: ['Total'], datasets: [{ label: 'Productos', data: [datos.productosVsServicios.productos], backgroundColor: '#6554C0', borderRadius: 8 }, { label: 'Servicios', data: [datos.productosVsServicios.servicios], backgroundColor: '#00d2ff', borderRadius: 8 }] },
        options: { responsive: true, maintainAspectRatio: false, scales: { x: { display: false } }, plugins: { legend: { position: 'bottom' } } }
      });
    }

    // Gráfica: Ventas Por Dia (Barra)
    clearChart('chartVentasPorDia');
    const vpdEmpty = !datos.ventasPorDia || datos.ventasPorDia.labels.length === 0;
    toggleEmptyState('chartVentasPorDia', vpdEmpty);
    if (!vpdEmpty) {
      chartsRegistry['chartVentasPorDia'] = new Chart($('#chartVentasPorDia'), {
        type: 'bar',
        data: { labels: datos.ventasPorDia.labels, datasets: [{ label: 'Ventas', data: datos.ventasPorDia.data, backgroundColor: '#6554C0', borderRadius: 8 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
      });
    }

    // Gráficas Donas Top (Productos, Servicios, Materias)
    const renderDoughnut = (id, labelKey, dataObj, title) => {
      clearChart(id);
      const isEmpty = !dataObj || dataObj.labels.length === 0;
      toggleEmptyState(id, isEmpty);
      if (!isEmpty) {
        chartsRegistry[id] = new Chart($(`#${id}`), {
          type: 'doughnut',
          data: { labels: dataObj.labels, datasets: [{ data: dataObj.data, backgroundColor: brandColors, borderWidth: 0 }] },
          options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { position: 'bottom' } } },
          plugins: [centerTextPlugin(title)]
        });
      }
    };
    renderDoughnut('chartTopProductos', 'Productos', datos.topProductos, 'TOP PROD');
    renderDoughnut('chartTopServicios', 'Servicios', datos.topServicios, 'TOP SERV');
    renderDoughnut('chartMateriasPrimas', 'Materias', datos.consumoMateriasPrimas, 'TOP MATERIA');

    // Gráficas Barras Horizontales Top (Clientes, Proveedores)
    const renderHBar = (id, labelKey, dataObj) => {
      clearChart(id);
      const isEmpty = !dataObj || dataObj.labels.length === 0;
      toggleEmptyState(id, isEmpty);
      if (!isEmpty) {
        chartsRegistry[id] = new Chart($(`#${id}`), {
          type: 'bar',
          data: { labels: dataObj.labels, datasets: [{ label: 'Total', data: dataObj.data, backgroundColor: brandColors, borderRadius: 8 }] },
          options: { 
            indexAxis: 'y', 
            responsive: true, 
            maintainAspectRatio: false, 
            plugins: { legend: { display: false } },
            scales: {
              y: {
                ticks: {
                  callback: function(value) {
                    let label = (this.getLabelForValue(value) || '').toString();
                    return label.length > 15 ? label.substring(0, 15) + '...' : label;
                  }
                }
              }
            }
          }
        });
      }
    };
    renderHBar('chartTopClientes', 'Clientes', datos.topClientes);
    renderHBar('chartTopProveedores', 'Proveedores', datos.topProveedores);

    // Gráfica: Producción (Línea)
    clearChart('chartProduccion');
    const prdEmpty = !datos.historialProduccion || datos.historialProduccion.labels.length === 0;
    toggleEmptyState('chartProduccion', prdEmpty);
    if (!prdEmpty) {
      chartsRegistry['chartProduccion'] = new Chart($('#chartProduccion'), {
        type: 'line',
        data: { labels: datos.historialProduccion.labels, datasets: [{ label: 'Producción', data: datos.historialProduccion.data, borderColor: '#8f94fb', backgroundColor: 'rgba(143, 148, 251, 0.2)', fill: true, tension: 0.4, borderWidth: 3 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top' } } }
      });
    }

  };

  const exportarAPDF = (canvasId, filename) => {
    const canvas = document.getElementById(canvasId);
    if (!canvas || canvas.style.display === 'none') {
      alertasAjax({ icono: 'warning', titulo: 'Sin Datos', texto: 'No hay datos en esta gráfica para exportar.' });
      return;
    }

    const newCanvas = document.createElement('canvas');
    newCanvas.width = canvas.width;
    newCanvas.height = canvas.height;
    const ctx = newCanvas.getContext('2d');
    ctx.fillStyle = '#FFFFFF';
    ctx.fillRect(0, 0, newCanvas.width, newCanvas.height);
    ctx.drawImage(canvas, 0, 0);

    const imgData = newCanvas.toDataURL('image/jpeg', 1.0);

    if (typeof window.jspdf === 'undefined') {
      alertasAjax({ icono: 'error', titulo: 'Error', texto: 'Librería PDF no cargada.' });
      return;
    }

    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF(newCanvas.width > newCanvas.height ? 'landscape' : 'portrait', 'pt', 'a4');
    
    pdf.setFontSize(18);
    pdf.setTextColor(40, 40, 40);
    pdf.text(filename, 40, 40);
    
    const dateStr = new Date().toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    pdf.setFontSize(10);
    pdf.setTextColor(120, 120, 120);
    pdf.text("Reporte estadístico generado el: " + dateStr, 40, 60);
    
    const maxImgWidth = pdf.internal.pageSize.getWidth() - 80;
    const maxImgHeight = pdf.internal.pageSize.getHeight() - 100;
    const ratio = Math.min(maxImgWidth / newCanvas.width, maxImgHeight / newCanvas.height);
    
    pdf.addImage(imgData, 'JPEG', 40, 80, newCanvas.width * ratio, newCanvas.height * ratio);
    pdf.save(filename + '.pdf');
  };

  return { init };

})();

$(document).ready(() => {
  DashboardApp.init();
});
