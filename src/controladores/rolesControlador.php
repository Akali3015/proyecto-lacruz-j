<?php

use src\modelos\rolesModelo;
use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {

  $accion = $_POST["accion"];
  $id = $_POST['id_rol'] ?? "";
  $nombre = $_POST['nombre_rol'] ?? "";
  $objeto = new rolesModelo();
  ob_clean();
  switch ($accion) {
    case "listar":
      $resultado = $objeto->seleccionarRoles();
      echo json_encode($resultado);
      exit();
    case "seleccionarUno":
      $resultado = $objeto->seleccionarRoles($id);
      echo json_encode($resultado);
      exit();
    case "registrar":
      $resultado = $objeto->registrarRoles($nombre);
      echo json_encode($resultado);
      exit();
    case "actualizar":
      $resultado = $objeto->actualizarRoles($id, $nombre);
      echo json_encode($resultado);
      exit();
    case "eliminar":
      $resultado = $objeto->eliminarRoles($id);
      echo json_encode($resultado);
      exit();
    default:
      echo json_encode(["error" => "Acción no reconocida"]);
      exit();
  }
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/roles/roles.php";
}
