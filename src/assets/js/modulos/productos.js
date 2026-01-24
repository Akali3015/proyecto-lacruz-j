import{
    enviarFormulario, eliminarRegistro, obtenerDatosRegistro,
    encabezados, ListarDataTable, cargarInputsActualizarQNR,
    extraerDatosAjax, pedirDatosAjax, obtenerSiguienteIndice,
} from "/proyecto-lacruz-j/src/assets/js/modulos/global.js";

//#region [FUNCIONES DE PRESENTACIONES] COMIENZO
async function renderizarPresentaciones() {
    let presentacionesBD = await pedirDatosAjax({
        modulo: "presentaciones",
        datosPe: { accion: "listar" },
    });
    let html = "";
    presentacionesBD.forEach((presentacion) => {
        html += `
            <div class="col-md-4 mb-3">
                <div class="form-check card-presentacion p-3 border rounded">
                    <input 
                        name="presentaciones"
                        class="form-check-input checkbox-presentacion d-none" 
                        type="checkbox" 
                        value="${presentacion["id_presentacion"]}"
                    >
                    <label class="form-check-label w-100">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>${presentacion["nombre_presentacion"]}</strong>
                            <span class="badge bg-info">${presentacion["cantidad_pmp"]} ${presentacion["nombre_unidad_medida"]}</span>
                        </div>
                    </label>
                </div>
            </div>
        `;
    });
    $(".contenedor-presentaciones").empty().append(html);
}
//#endregion [FUNCIONES DE PRESENTACIONES] FIN

//#region [FUNCIONES DE MATERIAS PRIMAS] COMIENZO
async function crearFilaMateriaPrima(modal, materiaPrima = null) {

    let codigoUniCoFila = obtenerSiguienteIndice(
        modal,
        "select",
        "materias_primas",
        "id_materia_prima"
    );
    let idMateriaPrima = materiaPrima ? materiaPrima["id_materia_prima"] : "";
    let cantidad = materiaPrima ? materiaPrima["cantidad_materia_prima"] : "";

    let html = `
        <tr>
            <td>
                <select 
                    class="form-select select-materia-prima"
                    name="materias_primas[${codigoUniCoFila}][id_materia_prima]"
                >
                    <option value="">Seleccione una materia prima</option>
                </select>
            </td>
            <td>
                <input type="number" step="0.01" min="0.01" 
                    name="materias_primas[${codigoUniCoFila}][cantidad_materia_prima]" 
                    class="form-control input-cantidad-materia" 
                    value="${cantidad}"
                    placeholder="Cantidad"
                >
            </td>
            <td class="costo-unitario"">0.00 Bs</td>
            <td class="subtotal">0.00 Bs</td>
            <td class="d-flex justify-content-center align-items-center">
                <button type="button" class="btn btn-sm btn-danger btn-eliminar-materia">
                    <i class="fi fi-rr-trash-check p-1 fs-5"></i>
                </button>
            </td>
        </tr>
    `;

    modal.find("#cuerpoTablaMateriasPrimas").append(html);
    const selectMateria = modal.find("#cuerpoTablaMateriasPrimas")
        .find("tr").last().find(".select-materia-prima");

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

}
async function calcularCostosMateriasPrimas(modal) {
    const tbody = modal.find("#cuerpoTablaMateriasPrimas");
    const contenedorInputs = modal.find("#inputsOcultosMaterias");
    let totalCosto = 0;
    contenedorInputs.empty();

    for (let i = 0; i < tbody.find("tr").length; i++) {
        const fila = $(tbody.find("tr")[i]);

        const materiaId = fila.find(".select-materia-prima").val();
        const cantidad = parseFloat(fila.find(".input-cantidad-materia").val()) || 0;

        let materiaPrimaBD = await pedirDatosAjax({
            modulo: "materiasPrimas",
            datosPe: { 'accion': "seleccionarUno", 'id_materia_prima': materiaId },
        });

        if (materiaId && materiaPrimaBD) {
            const costoUnitario = parseFloat(materiaPrimaBD['costo_materia_prima']) || 0;
            const subtotal = costoUnitario * cantidad;
            fila.find(".costo-unitario").text(`${costoUnitario.toFixed(2)} Bs`);
            fila.find(".subtotal").text(`${subtotal.toFixed(2)} Bs`);
            totalCosto += parseFloat(subtotal);
        }
    };
    
    modal.find("#totalCostoMaterias").text(`${totalCosto.toFixed(2)} Bs`);
}
async function inicializarModalProducto(modal) {
    try {
        let idProducto = modal.attr("id_producto");
        let productoBD = await pedirDatosAjax({
            modulo: "productos",
            datosPe: { 'accion': "seleccionarUno", 'id_producto': idProducto },
        });
        
        //Seleccionamos las presentaciones del producto
        const contenedorPresentaciones = modal.find(".contenedor-presentaciones");
        contenedorPresentaciones.find('.checkbox-presentacion').prop("checked", false)
        .closest(".card-presentacion").removeClass("bg-light border-primary");

        productoBD["detallesExtra"]["idsPresentaciones"].forEach(id_presentacion => {
            contenedorPresentaciones.find('.checkbox-presentacion[value="' + id_presentacion + '"]').prop("checked", true)
            .closest(".card-presentacion").addClass("bg-light border-primary");
        });

        //Luego cargamos las materias primas
        const esFabricado = productoBD["producto_es_fabricado"] == "1";
        modal.find("#cuerpoTablaMateriasPrimas").empty();
        modal.find(".campos-fabricado").hide();

        if(esFabricado){
            modal.find(".campos-fabricado").show();
            for (let i = 0; i < productoBD["detallesExtra"]["materias_primas"].length; i++) {
                const materiaPrimaBD = productoBD["detallesExtra"]["materias_primas"][i];
                await crearFilaMateriaPrima(modal, materiaPrimaBD);
            }
            await calcularCostosMateriasPrimas(modal);
        }

    } catch (error) {
        console.error("Error al inicializar modal:", error);
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
//#endregion [FUNCIONES DE MATERIAS PRIMAS] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on("DOMContentLoaded", async function () {
    await ListarDataTable({
        encabezados: encabezados,
        modulo: "productos",
    });
    await extraerDatosAjax({
        modulosPeticion: ["unidadesMedidas"],
        accionesPeticion: [{ accion: "listar" }],
        tipoElemento: ["select"],
        elementosDestino: [$(".selectUnidadMedida")],
        datosInsertar: [
            {
                value: "id_unidad_medida",
                texto: "nombre_unidad_medida",
                textoDefault: "Seleccione una unidad de medida",
            },
        ],
    });
    renderizarPresentaciones();
});

$(document).off("click", "#btnAgregarMateriaPrima");
$(document).on("click", "#btnAgregarMateriaPrima", function () {
    let modal = $(this).closest(".modal");
    crearFilaMateriaPrima(modal);
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
    $(this).closest(".modal").find(".checkbox-presentacion").prop("checked", true);
});

$(document).off("click", ".btn-deseleccionar-todas");
$(document).on("click", ".btn-deseleccionar-todas", function (e) {
    $(this).closest(".modal").find(".checkbox-presentacion").prop("checked", false);
});

$(document).off("change", "#tipo_producto");
$(document).on("change", "#tipo_producto", function () {
    const esFabricado = $(this).val() == "1";
    const modal = $(this).closest(".modal");

    if (esFabricado) {
        modal.find(".campos-fabricado").show();
    } else {
        modal.find(".campos-fabricado").hide();
        modal.find("#cuerpoTablaMateriasPrimas").empty();
    }
});

$(document).off("click", ".botonEditar");
$(document).on("click", ".botonEditar", async function (e) {
    e.preventDefault();

    const idProducto = $(this).attr("value");
    const modalTarget = $(this).attr("data-bs-target");
    const modal = $(modalTarget);

    await obtenerDatosRegistro.call(this);
    await cargarInputsActualizarQNR.call(modal.find("form"));
    modal.attr("id_producto", idProducto);
    inicializarModalProducto(modal);
});

$(document).off("submit", ".formularioAjax");
$(document).on("submit", ".formularioAjax", async function (e) {
    e.preventDefault();
    await enviarFormulario.call(this);
});

$(document).off("click", ".botonEliminar");
$(document).on("click", ".botonEliminar", async function (e) {
    e.preventDefault();
    await eliminarRegistro.call(this);
});
//#endregion [DELEGACIÓN DE EVENTOS] FIN