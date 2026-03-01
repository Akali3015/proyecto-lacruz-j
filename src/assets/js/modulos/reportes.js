//#region [ IMPORTACIONES ] COMIENZO
import { alertasAjax, encabezadosPeticiones, rutaAbsoluta } from './global.js';
//#endregion [ IMPORTACIONES ] FIN

//#region [ FUNCIONES PROPIAS DEL MODULO ] COMIENZO

// Función para generar reporte de ventas
async function generarReporteVentas() {
  const form = document.getElementById('formReporteVentas');
  const formData = new FormData(form);
  const datos = {};
  formData.forEach((value, key) => {
    datos[key] = value;
  });
  datos.reporte = 'reporte_ventas';

  Swal.fire({
    title: 'Generando reporte',
    text: 'Por favor espere...',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });

  let resultado = await fetch(rutaAbsoluta + 'reportes', {
    method: 'POST',
    headers: encabezadosPeticiones,
    body: JSON.stringify(datos)
  })
  Swal.close();
  const contentType = resultado.headers.get('content-type');
  if (contentType == 'application/json') {
    alertasAjax(resultado.json());
  } else if (contentType == 'application/pdf') {
    let pdf = await resultado.blob();
    const url = window.URL.createObjectURL(pdf);
    window.open(url, '_blank');
  } else {
    console.error('Content type no identificado')
  }
}
// Función para generar reporte de compras
async function generarReporteCompras() {
  const form = document.getElementById('formReporteCompras');
  const formData = new FormData(form);
  const datos = {};
  formData.forEach((value, key) => {
    datos[key] = value;
  });
  datos.reporte = 'reporte_compras';

  Swal.fire({
    title: 'Generando reporte',
    text: 'Por favor espere...',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });

  let resultado = await fetch(rutaAbsoluta + 'reportes', {
    method: 'POST',
    headers: encabezadosPeticiones,
    body: JSON.stringify(datos)
  })
  Swal.close();
  const contentType = resultado.headers.get('content-type');

  if (contentType == 'application/json' || contentType == 'text/html; charset=UTF-8') {
    let JSON = await resultado.json()
    alertasAjax(JSON);
    console.log('resultado: ', JSON)
  } else if (contentType == 'application/pdf') {
    let pdf = await resultado.blob();
    const url = window.URL.createObjectURL(pdf);
    window.open(url, '_blank');
  } else {
    console.error('Content type no identificado')
  }
}
// Función principal para generar cierre de caja
async function generarCierreCaja() {
  const form = $('#formCierreCaja')[0];
  console.log(form)
  const formData = new FormData(form);
  const datos = {};
  formData.forEach((value, key) => {
    datos[key] = value;
  });
  datos.reporte = 'reporte_cierre_caja';

  Swal.fire({
    title: 'Generando reporte',
    text: 'Por favor espere...',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });

  let resultado = await fetch(rutaAbsoluta + 'reportes', {
    method: 'POST',
    headers: encabezadosPeticiones,
    body: JSON.stringify(datos)
  })
  Swal.close();
  const contentType = resultado.headers.get('content-type');
  console.log(contentType)
  if (contentType == 'application/json') {
    alertasAjax(resultado.json());
  } else if (contentType == 'application/pdf') {
    let pdf = await resultado.blob();
    const url = window.URL.createObjectURL(pdf);
    window.open(url, '_blank');
  } else {
    console.error('Content type no identificado')
  }
}
// Función para generar reporte de productos
async function generarReporteProductos(event) {
  event.preventDefault();
  const form = document.getElementById('formReporteProductos');
  const formData = new FormData(form);
  const datos = {};
  formData.forEach((value, key) => {
    datos[key] = value;
  });
  datos.reporte = 'reporte_productos';

  Swal.fire({
    title: 'Generando reporte',
    text: 'Por favor espere...',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });

  let resultado = await fetch(rutaAbsoluta + 'reportes', {
    method: 'POST',
    headers: encabezadosPeticiones,
    body: JSON.stringify(datos)
  })
  Swal.close();
  const contentType = resultado.headers.get('content-type');
  if (contentType == 'application/json') {
    alertasAjax(resultado.json());
  } else if (contentType == 'application/pdf') {
    let pdf = await resultado.blob();
    const url = window.URL.createObjectURL(pdf);
    window.open(url, '_blank');
  } else {
    console.error('Content type no identificado')
  }
}
// Función para generar reporte de servicios
async function generarReporteServicios(event) {
  event.preventDefault();
  const form = document.getElementById('formReporteServicios');
  const formData = new FormData(form);
  const datos = {};
  formData.forEach((value, key) => {
    datos[key] = value;
  });
  datos.reporte = 'reporte_servicios';

  Swal.fire({
    title: 'Generando reporte',
    text: 'Por favor espere...',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });

  let resultado = await fetch(rutaAbsoluta + 'reportes', {
    method: 'POST',
    headers: encabezadosPeticiones,
    body: JSON.stringify(datos)
  })
  Swal.close();
  const contentType = resultado.headers.get('content-type');
  if (contentType == 'application/json') {
    alertasAjax(resultado.json());
  } else if (contentType == 'application/pdf') {
    let pdf = await resultado.blob();
    const url = window.URL.createObjectURL(pdf);
    window.open(url, '_blank');
  } else {
    console.error('Content type no identificado')
  }
}
// Función para cargar items específicos en ventas
async function cargarItemsVentas() {
  const tipo = document.getElementById('tipo_producto_ventas').value;
  const divItem = document.getElementById('div_item_especifico_ventas');
  const selectItem = document.getElementById('id_item_ventas');

  if (tipo === 'especifico') {
    divItem.style.display = 'block';

    // Aquí puedes hacer una petición AJAX para cargar los items

    let resultado = await fetch(rutaAbsoluta + 'reportes', {
      method: 'POST',
      headers: encabezadosPeticiones,
      body: JSON.stringify({ accion: 'listar_items' })
    })
      .then(response => response.json())
      .then(data => {
        selectItem.innerHTML = '<option value="">Seleccione un item</option>';
        data.forEach(item => {
          selectItem.innerHTML += `<option value="${item.id_producto_servicio}">${item.nombre_producto_servicio}</option>`;
        });
      })
      .catch(error => console.error('Error cargando items:', error));
  } else {
    divItem.style.display = 'none';
  }
}
// Función para cargar materias primas en compras
async function cargarMateriasPrimas() {
  const tipo = document.getElementById('tipo_materia').value;
  const divMateria = document.getElementById('div_materia_especifica');
  const selectMateria = document.getElementById('id_materia');

  if (tipo === 'especifico') {
    divMateria.style.display = 'block';

    let resultado = await fetch(rutaAbsoluta + 'reportes', {
      method: 'POST',
      headers: encabezadosPeticiones,
      body: JSON.stringify({ accion: 'listar_materias_primas' })
    })
      .then(response => response.json())
      .then(data => {
        selectMateria.innerHTML = '<option value="">Seleccione una materia prima</option>';
        data.forEach(materia => {
          selectMateria.innerHTML += `<option value="${materia.id_materia_prima}">${materia.nombre_materia_prima}</option>`;
        });
      })
      .catch(error => console.error('Error cargando materias primas:', error));
  } else {
    divMateria.style.display = 'none';
  }
}
// Función para mostrar campos de período
function mostrarCamposPeriodo(tipo) {
  const periodo = document.getElementById(`periodo_${tipo}`).value;
  const divPersonalizado = document.getElementById(`div_periodo_personalizado_${tipo}`);
  const divEspecifico = document.getElementById(`div_periodo_especifico_${tipo}`);

  divPersonalizado.style.display = periodo === 'personalizado' ? 'flex' : 'none';
  divEspecifico.style.display = (periodo === 'mes' || periodo === 'anio') ? 'flex' : 'none';
}
//#endregion [ FUNCIONES PROPIAS DEL MODULO ] FIN

//#region [ DELEGACIÓN DE EVENTOS ] COMIENZO

// Evento de carga de la pagina
document.addEventListener('DOMContentLoaded', function () {
  // Aca vas a cargar solo aquellas funciones que busquen los datos iniciales, como las materias primas y demas
});

// Asignar evento al formulario de ventas
$(document).off('submit', '#formReporteVentas')
$(document).on('submit', '#formReporteVentas', function (e) {
  e.preventDefault();
  generarReporteVentas();
})

// Asignar evento al formulario de ventas
$(document).off('submit', '#formReporteCompras')
$(document).on('submit', '#formReporteCompras', function (e) {
  e.preventDefault();
  generarReporteCompras();
})

// Asignar evento al formulario de ventas
$(document).off('submit', '#formCierreCaja')
$(document).on('submit', '#formCierreCaja', function (e) {
  e.preventDefault();
  generarCierreCaja();
})

//#endregion [ DELEGACIÓN DE EVENTOS ] FIN



