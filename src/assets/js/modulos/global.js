//#region [VARIABLES O CONSTANTES GLOBALES] COMIENZO
export const rutaAbsoluta = window.location.origin + "/proyecto-lacruz-j/";
export const rutaFotos = rutaAbsoluta + "src/assets/fotosModulos/";
export let vista = $('.nombreVista').val();
export let instanciasDatatable = [];
export let variableDeError = '';
export let inputsActualizarNoRepetir = {};
export const tokenCSRF = $('meta[name="TOKEN_CSRF"]').attr('content');
export const encabezadosPeticiones = new Headers();
encabezadosPeticiones.append('X-TOKEN-CSRF', tokenCSRF);
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
export function funcionAlertaError(input, texto) {
  if ($(input).closest('form').hasClass('login')) {
    return `
        <div class="mensajeError d-flex alert alert-danger alert-dismissible fade show mt-3">
            <i class="fi fi-rr-triangle-warning me-2"></i>
            ${texto}
        </div>
      `;
  } else {
    return `<div class="mensajeError text-danger small mt-1">${texto}</div>`;
  }
};
export function funcionMandarError(input, mensaje) {
  input = $(input);
  let mensajeHTML = funcionAlertaError(input, mensaje);
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
export function funcionEliminaError(input) {
  input = $(input);
  input.addClass('validado').removeClass('error');
  let contenedorGI = input.closest('.form-group').length > 0 ? input.closest('.form-group') : input.closest('[class^="col-"]');
  contenedorGI.find('.mensajeError').remove();
}
export function reiniciarCampo(input) {
  input = $(input);
  input.removeClass('error validado');
  let contenedorGI = input.closest('.form-group').length > 0 ? input.closest('.form-group') : input.closest('[class^="col-"]');
  contenedorGI.find('.mensajeError').remove();
}
export async function validarEnTiempoReal(input, modulo) {

  input = $(input);
  if (input.is(':disabled')) return;
  let nameImput = input.attr('name')
  let valorIntroducido = input.val();
  let minimo = input.attr('minlength') || false;
  let maximo = input.attr('maxlength') || false;
  let expresionRegular = RegExp(input.attr('pattern')) || false;
  let requerido = input.attr('required') || false;
  let esValido = expresionRegular.test(valorIntroducido);

  //Validar si es requerido
  if (requerido && valorIntroducido == '') {
    funcionMandarError(input, 'Este campo es obligatorio!!!');
    return;
  } else {
    funcionEliminaError(input);
  }

  if ((!requerido && valorIntroducido == '') || input.attr('readonly')) {
    input.removeClass('validado error')
    return;
  }

  //Para validar el minimo del campo
  if (minimo && valorIntroducido.length < minimo) {
    funcionMandarError(input, `El valor del campo debe ser mayor o igual a ${minimo} caracteres`)
    return;
  } else {
    funcionEliminaError(input);
  }

  //Para validar el maximo del campo
  if (maximo && valorIntroducido.length > maximo) {
    funcionMandarError(input, `El valor del campo debe ser menor o igual a ${maximo} caracteres`)
    return;
  } else {
    funcionEliminaError(input)
  }

  //Para validar la contrasena de confirmación
  if (input.attr('id') == 'contrasena2_usuario') {
    if ($('#contrasena1_usuario').val() != $('#contrasena2_usuario').val()) {
      funcionMandarError(input, 'El valor de ambas contraseña debe coincidir');
      return;
    } else {
      funcionEliminaError(input);
    }
  }

  //Para validar el formato del campo
  if (!esValido) {
    funcionMandarError(input, 'El valor del campo no es valido');
    return;
  } else {
    funcionEliminaError(input);
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
      funcionMandarError(input, 'El dato ingresado ya se encuentra registrado')
    } else {
      funcionEliminaError(input);
    }
  }
}
export async function validarTodosLosCampos(formulario, modulo) {

  let elementosForm = $(formulario).find('input, select, textarea');
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
export function mLength(objeto) {
  if (objeto) {
    if (objeto instanceof FormData) {
      return [...objeto.keys()].length;
    } else {
      return Object.keys(objeto).length
    }
  } else {
    return 0;
  }
}
export function formateoCampos(elemento, formato) {
  switch (formato) {
    case 'referencia':
      $(elemento).mask('000000', {
        reverse: true,
      });
      break;
    case 'dinero':
      $(elemento).mask('#.##0,00', {
        reverse: true,
      });
      break;
    case 'dineroSL':
      $(elemento).each(function () {
        let el = $(this);
        let esInput = el.is('input, textarea');
        let valorActual = esInput ? el.val() : el.text();
        if (!esInput && valorActual.toString().includes(',')) {
          valorActual = valorActual.toString().replace(/\./g, '').replace(',', '.');
        }
        let mascara = valorActual.toString().indexOf('-') > -1 ? 'S#.##0,00' : '#.##0,00';
        let opciones = {
          reverse: true,
          translation: { 'S': { pattern: /-/, optional: true } },
          onKeyPress: function (val, e, field, options) {
            const msk = val.indexOf('-') > -1 ? 'S#.##0,00' : '#.##0,00';
            if (options.mask !== msk) $(field).mask(msk, options);
          }
        };
        if (esInput) {
          el.unmask().mask(mascara, opciones);
        } else {
          let num = parseFloat(valorActual);
          if (!isNaN(num)) {
            let prefijo = num < 0 ? '-' : '';
            let absoluto = Math.abs(num).toFixed(2);
            let partes = absoluto.split('.');
            partes[0] = partes[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            el.text(prefijo + partes.join(','));
          }
        }
      });
      break;
    default:
      break;
  }
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
          </li>
        `;
      }
      if (permisos[modulo].includes('eliminar')) {
        boton += `
          <li value="${id}" class="botonEliminar list-inline-item align-bottom" data-bs-toggle="tooltip" title="Eliminar">
              <a href="#" class="avtar avtar-xs btn-link-danger btn-pc-default">
              <i class="fi fi-rs-trash fs-3 iconoCentrado"></i>
              </a>
          </li>
        `;
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
      dynamicColumnDefs.push({ targets: [targetsCount], className: 'dt-center alineado_vertical' });
      targetsCount++;
    }
  });

  if (arregloColumnas.length === 0) {
    arregloColumnas.push({ data: null, title: 'No hay datos disponibles' });
    dynamicColumnDefs.push({ targets: [0], className: 'tabla dt-center' });
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
    dynamicColumnDefs.push({ orderable: false, className: 'acciones dt-center alineado_vertical', targets: [targetsCount] });
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

export async function convertirHTMLJSON(instrucciones) {

  let {
    elemento,
    camposFuera = null,
    camposFoto = null
  } = instrucciones
  elemento = $(elemento);

  let cuerpoPeticion = new FormData();
  let elementos = elemento.find('select, input, textarea')
  let datosTransformados = {};

  elementos.each((i, elemento) => {
    elemento = $(elemento);
    let name = elemento.attr('name')
    let type = elemento.attr('type')

    let llaves = name.split('-');
    let valor = elemento.val()
    let referencia = datosTransformados;

    //Depurar campos 
    if (!name || elemento.attr('disabled')) {
      return true;
    }
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
    if (type == 'checkbox' && !elemento.is(':checked')) {
      return true;
    }

    //Campos de tipo FILE
    if (camposFoto) {
      let esUnCampoFoto = false;
      llaves.forEach(llave => {
        if (camposFoto.includes(llave)) {
          esUnCampoFoto = true;
        }
      });
      if (esUnCampoFoto) return true;
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
      if (elemento.find(`input[name="${campoFoto}"]`).length > 0) {
        huboUnCampoFoto = true;
        const campoHTML = elemento.find('input[name="' + campoFoto + '"]');
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
    if (mLength(datosTransformados) > 0) {
      cuerpoPeticion.append('metadatos', JSON.stringify(datosTransformados));
    }
  } else {
    cuerpoPeticion = JSON.stringify(datosTransformados);
  }
  return cuerpoPeticion;

}
export async function enviarFormulario(instrucciones) {
  let {

  } = instrucciones;
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

    if (convertirJSON) {
      let elementos = formulario.find('select, input, textarea')
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
        let nuevaData = new FormData();
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
      headers: encabezadosPeticiones,
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
export function obtenerSiguienteIndice(elementoContenedor, etiqueta, name) {
  const elementosTotales = $(elementoContenedor).find(etiqueta + `[name^="${name}"`);
  let existentes = {};
  elementosTotales.each((i, elemento) => {
    let name = elemento.name;
    let arrayName = name.split('-');
    if (!existentes[arrayName[1]]) {
      existentes[arrayName[1]] = true;
    }
  });
  let i = 0;
  while (existentes[i]) {
    i++;
  }
  return i;
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
    notifier.show(alerta.titulo, alerta.texto, alerta.icono, rutaAbsoluta + `/src/assets/images/${alerta.icono}Icono.png`, alerta.tiempo ?? 0);
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
        $(formulario).find('input,select,textarea').removeClass('error').each((i, elemento) => {
          reiniciarCampo(elemento);
        });
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
        $(formulario).find('input,select,textarea').removeClass('error').each((i, elemento) => {
          reiniciarCampo(elemento);
        });
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
    let resultadoAlerta= await alertasAjax(respuesta);
    return {
      resultadoAlerta,
      respuestaBack:respuesta
    }
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

  const inputs = formulario.find('select,input,textarea');
  inputs.each((indice, input) => {
    let tipoCampo = $(input).attr('type');
    const nombreCampo = input.name;
    if (tipoCampo == 'checkbox') {
      $(input).prop('checked', (datosNoAgrupados[nombreCampo] == 1 || datosNoAgrupados[nombreCampo] == true));
    } else if(tipoCampo!='file') {
      if (Object.prototype.hasOwnProperty.call(datosNoAgrupados, nombreCampo)) {
        input.value = datosNoAgrupados[nombreCampo]; // Le Asignamos el valor al input
      }
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
    let formData = new FormData;
    if (JSONstring) {
      formData = JSON.stringify(datosPe);
    } else {
      for (const [clave, valor] of Object.entries(datosPe)) {
        formData.append(clave, valor);
      }
    }

    respuesta = await fetch(rutaAbsoluta + modulo, {
      method: metodo,
      headers: encabezadosPeticiones,
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

// #region [WEBSOCKETS] COMIENZO  

// #region [ CONFIGURACIONES Y FUNCIONES GENERALES ] COMIENZO  
export let socket;
export async function iniciarServidorWS() {
  try {
    let datosUsuario = await obtenerUsuarioWS();
    socket = io(
      'https://api-the-vina-node.onrender.com/',
      // 'http://localhost:1234/',
      {
        reconnection: false,
        auth: {
          datosUsuario: datosUsuario
        }
      }
    )

    let intervaloPollingSinc = false;
    let intervaloReconexion = false;
    let nroIntentosMax = 10;
    let intentos = 0;
    let activarPollings = () => {
      if (!intervaloPollingSinc) {
        intervaloPollingSinc = setInterval(() => {
          procesarAccionesResagadas()
        }, 1000 * 10 * 1)
      }
      if (!intervaloReconexion) {
        intervaloReconexion = setInterval(() => {
          if (intentos < nroIntentosMax) {
            try {
              socket.connect();
            } catch (error) {

            }
            intentos++;
          }
        }, 1000 * 30 * 1)
      }
    }
    let desactivarPollings = () => {
      if (intervaloPollingSinc) {
        clearInterval(intervaloPollingSinc);
        intervaloPollingSinc = false;
      }
      if (intervaloReconexion) {
        clearInterval(intervaloReconexion);
        intervaloReconexion = false;
      }
    }

    socket.on('mensajeServidor', (msj) => {
      procesarMensajeWS(msj);
    });
    socket.on('connect', () => {
      intentos = 0;
      desactivarPollings();
    });
    socket.on('disconnect', () => {
      activarPollings();
    });
    socket.on('connect_error', (error) => {
      activarPollings();
    });
  } catch (error) {
  }
}
export async function procesarMensajeWS(instruccionesMsj) {
  if (typeof instruccionesMsj == 'string') {
    instruccionesMsj = JSON.parse(instruccionesMsj);
  }

  let EARBD = async (msj) => { //Eliminar Accion Resagada en la BD
    await pedirDatosAjax({
      'noGuardarLocal': true,
      'JSONstring': true,
      'modulo': 'mensajesWS',
      'datosPe': {
        'accion': "eliminarAccionResagada",
        'AccionMsj': msj
      }
    })
  }
  let procesarMsj = async (msj) => {

    let {
      accion,
      modulo = false,
      alerta = false,
    } = msj

    switch (accion) {
      case "actDT":
        if (modulo == modulo) {
          reiniciarDataTables();
          await EARBD(msj);
        }
        break;
      case "actPrecioDolar":
        if (!msj.precioDolar) {
          cargarPrecioDolar();
        } else {
          $('.tipoDeDolarPrecio').empty();
          $('.tipoDeDolarPrecio').append(`<a href="https://www.bcv.org.ve/">Precio del BCV</a>`);
          $('.contenedorPrecioDolar').find('.precio_dolar').text(parseFloat(msj.precioDolar).toFixed(2));
          let infoDolarWS = {
            'precio': msj.precioDolar,
            'tipoPrecio': 'Precio del BCV'
          }
          sessionStorage.setItem('infoDolarWS', JSON.stringify(infoDolarWS));
        }
        await EARBD(msj)
        break;
      case "alertar":
        alertasAjax(alerta);
        listarNotificaciones()
        break;
      case "borrarDataModuloSS":
        let caching = sessionStorage.getItem('cachingModulos') ?? false;
        if (caching) {
          caching = JSON.parse(caching);
          let moduloBorrar = caching[modulo] ?? false;
          if (moduloBorrar) {
            delete caching[modulo];
            sessionStorage.setItem('cachingModulos', JSON.stringify(caching))
          }
        }
        await EARBD(msj)
        break;
      default:
        console.error('Acción no reconocida');
        break;
    }
  }

  if (Array.isArray(instruccionesMsj)) {
    instruccionesMsj.forEach(msjInd => {
      procesarMsj(msjInd);
    });
  } else {
    procesarMsj(instruccionesMsj);
  }
}
export const obtenerUsuarioWS = async () => {
  let header = $('header');
  let cedula = header.find('.cedulaIS').attr('id_registro');
  let rol = header.find('.rolIS').text();
  let ws = {
    emisor: {
      cedula,
      rol
    }
  };
  return ws;
}
// #endregion [ CONFIGURACIONES Y FUNCIONES GENERALES ] FIN  

// #region [NOTIFICACIONES DEL SISTEMA] COMIENZO
export async function listarNotificaciones() {
  const notificaciones = await pedirDatosAjax({
    modulo: 'mensajesWS',
    datosPe: {
      accion: 'listarNotificaciones'
    }
  });
  let notificacionHTML = '';
  let notificacionesNoLeidas = 0;

  if (!notificaciones.icono) {
    $('.btnETLN').show()
    $('.btnMTLNCL').addClass('link-primary').removeClass('text-muted').attr('href', '#');
    notificaciones.forEach(notificacion => {

      let {
        tipo_notificacion,
        fecha_creacion_notificacion,
        status,
        texto_notificacion,
        titulo_notificacion,
        icono_notificacion,
      } = notificacion

      let fecha = cambiarFormatos(fecha_creacion_notificacion, 'fecha_hora');
      fecha = fecha.split(' ');
      const dia = fecha[0];
      const hora = fecha[1] + ' ' + fecha[2];

      let bgNotificacion = '';
      switch (tipo_notificacion) {
        case 'success':
          bgNotificacion = 'bg-light-success';
          break;
        case 'error':
          bgNotificacion = 'bg-light-danger';
          break;
        default:
          break;
      }
      let bgNoLeida = '';
      if (status == 1) {
        notificacionesNoLeidas++;
        bgNoLeida = 'style="background-color: #4d88ff45;"';
      }

      notificacionHTML += `
        <li ${bgNoLeida}>
            <a href="#" class="d-flex align-items-center">
                <div class="img me-3 ${bgNotificacion}">
                    <img src="http://localhost/proyecto-lacruz-j/src/assets/images/${icono_notificacion}Icono.png" alt="Image" class="img-fluid">
                </div>
                <div class="text w-100">
                    <span class="float-end text-muted">${hora}</span>
                <strong>${titulo_notificacion}</strong>
                <p class="p-0 m-0">${texto_notificacion}</p>
                <p class="p-0 m-0">${dia}</p>
                </div>
            </a>
        </li>
      `;
    });
    $('.contenedorNotificaciones').empty().append(notificacionHTML);
    if (notificacionesNoLeidas == 0) {
      $('.nroNotNoLeidas').hide();
      $('.btnMTLNCL').hide()
    } else {
      $('.nroNotNoLeidas').show().text(notificacionesNoLeidas);
      $('.btnMTLNCL').show()
    }
  } else {
    $('.btnETLN').hide()
    $('.btnMTLNCL').removeClass('link-primary').addClass('text-muted').removeAttr('href');
    $('.nroNotNoLeidas').hide();
    $('.contenedorNotificaciones').empty().append(`<h3 class="title text-center ms-1 fs-5">SIN NOTIFICACIONES</h3>`);
  }
}
export async function marcarNotificacionesComoLeidas() {
  let resultado = await Swal.fire({
    title: '¿Estás seguro?',
    text: '¿Seguro de marcar todas las notificaciones como leídas?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Aceptar',
    cancelButtonText: 'Cancelar'
  });

  if (resultado.isConfirmed) {
    resultado = await pedirDatosAjax({
      noGuardarLocal: true,
      modulo: 'mensajesWS',
      datosPe: {
        accion: 'marcarTodasNotComoLeidas'
      }
    });
    if (resultado.icono == 'success') {
      listarNotificaciones();
      alertasAjax(resultado);
    } else {
      alertasAjax(resultado);
    }
  }
}
export async function vaciarBuzonNotificaciones() {
  let resultado = await Swal.fire({
    title: '¿Estás seguro?',
    text: '¿Seguro que desea eliminar todas las notificaciones?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Aceptar',
    cancelButtonText: 'Cancelar'
  });
  if (resultado.isConfirmed) {
    resultado = await pedirDatosAjax({
      noGuardarLocal: true,
      modulo: 'mensajesWS',
      datosPe: {
        accion: 'eliminarTodasNot'
      }
    });
    listarNotificaciones();
    alertasAjax(resultado);
  }
}
// #endregion [NOTIFICACIONES DEL SISTEMA] FIN

// #region [ACCIONES RESAGADAS] COMIENZO
export async function procesarAccionesResagadas() {
  let acciones = await pedirDatosAjax({
    'noGuardarLocal': true,
    'modulo': 'mensajesWS',
    'datosPe': {
      'accion': 'listarAccionesResagadas'
    }
  });
  const accionesMapeadas = acciones.map(
    (
      {
        nombre_accion: accion,
        nombre_modulo: modulo
      }
    ) => (
      {
        accion,
        modulo
      }
    )
  );
  procesarMensajeWS(accionesMapeadas)
}
// #endregion [ACCIONES RESAGADAS] FIN

// #endregion [WEBSOCKETS] FIN

// #region [E-COMMERCE] COMIENZO
export function listarItemPanelCarritoPedido() {
  let itemCarrito = sessionStorage.getItem('itemsCarritoPedido');
  $('.depositoDetallesPedido').empty()
  if (itemCarrito) {
    itemCarrito = JSON.parse(itemCarrito);
    let { productos, servicios } = itemCarrito;
    for (const info of Object.values(productos)) {

      let contenedorDetalles = $('.panelCotizacionPedido').find('.depositoDetallesPedido')
      let molde = $('.panelCotizacionPedido').find('.moldeItemPedido').clone();

      let productoBD = {
        'id_producto': 1,
        'id_presentacion': 1,
        'nombre_producto': 'CLORO',
        'precio_bcv_producto': 36.5,
        'precio_divisas_producto': 1,
        'nombre_presentacion': '1 Litro',
        'foto_producto': 'producto_default.png',
      }
      let presentacionesBD = [
        {
          'id_presentacion': '1',
          'nombre_presentacion': 'Por Litro',
          'cantidad_pmp': 1
        },
        {
          'id_presentacion': '2',
          'nombre_presentacion': 'Bidon',
          'cantidad_pmp': 20
        },
        {
          'id_presentacion': '3',
          'nombre_presentacion': 'Por Pipa',
          'cantidad_pmp': 200
        },
      ]
      let {
        nombre_producto,
        precio_bcv_producto,
        precio_divisas_producto,
        foto_producto
      } = productoBD
      let presentacionBD = presentacionesBD.find((P => P.id_presentacion == productoBD.id_presentacion));
      let { id_presentacion, nombre_presentacion, cantidad_pmp } = presentacionBD

      let dirFotos = rutaAbsoluta + 'src/assets/images/';

      molde.data({
        'id': info.id,
        'id_presentacion': id_presentacion,
        'tipo_item': 'productos',
        'precio_bcv': precio_bcv_producto,
        'precio_divisas': precio_divisas_producto,
        'cantidad_pmp': cantidad_pmp,
      })

      molde.find('.nombreItem').text(nombre_producto + ' - ' + nombre_presentacion);
      molde.find('.imagenItemPedido').attr('src', dirFotos + foto_producto);
      molde.find('.cantidadItemCarrito').text(info.cantidad)

      molde.removeClass('d-none moldeItemPedido').addClass('itemPedido');
      contenedorDetalles.append(molde);
    }
  }
  recalcularTotalesPedido();
}
function sumarRestarItemCarritoPedido() {

  let detalle = $(this).closest('.itemPedido');

  let { id, tipo_item, id_presentacion } = detalle.data();
  let items = sessionStorage.getItem('itemsCarritoPedido');
  items = JSON.parse(items);
  const cantidadItem = $(this).siblings(".cantidadItemCarrito");
  let cantidadActual = parseInt(cantidadItem.text() || 0);

  if ($(this).hasClass('btnSumItemPedido')) {
    cantidadActual++;
  } else if ($(this).hasClass('btnResItemPedido') && cantidadActual > 1) {
    cantidadActual--;
  }

  let objeto = {
    id: parseInt(id),
    id_presentacion: parseInt(id_presentacion)
  }
  let clave = JSON.stringify(objeto);

  cantidadItem.text(cantidadActual);
  items[tipo_item][clave].cantidad = cantidadActual;

  items = JSON.stringify(items);
  sessionStorage.setItem('itemsCarritoPedido', items);
  recalcularTotalesPedido();
}
function recalcularTotalesPedido() {

  let totalBase = 0;
  let totalDescuento = 0;
  let tipoMoneda = $('.panelCotizacionPedido').find('.btnTipoPago.active').data('tipo_pago');

  let signoMoneda = '';
  if (tipoMoneda == 'bs') {
    signoMoneda = ' Bs';
  } else {
    signoMoneda = '$'
  }
  $('.panelCotizacionPedido').find('.signoPrecio').text(signoMoneda);
  $('#modalPagoUbicacion').find('.signoPrecio').text(signoMoneda);
  $('#modalPagoDetalles').find('.signoPrecio').text(signoMoneda);

  let totalItems = 0;

  $('.depositoDetallesPedido').find(".itemPedido").each(function () {

    const detalle = $(this);
    let {
      id,
      precio_bcv,
      precio_divisas,
      cantidad_pmp,
      id_presentacion
    } = detalle.data();

    let llave = JSON.stringify({
      id: parseInt(id),
      id_presentacion: parseInt(id_presentacion)
    })
    let items = JSON.parse(sessionStorage.getItem('itemsCarritoPedido'));
    let cantidad = parseInt(items['productos'][llave]['cantidad']);
    totalItems += cantidad;

    let precioBase = 0;
    if (tipoMoneda == 'bs') {
      precioBase = precio_bcv;
    } else {
      precioBase = precio_divisas
    }
    detalle.find('.precioBaseItem').text(precioBase.toFixed(2));
    detalle.find('.precioMayorItem').text((precioBase - (precioBase * 0.1)).toFixed(2));

    let cantidadBruta = cantidad * cantidad_pmp;
    let subtotalBase = cantidad * precioBase;
    let subTotalDescuento = subtotalBase;
    if (cantidadBruta >= 20) {
      detalle.find('.subTotalBase').removeClass('d-none');
      subTotalDescuento = (subtotalBase - (subtotalBase * 0.1)).toFixed(2);
    } else {
      detalle.find('.subTotalBase').addClass('d-none');
    }
    detalle.find('.cantidadSubTotalBase').text(subtotalBase);
    detalle.find('.cantidadSubTotalDescuento').text(subTotalDescuento);

    // CALCULO
    totalBase += parseFloat(subtotalBase);
    totalDescuento += parseFloat(subTotalDescuento)
  });

  if (totalItems == 0) {
    $('.carritoVacio').removeClass('d-none');
    $('.nroItemsPedido').addClass('d-none')
  } else {
    $('.carritoVacio').addClass('d-none');
    $('.nroItemsPedido').removeClass('d-none').text(totalItems)
  }

  $(".totalesPedido").find('.cantidadTotalBase').text(totalBase.toFixed(2));
  $(".totalesPedido").find('.cantidadTotalDescuento').text(totalDescuento.toFixed(2));

  let montoDescuentoMayor = parseFloat((totalBase - totalDescuento).toFixed(2));
  if (montoDescuentoMayor <= 0) {
    $('.descuentoPedidoPorMayor').addClass('d-none');
    $('.totalBase').addClass('d-none').removeClass('d-flex');
    $(".cantidadDescuentoPedidoPorMayor").text("0.00");
  } else {
    $('.descuentoPedidoPorMayor').removeClass('d-none');
    $('.totalBase').removeClass('d-none').addClass('d-flex');
    $(".cantidadDescuentoPedidoPorMayor").text(montoDescuentoMayor);
  }

  //Total delivery
  let precioDolar = 450.00;
  let rutas = [
    {
      'id_ruta': 1,
      'minimo_km_ruta': 0,
      'maximo_km_ruta': 2,
      'precio_ruta': 0.50
    },
    {
      'id_ruta': '2',
      'minimo_km_ruta': 3,
      'maximo_km_ruta': 5,
      'precio_ruta': 1
    },
    {
      'id_ruta': 3,
      'minimo_km_ruta': 6,
      'maximo_km_ruta': 1000,
      'precio_ruta': 2
    },
  ];
  let kmR = $('#modalPagoUbicacion').find('#inputKilometrosTotales').val();
  let distanciaKM = parseFloat(kmR != '' ? kmR : 0);
  let precioKm = 0;
  rutas.forEach(ruta => {
    if (
      distanciaKM >= ruta.minimo_km_ruta &&
      distanciaKM <= ruta.maximo_km_ruta &&
      precioKm < ruta.precio_ruta
    ) {
      precioKm = ruta.precio_ruta;
    }
  });

  if (tipoMoneda == 'bs') precioKm = precioKm * precioDolar;
  const subtotalEnvio = parseFloat((distanciaKM * precioKm));
  $('#inputPrecioPorKm').val(precioKm.toFixed(2));
  $('#inputSubtotalEnvio').val(subtotalEnvio.toFixed(2));

  // Calculo pagos
  let monedasBD = [
    {
      'id_moneda': 1,
      'nombre_moneda': 'DÓLAR',
      'valor_moneda': 450.00,
    },
    {
      'id_moneda': 2,
      'nombre_moneda': 'BOLÍVAR',
      'valor_moneda': 1,
    },
    {
      'id_moneda': 3,
      'nombre_moneda': 'EURO',
      'valor_moneda': 550.00,
    },
    {
      'id_moneda': 4,
      'nombre_moneda': 'YUAN',
      'valor_moneda': 70,
    },
  ];
  let totalCancelado = 0.00;
  $('#modalPagoDetalles').find('.detalles_pago').each((i, e) => {
    let idMoneda = $(e).find('.selectMonedaPagoPedido').val();
    let montoPago = $(e).find('.inputMontoPagoPedido').val();
    montoPago = montoPago.replaceAll('.', '').replaceAll(',', '.');

    if (idMoneda != '' && montoPago != '') {
      let moneda = monedasBD.find(M => (M.id_moneda == idMoneda));
      totalCancelado += (moneda.valor_moneda * parseFloat(montoPago));
    }
    formateoCampos($(e).find('.inputMontoPagoPedido'), 'dinero')
  })
  if (tipoMoneda == 'usd') totalCancelado = totalCancelado / precioDolar;

  //Mandar totales al modal 2 del pago
  let modalPagoDeta = $('#modalPagoDetalles');
  modalPagoDeta.find('.totalItemsPedido').text(totalDescuento.toFixed(2))
  modalPagoDeta.find('.totalDeliveryPedido').text(subtotalEnvio)
  modalPagoDeta.find('.sumaTotalPedido').text(((totalDescuento + subtotalEnvio).toFixed(2)))
  modalPagoDeta.find('.canceladoTotalPedido').text(totalCancelado.toFixed(2))
  modalPagoDeta.find('.restanteTotalPedido').text(((((totalDescuento + subtotalEnvio) - totalCancelado) * -1).toFixed(2)))
  modalPagoDeta.find('.signoPrecio').text(signoMoneda);

  //Formateo
  formateoCampos(modalPagoDeta.find('.totalItemsPedido'), 'dineroSL')
  formateoCampos(modalPagoDeta.find('.totalDeliveryPedido'), 'dineroSL')
  formateoCampos(modalPagoDeta.find('.sumaTotalPedido'), 'dineroSL')
  formateoCampos(modalPagoDeta.find('.canceladoTotalPedido'), 'dineroSL')
  formateoCampos(modalPagoDeta.find('.restanteTotalPedido'), 'dineroSL')
  formateoCampos($('#inputPrecioPorKm'), 'dineroSL')
  formateoCampos($('#inputSubtotalEnvio'), 'dineroSL')
}
function cambiarMonedaCalculoPedido() {
  $(this).siblings().removeClass("active btn-dark text-white").addClass("btn-outline-secondary bg-light border-0 text-dark");
  $(this).addClass("active btn-dark text-white").removeClass("btn-outline-secondary bg-light border-0 text-dark");
  let monedaSel = $(this).data('tipo_pago');
  let contenedor = '';
  if ($(this).closest('.botonera').data('nro_btn') == '1') {
    contenedor = $('#modalPagoDetalles');
  } else {
    contenedor = $('.panelCotizacionPedido')
  }
  let botones = contenedor.find('.btnTipoPago');
  botones.removeClass("active btn-dark text-white").addClass("btn-outline-secondary bg-light border-0 text-dark");
  let botonElegido = botones.filter(`[data-tipo_pago="${monedaSel}"]`)
  botonElegido.addClass("active btn-dark text-white").removeClass("btn-outline-secondary bg-light border-0 text-dark");

  if (monedaSel == 'usd') {
    $('#modalPagoDetalles').find('.infoPagoBCV').addClass('d-none');
  } else {
    $('#modalPagoDetalles').find('.infoPagoBCV').removeClass('d-none');
  }

  recalcularTotalesPedido();
}
function eliminarItemPedido() {
  let detalle = $(this).closest('.itemPedido');
  let { id, tipo_item, id_presentacion } = detalle.data();

  let llave = JSON.stringify({
    id: parseInt(id),
    id_presentacion: parseInt(id_presentacion)
  })

  let items = JSON.parse(sessionStorage.getItem('itemsCarritoPedido'));
  if (items[tipo_item][llave]) {
    delete items[tipo_item][llave];
  }
  items = JSON.stringify(items);
  sessionStorage.setItem('itemsCarritoPedido', items);
  detalle.remove();
  recalcularTotalesPedido();
}
function obtenerUbicacion() {
  return new Promise((resolve, reject) => {
    navigator.geolocation.getCurrentPosition(resolve, reject);
  });
};
let instanciaMapa = '';

async function cargarMapaPedido() {
  const contenedorMapa = L.DomUtil.get('contenedorMapaPedido');
  if (contenedorMapa && contenedorMapa._leaflet_id) {
    if (instanciaMapa) {
      instanciaMapa.remove();
      instanciaMapa = null;
    }
  }
  if (contenedorMapa) {
    const resultado = await navigator.permissions.query({ name: 'geolocation' });
    const funcionDevolverDelMapa = async () => {
      const alerta = {
        tipo: 'simple',
        icono: 'warning',
        titulo: 'Permiso denegado!',
        texto: 'No se puede mostrar el mapa sin la autorización del uso del GPS del dispositivo'
      };
      $('#modalPagoUbicacion').find('.btn-close').trigger('click');
      await alertasAjax(alerta);
    };

    if (resultado.state === 'prompt') {
      const alerta = {
        tipo: 'preguntar',
        titulo: 'Usar Ubicación actual',
        texto: '¿Esta de acuerdo en que se use su ubicación actual para el pedido?'
      };
      const respuesta = await alertasAjax(alerta);
      if (!respuesta.isConfirmed) {
        return funcionDevolverDelMapa();
      }
    } else if (resultado.state === 'denied') {
      return funcionDevolverDelMapa();
    }

    let ubicacion = '';
    try {
      ubicacion = await obtenerUbicacion();
    } catch (error) {
      if (error.code == 1) {
        await funcionDevolverDelMapa();
        return;
      }
      return;
    }
    const latitud = ubicacion.coords.latitude;
    const longitud = ubicacion.coords.longitude;

    // ubicación por default (ubicacion de la persona)
    const mapa = L.map('contenedorMapaPedido').setView([latitud, longitud], 15);
    instanciaMapa = mapa;
    let marcadorActual = L.marker([latitud, longitud]).addTo(mapa).bindPopup('Ubicación del envío');
    var posicion = marcadorActual.getLatLng();
    actualizarDetallesEnvio({ 'lat': posicion.lat, 'lng': posicion.lng });

    // Ubicación de JLACRUZ
    const iconoJLACRUZ = L.divIcon({ className: 'iconoHamburguesa' });
    const marcadorJLACRUZ = L.marker([10.063276, -69.31708], { icon: iconoJLACRUZ }).addTo(mapa).bindPopup('JLACRUZ');

    // Capa de visualización
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png',
      {
        attribution: '&copy; <a href="#">OpenStreetMap</a> contributors'
      }
    ).addTo(mapa);

    // Evento del click
    mapa.on('click', function clickEnMapa(e) {
      if (marcadorActual) {
        mapa.removeLayer(marcadorActual);
      }
      marcadorActual = L.marker([e.latlng.lat, e.latlng.lng]).addTo(mapa);
      mapa.panTo([e.latlng.lat, e.latlng.lng]);
      actualizarDetallesEnvio(e.latlng);
    });

    $(document).on('click', '.btnSelUbiActualPedido', async function (e) {
      const ubicacion = await obtenerUbicacion();
      const latitud = ubicacion.coords.latitude;
      const longitud = ubicacion.coords.longitude;
      const latlng = L.latLng(latitud, longitud);

      mapa.flyTo([latitud, longitud], 16, {
        animate: true,
        duration: 2
      });

      if (marcadorActual) {
        mapa.removeLayer(marcadorActual);
      }
      marcadorActual = L.marker([latitud, longitud]).addTo(mapa);
      actualizarDetallesEnvio(latlng);
    });
  }
}
async function actualizarDetallesEnvio(latlng) {

  let items = JSON.parse(sessionStorage.getItem('itemsCarritoPedido'))
  if (!items) {
    items = {
      'productos': {},
      'servicios': {},
      'delivery': {}
    }
  }
  items['delivery']['latitud'] = latlng.lat;
  items['delivery']['longitud'] = latlng.lng;

  const centroJLACRUZ = { lat: 10.063276, lng: -69.31708 };
  let distanciaKM = 0;
  sessionStorage.setItem('itemsCarritoPedido', JSON.stringify(items));

  let apiKey = "plFhQVWfX5abG1DPt7jja56Syrqh7rY2";
  try {
    const respuesta = await fetch(
      `https://api.tomtom.com/routing/1/calculateRoute/${centroJLACRUZ.lat},${centroJLACRUZ.lng}:${latlng.lat},${latlng.lng}/json?key=${apiKey}&travelMode=car`
    );
    const infoRuta = await respuesta.json();

    if (infoRuta.routes[0].summary.lengthInMeters) {
      distanciaKM = Math.ceil(infoRuta.routes[0].summary.lengthInMeters / 1000);
    } else {
      throw new Error('No se pudo encontrar una ruta transitable por carretera');
    }
  } catch (error) {
    console.warn("Falla en Routing API, usando distancia lineal como respaldo:", error);
    const centroLL = L.latLng(centroJLACRUZ.lat, centroJLACRUZ.lng);
    distanciaKM = (centroLL.distanceTo(latlng) / 1000).toFixed(2);
  }
  $('#inputKilometrosTotales').val(distanciaKM);
  recalcularTotalesPedido();

  //Obtener ubicacion en reversa
  try {
    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latlng.lat}&lon=${latlng.lng}`);
    const data = await response.json();
    if (data.display_name) {
      $('#inputDireccionEnvio').val(data.display_name);
    } else {
      $('#inputDireccionEnvio').val(`${latlng.lat.toFixed(6)}, ${latlng.lng.toFixed(6)}`);
    }
  } catch (error) {
    console.error("Error obteniendo dirección:", error);
    $('#inputDireccionEnvio').val(`${latlng.lat.toFixed(6)}, ${latlng.lng.toFixed(6)}`);
  }
}
function aggDetallePagoPedido() {
  const detallesT = $('#contenedorDetallesPago').find('.detalles_pago');
  const htmlMolde = $('#modalPagoDetalles').find('.plantillaDetallePago').html();
  const stringMolde = htmlMolde.replace(/\[INDICES\]/g, detallesT.length);
  const molde = $(`<div class="detalles_pago">${stringMolde}</div>`);
  molde.find('.nroDetalle').text(detallesT.length + 1);
  molde.find('.btnEliminarDetallePago').removeClass('d-none');
  molde.removeClass('plantillaDetallePago d-none').addClass('detalles_pago');
  $('#contenedorDetallesPago').append(molde);
  actualizarBotonesEliminar();
  recalcularTotalesPedido();
}
function eliDetallePagoPedido() {
  let detallesT = $('#contenedorDetallesPago').find('.detalles_pago');
  if (detallesT.length <= 1) return;
  $(this).closest('.detalles_pago').remove();
  detallesT = $('#contenedorDetallesPago').find('.detalles_pago');
  detallesT.each((i, detalle) => {
    detalle = $(detalle)
    detalle.find('.nroDetalle').text(i + 1);
    detalle.find('.selectMetodoPagoPedido').attr('name', `pagos-${i}-id_metodo_pago`)
    detalle.find('.inputMontoPagoPedido').attr('name', `pagos-${i}-monto_pago`)
    detalle.find('.selectMonedaPagoPedido').attr('name', `pagos-${i}-id_moneda`)
    detalle.find('.inputReferenciaPagoPedido').attr('name', `pagos-${i}-referencia_pago`)
    detalle.find('.selectBancoEmisorPagoPedido').attr('name', `pagos-${i}-id_banco_emisor`)
    detalle.find('.selectBancoReceptorPagoPedido').attr('name', `pagos-${i}-id_banco_receptor`)
  });
  actualizarBotonesEliminar();
  recalcularTotalesPedido();
}
function actualizarBotonesEliminar() {
  const total = $('#contenedorDetallesPago').find('.detalles_pago').length;
  if (total <= 1) {
    $('.btnEliminarDetallePago').addClass('d-none');
  } else {
    $('.btnEliminarDetallePago').removeClass('d-none');
  }
}
function mostrarPreviewComprobantes() {
  const preview = $('#modalPagoDetalles').find('#pagoComprobantesPreview');
  preview.empty();

  let archivos = Array.from(this.files);
  archivos.forEach(file => {
    const nombreFoto = $(`
      <div class="pago-file-chip">
        <i class="fi fi-rr-picture"></i>
        <span>${file.name}</span>
      </div>
    `);
    preview.append(nombreFoto);
  });

  let drop = $('#modalPagoDetalles').find('.areaComprobantesPago').find('.msjDropCapturesPago');
  if (archivos.length > 0) {
    drop.addClass('d-none');
  } else {
    drop.removeClass('d-none');
  }
}
async function eliminarPedido() {
  const resultado = await Swal.fire({
    title: '¿Eliminar pedido?',
    text: 'Esta acción vaciará todos los ítems de tu pedido actual.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#c82333',
    cancelButtonColor: '#4e54c8',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
  });
  if (resultado.isConfirmed) {
    sessionStorage.removeItem('itemsCarritoPedido');
    listarItemPanelCarritoPedido();
    $('#modalPagoDetalles').modal('hide');
    $('#modalPagoDetalles').find('#contenedorDetallesPago').empty();
    aggDetallePagoPedido();
    $('#modalPagoDetalles').find('#inputComprobantes').val('').trigger('change')
    Swal.fire({ icon: 'success', title: 'Pedido eliminado', timer: 1500, showConfirmButton: false });
  }
}
async function mostrarUOcultarCamposDetallesPagoPedido() {

  let idMetodoPagoSel = $(this).val()

  let detalle = $(this).closest('.detalles_pago');
  if (idMetodoPagoSel == '') {
    detalle.find(`
      .inputReferenciaPagoPedido,
      .selectBancoEmisorPagoPedido,
      .selectBancoReceptorPagoPedido
    `).each((i, e) => {
      $(e).prop('disabled', true).closest('[class*="col-lg-"]').addClass('d-none')
    });

    detalle.find('.selectMonedaPagoPedido')
      .closest('[class*="col-lg-"]').addClass('d-none')

    detalle.find('.selectMetodoPagoPedido')
      .closest('[class*="col-lg-"]')
      .removeClass('col-lg-4')
      .addClass('col-lg-6');

    detalle.find('.inputMontoPagoPedido')
      .closest('[class*="col-lg-"]')
      .removeClass('col-lg-4')
      .addClass('col-lg-6');

    return;
  }
  let metodoPagoBD = await pedirDatosAjax({
    'modulo': 'metodos-pago',
    'datosPe': {
      'accion': 'seleccionarUno',
      id_metodo_pago: idMetodoPagoSel
    },
  })
  let totalCampos = 0;

  //Moneda
  if (metodoPagoBD.necesita_moneda == 1) {
    detalle.find('.selectMetodoPagoPedido')
      .closest('.col-lg-6')
      .removeClass('col-lg-6')
      .addClass('col-lg-4');
    detalle.find('.inputMontoPagoPedido')
      .closest('.col-lg-6')
      .removeClass('col-lg-6')
      .addClass('col-lg-4');
    detalle.find('.selectMonedaPagoPedido')
      .val('')
      .closest('.col-lg-4')
      .removeClass('d-none');
  } else {
    detalle.find('.selectMetodoPagoPedido')
      .closest('.col-lg-4')
      .removeClass('col-lg-4')
      .addClass('col-lg-6');
    detalle.find('.inputMontoPagoPedido')
      .closest('.col-lg-4')
      .removeClass('col-lg-4')
      .addClass('col-lg-6');
    detalle.find('.selectMonedaPagoPedido')
      .val(2)
      .closest('.col-lg-4')
      .addClass('d-none');
  }

  // Referencia
  if (metodoPagoBD.necesita_referencia == 1) {
    totalCampos++;
    detalle
      .find('.inputReferenciaPagoPedido').prop('disabled', false)
      .closest('[class*="col-lg"]').removeClass('d-none');
  } else {
    detalle
      .find('.inputReferenciaPagoPedido').prop('disabled', true)
      .closest('[class*="col-lg"]').addClass('d-none');
  }

  //Banco emisor
  if (metodoPagoBD.necesita_banco_emisor == 1) {
    totalCampos++;
    detalle
      .find('.selectBancoEmisorPagoPedido').prop('disabled', false)
      .closest('[class*="col-lg"]').removeClass('d-none');
  } else {
    detalle
      .find('.selectBancoEmisorPagoPedido').prop('disabled', true)
      .closest('[class*="col-lg"]').addClass('d-none');
  }

  // Banco Receptor
  if (metodoPagoBD.necesita_banco_receptor == 1) {
    totalCampos++;
    detalle
      .find('.selectBancoReceptorPagoPedido').prop('disabled', false)
      .closest('[class*="col-lg"]').removeClass('d-none');
  } else {
    detalle
      .find('.selectBancoReceptorPagoPedido').prop('disabled', true)
      .closest('[class*="col-lg"]').addClass('d-none');
  }

  let largoCSF = 12 / (totalCampos != 0 ? totalCampos : 3);

  let CSF = detalle.find('.inputReferenciaPagoPedido, .selectBancoEmisorPagoPedido, .selectBancoReceptorPagoPedido')
  CSF.each((i, elemento) => {
    let grupocCampo = $(elemento).closest('[class*="col-lg"]');
    grupocCampo.attr('class', function (i, c) {
      let clase = c.replace(/(^|\s)col-lg-\S+/g, '');
      return c.replace(/(^|\s)col-lg-\S+/g, '');
    });
    grupocCampo.addClass(`col-lg-${largoCSF}`)
  });
  recalcularTotalesPedido();
}
async function enviarPedido() {
  let respuesta = await alertasAjax({
    'tipo': 'preguntar',
    'titulo': 'Confirmar pedido',
    'texto': '¿Desea confirmar y registrar su pedido?'
  })
  if (respuesta.isConfirmed) {

    let hayCamposInvalidos = await validarTodosLosCampos($(this).closest('.modal').find('#contenedorDetallesPago'), 'pedidos');
    if (hayCamposInvalidos) {
      return alertasAjax({
        'tipo': 'simple',
        'titulo': 'Campos Inválidos',
        'texto': 'El formulario del pago tiene campos inválidos, verifique e intente de nuevo',
        'icono': 'warning'
      })
    }

    let pagos = await convertirHTMLJSON({
      elemento: $('#modalPagoDetalles #contenedorDetallesPago'),
    })
    pagos = await JSON.parse(pagos);
    let comprobantesPago = await convertirHTMLJSON({
      elemento: $('#modalPagoDetalles .areaComprobantesPago'),
      camposFoto: ['comprobantes_pago']
    })
    let items = JSON.parse(sessionStorage.getItem('itemsCarritoPedido') ?? {});
    if (mLength(items['productos']) <= 0) {
      return alertasAjax({
        'tipo': 'simple',
        'titulo': 'Sin items en el carrito',
        'texto': 'No puedes enviar un pedido sin articulos',
        'icono': 'warning'
      });
    }
    if (!(comprobantesPago instanceof FormData) || mLength(comprobantesPago) <= 0) {
      return alertasAjax({
        'tipo': 'simple',
        'titulo': 'Sin comprobantes de pago',
        'texto': 'Debe agg al menos un comprobante de pago',
        'icono': 'warning'
      });
    }

    let todaInfo = {
      ...items,
      ...pagos,
      ...{
        'accion': 'registrar'
      }
    };
    todaInfo.productos = Object.values(items.productos);
    comprobantesPago.append('metadatos', JSON.stringify(todaInfo));
    const config = {
      method: 'POST',
      headers: encabezadosPeticiones,
      mode: 'cors',
      cache: 'no-cache',
      body: comprobantesPago
    };
    respuesta = await (await fetch(rutaAbsoluta + 'pedidos', config)).json();
    if (respuesta.icono == 'success') {
      $(this).closest('.modal').modal('hide')
    }



    return alertasAjax(respuesta);
  }
}
// #endregion [E-COMMERCE] FIN

//#region [ DINAMISMO DEL HTML ] COMIENZO

function initSidebar() {

  //Evento para abrir el sidebar
  $(document).off('click', '.sidebarToggle')
  $(document).on('click', '.sidebarToggle', function (e) {
    $("body").toggleClass("sidebar-closed");
    $(".sidebar").toggleClass("show");
    $(".sidebarBackdrop").toggleClass("show");
  })

  //Evento para cerrar el sidebar
  $(document).off('click', '.sidebarBackdrop')
  $(document).on('click', '.sidebarBackdrop', function (e) {
    $(".sidebar").removeClass("show");
    $(this).removeClass("show");
    $("body").addClass("sidebar-closed");
  })

  $(document).off('click', '.sidebar-menu li')
  $(document).on('click', '.sidebar-menu li', function (e) {
    cambiarEstadoLiSidebar.call(this);
  })
}
function cambiarEstadoLiSidebar() {

  if ($(this).hasClass('activa')) {
    return;
  } else {
    $(this).addClass('activa');
    $(this).closest('.sidebar-menu').find('li').not($(this)).removeClass('activa')
  }

  if (!$(this).hasClass('aSubSidebar')) {
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
function eliminarAriaHidden() {
  $('[aria-hidden="true"]').removeAttr('aria-hidden');
  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (mutation.type === 'attributes' && mutation.attributeName === 'aria-hidden') {
        const target = $(mutation.target);
        if (target.attr('aria-hidden') === 'true') {
          // Lo removemos inmediatamente
          target.removeAttr('aria-hidden');
        }
      }
    });
  });
  observer.observe(document.body, {
    attributes: true,
    subtree: true,
    attributeFilter: ['aria-hidden']
  });
}
function iniciarTooltips() {
  let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
  })
}
//#endregion [ DINAMISMO DEL HTML ] FIN

// #region [CARGAR PRECIO DEL DÓLAR API] COMIENZO
export async function cargarPrecioDolar() {
  try {
    let infoDolarWS = sessionStorage.getItem('infoDolarWS');
    if (!infoDolarWS) {
      infoDolarWS = {};
    } else {
      infoDolarWS = JSON.parse(infoDolarWS);
    }
    const tipoPrecio = infoDolarWS.tipoPrecio;
    let precioDolar = '';
    let pedirPorAPI = false;

    if (tipoPrecio == 'Precio Local' || !tipoPrecio) {
      pedirPorAPI = true;
    }

    if (pedirPorAPI) {
      try {
        const dolar = await (await fetch('https://api-the-vina-node.onrender.com/api/precio-dolar-bcv')).json();
        precioDolar = dolar.precioDolar.toFixed(2);
        infoDolarWS['precio'] = precioDolar.toString();
        infoDolarWS['tipoPrecio'] = 'Precio del BCV';
      } catch (error) {
        try {
          const dolar = await (await fetch('https://ve.dolarapi.com/v1/dolares/oficial')).json();
          precioDolar = dolar.promedio.toFixed(2);
          infoDolarWS['precio'] = precioDolar.toString();
          infoDolarWS['tipoPrecio'] = 'Precio del BCV';
        } catch (error) {
          const dolar = await pedirDatosAjax({
            modulo: 'monedas',
            datosPe: {
              accion: 'seleccionarUno',
              id_moneda: 1
            }
          });
          precioDolar = dolar.valor_moneda.toFixed(2);
          infoDolarWS['precio'] = precioDolar.toString();
          infoDolarWS['tipoPrecio'] = 'Precio Local';
        }
      }
    }

    if (infoDolarWS['tipoPrecio'] == 'Precio Local') {
      $('.tipoDeDolarPrecio').text(infoDolarWS['tipoPrecio']);
    } else {
      $('.tipoDeDolarPrecio').empty();
      $('.tipoDeDolarPrecio').append(`<a href="https://www.bcv.org.ve/">${infoDolarWS['tipoPrecio']}</a>`);
    }

    let precio = parseFloat(infoDolarWS['precio']).toFixed(2);
    infoDolarWS['precio'] = precio
    $('.precio_dolar').text(precio);
    sessionStorage.setItem('infoDolarWS', JSON.stringify(infoDolarWS))
  } catch (error) {
    $('.contenedorPrecioDolar').empty().text('ERROR AL CARGAR EL PRECIO DEL DÓLAR');
  }
}
async function initPrecioDolar() {
  if (!sessionStorage.getItem('infoDolarWS')) {
    cargarPrecioDolar();
  } else {
    let infoDolar = JSON.parse(sessionStorage.getItem('infoDolarWS'));
    if (infoDolar['tipoPrecio'] == 'Precio Local') {
      $('.tipoDeDolarPrecio').text(infoDolar['tipoPrecio']);
    } else {
      $('.tipoDeDolarPrecio').empty();
      $('.tipoDeDolarPrecio').append(`<a href="https://www.bcv.org.ve/">${infoDolar['tipoPrecio']}</a>`);
    }

    $('.precio_dolar').text(parseFloat(infoDolar['precio']).toFixed(2));
  }
  extraerDatosAjax({
    modulosPeticion: ['monedas'],
    accionesPeticion: [{ 'accion': 'seleccionarUno', 'id_moneda': 1 }],
    tipoElemento: ['input'],
    elementosDestino: [$('.precioDolar')],
    datosInsertar: ['valor_moneda'],
    funcionBusqueda: [
      function (registros) {
        return registros.find(dato => dato.id_moneda == 1)
      },
    ]
  })
}
// #endregion [CARGAR PRECIO DEL DÓLAR API] FIN

// #region [IMAGENES REGISTROS] FIN
async function actualizarFotoPerfil() {
  let modal = $(this).closest('.modal')
  let datos = modal.data();
  console.log('datos: ', datos)
  const formulario = modal.find('.formularioActualizarFotoPerfil');
  const inputFoto = modal.find('#inputFotoPerfil');
  if (inputFoto[0].files.length === 0) {
    Swal.fire({
      icon: 'warning',
      title: 'Atención',
      text: 'Por favor, selecciona una nueva foto para actualizar.',
    });
    return;
  }

  const resultado = await enviarFormulario({
    formulario: formulario,
    modulo: datos.tabla_bd,
    camposFoto: datos.campo_foto
  });
  if (resultado && resultado.icono === 'success') {
    const nuevaRuta = $('#previsualizacionFotoPerfilModal').attr('src');
    $(`img[data-tabla_bd="${datos.tabla_bd}"][data-campo_id="${datos.campo_id}"][data-valor_id="${datos.valor_id}"]`)
      .attr('src', nuevaRuta);
    modal.modal('hide');
  }
}
async function eliminarFotoPerfil() {
  let modal = $(this).closest('.modal');
  let datos = modal.data();

  let resultado = await alertasAjax({
    tipo: 'preguntar',
    titulo: '¿Estás seguro?',
    texto: datos.texto_alerta,
    icono: 'warning',
  });
  if (resultado.isConfirmed) {
    const resultado = await pedirDatosAjax({
      modulo: datos.tabla_bd,
      datosPe: {
        accion: datos.accion_eli,
        [datos.campo_id]: datos.valor_id
      }
    });
    if (resultado && resultado.icono === 'success') {
      const rutaDefault = rutaFotos + datos.tabla_bd +'/'+ datos.foto_default;
      modal.find('#previsualizacionFotoPerfilModal').attr('src', rutaDefault);
      $(`img[data-tabla_bd="${datos.tabla_bd}"][data-campo_id="${datos.campo_id}"][data-valor_id="${datos.valor_id}"]`)
      .attr('src', rutaDefault);
      modal.find('#inputFotoPerfil').val('');
      modal.modal('hide');
    }
    alertasAjax(resultado);
  }
}
// #endregion [IMAGENES REGISTROS] FIN

//#region [ DELEGACIÓN DE EVENTOS ] COMIENZO

//Evento para la precarga datos y eventos
$(document).on('DOMContentLoaded', async function (e) {
  eliminarAriaHidden();
  iniciarTooltips()
  let vistasFueraSe = ['login'];
  if (!vistasFueraSe.includes(vista)) {
    initSidebar();
    initPrecioDolar();
    recalcularTotalesPedido();
    aggDetallePagoPedido();
    cargarModuloSeleccionaSidebar();
    initNotificaciones();
    listarNotificaciones()
    iniciarServidorWS();
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
    listarItemPanelCarritoPedido();
  }
});
//#region [ IMAGENES DE REGISTROS ] COMIENZO

// Sincronizar imagen con la del perfil
$(document).on('click', '.fotoRegistro', function () {
  let modal = $('#modalActualizarFotoPerfil');
  let datos = $(this).data();
  modal.modal('show');
  modal.find('#previsualizacionFotoPerfilModal').attr('src', $(this).attr('src'))
  modal.find('#inputFotoPerfil').val('').attr('name', datos.campo_foto);
  modal.find('#nombreCampoIdRegistroFoto').val(datos.valor_id).attr('name', datos.campo_id);
  modal.find('#modalActualizarFotoPerfilLabel').text(datos.label_foto ?? 'Actualizar imagen de registro');
  modal.find('.inputAccionActFoto').val(datos.accion_act);
  modal.data(datos);
});

// Vinculación foto - input FILE
$(document).on('click', '#btnDispararInputFile', function () {
  $('#inputFotoPerfil').trigger('click');
});

// Previsualización de la imagen seleccionada
$(document).on('change', '#inputFotoPerfil', function (e) {
  const archivo = e.target.files[0];
  if (archivo) {
    const reader = new FileReader();
    reader.onload = function (event) {
      $('#previsualizacionFotoPerfilModal').attr('src', event.target.result);
    };
    reader.readAsDataURL(archivo);
  }
});

// Actualizar foto de perfil
$(document).on('click', '.btnGuardarFotoPerfil', async function () {
  actualizarFotoPerfil.call(this);
});

// Eliminar foto de perfil
$(document).on('click', '.btnEliminarFotoPerfil', async function (e) {
  e.stopPropagation(); // Evitar que el clic se propague al contenedor que abre el input file
  eliminarFotoPerfil.call(this);
});

//#endregion [ IMAGENES DE REGISTROS ] FIN

//#region [ NOTIFICACIONES ] COMIENZO

//Eliminar todas las notificaciones
$(document).off('click', '.btnETLN')
$(document).on('click', '.btnETLN', function () {
  vaciarBuzonNotificaciones();
})

//Marcar todas las notificaciones como leidas
$(document).off('click', '.btnMTLNCL')
$(document).on('click', '.btnMTLNCL', function () {
  marcarNotificacionesComoLeidas();
})

//#endregion [ NOTIFICACIONES ] FIN

//#region [E-COMMERCE]

//Evento para validar en tiempo real
$(document).off('input blur', '#modalPagoDetalles input, #modalPagoDetalles select, #modalPagoDetalles textarea')
$(document).on('input blur', '#modalPagoDetalles input, #modalPagoDetalles select, #modalPagoDetalles textarea', function () {
  validarEnTiempoReal(this, 'pedidos');
})

//Evento para validar en tiempo real
$(document).off('input', '#modalPagoDetalles .inputReferenciaPagoPedido')
$(document).on('input', '#modalPagoDetalles .inputReferenciaPagoPedido', function () {
  formateoCampos($(this), 'referencia');
})

// Cambiar moneda de calculo
$(document).off('click', '.btnTipoPago')
$(document).on("click", ".btnTipoPago", function () {
  cambiarMonedaCalculoPedido.call(this);
});

//Aumentar o decrementar un item
$(document).off('click', '.btnSumItemPedido, .btnResItemPedido')
$(document).on("click", ".btnSumItemPedido, .btnResItemPedido", function () {
  sumarRestarItemCarritoPedido.call(this);
});

//Eliminar un item
$(document).off("click", ".btnEliItemPedido");
$(document).on("click", ".btnEliItemPedido", function () {
  eliminarItemPedido.call(this)
});

// cargar mapa leafteat
$(document).off('click', '.btnProcesarPedido');
$(document).on('click', '.btnProcesarPedido', function (e) {
  e.preventDefault();
  const offcanvasCarrito = bootstrap.Offcanvas.getInstance(document.getElementById('cartOffcanvas'));
  if (offcanvasCarrito) offcanvasCarrito.hide();
  setTimeout(() => {
    $('#modalPagoUbicacion').modal('show');
  }, 200);
  cargarMapaPedido.call(this);
});

//Agg detalle pago pedido
$(document).off('click', '.btnAggDetallePagoPedido');
$(document).on('click', '.btnAggDetallePagoPedido', function () {
  aggDetallePagoPedido();
});

// Eliminar detalle Pago Pedido
$(document).off('click', '.btnEliminarDetallePago');
$(document).on('click', '.btnEliminarDetallePago', function () {
  eliDetallePagoPedido.call($(this));
});

// Preview de comprobantes
$(document).off('change', '#modalPagoDetalles #inputComprobantes');
$(document).on('change', '#modalPagoDetalles #inputComprobantes', function () {
  mostrarPreviewComprobantes.call(this);
});

// Borrrar detalles del pedido
$(document).off('click', '#btnEliminarPedido');
$(document).on('click', '#btnEliminarPedido', async function () {
  eliminarPedido();
});

// Enviar pedido
$(document).off('click', '#btnConfirmarPedido');
$(document).on('click', '#btnConfirmarPedido', function () {
  enviarPedido.call(this);
});

// Sincronizar Leaflet con el ciclo de vida del Modal
$(document).on('shown.bs.modal', '#modalPagoUbicacion', function () {
  if (instanciaMapa) {
    setTimeout(() => {
      instanciaMapa.invalidateSize();
    }, 200);
  }
});

// mostrar u ocultar campos de detalles dependiendo del metodo de pago
$(document).off('change', '.selectMetodoPagoPedido');
$(document).on('change', '.selectMetodoPagoPedido', function () {
  mostrarUOcultarCamposDetallesPagoPedido.call(this);
});

// mostrar u ocultar campos de detalles dependiendo del metodo de pago
$(document).off('input', '.inputMontoPagoPedido');
$(document).on('input', '.inputMontoPagoPedido', function () {
  recalcularTotalesPedido();
});

// mostrar u ocultar campos de detalles dependiendo del metodo de pago
$(document).off('change', '.selectMonedaPagoPedido');
$(document).on('change', '.selectMonedaPagoPedido', function () {
  recalcularTotalesPedido();
});



//#endregion [E-COMMERCE]

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






