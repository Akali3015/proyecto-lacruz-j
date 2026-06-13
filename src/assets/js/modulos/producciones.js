//#region [ IMPORTACIONES ] COMIENZO
import {
  enviarFormulario, obtenerDatosRegistro,
  listarDataTable, cargarInputsActualizarQNR,
  pedirDatosAjax, validarEnTiempoReal,
  obtenerSiguienteIndice, cambiarFormatos
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
import { driverAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"

//#endregion [ IMPORTACIONES ] FIN

//#region [ VARIABLES GLOBALES ] COMIENZO

let productosData = [];

//#endregion [ VARIABLES GLOBALES ] FIN

//#region [ FUNCIONES PROPIAS DEL MODULO ] COMIENZO

async function cargarProductos() {
  try {
    productosData = await pedirDatosAjax({
      modulo: 'productos',
      datosPe: { accion: 'listar' }
    });
    console.log('Productos cargados:', productosData);
  } catch (error) {
    console.error('Error cargando productos:', error);
  }
}

async function cargarProductosEnSelect($select, valorSeleccionado = null) {
  if (!productosData || productosData.length === 0) {
    try {
      productosData = await pedirDatosAjax({
        modulo: 'productos',
        datosPe: { accion: 'listar' }
      });
    } catch (error) {
      console.error('Error cargando productos:', error);
    }
  }

  const valorActual = valorSeleccionado !== null ? valorSeleccionado : $select.val();

  $select.empty();
  $select.append('<option value="">Seleccione Producto</option>');

  if (productosData && productosData.length > 0) {
    productosData.forEach(producto => {
      const option = $('<option>', {
        value: producto.id_producto,
        text: producto.nombre_producto
      });
      $select.append(option);
    });
  }

  if (valorActual) {
    $select.val(valorActual);
  }
}

function obtenerUnidadProducto(idProducto) {
  if (!productosData || productosData.length === 0) return { nombre: '', id: '' };
  const producto = productosData.find(p => p.id_producto == idProducto);
  if (producto) {
    return {
      nombre: producto.nombre_unidad_medida || '',
      id: producto.id_unidad_medida || ''
    };
  }
  return { nombre: '', id: '' };
}

async function actualizarUnidadMedida($select) {
  const $fila = $select.closest('.fila-producto');
  const idProducto = $select.val();

  if (!idProducto) {
    $fila.find('.unidad-medida-texto').val('');
    $fila.find('.id-unidad-medida').val('');
    return;
  }

  try {
    const productoInfo = productosData.find(p => p.id_producto == idProducto);
    if (productoInfo && productoInfo.id_unidad_medida) {
      const unidadMedida = await pedirDatosAjax({
        modulo: 'unidadesMedidas',
        datosPe: {
          accion: 'seleccionarUno',
          id_unidad_medida: productoInfo.id_unidad_medida
        }
      });

      if (unidadMedida && !unidadMedida.icono) {
        $fila.find('.unidad-medida-texto').val(unidadMedida.nombre_unidad_medida);
      } else {
        $fila.find('.unidad-medida-texto').val(productoInfo.nombre_unidad_medida || '');
      }
    }
  } catch (error) {
    console.error('Error obteniendo unidad de medida:', error);
    const unidad = obtenerUnidadProducto(idProducto);
    $fila.find('.unidad-medida-texto').val(unidad.nombre);
  }
}

function actualizarSelectFila($select) {
  const $fila = $select.closest('.fila-producto');
  const valorActual = $select.val();

  $select.empty();
  $select.append('<option value="">Seleccione Producto</option>');

  if (productosData && productosData.length > 0) {
    productosData.forEach(producto => {
      const option = $('<option>', {
        value: producto.id_producto,
        text: producto.nombre_producto
      });
      $select.append(option);
    });
  }

  if (valorActual) {
    $select.val(valorActual);
    setTimeout(() => {
      actualizarUnidadMedida($select);
    }, 100);
  }
}

async function agregarFilaProducto(contenedorId, esActualizar = false, detallesActualizar = null) {
  const $contenedor = $(`#${contenedorId}`);
  const template = $('#templateProductoFila').html();

  if (detallesActualizar && detallesActualizar.length > 0) {
    for (let i = 0; i < detallesActualizar.length; i++) {
      let detalleBD = detallesActualizar[i];

      let nuevaFilaHtml = template.replace(/\[INDICE\]/g, i);
      const $nuevaFila = $(nuevaFilaHtml);
      $nuevaFila.attr('data-indice', i);
      $contenedor.append($nuevaFila);

      let filaDetalleHTML = $contenedor.find('.fila-producto').last();

      // Cargar productos en el select
      await cargarProductosEnSelect(filaDetalleHTML.find('.selectProductos'), detalleBD.id_producto);

      // Asignar valores
      filaDetalleHTML.find('.cantidad-producto').val(detalleBD.cantidad_producida);

      // Obtener unidad de medida
      await actualizarUnidadMedida(filaDetalleHTML.find('.selectProductos'));

      $nuevaFila.hide().fadeIn(300);
    }
  } else {
    const siguienteIndice = obtenerSiguienteIndice(
      $contenedor,
      'input',
      'productos'
    );
    let nuevaFilaHtml = template.replace(/\[INDICE\]/g, siguienteIndice);
    const $nuevaFila = $(nuevaFilaHtml);
    $nuevaFila.attr('data-indice', siguienteIndice);
    $contenedor.append($nuevaFila);
    await cargarProductosEnSelect($nuevaFila.find('.selectProductos'));
    $nuevaFila.hide().fadeIn(300);
    return $nuevaFila;
  }
}

function eliminarFilaProducto($btnEliminar) {
  const $fila = $btnEliminar.closest('.fila-producto');
  const $contenedor = $fila.closest('[id^="contenedorProductos"]');

  const $todasLasFilas = $contenedor.find('.fila-producto');
  const totalFilas = $todasLasFilas.length;

  if (totalFilas <= 1) {
    Swal.fire({
      icon: 'warning',
      title: 'No se puede eliminar',
      text: 'Debe haber al menos un producto en la producción',
      timer: 2000,
      showConfirmButton: false
    });
    return;
  }

  Swal.fire({
    title: '¿Eliminar producto?',
    text: '¿Estás seguro de eliminar este producto de la producción?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      $fila.fadeOut(300, function () {
        $(this).remove();
        Swal.fire({
          icon: 'success',
          title: 'Eliminado',
          text: 'El producto ha sido eliminado',
          timer: 1500,
          showConfirmButton: false
        });
      });
    }
  });
}

async function consultarProduccion(boton) {
  const idProduccion = $(boton).attr('value');
  const modal = $('.modalConsultar');
  const tbody = modal.find('#tbodyProductosConsulta');

  modal.find('.textoIdProduccion').text('Cargando...');
  modal.find('.textoFechaProduccion').text('Cargando...');
  tbody.html('<tr><td colspan="3" class="text-center text-muted">Cargando...</td></tr>');

  try {
    const produccion = await pedirDatosAjax({
      modulo: 'producciones',
      datosPe: {
        accion: 'seleccionarUno',
        id_produccion: idProduccion
      }
    });

    if (!produccion || produccion.icono === 'error') {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: produccion?.texto || 'No se pudo cargar la producción'
      });
      return;
    }

    modal.find('.textoIdProduccion').text(produccion.id_produccion);
    modal.find('.textoFechaProduccion').text(cambiarFormatos(produccion.fecha_produccion, 'fecha_hora'));

    tbody.empty();

    if (produccion.detalles && produccion.detalles.length > 0) {
      const promesasProductos = produccion.detalles.map(async (detalle) => {
        const producto = await pedirDatosAjax({
          modulo: 'productos',
          datosPe: {
            accion: 'seleccionarUno',
            id_producto: detalle.id_producto
          }
        });

        let unidadMedida = { nombre_unidad_medida: 'N/A' };
        if (producto && producto.id_unidad_medida && !producto.icono) {
          unidadMedida = await pedirDatosAjax({
            modulo: 'unidadesMedidas',
            datosPe: {
              accion: 'seleccionarUno',
              id_unidad_medida: producto.id_unidad_medida
            }
          });
        }

        return {
          nombre_producto: producto && !producto.icono ? producto.nombre_producto : 'N/A',
          nombre_unidad_medida: unidadMedida && !unidadMedida.icono ? unidadMedida.nombre_unidad_medida : 'N/A',
          cantidad_producida: detalle.cantidad_producida
        };
      });

      const productosInfo = await Promise.all(promesasProductos);

      productosInfo.forEach(producto => {
        tbody.append(`
          <tr>
            <td>${producto.nombre_producto}</td>
            <td><strong>${producto.cantidad_producida}</strong></td>
            <td>${producto.nombre_unidad_medida}</td>
          </tr>
        `);
      });
    } else {
      tbody.html('<tr><td colspan="3" class="text-center text-muted">No hay productos registrados</td></tr>');
    }

    modal.modal('show');

  } catch (error) {
    console.error('Error al consultar producción:', error);
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'No se pudo cargar la información de la producción'
    });
  }
}

//#endregion [ FUNCIONES PROPIAS DEL MODULO ] FIN

//#region [ DELEGACIÓN DE EVENTOS ] COMIENZO

$(document).on('DOMContentLoaded', async function (e) {
  await listarDataTable({
    'encabezados': {
      "id_produccion": "ID",
      "fecha_produccion": "FECHA",
    },
    informacionPe: {
      'modulo': 'producciones',
      'datosPe': {
        'accion': 'listar'
      }
    },
    campoIdBtn: 'id_produccion',
    botones: (datos) => {
      let botones = '';
      let permisos = datos.permisos.producciones;
      let datosFila = datos.fila;

      if (permisos.includes('actualizar')) {
        botones += `
        <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar datos del registro">
            <a href="#" value="${datosFila.id_produccion}" class="botonEditar avtar avtar-xs btn-link-success btn-pc-default" data-bs-toggle="modal" data-bs-target=".modalActualizar">
            <i class="fi fi-rs-pen-circle fs-3 iconoCentrado"></i>
            </a>
        </li>`;
      }

      botones += `
      <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" data-bs-placement="top" title="Consultar producción">
          <a href="#" value="${datosFila.id_produccion}" class="botonConsultar avtar avtar-xs btn-link-info btn-pc-default">
          <i class="fi fi-rs-info fs-3 iconoCentrado"></i>
          </a>
      </li>`;

      return `
      <ul class="list-inline me-auto mb-0">
        ${botones}
      </ul>`;
    },
    infoTratoEspecial: {
      fecha_produccion: (info) => {
        return cambiarFormatos(info.fila.fecha_produccion, 'fecha_hora')
      }
    }
  });
  await cargarProductos();
  driverAyuda('producciones', {
    pasos: [
      {
        element: 'button[data-bs-target=".modalRegistrar"]',
        popover: {
          title: 'Registrar Producción',
          description: 'Haz clic aquí para registrar una nueva producción. Se descontarán las materias primas y se aumentará el stock de productos.',
          side: 'bottom',
          align: 'start'
        }
      },
      {
        element: '.tabla-ajax',
        popover: {
          title: 'Lista de Producciones',
          description: 'Aquí puedes ver todas las producciones registradas, su fecha y opciones para consultar o editar.',
          side: 'top'
        }
      },
      {
        element: '.botonEditar',
        popover: {
          title: 'Editar Producción',
          description: 'Modifica los productos o cantidades de una producción existente. El stock se ajustará automáticamente.',
          side: 'left'
        }
      },
      {
        element: '.botonConsultar',
        popover: {
          title: 'Consultar Producción',
          description: 'Ver el detalle de una producción: productos producidos y cantidades.',
          side: 'left'
        }
      },
      {
        popover: {
          title: '¡Ayuda completada!',
          description: 'Ya conoces la gestión de producciones. Recuerda que cada producción afecta el stock de productos y materias primas.',
          side: 'top'
        }
      }
    ]
  });
});

$(document).off('change', '.selectProductos');
$(document).on('change', '.selectProductos', function () {
  actualizarUnidadMedida($(this));
  $(this).removeClass('is-invalid');
  validarEnTiempoReal(this, 'producciones');
});

$(document).off('click', '#btnAgregarProducto');
$(document).on('click', '#btnAgregarProducto', function (e) {
  e.preventDefault();
  agregarFilaProducto('contenedorProductos');
});

$(document).off('click', '#btnAgregarProductoActualizar');
$(document).on('click', '#btnAgregarProductoActualizar', function (e) {
  e.preventDefault();
  agregarFilaProducto('contenedorProductosActualizar');
});

$(document).off('click', '.btn-eliminar-fila');
$(document).on('click', '.btn-eliminar-fila', function (e) {
  e.preventDefault();
  e.stopPropagation();
  eliminarFilaProducto($(this));
});

$(document).off('show.bs.modal', '.modalRegistrar');
$(document).on('show.bs.modal', '.modalRegistrar', function () {
  const $contenedor = $('#contenedorProductos');
  $contenedor.empty();
  agregarFilaProducto('contenedorProductos');
});

$(document).off('hidden.bs.modal', '.modalRegistrar');
$(document).on('hidden.bs.modal', '.modalRegistrar', function () {
  $('#contenedorProductos').empty();
  $(this).find('form')[0].reset();
});

$(document).off('submit', '.formularioAjax');
$(document).on('submit', '.formularioAjax', async function (e) {
  e.preventDefault();

  const $form = $(this);
  const $contenedor = $form.find('[id^="contenedorProductos"]');
  const numProductos = $contenedor.find('.fila-producto').length;

  if (numProductos === 0) {
    Swal.fire({
      icon: 'warning',
      title: 'Sin productos',
      text: 'Debe agregar al menos un producto a la producción',
      confirmButtonText: 'Entendido'
    });
    return;
  }

  let hayError = false;
  let mensajeError = '';

  $contenedor.find('.fila-producto').each(function (index) {
    const $fila = $(this);
    const $select = $fila.find('.selectProductos');
    const $cantidad = $fila.find('.cantidad-producto');

    if (!$select.val()) {
      hayError = true;
      mensajeError = `Debe seleccionar un producto en la fila ${index + 1}`;
      $select.addClass('is-invalid');
      return false;
    }

    if ($cantidad.val() <= 0 || $cantidad.val() === '') {
      hayError = true;
      mensajeError = `La cantidad debe ser mayor a 0 en la fila ${index + 1}`;
      $cantidad.addClass('is-invalid');
      return false;
    }
  });

  if (hayError) {
    Swal.fire({
      icon: 'warning',
      title: 'Error de validación',
      text: mensajeError,
      confirmButtonText: 'Entendido'
    });
    return;
  }

  const respuesta = await enviarFormulario({
    'formulario': this,
    'modulo': 'producciones',
    'convertirJSON': true,
    'camposFuera': []
  });

  console.log('Respuesta del servidor:', respuesta);
});

$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
  e.preventDefault();

  const idProduccion = $(this).attr('value');
  const $modal = $('.modalActualizar');
  const $form = $modal.find('form');
  const $contenedor = $('#contenedorProductosActualizar');

  // Limpiar todo
  $contenedor.empty();
  $form.find('input[name="id_produccion"]').val('');

  Swal.fire({
    title: 'Cargando...',
    text: 'Obteniendo datos de la producción',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  try {
    const produccion = await pedirDatosAjax({
      modulo: 'producciones',
      datosPe: {
        accion: 'seleccionarUno',
        id_produccion: idProduccion
      }
    });

    Swal.close();

    if (!produccion || produccion.icono === 'error') {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: produccion?.texto || 'No se pudo cargar la producción'
      });
      return;
    }

    console.log('Producción cargada:', produccion);

    $form.find('input[name="id_produccion"]').val(produccion.id_produccion);

    if (produccion.detalles && produccion.detalles.length > 0) {
      await agregarFilaProducto('contenedorProductosActualizar', true, produccion.detalles);
    } else {
      await agregarFilaProducto('contenedorProductosActualizar');
    }

    $contenedor.find('input, select').addClass('formularioActualizar');

  } catch (error) {
    Swal.close();
    console.error('Error al cargar producción:', error);
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Ocurrió un error al cargar los datos de la producción'
    });
  }
});

$(document).off('click', '.botonConsultar');
$(document).on('click', '.botonConsultar', function (e) {
  e.preventDefault();
  consultarProduccion(this);
});

$(document).off('input', '.cantidad-producto');
$(document).on('input', '.cantidad-producto', function () {
  if ($(this).val() <= 0 || $(this).val() === '') {
    $(this).addClass('is-invalid');
  } else {
    $(this).removeClass('is-invalid');
  }
  validarEnTiempoReal(this, 'producciones');
});

$(document).off('input', '.validar input, .validar select');
$(document).on('input', '.validar input, .validar select', function () {
  validarEnTiempoReal(this, 'producciones');
});

//#endregion [ DELEGACIÓN DE EVENTOS ] FIN