<div class="row">
  <input type="hidden" class="nombreVista" value="permisos">
  <!-- [LISTA DE PERMISOS] COMIENZO -->
  <div class="main-content" id="mainContent">
    <dvi class="container-fluid py-4">
      <div class="row mb-4">
        <div class="col-md-6">
          <h2 class="mb-0">Gestionar Permisos</h2>
        </div>
      </div>
      <div class="card">
        <div class="card-body">
          <div class="row g-4 contenedorPanel">
            <div class="col-lg-3 col-sm-12">
              <ul class="selectRolesPermisos nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
              </ul>
            </div>
            <div class="col-lg-9 col-sm-12">
              <div class="col-12 mb-4">
                <ul class="nav nav-tabs profile-tabs" id="myTab" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="profile-tab-1" data-bs-toggle="tab" href="#profile-1" role="tab" aria-selected="true">
                      <i class="fi fi-rs-key me-2"></i>
                      Permisos generales
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="profile-tab-2" data-bs-toggle="tab" href="#profile-2" role="tab" aria-selected="true">
                      <i class="fi fi-bs-icon-star me-2"></i>
                      Permisos Especiales
                    </a>
                  </li>
                </ul>
              </div>
              <div class="col-12">
                <div class="tab-content" id="v-pills-tabContent">
                  <div class="tab-pane fade show active" id="profile-1" role="tabpanel">
                    <div class="text-center">
                      <div class="row">
                        <div class="table-responsive">
                          <table class="listaPermisos table table-striped table-bordered text-center">
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="tab-pane fade show" id="profile-2" role="tabpanel">
                    <div class="text-center">
                      <div class="row containerPermEspe">
                        <div class="input-group">
                          <span class="form-control">Ver detalles de promociones</span>
                          <div class="input-group-text">
                            <div class="form-check form-switch p-0">
                              <input class="m-0 form-check-input h5 position-relative" type="checkbox" role="switch" checked="">
                            </div>
                          </div>
                          <span class="form-control">Ver detalles de ventas</span>
                          <div class="input-group-text">
                            <div class="form-check form-switch p-0">
                              <input class="m-0 form-check-input h5 position-relative" type="checkbox" role="switch" checked="">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </dvi>
  </div>
  <!-- [LISTA DE PERMISOS] FIN -->
</div>