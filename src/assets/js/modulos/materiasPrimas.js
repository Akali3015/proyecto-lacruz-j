//#region [ IMPORTACIONES ] COMIENZO
import {
  enviarFormulario, eliminarRegistro, obtenerDatosRegistro,
  listarDataTable, cargarInputsActualizarQNR, extraerDatosAjax,
  pedirDatosAjax, instanciasDatatable, validarEnTiempoReal
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
import { driverAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"

//#endregion [ IMPORTACIONES ] FIN

//#region [FUNCIONES PROPIAS DEL MODULO] COMIENZO
async function renderizarPresentaciones() {
  let presentacionesBD = await pedirDatosAjax({
    modulo: "presentaciones",
    datosPe: { accion: "listar" },
  });
  let html = "";
  for (let i = 0; i < presentacionesBD.length; i++) {
    let {
      id_presentacion,
      nombre_presentacion,
      cantidad_pmp,
      nombre_unidad_medida
    } = presentacionesBD[i]
    html += `
      <div class="filaPresentacion col-lg-4 mb-3">
        <div class="form-check card-presentacion p-3 border rounded">
          <input 
            name="presentaciones-${i}"
            class="checkbox-presentacion d-none" 
            type="checkbox" 
            value="${id_presentacion}"
          >
          <div class="form-check-label w-100">
            <div class="d-flex justify-content-between align-items-center">
              <strong>${nombre_presentacion}</strong>
              <span class="badge bg-info">${cantidad_pmp} ${nombre_unidad_medida}</span>
            </div>
          </div>
        </div>
      </div>
    `;
  }
  $(".contenedor-presentaciones").empty().append(html);
}
function habilitarDeshabilitarPresentacion(cambio = null) {
  let card = $(this);
  let checkBox = card.find('.checkbox-presentacion')
  if (cambio) {
    if (cambio == 'habilitar') {
      checkBox.prop('checked', false);
      card.removeClass('bg-light border-primary')
    } else {
      checkBox.prop('checked', true);
      card.addClass('bg-light border-primary')
    }
  }
  card.toggleClass("bg-light border-primary")
  checkBox.prop("checked", function (i, val) {
    return !val;
  });
}
//#endregion [FUNCIONES PROPIAS DEL MODULO] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {
  listarDataTable({
    encabezados: {
      "id_materia_prima": "ID",
      "nombre_unidad_medida": "UNIDAD DE MEDIDA",
      "nombre_materia_prima": "NOMBRE",
      "stock_materia_prima": "STOCK",
      "precio_materia_prima": "PRECIO",
    },
    informacionPe: {
      'modulo': 'materiasPrimas',
      'datosPe': {
        'accion': 'listar'
      }
    },
    campoIdBtn: 'id_materia_prima',
    botones: 'CRUD',
    infoTratoEspecial: {
      precio_materia_prima: (info) => { return info.valor + '$'; },
    },
  });
  extraerDatosAjax({
    'modulosPeticion': ['unidadesMedidas'],
    'accionesPeticion': [{ 'accion': 'listar' }],
    'tipoElemento': ['select'],
    'elementosDestino': [$('.selectUnidadMedida')],
    'datosInsertar': [
      {
        'value': 'id_unidad_medida',
        'texto': 'nombre_unidad_medida',
        'textoDefault': 'Seleccione una unidad de medida'
      }
    ]
  });
  renderizarPresentaciones();
  driverAyuda('materiasPrimas', {
    pasos: [
      {
        element: 'button[data-bs-target=".modalRegistrar"]',
        popover: {
          title: 'Registrar Materia Prima',
          description: 'Haz clic aquí para agregar una nueva materia prima al sistema. Las materias primas se utilizan en la fabricación de productos.',
          side: 'bottom',
          align: 'start'
        }
      },
      {
        element: '.tabla-ajax',
        popover: {
          title: 'Lista de Materias Primas',
          description: 'Aquí puedes ver todas las materias primas registradas, su stock actual y precio.',
          side: 'top'
        }
      },
      {
        element: '.botonEditar',
        popover: {
          title: 'Editar Materia Prima',
          description: 'Modifica los datos de cualquier materia prima haciendo clic en este botón.',
          side: 'left'
        }
      },
      {
        element: '.botonEliminar',
        popover: {
          title: 'Eliminar Materia Prima',
          description: 'Elimina materias primas que ya no sean necesarias. Ten cuidado porque puede afectar productos asociados.',
          side: 'left'
        }
      },
      {
        popover: {
          title: '¡Ayuda completada!',
          description: 'Ya conoces la gestión de materias primas. Puedes registrar, editar o eliminar materias primas para controlar tu inventario.',
          side: 'top'
        }
      }
    ]
  });
})

$(document).off("click", ".card-presentacion");
$(document).on("click", ".card-presentacion", function (e) {
  habilitarDeshabilitarPresentacion.call(this);
});

$(document).off("click", ".btn-seleccionar-todas");
$(document).on("click", ".btn-seleccionar-todas", function (e) {
  $(this).closest(".modal").find(".card-presentacion").each((i, card) => {
    habilitarDeshabilitarPresentacion.call(card, 'habilitar');
  })
});

$(document).off("click", ".btn-deseleccionar-todas");
$(document).on("click", ".btn-deseleccionar-todas", function (e) {
  $(this).closest(".modal").find(".card-presentacion").each((i, card) => {
    habilitarDeshabilitarPresentacion.call(card, 'deshabilitar');
  })
});

$(document).off('submit', '.formularioAjax');
$(document).on('submit', '.formularioAjax', async function (e) {
  e.preventDefault();
  let resultado = await enviarFormulario({
    'formulario': this,
    'modulo': 'materiasPrimas',
    'convertirJSON': true,
  })
  if (resultado['icono'] && resultado['icono'] == 'success') {
    $(this).closest('.modal').find('.card-presentacion').each((i, card) => {
      habilitarDeshabilitarPresentacion.call(card, 'deshabilitar');
    })
  }

});

$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
  e.preventDefault();
  eliminarRegistro({
    boton: this,
    campoId: 'id_materia_prima',
    modulo: 'materiasPrimas',
  }).then(() => {
    actualizarDataTableMateriasPrimas();
  }).catch(error => {
    console.error('Error al eliminar:', error);
  });
});

$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
  e.preventDefault();
  const modalTarget = $(this).attr('data-bs-target');
  const modal = $(modalTarget);
  let infoCompleta = await obtenerDatosRegistro({
    boton: this,
    campoId: 'id_materia_prima',
    modulo: 'materiasPrimas',
  });
  console.log(infoCompleta);
  cargarInputsActualizarQNR.call(modal.find('form'));

  $(modal).find('.card-presentacion').each((i, card) => {
    let idPre = $(card).find('.checkbox-presentacion').val();
    let idBuscado = infoCompleta.presentaciones.find(p => {
      return p.id_presentacion == idPre
    })
    if (idBuscado) {
      habilitarDeshabilitarPresentacion.call(card, 'habilitar');
    } else {
      habilitarDeshabilitarPresentacion.call(card, 'deshabilitar');
    }
  })

});

//Evento para validar en tiempo real
$(document).off('input', '.validar input, .validar select')
$(document).on('input', '.validar input, .validar select', function () {
  validarEnTiempoReal(this, 'materiasPrimas');
})
//#endregion [DELEGACIÓN DE EVENTOS] FIN
