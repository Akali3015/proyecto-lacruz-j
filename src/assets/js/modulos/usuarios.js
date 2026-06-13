//#region [ IMPORTACIONES ] COMIENZO
import {
  enviarFormulario, eliminarRegistro, obtenerDatosRegistro,
  listarDataTable, extraerDatosAjax, cargarInputsActualizarQNR, 
  validarEnTiempoReal,rutaFotos
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
import { driverAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"

//#endregion [ IMPORTACIONES ] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {
  if ($('.nombreVista').val() != 'login') {
    await listarDataTable({
      encabezados: {
        "cedula_usuario": "CÉDULA",
        "foto_usuario": "FOTO",
        "nombre_rol": "ROL",
        "nombre_usuario": "NOMBRE",
        "apellido_usuario": "APELLIDO",
        "telefono_usuario": "TELÉFONO",
        "correo_usuario": "CORREO",
        "usuario_usuario": "USUARIO",
      },
      informacionPe: {
        'modulo': 'usuarios',
        'datosPe': {
          'accion': 'listar'
        }
      },
      campoIdBtn: 'cedula_usuario',
      botones: 'CRUD',
      infoTratoEspecial: {
        foto_usuario: (info) => {
          let foto = info.valor != '' ? info.valor : 'perfilDefaultUsuario.png';
          return `
            <img 
              src="${rutaFotos}usuarios/${foto}"
              class="estiloFotoRegistro fotoRegistro shadow-sm"
              data-modulo="usuarios"
              data-tabla_bd="usuarios"
              data-campo_id="cedula_usuario"
              data-valor_id="${info.fila.cedula_usuario}"
              data-campo_foto="foto_usuario"
              data-accion_act="actualizarFoto"
              data-accion_eli="eliminarFoto"
              data-label_foto="Actualizar Foto de Perfil"
              data-texto_alerta="Tu foto de perfil volverá a la configuración predeterminada"
              data-foto_default="perfilDefaultUsuario.png"
            >`;
        }
      }
    });
    extraerDatosAjax({
      'modulosPeticion': ['roles'],
      'accionesPeticion': [{ 'accion': 'listar' }],
      'tipoElemento': ['select'],
      'elementosDestino': [$('.selectRoles')],
      'datosInsertar': [
        {
          'value': 'id_rol',
          'texto': 'nombre_rol',
          'textoDefault': 'Seleccione un rol'
        }
      ]
    })
    driverAyuda('usuarios', {
      pasos:
        [
          {
            element: 'button[data-bs-target=".modalRegistrar"]',
            popover: {
              title: 'Registrar Usuarios',
              description: 'Haz clic aquí para registrar un nuevo usuario del sistema.',
              side: 'bottom',
              align: 'start'
            }
          },
          {
            element: '.tabla-ajax',
            popover: {
              title: 'Lista de Usuarios',
              description: 'Aquí puedes ver todos los usuarios registradas del sistema.',
              side: 'top'
            }
          },
          {
            element: '.botonEditar',
            popover: {
              title: 'Editar Usuarios',
              description: 'Modifica el nombre, rol, foto de perfil etc de cualquier usuario haciendo clic en este botón.',
              side: 'left'
            }
          },
          {
            element: '.botonEliminar',
            popover: {
              title: 'Eliminar Usuarios',
              description: 'En este boton podras Eliminar los usuarios del sistema.',
              side: 'left'
            }
          },
          {
            popover: {
              title: '¡Ayuda completada!',
              description: 'Ya conoces la gestion de usuarios. Da click en finaliar para acabar la ayuda.',
              side: 'top'
            }
          }
        ]
    });
  }
})

//Evento para el envío de formularios
$(document).off('submit', '.formularioAjax');
$(document).on('submit', '.formularioAjax', function (e) {
  e.preventDefault();
  /* if ($(this).hasClass('login')) {
    const respuestaCaptcha = grecaptcha.getResponse();
    if (respuestaCaptcha === "") {
      e.preventDefault();
      e.stopImmediatePropagation();
      Swal.fire({
        icon: 'warning',
        title: 'Validación Requerida',
        text: 'Por favor, resuelva el puzle de seguridad para poder ingresar.',
        confirmButtonColor: '#4e54c8'
      });
      return false;
    }
  } */
  enviarFormulario({
    'formulario': this,
    'modulo': 'usuarios'
  })
});

//Evento para el envío de formularios
$(document).off('click', '.botonEliminar');
$(document).on('click', '.botonEliminar', function (e) {
  e.preventDefault();
  eliminarRegistro({
    boton: this,
    campoId: 'cedula_usuario',
    modulo: 'usuarios',
  });
});

//Evento para los botones de editar
$(document).off('click', '.botonEditar');
$(document).on('click', '.botonEditar', async function (e) {
  e.preventDefault();
  let datos = await obtenerDatosRegistro({
    boton: this,
    campoId: 'cedula_usuario',
    modulo: 'usuarios',
  });
  let form = $($(this).attr('data-bs-target')).find('form');
  form.find('[name="prefijo_telefono_usuario"]').val(datos.telefono_usuario.slice(0, 4));
  form.find('[name="telefono_usuario"]').val(datos.telefono_usuario.slice(4));
  cargarInputsActualizarQNR.call(form);
});

//Evento para validar en tiempo real
$(document).off('input', '.validar input, .validar select')
$(document).on('input', '.validar input, .validar select', function () {
  validarEnTiempoReal(this, 'usuarios');
})

//#endregion [DELEGACIÓN DE EVENTOS] FIN
