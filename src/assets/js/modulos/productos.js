//#region [ IMPORTACIONES ] COMIENZO
import {
  enviarFormulario, eliminarRegistro, obtenerDatosRegistro,
  listarDataTable, cargarInputsActualizarQNR,
  extraerDatosAjax, pedirDatosAjax, obtenerSiguienteIndice,
  validarEnTiempoReal, formateoCampos, rutaFotos
} from "/proyecto-lacruz-j/src/assets/js/modulos/global.js";
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
      <div class="col-md-4 mb-3">
        <div class="form-check card-presentacion p-3 border rounded">
          <input 
            name="presentaciones-${i}"
            class="form-check-input checkbox-presentacion d-none" 
            type="checkbox" 
            value="${id_presentacion}"
          >
          <label class="form-check-label w-100">
            <div class="d-flex justify-content-between align-items-center">
              <strong>${nombre_presentacion}</strong>
              <span class="badge bg-info">${cantidad_pmp} ${nombre_unidad_medida}</span>
            </div>
          </label>
        </div>
      </div>
    `;
  }

  $(".contenedor-presentaciones").empty().append(html);
}
async function crearFilaMateriaPrima(materiaPrima = null) {

  let modal = $(this).closest('.modal');
  let codigoUniCoFila = obtenerSiguienteIndice(
    modal,
    "select",
    "materias_primas",
    "id_materia_prima"
  );
  let idMateriaPrima = materiaPrima ? materiaPrima["id_materia_prima"] : "";
  let cantidad = materiaPrima ? materiaPrima["cantidad_materia_prima"] : "";
  let plantillaFila = $($('.plantillaFilaMP').html().replaceAll('[COD-FILA]', codigoUniCoFila));
  let selectMateria = plantillaFila.find('.select-materia-prima');
  plantillaFila.find('.input-cantidad-materia').val(idMateriaPrima);
  await extraerDatosAjax({
    modulosPeticion: ["materiasPrimas"],
    accionesPeticion: [{ accion: "listar" }],
    tipoElemento: ["select"],
    elementosDestino: [selectMateria],
    datosInsertar: [
      {
        value: "id_materia_prima",
        texto: "nombre_materia_prima",
        textoDefault: "Seleccione una materia prima",
        opcionSeleccionada: idMateriaPrima,
      }
    ],
  });
  modal.find('.cuerpoTablaMateriasPrimas').append(plantillaFila);
}
async function calcularCostosMateriasPrimas(modal) {
  const tbody = modal.find(".cuerpoTablaMateriasPrimas");
  const contenedorInputs = modal.find("#inputsOcultosMaterias");
  let totalCosto = 0;
  contenedorInputs.empty();

  for (let i = 0; i < tbody.find("tr").length; i++) {
    const fila = $(tbody.find("tr")[i]);

    const materiaId = fila.find(".select-materia-prima").val();
    let cantidad = fila.find(".input-cantidad-materia").val().replaceAll('.', '').replaceAll(',', '.')
    cantidad = parseFloat(cantidad) || 0;

    let materiaPrimaBD = await pedirDatosAjax({
      modulo: "materiasPrimas",
      datosPe: {
        'accion': "seleccionarUno",
        'id_materia_prima': materiaId
      },
    });

    if (materiaId && materiaPrimaBD) {
      const costoUnitario = parseFloat(materiaPrimaBD['precio_materia_prima']) || 0;
      const subtotal = costoUnitario * cantidad;
      fila.find(".costo-unitario").text(`${costoUnitario.toFixed(2)}$`);
      fila.find(".subtotal").text(`${subtotal.toFixed(2)}$`);
      totalCosto += parseFloat(subtotal);
    }
  };

  modal.find("#totalCostoMaterias").text(`${totalCosto.toFixed(2)} $`);
}
async function inicializarModalProducto(modal) {
  try {
    let idProducto = modal.attr("id_producto");
    let productoBD = await pedirDatosAjax({
      modulo: "productos",
      datosPe: {
        'accion': "seleccionarUno",
        'id_producto': idProducto
      },
    });

    let {
      necesitan_materias_primas,
      detallesExtra = {}
    } = productoBD
    let {
      presentaciones = false,
      materias_primas = false
    } = detallesExtra

    //Seleccionamos las presentaciones del producto
    const contenedorPresentaciones = modal.find(".contenedor-presentaciones");
    contenedorPresentaciones
      .find('.checkbox-presentacion')
      .prop("checked", false)
      .closest(".card-presentacion")
      .removeClass("bg-light border-primary")
      ;
    presentaciones.forEach(id_presentacion => {
      contenedorPresentaciones
        .find('.checkbox-presentacion[value="' + id_presentacion + '"]')
        .prop("checked", true)
        .closest(".card-presentacion")
        .addClass("bg-light border-primary");
    });

    //Luego cargamos las materias primas
    let cuerpoMPHTML = modal.find(".cuerpoTablaMateriasPrimas");
    cuerpoMPHTML.empty();
    modal.find(".campos-fabricado").hide();
    if (necesitan_materias_primas == 1) {
      modal.find(".campos-fabricado").show();
      for (let i = 0; i < materias_primas.length; i++) {
        await crearFilaMateriaPrima.call(cuerpoMPHTML, materias_primas[i]);
      }
      await calcularCostosMateriasPrimas(modal);
    }

  } catch (error) {
    modal.find(".contenedor-presentaciones").html(`
      <div class="col-12">
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Error al cargar datos: ${error.message}
        </div>
      </div>
    `);
  }
}
function renderizarDashboard() {
  let datosTabla = $('.tabla-ajax').DataTable().rows().data().toArray();
  let total = datosTabla.length;
  let criticos = datosTabla.filter(p => parseFloat(p.stock_producto) <= parseFloat(p.stock_minimo_producto)).length;
  let valorDivisas = datosTabla.reduce((acc, p) => acc + (parseFloat(p.precio_producto) * parseFloat(p.stock_producto)), 0);

  let dashboardHTML = `
    <div class="row mb-4" id="metricasDashboard">
      <div class="col-md-4">
        <div class="card shadow-sm border-0 border-start border-4 border-primary h-100">
          <div class="card-body d-flex align-items-center">
            <div class="me-3">
              <div class="p-3 bg-primary bg-opacity-10 rounded-circle"><i class="fi fi-rr-box text-primary fs-3"></i></div>
            </div>
            <div>
              <h6 class="text-muted mb-1 text-uppercase" style="font-size:0.8rem; font-weight:700;">
                Productos Totales
              </h6>
              <h3 class="mb-0 fw-bold totalProdDashboard">
                ${total}
              </h3>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm border-0 border-start border-4 border-success h-100">
          <div class="card-body d-flex align-items-center">
            <div class="me-3">
              <div class="p-3 bg-success bg-opacity-10 rounded-circle">
                <i class="fi fi-rr-money text-success fs-3"></i>
              </div>
            </div>
            <div>
              <h6 class="text-muted mb-1 text-uppercase" style="font-size:0.8rem; font-weight:700;">
                Valor Inventario ($)
              </h6>
              <h3 class="valorTotalInventario mb-0 fw-bold">
                ${valorDivisas}$
              </h3>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm border-0 border-start border-4 border-danger h-100">
          <div class="card-body d-flex align-items-center">
            <div class="me-3">
              <div class="p-3 bg-danger bg-opacity-10 rounded-circle">
                <i class="fi fi-rr-triangle-warning text-danger fs-3 pulse-animation"></i>
              </div>
            </div>
            <div>
              <h6 class="text-muted mb-1 text-uppercase" style="font-size:0.8rem; font-weight:700;">
                Stock Crítico
              </h6>
              <h3 class="nroProdStockCriticos mb-0 fw-bold text-danger">${criticos}</h3>
            </div>
          </div>
        </div>
      </div>
    </div>
  `; 
  let dashboard=$('#metricasDashboard'); 
  if(dashboard.length==0){
    $(dashboardHTML).insertBefore($('.tabla-ajax').closest('.card'));
  }else{
    dashboard.find('.totalProdDashboard').text(total)
    dashboard.find('.valorTotalInventario').text(`${valorDivisas}$`)
    dashboard.find('.nroProdStockCriticos').text(criticos)
  }
}
//#endregion [FUNCIONES PROPIAS DEL MODULO] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on("DOMContentLoaded", async function () {
  await listarDataTable({
    encabezados: {
      "id_producto": "ID",
      "foto_producto": 'FOTO',
      "nombre_producto": "NOMBRE",
      "nombre_categoria": "CATEGORÍA",
      "precio_producto": "PRECIO",
      "stock_producto": "STOCK",
      "nombre_unidad_medida": "UNIDAD DE MEDIDA",
      "mostrar_ecommerce": "E-COMMERCE",
    },
    informacionPe: {
      'modulo': 'productos',
      modulo: "productos",
      datosPe: { accion: "listar" },
    },
    campoIdBtn: "id_producto",
    botones: "CRUD",
    infoTratoEspecial: {
      mostrar_ecommerce: (info) => {
        return info.valor == 1
          ? '<span class="badge bg-success"><i class="fi fi-rr-globe me-1"></i>Visible</span>'
          : '<span class="badge bg-secondary"><i class="fi fi-rr-eye-crossed me-1"></i>Oculto</span>';
      },
      precio_producto: (info) => {
        return `<strong>${parseFloat(info.valor).toFixed(2)}$</strong>`;
      },
      stock_producto: (info) => {
        const stockActual = parseFloat(info.valor);
        const stockMinimo = parseFloat(info.fila?.stock_minimo_producto ?? 0);
        const clase = stockActual <= stockMinimo ? 'danger' : 'success';
        return `<span class="badge bg-${clase} px-2 py-1" style="font-size:.85rem;">${stockActual}</span>`;
      },
      foto_producto: (info) => {
        let foto = info.valor != '' ? info.valor : 'productoDefault.png';
        return `
          <img 
            src="${rutaFotos}productos/${foto}"
            class="estiloFotoRegistro fotoRegistro shadow-sm"
            data-tabla_bd="productos"
            data-campo_id="id_producto"
            data-valor_id="${info.fila.id_producto}"
            data-campo_foto="foto_producto"
            data-accion_act="actualizarFoto"
            data-accion_eli="eliminarFoto"
            data-label_foto="Actualizar Foto del Producto"
            data-texto_alerta="La foto del producto volverá a la configuración predeterminada"
            data-foto_default="productoDefault.png"
          >
        `;
      }
    }
  });

  // Cargar categorías y dibujarlas manualmente para incluir el data-fabricado
  let categorias = await pedirDatosAjax({
    modulo: "categoriasProductos",
    datosPe: { accion: "listar" }
  });
  let htmlCat = '<option value="">Seleccione una categoría</option>';
  if (categorias && Array.isArray(categorias)) {
    categorias.forEach(cat => {
      htmlCat += `<option value="${cat.id_categoria_producto}" data-fabricado="${cat.necesitan_materias_primas}">${cat.nombre_categoria}</option>`;
    });
  }
  $('.selectCategoriaProducto').html(htmlCat);

  await extraerDatosAjax({
    modulosPeticion: ["unidadesMedidas"],
    accionesPeticion: [{ accion: "listar" }],
    tipoElemento: ["select"],
    elementosDestino: [$(".selectUnidadMedida")],
    datosInsertar: [
      {
        value: "id_unidad_medida",
        texto: "nombre_unidad_medida",
        textoDefault: "Seleccione una unidad",
      }
    ],
  });
  renderizarPresentaciones();
  renderizarDashboard();
});

$(document).off("click", "#btnAgregarMateriaPrima");
$(document).on("click", "#btnAgregarMateriaPrima", function () {
  crearFilaMateriaPrima.call(this);
});

$(document).off("click", ".btn-eliminar-materia");
$(document).on("click", ".btn-eliminar-materia", function () {
  let modal = $(this).closest(".modal");
  $(this).closest("tr").remove();
  calcularCostosMateriasPrimas(modal);
});

$(document).off("change", ".select-materia-prima");
$(document).on("change", ".select-materia-prima", function () {
  let modal = $(this).closest(".modal");
  calcularCostosMateriasPrimas(modal);
});

$(document).off("input", ".input-cantidad-materia");
$(document).on("input", ".input-cantidad-materia", function () {
  let modal = $(this).closest(".modal");
  calcularCostosMateriasPrimas(modal);
});

$(document).off("click", ".card-presentacion");
$(document).on("click", ".card-presentacion", function () {
  $(this).toggleClass("bg-light border-primary")
    .find('.checkbox-presentacion').prop("checked", function (i, val) {
      return !val;
    });
});

$(document).off("click", ".btn-seleccionar-todas");
$(document).on("click", ".btn-seleccionar-todas", function (e) {
  let modal = $(this).closest(".modal")
  modal.find(".checkbox-presentacion").prop("checked", true);
  modal.find(".card-presentacion").addClass("bg-light border-primary");
});

$(document).off("click", ".btn-deseleccionar-todas");
$(document).on("click", ".btn-deseleccionar-todas", function (e) {
  let modal = $(this).closest(".modal")
  modal.find(".checkbox-presentacion").prop("checked", false);
  modal.find(".card-presentacion").removeClass("bg-light border-primary");
});

// Cambiar si es fabricado de acuerdo a la categoria seleccionada
$(document).off("change", ".selectCategoria");
$(document).on("change", ".selectCategoria", function () {
  const opcionSeleccionada = $(this).find('option:selected');
  const esFabricado = opcionSeleccionada.attr('data-fabricado') == "1";
  const modal = $(this).closest(".modal");

  if (esFabricado) {
    modal.find(".campos-fabricado").show();
  } else {
    modal.find(".campos-fabricado").hide();
    modal.find(".cuerpoTablaMateriasPrimas").empty();
  }
});

$(document).off("click", ".botonEditar");
$(document).on("click", ".botonEditar", async function (e) {
  e.preventDefault();

  const idProducto = $(this).attr("value");
  const modalTarget = $(this).attr("data-bs-target");
  const modal = $(modalTarget);

  await obtenerDatosRegistro({
    boton: this,
    campoId: 'id_producto',
    modulo: 'productos',
  });
  await cargarInputsActualizarQNR.call(modal.find("form"));
  modal.attr("id_producto", idProducto);
  inicializarModalProducto(modal);
});

$(document).off("submit", ".formularioAjax");
$(document).on("submit", ".formularioAjax", async function (e) {
  e.preventDefault();
  let resultado= await enviarFormulario({
    convertirJSON: true,
    camposFoto: ['foto_producto'],
    formulario: this,
    modulo: 'productos'
  })
  if(resultado.icono && resultado.icono == 'success'){
    renderizarDashboard();
  }
});

$(document).off("click", ".botonEliminar");
$(document).on("click", ".botonEliminar", async function (e) {
  e.preventDefault();
  let resultado= await eliminarRegistro({
    boton: this,
    campoId: 'id_producto',
    modulo: 'productos',
  });
  if(resultado.resultadoBack.icono && resultado.resultadoBack.icono == 'success'){
    renderizarDashboard();
  }
});

$(document).off("input", ".dinero");
$(document).on("input", ".dinero", async function (e) {
  formateoCampos($(this), 'dinero')
});

//Evento para validar en tiempo real
$(document).off('input blur', '.validar input, .validar select')
$(document).on('input blur', '.validar input, .validar select', function () {
  validarEnTiempoReal(this, 'productos');
})
//#endregion [DELEGACIÓN DE EVENTOS] FIN