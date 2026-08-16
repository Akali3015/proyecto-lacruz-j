import './global.js'
import { driverAyuda, mostrarAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"

//#region [ FUNCIONES PROPIAS DEL MODULO ] COMIENZO

function registrarTutorial() {
  driverAyuda('home', {
    pasos: [
      {
        element: 'a[href*="clientes"] .step-card',
        popover: {
          title: 'Clientes',
          description: 'Accede a la gestión de clientes para registrar, editar o eliminar clientes de tu base de datos.',
          side: 'top',
          align: 'start'
        }
      },
      {
        element: 'a[href*="inventario"] .step-card',
        popover: {
          title: 'Inventario',
          description: 'Controla el stock de productos y materias primas, y registra movimientos de inventario.',
          side: 'top',
          align: 'start'
        }
      },
      {
        element: 'a[href*="ordenesEntregasPresupuestos"] .step-card',
        popover: {
          title: 'Órdenes de Entrega',
          description: 'Crea y gestiona las órdenes de entrega de productos.',
          side: 'top',
          align: 'start'
        }
      },
      {
        element: 'a[href*="servicios"] .step-card',
        popover: {
          title: 'Servicios',
          description: 'Administra el catálogo de servicios de la empresa.',
          side: 'top',
          align: 'start'
        }
      },
      {
        element: 'a[href*="productos"] .step-card',
        popover: {
          title: 'Productos',
          description: 'Administra el catálogo de productos de la empresa.',
          side: 'top',
          align: 'start'
        }
      },
      {
        element: 'a[href*="materiasPrimas"] .step-card',
        popover: {
          title: 'Materias Primas',
          description: 'Controla el inventario de materias primas necesarias para la producción.',
          side: 'top',
          align: 'start'
        }
      },
      {
        element: 'a[href*="proveedores"] .step-card',
        popover: {
          title: 'Proveedores',
          description: 'Gestiona los proveedores de la empresa y sus datos de contacto.',
          side: 'top',
          align: 'start'
        }
      },
      {
        element: 'a[href*="compras"] .step-card',
        popover: {
          title: 'Recepciones',
          description: 'Registra las recepciones de compras realizadas a proveedores.',
          side: 'top',
          align: 'start'
        }
      },
      {
        element: '.contenedorPrecioDolar',
        popover: {
          title: 'Precio del Dólar',
          description: 'Muestra la tasa de cambio actual del dólar según el BCV, actualizada en tiempo real.',
          side: 'bottom',
          align: 'center'
        }
      },
      {
        element: '#btnAyudaInteractiva',
        popover: {
          title: 'Ayuda Disponible',
          description: 'En cualquier momento puedes hacer clic aquí para volver a ver este tutorial o explorar otros módulos.',
          side: 'left',
          align: 'center'
        }
      },
      {
        element: '.nav-item a[data-bs-toggle="offcanvas"][data-bs-target="#cartOffcanvas"]',
        popover: {
          title: 'Carrito de Pedidos',
          description: 'Haz clic aquí para realizar tus pedidos. Podrás revisar los productos agregados y proceder con el pago.',
          side: 'bottom',
          align: 'center'
        }
      }, 
      {
        element: '.dropdownUsuario .user-avatar',
        popover: {
          title: 'Perfil de Usuario',
          description: 'Aquí puedes ver y editar tu información personal, cambiar tu contraseña o cerrar sesión.',
          side: 'bottom',
          align: 'center'
        }
      },
      {
        popover: {
          title: '¡Ayuda completada!',
          description: 'Ya conoces los módulos principales del sistema. Haz clic en cualquier tarjeta para acceder a su funcionalidad.',
          side: 'top'
        }
      }
    ]
  });
}

//#endregion [ FUNCIONES PROPIAS DEL MODULO ] FIN

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {
  registrarTutorial();
  
  await new Promise(resolve => setTimeout(resolve, 500));
  
  const driverPendiente = sessionStorage.getItem('driver_pendiente');
  if (driverPendiente === 'home') {
    sessionStorage.removeItem('driver_pendiente');
    setTimeout(() => {
      mostrarAyuda();
    }, 1000);
  }
});
//#endregion [DELEGACIÓN DE EVENTOS] FIN