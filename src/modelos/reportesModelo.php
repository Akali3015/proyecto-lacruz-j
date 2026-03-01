<?php

namespace src\modelos;

use src\config\connect\conexion;
use DateTime;
use Exception;
use PDO;
use FPDF;

class reportesModelo extends FPDF
{
  use traitModelo;
  private $nombreFiltro;
  private $pdo;

  public function __construct($orientation = 'P', $unit = 'mm', $size = 'A4')
  {
    $this->pdo = new conexion();
    $this->pdo = $this->pdo->getPdo();
    return parent::__construct($orientation, $unit, $size);
  }

  // Cabecera de página
  function Header()
  {
    //Encabezado
    $fechaActual = new DateTime();
    $fechaActual = $fechaActual->format('d-m-Y');
    $this->SetFont('Arial', 'B', 12);
    $this->Cell(30, 10);
    $this->Cell(135, 10, 'REPORTE', 0, 0, 'C');
    $this->Cell(30, 10, 'EMITIDO EL: ' . $fechaActual, 0, 0, 'R');
    $this->Ln(20);
  }
  // Pie de página
  function Footer()
  {
    $this->SetY(-10);
    $this->SetFont('Arial', 'I', 10);
    $pagina = mb_convert_encoding("Página", 'ISO-8859-1', 'UTF-8');
    $this->Cell(0, 10, $pagina . ' ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
  }
  public function Productos_Rep($filtrosProductos)
  {
    $this->nombreFiltro = $filtrosProductos['nombre'] ?? '';

    $this->AliasNbPages();
    $this->AddPage();
    $this->SetMargins(10, 10, 10);
    $this->SetAutoPageBreak(true, 10);

    // Título
    $this->SetFont("Arial", "B", "14");
    $titulo = mb_convert_encoding('REPORTE DE PRODUCTOS', 'ISO-8859-1', 'UTF-8');
    $this->Cell(0, 10, $titulo, 0, 1, 'C');
    $this->Ln(5);

    // Conectar a BD
    $conexion = new conexion();
    $conexion = $conexion->getPdo();

    // Consulta corregida
    $consulta = "SELECT 
                        p.id_producto,
                        p.nombre_producto,
                        p.precio_producto_detal,
                        p.precio_producto_mayor,
                        p.stock_producto,
                        um.nombre_unidad_medida AS unidad_medida,
                        um.simbolo_unidad_medida AS simbolo
                    FROM productos p
                    LEFT JOIN unidades_medidas um ON p.id_unidad_medida = um.id_unidad_medida";

    // Agregar filtro si existe
    if ($this->nombreFiltro != '') {
      $consulta .= " WHERE p.nombre_producto LIKE :nombre";
    }

    $stmt = $conexion->prepare($consulta);

    if ($this->nombreFiltro != '') {
      $nombreFiltro = '%' . $this->nombreFiltro . '%';
      $stmt->bindParam(':nombre', $nombreFiltro);
    }

    $stmt->execute();

    if ($stmt->rowCount() == 0) {
      $this->SetFont("Arial", "", "12");
      $this->Cell(0, 10, mb_convert_encoding('No hay productos registrados', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
      return $this;
    }

    $productosBD = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Encabezados de tabla
    $this->SetFont("Arial", "B", "11");
    $this->Cell(25, 10, 'ID', 1, 0, 'C');
    $this->Cell(40, 10, mb_convert_encoding('NOMBRE', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
    $this->Cell(40, 10, 'PRECIO DETAL', 1, 0, 'C');
    $this->Cell(40, 10, 'PRECIO MAYOR', 1, 0, 'C');
    $this->Cell(25, 10, 'STOCK', 1, 0, 'C');
    $this->Cell(20, 10, 'UNIDAD', 1, 1, 'C');

    // Datos
    $this->SetFont("Arial", "", "10");
    foreach ($productosBD as $producto) {
      if ($this->GetY() > 250) {
        $this->AddPage();
        $this->SetFont("Arial", "B", "11");
        $this->Cell(25, 10, 'ID', 1, 0, 'C');
        $this->Cell(40, 10, 'NOMBRE', 1, 0, 'C');
        $this->Cell(40, 10, 'PRECIO DETAL', 1, 0, 'C');
        $this->Cell(40, 10, 'PRECIO MAYOR', 1, 0, 'C');
        $this->Cell(25, 10, 'STOCK', 1, 0, 'C');
        $this->Cell(20, 10, 'UNIDAD', 1, 1, 'C');
        $this->SetFont("Arial", "", "10");
      }

      $this->Cell(25, 10, $producto['id_producto'], 1, 0, 'C');
      $nombreTexto = mb_convert_encoding($producto['nombre_producto'], 'ISO-8859-1', 'UTF-8');
      $this->Cell(40, 10, $nombreTexto, 1, 0, 'L');
      $this->Cell(40, 10, number_format($producto['precio_producto_detal'], 2) . ' Bs', 1, 0, 'R');
      $this->Cell(40, 10, number_format($producto['precio_producto_mayor'], 2) . ' Bs', 1, 0, 'R');
      $this->Cell(25, 10, $producto['stock_producto'], 1, 0, 'R');
      $this->Cell(20, 10, $producto['simbolo_unidad_medida'] ?? 'N/A', 1, 1, 'C');
    }

    return $this;
  }
  public function Servicios_Rep($filtrosServicios)
  {
    $this->nombreFiltro = $filtrosServicios['nombre'] ?? '';

    $this->AliasNbPages();
    $this->AddPage();
    $this->SetMargins(10, 10, 10);
    $this->SetAutoPageBreak(true, 10);

    // Título
    $this->SetFont("Arial", "B", "14");
    $titulo = mb_convert_encoding('REPORTE DE SERVICIOS', 'ISO-8859-1', 'UTF-8');
    $this->Cell(0, 10, $titulo, 0, 1, 'C');
    $this->Ln(5);

    // Conectar a BD
    $conexion = new conexion();
    $conexion = $conexion->getPdo();

    $consulta = "SELECT id_servicio, nombre_servicio, costo_servicio FROM servicios";
    $stmt = $conexion->prepare($consulta);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
      $this->SetFont("Arial", "", "12");
      $this->Cell(0, 10, mb_convert_encoding('No hay servicios registrados', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
      return $this;
    }

    $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Encabezados
    $this->SetFont("Arial", "B", "11");
    $this->Cell(30, 10, 'ID', 1, 0, 'C');
    $this->Cell(100, 10, mb_convert_encoding('NOMBRE DEL SERVICIO', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
    $this->Cell(60, 10, 'PRECIO', 1, 1, 'C');

    // Datos
    $this->SetFont("Arial", "", "10");
    foreach ($servicios as $servicio) {
      if ($this->GetY() > 250) {
        $this->AddPage();
        $this->SetFont("Arial", "B", "11");
        $this->Cell(30, 10, 'ID', 1, 0, 'C');
        $this->Cell(100, 10, 'NOMBRE DEL SERVICIO', 1, 0, 'C');
        $this->Cell(60, 10, 'PRECIO', 1, 1, 'C');
        $this->SetFont("Arial", "", "10");
      }

      $this->Cell(30, 10, $servicio['id_servicio'], 1, 0, 'C');
      $nombre = mb_convert_encoding($servicio['nombre_servicio'], 'ISO-8859-1', 'UTF-8');
      $this->Cell(100, 10, $nombre, 1, 0, 'L');
      $this->Cell(60, 10, number_format($servicio['costo_servicio'], 2) . ' Bs', 1, 1, 'R');
    }

    return $this;
  }
  public function Materia_Rep($filtrosMateria)
  {
    $this->nombreFiltro = $filtrosMateria['nombre'] ?? '';

    $this->AliasNbPages();
    $this->AddPage();
    $this->SetMargins(10, 10, 10);
    $this->SetAutoPageBreak(true, 10);

    // Título
    $this->SetFont("Arial", "B", "14");
    $titulo = mb_convert_encoding('REPORTE DE MATERIA PRIMA', 'ISO-8859-1', 'UTF-8');
    $this->Cell(0, 10, $titulo, 0, 1, 'C');
    $this->Ln(5);

    // Conectar a BD
    $conexion = new conexion();
    $conexion = $conexion->getPdo();

    $consulta = "SELECT 
                        mp.id_materia_prima, 
                        mp.nombre_materia_prima,
                        mp.stock_materia_prima,
                        mp.costo_materia_prima,
                        um.nombre_unidad_medida,
                        um.simbolo_unidad_medida
                    FROM materias_primas mp
                    LEFT JOIN unidades_medidas um ON mp.id_unidad_medida = um.id_unidad_medida";

    $stmt = $conexion->prepare($consulta);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
      $this->SetFont("Arial", "", "12");
      $this->Cell(0, 10, mb_convert_encoding('No hay materias primas registradas', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
      return $this;
    }

    $materias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Encabezados
    $this->SetFont("Arial", "B", "11");
    $this->Cell(25, 10, 'ID', 1, 0, 'C');
    $this->Cell(70, 10, mb_convert_encoding('NOMBRE', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
    $this->Cell(40, 10, 'UNIDAD', 1, 0, 'C');
    $this->Cell(30, 10, 'STOCK', 1, 0, 'C');
    $this->Cell(35, 10, 'COSTO', 1, 1, 'C');

    // Datos
    $this->SetFont("Arial", "", "10");
    foreach ($materias as $materia) {
      if ($this->GetY() > 250) {
        $this->AddPage();
        $this->SetFont("Arial", "B", "11");
        $this->Cell(25, 10, 'ID', 1, 0, 'C');
        $this->Cell(70, 10, 'NOMBRE', 1, 0, 'C');
        $this->Cell(40, 10, 'UNIDAD', 1, 0, 'C');
        $this->Cell(30, 10, 'STOCK', 1, 0, 'C');
        $this->Cell(35, 10, 'COSTO', 1, 1, 'C');
        $this->SetFont("Arial", "", "10");
      }

      $this->Cell(25, 10, $materia['id_materia_prima'], 1, 0, 'C');
      $nombre = mb_convert_encoding($materia['nombre_materia_prima'], 'ISO-8859-1', 'UTF-8');
      $this->Cell(70, 10, $nombre, 1, 0, 'L');
      $this->Cell(40, 10, $materia['nombre_unidad_medida'] ?? 'N/A', 1, 0, 'L');
      $this->Cell(30, 10, $materia['stock_materia_prima'] . ' ' . ($materia['simbolo_unidad_medida'] ?? ''), 1, 0, 'R');
      $this->Cell(35, 10, number_format($materia['costo_materia_prima'], 2) . ' Bs', 1, 1, 'R');
    }

    return $this;
  }
  public function Cierre_Rep($filtrosCierre)
  {
    try {
      // Configurar el documento
      $this->AliasNbPages();
      $this->AddPage();
      $this->SetMargins(10, 10, 10);
      $this->SetAutoPageBreak(true, 15);

      // Obtener fecha del filtro o usar fecha actual
      $fecha = $filtrosCierre['fecha'] ?? date('Y-m-d');

      // Título
      $this->SetFont('Arial', 'B', 16);
      $titulo = mb_convert_encoding('CIERRE DE CAJA', 'ISO-8859-1', 'UTF-8');
      $this->Cell(0, 10, $titulo, 0, 1, 'C');
      $this->Ln(5);

      // Fecha
      $this->SetFont('Arial', 'B', 12);
      $fechaFormateada = date('d/m/Y', strtotime($fecha));
      $this->Cell(0, 10, 'Fecha: ' . $fechaFormateada, 0, 1, 'C');
      $this->Ln(10);

      // $this->SetFont('Arial', 'I', 10);
      //$this->Cell(0, 6, mb_convert_encoding('*** Reporte de Cierre ***', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

      // Conectar a la base de datos
      $conexion = new conexion();
      $conexion = $conexion->getPdo();

      // CONSULTA 1: Resumen General del Día
      $sqlResumen = "SELECT 
        DATE(v.fecha_venta) AS fechaVenta,
        COUNT(DISTINCT v.id_venta) AS total_ventas,
        COUNT(DISTINCT v.rif_cedula_cliente) AS clientes_atendidos,
        
        COALESCE((
            SELECT SUM(pv.cantidad_producto * pr.precio_producto_detal)
            FROM productos_ventas pv
            INNER JOIN productos pr ON pv.id_producto = pr.id_producto
            WHERE pv.id_venta IN (
                SELECT id_venta 
                FROM ventas 
                WHERE DATE(fecha_venta) =fechaVenta
            )
        ), 0) AS total_productos,
        
        COALESCE((
            SELECT SUM(sv.cantidad_servicio * s.costo_servicio)
            FROM servicios_ventas sv
            INNER JOIN servicios s ON sv.id_servicio = s.id_servicio
            WHERE sv.id_venta IN (
                SELECT id_venta 
                FROM ventas 
                WHERE DATE(fecha_venta) =fechaVenta
            )
        ), 0) AS total_servicios,
        
        COALESCE((
            SELECT SUM(ci.monto_cambio_iva)
            FROM ventas v2
            INNER JOIN cambios_iva ci ON v2.id_cambio_iva = ci.id_cambio_iva
            WHERE DATE(v2.fecha_venta) = fechaVenta
          ), 0) AS total_iva
          FROM ventas v
          WHERE DATE(v.fecha_venta) = fechaVenta
          GROUP BY DATE(v.fecha_venta)";

      $stmt = $conexion->prepare($sqlResumen);
      $stmt->execute();
      $resumen = $stmt->fetch(PDO::FETCH_ASSOC);

      // Si no hay ventas, mostrar mensaje
      if (!$resumen || $resumen['total_ventas'] == 0) {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 20, mb_convert_encoding('NO HAY VENTAS PARA LA FECHA SELECCIONADA', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        return $this;
      }

      // CONSULTA 2: Desglose por Método de Pago
      $sqlPagos = "SELECT 
                    mp.nombre_metodo_pago,
                    mn.nombre_moneda,
                    mn.simbolo_moneda,
                    COUNT(DISTINCT p.id_pago) AS cantidad_pagos,
                    COALESCE(SUM(dp.monto_pago), 0) AS monto_total
                FROM pagos p
                INNER JOIN ventas v ON p.id_venta = v.id_venta
                INNER JOIN detalles_pagos dp ON p.id_pago = dp.id_pago
                INNER JOIN metodos_pagos mp ON dp.id_metodo_pago = mp.id_metodo_pago
                INNER JOIN monedas mn ON dp.id_moneda = mn.id_moneda
                WHERE DATE(v.fecha_venta) = :fecha
                GROUP BY mp.nombre_metodo_pago, mn.nombre_moneda, mn.simbolo_moneda
                ORDER BY monto_total DESC";

      $stmt = $conexion->prepare($sqlPagos);
      // $stmt->bindParam(':fecha', $fecha);
      $stmt->execute();
      $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);

      // CONSULTA 3: Detalle de Ventas del Día
      $sqlDetalle = "SELECT 
                        v.id_venta,
                        v.fecha_venta,
                        COALESCE(c.razón_social_cliente, 'Público General') AS cliente,
                        
                        COALESCE((
                            SELECT SUM(pv.cantidad_producto * pr.precio_producto_detal)
                            FROM productos_ventas pv
                            INNER JOIN productos pr ON pv.id_producto = pr.id_producto
                            WHERE pv.id_venta = v.id_venta
                        ), 0) AS monto_productos,
                        
                        COALESCE((
                            SELECT SUM(sv.cantidad_servicio * s.precio_servicio)
                            FROM servicios_ventas sv
                            INNER JOIN servicios s ON sv.id_servicio = s.id_servicio
                            WHERE sv.id_venta = v.id_venta
                        ), 0) AS monto_servicios,
                        
                        COALESCE(ci.monto_cambio_iva, 0) AS monto_iva
                    FROM ventas v
                    LEFT JOIN clientes c ON v.rif_cedula_cliente = c.rif_cedula_cliente
                    LEFT JOIN cambios_iva ci ON v.id_cambio_iva = ci.id_cambio_iva
                    WHERE DATE(v.fecha_venta) = :fecha
                    ORDER BY v.fecha_venta DESC";

      $stmt = $conexion->prepare($sqlDetalle);
      // $stmt->bindParam(':fecha', $fecha);
      $stmt->execute();
      $detalleVentas = $stmt->fetchAll(PDO::FETCH_ASSOC);

      // Calcular totales
      $totalProductos = $resumen['total_productos'] ?? 0;
      $totalServicios = $resumen['total_servicios'] ?? 0;
      $totalIva = $resumen['total_iva'] ?? 0;
      $totalGeneral = $totalProductos + $totalServicios + $totalIva;

      // --- CONSTRUIR EL PDF ---

      // Título del reporte
      $this->SetFont('Arial', 'B', 16);
      $titulo = mb_convert_encoding('CIERRE DE CAJA', 'ISO-8859-1', 'UTF-8');
      $this->Cell(0, 10, $titulo, 0, 1, 'C');
      $this->Ln(5);

      // Fecha del cierre
      $this->SetFont('Arial', 'B', 12);
      $fechaFormateada = date('d/m/Y', strtotime($fecha));
      $this->Cell(0, 10, 'Fecha: ' . $fechaFormateada, 0, 1, 'C');
      $this->Ln(10);

      // SECCIÓN 1: RESUMEN GENERAL
      $this->SetFont('Arial', 'B', 14);
      $this->SetFillColor(78, 84, 200); // Color morado
      $this->SetTextColor(255, 255, 255); // Blanco
      $this->Cell(0, 10, mb_convert_encoding('RESUMEN GENERAL', 'ISO-8859-1', 'UTF-8'), 0, 1, 'L', true);
      $this->SetTextColor(0, 0, 0); // Restaurar color negro
      $this->Ln(5);

      // Tabla de resumen
      $this->SetFont('Arial', 'B', 11);
      $this->Cell(100, 8, 'Concepto', 1, 0, 'L');
      $this->Cell(80, 8, 'Monto', 1, 1, 'R');

      $this->SetFont('Arial', '', 11);
      $this->Cell(100, 8, 'Total Ventas del Día', 1, 0, 'L');
      $this->Cell(80, 8, number_format($resumen['total_ventas'], 2) . ' (' . $resumen['total_ventas'] . ' ventas)', 1, 1, 'R');

      $this->Cell(100, 8, 'Clientes Atendidos', 1, 0, 'L');
      $this->Cell(80, 8, $resumen['clientes_atendidos'], 1, 1, 'R');

      $this->Cell(100, 8, 'Total Productos', 1, 0, 'L');
      $this->Cell(80, 8, number_format($totalProductos, 2), 1, 1, 'R');

      $this->Cell(100, 8, 'Total Servicios', 1, 0, 'L');
      $this->Cell(80, 8, number_format($totalServicios, 2), 1, 1, 'R');

      $this->Cell(100, 8, 'Total IVA', 1, 0, 'L');
      $this->Cell(80, 8, number_format($totalIva, 2), 1, 1, 'R');

      $this->SetFont('Arial', 'B', 11);
      $this->Cell(100, 8, 'TOTAL GENERAL', 1, 0, 'L');
      $this->Cell(80, 8, number_format($totalGeneral, 2), 1, 1, 'R');
      $this->Ln(10);

      // SECCIÓN 2: DESGLOSE POR MÉTODO DE PAGO
      $this->SetFont('Arial', 'B', 14);
      $this->SetFillColor(78, 84, 200);
      $this->SetTextColor(255, 255, 255);
      $this->Cell(0, 10, mb_convert_encoding('DESGLOSE POR MÉTODO DE PAGO', 'ISO-8859-1', 'UTF-8'), 0, 1, 'L', true);
      $this->SetTextColor(0, 0, 0);
      $this->Ln(5);

      // Tabla de métodos de pago
      $this->SetFont('Arial', 'B', 10);
      $this->Cell(50, 8, 'Método de Pago', 1, 0, 'L');
      $this->Cell(40, 8, 'Moneda', 1, 0, 'L');
      $this->Cell(30, 8, 'Cantidad', 1, 0, 'C');
      $this->Cell(70, 8, 'Monto Total', 1, 1, 'R');

      $this->SetFont('Arial', '', 10);
      $totalPagos = 0;
      foreach ($pagos as $pago) {
        $this->Cell(50, 8, mb_convert_encoding($pago['nombre_metodo_pago'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'L');
        $this->Cell(40, 8, mb_convert_encoding($pago['nombre_moneda'] . ' (' . $pago['simbolo_moneda'] . ')', 'ISO-8859-1', 'UTF-8'), 1, 0, 'L');
        $this->Cell(30, 8, $pago['cantidad_pagos'], 1, 0, 'C');
        $this->Cell(70, 8, number_format($pago['monto_total'], 2), 1, 1, 'R');
        $totalPagos += $pago['monto_total'];
      }

      // Verificar consistencia
      $this->SetFont('Arial', 'B', 10);
      $this->Cell(120, 8, 'TOTAL COBRADO (Métodos de Pago)', 1, 0, 'L');
      $this->Cell(70, 8, number_format($totalPagos, 2), 1, 1, 'R');

      if (abs($totalGeneral - $totalPagos) > 0.01) {
        $this->SetFont('Arial', 'I', 9);
        $this->SetTextColor(255, 0, 0);
        $this->Cell(0, 6, mb_convert_encoding('* Diferencia: ' . number_format($totalGeneral - $totalPagos, 2), 'ISO-8859-1', 'UTF-8'), 0, 1, 'R');
        $this->SetTextColor(0, 0, 0);
      }
      $this->Ln(10);

      // Verificar si necesitamos una nueva página para los detalles
      if ($this->GetY() > 200) {
        $this->AddPage();
      }

      // SECCIÓN 3: DETALLE DE VENTAS
      $this->SetFont('Arial', 'B', 14);
      $this->SetFillColor(78, 84, 200);
      $this->SetTextColor(255, 255, 255);
      $this->Cell(0, 10, mb_convert_encoding('DETALLE DE VENTAS', 'ISO-8859-1', 'UTF-8'), 0, 1, 'L', true);
      $this->SetTextColor(0, 0, 0);
      $this->Ln(5);

      // Tabla de ventas
      $this->SetFont('Arial', 'B', 9);
      $this->Cell(20, 8, 'Venta #', 1, 0, 'C');
      $this->Cell(25, 8, 'Hora', 1, 0, 'C');
      $this->Cell(50, 8, mb_convert_encoding('Cliente', 'ISO-8859-1', 'UTF-8'), 1, 0, 'L');
      $this->Cell(25, 8, 'Productos', 1, 0, 'R');
      $this->Cell(25, 8, 'Servicios', 1, 0, 'R');
      $this->Cell(20, 8, 'IVA', 1, 0, 'R');
      $this->Cell(25, 8, 'Total', 1, 1, 'R');

      $this->SetFont('Arial', '', 9);
      foreach ($detalleVentas as $venta) {
        $hora = date('H:i', strtotime($venta['fecha_venta']));
        $totalVenta = $venta['monto_productos'] + $venta['monto_servicios'] + $venta['monto_iva'];

        // Verificar si necesitamos nueva página
        if ($this->GetY() > 250) {
          $this->AddPage();
          // Repetir encabezados
          $this->SetFont('Arial', 'B', 9);
          $this->Cell(20, 8, 'Venta #', 1, 0, 'C');
          $this->Cell(25, 8, 'Hora', 1, 0, 'C');
          $this->Cell(50, 8, 'Cliente', 1, 0, 'L');
          $this->Cell(25, 8, 'Productos', 1, 0, 'R');
          $this->Cell(25, 8, 'Servicios', 1, 0, 'R');
          $this->Cell(20, 8, 'IVA', 1, 0, 'R');
          $this->Cell(25, 8, 'Total', 1, 1, 'R');
          $this->SetFont('Arial', '', 9);
        }

        $this->Cell(20, 8, $venta['id_venta'], 1, 0, 'C');
        $this->Cell(25, 8, $hora, 1, 0, 'C');

        $cliente = mb_convert_encoding(substr($venta['cliente'], 0, 25), 'ISO-8859-1', 'UTF-8');
        $this->Cell(50, 8, $cliente, 1, 0, 'L');

        $this->Cell(25, 8, number_format($venta['monto_productos'], 2), 1, 0, 'R');
        $this->Cell(25, 8, number_format($venta['monto_servicios'], 2), 1, 0, 'R');
        $this->Cell(20, 8, number_format($venta['monto_iva'], 2), 1, 0, 'R');
        $this->Cell(25, 8, number_format($totalVenta, 2), 1, 1, 'R');
      }

      // Línea de totales
      $this->SetFont('Arial', 'B', 9);
      $this->Cell(95, 8, 'TOTALES', 1, 0, 'R');
      $this->Cell(25, 8, number_format($totalProductos, 2), 1, 0, 'R');
      $this->Cell(25, 8, number_format($totalServicios, 2), 1, 0, 'R');
      $this->Cell(20, 8, number_format($totalIva, 2), 1, 0, 'R');
      $this->Cell(25, 8, number_format($totalGeneral, 2), 1, 1, 'R');

      // Pie del reporte
      $this->Ln(10);
      $this->SetFont('Arial', 'I', 10);
      $this->Cell(0, 6, mb_convert_encoding('*** Fin del Reporte de Cierre de Caja ***', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

      return $this;
    } catch (Exception $e) {
      error_log("Error en Cierre_Rep: " . $e->getMessage());
      throw $e;
    }
  }
  public function VentasParametrizadas_Rep($filtros)
  {
    try {
      $this->AliasNbPages();
      $this->AddPage();
      $this->SetMargins(10, 10, 10);
      $this->SetAutoPageBreak(true, 15);

      // Título
      $this->SetFont('Arial', 'B', 16);
      $titulo = mb_convert_encoding('REPORTE DE VENTAS', 'ISO-8859-1', 'UTF-8');
      $this->Cell(0, 10, $titulo, 0, 1, 'C');
      $this->Ln(5);

      // Mostrar filtros aplicados
      $this->SetFont('Arial', 'B', 10);
      $this->Cell(0, 6, mb_convert_encoding('Filtros aplicados:', 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
      $this->SetFont('Arial', '', 10);

      $textoFiltros = $this->generarTextoFiltrosVentas($filtros);
      $this->MultiCell(0, 6, mb_convert_encoding($textoFiltros, 'ISO-8859-1', 'UTF-8'), 0, 'L');
      $this->Ln(5);

      // Construir consulta SQL
      $sql = "SELECT 
                    v.id_venta,
                    v.fecha_venta,
                    COALESCE(c.rif_cedula_cliente, 'Público General') AS cliente,
                    COALESCE(SUM(pv.cantidad_producto * pr.precio_producto_detal), 0) + 
                    COALESCE(SUM(sv.cantidad_servicio * s.costo_servicio), 0) + 
                    COALESCE(ci.monto_cambio_iva, 0) AS total_venta,
                    'Completada' AS estado_venta,
                CASE 
                   WHEN pv.id_producto IS NOT NULL THEN pr.nombre_producto
                   WHEN sv.id_servicio IS NOT NULL THEN s.nombre_servicio
                   ELSE 'N/A'
                END AS item,
                CASE 
                   WHEN pv.id_producto IS NOT NULL THEN 'Producto'
                   WHEN sv.id_servicio IS NOT NULL THEN 'Servicio'
                ELSE 'N/A'
                END AS tipo_item,
                   COALESCE(pv.cantidad_producto, sv.cantidad_servicio, 0) AS cantidad
                FROM ventas v
                LEFT JOIN clientes c ON v.rif_cedula_cliente = c.rif_cedula_cliente
                LEFT JOIN cambios_iva ci ON v.id_cambio_iva = ci.id_cambio_iva
                LEFT JOIN productos_ventas pv ON v.id_venta = pv.id_venta
                LEFT JOIN productos pr ON pv.id_producto = pr.id_producto
                LEFT JOIN servicios_ventas sv ON v.id_venta = sv.id_venta
                LEFT JOIN servicios s ON sv.id_servicio = s.id_servicio
                WHERE 1=1";

      $params = [];

      // Aplicar filtros
      if ($filtros['tipo_item'] === 'productos') {
        $sql .= " AND ps.tipo = 1";
      } elseif ($filtros['tipo_item'] === 'servicios') {
        $sql .= " AND ps.tipo = 2";
      } elseif ($filtros['tipo_item'] === 'especifico' && !empty($filtros['id_item'])) {
        $sql .= " AND v.id_producto_servicio = ?";
        $params[] = $filtros['id_item'];
      }

      // Aplicar filtros de fecha
      switch ($filtros['periodo']) {
        case 'dia':
          $fecha = date('Y-m-d');
          $sql .= " AND DATE(v.fecha_venta) = ?";
          $params[] = $fecha;
          break;
        case 'semana':
          $fechaInicio = date('Y-m-d', strtotime('monday this week'));
          $fechaFin = date('Y-m-d', strtotime('sunday this week'));
          $sql .= " AND DATE(v.fecha_venta) BETWEEN ? AND ?";
          $params[] = $fechaInicio;
          $params[] = $fechaFin;
          break;
        case 'mes':
          $mes = $filtros['mes'] ?? date('m');
          $anio = $filtros['anio'] ?? date('Y');
          $sql .= " AND MONTH(v.fecha_venta) = ? AND YEAR(v.fecha_venta) = ?";
          $params[] = $mes;
          $params[] = $anio;
          break;
        case 'anio':
          $anio = $filtros['anio'] ?? date('Y');
          $sql .= " AND YEAR(v.fecha_venta) = ?";
          $params[] = $anio;
          break;
        case 'personalizado':
          if (!empty($filtros['fecha_desde']) && !empty($filtros['fecha_hasta'])) {
            $sql .= " AND DATE(v.fecha_venta) BETWEEN ? AND ?";
            $params[] = $filtros['fecha_desde'];
            $params[] = $filtros['fecha_hasta'];
          }
          break;
      }

      $sql .= " ORDER BY v.fecha_venta DESC";

      $stmt = $this->pdo->prepare($sql);
      $stmt->execute($params);
      $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);

      if (empty($ventas)) {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 20, mb_convert_encoding('NO HAY VENTAS PARA LOS FILTROS SELECCIONADOS', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        return $this;
      }

      // Calcular totales
      $totalGeneral = 0;
      foreach ($ventas as $venta) {
        $totalGeneral += $venta['total_venta'];
      }

      // Encabezados de tabla
      $this->SetFont('Arial', 'B', 10);
      $this->SetFillColor(78, 84, 200);
      $this->SetTextColor(255, 255, 255);

      $this->Cell(25, 8, 'ID Venta', 1, 0, 'C', true);
      $this->Cell(30, 8, 'Fecha', 1, 0, 'C', true);
      $this->Cell(50, 8, mb_convert_encoding('Cliente', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
      $this->Cell(40, 8, 'Item', 1, 0, 'C', true);
      $this->Cell(25, 8, 'Total', 1, 1, 'C', true);

      $this->SetTextColor(0, 0, 0);
      $this->SetFont('Arial', '', 9);

      // Datos
      foreach ($ventas as $venta) {
        if ($this->GetY() > 250) {
          $this->AddPage();
          // Repetir encabezados
          $this->SetFont('Arial', 'B', 10);
          $this->SetFillColor(78, 84, 200);
          $this->SetTextColor(255, 255, 255);
          $this->Cell(25, 8, 'ID Venta', 1, 0, 'C', true);
          $this->Cell(30, 8, 'Fecha', 1, 0, 'C', true);
          $this->Cell(50, 8, 'Cliente', 1, 0, 'C', true);
          $this->Cell(40, 8, 'Item', 1, 0, 'C', true);
          $this->Cell(25, 8, 'Total', 1, 1, 'C', true);
          $this->SetTextColor(0, 0, 0);
          $this->SetFont('Arial', '', 9);
        }

        $fecha = date('d/m/Y', strtotime($venta['fecha_venta']));
        $cliente = mb_convert_encoding(substr($venta['cliente'], 0, 25), 'ISO-8859-1', 'UTF-8');
        $item = mb_convert_encoding(substr($venta['item'], 0, 20), 'ISO-8859-1', 'UTF-8');

        $this->Cell(25, 8, $venta['id_venta'], 1, 0, 'C');
        $this->Cell(30, 8, $fecha, 1, 0, 'C');
        $this->Cell(50, 8, $cliente, 1, 0, 'L');
        $this->Cell(40, 8, $item, 1, 0, 'L');
        $this->Cell(25, 8, number_format($venta['total_venta'], 2) . ' Bs', 1, 1, 'R');
      }

      // Línea de total
      $this->SetFont('Arial', 'B', 10);
      $this->Cell(145, 8, 'TOTAL GENERAL', 1, 0, 'R');
      $this->Cell(25, 8, number_format($totalGeneral, 2) . ' Bs', 1, 1, 'R');

      return $this;
    } catch (Exception $e) {
      error_log("Error en VentasParametrizadas_Rep: " . $e->getMessage());
      throw $e;
    }
  }
  public function comprasParametrizadas_Rep($filtros)
  {
    // Construir consulta SQL
    $instruccionesDB = [
      'tabla' => 'compras as co',
      'campos' => '
        co.id_compra,co.fecha_compra,p.razon_social_proveedor,
        mp.nombre_materia_prima,dc.cantidad_materia_prima,
        mp.costo_materia_prima,
        SUM(
          dc.cantidad_materia_prima * mp.costo_materia_prima
        ) AS total_compra
      ',
      'datosJoins' => [
        'proveedores as p' => 'co.rif_proveedor = p.rif_proveedor',
        'materias_primas_compras as dc' => 'co.id_compra = dc.id_compra',
        'materias_primas as mp' => 'dc.id_materia_prima = mp.id_materia_prima',
      ],
      'ORDER' => 'co.fecha_compra DESC'
    ];
    // Aplicar filtros
    if ($filtros['tipo_materia'] != 'todos') {
      $instruccionesDB['WHERE']['id_materia_prima'] = $filtros['id_materia'];
    }
    switch ($filtros['periodo']) {
      case 'dia':
        $fecha = date('Y-m-d');
        $instruccionesDB['WHERE']["DATE(co.fecha_compra)"] = $fecha;
        break;
      case 'semana':
        $fechaInicio = date('Y-m-d', strtotime('monday this week'));
        $fechaFin = date('Y-m-d', strtotime('sunday this week'));
        $instruccionesDB['WHERE']["DATE(co.fecha_compra)"] = [
          '>=' => $fechaInicio,
          '<=' => $fechaFin,
        ];
        break;
      case 'mes':
        $mes = $filtros['mes'] ?? date('m');
        $anio = $filtros['anio'] ?? date('Y');
        $instruccionesDB['WHERE']["MONTH(co.fecha_compra)"] = $mes;
        $instruccionesDB["WHERE"]["YEAR(co.fecha_compra)"] = $anio;
        break;
      case 'anio':
        $anio = $filtros['anio'] ?? date('Y');
        $instruccionesDB['WHERE']["YEAR(co.fecha_compra)"] = $anio;
        break;
      case 'personalizado':
        if (!empty($filtros['fecha_desde']) && !empty($filtros['fecha_hasta'])) {
          $instruccionesDB['WHERE']["DATE(co.fecha_compra)"] = [
            '>=' => $filtros['fecha_desde'],
            '<=' => $filtros['fecha_hasta']
          ];
        }
        break;
    }
    $resultado = $this->seleccionarDatos2($instruccionesDB);
    $compras = $resultado->fetchAll(PDO::FETCH_ASSOC);

    if (empty($compras)) {
      $this->SetFont('Arial', 'B', 14);
      $this->Cell(0, 20, mb_convert_encoding('NO HAY COMPRAS PARA LOS FILTROS SELECCIONADOS', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
      return $this;
    }

    // Calcular totales
    $totalGeneral = 0;
    foreach ($compras as $compra) {
      $totalGeneral += $compra['total_compra'];
    }

    // Construimos el PDF
    $this->AliasNbPages();
    $this->AddPage();
    $this->SetMargins(10, 10, 10);
    $this->SetAutoPageBreak(true, 15);

    // Título
    $this->SetFont('Arial', 'B', 16);
    $titulo = mb_convert_encoding('REPORTE DE COMPRAS', 'ISO-8859-1', 'UTF-8');
    $this->Cell(0, 10, $titulo, 0, 1, 'C');
    $this->Ln(5);

    // Mostrar filtros aplicados
    $this->SetFont('Arial', 'B', 10);
    $this->Cell(0, 6, mb_convert_encoding('Filtros aplicados:', 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
    $this->SetFont('Arial', '', 10);

    $textoFiltros = $this->generarTextoFiltrosCompras($filtros);
    $this->MultiCell(0, 6, mb_convert_encoding($textoFiltros, 'ISO-8859-1', 'UTF-8'), 0, 'L');
    $this->Ln(5);


    // Encabezados de tabla
    $this->SetFont('Arial', 'B', 10);
    $this->SetFillColor(78, 84, 200);
    $this->SetTextColor(255, 255, 255);

    $this->Cell(20, 8, 'ID', 1, 0, 'C', true);
    $this->Cell(25, 8, 'Fecha', 1, 0, 'C', true);
    $this->Cell(40, 8, mb_convert_encoding('Proveedor', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
    $this->Cell(45, 8, 'Materia Prima', 1, 0, 'C', true);
    $this->Cell(20, 8, 'Cant.', 1, 0, 'C', true);
    $this->Cell(25, 8, 'Costo Unit.', 1, 0, 'C', true);
    $this->Cell(25, 8, 'Total', 1, 1, 'C', true);

    $this->SetTextColor(0, 0, 0);
    $this->SetFont('Arial', '', 9);

    // Datos
    foreach ($compras as $compra) {
      if ($this->GetY() > 250) {
        $this->AddPage();
        // Repetir encabezados
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(78, 84, 200);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(20, 8, 'ID', 1, 0, 'C', true);
        $this->Cell(25, 8, 'Fecha', 1, 0, 'C', true);
        $this->Cell(40, 8, 'Proveedor', 1, 0, 'C', true);
        $this->Cell(45, 8, 'Materia Prima', 1, 0, 'C', true);
        $this->Cell(20, 8, 'Cant.', 1, 0, 'C', true);
        $this->Cell(25, 8, 'Costo Unit.', 1, 0, 'C', true);
        $this->Cell(25, 8, 'Total', 1, 1, 'C', true);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', '', 9);
      }

      $fecha = date('d/m/Y', strtotime($compra['fecha_compra']));
      $proveedor = mb_convert_encoding(substr($compra['razon_social_proveedor'], 0, 20), 'ISO-8859-1', 'UTF-8');
      //$materia = mb_convert_encoding(substr($compra['nombre_materia_prima'], 0, 20), 'ISO-8859-1', 'UTF-8');

      $item = mb_convert_encoding(substr($compra['nombre_materia_prima'], 0, 20), 'ISO-8859-1', 'UTF-8');

      $this->Cell(20, 8, $compra['id_compra'], 1, 0, 'C');
      $this->Cell(25, 8, $fecha, 1, 0, 'C');
      $this->Cell(40, 8, $proveedor, 1, 0, 'L');
      $this->Cell(45, 8, $item, 1, 0, 'L');
      $this->Cell(20, 8, $compra['cantidad_materia_prima'], 1, 0, 'C');
      $this->Cell(25, 8, number_format($compra['costo_materia_prima'], 2) . ' Bs', 1, 0, 'R');
      $this->Cell(25, 8, number_format($compra['total_compra'], 2) . ' Bs', 1, 1, 'R');
    }

    // Línea de total
    $this->SetFont('Arial', 'B', 10);
    $this->Cell(175, 8, 'TOTAL GENERAL', 1, 0, 'R');
    $this->Cell(25, 8, number_format($totalGeneral, 2) . ' Bs', 1, 1, 'R');

    return $this;
  }
  private function generarTextoFiltrosVentas($filtros)
  {
    $texto = [];

    // Tipo de item
    switch ($filtros['tipo_item']) {
      case 'todos':
        $texto[] = "Todos los items";
        break;
      case 'productos':
        $texto[] = "Solo productos";
        break;
      case 'servicios':
        $texto[] = "Solo servicios";
        break;
      case 'especifico':
        $texto[] = "Item específico (ID: " . ($filtros['id_item'] ?? 'N/A') . ")";
        break;
    }

    // Período
    switch ($filtros['periodo']) {
      case 'dia':
        $texto[] = "Día actual";
        break;
      case 'semana':
        $texto[] = "Semana actual";
        break;
      case 'mes':
        $texto[] = "Mes: " . $this->nombreMes($filtros['mes'] ?? date('m')) . " " . ($filtros['anio'] ?? date('Y'));
        break;
      case 'anio':
        $texto[] = "Año: " . ($filtros['anio'] ?? date('Y'));
        break;
      case 'personalizado':
        $texto[] = "Desde: " . ($filtros['fecha_desde'] ?? 'N/A') . " Hasta: " . ($filtros['fecha_hasta'] ?? 'N/A');
        break;
    }

    return implode(" | ", $texto);
  }
  private function generarTextoFiltrosCompras($filtros)
  {
    $texto = [];

    // Tipo de materia prima
    // Tipo de item
    switch ($filtros['tipo_materia']) {
      case 'todos':
        $texto[] = "Todas las materias primas";
        break;
      case 'especifico':
        $texto[] = "Item específico (ID: " . ($filtros['id_item'] ?? 'N/A') . ")";
        break;
    }

    // Período
    switch ($filtros['periodo']) {
      case 'dia':
        $texto[] = "Día actual";
        break;
      case 'semana':
        $texto[] = "Semana actual";
        break;
      case 'mes':
        $texto[] = "Mes: " . $this->nombreMes($filtros['mes'] ?? date('m')) . " " . ($filtros['anio'] ?? date('Y'));
        break;
      case 'anio':
        $texto[] = "Año: " . ($filtros['anio'] ?? date('Y'));
        break;
      case 'personalizado':
        $texto[] = "Desde: " . ($filtros['fecha_desde'] ?? 'N/A') . " Hasta: " . ($filtros['fecha_hasta'] ?? 'N/A');
        break;
    }

    return implode(" | ", $texto);
  }
  private function nombreMes($numeroMes)
  {
    $meses = [
      '01' => 'Enero',
      '02' => 'Febrero',
      '03' => 'Marzo',
      '04' => 'Abril',
      '05' => 'Mayo',
      '06' => 'Junio',
      '07' => 'Julio',
      '08' => 'Agosto',
      '09' => 'Septiembre',
      '10' => 'Octubre',
      '11' => 'Noviembre',
      '12' => 'Diciembre'
    ];
    return $meses[str_pad($numeroMes, 2, '0', STR_PAD_LEFT)] ?? $numeroMes;
  }
}
