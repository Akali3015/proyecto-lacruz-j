<?php

use src\modelos\preguntasSeguridadModelo;
use src\config\inc\componentesModelo;
use src\modelos\accesosModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"])) {

  $accion = $_POST["accion"];
  $objeto = new preguntasSeguridadModelo();
  ob_clean();
  $resultado = [
    'icono' => 'error',
    'titulo' => 'Acción no reconocida'
  ];
  switch ($accion) {
    case "listar":
    case "seleccionarUno":
      $resultado = $objeto->seleccionarPreguntasSeguridad($_POST);
      break;
    case "registrar":
      $resultado = $objeto->registrarPreguntasSeguridad($_POST);
      break;
    case "actualizar":
      $resultado = $objeto->actualizarPreguntasSeguridad($_POST);
      break;
    case "eliminar":
      $resultado = $objeto->eliminarPreguntasSeguridad($_POST);
      break;
  }
  $objeto->DECORE($resultado);
  exit();
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
  $objAcceso = new accesosModelo();
  $v = $objAcceso->validarPermisos('preguntas-seguridad', 'ver');
  if ($v) $objAcceso->DECORE($v);

  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/preguntas-seguridad/preguntas-seguridad.php";
}
