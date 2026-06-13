//#region [ IMPORTACIONES ] COMIENZO
import {
  enviarFormulario, eliminarRegistro, obtenerDatosRegistro,
  listarDataTable, cargarInputsActualizarQNR, validarEnTiempoReal
} from "/proyecto-lacruz-j/src/assets/js/modulos/global.js";
import { driverAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"

//#endregion [ IMPORTACIONES ] FIN

//#region [ FUNCIONES PROPIAS DEL MODULO ] COMIENZO
async function cargarDashboard() {
  // Obtenemos todos los datos que la tabla mantiene en memoria (independiente de si se filtró arriba)
  let datosTabla = $('.tabla-ajax').DataTable().rows().data().toArray();
  let total = datosTabla.length;
  let fabricacion = datosTabla.filter(c => c.necesitan_materias_primas == 1).length;
  let reventa = datosTabla.filter(c => c.necesitan_materias_primas == 0).length;
  let containerHTML = $('#metricasDashboard');

  console.log(containerHTML.length)
  if (containerHTML.length == 0) {
    let dashboardHTML = `
      <div class="row mb-4" id="metricasDashboard">
        <div class="col-md-4">
          <div class="card shadow-sm border-0 border-start border-4 border-primary h-100">
            <div class="card-body d-flex align-items-center">
              <div class="me-3">
                <div class="p-3 bg-primary bg-opacity-10 rounded-circle"><i class="fi fi-rr-tags text-primary fs-3"></i></div>
              </div>
              <div>
              <h6 class="text-muted mb-1 text-uppercase" style="font-size:0.8rem; font-weight:700;">
                Categorías Totales
              </h6>
              <h3 class="totalCategorias mb-0 fw-bold">${total}</h3>
            </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow-sm border-0 border-start border-4 border-success h-100">
            <div class="card-body d-flex align-items-center">
              <div class="me-3">
                <div class="p-3 bg-success bg-opacity-10 rounded-circle">
                  <i class="fi fi-rr-hammer text-success fs-3"></i>
                </div>
              </div>
              <div>
                <h6 class="text-muted mb-1 text-uppercase" style="font-size:0.8rem; font-weight:700;">
                  De Fabricación
                </h6>
                <h3 class="totalFabricados mb-0 fw-bold">${fabricacion}</h3>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow-sm border-0 border-start border-4 border-info h-100">
            <div class="card-body d-flex align-items-center">
              <div class="me-3">
                <div class="p-3 bg-info bg-opacity-10 rounded-circle">
                  <i class="fi fi-rr-box text-info fs-3"></i>
                </div>
              </div>
              <div>
                <h6 class="text-muted mb-1 text-uppercase" style="font-size:0.8rem; font-weight:700;">
                  De Reventa
                </h6>
                <h3 class="totalReventa mb-0 fw-bold">${reventa}</h3>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
    containerHTML.remove();
    let card = $('.tabla-ajax').closest('.card')
    $(dashboardHTML).insertBefore(card);
  } else {
    console.log(containerHTML.find('.totalCategorias'))
    containerHTML.find('.totalCategorias').text(total);
    containerHTML.find('.totalFabricados').text(fabricacion);
    containerHTML.find('.totalReventa').text(reventa);
  }
}
//#endregion [ FUNCIONES PROPIAS DEL MODULO ] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on("DOMContentLoaded", async function () {
  await listarDataTable({
    encabezados: {
      "id_categoria_producto": "ID",
      "nombre_categoria_producto": "NOMBRE DE CATEGORÍA",
      "necesitan_materias_primas": "TIPO DE CATEGORÍA",
      "cantidad_productos": "PRODUCTOS ASIGNADOS",
    },
    informacionPe: {
      'modulo': 'categoriasProductos',
      'datosPe': {
        'accion': 'listar'
      }
    },
    campoIdBtn: 'id_categoria_producto',
    botones: 'CRUD',
    infoTratoEspecial: {
      necesitan_materias_primas: (info) => {
        if (info.valor == 1) {
          return '<span class="badge bg-success"><i class="fi fi-rr-hammer me-1"></i> Fabricación</span>';
        } else {
          return '<span class="badge bg-info text-dark"><i class="fi fi-rr-box me-1"></i> Reventa</span>';
        }
      },
      cantidad_productos: (info) => {
        let badge = info.valor > 0 ? 'bg-primary' : 'bg-secondary';
        return `<span class="badge rounded-pill ${badge}">${info.valor} Productos</span>`;
      }
    }
  });
  cargarDashboard();
  driverAyuda('categoriasProductos', {
    pasos: [
      {
        element: 'button[data-bs-target=".modalRegistrar"]',
        popover: {
          title: 'Registrar Categoría',
          description: 'Haz clic aquí para agregar una nueva categoría de producto. Las categorías ayudan a organizar tu inventario.',
          side: 'bottom',
          align: 'start'
        }
      },
      {
        element: '.tabla-ajax',
        popover: {
          title: 'Lista de Categorías',
          description: 'Aquí puedes ver todas las categorías registradas, su tipo (Fabricación o Reventa) y cuántos productos están asociados a cada una.',
          side: 'top'
        }
      },
      {
        element: '.botonEditar',
        popover: {
          title: 'Editar Categoría',
          description: 'Modifica el nombre o el tipo de cualquier categoría haciendo clic en este botón.',
          side: 'left'
        }
      },
      {
        element: '.botonEliminar',
        popover: {
          title: 'Eliminar Categoría',
          description: 'Elimina categorías que ya no sean necesarias. Ten cuidado: esto puede afectar los productos asociados.',
          side: 'left'
        }
      },
      {
        popover: {
          title: '¡Ayuda completada!',
          description: 'Ya conoces la gestión de categorías de productos. Puedes organizar tu inventario entre productos de fabricación y reventa.',
          side: 'top'
        }
      }
    ]
  });
});

$(document).off("click", ".botonEditar");
$(document).on("click", ".botonEditar", async function (e) {
  e.preventDefault();
  const modalTarget = $(this).attr("data-bs-target");
  const modal = $(modalTarget);
  await obtenerDatosRegistro({
    boton: this,
    campoId: 'id_categoria_producto',
    modulo: 'categoriasProductos',
  });
  await cargarInputsActualizarQNR.call(modal.find("form"));
});

$(document).off("submit", ".formularioAjax");
$(document).on("submit", ".formularioAjax", async function (e) {
  e.preventDefault();
  let resultado = await enviarFormulario({
    'formulario': this,
    'modulo': 'categoriasProductos'
  })
  cargarDashboard();
  console.log(resultado);
  if (resultado['icono'] && resultado['icono'] == "success") {

  }
});

$(document).off("click", ".botonEliminar");
$(document).on("click", ".botonEliminar", async function (e) {
  e.preventDefault();
  eliminarRegistro({
    boton: this,
    campoId: 'id_categoria_producto',
    modulo: 'categoriasProductos',
  });
});

//Evento para validar en tiempo real
$(document).off('input', '.validar input, .validar select')
$(document).on('input', '.validar input, .validar select', function () {
  validarEnTiempoReal(this, 'categoriasProductos');
})
//#endregion [DELEGACIÓN DE EVENTOS] FIN
