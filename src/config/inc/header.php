<nav class="headerPrincipal navbar navbar-expand-lg noselec">
  <div class="container-fluid">
    <div class="d-flex align-items-center">
      <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fi fi-br-menu-burger"></i>
      </button>
      <div class="iconoNombre d-flex align-items-center">
        <img src="/proyecto-lacruz-j/src/assets/images/logo2.png" class="navbar-logo" alt="logo">
        <a class="navbar-brand" href="index.php?c=loginControlador&m=home">J.Lacruz</a>
      </div>
    </div>
    <div class="navbar-nav d-flex flex-row">
      <div class="dropdown custom-dropdown me-3 d-flex justify-content-center align-items-center">
        <a href="#" data-bs-toggle="dropdown" data-bs-target="dropdown-notificaciones" class="dropdown-link" aria-haspopup="true" aria-expanded="false">
          <span class="wrap-icon icon-notifications">
            <i class="fi fi-rs-bell fs-4"></i>
          </span>
          <span class="number fs-6">5</span>
        </a>
        <div class="dropdown-notificaciones dropdown-menu">
          <div class="title-wrap d-flex align-items-center justify-content-between">
            <h3 class="title mb-0 ms-3 fs-5">Notificaciones</h3>
            <a href="#" class="btnMTLNCL small ml-auto me-3 p-2">Marcar todas como leídas</a>
          </div>

          <ul class="custom-notifications">
            <li>
              <a href="#" class="d-flex align-items-center">
                <div class="img me-3">
                  <img src="<?php echo APP_URL ?>src/assets/images/errorIcono.png" alt="Image" class="img-fluid">
                </div>
                <div class="text">
                  <strong>Nueva venta registrada</strong>
                  <p class="p-0 m-0">Se ha registrado una nueva venta</p>
                </div>
              </a>
            </li>
            <li>
              <a href="#" class="d-flex">
                <div class="img mr-3">
                  <img src="<?php echo APP_URL ?>src/assets/images/logo.png" alt="Image" class="img-fluid">
                </div>
                <div class="text">
                  <strong>Devin Richards</strong> mentioned you in her comment on Invoices 2 days ago
                </div>
              </a>
            </li>
            <li>
              <a href="#" class="d-flex">
                <div class="img mr-3">
                  <img src="<?php echo APP_URL ?>src/assets/images/logo.png" alt="Image" class="img-fluid">
                </div>
                <div class="text">
                  <strong>Devin Richards</strong> mentioned you in her comment on Invoices 2 days ago
                </div>
              </a>
            </li>
            <li>
              <a href="#" class="d-flex">
                <div class="img mr-3">
                  <img src="<?php echo APP_URL ?>src/assets/images/logo.png" alt="Image" class="img-fluid">
                </div>
                <div class="text">
                  <strong>Devin Richards</strong> mentioned you in her comment on Invoices 2 days ago
                </div>
              </a>
            </li>
            <li>
              <a href="#" class="d-flex">
                <div class="img mr-3">
                  <img src="<?php echo APP_URL ?>src/assets/images/logo.png" alt="Image" class="img-fluid">
                </div>
                <div class="text">
                  <strong>Devin Richards</strong> mentioned you in her comment on Invoices 2 days ago
                </div>
              </a>
            </li>
            <li>
              <a href="#" class="d-flex">
                <div class="img mr-3">
                  <img src="<?php echo APP_URL ?>src/assets/images/logo.png" alt="Image" class="img-fluid">
                </div>
                <div class="text">
                  <strong>Devin Richards</strong> mentioned you in her comment on Invoices 2 days ago
                </div>
              </a>
            </li>
            <li>
              <a href="#" class="d-flex">
                <div class="img mr-3">
                  <img src="<?php echo APP_URL ?>src/assets/images/logo.png" alt="Image" class="img-fluid">
                </div>
                <div class="text">
                  <strong>Devin Richards</strong> mentioned you in her comment on Invoices 2 days ago
                </div>
              </a>
            </li>
            <li>
              <a href="#" class="d-flex">
                <div class="img mr-3">
                  <img src="<?php echo APP_URL ?>src/assets/images/logo.png" alt="Image" class="img-fluid">
                </div>
                <div class="text">
                  <strong>Devin Richards</strong> mentioned you in her comment on Invoices 2 days ago
                </div>
              </a>
            </li>
          </ul>
          <!-- <p class="text-center m-0 p-0"><a href="#" class="small">Ver todo</a></p> -->
        </div>
      </div>
      <div class="nav-item dropdown">
        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown"
          role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <div class="user-avatar">
            <i class="fas fa-user-circle"></i>
          </div>
          <span class="user-name ms-2">
            <?php echo $_SESSION['usuario']; ?>
          </span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end ">
          <li class="btnEditarPerfil" data-bs-toggle="modal"
            data-bs-target=".modalPerfil" value="<?php echo $_SESSION['cedula'] ?>"
            claseFormulario=".formularioPerfil">
            <a class="dropdown-item d-flex justify-content-center align-items-center" href="#">
              <i class="fi fi-br-circle-user fs-5 me-2"></i>
              <p class="m-0 fs-5">Mi Perfil</p>
            </a>
          </li>
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
      <form class="formularioPerfil validar" method="POST" action="" novalidate>
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
                <input type="password" class="form-control" name="contrasena3_usuario"
                  pattern="<?php echo regexContrasena ?>" minlength="<?php echo minRegexContrasena ?>"
                  maxlength="<?php echo maxRegexContrasena  ?>">
              </div>
              <div class="mb-3 form-group">
                <label for="clave_nueva" class="form-label">Nueva Contraseña</label>
                <input type="password" class="form-control" name="contrasena1_usuario"
                  pattern="<?php echo regexContrasena ?>" minlength="<?php echo minRegexContrasena ?>"
                  maxlength="<?php echo maxRegexContrasena  ?>">
                <div class="form-text">Mínimo 6 caracteres.</div>
              </div>
              <div class="mb-3 form-group">
                <label for="confirmar_clave" class="form-label">Confirmar Nueva Contraseña</label>
                <input type="password" class="form-control" name="contrasena2_usuario"
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