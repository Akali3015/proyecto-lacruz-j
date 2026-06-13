//#region [ IMPORTACIONES ] COMIENZO
import {
  enviarFormulario, eliminarRegistro, obtenerDatosRegistro,
  listarDataTable, cargarInputsActualizarQNR, validarEnTiempoReal
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
import { driverAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"

//#endregion [ IMPORTACIONES ] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {
  await listarDataTable({
    encabezados: {
      "id_unidad_medida": "ID",
      "nombre_unidad_medida": "NOMBRE",
      "simbolo_unidad_medida": "SÍMBOLO",
      "equivalencia_ub": "EQUIVALENCIA A UNIDAD BASE",
    },
    informacionPe: {
      'modulo': 'unidadesMedidas',
      'datosPe': {
        'accion': 'listar'
      }
    },
    campoIdBtn: 'id_unidad_medida',
    botones: 'CRUD',
  });
  driverAyuda('unidadesMedidas', {
    pasos: [
      {
        element: 'button[data-bs-target=".modalRegistrar"]',
        popover: {
          title: 'Registrar Unidad de Medida',
          description: 'Haz clic aquí para agregar una nueva unidad de medida al sistema. Las unidades se utilizan en productos, materias primas y servicios.',
          side: 'bottom',
          align: 'start'
        }
      },
      {
        element: '.tabla-ajax',
        popover: {
          title: 'Lista de Unidades de Medida',
          description: 'Aquí puedes ver todas las unidades de medida registradas, su símbolo y equivalencia.',
          side: 'top'
        }
      },
      {
        element: '.botonEditar',
        popover: {
          title: 'Editar Unidad',
          description: 'Modifica los datos de cualquier unidad de medida haciendo clic en este botón.',
          side: 'left'
        }
      },
      {
        element: '.botonEliminar',
        popover: {
          title: 'Eliminar Unidad',
          description: 'Elimina unidades de medida que ya no sean necesarias. Ten cuidado porque puede afectar productos, materias primas y servicios asociados.',
          side: 'left'
        }
      },
      {
        popover: {
          title: '¡Ayuda completada!',
          description: 'Ya conoces la gestión de unidades de medida. Estas se utilizan en productos, materias primas, servicios y presentaciones.',
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
    'modulo': 'unidadesMedidas'
  })
});

//Evento para el envío de formularios
$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
  e.preventDefault();
  eliminarRegistro({
    boton: this,
    campoId: 'id_unidad_medida',
    modulo: 'unidadesMedidas',
  });
});

//Evento para los botones de editar
$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
  e.preventDefault();
  await obtenerDatosRegistro({
    boton: this,
    campoId: 'id_unidad_medida',
    modulo: 'unidadesMedidas',
  });
  cargarInputsActualizarQNR.call($($(this).attr('data-bs-target')).find('form'));
});

//Evento para validar en tiempo real
$(document).off('input', '.validar input, .validar select')
$(document).on('input', '.validar input, .validar select', function () {
  validarEnTiempoReal(this, 'unidadesMedidas');
})

//#endregion [DELEGACIÓN DE EVENTOS] FIN