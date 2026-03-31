<?php

use src\modelos\productosModelo;
use src\config\inc\componentesModelo;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['cedula'])) {

  $datos = file_get_contents('php://input');
  $datos = json_decode($datos, true);
  $accion = $datos["accion"] ?? $_POST['accion'] ?? "";

  $id = $datos["id_producto"] ?? $_POST['id_producto'] ?? "";
  $idUnidadMedida = $datos['id_unidad_medida'] ?? "";
  $idCategoria = $datos['id_categoria'] ?? "";
  $nombre = $datos['nombre_producto'] ?? "";
  $precioDivisas = $datos['precio_producto_divisas'] ?? "";
  $precioBCV = $datos['precio_producto_bcv'] ?? "";
  $stock = $datos['stock_producto'] ?? "0";
  $presentaciones = $datos['presentaciones'] ?? [];
  $materiasPrimas = $datos['materias_primas'] ?? [];

  $objeto = new productosModelo();
  ob_clean();
  switch ($accion) {
    case "listar":
      $resultado = $objeto->seleccionarProductos();
      echo json_encode($resultado);
      exit();

    case "seleccionarUno":
      $resultado = $objeto->seleccionarProductos($id);
      echo json_encode($resultado);
      exit();
    case "registrar":
      $resultado = $objeto->registrarProductos(
        $idUnidadMedida,
        $nombre,
        $precioDetal,
        $precioMayor,
        $stock,
        $fabricado,
        $presentaciones,
        $materiasPrimas
      );
      echo json_encode($resultado);
      exit();

    case "actualizar":
      $resultado = $objeto->actualizarProductos(
        $id,
        $idUnidadMedida,
        $nombre,
        $precioDetal,
        $precioMayor,
        $stock,
        $fabricado,
        $presentaciones,
        $materiasPrimas
      );
      echo json_encode($resultado);
      exit();

    case "eliminar":
      $resultado = $objeto->eliminarProductos($id);
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
  require_once "src/vistas/productos/productos.php";
}
