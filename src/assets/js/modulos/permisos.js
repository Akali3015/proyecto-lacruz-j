// #region [IMPORTACIONES] COMIENZO
import {
  pedirDatosAjax, españolDataTable
} from './global.js';
// #endregion [IMPORTACIONES] FIN

// #region [FUNCIONES PROPIAS DEL MÓDULO] COMIENZO

let rolListado = '';
async function listarPermisos(idRol) {
  if (idRol == rolListado) {
    return;
  }

  let permisos = await pedirDatosAjax({
    modulo: 'permisos',
    datosPe: {
      accion: 'listar',
      id_rol: idRol
    }
  });
  let { generales, especiales } = permisos

  if (typeof generales === 'string') {
    try {
      generales = await JSON.parse(generales);
    } catch (e) {
      generales = [];
    }
  }
  if (!Array.isArray(generales)) {
    generales = [generales];
  }

  // PERMISOS GENERALES
  const selector = '.listaPermisos';
  if (!$.fn.DataTable.isDataTable(selector)) {
    const arregloColumnas = [];
    const dynamicColumnDefs = [];
    let targetsCount = 0;

    // Columna 'Módulo'
    arregloColumnas.push({
      data: 'modulo.nombre',
      title: 'Módulo',
      render: function (data, type, row) {
        const modulo = data.toUpperCase().replace('_', ' ');
        return `${modulo}<input type="hidden" class="id_modulo" value="${row.modulo.id}">`;
      }
    });
    dynamicColumnDefs.push({
      targets: [targetsCount],
      className: 'dt-head-center'
    });
    targetsCount++;
    const nombresPermisos = [
      'ver',
      'listar',
      'registrar',
      'actualizar',
      'eliminar'
    ];
    nombresPermisos.forEach((nombrePermiso) => {
      arregloColumnas.push({
        data: null,
        title: nombrePermiso.charAt(0).toUpperCase() + nombrePermiso.slice(1),
        render: function (data, type, row) {
          const permiso = row.permisos.find((p) => p.nombre === nombrePermiso);
          let activo = permiso.activo === true ? 'checked' : '';
          return `
            <div class="d-flex justify-content-center form-check form-switch custom-switch-v1 mb-0">
                <input type="checkbox" class="permiso_checkbox form-check-input input-primary" idPermiso="${permiso.id}" ${activo} >
            </div>`
            ;
        }
      });
      dynamicColumnDefs.push({
        targets: [targetsCount],
        className: 'dt-head-center dt-body-center'
      });
      targetsCount++;
    });

    if (arregloColumnas.length === 0) {
      arregloColumnas.push({
        data: null,
        title: 'No hay datos disponibles'
      });
      dynamicColumnDefs.push({
        targets: [0],
        className: 'dt-body-center'
      });
      targetsCount = 1;
    }
    const configDataTable = {
      columns: arregloColumnas,
      autoWidth: false,
      columnDefs: dynamicColumnDefs,
      data: generales,
      language: españolDataTable
    };
    let tablaPermisos = $(selector).DataTable(configDataTable);
  } else {
    $(selector).DataTable().clear().rows.add(generales).draw();
  }

  // PERMISOS ESPECIALES
  rolListado = idRol;
  let CP = 2;
  let permisosEspHTML = '';
  especiales.forEach((permiso) => {
    if (CP % 2 == 0) {
      permisosEspHTML += `
        <div class="input-group">
      `;
    }
    const nombrePermiso = permiso.nombre_permiso.toUpperCase();
    const checked = permiso.status == 1 ? 'checked' : '';

    permisosEspHTML += `
        <span class="form-control bg-blanco">${nombrePermiso}</span>
        <div class="input-group-text">
            <div class="form-check form-switch p-0">
                <input idModulo="${permiso.id_modulo}" idPermiso="${permiso.id_permiso}" class="permiso_checkbox m-0 form-check-input h5 position-relative input-primary" type="checkbox" role="switch" ${checked}>
            </div>
        </div>
    `;

    if (CP % 2 == 1) {
      permisosEspHTML += `
        </div>
      `;
    }

    CP++;
  });

  $('.containerPermEspe').empty().append(permisosEspHTML);
}
async function cambioPermisos() {

  console.log('this: ', this)
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
    modulo: 'permisos',
    noGuardarLocal: true,
    datosPe: {
      accion: 'actualizar',
      id_rol: idRol,
      id_modulo: idModulo,
      id_permiso: idPermiso,
      cambio: cambioP
    }
  });
}
function cambiarOpcionRol() {
  $(this)
    .closest('.selectRolesPermisos')
    .find('a.active')
    .removeClass('active');
  $(this).find('a').addClass('active');
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
  console.log('roles: ', roles);
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
