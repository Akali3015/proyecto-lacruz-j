//#region [ IMPORTACIONES ] COMIENZO
import {
  listarDataTable,cambiarFormatos
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
//#endregion [ IMPORTACIONES ] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {
  await listarDataTable({
    encabezados: {
      "accion": "ACCIÓN",
      "fecha_bitacora": "FECHA",
      "id_bitacora": "ID",
      "nombre_modulo": "MÓDULO",
      "nombre_usuario": "USUARIO",
      "resultado_bitacora": "RESULT",
    },
    informacionPe: {
      'modulo': 'bitacora',
      'datosPe': {
        'accion': 'listar'
      }
    },
    infoTratoEspecial: {
      fecha_bitacora: (info) => { return cambiarFormatos(info.valor, 'fecha_hora') },
    }
  });
})
//#endregion [DELEGACIÓN DE EVENTOS] FIN

