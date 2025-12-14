//#region [IMPORTACIONES] COMIENZO
import { pedirDatosAjax, españolDataTable }
from "/proyecto-lacruz-j/src/assets/js/modulos/global.js"
//#endregion [IMPORTACIONES] FIN 

//#region [FUNCIONES PROPIAS DEL MÓDULO] COMIENZO

let tablaPermisosInst = '';
let rolListado;

async function listarPermisos(idRol = null) {

    let datos = '';
    let permisosEsp = '';
    if (rolListado == undefined || idRol != rolListado) {
        let instruccionesPet = {
            'modulo': 'permisos',
            'datosPe': {
                'accion': 'listar'
            }
        }
        if (idRol != null) { instruccionesPet['datosPe']['id_rol'] = idRol }
        datos = await pedirDatosAjax(instruccionesPet)
        permisosEsp = datos['especiales']
        datos = datos['generales']
    } else {
        return;
    }


    if (typeof datos === 'string') {
        try {
            datos = JSON.parse(datos);
        } catch (e) {
            console.error("Error al parsear el JSON:", e);
            datos = [];
        }
    }
    if (!Array.isArray(datos)) { datos = [datos]; }

    if (idRol == null) {
        let selector = '.listaPermisos';
        rolListado = idRol;
        if ($.fn.DataTable.isDataTable(selector)) {
            $(selector).DataTable().destroy();
        }

        let arregloColumnas = [];
        let dynamicColumnDefs = [];
        let targetsCount = 0;

        // Columna 'Módulo'
        arregloColumnas.push({
            data: 'modulo.nombre',
            title: 'Módulo',
            render: function (data, type, row) {
                let modulo = data.toUpperCase().replace('_', ' ');
                return `${modulo}<input type="hidden" class="id_modulo" value="${row.modulo.id}">
            `;
            }
        });
        dynamicColumnDefs.push({ targets: [targetsCount], className: 'dt-head-center' });
        targetsCount++;

        const nombresPermisos = ["ver", "listar", "registrar", "actualizar", "eliminar"];

        nombresPermisos.forEach(nombrePermiso => {
            arregloColumnas.push({
                data: null, // No mapea directamente, se renderiza
                title: nombrePermiso.charAt(0).toUpperCase() + nombrePermiso.slice(1), // Capitaliza el nombre
                render: function (data, type, row) {
                    const permiso = row.permisos.find(p => p.nombre === nombrePermiso);
                    const estaActivo = permiso && permiso.activo === true;
                    if (estaActivo) {
                        return permiso ? `
                        <div class="d-flex justify-content-center form-check form-switch custom-switch-v1 mb-0">
                            <input type="checkbox" class="permiso_checkbox form-check-input input-primary" idPermiso="${permiso.id}" checked="true" >
                        </div>` : ''
                    } else {
                        return permiso ?
                            `<div class="d-flex justify-content-center form-check form-switch custom-switch-v1 mb-0">
                            <input type="checkbox" class="permiso_checkbox form-check-input input-primary" idPermiso="${permiso.id}">
                        </div>` : ''
                    }
                }
            });
            dynamicColumnDefs.push({ targets: [targetsCount], className: 'dt-head-center dt-body-center' });
            targetsCount++;
        });

        if (arregloColumnas.length === 0) {
            arregloColumnas.push({ data: null, title: 'No hay datos disponibles' });
            dynamicColumnDefs.push({ targets: [0], className: 'dt-body-center' });
            targetsCount = 1;
        }
        let configDataTable = {
            columns: arregloColumnas,
            autoWidth: false,
            columnDefs: dynamicColumnDefs,
            data: datos,
            language: españolDataTable,
        }
        tablaPermisosInst = $(selector).DataTable(configDataTable);
    } else {
        rolListado = idRol
        tablaPermisosInst.clear().rows.add(datos).draw();
    }

    let CP = 2; let permisosEspHTML = ``;
    permisosEsp.forEach(permiso => {
        if (CP % 2 == 0) {
            permisosEspHTML += `
            <div class="input-group">
            `;
        }

        let nombrePermiso = permiso['nombre_permiso'].toUpperCase();
        let checked = permiso['status'] == 1 ? 'checked' : '';

        permisosEspHTML += `
        <span class="form-control">${nombrePermiso}</span>
        <div class="input-group-text">
            <div class="form-check form-switch p-0">
                <input idModulo="${permiso['id_modulo']}" idPermiso="${permiso['id_permiso']}" class="permiso_checkbox m-0 form-check-input h5 position-relative" type="checkbox" role="switch" ${checked}>
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

    $('.containerPermEspe').empty().append(permisosEspHTML)

};
async function cambioPermisos() {

    let cambioP = this.checked ? 1 : 0;
    let idRol = $(this).closest('.contenedorPanel').find('.selectRolesPermisos').find('a.active').parent().attr('idRol');
    let idModulo = $(this).attr('idModulo') != undefined ? $(this).attr('idModulo') : $(this).closest('tr').find('.id_modulo').val();
    let idPermiso = $(this).attr('idPermiso');

    let instruccionesPe = {
        'modulo': 'permisos',
        'noGuardarLocal': true,
        'datosPe': {
            'accion': 'actualizar',
            'id_rol': idRol,
            'id_modulo': idModulo,
            'id_permiso': idPermiso,
            'cambio': cambioP,
        }
    }
    let respuesta = await pedirDatosAjax(instruccionesPe);
    console.log(respuesta)
}
function cambiarOpcionRol() {
    $(this).closest('.selectRolesPermisos').find('a.active').removeClass('active');
    $(this).find('a').addClass('active')
}
//#endregion [FUNCIONES PROPIAS DEL MÓDULO] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO

$(document).on('DOMContentLoaded', async function (e) {
    await listarPermisos();
    
    let instrucciones = {
        'modulo': 'roles',
        'datosPe': {
            'accion': 'listar',
        }
    }
    let roles = await pedirDatosAjax(instrucciones);
    $('.selectRolesPermisos').empty()

    let i = 0;
    let opcionRol = '';
    roles.forEach(rol => {

        let nombreRol = rol['nombre_rol'].toUpperCase()
        if (i == 0) {
            opcionRol = `
                <li class="opcionesRoles" idRol='${rol['id_rol']}'>
                    <a class="nav-link active" href="#">${nombreRol}</a>
                </li>`;
        } else {
            opcionRol = `
                <li class="opcionesRoles" idRol='${rol['id_rol']}'>
                    <a class="nav-link" href="#">${nombreRol}</a>
                </li>`;
        }
        i++;

        $('.selectRolesPermisos').append(opcionRol)
    })
})

//Para cambiar los colores del menu de opciones de los roles de usuario
$('.selectRolesPermisos').off('click', 'li');
$('.selectRolesPermisos').on('click', 'li', function (e) {
	cambiarOpcionRol.call(this);
});

//Evento para habilitar o deshabilitar permisos
$(document).off('change', '.permiso_checkbox');
$(document).on('change', '.permiso_checkbox', function (e) {
    cambioPermisos.call(this);
});

//Evento para desplegar los permisos en la tabla dependiendo el rol
$(document).off('click', '.opcionesRoles');
$(document).on('click', '.opcionesRoles', function (e) {
    let idRol = $(this).attr('idRol');
    listarPermisos.call(this, idRol);
});

//#endregion [DELEGACIÓN DE EVENTOS] FIN