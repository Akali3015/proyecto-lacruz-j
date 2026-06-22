import { rutaAbsoluta, alertasAjax } from './global.js';
export function initExportarDB() {
  // Abrir modal
  $(document).off('click', '#btnExportarBD');
  $(document).on('click', '#btnExportarBD', function (e) {
    e.preventDefault();
    $('#modalExportarBD').modal('show');
  });

  // Confirmar exportación
  $(document).off('click', '#btnConfirmarExportar');
  $(document).on('click', '#btnConfirmarExportar', async function () {
    const basesSeleccionadas = [];
    $('.checkExportBD:checked').each(function () {
      basesSeleccionadas.push($(this).val());
    });

    if (basesSeleccionadas.length === 0) {
      await alertasAjax({
        tipo: 'simple',
        icono: 'warning',
        titulo: 'Sin selección',
        texto: 'Debe seleccionar al menos una base de datos'
      });
      return;
    }

    // Mostrar estado
    const btn = $(this);
    btn.prop('disabled', true);
    $('#exportarStatus').removeClass('d-none');

    // Exportar cada base seleccionada
    for (const base of basesSeleccionadas) {
      const nombre = base === 'proyecto_lacruz_seguridad' ? 'BD Seguridad' : 'BD Principal';

      try {
        window.open(rutaAbsoluta + 'exportar?bd=' + base, '_blank');
        await new Promise(resolve => setTimeout(resolve, 500));
      } catch (error) {
        console.error('Error al exportar ' + nombre + ':', error);
      }
    }

    // Ocultar estado y cerrar modal
    $('#exportarStatus').addClass('d-none');
    btn.prop('disabled', false);
    $('#modalExportarBD').modal('hide');

    await alertasAjax({
      tipo: 'simple',
      icono: 'success',
      titulo: 'Exportación iniciada',
      texto: 'Se descargarán ' + basesSeleccionadas.length + ' archivo(s) SQL'
    });
  });
}