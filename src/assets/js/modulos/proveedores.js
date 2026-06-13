//#region [ IMPORTACIONES ] COMIENZO
import {
  enviarFormulario, eliminarRegistro, obtenerDatosRegistro,
  listarDataTable, cargarInputsActualizarQNR, validarEnTiempoReal,
  pedirDatosAjax, funcionEliminaError, funcionMandarError
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
import { driverAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"

//#endregion [ IMPORTACIONES ] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {
  await listarDataTable({
    encabezados: {
      "rif_proveedor": "RIF",
      "razon_social_proveedor": "RAZON SOCIAL",
      "telefono_proveedor": "TELÉFONO",
      "correo_proveedor": "CORREO ELECTRÓNICO",
      "direccion_proveedor": "DIRECCIÓN",
    },
    informacionPe: {
      'modulo': 'proveedores',
      'datosPe': {
        'accion': 'listar'
      }
    },
    campoIdBtn: 'rif_proveedor',
    botones: 'CRUD',
  });
  driverAyuda('proveedores', {
    pasos: [
      {
        element: 'button[data-bs-target=".modalRegistrar"]',
        popover: {
          title: 'Registrar Proveedor',
          description: 'Haz clic aquí para agregar un nuevo proveedor al sistema. Los proveedores se utilizan en las compras de productos y materias primas.',
          side: 'bottom',
          align: 'start'
        }
      },
      {
        element: '.tabla-ajax',
        popover: {
          title: 'Lista de Proveedores',
          description: 'Aquí puedes ver todos los proveedores registrados con su información de contacto.',
          side: 'top'
        }
      },
      {
        element: '.botonEditar',
        popover: {
          title: 'Editar Proveedor',
          description: 'Modifica los datos de cualquier proveedor haciendo clic en este botón.',
          side: 'left'
        }
      },
      {
        element: '.botonEliminar',
        popover: {
          title: 'Eliminar Proveedor',
          description: 'Elimina proveedores que ya no sean necesarios. Ten cuidado porque puede afectar compras asociadas.',
          side: 'left'
        }
      },
      {
        popover: {
          title: '¡Ayuda completada!',
          description: 'Ya conoces la gestión de proveedores. Puedes registrar, editar o eliminar proveedores para mantener actualizada tu base de datos.',
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
    'modulo': 'proveedores'
  })
});

//Evento para el envío de formularios
$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
  e.preventDefault();
  eliminarRegistro({
    boton: this,
    campoId: 'rif_proveedor',
    modulo: 'proveedores',
  });
});

//Evento para los botones de editar
$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
  e.preventDefault();
  let datos= await obtenerDatosRegistro({
    boton: this,
    campoId: 'rif_proveedor',
    modulo: 'proveedores',
  });
  let form=$($(this).attr('data-bs-target')).find('form');
  form.find('[name="prefijo_telefono_proveedor"]').val(datos.telefono_proveedor.slice(0,4));
  form.find('[name="telefono_proveedor"]').val(datos.telefono_proveedor.slice(4));
  cargarInputsActualizarQNR.call(form);
});

//Evento para validar en tiempo real
$(document).off('input', '.validar input, .validar select, .validar textarea');
$(document).on('input', '.validar input, .validar select, .validar textarea', function () {
  validarEnTiempoReal(this, 'proveedores');
})
//#endregion [DELEGACIÓN DE EVENTOS] FIN