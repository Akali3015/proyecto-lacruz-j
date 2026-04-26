<?php

$fotoPerfil = ($_SESSION['foto'] ?? '') != '' ? $_SESSION['foto'] : 'perfilDefaultUsuario.png';
$fotoPerfil = explode("?", $fotoPerfil);
$fotoPerfil = APP_URL . DIR_FOTOS . 'usuarios/' . $fotoPerfil[0];

?>

<input class="precioDolar" type="hidden">

<nav class="headerPrincipal navbar navbar-expand-lg noselec shadow-sm py-2">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
      <button class="btn text-white sidebarToggle p-2 me-1 rounded-circle hover-opacity">
        <i class="fi fi-br-menu-burger fs-4"></i>
      </button>
    </div>
    <div class="navbar-nav d-flex flex-row align-items-center gap-2 gap-md-3">
      <!-- Precio Dólar -->
      <div class="nav-item d-flex justify-content-center align-items-center">
        <div class="bg-white bg-opacity-10 border border-white border-opacity-25 rounded-pill px-3 py-1 d-flex flex-column align-items-center justify-content-center shadow-sm">
          <h6 class="contenedorPrecioDolar p-0 m-0 text-white fw-bold lh-1 d-flex align-items-center" style="font-size: 0.85rem; letter-spacing: 0.5px;">
            1<span class="text-green-500 me-1">$</span> ≈ <span class="ms-1 precio_dolar">484.74</span> <span class="ms-1 small opacity-75">Bs</span>
          </h6>
          <small class="tipoDeDolarPrecio m-0 p-0 d-none d-md-block" style="font-size: 0.6rem;">
            <a href="https://www.bcv.org.ve/" target="_blank" class="text-white">Tasa BCV</a>
          </small>
        </div>
      </div>
      <!-- Notificaciones -->
      <div class="nav-item dropdown custom-dropdown d-flex justify-content-center align-items-center">
        <a
          href="#"
          data-bs-toggle="dropdown"
          data-bs-auto-close="outside"
          data-bs-target="dropdown-notificaciones"
          class="nav-link text-white p-2 position-relative d-flex align-items-center justify-content-center"
          aria-haspopup="true"
          aria-expanded="false"
          style="width: 40px; height: 40px;">
          <i class="fi fi-rs-bell fs-4"></i>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger nroNotNoLeidas border border-2 border-primary" style="font-size: 0.65rem; min-width: 18px; padding: 2px;"></span>
        </a>
        <div class="dropdown-notificaciones dropdown-menu shadow-lg border-0 mt-3 p-0" style="width: 320px; max-width: 90vw;">
          <div class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between rounded-top">
            <h6 class="mb-0 fw-bold text-dark">Notificaciones</h6>
            <div class="d-flex gap-2">
              <button class="btn btn-sm btn-link p-0 text-primary fw-semibold btnMTLNCL" style="font-size: 0.75rem; text-decoration: none;">Leídas</button>
              <button class="btn btn-sm btn-outline-danger p-1 rounded-circle btnETLN" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                <i class="fi fi-rr-trash-clock fs-7"></i>
              </button>
            </div>
          </div>
          <ul class="custom-notifications contenedorNotificaciones p-0 m-0 list-unstyled" style="max-height: 350px; overflow-y: auto;">
          </ul>
        </div>
      </div>
      <!-- Carrito -->
      <div class="nav-item">
        <a href="#" class="nav-link text-white p-2 position-relative d-flex align-items-center justify-content-center" 
           data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas" style="width: 40px; height: 40px;">
          <i class="fi fi-rr-shopping-cart fs-4"></i>
          <span class="nroItemsPedido d-none position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-2 border-primary" style="font-size: 0.65rem; min-width: 18px; padding: 2px;">
            0
          </span>
        </a>
      </div>
      <!-- Usuario -->
      <div class="nav-item dropdown dropdownUsuario">
        <a class="nav-link d-flex align-items-center p-1 rounded-pill hover-bg-white-10" href="#" role="button" aria-expanded="false"
          data-bs-toggle="dropdown" data-bs-auto-close="outside">
          <div class="user-avatar text-warning d-flex align-items-center justify-content-center overflow-hidden border border-2 border-white border-opacity-25" style="width: 36px; height: 36px; border-radius: 50%;">
            <img 
              src="<?php echo $fotoPerfil; ?>" 
              data-tabla_bd="usuarios"
              data-campo_id="cedula_usuario"
              data-valor_id="<?php echo $_SESSION['cedula']?>"
              style="width: 100%; height: 100%; object-fit: cover;"
            >
          </div>
          <div class="ms-2 d-none d-md-block me-1">
            <span class="user-name d-block fw-bold text-white lh-1" style="font-size: 0.9rem;"><?php echo $_SESSION['usuario']; ?></span>
            <small class="text-white text-opacity-75" style="font-size: 0.7rem;">En línea</small>
          </div>
          <i class="fi fi-rr-angle-small-down fs-6 text-white d-none d-md-block ms-1"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 p-0" style="min-width: 350px; border-radius: 12px; overflow: hidden;">
          <div class="p-4 bg-primary text-white position-relative overflow-hidden">
            <div class="position-absolute rounded-circle"
              style="width: 120px; height: 120px; background-color: rgba(255,255,255,0.1); top: -30px; right: -30px;">
            </div>
            <div class="d-flex align-items-center position-relative">
              <img 
                src="<?php echo $fotoPerfil; ?>" alt="Perfil"
                class="fotoRegistro estiloFotoRegistro imgG-60 shadow-sm"
                data-tabla_bd="usuarios"
                data-campo_id="cedula_usuario"
                data-valor_id="<?php echo $_SESSION['cedula'];?>"
                data-campo_foto="foto_usuario"
                data-accion_act="actualizarFoto"
                data-accion_eli="eliminarFoto"
                data-label_foto="Actualizar Foto de Perfil"
                data-texto_alerta="Tu foto de perfil volverá a la configuración predeterminada"
                data-foto_default="perfilDefaultUsuario.png"
              >
              <div class="ms-3">
                <h6 class="mb-0 fw-bold"><?php echo $_SESSION['nombre'] . ' ' . $_SESSION['apellido'] ?></h6>
                <small class="text-white text-opacity-75"><?php echo $_SESSION['nombreRol']; ?></small>
              </div>
            </div>
          </div>
          <div class="p-2">
            <a
              data-bs-toggle="modal"
              data-bs-target=".modalPerfil"
              value="<?php echo $_SESSION['cedula'] ?>"
              claseFormulario=".formularioPerfil"
              class="btnEditarPerfil dropdown-item d-flex align-items-center py-2 px-3 rounded mb-1 text-muted fw-semibold"
              href="#">
              <i class="fi fi-rr-user fs-5 me-3 text-primary"></i>
              <span class="m-0">Editar mi perfil</span>
            </a>
            <hr class="dropdown-divider mx-2 my-2 opacity-10">
            <div
              class="dropdown-item d-flex align-items-center justify-content-between py-2 px-3 rounded mb-1 text-muted fw-semibold"
              style="cursor: pointer;">
              <div class="d-flex align-items-center">
                <i class="fi fi-rr-moon-stars fs-5 me-3 text-primary"></i>
                <span class="m-0">Modo Oscuro</span>
              </div>
              <div class="form-check form-switch m-0 p-0 d-flex align-items-center">
                <input class="form-check-input ms-0 me-0" type="checkbox" id="darkModeSwitch"
                  style="cursor: pointer; transform: scale(1.1);">
              </div>
            </div>
          </div>
          <div class="p-2 border-top bg-light">
            <a class="btnCerrarSession dropdown-item d-flex align-items-center py-2 px-3 rounded text-danger fw-bold" href="#">
              <i class="fi fi-rr-power fs-5 me-3"></i>
              <span class="m-0">Cerrar Sesión</span>
            </a>
          </div>
        </ul>
      </div>
    </div>
  </div>
</nav>

<!-- MODAL PERFIL [ COMIENZO ] -->
<div class="modal fade modalPerfil" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
        <h5 class="modal-title" id="registrarUsuarioModalLabel">
          <i class="fas fa-user-plus me-2"></i> Información Personal
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form autocomplete="off" class="formularioPerfil validar" method="POST" action="" novalidate>
        <input type="hidden" name="accion" value="actualizar">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3 form-group">
                <label for="cedula" class="form-label">Cédula</label>
                <input name="cedula_usuario" type="text" class="form-control" readonly>
              </div>
              <div class="mb-3 form-group">
                <label for="nombre" class="form-label">Nombre de Usuario</label>
                <input type="text"
                  class="form-control" name="usuario_usuario" pattern="<?php echo regexUsuario ?>"
                  minlength="<?php echo minRegexUsuario ?>" maxlength="<?php echo maxRegexUsuario  ?>"
                  required>
              </div>
              <div class="mb-3 form-group">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="text" class="form-control" name="telefono_usuario"
                  pattern="<?php echo regexTelefono ?>" minlength="<?php echo minRegexTelefono  ?>"
                  maxlength="<?php echo maxRegexTelefono  ?>" required>
                <div class="form-text">Formato: 04121234567</div>
              </div>
              <div class="mb-3 form-group">
                <label for="correo" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" name="correo_usuario" pattern="<?php echo regexCorreo ?>"
                  minlength="<?php echo minRegexCorreo  ?>" maxlength="<?php echo maxRegexCorreo  ?>"
                  required>
              </div>
              <div class="mb-3 form-group">
                <label for="id_rol" class="form-label">Rol</label>
                <select class="form-control selectRoles" id="id_rol" name="id_rol"
                  pattern="<?php echo regexId ?>" minlength="<?php echo minRegexId  ?>"
                  maxlength="<?php echo maxRegexId  ?>" required>
                  <option value="">Seleccione un rol</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <h5 class="text-primary">Cambiar Contraseña</h5>
              <div class="form-text mb-3">
                Completa estos campos solo si deseas cambiar tu contraseña.
              </div>
              <div class="mb-3 form-group">
                <label for="clave_actual" class="form-label">Contraseña Actual</label>
                <input autocomplete="off" type="password" class="form-control" name="contrasena3_usuario"
                  pattern="<?php echo regexContrasena ?>" minlength="<?php echo minRegexContrasena ?>"
                  maxlength="<?php echo maxRegexContrasena  ?>">
              </div>
              <div class="mb-3 form-group">
                <label for="clave_nueva" class="form-label">Nueva Contraseña</label>
                <input autocomplete="off" type="password" class="form-control" name="contrasena1_usuario"
                  pattern="<?php echo regexContrasena ?>" minlength="<?php echo minRegexContrasena ?>"
                  maxlength="<?php echo maxRegexContrasena  ?>">
                <div class="form-text">Mínimo 6 caracteres.</div>
              </div>
              <div class="mb-3 form-group">
                <label for="confirmar_clave" class="form-label">Confirmar Nueva Contraseña</label>
                <input autocomplete="off" type="password" class="form-control" name="contrasena2_usuario"
                  pattern="<?php echo regexContrasena ?>" minlength="<?php echo minRegexContrasena ?>"
                  maxlength="<?php echo maxRegexContrasena  ?>">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
            <i class="fas fa-save me-2"></i> Guardar cambios
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- MODAL PERFIL [ FIN ] -->

<!-- OFFCANVAS CARRITO -->
<div class="panelCotizacionPedido offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas"
  aria-labelledby="cartOffcanvasLabel">
  <div class="offcanvas-header bg-light border-bottom">
    <h5 class="offcanvas-title fw-bold text-dark d-flex align-items-center" id="cartOffcanvasLabel">
      <i class="fi fi-rr-shopping-cart text-warning fs-3 me-2"></i> Pedido Actual
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body d-flex flex-column p-0">
    <div class="bg-white p-3 border-bottom d-flex justify-content-between align-items-center shadow-sm">
      <span class="text-muted small fw-bold">MOSTRAR TOTAL EN:</span>
      <div data-nro_btn="1" class="botonera btn-group btn-group-sm rounded-pill shadow-sm" role="group">
        <button type="button" class="btnTipoPago btn btn-dark text-white active fw-bold px-3"
          data-tipo_pago="bs">Bs</button>
        <button type="button" class="btnTipoPago btn btn-outline-secondary text-dark  px-3 border-0 bg-light"
          data-tipo_pago="usd">USD</button>
      </div>
    </div>
    <div class="divItemsPedido cart-items flex-grow-1 overflow-auto p-3 ">
      <div class="carritoVacio d-none flex-column align-items-center justify-content-center text-center py-5 px-3"
        style="min-height: 200px;">
        <div class="mb-3" style="font-size: 3.5rem; opacity: 0.18; color: #4e54c8;">
          <i class="fi fi-rr-shopping-cart-add"></i>
        </div>
        <h6 class="fw-bold text-dark mb-1">Tu pedido está vacío</h6>
        <p class="text-muted small mb-0">Agrega productos del catálogo para comenzar tu pedido.</p>
      </div>
      <div class="moldeItemPedido d-none card border-0 shadow-sm mb-3 position-relative">
        <button
          class="btnEliItemPedido btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2 rounded-circle"
          title="Eliminar del pedido">
          <i class="fi fi-rr-trash"></i>
        </button>
        <div class="card-body p-3">
          <div class="d-flex mb-3 align-items-center">
            <img src="<?php echo APP_URL ?>src/assets/images/producto_default.png" class="rounded border imagenItemPedido">
            <div class="ms-3 pe-4 flex-grow-1">
              <h6 class="mb-1 fw-bold text-dark text-truncate nombreItem" style="max-width: 150px;">Cloro Común</h6>
              <div class="d-flex flex-column lh-sm">
                <small class="text-muted">
                  <span class="fw-bold">Base:</span>
                  <span class="precioBaseItem">36.50</span>
                  <span class="signoPrecio">Bs</span>
                </small>
                <small class="text-success">
                  <span class="fw-bold">Mayor (-10%):</span>
                  <span class="precioMayorItem">32.85</span>
                  <span class="signoPrecio">Bs</span>
                </small>
              </div>
            </div>
          </div>
          <div class="d-flex justify-content-between align-items-center bg-light rounded p-2">
            <div class="d-flex align-items-center bg-white rounded border border-light shadow-sm px-1">
              <button class="btn btn-sm text-muted p-1 px-2 btnResItemPedido">
                <i class="fi fi-rr-minus"></i>
              </button>
              <span class="fw-bold mx-2 cantidadItemCarrito">1</span>
              <button class="btn btn-sm text-muted p-1 px-2 btnSumItemPedido">
                <i class="fi fi-rr-plus"></i>
              </button>
            </div>
            <div class="text-end divSubTotalItemPedido">
              <small class="text-muted d-block lh-1">Subtotal</small>
              <span class="fw-bold text-dark fs-6 lh-1 d-flex">
                <div class="subTotalBase precioDescuento me-3">
                  <span class="cantidadSubTotalBase">33.1</span>
                  <span class="signoPrecio">Bs</span>
                </div>
                <div class="subTotalDescuento">
                  <span class="cantidadSubTotalDescuento">36.50</span>
                  <span class="signoPrecio">Bs</span>
                </div>
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="depositoDetallesPedido">
      </div>
    </div>
    <div class="contenedorTotalPagar bg-white p-4 border-top shadow-lg">

      <div class="descuentoPedidoPorMayor d-flex justify-content-between mb-2">
        <span class="text-muted">Descuento por Mayor:</span>
        <div class="d-flex">
          <span class="cantidadDescuentoPedidoPorMayor text-success fw-bold">0.00</span>
          <span class="signoPrecio text-success fw-bold">Bs</span>
        </div>
      </div>

      <div class="totalesPedido d-flex justify-content-between align-items-center mb-4">
        <span class="fs-5 fw-bold text-dark">TOTAL:</span>
        <div class="text-end">
          <span class="d-flex lh-1">
            <div class="totalBase precioDescuento fs-5 me-3">
              <span class="cantidadTotalBase">33.1</span>
              <span class="signoPrecio">Bs</span>
            </div>
            <div class="totalDescuento fw-bold text-dark fs-4 ">
              <span class="cantidadTotalDescuento">36.50</span>
              <span class="signoPrecio">Bs</span>
            </div>
          </span>
        </div>
      </div>

      <button
        class="
        btnProcesarPedido
        btn 
        btn-dark
        w-100
        rounded-pill 
        py-3 
        fw-bold 
        shadow 
        position-relative 
        overflow-hidden 
        ">
        <span class="position-relative z-1 d-flex justify-content-center align-items-center">
          Procesar Pedido
          <i class="fi fi-rr-arrow-right ms-2 mt-1"></i>
        </span>
      </button>
    </div>
  </div>
</div>
<!-- OFFCANVAS CARRITO -->

<!-- MAPA DE ENVÍO DEL PEDIDO [ COMIENZO ] -->
<div class="modal fade" id="modalPagoUbicacion" tabindex="-1" aria-labelledby="modalPagoUbicacionLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-xl modal-fullscreen-sm-down modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg pago-modal-content">

      <!-- Header con Step Indicator -->
      <div class="modal-header border-0 pb-0 pago-modal-header">
        <div class="d-flex flex-column w-100">
          <div class="d-flex align-items-center mb-3">
            <div class="pago-step-icon me-3">
              <i class="fi fi-rr-map-marker-home"></i>
            </div>
            <div>
              <h5 class="modal-title fw-bold mb-0" id="modalPagoUbicacionLabel">Dirección de Envío</h5>
              <small class="text-white opacity-75">Paso 1 de 2 · Ubica tu dirección exacta en el mapa</small>
            </div>
            <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <!-- Step Progress Bar -->
          <div class="pago-progress-bar">
            <div class="pago-progress-fill" style="width: 50%;"></div>
          </div>
        </div>
      </div>

      <div class="modal-body p-0">
        <div class="px-4 pt-3 pb-2">
          <div class="pago-info-box align-items-center gap-2">
            <i class="fi fi-rr-info text-primary fs-5"></i>
            <span class="small">Puedes arrastrar el marcador para ajustar tu ubicación con precisión, o usar el botón de localización automática.</span>
          </div>
        </div>

        <!-- Mapa Leaflet -->
        <div id="contenedorMapaPedido" class="divMapaPago"></div>

        <!-- Dirección y Detalles de Envío -->
        <div class="px-4 py-3">
          <div class="mb-3">
            <label class="campoFinoForm">Dirección de Envío</label>
            <div class="input-group pago-input-group">
              <span class="input-group-text pago-input-icon">
                <i class="fi fi-rr-map-marker"></i>
              </span>
              <input type="text" id="inputDireccionEnvio" class="form-control pago-input" placeholder="Ubica tu dirección en el mapa..." readonly>
            </div>
          </div>
          <div class="row g-2 camposExpDireccion">
            <div class="col-md-4">
              <label class="campoFinoForm">Precio x KM</label>
              <div class="d-flex">
                <div class="input-group pago-input-group shadow-none border">
                  <span class="input-group-text pago-input-icon border-0 bg-transparent ps-2 pe-1">
                    <i class="fi fi-rr-money-bill-wave fs-6 px-2"></i>
                  </span>
                  <input type="text" id="inputPrecioPorKm" class="form-control pago-input border-0 bg-transparent ps-1" placeholder="0.00" readonly>
                  <span class="signoPrecio input-group-text pago-input-icon border-0 bg-transparent fs-5 pe-3">
                    Bs
                  </span>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <label class="campoFinoForm">Distancia</label>
              <div class="input-group pago-input-group shadow-none border">
                <span class="input-group-text pago-input-icon border-0 bg-transparent ps-2 pe-1">
                  <i class="fi fi-rr-route fs-6 px-2"></i>
                </span>
                <input type="text" id="inputKilometrosTotales" class="form-control pago-input border-0 bg-transparent ps-1" placeholder="0.00 km" readonly>
                <span class="input-group-text pago-input-icon border-0 bg-transparent ps-2 pe-1">
                  <i class="fs-6 px-3">Km</i>
                </span>
              </div>
            </div>
            <div class="col-md-4">
              <label class="campoFinoForm text-primary">Subtotal Envío</label>
              <div class="input-group pago-input-group shadow-none border bg-light">
                <span class="input-group-text pago-input-icon border-0 bg-transparent ps-2 pe-1">
                  <i class="fi fi-rr-truck-side fs-6 px-2 text-primary"></i>
                </span>
                <input type="text" id="inputSubtotalEnvio" class="form-control pago-input border-0 bg-transparent ps-1 fw-bold text-primary" placeholder="0.00" readonly>
                <span class="signoPrecio input-group-text pago-input-icon border-0 bg-transparent fs-5 pe-3">
                  Bs
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer border-0 pago-modal-footer d-flex justify-content-between">
        <button type="button" class="btnSelUbiActualPedido btn pago-btn-secondary">
          <i class="fi fi-rr-crosshairs me-2"></i>
          Mi Ubicación Actual
        </button>
        <button type="button" class="btn pago-btn-primary"
          data-bs-toggle="modal" data-bs-target="#modalPagoDetalles">
          Siguiente
          <i class="fi fi-rr-arrow-right ms-2"></i>
        </button>
      </div>
    </div>
  </div>
</div>
<!-- MAPA DE ENVÍO DEL PEDIDO [ FIN ] -->

<!-- MAPA DE PAGO DEL PEDIDO [ COMIENZO ] -->
<div class="modal fade" id="modalPagoDetalles" tabindex="-1" aria-labelledby="modalPagoDetallesLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-xl modal-fullscreen-sm-down modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg pago-modal-content">
      <div class="modal-header border-0 pb-0 pago-modal-header">
        <div class="d-flex flex-column w-100">
          <div class="d-flex align-items-center mb-3">
            <div class="pago-step-icon me-3">
              <i class="fi fi-rr-credit-card"></i>
            </div>
            <div>
              <h5 class="modal-title fw-bold mb-0" id="modalPagoDetallesLabel">Detalles del Pago</h5>
              <small class="text-white opacity-75">Paso 2 de 2 · Especifica cómo realizarás el pago</small>
            </div>
            <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="pago-progress-bar">
            <div class="pago-progress-fill w-100"></div>
          </div>
        </div>
      </div>
      <div class="modal-body px-4 py-3">
        <div class="pago-totales-card mb-4">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
              <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="pago-total-item">
                  <span class="pago-total-label d-flex">
                    <i class="fi fi-rr-box-open me-2 fs-6"></i>
                    Productos
                  </span>
                  <div class="d-flex">
                    <span class="totalItemsPedido me-2">0.00</span>
                    <span class="signoPrecio" id="pagoResumenMonedaProductos">Bs</span>
                  </div>
                </div>
                <div class="pago-total-divider">+</div>
                <div class="pago-total-item ">
                  <span class="pago-total-label d-flex">
                    <i class="fi fi-rr-truck-side me-2 fs-6"></i>
                    Delivery
                  </span>
                  <div class="d-flex">
                    <span class="totalDeliveryPedido me-2" id="pagoResumenDelivery">5.00</span>
                    <span class="signoPrecio">Bs</span>
                  </div>
                </div>
                <div class="pago-total-divider">=</div>
                <div class="pago-total-item pago-total-grand">
                  <span class="pago-total-label fw-bold d-flex">
                    <i class="fi fi-br-check me-2 fs-6"></i>
                    TOTAL
                  </span>
                  <div class="d-flex">
                    <span class="sumaTotalPedido fw-bold me-2">5.00</span>
                    <span class="signoPrecio fw-bold">Bs</span>
                  </div>
                </div>
                <div class="pago-total-divider">-</div>
                <div class="pago-total-item pago-total-grand">
                  <span class="pago-total-label fw-bold d-flex">
                    <i class="fi fi-br-money-bill-wave me-2 fs-6"></i>
                    CANCELADO
                  </span>
                  <div class="d-flex">
                    <span class="canceladoTotalPedido fw-bold me-2">0.00</span>
                    <span class="signoPrecio fw-bold">Bs</span>
                  </div>
                </div>
                <div class="pago-total-divider">=</div>
                <div class="pago-total-item pago-total-grand">
                  <span class="pago-total-label fw-bold d-flex">
                    <i class="fi fi-br-exclamation me-2 fs-6"></i>
                    RESTANTE
                  </span>
                  <div class="d-flex">
                    <span class="restanteTotalPedido fw-bold me-2">0.00</span>
                    <span class="signoPrecio fw-bold">Bs</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="botonera btn-group btn-group-sm rounded-pill shadow-sm" data-nro_btn="2" role="group">
              <button type="button" class="btnTipoPago btn btn-dark text-white active fw-bold px-3"
                data-tipo_pago="bs">Bs</button>
              <button type="button" class="btnTipoPago btn btn-outline-secondary text-dark  px-3 border-0 bg-light"
                data-tipo_pago="usd">USD</button>
            </div>
          </div>
          <div class="infoPagoBCV mt-2" id="pagoInfoBcv">
            <div class="d-flex">
              <i class="fi fi-rr-bank me-2"></i>
              <div class="d-block">
                Tasa BCV: <strong class="pagoBcvTasa">13.00</strong> Bs/USD · <a href="https://www.bcv.org.ve/" target="_blank" class="text-white opacity-75">ver en BCV</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Plantilla detalle -->
        <div class="plantillaDetallePago d-none">
          <div class="pago-detalle-header d-flex align-items-center justify-content-between mb-3">
            <span class="pago-detalle-numero fw-bold d-flex">
              <i class="fi fi-rr-receipt me-2"></i>
              Detalle de Pago #
              <span class="nroDetalle">1</span>
            </span>
            <button type="button" class="btn btn-sm pago-btn-eliminar-detalle d-none btnEliminarDetallePago">
              <i class="fi fi-rr-trash me-1"></i> Eliminar
            </button>
          </div>
          <div class="row g-3">
            <!-- Método de Pago -->
            <div class="col-lg-6">
              <label class="campoFinoForm">Método de Pago</label>
              <div class="input-group pago-input-group">
                <span class="input-group-text pago-input-icon"><i class="fi fi-rr-credit-card"></i></span>
                <select class="form-select pago-input selectMetodoPagoPedido" name="pagos-[INDICES]-id_metodo_pago" required>
                  <option value="">Selecciona un método</option>
                  <option value="1">Transferencia</option>
                  <option value="2">Pago Móvil</option>
                  <option value="3">Zelle</option>
                  <option value="4">Zinli</option>
                  <option value="5">Binance</option>
                  <option value="5">Efectivo</option>
                </select>
              </div>
            </div>
            <!-- Monto -->
            <div class="col-lg-6">
              <label class="campoFinoForm">Monto Pagado</label>
              <div class="input-group pago-input-group shadow-none border">
                <span class="input-group-text pago-input-icon border-0 ps-2 pe-1">
                  <i class="fi fi-rr-coins fs-6 px-2"></i>
                </span>
                <input
                  type="text"
                  class="inputMontoPagoPedido dinero form-control pago-input border-0 bg-transparent ps-2 fw-bold text-primary"
                  name="pagos-[INDICES]-monto_pago"
                  placeholder="0.00"
                  pattern="<?php echo regexPrecioFront ?>"
                  minlength="<?php echo minRegexPrecioFront ?>"
                  maxlength="<?php echo maxRegexPrecioFront ?>"
                  required>
                <span class="signoPrecio input-group-text pago-input-icon border-0 bg-transparent ps-1 pe-3">Bs</span>
              </div>
            </div>
            <!-- Moneda -->
            <div class="col-lg-4 d-none">
              <label class="campoFinoForm">Moneda</label>
              <div class="input-group pago-input-group">
                <span class="input-group-text pago-input-icon"><i class="fi fi-rr-dollar"></i></span>
                <select class="form-select pago-input selectMonedaPagoPedido" name="pagos-[INDICES]-id_moneda" required>
                  <option value="">Selecciona moneda</option>
                  <option value="1">Dólares (USD)</option>
                  <option value="2">Bolívares (Bs)</option>
                  <option value="3">Euros (EUR)</option>
                  <option value="4">Yuanes (YU)</option>
                </select>
              </div>
            </div>
            <!-- Referencia -->
            <div class="col-lg-4 d-none">
              <label class="campoFinoForm">Número de Referencia</label>
              <div class="input-group pago-input-group">
                <span class="input-group-text pago-input-icon">
                  <i class="fi fi-rc-hashtag"></i>
                </span>
                <input
                  disabled
                  type="text"
                  class="form-control pago-input inputReferenciaPagoPedido fw-bold text-primary"
                  name="pagos-[INDICES]-referencia_pago"
                  placeholder="Ej. 1234567890"
                  pattern="<?php echo regexCantidadItem ?>"
                  minlength="<?php echo minRegexCantidadItem ?>"
                  maxlength="<?php echo maxRegexCantidadItem ?>"
                  required>
              </div>
            </div>
            <!-- Banco Emisor -->
            <div class="col-lg-4 d-none">
              <label class="campoFinoForm">Banco Emisor</label>
              <div class="input-group pago-input-group">
                <span class="input-group-text pago-input-icon"><i class="fi fi-rr-send-money"></i></span>
                <select disabled class="form-select pago-input selectBancoEmisorPagoPedido" name="pagos-[INDICES]-id_banco_emisor" required>
                  <option value="">Banco del cliente</option>
                  <option value="1">Banesco</option>
                  <option value="2">BBVA Provincial</option>
                  <option value="3">Mercantil</option>
                  <option value="4">Banco de Venezuela</option>
                  <option value="5">BNC</option>
                  <option value="6">Bicentenario</option>
                  <option value="7">Venezolano de Crédito</option>
                </select>
              </div>
            </div>
            <!-- Banco Receptor -->
            <div class="col-lg-4 d-none">
              <label class="campoFinoForm">Banco Receptor</label>
              <div class="input-group pago-input-group">
                <span class="input-group-text pago-input-icon"><i class="fi fi-rr-bank"></i></span>
                <select disabled class="form-select pago-input selectBancoReceptorPagoPedido" name="pagos-[INDICES]-id_banco_receptor" required>
                  <option value="">Banco de la empresa</option>
                  <option value="1">Banesco</option>
                  <option value="2">BBVA Provincial</option>
                  <option value="3">Mercantil</option>
                  <option value="4">Banco de Venezuela</option>
                  <option value="5">BNC</option>
                  <option value="6">Bicentenario</option>
                  <option value="7">Venezolano de Crédito</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <!-- Contenedor de los detalles -->
        <div id="contenedorDetallesPago">
        </div>

        <!-- Boton mas detalles -->
        <button type="button" class="d-flex justify-content-center align-items-center btn btnAggDetallePagoPedido w-100 my-3">
          <i class="fi fi-rr-plus-small me-2 fs-5"></i>
          Agregar otro detalle de pago
        </button>

        <div class="areaComprobantesPago">
          <label class="campoFinoForm mb-2 d-flex">
            <i class="fi fi-rr-picture me-2 text-primary"></i>
            Comprobante(s) de Pago
          </label>
          <label for="inputComprobantes" class="dropCapturesPago w-100">

            <div class="msjDropCapturesPago">
              <div class="pago-file-icon"><i class="fi fi-rr-upload"></i></div>
              <div class="pago-file-text">Arrastra tus comprobantes aquí o <span class="text-primary fw-bold">haz clic para seleccionar</span></div>
              <small class="text-muted">JPG, PNG, PDF · Puedes seleccionar múltiples archivos</small>
            </div>
            <div id="pagoComprobantesPreview" class="pago-files-preview mt-2"></div>
          </label>
          <input type="file" id="inputComprobantes" name="comprobantes_pago" accept="image/*" multiple class="d-none">
        </div>
      </div>
      <div class="modal-footer border-0 pago-modal-footer justify-content-between">
        <div class="d-flex gap-2">
          <button type="button" class="btn pago-btn-ghost"
            data-bs-toggle="modal" data-bs-target="#modalPagoUbicacion">
            <i class="fi fi-rr-arrow-left me-1"></i> Volver
          </button>
          <button type="button" class="btn pago-btn-danger" id="btnEliminarPedido">
            <i class="fi fi-rr-trash me-1"></i> Eliminar Pedido
          </button>
        </div>
        <button type="button" class="btn pago-btn-primary" id="btnConfirmarPedido">
          <i class="fi fi-rr-check me-1"></i> Confirmar Pedido
        </button>
      </div>
    </div>
  </div>
</div>
<!-- MAPA DE PAGO DEL PEDIDO [ FIN ] -->

<!-- MODAL ACTUALIZAR FOTO DE PERFIL [ COMIENZO ] -->
<div class="modal fade" id="modalActualizarFotoPerfil" tabindex="-1" aria-labelledby="modalActualizarFotoPerfilLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 1.5rem; overflow: hidden;">
      <div class="modal-header border-0 pb-0 justify-content-end p-3">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center pt-0 pb-4">
        <h4 class="fw-bold mb-4" id="modalActualizarFotoPerfilLabel">Imagen del registro</h4>
        <div class="position-relative mx-auto mb-4" style="width: 200px;">
          <span class="btnEliminarFotoPerfil" title="Eliminar foto">
            <i class="fi fi-ss-cross-circle"></i>
          </span>
          <div class="perfil-container-modal mx-auto" id="btnDispararInputFile">
            <img
              src="<?php echo APP_URL; ?>/src/assets/fotosModulos/usuarios/perfilDefaultUsuario.png"
              alt="Perfil Actual"
              class="perfil-img-modal shadow"
              id="previsualizacionFotoPerfilModal">
            <div class="perfil-overlay-modal d-flex flex-column align-items-center justify-content-center">
              <i class="fi fi-rr-camera fs-1 mb-2"></i>
              <span class="fw-bold">Actualizar</span>
            </div>
          </div>
        </div>
        <form autocomplete="off" class="formularioActualizarFotoPerfil" method="POST" enctype="multipart/form-data">
          <input type="hidden" class="inputAccionActFoto" name="accion" value="">
          <input type="hidden" id="nombreCampoIdRegistroFoto" name="">
          <input type="file" id="inputFotoPerfil" name="" accept="image/*" class="d-none">
          <div class="px-4">
            <p class="text-muted small mb-4">Haz clic en la imagen para seleccionar una nueva fotografía de tu galería.</p>
            <div class="d-grid gap-2">
              <button type="button" class="btn btn-primary py-3 fw-bold rounded-pill shadow-sm btnGuardarFotoPerfil" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                <i class="fi fi-rr-disk me-2"></i> Guardar Cambios
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- MODAL ACTUALIZAR FOTO DE PERFIL [ FIN ] -->