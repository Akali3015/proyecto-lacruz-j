<?php
// 1. Requerimos el modelo
use src\modelos\categoriasProductosModelo;

// 2. Verificamos REST API (Solo entras por POST y estando logueado)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['cedula'])) {

  // Recibimos y decodificamos el cuerpo
  $rawData = file_get_contents('php://input');
  $datos = json_decode($rawData, true);
  if (!is_array($datos)) {
    $datos = [];
  }

  ob_clean();
  // Obtenemos las variables de la lista
  $accion = $datos["accion"] ?? $_POST['accion'] ?? "";
  $id = $datos["id_categoria_producto"] ?? $_POST['id_categoria_producto'] ?? "";
  $nombre = $datos['nombre_categoria'] ?? $_POST['nombre_categoria'] ?? "";
  $necesitan = $datos['necesitan_materias_primas'] ?? $_POST['necesitan_materias_primas'] ?? "0";

  // Instanciación del modelo 
  $objeto = new categoriasProductosModelo();

  switch ($accion) {
    case "listar":
      $resultado = $objeto->seleccionarCategoriaProducto();
      echo json_encode($resultado);
      exit();
    case "seleccionarUno":
      $resultado = $objeto->seleccionarCategoriaProducto($id);
      echo json_encode($resultado);
      exit();
    case "registrar":
      $resultado = $objeto->registrarCategoriaProducto($nombre, $necesitan);
      echo json_encode($resultado);
      exit();
    case "actualizar":
      $resultado = $objeto->actualizarCategoriaProducto($id, $nombre, $necesitan);
      echo json_encode($resultado);
      exit();
    case "eliminar":
      $resultado = $objeto->eliminarCategoriaProducto($id);
      echo json_encode($resultado);
      exit();
    default:
      echo json_encode(["error" => "Error: Acción no reconocida"]);
      exit();
  }
}
// Para el renderizado de pantalla
elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
  $objComponentes = new \src\config\inc\componentesModelo();
  require_once "src/config/inc/header.php";
  echo $objComponentes->sidebar();
  require_once "src/vistas/categoriasProductos/categoriasProductos.php";
}
