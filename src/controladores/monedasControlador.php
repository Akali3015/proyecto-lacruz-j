<?php

use src\modelos\monedasModelo;
use src\config\inc\componentesModelo;
use src\modelos\accesosModelo;

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
  $objAcceso = new accesosModelo();
  $vistaSolicitada = $url2 ?? 'monedas';
  if ($vistaSolicitada === 'cambios-monedas') {
    $v = $objAcceso->validarPermisos('monedas', 'ver historial de cambio de las divisas');
    if ($v) $objAcceso->DECORE($v);

    if (is_file("src/vistas/monedas/cambios-monedas.php")) {
      $archivo = "src/vistas/monedas/cambios-monedas.php";
      $_SESSION['vistaActual'] = 'cambios-monedas';
    }
  } else {
    $v = $objAcceso->validarPermisos('monedas', 'ver');
    if ($v) $objAcceso->DECORE($v);

    if ($vistaSolicitada !== 'monedas' && is_file("src/vistas/monedas/" . $vistaSolicitada . ".php")) {
      $archivo = "src/vistas/monedas/" . $vistaSolicitada . ".php";
      $_SESSION['vistaActual'] = $vistaSolicitada;
    }
  }
  
  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once $archivo;
}