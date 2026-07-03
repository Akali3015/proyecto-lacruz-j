// #region [IMPORTACIONES] COMIENZO
import {
  pedirDatosAjax, españolDataTable, reiniciarDataModuloSS, mLength,
  alertasAjax
} from './global.js';
import { driverAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"

// #endregion [IMPORTACIONES] FIN

// #region [FUNCIONES PROPIAS DEL MÓDULO] COMIENZO

let rolListado = '';
async function listarPermisos(idRol) {
  if (idRol == rolListado) return;
  rolListado = idRol;
  let dataPermisos = await pedirDatosAjax({
    modulo: 'accesos',
    datosPe: {
      accion: 'listar',
      id_rol: idRol
    }
  });
  let { mapeoPG, permisos } = dataPermisos

  // PERMISOS TOTALES
  let htmlPermisos = ``;
  let contadorItem = 0;

  let objetoOrdenado = {};
  for (const [clave, valor] of Object.entries(permisos)) {
    objetoOrdenado[clave.toLowerCase()] = valor;
  }
  for (const [modulo, datos] of Object.entries(objetoOrdenado)) {

    let { id_modulo, pg = false, pe = false } = datos

    //Permisos generales (5/f)
    let htmlPG = ``;
    if (pg) {
      let html = ``;
      for (const [id, estaActivo] of Object.entries(pg)) {
        const nombrePermiso = mapeoPG[id].toUpperCase();
        const checked = estaActivo == 1 ? 'checked' : '';
        html += `
          <span class="spanPG  nombrePermiso form-control bg-blanco align-items-center">${nombrePermiso}</span>
          <div class="inputPG input-group-text">
            <div class="form-check form-switch p-0">
              <input 
                idModulo="${id_modulo}" 
                idPermiso="${id}" 
                class="permiso_checkbox m-0 form-check-input h5 position-relative input-primary" 
                type="checkbox" 
                role="switch" 
                ${checked}>
            </div>
          </div>
        `;
      }
      htmlPG += `<div class="input-group permisos-generales">${html}</div>`;
    }

    //Permisos especiales (1-2/f)
    let htmlPE = '';
    if (pe) {
      let CP = 2;
      pe.forEach((permiso, indice) => {
        let { id_permiso, nombre_permiso, status } = permiso;
        if (CP % 2 == 0) htmlPE += `<div class="permisosEspeciales input-group ">`;
        const nombrePermiso = nombre_permiso.toUpperCase();
        const checked = status == 1 ? 'checked' : '';
        htmlPE += `
          <span class="nombrePermiso form-control bg-blanco align-items-center">${nombrePermiso}</span>
          <div class="input-group-text">
            <div class="form-check form-switch p-0">
              <input 
                idModulo="${id_modulo}" 
                idPermiso="${id_permiso}" 
                class="permiso_checkbox m-0 form-check-input h5 position-relative input-primary" 
                type="checkbox" 
                role="switch" 
                ${checked}>
            </div>
          </div>
        `;
        if (CP % 2 == 1 || indice + 1 == pe.length) htmlPE += `</div>`;
        CP++;
      })
    }

    htmlPermisos += `
      <div class="accordion-item">
        <h2 class="accordion-header" id="headingPermisoEsp${contadorItem}">
          <button class="accordion-button p-2 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#permisoEsp${contadorItem}" aria-expanded="false" aria-controls="permisoEsp${contadorItem}">
            ${modulo.toLocaleUpperCase()}
          </button>
        </h2>
        <div id="permisoEsp${contadorItem}" class="accordion-collapse collapse" aria-labelledby="headingPermisoEsp${contadorItem}" data-bs-parent="#acordionCaptures" style="">
          <div class="accordion-body">
            ${htmlPG + htmlPE}
          </div>
        </div>
      </div>
    `;
    contadorItem++;
  }
  $('.containerPermEspe').empty().append(`<div class="accordion">${htmlPermisos}</div>`);
  mostrarPermisosPorIntervalo.call($('.containerPermEspe'), true)
}
async function cambioPermisos() {

  const cambioP = this.checked ? 1 : 0;
  const idRol = $(this)
    .closest('.contenedorPanel')
    .find('.selectRolesPermisos')
    .find('a.active')
    .parent()
    .attr('idRol');
  const idModulo =
    $(this).attr('idModulo') != undefined
      ? $(this).attr('idModulo')
      : $(this)
        .closest('tr')
        .find('.id_modulo').val();
  const idPermiso = $(this).attr('idPermiso');

  const respuesta = await pedirDatosAjax({
    modulo: 'accesos',
    noGuardarLocal: true,
    datosPe: {
      accion: 'actualizar',
      id_rol: idRol,
      id_modulo: idModulo,
      id_permiso: idPermiso,
      cambio: cambioP
    }
  });
  if (respuesta?.icono == 'error') return alertasAjax(respuesta);
  if (respuesta?.icono == 'success') {
    reiniciarDataModuloSS('accesos')
  }
}
function cambiarOpcionRol() {
  $(this)
    .closest('.selectRolesPermisos')
    .find('a.active')
    .removeClass('active');
  $(this).find('a').addClass('active');
}
function expandirContraerModulosPermisos() {
  let esExpandir = $(this).text() == 'Expandir' ? true : false
  let acordiones = $(this).closest('.contenedorPanel').find('.accordion-button')
  console.log(acordiones)
  acordiones.each(function () {
    let item = $(this)
    if (esExpandir && item.hasClass('collapsed')) item.trigger('click');
    else if (!esExpandir && !item.hasClass('collapsed')) item.trigger('click');
  })
  if (esExpandir) $(this).text('Contraer').addClass('active')
  else $(this).text('Expandir').removeClass('active')
}
function mostrarPermisosPorIntervalo(construirPaginador = false) {
  let c = (s) => {
    return $(this).closest('.contenedorPanel').find(s);
  }
  let cantidadFilasPorPagina = c('.selectCantidadFilasPermisos').val();
  let textoBusqueda = c('.inputBusquedaPermisos').val()
  let nroPaginaActual = c('.paginadorPermisos').find('.page-item.active').find('a').text()
  let acordiones = c('.accordion-item');
  let totalFilas = acordiones.length;
  let inicio = nroPaginaActual == 1 ? 0 : ((parseInt(nroPaginaActual) - 1) * parseInt(cantidadFilasPorPagina));
  if (construirPaginador) inicio = 0;
  let fin = ((inicio + parseInt(cantidadFilasPorPagina)) <= totalFilas) ? (inicio + parseInt(cantidadFilasPorPagina)) : parseInt(totalFilas);
  let acordionesMostrar = acordiones;

  let ordenarObjeto = (itemsCoincidientes) => {
    let objetoOrdenado = Object.keys(itemsCoincidientes).sort().reduce((obj, key) => (obj[key] = itemsCoincidientes[key], obj), {});
    let arrayFinalOrdenado = Object.values(objetoOrdenado)
    return acordionesMostrar = $(arrayFinalOrdenado);
  }

  //Filtramos por la busqueda
  if (textoBusqueda != '') {
    let itemsCoincidientes = {};
    acordiones.each(function () {
      let item = $(this);
      let permisos = item.find('.nombrePermiso')
      let tieneUnPermisoQueCoincide = false;
      let nombreModulo = $(this).find('.accordion-button').text().toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");

      permisos.each(function () {
        let nombrePermisoLimpio = $(this).text().toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        if (textoBusqueda == '' || nombrePermisoLimpio.includes(textoBusqueda) || nombreModulo.includes(textoBusqueda)) {
          tieneUnPermisoQueCoincide = true;
          $(this).removeClass('d-none').next().removeClass('d-none');
        } else $(this).addClass('d-none').next().addClass('d-none');
      })

      if (tieneUnPermisoQueCoincide || nombreModulo.includes(textoBusqueda)) {
        itemsCoincidientes[nombreModulo] = this;
      }
    })
    acordionesMostrar = ordenarObjeto(itemsCoincidientes);
    totalFilas = mLength(itemsCoincidientes);
    fin = totalFilas < cantidadFilasPorPagina ? totalFilas : nroPaginaActual * cantidadFilasPorPagina;
  } else {
    let objeto = {}
    acordiones.each(function () {
      let item = $(this);
      let nombreModulo = $(this).find('.accordion-button').text().toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
      objeto[nombreModulo] = this;
    })
    acordionesMostrar = ordenarObjeto(objeto);
  }

  //Filtramos por la pagina seleccionada y la cantidad de elementos
  let elementosFinales = $(acordionesMostrar).slice(inicio, fin);
  let elementosOcultar = $(acordiones).not(elementosFinales);

  // console.log({ elementosFinales, elementosOcultar })
  // console.log({ inicio, fin, elementosFinales, elementosOcultar, nroPaginaActual, textoBusqueda, totalFilas })

  // Incertamos y mostramos los que queden
  elementosOcultar.addClass('d-none');
  elementosFinales.removeClass('d-none');
  c('.containerPermEspe').find('.accordion').prepend(elementosFinales);

  //leyenda de lo que se esta mostrando
  if (totalFilas == 0) {
    c('.textoMostrandoPermisos').text(`Sin registros disponibles`)
  } else if (totalFilas == 1) {
    c('.textoMostrandoPermisos').text(`Mostrando registro 1 de un total de 1 registro`)
  } else {
    c('.textoMostrandoPermisos').text(`Mostrando registros del ${inicio + 1} al ${fin} de un total de ${totalFilas} registros`)
  }

  if (construirPaginador) {

    //Botones
    let nroBotones = textoBusqueda != '' ? elementosFinales.length / cantidadFilasPorPagina : (totalFilas / cantidadFilasPorPagina);
    nroBotones = Math.ceil(nroBotones)
    let botones = ``;
    for (let i = 0; i < nroBotones; i++) {
      let active = i == 0 ? 'active' : '';
      botones += `
        <li class="paginate_button page-item ${active}">
          <a href="#" class="page-link">${i + 1}</a>
        </li>
      `;
    }

    let disabled = (totalFilas == 0 || totalFilas == 1) ? 'disabled':''
    let botonesC = `
      <li class="paginate_button page-item previous disabled">
        <a href="#" class="page-link">Anterior</a>
      </li>
      ${botones}
      <li class="paginate_button page-item next ${disabled}">
        <a href="#" class="page-link">Siguiente</a>
      </li>
    `;
    c('.paginadorPermisos').empty().append(botonesC);
  }
}
function cambiarPagina() {
  $(this).siblings('.paginate_button').removeClass('active')
  $(this).addClass('active');
  let cp = $(this).closest('.paginadorPermisos')

  let nroPaginaActual = $(this).text()
  let nroTotalPaginas = cp.find('.page-item:not(.next,.previous)').length
  if (nroPaginaActual == nroTotalPaginas) cp.find('.next').addClass('disabled')
  else cp.find('.next').removeClass('disabled')

  if (nroPaginaActual > 1) cp.find('.previous').removeClass('disabled')
  else cp.find('.previous').addClass('disabled');

  mostrarPermisosPorIntervalo.call(this)

}
function anteriorSiguientePagina() {

  if ($(this).hasClass('disabled')) return;
  let cp = $(this).closest('.paginadorPermisos');

  let nroPaginaActual = $(this).siblings('.page-item.active').find('a').text()
  if ($(this).hasClass('next')) {
    nroPaginaActual++;
  } else {
    nroPaginaActual--
  }
  if (nroPaginaActual == 0) return;
  let nuevaPagina = cp.find('a').get().find(a => {
    return $(a).text() == nroPaginaActual;
  });

  nuevaPagina = $(nuevaPagina).parent();
  if (nuevaPagina.length > 0) {
    nuevaPagina.siblings().removeClass('active')
    nuevaPagina.addClass('active')
  }

  let nroTotalPaginas = cp.find('.page-item:not(.next,.previous)').length
  if (nroPaginaActual == nroTotalPaginas) cp.find('.next').addClass('disabled')
  else cp.find('.next').removeClass('disabled')

  if (nroPaginaActual > 1) cp.find('.previous').removeClass('disabled')
  else cp.find('.previous').addClass('disabled')
  mostrarPermisosPorIntervalo.call(this)
}

// #endregion [FUNCIONES PROPIAS DEL MÓDULO] FIN

// #region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {

  const roles = await pedirDatosAjax({
    modulo: 'roles',
    datosPe: {
      accion: 'listar'
    }
  });
  $('.selectRolesPermisos').empty();

  let i = 0;
  let opcionRol = '';
  roles.forEach((rol) => {
    const nombreRol = rol.nombre_rol.toUpperCase();
    if (i == 0) {
      opcionRol = `
        <li class="opcionesRoles" idRol='${rol.id_rol}'>
          <a class="nav-link active" href="#">${nombreRol}</a>
        </li>
      `;
    } else {
      opcionRol = `
        <li class="opcionesRoles" idRol='${rol.id_rol}'>
          <a class="nav-link" href="#">${nombreRol}</a>
        </li>
      `;
    }
    i++;
    $('.selectRolesPermisos').append(opcionRol);
  });
  let id_rol = $('.selectRolesPermisos').find('.opcionesRoles').first().attr('idRol');
  await listarPermisos(id_rol);

  driverAyuda('accesos', {
    pasos: [
      {
        element: '.selectRolesPermisos',
        popover: {
          title: 'Selección de Roles',
          description: 'Aquí puedes seleccionar el rol al que deseas asignar o modificar permisos. Cada rol tiene diferentes niveles de acceso.',
          side: 'bottom',
          align: 'start'
        }
      },
      {
        element: '.listaPermisos',
        popover: {
          title: 'Permisos por Módulo',
          description: 'Esta tabla muestra todos los módulos del sistema y los permisos disponibles (Ver, Listar, Registrar, Actualizar, Eliminar) para el rol seleccionado.',
          side: 'top'
        }
      },
      {
        element: '.permiso_checkbox',
        popover: {
          title: 'Activar/Desactivar Permisos',
          description: 'Activa o desactiva los permisos para cada módulo. Los cambios se guardan automáticamente al hacer clic.',
          side: 'right'
        }
      },
      {
        popover: {
          title: '¡Ayuda completada!',
          description: 'Ya conoces la gestión de permisos. Puedes asignar o quitar accesos a los diferentes roles del sistema.',
          side: 'top'
        }
      }
    ]
  });
});

$(document).off('click', '.paginadorPermisos .next,.previous');
$(document).on('click', '.paginadorPermisos .next,.previous', function () {
  anteriorSiguientePagina.call(this);
});

$(document).off('click', '.paginadorPermisos .paginate_button:not(".next, .previous")');
$(document).on('click', '.paginadorPermisos .paginate_button:not(".next, .previous")', function () {
  cambiarPagina.call(this);
});

$(document).off('change', '.selectCantidadFilasPermisos');
$(document).on('change', '.selectCantidadFilasPermisos', function () {
  mostrarPermisosPorIntervalo.call(this, true);
});

$(document).off('input', '.inputBusquedaPermisos');
$(document).on('input', '.inputBusquedaPermisos', function () {
  mostrarPermisosPorIntervalo.call(this, true);
});

$(document).off('click', '.btnExpandirContraerPermisos');
$(document).on('click', '.btnExpandirContraerPermisos', function () {
  expandirContraerModulosPermisos.call(this)
});

// Para cambiar los colores del menu de opciones de los roles de usuario
$('.selectRolesPermisos').off('click', 'li');
$('.selectRolesPermisos').on('click', 'li', function (e) {
  cambiarOpcionRol.call(this);
});

// Evento para habilitar o deshabilitar permisos
$(document).off('change', '.permiso_checkbox');
$(document).on('change', '.permiso_checkbox', function (e) {
  cambioPermisos.call(this);
});

// Evento para desplegar los permisos en la tabla dependiendo el rol
$(document).off('click', '.opcionesRoles');
$(document).on('click', '.opcionesRoles', function (e) {
  const idRol = $(this).attr('idRol');
  listarPermisos.call(this, idRol);
});

// #endregion [DELEGACIÓN DE EVENTOS] FIN
