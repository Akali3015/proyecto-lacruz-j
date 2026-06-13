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
      "cedula_repartidor": "CÉDULA",
      "nombre_repartidor": "NOMBRE",
      "apellido_repartidor": "APELLIDO",
      "telefono_repartidor": "TELÉFONO",
    },
    informacionPe: {
      'modulo': 'repartidores',
      'datosPe': {
        'accion': 'listar'
      }
    },
    campoIdBtn: 'cedula_repartidor',
    botones: 'CRUD',
  });
  driverAyuda('repartidores', {
    pasos: [
      {
        element: 'button[data-bs-target=".modalRegistrar"]',
        popover: {
          title: 'Registrar Repartidor',
          description: 'Haz clic aquí para agregar un nuevo repartidor al sistema. Los repartidores se asignan a los pedidos con delivery.',
          side: 'bottom',
          align: 'start'
        }
      },
      {
        element: '.tabla-ajax',
        popover: {
          title: 'Lista de Repartidores',
          description: 'Aquí puedes ver todos los repartidores registrados con su información personal y de contacto.',
          side: 'top'
        }
      },
      {
        element: '.botonEditar',
        popover: {
          title: 'Editar Repartidor',
          description: 'Modifica los datos de cualquier repartidor haciendo clic en este botón.',
          side: 'left'
        }
      },
      {
        element: '.botonEliminar',
        popover: {
          title: 'Eliminar Repartidor',
          description: 'Elimina repartidores que ya no trabajen con la empresa. Ten cuidado porque puede afectar pedidos asignados.',
          side: 'left'
        }
      },
      {
        popover: {
          title: '¡Ayuda completada!',
          description: 'Ya conoces la gestión de repartidores. Los repartidores se asignan a los pedidos para realizar las entregas a domicilio.',
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
    'modulo': 'repartidores',
  });
});

//Evento para el envío de formularios
$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
  e.preventDefault();
  eliminarRegistro({
    boton: this,
    campoId: 'cedula_repartidor',
    modulo: 'repartidores',
  });
});

//Evento para los botones de editar
$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
  e.preventDefault();
  let datos = await obtenerDatosRegistro({
    boton: this,
    campoId: 'cedula_repartidor',
    modulo: 'repartidores',
  });
  let form = $($(this).attr('data-bs-target')).find('form');
  form.find('[name="prefijo_telefono_repartidor"]').val(datos.telefono_repartidor.slice(0, 4));
  form.find('[name="telefono_repartidor"]').val(datos.telefono_repartidor.slice(4));
  cargarInputsActualizarQNR.call(form);
});

//Evento para validar en tiempo real
$(document).off('input', '.validar input, .validar select')
$(document).on('input', '.validar input, .validar select', function () {
  validarEnTiempoReal(this, 'repartidores');
})
//#endregion [DELEGACIÓN DE EVENTOS] FIN