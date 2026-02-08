//#region [ IMPORTACIONES ] COMIENZO
import {
    listarDataTable
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
//#endregion [ IMPORTACIONES ] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {
    await listarDataTable({
        encabezados: {
            "id_bitacora": "ID",
            "nombre_usuario": "USUARIO",
            "nombre_modulo": "MÓDULO",
            "nombre_accion": "ACCIÓN",
            "fecha_bitacora": "FECHA",
            "resultado_accion_bitacora": "RESULT",
        },
        informacionPe: {
            'modulo': 'bitacora',
            'datosPe': {
                'accion': 'listar'
            }
        }
    });
})
//#endregion [DELEGACIÓN DE EVENTOS] FIN

