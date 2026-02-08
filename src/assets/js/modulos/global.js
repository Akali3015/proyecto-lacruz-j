//#region [VARIABLES O CONSTANTES GLOBALES] COMIENZO
export const rutaAbsoluta = window.location.origin + "/proyecto-lacruz-j/";
export let esteFormulario;
export let vista = $('.nombreVista').val();
export let instanciasDatatable = [];
export let variableDeError = '';
export let inputsActualizarNoRepetir = {};

//#region [Lenguajes] COMIENZO
export const españolDataTable = {
    "sProcessing": "Procesando...",
    "sLengthMenu": "Mostrar _MENU_ registros",
    "sZeroRecords": "No se encontraron resultados",
    "sEmptyTable": "Ningún dato disponible en esta tabla",
    "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
    "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
    "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
    "sSearch": "Buscar:",
    "sInfoThousands": ",",
    "sLoadingRecords": "Cargando...",
    "oPaginate": {
        "sFirst": "Primero",
        "sLast": "Último",
        "sNext": "Siguiente",
        "sPrevious": "Anterior"
    },
    "oAria": {
        "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
    },
    "buttons": {
        "copy": "Copiar",
        "colvis": "Visibilidad"
    }
};
//#endregion [Lenguajes] FIN

//#endregion [VARIABLES O CONSTANTES GLOBALES] FIN

//#region [ VALIDACIONES ] COMIENZO
export async function validarEnTiempoReal(input, modulo) {

    input = $(input);
    let nameImput = input.attr('name')
    let valorIntroducido = input.val();
    let minimo = input.attr('minlength') || false;
    let maximo = input.attr('maxlength') || false;
    let expresionRegular = RegExp(input.attr('pattern')) || false;
    let requerido = input.attr('required') || false;
    let esValido = expresionRegular.test(valorIntroducido);

    let funcionAlertaError = (texto) => {
        return `<div class="mensajeError text-danger small mt-1">${texto}</div>`;
    };
    if ($(input).closest('form').hasClass('login')) {
        funcionAlertaError = (texto) => {
            return `
                <div class="mensajeError d-flex alert alert-danger alert-dismissible fade show mt-3">
                    <i class="fi fi-rr-triangle-warning me-2"></i>
                    ${texto}
                </div>
            `;
        }
    }
    let funcionMandarError = (mensaje) => {
        let mensajeHTML = funcionAlertaError(mensaje);
        let contenedorGI = input.closest('.form-group').length > 0 ? input.closest('.form-group') : input.closest('[class^="col-"]');
        input.removeClass('validado').addClass('error');

        if (contenedorGI.find('.msjError').length > 0) {
            contenedorGI.find('.msjError').find('.mensajeError').remove();
            contenedorGI.find('.msjError').append(mensajeHTML)
        } else {
            contenedorGI.find('.mensajeError').remove();
            contenedorGI.append(mensajeHTML)
        }
    }
    let funcionEliminaError = () => {
        input.addClass('validado').removeClass('error');
        let contenedorGI = input.closest('.form-group').length > 0 ? input.closest('.form-group') : input.closest('[class^="col-"]');
        contenedorGI.find('.mensajeError').remove();
    }

    //Validar si es requerido
    if (requerido && valorIntroducido == '') {
        funcionMandarError('Este campo es obligatorio!!!');
        return;
    } else {
        funcionEliminaError();
    }

    if ((!requerido && valorIntroducido == '') || input.attr('readonly')) {
        input.removeClass('validado error')
        return;
    }

    //Para validar el minimo del campo
    if (minimo && valorIntroducido.length < minimo) {
        funcionMandarError(`El valor del campo debe ser mayor o igual a ${minimo} caracteres`)
        return;
    } else {
        funcionEliminaError();
    }

    //Para validar el maximo del campo
    if (maximo && valorIntroducido.length > maximo) {
        funcionMandarError(`El valor del campo debe ser menor o igual a ${maximo} caracteres`)
        return;
    } else {
        funcionEliminaError()
    }

    //Para validar la contrasena de confirmación
    if (input.attr('id') == 'contrasena2_usuario') {
        if ($('#contrasena1_usuario').val() != $('#contrasena2_usuario').val()) {
            funcionMandarError('El valor de ambas contraseña debe coincidir');
            return;
        } else {
            funcionEliminaError();
        }
    }

    //Para validar el formato del campo
    if (!esValido) {
        funcionMandarError('El valor del campo no es valido');
        return;
    } else {
        funcionEliminaError();
    }

    //Para validar campos que deben tener valores únicos
    if (input.hasClass('noRepetir')) {
        let proseguir = false;

        if (input.hasClass('formularioActualizar')) {
            if (
                inputsActualizarNoRepetir[nameImput] != valorIntroducido &&
                inputsActualizarNoRepetir[nameImput] != valorIntroducido.toUpperCase()
            ) {
                proseguir = true;
            }
        } else {
            proseguir = true;
        }
        if (proseguir != true) {
            return;
        }

        // Interacción con la BD
        let instruccionesPe = {
            'modulo': modulo,
            'datosPe': {
                'accion': 'listar',
            },
        }
        let registrosExistentes = await pedirDatosAjax(instruccionesPe);

        let mandaAlerta = false;
        for (let i = 0; i < registrosExistentes.length; i++) {
            if (
                registrosExistentes[i][`${nameImput}`] == valorIntroducido ||
                registrosExistentes[i][`${nameImput}`] == valorIntroducido.toUpperCase()
            ) {
                mandaAlerta = true;
                break;
            }
        }
        if (mandaAlerta) {
            funcionMandarError('El dato ingresado ya se encuentra registrado')
        } else {
            funcionEliminaError();
        }
    }
}
export async function validarTodosLosCampos(formulario, modulo) {

    let elementosForm = $(formulario).find('input, select');
    elementosForm.each(async (indice, elemento) => {
        await validarEnTiempoReal(elemento, modulo);
    });

    let hayUnoInvalido = false;
    elementosForm.each((indice, elemento) => {
        if ($(elemento).hasClass('error')) {
            hayUnoInvalido = true;
        }
    })

    if (hayUnoInvalido) {
        Swal.fire({
            icon: 'error',
            title: 'Hay campos inválidos',
            text: 'No se puede enviar el formulario con campos inválidos',
        })
        return true;
    } else {
        return false;
    }
}
export function cargarInputsActualizarQNR() {
    inputsActualizarNoRepetir = {};
    let inputsNR = $(this).find('.formularioActualizar.noRepetir');
    inputsNR.each((indice, input) => {
        inputsActualizarNoRepetir[$(input).attr('name')] = $(input).val();
    });
}
//#endregion [ VALIDACIONES ] FIN

//#region [ LISTAR CON DATATABLE ] COMIENZO
export async function listarDataTable(instrucciones) {
    const permisos = await pedirDatosAjax({
        modulo: 'permisos',
        noGuardarLocal: true,
        datosPe: {
            accion: 'listarPorRol'
        }
    });

    console.log(permisos)

    let {
        selectorTabla = '.tabla-ajax',
        encabezados = null,
        informacionPe,
        botones = null,
        camposFuera = [],
        campoIdBtn,
        infoTratoEspecial = {},
        camposFoto = [],
    } = instrucciones;
    let {
        modulo
    } = informacionPe
    let fotoDefault = 'default';
    fotoDefault += (modulo !== 'usuarios') ? '2' : '';
    let botonesCRUD = function (info) {
        let id = info['fila'][campoIdBtn];
        let boton = '';
        boton += '<ul class="list-inline me-auto mb-0">';
        if (permisos[modulo]) {
            if (permisos[modulo].includes('actualizar')) {
                boton += `
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar datos del registro">
                        <a href="#" value="${id}"  class="botonEditar avtar avtar-xs btn-link-success btn-pc-default" data-bs-toggle="modal" data-bs-target=".modalActualizar">
                        <i class="fi fi-rs-pen-circle fs-3 iconoCentrado"></i>
                        </a>
                    </li>`;
            }
            if (permisos[modulo].includes('eliminar')) {
                boton += `
                    <li value="${id}" class="botonEliminar list-inline-item align-bottom" data-bs-toggle="tooltip" title="Eliminar">
                        <a href="#" class="avtar avtar-xs btn-link-danger btn-pc-default">
                        <i class="fi fi-rs-trash fs-3 iconoCentrado"></i>
                        </a>
                    </li>`;
            }
        }
        boton += '</ul>';
        return boton;
    };
    let botonesAccion;
    if (!botones) {
        botonesAccion = null
    } else if (botones == 'CRUD') {
        botonesAccion = botonesCRUD;
    } else if (botones) {
        botonesAccion = instrucciones.botones;
    }

    // Destruye cualquier instancia existente de DataTables en la tabla para evitar conflictos
    if ($.fn.DataTable.isDataTable(selectorTabla)) {
        $(selectorTabla).DataTable().destroy();
    }
    const data = await pedirDatosAjax(informacionPe);
    let datos = data;
    const arregloColumnas = [];
    const dynamicColumnDefs = [];
    let targetsCount = 0;
    let textoEncabezados;

    // Intenta parsear los datos si vienen como un JSON de tipo string
    if (typeof datos === 'string') {
        try {
            datos = await JSON.parse(datos);
        } catch (e) {
            datos = []; // Si falla el parseo, tratamos como un arreglo vacío
        }
    }

    // para construir el objeto con los nombres de los campos que vienen en los datos del servidor
    let keysParaLasColumnas = [];
    if (encabezados) {
        keysParaLasColumnas = Object.keys(encabezados);
    } else if (datos.length >= 1) {
        keysParaLasColumnas = Object.keys(datos[0]);
    } else {
        console.error('No se pudieron encontrar encabezados para las columnas');
        return;
    }

    keysParaLasColumnas.forEach((key) => {
        if (!camposFuera.includes(key)) {

            let transformar = (key) => {
                return (key.charAt(0).toUpperCase() + key.slice(1)).replace(/_/g, ' ');
            }

            textoEncabezados = encabezados ? (encabezados[key] ?? transformar(key)) : transformar(key);
            if (infoTratoEspecial[key]) {
                arregloColumnas.push({
                    data: key,
                    title: textoEncabezados,
                    render: function (valor, type, fila) {
                        return infoTratoEspecial[key]({
                            valor,
                            fila,
                            type
                        });
                    }
                });
            } else if (camposFoto.includes(key)) {
                const subCarpetaImg = obtenerSubCarpetaImg(key);
                arregloColumnas.push({
                    data: key,
                    title: textoEncabezados,
                    render: function (data, type, row) {
                        let rutaImagenCompleta = rutaAbsoluta + rutaImagenes
                        rutaImagenCompleta += data ? subCarpetaImg + data : fotoDefault + '.png';
                        return `
                            <img 
                                src="${rutaImagenCompleta}"
                                class="
                                    imagenRegistro 
                                    img-fluid rounded-circle 
                                    circular-image-bootstrap
                                " 
                                id_registro="${row[campoIdBtn]}" 
                                campo_id="${campoIdBtn}" 
                                campo_foto="${key}" 
                                modulo="${modulo}" 
                                data-bs-toggle="modal" 
                                data-bs-target=".modalActualizarFoto" 
                                alt="Foto de perfil" 
                                style="width:50px; height:50px; border-radius:50%; cursor: pointer;"
                            >
                        `;
                    }
                });
            } else {
                arregloColumnas.push({
                    data: key,
                    title: textoEncabezados
                });
            }
            // Añade la definición de clase
            dynamicColumnDefs.push({ targets: [targetsCount], className: 'dt-body-center dt-head-center' });
            targetsCount++;
        }
    });

    if (arregloColumnas.length === 0) {
        arregloColumnas.push({ data: null, title: 'No hay datos disponibles' });
        dynamicColumnDefs.push({ targets: [0], className: 'tabla' });
        targetsCount = 1; // Aseguramos que targetsCount esté correcto para la siguiente columna (acciones)
    }
    if (botonesAccion != null) {
        arregloColumnas.push({
            data: null,
            title: 'ACCIONES',
            render: function (data, type, row) {
                let info = {
                    permisos,
                    'fila': row,
                }
                return botonesAccion(info);
            }
        });
        dynamicColumnDefs.push({ orderable: false, className: 'acciones dt-body-center dt-head-center', targets: [targetsCount] });
    }

    // Inicializa DataTables con la configuración construida
    const dataTableInstance = await $(selectorTabla).DataTable({
        ajax: function (data, callback, settings) {
            pedirDatosAjax(informacionPe)
                .then(losDatos => {
                    let datosFiltrados = losDatos.map((dato) => {
                        Object.keys(dato).forEach(clave => {
                            if (camposFuera.includes(clave)) {
                                delete dato[clave];
                            }
                        });
                        return dato;
                    })
                    callback({ data: datosFiltrados });
                })
                .catch(err => console.error(err));
            return { abort: function () { } };
        },
        order: [[0, 'desc']],
        columns: arregloColumnas, // Columnas ya definidas
        autoWidth: false, // Deshabilita el auto-ajuste de ancho de columna
        columnDefs: dynamicColumnDefs, // Definiciones de columna adicionales (clases, ordenamiento)
        language: españolDataTable,
        // initComplete: async function (settings, json) {
        // }
    });
    instanciasDatatable.push(dataTableInstance);
};
export function cambiarFormatos(cadena, tipo) {
    if (tipo == "fecha_hora") {

        const fechaObj = new Date(cadena);

        // Obtener los componentes de la fecha
        const dia = String(fechaObj.getDate()).padStart(2, '0');
        const mes = String(fechaObj.getMonth() + 1).padStart(2, '0'); // Los meses en JS van de 0 a 11
        const ano = fechaObj.getFullYear();

        // Pasamos la hora a formato AM/PM
        let horas = fechaObj.getHours();
        const minutos = String(fechaObj.getMinutes()).padStart(2, '0');
        const ampm = horas >= 12 ? 'PM' : 'AM'; // Determinar si es AM o PM

        // Convertimos las horas de formato de 24h a 12h
        horas = horas % 12;
        horas = horas ? horas : 12; // Si horas es 0, significa 12 AM
        const horasFormateadas = String(horas).padStart(2, '0');

        //Unimos todo
        const fechaFormateada = `${dia}-${mes}-${ano} ${horasFormateadas}:${minutos} ${ampm}`;
        cadena = fechaFormateada;
    } if (tipo == "fecha") {
        const fechaObj = new Date(cadena);
        // Obtener los componentes de la fecha
        const dia = String(fechaObj.getDate()).padStart(2, '0');
        const mes = String(fechaObj.getMonth() + 1).padStart(2, '0'); // Los meses en JS van de 0 a 11
        const ano = fechaObj.getFullYear();

        //Unimos todo
        const fechaFormateada = `${dia}-${mes}-${ano}`;
        cadena = fechaFormateada;
    }
    return cadena;
}
export function reiniciarDataTables() {
    if (instanciasDatatable.length > 0) {
        instanciasDatatable.forEach(instancia => {
            instancia.ajax.reload(null, false);
        });
    }
}
//#endregion [ LISTAR CON DATATABLE ] FIN

//#region [ ENVIAR FORMULARIOS CON AJAX ] COMIENZO
export async function enviarFormulario(instrucciones) {
    let {
        formulario,
        modulo,
        convertirJSON = false,
        camposFoto = false,
        camposFuera = false,
    } = instrucciones
    formulario = $(formulario)
    const resultado = await Swal.fire({
        title: '¿Estás seguro?',
        text: 'Quieres realizar la acción solicitada',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar'
    });
    if (resultado.isConfirmed) {
        if (formulario.hasClass('validar')) {
            let validacion = await validarTodosLosCampos(formulario[0], modulo);
            if (validacion != false) {
                return alertasAjax(validacion);
            }
        }

        const metodo = formulario.attr('method');
        const action = formulario.attr('action');
        let data = new FormData(formulario[0]);
        let cuerpoPeticion = new FormData();
        const encabezados = new Headers();
        if (convertirJSON) {
            let elementos = formulario.find('select, input')
            let datosTransformados = {};
            elementos.each((i, elemento) => {
                elemento = $(elemento);
                let name = elemento.attr('name')
                let type = elemento.attr('type')
                if (!name || elemento.attr('disabled')) {
                    return true;
                }
                let llaves = name.split('-');
                let valor = elemento.val()
                let referencia = datosTransformados;

                if (camposFuera) {
                    let coincide = false
                    camposFuera.forEach(campoF => {
                        if (name.startsWith(campoF) || name == campoF) {
                            coincide = true;
                        }
                    });
                    if (coincide) {
                        return true;
                    }
                }
                if (camposFoto) {
                    llaves.forEach(llave => {
                        if (camposFoto.includes(llave)) {
                            return true;
                        }
                    });
                }

                if (type == 'checkbox' && !elemento.is(':checked')) {
                    return true;
                }

                for (let i = 0; i < llaves.length; i++) {
                    const llave = llaves[i];
                    if (!referencia[llave]) {
                        referencia[llave] = {}
                    }
                    if (i == llaves.length - 1) {
                        referencia[llave] = valor
                    } else {
                        referencia = referencia[llave]
                    }
                }
            });
            let huboUnCampoFoto = false;
            if (camposFoto) {
                camposFoto.forEach(campoFoto => {
                    if (data.has(campoFoto)) {
                        huboUnCampoFoto = true;
                        const campoHTML = formulario.find('input[name="' + campoFoto + '"]');
                        if (
                            !campoHTML.attr('multiple') &&
                            campoHTML.val() != undefined &&
                            campoHTML.val() != '' &&
                            campoHTML.val() != [] &&
                            campoHTML.val() != null
                        ) {
                            cuerpoPeticion.append(campoFoto, campoHTML[0].files[0]);
                        } else {
                            let imagenes = campoHTML[0].files;
                            for (let i = 0; i < imagenes.length; i++) {
                                const imagen = imagenes[i];
                                cuerpoPeticion.append(campoFoto + '[]', imagen);
                            }
                        }
                    }
                });
            }
            if (huboUnCampoFoto) {
                cuerpoPeticion.append('metadatos', JSON.stringify(datosTransformados));
            } else {
                cuerpoPeticion = JSON.stringify(datosTransformados);
            }
        } else {
            if (camposFuera) {
                let nuevaData = FormData();
                for (const [clave, valor] of data.entries()) {
                    let sirve = true;
                    camposFuera.forEach(campoF => {
                        if (clave.startsWith(campoF)) {
                            sirve = false
                        }
                    });
                    if (sirve) {
                        nuevaData.append(clave, valor)
                    }
                }
                cuerpoPeticion = nuevaData
            } else {
                cuerpoPeticion = data;
            }

        }
        const config = {
            method: metodo,
            headers: encabezados,
            mode: 'cors',
            cache: 'no-cache',
            body: cuerpoPeticion
        };

        const respuesta = await fetch(rutaAbsoluta + modulo, config);
        const contentType = respuesta.headers.get('Content-Type');

        // Si es una respuesta JSON
        if (contentType.includes('application/json') || contentType.includes('text/html')) {
            const respuestaJSON = await respuesta.json();
            // Para reiniciar la SesionStorage y las listas DataTable's

            if (respuestaJSON.icono == 'success') {
                reiniciarDataModuloSS(modulo);
                if (modulo == 'monedas' || modulo == 'cambios') {
                    reiniciarDataModuloSS('monedas');
                    reiniciarDataModuloSS('cambios');
                }
                reiniciarDataTables();
            }
            respuestaJSON.formulario = formulario
            await alertasAjax(respuestaJSON);
            return respuestaJSON;
        } else if (contentType.includes('application/pdf')) {
            const pdfBlob = await respuesta.blob();
            const urlPDF = URL.createObjectURL(pdfBlob);
            window.open(urlPDF, '_blank');
        } else {
            console.error('Tipo de contenido no reconocido!!!');
        }
    }
}
export function obtenerSiguienteIndice(elementoContenedor, etiqueta, grupo, atributoEje) {
    const elementosTotales = $(elementoContenedor).find(etiqueta + '[name^="' + grupo + '["][name$="][' + atributoEje + ']"]');
    const indicesExistente = new Set();
    elementosTotales.each((indice, elemento) => {

        const nameAttr = elemento.name;
        const regexDinamica = new RegExp(`${grupo}\\[(\\d+)\\]\\[${atributoEje}\\]`);
        const match = nameAttr.match(regexDinamica);
        if (match && match[1]) {
            indicesExistente.add(parseInt(match[1], 10));
        }
    });

    let indicePropuesto = 0;
    while (indicesExistente.has(indicePropuesto)) {
        indicePropuesto++;
    }
    return indicePropuesto;
}
export async function cerrarSession() {
    let respuesta = await alertasAjax({
        'tipo': 'preguntar',
        'titulo': '¿Desea cerrar la sesión?',
        'texto': 'Si cierra la sesión, deberá iniciar sesión nuevamente con su usuario y contraseña para acceder al sistema',
    });
    if (respuesta['isConfirmed'] == true) {
        respuesta = await pedirDatosAjax({
            'modulo': 'usuarios',
            'noGuardarLocal': true,
            'datosPe': {
                'accion': 'cerrarSesion'
            }
        });
        console.log(respuesta);
        await alertasAjax(respuesta);
    }
}
//#endregion [ENVIAR FORMULARIOS CON AJAX] FIN

//#region [ ALERTAS AJAX ] COMIENZO
export async function alertasAjax(alerta) {
    let resultado = '';

    let {
        tipo,
        icono,
        titulo,
        texto,
        formulario = null,

    } = alerta

    if (alerta.notifier == true) {
        notifier.show(alerta.titulo, alerta.texto, alerta.icono, rutaAbsoluta + `/app/assets/img/${alerta.icono}Icono.png`, alerta.tiempo ?? 0);
        return;
    }
    switch (tipo) {
        case 'simple':
            resultado = await Swal.fire({
                icon: alerta.icono,
                title: alerta.titulo,
                text: alerta.texto,
                confirmButtonText: 'Aceptar'
            });
            break;
        case 'recargar':
            resultado = await Swal.fire({
                icon: icono,
                title: titulo,
                text: texto,
                confirmButtonText: 'Aceptar'
            });
            if (resultado.isConfirmed) {
                window.location.reload();
            }
            break;
        case 'limpiar':
            resultado = await Swal.fire({
                icon: icono,
                title: titulo,
                text: texto,
                confirmButtonText: 'Aceptar'
            });
            if (formulario) {
                $(formulario)[0].reset();
            }
            break;
        case 'limpiarYcerrar':
            resultado = await Swal.fire({
                icon: icono,
                title: titulo,
                text: texto,
                confirmButtonText: 'Aceptar'
            });
            if (formulario) {
                $(formulario)[0].reset();
                const botonCerrar = $(formulario).closest('.modal').find('.btn-close');
                botonCerrar.trigger('click');
            }
            break;
        case 'redireccionar':
            window.location.href = alerta.url;
            break;
        case 'alertarYredireccionar':
            resultado = await Swal.fire({
                icon: alerta.icono,
                title: alerta.titulo,
                text: alerta.texto,
                showConfirmButton: false,
                timer: 2000
            });
            setTimeout(() => {
                window.location.href = alerta.url;
            }, 2000);
            break;
        case 'preguntar':
            resultado = await Swal.fire({
                title: alerta.titulo,
                text: alerta.texto,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar'
            });
            break;
        default:
            break;
    }
    return resultado;
}
//#endregion [ALERTAS AJAX] FIN

//#region [ PARA ELIMINAR REGISTROS ] COMIENZO
export async function eliminarRegistro(instrucciones) {

    let {
        campoId,
        modulo,
        boton
    } = instrucciones

    const resultado = await Swal.fire({
        title: '¿Estás seguro?',
        text: '¿Estás seguro de eliminar el registro?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar'
    });
    if (resultado.isConfirmed) {
        const respuesta = await pedirDatosAjax({
            noGuardarLocal: true,
            modulo,
            datosPe: {
                accion: 'eliminar',
                [campoId]: $(boton).attr('value')
            }
        });

        // Para actualizar los listados
        if (respuesta.icono == 'success') {
            reiniciarDataModuloSS(modulo);
            if (instanciasDatatable.length > 0) {
                instanciasDatatable.forEach(instancia => {
                    instancia.ajax.reload(null, false);
                });
            }
        }
        return alertasAjax(respuesta);
    }
}
//#endregion [ PARA ELIMINAR REGISTROS ] FIN

//#region [ PARA OBTENER DATOS A ACTUALIZAR ] COMIENZO
export async function obtenerDatosRegistro(instrucciones) {
    let {
        boton,
        campoId,
        modulo,
    } = instrucciones

    const claseModalObj = $(boton).attr('data-bs-target');
    const claseFormulario = $(boton).attr('claseFormulario') ?? '.formularioAjax';
    let formulario = $(claseModalObj).find(claseFormulario);
    const idRegistro = $(boton).attr('value');
    const respuesta = await pedirDatosAjax({
        modulo,
        campoId,
        datosPe: {
            accion: 'seleccionarUno',
            [campoId]: [idRegistro],
        },
        funcionBusqueda: function (registros) {
            return registros.find(registro => registro[campoId] == idRegistro)
        }
    });
    const datosNoAgrupados = respuesta.datosNoAgrupados ?? respuesta;

    const inputs = formulario.find('select,input');
    inputs.each((indice, input) => {
        console.log('name: ', input.name)
        console.log('datos: ', datosNoAgrupados)
        const nombreCampo = input.name;
        if (Object.prototype.hasOwnProperty.call(datosNoAgrupados, nombreCampo)) {
            input.value = datosNoAgrupados[nombreCampo]; // Le Asignamos el valor al input
        }
    });

    const datosInhab = inputs.filter('.inha');
    datosInhab.each((indice, input) => {
        $(input).prop('disabled', true);
    });
    return respuesta;
}
//#endregion [ PARA OBTENER DATOS A ACTUALIZAR ] FIN

//#region [ PARA HACER PETICIONES AJAX ] COMIENZO
export async function pedirDatosAjax(instrucciones) {
    const caching = false;
    const modulosFuera = ['ventas', 'bitacora', 'notificaciones'];
    let {
        datosPe,
        url = false,
        metodo = 'POST',
        modulo = false,
        JSONstring = false,
        noJSON = false,
        noGuardarLocal = false,
        campoId,
        funcionBusqueda = false
    } = instrucciones;
    let { accion } = datosPe;

    let rutaGuardado = [modulo, accion];
    if (datosPe[campoId]) {
        rutaGuardado.push(campoId, datosPe[campoId])
    }
    let datosCacheados = sessionStorage.getItem('cachingModulos')
    datosCacheados = datosCacheados ? JSON.parse(datosCacheados) : {}

    let buscarDatos = true;
    let datosLocales = false;

    //Para ahorrar espacio y garantizar la optimización de la memoria
    if (funcionBusqueda && datosCacheados != {} && caching) {
        let datos = datosCacheados[modulo] ? (datosCacheados[modulo]['listar'] ?? false) : false
        if (datos) {
            datos = funcionBusqueda(datos);
            if (datos != [] && datos != '' && datos != undefined && datos != null) {
                buscarDatos = false;
                datosLocales = datos;
            }
        }
    }
    //Buscamos directo a la ruta si en los datos generales no estaba
    if (buscarDatos && datosCacheados != {} && caching) {
        let referencia = datosCacheados;
        for (let j = 0; j < rutaGuardado.length; j++) {
            let clave = rutaGuardado[j];
            if (!referencia[clave]) {
                break;
            }
            if (
                j == rutaGuardado.length - 1 &&
                referencia[clave] != '' &&
                referencia[clave] != [] &&
                referencia[clave] != {}
            ) {
                datosLocales = referencia[clave];
                buscarDatos = false;
            }
        }
    }

    let respuesta = '';
    if (buscarDatos) {
        const headers = new Headers(); let formData = new FormData();
        if (JSONstring) {
            formData = JSON.stringify(datosPe);
        } else {
            for (const [clave, valor] of Object.entries(datosPe)) {
                formData.append(clave, valor);
            }
        }

        let endpoint = modulo ? `${rutaAbsoluta}` + modulo : url
        respuesta = await fetch(endpoint, {
            method: metodo,
            headers,
            mode: 'cors',
            body: formData
        });

        if (!noJSON) {
            respuesta = await respuesta.json();
        }
        if (
            !url && !noGuardarLocal &&
            !modulosFuera.includes(modulo) && caching
        ) {
            let referencia = datosCacheados;
            for (let i = 0; i < rutaGuardado.length; i++) {
                const clave = rutaGuardado[i];
                if (!referencia[clave]) {
                    referencia[clave] = {}
                }
                if (rutaGuardado.length == i + 1) {
                    referencia[clave] = respuesta
                } else {
                    referencia = referencia[clave];
                }
            }
            try {
                sessionStorage.setItem('cachingModulos', JSON.stringify(datosCacheados));
            } catch (error) {
                console.error('Ocurrió un error: ', error)
            }
        }
    } else {
        respuesta = datosLocales;
    }

    return respuesta;
}
export function reiniciarDataModuloSS(modulo) {
    let caching = sessionStorage.getItem('cachingModulos') ?? false;
    if (caching) {
        caching = JSON.parse(caching);
        let moduloBorrar = caching[modulo] ?? false;
        if (moduloBorrar) {
            delete caching[modulo];
            sessionStorage.setItem('cachingModulos', JSON.stringify(caching));
        }
    }
}
//#endregion [ PARA HACER PETICIONES AJAX ] FIN

//#region [ PARA EXTRAER DATOS DE LA DB E INSERTARLOS EN ELEMENTOS HTML ] COMIENZO
export async function extraerDatosAjax(instrucciones) {
    variableDeError = '';
    const {
        modulosPeticion: modulos,
        accionesPeticion: acciones,
        tipoElemento: tipos,
        elementosDestino: destinos,
        datosInsertar: datosInsercion,
        funcionBusqueda = []
    } = instrucciones;

    for (let i = 0; i < modulos.length; i++) {
        const modulo = modulos[i];
        const tipoElemento = tipos[i];
        let elementoDestino = $(destinos[i]);
        let funcionBusquedaInd = funcionBusqueda[i] ?? false
        if (Array.isArray(destinos[i]) || destinos[i].length > 1) {
            let elementosDom = $.map(destinos[i], function (objJq) {
                objJq = $(objJq);
                return objJq.toArray();
            });
            elementoDestino = $(elementosDom);
        }
        const datosInsertar = datosInsercion[i];
        const accion = acciones[i];

        const instruccionesPe = {
            modulo,
            datosPe: {},
            funcionBusqueda: funcionBusquedaInd
        };
        for (const [clave, valor] of Object.entries(accion)) {
            instruccionesPe.datosPe[clave] = valor;
        }
        let datosRecibidos = await pedirDatosAjax(instruccionesPe);
        if (datosRecibidos.tipo) {
            if (tipoElemento == 'select') {
                elementoDestino.empty();
                elementoDestino.append('<option value="">Sin Registros</option>');
                continue;
            } else {
                variableDeError = { error: 'sin registros' };
                continue;
            }
        } else {
            variableDeError = { exito: 'Con registros' };
        }
        if (tipoElemento == 'select') {
            // #region LÓGICA PARA QUE NO SE SELECCIONE DOS VECES EL MISMO ITEM
            let elementoObtClases = elementoDestino[0][0] ?? elementoDestino[0];
            if ($(elementoObtClases).is('option')) {
                elementoObtClases = elementoDestino[0]
            }
            elementoObtClases = $(elementoObtClases);

            if (elementoDestino.length > 1) {
                elementoObtClases = $(elementoDestino[0]);
            }
            const clasesDelSelect = elementoObtClases.attr('class');
            if (!clasesDelSelect) {
                return;
            }
            const arregloDeClases = clasesDelSelect.split(' ');
            const clasesForma = clasesDelSelect.replace(/\s/g, '.');

            // para obtener todos los id's seleccionados hasta el momento
            let registrosSeleccionados = [];
            if (arregloDeClases.includes('OQNPR')) {
                let selectsTotales;
                if (Array.isArray(elementoDestino)) {
                    selectsTotales = elementoDestino[0].closest('.contenedorDetalles').find('.' + clasesForma);
                } else {
                    selectsTotales = elementoDestino.closest('.contenedorDetalles').find('.' + clasesForma);
                }

                registrosSeleccionados = selectsTotales.map(function () {
                    if ($(this).val() != '') {
                        return $(this).val();
                    }
                    return false;
                }).get(); // el .get() transforma el arrays de jquery a DOM
            }
            // #endregion LÓGICA PARA QUE NO SE SELECCIONE DOS VECES EL MISMO ITEN

            let funcionRenderizarOpciones = (elemento, datosInsertar, registrosDB) => {

                elemento.empty();
                if (datosInsertar.textoDefault) {
                    elemento.append(`<option value="">${datosInsertar.textoDefault}</option>`);
                }
                registrosDB.forEach(registroBD => {
                    const idRegistroActual = String(registroBD[datosInsertar.value]);
                    if (!registrosSeleccionados.includes(idRegistroActual)) {
                        let textoOpcion = '';
                        if (Array.isArray(datosInsertar.texto)) {
                            for (let j = 0; j < datosInsertar.texto.length; j++) {
                                if (i == 0) {
                                    textoOpcion += registroBD[datosInsertar.texto[j]];
                                } else {
                                    textoOpcion += ' ' + registroBD[datosInsertar.texto[j]];
                                }
                            }
                        } else {
                            textoOpcion = registroBD[datosInsertar.texto];
                        }
                        elemento.append(
                            $('<option>', {
                                value: registroBD[datosInsertar.value],
                                text: textoOpcion,
                                selected: registroBD[datosInsertar.value] == datosInsertar.opcionSeleccionada
                            })
                        );
                    }
                });
            }
            if (Array.isArray(elementoDestino)) {
                array.forEach(elementoInd => {
                    funcionRenderizarOpciones(elementoInd, datosInsertar, datosRecibidos)
                });
            } else {
                funcionRenderizarOpciones(elementoDestino, datosInsertar, datosRecibidos)
            }

            // Para validar si quedan o no opciones para mostrar
            let totalOptions = '';
            if (Array.isArray(elementoDestino)) {
                totalOptions = elementoDestino[0].find('option').length;
            } else {
                totalOptions = elementoDestino.find('option').length;
            }
            if (totalOptions == 1) {
                if (Array.isArray(elementoDestino)) {
                    elementoDestino.forEach(elemento => {
                        elemento.empty();
                        elemento.append('<option class="texto-rojo">Sin más opciones</option>');
                    });
                } else {
                    elementoDestino.empty();
                    elementoDestino.append('<option class="texto-rojo">Sin más opciones</option>');
                }
            }
        } else if (tipoElemento == 'input') {
            if (Array.isArray(datosInsertar)) {
                for (let k = 0; k < elementoDestino.length; k++) {
                    let elemento = $(elementoDestino[k]);
                    elemento.val(datosRecibidos[datosInsertar[k]]);
                    elemento.removeClass('error');
                    elemento.closest('.form-group').find('.error-message').remove();
                }
            } else {
                elementoDestino.val(datosRecibidos[datosInsertar]);
                elementoDestino.removeClass('error');
                elementoDestino.closest('.form-group').find('.error-message').remove();
            }
        }
    };
}
//#endregion [ PARA EXTRAER DATOS DE LA DB E INSERTARLOS EN ELEMENTOS HTML ] FIN

//#region [ DINAMISMO DEL HTML ] COMIENZO

function cambiarEstadoLiSidebar() {
    if ($(this).hasClass('activa')) {
        return;
    } else {
        $(this).addClass('activa');
        $(this).closest('.sidebar-menu').find('li').not($(this)).removeClass('activa')
    }

    if (!$(this).hasClass('subMenuSidebar')) {
        let textoOpcionSeleccionada = $(this).find('span').text();
        sessionStorage.setItem('moduloSeleccionadoSidebar', textoOpcionSeleccionada);
    }
}
function cargarModuloSeleccionaSidebar() {
    let opcionSeleccionada = sessionStorage.getItem('moduloSeleccionadoSidebar');
    if (opcionSeleccionada != 'null' && opcionSeleccionada != null) {
        let opcionesSidebar = $('.sidebar-menu').find('li').find('span');
        opcionesSidebar.each((indice, elemento) => {
            if ($(elemento).text() == opcionSeleccionada) {
                $(elemento).closest('li').addClass('activa')
                let subMenuPadre = $(elemento).closest('.bloqueSubMenu')
                if (subMenuPadre.length > 0) {
                    subMenuPadre.addClass('show');
                    let liDeBloqueSM = $('[data-bs-target="#' + subMenuPadre.attr('id') + '"]');
                    liDeBloqueSM.addClass('activa').removeClass('collapsed').attr('aria-expanded', true)
                }
            }
        })
    }
}
function initNotificaciones() {
    $('.headerPrincipal').find('.custom-dropdown').on('show.bs.dropdown', function () {
        let that = $(this);
        setTimeout(function () {
            that.find('.dropdown-menu').addClass('active');
        }, 100);
    });
    $('.custom-dropdown').on('hide.bs.dropdown', function () {
        $(this).find('.dropdown-menu').removeClass('active');
    });
}
//#endregion [ DINAMISMO DEL HTML ] FIN

//#region [ DELEGACIÓN DE EVENTOS ] COMIENZO

//Evento para la precarga datos y eventos
$(document).on('DOMContentLoaded', async function (e) {
    const sidebar = document.querySelector('#sidebar');
    const sidebarToggle = document.querySelector('#sidebarToggle');
    if (sidebar && sidebarToggle) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('active');
        });
    }

    $(document).off('click', '.sidebar-menu li')
    $(document).on('click', '.sidebar-menu li', function (e) {
        // e.preventDefault();
        cambiarEstadoLiSidebar.call(this);
    })

    cargarModuloSeleccionaSidebar();
    initNotificaciones();

    extraerDatosAjax({
        modulosPeticion: ['roles'],
        accionesPeticion: [{ accion: 'listar' }],
        tipoElemento: ['select'],
        elementosDestino: [$('.selectRoles')],
        datosInsertar: [{
            texto: 'nombre_rol',
            value: 'id_rol',
            textoDefault: 'Seleccione una opción'
        }]
    });
});

//Cerrar sesión
$(document).off('click', '.btnCerrarSession')
$(document).on('click', '.btnCerrarSession', function () {
    cerrarSession();
})

//Formulario del perfil
$(document).off('submit', '.formularioPerfil')
$(document).on('submit', '.formularioPerfil', function (e) {
    e.preventDefault();
    enviarFormulario({
        'formulario': this,
        'modulo': 'usuarios'
    });
})

//Plasmar los datos en el formulario de los usuarios
$(document).off('click', '.btnEditarPerfil')
$(document).on('click', '.btnEditarPerfil', function () {
    obtenerDatosRegistro({
        boton: this,
        campoId: 'cedula_usuario',
        modulo: 'usuarios',
    });
})
//#endregion [ DELEGACIÓN DE EVENTOS ] FIN