//#region [ IMPORTACIONES ] COMIENZO
import {
  enviarFormulario, eliminarRegistro, obtenerDatosRegistro,
  listarDataTable, extraerDatosAjax, cargarInputsActualizarQNR, validarEnTiempoReal,
  rutaFotos
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
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
          let foto= info.valor!='' ? info.valor :'perfilDefaultUsuario.png';
          return `
            <img 
              src="${rutaFotos}usuarios/${foto}"
              class="estiloFotoRegistro fotoRegistro shadow-sm"
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
  }
})

//Evento para el envío de formularios
$(document).off('submit', '.formularioAjax');
$(document).on('submit', '.formularioAjax', function (e) {
  e.preventDefault();
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
  await obtenerDatosRegistro({
    boton: this,
    campoId: 'cedula_usuario',
    modulo: 'usuarios',
  });
  cargarInputsActualizarQNR.call($($(this).attr('data-bs-target')).find('form'));
});

//Evento para validar en tiempo real
$(document).off('input blur', '.validar input, .validar select')
$(document).on('input blur', '.validar input, .validar select', function () {
  validarEnTiempoReal(this, 'usuarios');
})

//#endregion [DELEGACIÓN DE EVENTOS] FIN
