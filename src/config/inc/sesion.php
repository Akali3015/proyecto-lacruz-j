<?php
session_name(APP_SESSION_NAME);
session_set_cookie_params([
  'lifetime' => 0,
  'path' => '/',
  'domain' => '',
  'secure' => false,
  'httponly' => true,
  'samesite' => 'Lax'
]);
session_start();
header_remove("X-Powered-By");
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://unpkg.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn-uicons.flaticon.com https://unpkg.com; img-src 'self' data: https://*.tile.openstreetmap.org https://tile.openstreetmap.org https://www.openstreetmap.org https://unpkg.com; font-src 'self' https://fonts.gstatic.com https://cdn-uicons.flaticon.com; connect-src 'self' https://api.tomtom.com https://router.project-osrm.org https://nominatim.openstreetmap.org https://project-osrm.org https://api-the-vina-node.onrender.com wss://api-the-vina-node.onrender.com;frame-ancestors 'self'; form-action 'self';base-uri 'self';");
header("X-Content-Type-Options: nosniff");
ob_start();
