//#region [ IMPORTACIONES ] COMIENZO
import {
  enviarFormulario, obtenerDatosRegistro,
  listarDataTable, cargarInputsActualizarQNR,
  pedirDatosAjax, validarEnTiempoReal,
  obtenerSiguienteIndice
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
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
function actualizarUnidadMedida($select) {
  const $fila = $select.closest('.fila-producto');
  const idProducto = $select.val();

  if (!idProducto) {
    $fila.find('.unidad-medida-texto').val('');
    $fila.find('.id-unidad-medida').val('');
    return;
  }

  const unidad = obtenerUnidadProducto(idProducto);

  const $inputUnidad = $fila.find('.unidad-medida-texto');
  const $inputIdUnidad = $fila.find('.id-unidad-medida');

  if ($inputUnidad.length) {
    $inputUnidad.val(unidad.nombre);
  }

  if ($inputIdUnidad.length) {
    $inputIdUnidad.val(unidad.id);
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
function agregarFilaProducto(contenedorId, esActualizar = false) {
  const $contenedor = $(`#${contenedorId}`);
  const template = $('#templateProductoFila').html();

  const siguienteIndice = obtenerSiguienteIndice(
    $contenedor,
    'input',
    'productos'
  );
  let nuevaFilaHtml = template.replace(/\[INDICE\]/g, siguienteIndice);
  const $nuevaFila = $(nuevaFilaHtml);
  $nuevaFila.attr('data-indice', siguienteIndice);
  $contenedor.append($nuevaFila);

  const $selectProducto = $nuevaFila.find('.selectProductos');
  actualizarSelectFila($selectProducto);

  if (esActualizar) {
    $nuevaFila.addClass('formularioActualizar');
  }
  $nuevaFila.hide().fadeIn(300);

  return $nuevaFila;
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
      return `
      <ul class="list-inline me-auto mb-0">
        ${botones}
      </ul>`;
    },
  });
  await cargarProductos();
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
  agregarFilaProducto('contenedorProductosActualizar', true);
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
});

$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
  e.preventDefault();
  $('#contenedorProductosActualizar').empty();
  let todosLosDatos = await obtenerDatosRegistro({
    boton: this,
    campoId: 'id_produccion',
    modulo: 'producciones',
  });
  agregarFilaProducto('contenedorProductosActualizar', true);
  cargarInputsActualizarQNR.call($($(this).attr('data-bs-target')).find('form'));
  console.log('todos los datos:', todosLosDatos);

  


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

//#endregion [ DELEGACIÓN DE EVENTOS ] FIN