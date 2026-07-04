<?php

use src\modelos\accesosModelo;
use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"])) {
  $accion = $_POST["accion"];
  ob_clean();

  $accion = $_POST["accion"] ?? '';
  $idRol = $_POST['id_rol'] ?? '';
  $idModulo = $_POST['id_modulo'] ?? "";
  $idPermiso = $_POST['id_permiso'] ?? "";
  $cambio = $_POST['cambio'] ?? "";

  $modeloPermisos = new accesosModelo();
  $resultado = [
    'icono' => 'error',
    'titulo' => 'Acción no reconocida'
  ];
  switch ($accion) {
    case "listar":
      $resultado = $modeloPermisos->listarPermisos($idRol);
      break;
    case "listarPorRol":
      $resultado = $modeloPermisos->seleccionarPermisosPorRol();
      break;
    case "actualizar":
      $resultado = $modeloPermisos->actualizarPermisos($idRol, $idModulo, $idPermiso, $cambio);
      break;
  }
  $modeloPermisos->DECORE($resultado);
  exit();
} else {
  $objAcceso = new accesosModelo();
  $v = $objAcceso->validarPermisos('accesos', 'ver');
  if ($v) $objAcceso->DECORE($v);

  $objComponentes = new componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/accesos/accesos.php";
}
