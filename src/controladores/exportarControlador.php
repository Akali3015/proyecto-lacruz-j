<?php

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['cedula'])) {
  http_response_code(403);
  header('Content-Type: application/json');
  echo json_encode(['icono' => 'error', 'titulo' => 'Acceso denegado']);
  exit;
}

$rol = strtolower($_SESSION['nombreRol'] ?? $_SESSION['rol'] ?? '');

if ($rol === 'cliente') {
  http_response_code(403);
  header('Content-Type: application/json');
  echo json_encode([
    'icono'  => 'error', 
    'titulo' => 'Acceso denegado', 
    'texto'  => 'No tienes permisos para exportar la base de datos.'
  ]);
  exit;
}

use src\modelos\exportarBDModelo;

try {
  $exportar = new exportarBDModelo();
  $filePath = $exportar->exportar();

  if (!file_exists($filePath)) {
    throw new Exception("No se pudo generar el archivo de exportación");
  }

  header('Content-Description: File Transfer');
  header('Content-Type: application/sql');
  header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
  header('Content-Length: ' . filesize($filePath));
  header('Cache-Control: no-cache, must-revalidate');
  header('Pragma: no-cache');
  header('Expires: 0');
  ob_clean();
  flush();
  readfile($filePath);
  unlink($filePath);
  exit;
} catch (Exception $e) {
  http_response_code(500);
  header('Content-Type: application/json');
  echo json_encode(['icono' => 'error', 'titulo' => 'Error', 'texto' => $e->getMessage()]);
  exit;
}
