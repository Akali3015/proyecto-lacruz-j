<input type="hidden" class="nombreVista" value="accesos">
<!-- [LISTA DE PERMISOS] COMIENZO -->
<div class="main-content px-4" id="mainContent">
  <div class="container-fluid py-4">
    <div class="row mb-4">
      <div class="col-md-6">
        <h2 class="mb-0">Gestionar Accesos</h2>
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
<!-- [LISTA DE PERMISOS] FIN -->
