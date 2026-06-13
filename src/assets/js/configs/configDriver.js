import { 
  mostrarOcultarSpinnerCarga,pedirDatosAjax,vista,rutaAbsoluta
} from "/proyecto-lacruz-j/src/assets/js/modulos/global.js";

//#region [ AYUDA INTERACTIVA CON DRIVER.JS ] COMIENZO

let driverInstanceGlobal = null;
let driverActivoGlobal = false;

// MODULOS
const listaCompletaModulos = [
  { id: 'accesos', nombre: 'Accesos', descripcion: 'Permisos por rol', ruta: 'accesos', permiso: 'permisos' },
  { id: 'bitacora', nombre: 'Bitácora', descripcion: 'Registro de actividades', ruta: 'bitacora', permiso: 'bitacora' },
  { id: 'bancos', nombre: 'Bancos', descripcion: 'Gestión de bancos', ruta: 'bancos', permiso: 'bancos' },
  { id: 'cambiosIva', nombre: 'IVA', descripcion: 'Configuración del IVA', ruta: 'cambiosIva', permiso: 'cambiosIva' },
  { id: 'categoriasProductos', nombre: 'Categorías', descripcion: 'Categorías de productos', ruta: 'categoriasProductos', permiso: 'categoriasProductos' },
  { id: 'compras', nombre: 'Compras', descripcion: 'Compras a proveedores', ruta: 'compras', permiso: 'compras' },
  { id: 'clientes', nombre: 'Clientes', descripcion: 'Base de datos de clientes', ruta: 'clientes', permiso: 'clientes' },
  { id: 'empresasEnvios', nombre: 'Empresas de Envíos', descripcion: 'Empresas de mensajería', ruta: 'empresasEnvios', permiso: 'empresasEnvios' },
  { id: 'facturacion', nombre: 'Facturación', descripcion: 'Proceso de facturación', ruta: 'facturacion', permiso: 'facturacion' },
  { id: 'home', nombre: 'Inicio / Dashboard', descripcion: 'Vista principal del sistema', ruta: 'home', permiso: 'dashboard' },
  { id: 'inventario', nombre: 'Inventario', descripcion: 'Control de stock', ruta: 'inventario', permiso: 'inventario' },
  { id: 'productos', nombre: 'Productos', descripcion: 'Gestión de productos y precios', ruta: 'productos', permiso: 'productos' },
  { id: 'usuarios', nombre: 'Usuarios', descripcion: 'Administración de usuarios', ruta: 'usuarios', permiso: 'usuarios' },
  { id: 'pedidos', nombre: 'Pedidos', descripcion: 'Seguimiento de pedidos', ruta: 'pedidos', permiso: 'pedidos' },
  { id: 'proveedores', nombre: 'Proveedores', descripcion: 'Gestión de proveedores', ruta: 'proveedores', permiso: 'proveedores' },
  { id: 'servicios', nombre: 'Servicios', descripcion: 'Catálogo de servicios', ruta: 'servicios', permiso: 'servicios' },
  { id: 'reportes', nombre: 'Reportes', descripcion: 'Reportes y estadísticas', ruta: 'reportes', permiso: 'reportes' },
  { id: 'producciones', nombre: 'Producciones', descripcion: 'Control de producción', ruta: 'producciones', permiso: 'producciones' },
  { id: 'materiasPrimas', nombre: 'Materias Primas', descripcion: 'Gestión de materias primas', ruta: 'materiasPrimas', permiso: 'materiasPrimas' },
  { id: 'roles', nombre: 'Roles', descripcion: 'Administración de roles', ruta: 'roles', permiso: 'roles' },
  { id: 'permisos', nombre: 'Permisos', descripcion: 'Gestión de permisos', ruta: 'permisos', permiso: 'permisos' },
  { id: 'monedas', nombre: 'Monedas', descripcion: 'Gestión de monedas', ruta: 'monedas', permiso: 'monedas' },
  { id: 'presentaciones', nombre: 'Presentaciones', descripcion: 'Presentaciones', ruta: 'presentaciones', permiso: 'presentaciones' },
  { id: 'unidadesMedidas', nombre: 'Unidades de Medida', descripcion: 'Unidades de medida', ruta: 'unidadesMedidas', permiso: 'unidadesMedidas' },
  { id: 'metodos-pago', nombre: 'Métodos de Pago', descripcion: 'Métodos de pago', ruta: 'metodos-pago', permiso: 'metodos-pago' },
  { id: 'rutas', nombre: 'Rutas', descripcion: 'Gestion de Rutas para la direccion', ruta: 'rutas', permiso: 'rutas' }
];

const driversConfigurados = {};

let permisosUsuario = null;

// Driver.js
const driverLib = window.driver.js.driver;

export async function cargarPermisosUsuario() {
  if (permisosUsuario) return permisosUsuario;

  try {
    const respuesta = await pedirDatosAjax({
      modulo: 'accesos',
      datosPe: { accion: 'listarPorRol' }
    });

    permisosUsuario = respuesta;
    return permisosUsuario;
  } catch (error) {
    permisosUsuario = {};
    return permisosUsuario;
  }
}
export function tienePermisoModulo(moduloId) {
  if (!permisosUsuario) return false;
  if (moduloId === 'home') {
    return permisosUsuario['dashboard'] && permisosUsuario['dashboard'].includes('ver dashboard');
  }
  const modulo = listaCompletaModulos.find(m => m.id === moduloId);
  if (!modulo) return false;
  const nombreModulo = modulo.permiso || modulo.id;
  return permisosUsuario[nombreModulo] && permisosUsuario[nombreModulo].includes('ver');
}
export async function obtenerModulosConPermisos() {
  await cargarPermisosUsuario();
  const modulosFiltrados = [];
  for (const modulo of listaCompletaModulos) {
    if (tienePermisoModulo(modulo.id)) {
      modulosFiltrados.push(modulo);
    }
  }
  modulosFiltrados.sort((a, b) => a.nombre.localeCompare(b.nombre));
  return modulosFiltrados;
}
export function driverAyuda(id, config) {
  if (!id || !config || !config.pasos || config.pasos.length === 0) {
    return false;
  }

  driversConfigurados[id] = {
    pasos: config.pasos,
    opciones: {
      showProgress: true,
      nextBtnText: 'Siguiente',
      prevBtnText: 'Anterior',
      doneBtnText: 'Finalizar',
      overlayOpacity: 0.75,
      animate: true,
      allowClose: true,
      ...config.opciones
    }
  };
  return true;
}
export function iniciarDriverModulo(id, forceShow = false) {
  const driver = driversConfigurados[id];

  if (!driver) {
    return false;
  }

  if (!forceShow) {
    const yaVisto = sessionStorage.getItem(`driver_${id}_visto`);
    if (yaVisto === 'true') {
      return false;
    }
  }

  if (driverInstanceGlobal) {
    try { driverInstanceGlobal.destroy(); } catch (e) { }
  }

  try {
    const opcionesConEventos = {
      ...driver.opciones,
      onDestroyed: () => {
        driverActivoGlobal = false;
        sessionStorage.setItem(`driver_${id}_visto`, 'true');
      }
    };

    driverInstanceGlobal = driverLib(opcionesConEventos);
    driverInstanceGlobal.setSteps(driver.pasos);
    driverActivoGlobal = true;
    driverInstanceGlobal.drive();

    return true;
  } catch (error) {
    return false;
  }
}

//#endregion

//#region [ MODAL DE AYUDA INTERACTIVA ] COMIENZO

function obtenerModuloActualURL() {
  const pathname = window.location.pathname;
  const partes = pathname.split('/');
  const indiceBase = partes.indexOf('proyecto-lacruz-j');
  if (indiceBase !== -1 && partes[indiceBase + 1]) {
    return partes[indiceBase + 1].split('?')[0];
  }
  return 'home';
}
async function cargarModulosEnModalAyuda(filtro = '') {
  const contenedor = $('#listaModulosAyuda');
  const sinResultados = $('#sinResultadosAyuda');
  mostrarOcultarSpinnerCarga('mostrar');
  try {
    await cargarPermisosUsuario();
    let modulos = await obtenerModulosConPermisos();

    console.log(modulos)

    if (filtro.trim() !== '') {
      const filtroLower = filtro.toLowerCase();
      modulos = modulos.filter(modulo =>
        modulo.nombre.toLowerCase().includes(filtroLower) ||
        modulo.descripcion.toLowerCase().includes(filtroLower) ||
        modulo.id.toLowerCase().includes(filtroLower)
      );
    }

    $('#totalModulosEncontrados').text(modulos.length);

    if (modulos.length === 0) {
      contenedor.addClass('d-none');
      sinResultados.removeClass('d-none');
      mostrarOcultarSpinnerCarga('ocultar');
      return;
    }

    contenedor.removeClass('d-none');
    sinResultados.addClass('d-none');

    const moduloActual = vista || obtenerModuloActualURL();

    let html = '';
    modulos.forEach(modulo => {
      const esModuloActual = modulo.id === moduloActual;

      html += `
        <div class="col-md-6 col-lg-4">
            <div class="card modulo-ayuda-card h-100 border-0 shadow-sm ${esModuloActual ? 'border-primary border-2' : ''}" 
                  data-modulo="${modulo.id}" 
                  data-ruta="${modulo.ruta}"
                  style="cursor: pointer; transition: all 0.2s ease;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-2">
                            <span class="fs-5">${modulo.nombre.charAt(0)}</span>
                        </div>
                        <h6 class="card-title mb-0 fw-bold">${modulo.nombre}</h6>
                        ${esModuloActual ? '<span class="badge bg-primary ms-2">Actual</span>' : ''}
                    </div>
                    <p class="card-text small text-muted mb-2">${modulo.descripcion}</p>
                </div>
            </div>
        </div>
      `;
    });

    contenedor.html(html);

    $('.modulo-ayuda-card').off('click').on('click', async function () {
      const moduloId = $(this).data('modulo');
      const rutaModulo = $(this).data('ruta');
      const moduloActualVisita = vista || obtenerModuloActualURL();

      $('#modalAyudaInteractiva').modal('hide');

      if (moduloId === moduloActualVisita) {
        if (driversConfigurados[moduloId]) {
          sessionStorage.removeItem(`driver_${moduloId}_visto`);
          setTimeout(() => {
            iniciarDriverModulo(moduloId, true);
          }, 300);
        }
      } else {
        sessionStorage.setItem('driver_pendiente', moduloId);
        window.location.href = rutaAbsoluta + rutaModulo;
      }
    });

  } catch (e){
    console.error(e)
    contenedor.addClass('d-none');
    sinResultados.removeClass('d-none');
    sinResultados.html('<p class="text-muted">Error al cargar los modulos. Intente de nuevo.</p>');
  } finally {
    mostrarOcultarSpinnerCarga('ocultar');
  }
}
export async function initAyudaInteractiva() {
  await cargarPermisosUsuario();

  const driverPendiente = sessionStorage.getItem('driver_pendiente');
  if (driverPendiente) {
    sessionStorage.removeItem('driver_pendiente');
    const moduloActual = vista || obtenerModuloActualURL();
    if (driverPendiente === moduloActual && driversConfigurados[driverPendiente]) {
      setTimeout(() => {
        iniciarDriverModulo(driverPendiente, true);
      }, 500);
    }
  }

  $('#btnAyudaInteractiva').off('click').on('click', function () {
    cargarModulosEnModalAyuda();
    $('#modalAyudaInteractiva').modal('show');
  });

  let timeoutBusqueda;
  $('#buscadorModulosAyuda').off('input').on('input', function () {
    clearTimeout(timeoutBusqueda);
    timeoutBusqueda = setTimeout(() => {
      cargarModulosEnModalAyuda($(this).val());
    }, 300);
  });

  $('#btnLimpiarBusquedaAyuda').off('click').on('click', function () {
    $('#buscadorModulosAyuda').val('');
    cargarModulosEnModalAyuda('');
  });

  $('#modalAyudaInteractiva').off('hidden.bs.modal').on('hidden.bs.modal', function () {
    $('#buscadorModulosAyuda').val('');
    cargarModulosEnModalAyuda('');
  });
}

//#endregion [ MODAL DE AYUDA INTERACTIVA ] FIN

$(document).on("DOMContentLoaded", async function () {
  initAyudaInteractiva();
});
