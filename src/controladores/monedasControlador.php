<?php

use src\modelos\monedasModelo;
use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"])) {
  $accion = $_POST["accion"];

  $id = $_POST['id_moneda'] ?? "";
  $nombre = $_POST['nombre_moneda'] ?? "";
  $simbolo = $_POST['simbolo_moneda'] ?? "";
  $valor = $_POST['valor_moneda'] ?? "";

  $objeto = new monedasModelo();

  ob_clean();
  switch ($accion) {
    case "listar":
      $resultado = $objeto->seleccionarMonedas();
      echo json_encode($resultado);
      exit();
    case "listarCambios":
      $resultado = $objeto->seleccionarCambiosMonedas();
      echo json_encode($resultado);
      exit();
    case "seleccionarUno":
      $resultado = $objeto->seleccionarMonedas($id);
      echo json_encode($resultado);
      exit();
    case "registrar":
      $resultado = $objeto->registrarMonedas($nombre, $simbolo, $valor);
      echo json_encode($resultado);
      exit();
    case "actualizar":
      $resultado = $objeto->actualizarMonedas('completa', $id, $valor, $nombre, $simbolo);
      echo json_encode($resultado);
      exit();
    case "actualizarValor":
      $resultado = $objeto->actualizarMonedas('soloValor', $id, $valor);
      echo json_encode($resultado);
      exit();
    case "eliminar":
      $resultado = $objeto->eliminarMonedas($id);
      echo json_encode($resultado);
      exit();
    default:
      echo json_encode(["error" => "Acción no reconocida"]);
      exit();
  }
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
