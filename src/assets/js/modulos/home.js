import './global.js'
import { driverAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"

let plantillaTarjetaHome = `
<div class="col-md-4 col-lg-3">
  <a href="<?php echo APP_URL ?>clientes" style="text-decoration: none; color: inherit;">
    <div class="card step-card h-100">
      <div class="card-body text-center p-4">
        <div class="step-icon mx-auto mb-3">
          <i class="fi fi-rr-users ix3 text-dark"></i>
        </div>
        <h5 class="card-title fw-bold">Clientes</h5>
        <p class="card-text">Gestiona y registra los clientes asociados</p>
      </div>
    </div>
  </a>
</div>
`;
