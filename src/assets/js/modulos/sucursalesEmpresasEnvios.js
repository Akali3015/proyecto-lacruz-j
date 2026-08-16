//#region [ IMPORTACIONES ] COMIENZO
import {
  enviarFormulario, eliminarRegistro, obtenerDatosRegistro,
  listarDataTable, cargarInputsActualizarQNR, validarEnTiempoReal,
  pedirDatosAjax, extraerDatosAjax, mostrarOcultarSpinnerCarga, coorJLACRUZ,
  alertasAjax
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
import { driverAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"

//#endregion [ IMPORTACIONES ] FIN

//#region [FUNCIONES PROPIAS DEL MODULO] COMIENZO
async function cargarMapaSucursal() {
  const contenedorMapa = $('[id^="mapSucursal"]');
  console.log({ contenedorMapa })
  if (contenedorMapa.length > 0) {
    contenedorMapa.each((i, mapaInd) => {
      mapaInd._leaflet_id = null;

      // ubicación por default (ubicacion de la persona)
      let mapa = L.map(mapaInd.id).setView(coorJLACRUZ, 15);

      $(mapaInd).data('instancia', mapa)

      let marcadorActual = L.marker(coorJLACRUZ, {
        idMarcador: 'marcadorSucursal'
      }).addTo(mapa).bindPopup('Ubicación de la sucursal');

      // Capa de visualización
      L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="#">OpenStreetMap</a> contributors'
      }
      ).addTo(mapa);

      mapa.on('click', function clickEnMapa(e) {
        mapa.panTo([e.latlng.lat, e.latlng.lng]);
        setTimeout(() => {
          mapa.invalidateSize();
        }, 100);
      });
    })
  }
}
async function cambiarUbicacionSucursal() {
  if (!$(this).hasClass('error')) {
    try {
      mostrarOcultarSpinnerCarga('mostrar');
      let urlStr = $(this).val();
      let latitud = false, longitud = false;

      // 1. Intentar extraer con regex (para enlaces largos)
      const match3d4d = urlStr.match(/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/);
      const matchAt = urlStr.match(/@(-?\d+\.\d+),(-?\d+\.\d+)/);

      if (match3d4d) {
        latitud = match3d4d[1];
        longitud = match3d4d[2];
      } else if (matchAt) {
        latitud = matchAt[1];
        longitud = matchAt[2];
      } else {
        // 2. Intentar con la API si es un enlace corto o distinto
        let resultado = await pedirDatosAjax({
          JSONstring: true,
          url: 'https://api-the-vina-node.onrender.com/api/extraer-coordenadas-maps',
          datosPe: { url: urlStr }
        });
        latitud = resultado.latitud || false;
        longitud = resultado.longitud || false;
      }

      if (!latitud || !longitud) {
        return alertasAjax({
          'tipo': 'simple',
          'titulo': 'Ubicación no reconocida',
          'texto': 'No se pudieron deducir las coordenadas geografics de la ubicación',
          'icono': 'warning'
        });
      }
      cambiarMarcadorUbicaSucursal.call(this, latitud, longitud);
    } catch (error) {
      console.error(error);
    } finally {
      mostrarOcultarSpinnerCarga('ocultar');
    }
  }
}
function cambiarMarcadorUbicaSucursal(latitud, longitud) {
  let contenedor = $(this).closest('form').find('[id^="mapSucursal"]')[0];
  let instanciaMapa = $(contenedor).data('instancia') || null;
  if (!instanciaMapa) return;
  if (instanciaMapa) {
    instanciaMapa.eachLayer((layer) => {
      if (layer.options && layer.options.idMarcador === 'marcadorSucursal') {
        instanciaMapa.removeLayer(layer);
      }
    });
    let marcadorActual = L.marker([latitud, longitud], {
      idMarcador: 'marcadorSucursal'
    }).addTo(instanciaMapa).bindPopup('Ubicación de la sucursal');
    instanciaMapa.panTo([latitud, longitud]);
    setTimeout(() => {
      instanciaMapa.invalidateSize();
    }, 250);
  }

  let form = $(this).closest('form');
  form.find('.inputLatitud').val(latitud)
  form.find('.inputLongitud').val(longitud)
}

//#endregion [FUNCIONES PROPIAS DEL MODULO] COMIENZO

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {
  await listarDataTable({
    encabezados: {
      "nombre_empresa": "EMPRESA",
      "id_sucursal_empresa_envios": "ID SUCURSAL",
      "nombre_sucursal_empresa": "NOMBRE DE LA SUCURSAL",
    },
    informacionPe: {
      'modulo': 'sucursalesEmpresasEnvios',
      'datosPe': {
        'accion': 'listar'
      }
    },
    campoIdBtn: 'id_sucursal_empresa_envios',
    botones: 'CRUD',
  });
  await cargarMapaSucursal();
  extraerDatosAjax({
    'modulosPeticion': ['empresasEnvios'],
    accionesPeticion: [{ accion: 'listar' }],
    tipoElemento: ['select'],
    elementosDestino: [$('.selectEmpresaEnvios')],
    datosInsertar: [{
      'texto': 'nombre_empresa',
      'value': 'id_empresa_envios',
      'textoDefault': 'Seleccione una empresa'
    }],
  })
  driverAyuda('empresasEnvios', {
    pasos: [
      {
        element: 'button[data-bs-target=".modalRegistrar"]',
        popover: {
          title: 'Registrar Sucursal',
          description: 'Haz clic aquí para agregar una nueva sucursal de una empresa de envíos. Las sucursales se utilizan para gestionar envíos de pedidos.',
          side: 'bottom',
          align: 'start'
        }
      },
      {
        element: '.selectEmpresaEnvios',
        popover: {
          title: 'Empresa de Envíos',
          description: 'Selecciona la empresa de envíos a la que pertenece esta sucursal.',
          side: 'right'
        }
      },
      {
        element: 'input[name="nombre_sucursal_empresa"]',
        popover: {
          title: 'Nombre de la Sucursal',
          description: 'Ingresa el nombre identificador de la sucursal (ej: Sucursal Metropolis, Sucursal Barquisimeto).',
          side: 'right'
        }
      },
      {
        element: '.inputURL',
        popover: {
          title: 'Ubicación de la Sucursal',
          description: 'Pega el enlace de Google Maps de la ubicación de la sucursal. El sistema extraerá automáticamente las coordenadas.',
          side: 'right'
        }
      },
      {
        element: '[id^="mapSucursal"]',
        popover: {
          title: 'Mapa de Ubicación',
          description: 'Aquí se mostrará la ubicación de la sucursal en el mapa. Puedes hacer clic en el mapa para ajustar la ubicación.',
          side: 'top'
        }
      },
      {
        element: '.tabla-ajax',
        popover: {
          title: 'Lista de Sucursales',
          description: 'Aquí puedes ver todas las sucursales registradas, incluyendo la empresa a la que pertenecen.',
          side: 'top'
        }
      },
      {
        element: '.botonEditar',
        popover: {
          title: 'Editar Sucursal',
          description: 'Modifica los datos de cualquier sucursal haciendo clic en este botón.',
          side: 'left'
        }
      },
      {
        element: '.botonEliminar',
        popover: {
          title: 'Eliminar Sucursal',
          description: 'Elimina sucursales que ya no estén operativas. Ten cuidado porque puede afectar pedidos asociados.',
          side: 'left'
        }
      },
      {
        popover: {
          title: '¡Ayuda completada!',
          description: 'Ya conoces la gestión de sucursales de empresas de envíos. Estas se utilizan para gestionar los puntos de recogida o entrega de pedidos.',
          side: 'top'
        }
      }
    ]
  });
})

//Evento para el envío de formularios
$(document).off('input', '.inputURL');
$(document).on('input', '.inputURL', function (e) {
  cambiarUbicacionSucursal.call(this)
});

//Evento para el envío de formularios
$(document).off('submit', '.formularioAjax');
$(document).on('submit', '.formularioAjax', function (e) {
  e.preventDefault();
  enviarFormulario({
    'formulario': this,
    'modulo': 'sucursalesEmpresasEnvios'
  })
});

//Evento para el envío de formularios
$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
  e.preventDefault();
  eliminarRegistro({
    boton: this,
    campoId: 'id_sucursal_empresa_envios',
    modulo: 'sucursalesEmpresasEnvios',
  });
});

//Evento para los botones de editar
$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
  e.preventDefault();
  let datos = await obtenerDatosRegistro({
    boton: this,
    campoId: 'id_sucursal_empresa_envios',
    modulo: 'sucursalesEmpresasEnvios',
  });
  let form = $($(this).attr('data-bs-target')).find('form');
  cargarInputsActualizarQNR.call(form);
  let { coordenada_latitud, coordenada_longitud } = datos
  cambiarMarcadorUbicaSucursal.call(form.find('.inputURL'), coordenada_latitud, coordenada_longitud);
});

//Evento para validar en tiempo real
$(document).off('input', '.validar input, .validar select')
$(document).on('input', '.validar input, .validar select', function () {
  validarEnTiempoReal(this, 'sucursalesEmpresasEnvios');
})

// Evento para ajustar el mapa cuando se muestra el modal de registro
$(document).on('shown.bs.modal', '.modalRegistrar, .modalActualizar', function () {
  let mapa = $(this).find('[id^="mapSucursal"]').data('instancia');
  if (mapa) mapa.invalidateSize();
});
//#endregion [DELEGACIÓN DE EVENTOS] FIN