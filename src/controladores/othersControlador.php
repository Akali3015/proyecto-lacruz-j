<?php

use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] == "GET") {
  $archivo = "src/vistas/others/";
  if (isset($url1) && $url1 != "") {
    if (is_file($archivo . $url1 . ".php")) {
      $archivo .= $url1 . ".php";
      $_SESSION['vistaActual'] = $url1;
    }
  }
  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once $archivo;
}
