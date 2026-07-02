<input type="hidden" class="nombreVista" value="accesos">
<link rel="stylesheet" href="/proyecto-lacruz-j/src/assets/css/accesos.css">

<!-- [LISTA DE PERMISOS] COMIENZO -->
<div class="main-content px-4" id="mainContent">
  <div class="container-fluid py-4">
    <div class="row mb-4">
      <div class="col-md-6">
        <h2 class="mb-0">Gestionar Accesos</h2>
      </div>
    </div>
    <div class="card contenedorPanel">
      <div class="card-body">
        <div class="row g-4">
          <div class="col-lg-3 col-sm-12">
            <ul class="selectRolesPermisos nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
            </ul>
          </div>
          <div class="col-lg-9 col-sm-12">
            <div class="col-12 mb-4">
              <div class="barraHerramientas d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="inputSelectorPaginas d-flex flex-grow-2">
                  <label class="d-flex">
                    Mostrar
                    <select
                      class="selectCantidadFilasPermisos form-select form-select-sm flex-grow-0 mx-2">
                      <option value="5">5</option>
                      <option value="10">10</option>
                      <option value="25">25</option>
                      <option value="50">50</option>
                      <option value="100">100</option>
                    </select>
                    registros
                  </label>
                </div>
                <input type="search" class="inputBusquedaPermisos form-control form-control-sm w-auto lex-grow-2 ps-2 mx-2 flex-grow-1" placeholder="Buscar rápido" aria-label="Buscar rápido">
                <button type="button" class="btnExpandirContraerPermisos btn btn-outline-primary btn-sm">Expandir</button>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12">
          <!-- Permisos Totales -->
          <div class="text-center">
            <div class="row containerPermEspe">
            </div>
          </div>
          <div class="row align-items-center">
            <div class="col-sm-12 col-md-5">
              <div class="textoMostrandoPermisos">Mostrando registros del 1 al 10 de un total de 36 registros</div>
            </div>
            <div class="col-sm-12 col-md-7 mt-4">
              <div class="d-flex justify-content-end">
                <ul class="paginadorPermisos pagination">
                </ul>
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