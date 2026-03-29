<?php

use src\modelos\permisosModelo;
use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"])) {
  $accion = $_POST["accion"];
  ob_clean();

  $accion = $_POST["accion"] ?? '';
  $idRol = $_POST['id_rol'] ?? '';
  $idModulo = $_POST['id_modulo'] ?? "";
  $idPermiso = $_POST['id_permiso'] ?? "";
  $cambio = $_POST['cambio'] ?? "";

  $modeloPermisos = new permisosModelo();

  switch ($accion) {
    case "listar":
      $resultado = $modeloPermisos->listarPermisos($idRol);
      echo json_encode($resultado);
      exit();
    case "listarPorRol":
      $resultado = $modeloPermisos->seleccionarPermisosPorRol();
      echo json_encode($resultado);
      exit();
    case "actualizar":
      $resultado = $modeloPermisos->actualizarPermisos($idRol, $idModulo, $idPermiso, $cambio);
      echo json_encode($resultado);
      exit();
    default:
      echo json_encode(["error" => "Acción no reconocida"]);
      exit();
  }
} else {
  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/permisos/permisos.php";
}
