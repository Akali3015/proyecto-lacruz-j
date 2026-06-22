import { 
  mostrarOcultarSpinnerCarga, pedirDatosAjax, vista, rutaAbsoluta
} from "/proyecto-lacruz-j/src/assets/js/modulos/global.js";

//#region [ AYUDA INTERACTIVA CON DRIVER.JS ] COMIENZO

let driverInstanceGlobal = null;
let driverActivoGlobal = false;
let spinnerAyuda = null;
let tutorialesRegistrados = false;

// MODULOS
const listaCompletaModulos = [
  { id: 'accesos', nombre: 'Accesos', descripcion: 'Permisos por rol', ruta: 'accesos', permiso: 'permisos' },
  { id: 'bitacora', nombre: 'Bitácora', descripcion: 'Registro de actividades', ruta: 'bitacora', permiso: 'bitacora' },
  { id: 'bancos', nombre: 'Bancos', descripcion: 'Gestión de bancos', ruta: 'bancos', permiso: 'bancos' },
  { id: 'cambiosIva', nombre: 'IVA', descripcion: 'Configuración del IVA', ruta: 'cambiosIva', permiso: 'cambiosIva' },
  { id: 'cambios-monedas', nombre: 'Cambios de Moneda', descripcion: 'Gestión de cambios de moneda', ruta: 'monedas/cambios-monedas', permiso: 'ver historial de cambio' },
  { id: 'categoriasProductos', nombre: 'Categorías', descripcion: 'Categorías de productos', ruta: 'categoriasProductos', permiso: 'categoriasProductos' },
  { id: 'compras', nombre: 'Compras', descripcion: 'Compras a proveedores', ruta: 'compras', permiso: 'compras' },
  { id: 'clientes', nombre: 'Clientes', descripcion: 'Base de datos de clientes', ruta: 'clientes', permiso: 'clientes' },
  { id: 'empresasEnvios', nombre: 'Empresas de Envíos', descripcion: 'Empresas de mensajería', ruta: 'empresasEnvios', permiso: 'empresasEnvios' },
  { id: 'facturacion', nombre: 'Facturación', descripcion: 'Proceso de facturación', ruta: 'facturacion', permiso: 'facturacion' },
  { id: 'home', nombre: 'Inicio / Dashboard', descripcion: 'Vista principal del sistema', ruta: 'home', permiso: 'dashboard' },
  { id: 'inventario', nombre: 'Inventario', descripcion: 'Control de stock', ruta: 'inventario', permiso: 'ver inventario' },
  { id: 'productos', nombre: 'Productos', descripcion: 'Gestión de productos y precios', ruta: 'productos', permiso: 'productos' },
  { id: 'usuarios', nombre: 'Usuarios', descripcion: 'Administración de usuarios', ruta: 'usuarios', permiso: 'usuarios' },
  { id: 'pedidos', nombre: 'Pedidos', descripcion: 'Seguimiento de pedidos', ruta: 'pedidos', permiso: 'pedidos' },
  { id: 'proveedores', nombre: 'Proveedores', descripcion: 'Gestión de proveedores', ruta: 'proveedores', permiso: 'proveedores' },
  { id: 'servicios', nombre: 'Servicios', descripcion: 'Catálogo de servicios', ruta: 'servicios', permiso: 'servicios' },
  { id: 'reportes', nombre: 'Reportes', descripcion: 'Reportes y estadísticas', ruta: 'reportes', permiso: 'ver reportes' },
  { id: 'producciones', nombre: 'Producciones', descripcion: 'Control de producción', ruta: 'producciones', permiso: 'producciones' },
  { id: 'materiasPrimas', nombre: 'Materias Primas', descripcion: 'Gestión de materias primas', ruta: 'materiasPrimas', permiso: 'materiasPrimas' },
  { id: 'roles', nombre: 'Roles', descripcion: 'Administración de roles', ruta: 'roles', permiso: 'roles' },
  { id: 'permisos', nombre: 'Permisos', descripcion: 'Gestión de permisos', ruta: 'permisos', permiso: 'permisos' },
  { id: 'monedas', nombre: 'Monedas', descripcion: 'Gestión de monedas', ruta: 'monedas', permiso: 'monedas' },
  { id: 'modulos', nombre: 'Módulos', descripcion: 'Gestión de módulos', ruta: 'modulos', permiso: 'modulos' },
  { id: 'presentaciones', nombre: 'Presentaciones', descripcion: 'Presentaciones', ruta: 'presentaciones', permiso: 'presentaciones' },
  { id: 'unidadesMedidas', nombre: 'Unidades de Medida', descripcion: 'Unidades de medida', ruta: 'unidadesMedidas', permiso: 'unidadesMedidas' },
  { id: 'metodos-pago', nombre: 'Métodos de Pago', descripcion: 'Métodos de pago', ruta: 'metodos-pago', permiso: 'metodos-pago' },
  { id: 'rutas', nombre: 'Rutas', descripcion: 'Gestion de Rutas para la direccion', ruta: 'rutas', permiso: 'rutas' },
  { id: 'repartidores', nombre: 'Repartidores', descripcion: 'Gestion de Repartidores', ruta: 'repartidores', permiso: 'repartidores' },
  { id: 'ordenesServicios', nombre: 'Órdenes de Servicio', descripcion: 'Gestión de órdenes de servicio', ruta: 'ordenesServicios', permiso: 'ordenesServicios' },
];

const driversConfigurados = {};

let permisosUsuario = null;

// Driver.js
const driverLib = window.driver.js.driver;

function mostrarSpinnerAyuda() {
  if (spinnerAyuda) return;
  
  spinnerAyuda = $(`
    <div id="spinnerAyudaDriver" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 99999; display: flex; justify-content: center; align-items: center;">
      <div class="bg-white rounded-3 p-4 shadow-lg" style="text-align: center; min-width: 250px;">
        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
          <span class="visually-hidden">Cargando...</span>
        </div>
        <h6 class="mb-1">Preparando ayuda interactiva</h6>
        <p class="text-muted small mb-0">Cargando tutorial...</p>
      </div>
    </div>
  `);
  $('body').append(spinnerAyuda);
}

function ocultarSpinnerAyuda() {
  if (spinnerAyuda) {
    spinnerAyuda.fadeOut(300, function() {
      $(this).remove();
      spinnerAyuda = null;
    });
  }
}

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
  
  const nombrePermiso = modulo.permiso;

  if (permisosUsuario[nombrePermiso]) {
    return true;
  }
  
  if (nombrePermiso.includes('ver ')) {
    const permisoExacto = permisosUsuario[nombrePermiso];
    if (permisoExacto && permisoExacto.includes(nombrePermiso)) {
      return true;
    }
  }

  for (const [key, value] of Object.entries(permisosUsuario)) {
    if (key === nombrePermiso || key === moduloId) {
      return true;
    }
    if (value && Array.isArray(value) && value.includes('ver')) {
      return true;
    }
  }
  
  return false;
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
    ocultarSpinnerAyuda();
    console.warn(`No hay tutorial configurado para el módulo: ${id}`);
    return false;
  }

  if (!forceShow) {
    const yaVisto = sessionStorage.getItem(`driver_${id}_visto`);
    if (yaVisto === 'true') {
      ocultarSpinnerAyuda();
      return false;
    }
  }

  if (driverInstanceGlobal) {
    try { driverInstanceGlobal.destroy(); } catch (e) { }
  }

  try {
    const pasosProcesados = driver.pasos.map(paso => {
      if (!paso.element || paso.element === null) {
        const { element, ...resto } = paso;
        return resto;
      }
      return paso;
    });

    const opcionesConEventos = {
      ...driver.opciones,
      onDestroyed: () => {
        driverActivoGlobal = false;
        sessionStorage.setItem(`driver_${id}_visto`, 'true');
        ocultarSpinnerAyuda();
      },
      onHighlightStarted: (element, step) => {
        ocultarSpinnerAyuda();
      }
    };

    driverInstanceGlobal = driverLib(opcionesConEventos);
    driverInstanceGlobal.setSteps(pasosProcesados);
    driverActivoGlobal = true;
    driverInstanceGlobal.drive();

    return true;
  } catch (error) {
    console.error('Error al iniciar driver:', error);
    ocultarSpinnerAyuda();
    return false;
  }
}

export function mostrarAyuda() {
  const moduloActual = vista || obtenerModuloActualURL();
  if (driversConfigurados[moduloActual]) {
    sessionStorage.removeItem(`driver_${moduloActual}_visto`);
    mostrarSpinnerAyuda();
    requestAnimationFrame(() => {
      setTimeout(() => {
        iniciarDriverModulo(moduloActual, true);
      }, 100);
    });
    return true;
  }
  return false;
}

export function marcarTutorialesRegistrados() {
  tutorialesRegistrados = true;
}

export function tieneTutorial(modulo) {
  return !!driversConfigurados[modulo];
}

export async function esperarDOMListo() {
  if (document.readyState === 'complete') {
    return;
  }
  
  return new Promise((resolve) => {
    const checkReadyState = () => {
      if (document.readyState === 'complete') {
        resolve();
      } else {
        requestAnimationFrame(checkReadyState);
      }
    };
  
    window.addEventListener('load', resolve, { once: true });
    requestAnimationFrame(checkReadyState);
  });
}

export async function esperarDataTableLista(selector, maxIntentos = 30) {
  let intentos = 0;
  
  while (intentos < maxIntentos) {
    if ($.fn.DataTable.isDataTable(selector)) {
      const tabla = $(selector).DataTable();
      if (tabla.data().any() || tabla.rows().count() > 0) {
        return true;
      }
      await new Promise(resolve => {
        tabla.one('draw', resolve);
        setTimeout(resolve, 200);
      });
      return true;
    }
    await new Promise(resolve => setTimeout(resolve, 100));
    intentos++;
  }
  return false;
}

function esElementoVisible(elemento) {
  if (!elemento) return false;
  const rect = elemento.getBoundingClientRect();
  return rect.width > 0 && rect.height > 0;
}

function existeElemento(selector) {
  if (typeof selector === 'string') {
    return $(selector).length > 0;
  }
  return !!selector;
}

export async function esperarElementoVisible(selector, maxIntentos = 50) {
  let intentos = 0;
  
  while (intentos < maxIntentos) {
    let elemento;
    if (typeof selector === 'string') {
      elemento = document.querySelector(selector);
    } else {
      elemento = selector;
    }
    
    if (elemento && esElementoVisible(elemento)) {
      return elemento;
    }
    
    await new Promise(resolve => setTimeout(resolve, 100));
    intentos++;
  }
  
  if (typeof selector === 'string') {
    const $elemento = $(selector);
    if ($elemento.length > 0) {
      console.warn(`Elemento existe pero no es visible: ${selector}`);
      return null;
    }
  }
  
  return null;
}

export async function iniciarAyudaModulo(modulo, pasos, opciones = {}) {
  mostrarSpinnerAyuda();
  
  driverAyuda(modulo, { pasos, ...opciones });
  
  await esperarDOMListo();
  
  const pasosValidos = [];
  for (const paso of pasos) {
    if (paso.element) {
      let elementoValido = null;
      let elementoExiste = false;
      
      if (typeof paso.element === 'string') {
        elementoExiste = existeElemento(paso.element);
        if (elementoExiste) {
          elementoValido = await esperarElementoVisible(paso.element);
        }
      } else if (paso.element instanceof HTMLElement) {
        elementoValido = paso.element;
        elementoExiste = true;
      } else if (paso.element && paso.element.length) {
        elementoValido = paso.element[0];
        elementoExiste = true;
      }
      
      if (elementoValido) {
        pasosValidos.push({
          ...paso,
          element: elementoValido
        });
      } else if (elementoExiste) {
        console.warn(`Elemento existe pero no es visible: ${paso.element}`);
        pasosValidos.push({
          ...paso,
          element: null,
          popover: {
            ...paso.popover,
            side: 'center',
            description: `[Elemento no visible actualmente] ${paso.popover.description || ''}`
          }
        });
      } else {
        console.warn(`Elemento no encontrado: ${paso.element}`);
        pasosValidos.push({
          ...paso,
          element: null,
          popover: {
            ...paso.popover,
            side: 'center',
            description: `[Elemento no disponible] ${paso.popover.description || ''}`
          }
        });
      }
    } else {
      pasosValidos.push(paso);
    }
  }
  
  if (pasosValidos.length === 0) {
    console.warn('No hay pasos válidos para mostrar');
    ocultarSpinnerAyuda();
    return false;
  }
  
  if (driversConfigurados[modulo]) {
    driversConfigurados[modulo].pasos = pasosValidos;
  }
  
  await new Promise(resolve => setTimeout(resolve, 300));

  mostrarAyuda();
  
  return true;
}


//#region [ MODAL DE AYUDA INTERACTIVA ] COMIENZO

function obtenerModuloActualURL() {
  const pathname = window.location.pathname;
  const partes = pathname.split('/');
  const indiceBase = partes.indexOf('proyecto-lacruz-j');
  if (indiceBase !== -1 && partes[indiceBase + 1]) {
    const ruta = partes[indiceBase + 1];
    if (ruta === 'monedas' && partes[indiceBase + 2] === 'cambios-monedas') {
      return 'cambios-monedas';
    }
    return ruta.split('?')[0];
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
          mostrarSpinnerAyuda();
          setTimeout(() => {
            iniciarDriverModulo(moduloId, true);
          }, 300);
        }
      } else {
        sessionStorage.setItem('driver_pendiente', moduloId);
        window.location.href = rutaAbsoluta + rutaModulo;
      }
    });

  } catch (e) {
    console.error(e)
    contenedor.addClass('d-none');
    sinResultados.removeClass('d-none');
    sinResultados.html('<p class="text-muted">Error al cargar los modulos. Intente de nuevo.</p>');
  } finally {
    mostrarOcultarSpinnerCarga('ocultar');
  }
}

export function initModalAyuda() {
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

export async function initAyudaInteractiva() {
  await cargarPermisosUsuario();

  await new Promise(resolve => setTimeout(resolve, 500));

  const driverPendiente = sessionStorage.getItem('driver_pendiente');
  if (driverPendiente) {
    sessionStorage.removeItem('driver_pendiente');
    const moduloActual = vista || obtenerModuloActualURL();
    if (driverPendiente === moduloActual && driversConfigurados[driverPendiente]) {
      setTimeout(() => {
        sessionStorage.removeItem(`driver_${driverPendiente}_visto`);
        mostrarSpinnerAyuda();
        iniciarDriverModulo(driverPendiente, true);
      }, 800);
    }
  }
}

//#endregion [ MODAL DE AYUDA INTERACTIVA ] FIN

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    initModalAyuda();
  });
} else {
  initModalAyuda();
}

esperarDOMListo().then(() => {
  initAyudaInteractiva();
});