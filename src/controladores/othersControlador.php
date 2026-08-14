<?php

use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] == "GET") {
  $archivo = "src/vistas/others/";
  if (isset($url2) && $url2 != "") {
    
    if (is_file($archivo . $url2 . ".php")) {
      $archivo .= $url2 . ".php";
      $_SESSION['vistaActual'] = $url2;
    }
  }
  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once $archivo;
}
