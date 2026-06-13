//#region [ IMPORTACIONES ] COMIENZO
import {
  enviarFormulario, eliminarRegistro, obtenerDatosRegistro,
  listarDataTable, cargarInputsActualizarQNR, extraerDatosAjax, validarEnTiempoReal
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
import { driverAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"

//#endregion [ IMPORTACIONES ] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {

  await listarDataTable({
    encabezados: {
      "id_presentacion": "ID",
      "nombre_presentacion": "NOMBRE",
      "cantidad_pmp": "CANTIDAD",
      "nombre_unidad_medida": "UNIDAD DE MEDIDA",
    },
    informacionPe: {
      'modulo': 'presentaciones',
      'datosPe': {
        'accion': 'listar'
      }
    },
    campoIdBtn: 'id_presentacion',
    botones: 'CRUD',
  });
  await extraerDatosAjax({
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
  driverAyuda('presentaciones', {
    pasos: [
      {
        element: 'button[data-bs-target=".modalRegistrar"]',
        popover: {
          title: 'Registrar Presentación',
          description: 'Haz clic aquí para agregar una nueva presentación al sistema. Las presentaciones definen cómo se venden los productos (ej: Litro, Kilo, Unidad).',
          side: 'bottom',
          align: 'start'
        }
      },
      {
        element: '.tabla-ajax',
        popover: {
          title: 'Lista de Presentaciones',
          description: 'Aquí puedes ver todas las presentaciones registradas, su unidad de medida y cantidad asociada.',
          side: 'top'
        }
      },
      {
        element: '.botonEditar',
        popover: {
          title: 'Editar Presentación',
          description: 'Modifica los datos de cualquier presentación haciendo clic en este botón.',
          side: 'left'
        }
      },
      {
        element: '.botonEliminar',
        popover: {
          title: 'Eliminar Presentación',
          description: 'Elimina presentaciones que ya no sean necesarias. Ten cuidado porque puede afectar productos y materias primas asociadas.',
          side: 'left'
        }
      },
      {
        popover: {
          title: '¡Ayuda completada!',
          description: 'Ya conoces la gestión de presentaciones. Las presentaciones se usan para definir las diferentes formas de venta de productos y materias primas.',
          side: 'top'
        }
      }
    ]
  });
})

//Evento para el envío de formularios
$(document).off('submit', '.formularioAjax');
$(document).on('submit', '.formularioAjax', function (e) {
  e.preventDefault();
  enviarFormulario({
    'formulario': this,
    'modulo': 'presentaciones'
  })
});

//Evento para el envío de formularios
$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
  e.preventDefault();
  eliminarRegistro({
    boton: this,
    campoId: 'id_presentacion',
    modulo: 'presentaciones',
  });
});

//Evento para los botones de editar
$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
  e.preventDefault();
  await obtenerDatosRegistro({
    boton: this,
    campoId: 'id_presentacion',
    modulo: 'presentaciones',
  });
  cargarInputsActualizarQNR.call($($(this).attr('data-bs-target')).find('form'));
});

//Evento para validar en tiempo real
$(document).off('input', '.validar input, .validar select')
$(document).on('input', '.validar input, .validar select', function () {
  validarEnTiempoReal(this, 'presentaciones');
})
//#endregion [DELEGACIÓN DE EVENTOS] FIN

