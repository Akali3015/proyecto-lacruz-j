<?php

use src\modelos\monedasModelo;
use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"])) {
  $accion = $_POST["accion"];
  $objeto = new monedasModelo();

  ob_clean();
    $resultado = [
    'icono' => 'error',
    'titulo' => 'Acción no reconocida'
  ];
  switch ($accion) {
    case "listar":
      $resultado = $objeto->seleccionarMonedas($_POST);
      break;
    case "listarCambios":
      $resultado = $objeto->seleccionarCambiosMonedas();
      break;
    case "seleccionarUno":
      $resultado = $objeto->seleccionarMonedas($_POST);
      break;
    case "registrar":
      $resultado = $objeto->registrarMonedas($_POST);
      break;
    case "actualizar":
      $resultado = $objeto->actualizarMonedas($_POST);
      break;
    case "actualizarValor":
      $resultado = $objeto->actualizarMonedas($_POST);
      break;
    case "eliminar":
      $resultado = $objeto->eliminarMonedas($_POST);
      break;
  }
  $objeto->DECORE($resultado);
  exit();
} else {
  $archivo = "src/vistas/monedas/monedas.php";
  if (isset($url2) && $url2 != "") {
    if (is_file("src/vistas/monedas/" . $url2 . ".php")) {
      $archivo = "src/vistas/monedas/" . $url2 . ".php";
      $_SESSION['vistaActual'] = $url2;
    }
  }
  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once $archivo;
}
