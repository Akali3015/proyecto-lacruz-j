<?php

use src\modelos\clientesModelo;
use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && isset($_SESSION['cedula'])) {

  $accion = $_POST["accion"];
  $codigoRifCedula = $_POST['codigo_rif_cedula_cliente'] ?? "";
  $rifCedulaCliente = $_POST['rif_cedula_cliente'] ?? "";
  $rifCedulaCompleto = $codigoRifCedula . $rifCedulaCliente;
  $razon = $_POST['razon_social_cliente'] ?? "";
  $telefono = $_POST['telefono_cliente'] ?? "";
  $correo = $_POST['correo_cliente'] ?? "";
  $direccion = $_POST['direccion_cliente'] ?? "";

  $objeto = new clientesModelo();
  ob_clean();
  switch ($accion) {
    case 'listar':
      $resultado = $objeto->seleccionarCliente();
      echo json_encode($resultado);
      exit();
    case 'seleccionarUno':
      $resultado = $objeto->seleccionarCliente($rifCedulaCompleto);
      echo json_encode($resultado);
      exit();
    case 'registrar':
      $resultado = $objeto->registrarCliente($rifCedulaCompleto, $razon, $telefono, $correo, $direccion);
      echo json_encode($resultado);
      exit();
    case 'actualizar':
      $resultado = $objeto->actualizarCliente($rifCedulaCompleto, $razon, $telefono, $correo, $direccion);
      echo json_encode($resultado);
      exit();
    case 'eliminar':
      $resultado = $objeto->eliminarCliente($rifCedulaCompleto);
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
  require_once "src/vistas/clientes/clientes.php";
}
