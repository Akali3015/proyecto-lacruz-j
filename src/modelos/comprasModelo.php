<?php

namespace src\modelos;

use src\config\connect\conexion;
use PDO;
use Exception;
use src\modelos\productosModelo;
use src\modelos\insumosModelo;
use src\modelos\materiasPrimasModelo;
use src\modelos\proveedoresModelo;

class comprasModelo extends conexion
{
  use traitModelo;
  private $id_compra;
  private $rif_proveedor;
  private $fecha_compra;
  private $status;

  private $detalles = [];

  public function seleccionarCompra()
  {
    $resultado = $this->seleccionarCompraP();
    return $resultado;
  }

  private function seleccionarCompraP()
  {
    // Consulta detallada para el listado general (sin filtro ID)
    $sql = "
      SELECT 
          c.id_compra,
          c.fecha_compra,
          c.rif_proveedor,
          p.razon_social_proveedor AS PROVEEDOR,
          'producto' AS TIPO,
          prod.nombre_producto AS ARTICULO,
          prod.id_producto AS id_item,
          det.cantidad_producto AS cantidad_raw,
          CONCAT(det.cantidad_producto, ' ', COALESCE(um.simbolo_unidad_medida, 'UNID')) AS cantidad,
          um.id_unidad_medida,
          um.nombre_unidad_medida
      FROM compras c
      JOIN proveedores p ON c.rif_proveedor = p.rif_proveedor
      JOIN productos_compras det ON c.id_compra = det.id_compra
      JOIN productos prod ON det.id_producto = prod.id_producto
      LEFT JOIN unidades_medidas um ON prod.id_unidad_medida = um.id_unidad_medida
      WHERE c.status = 1

      UNION ALL

      SELECT 
          c.id_compra,
          c.fecha_compra,
          c.rif_proveedor,
          p.razon_social_proveedor AS PROVEEDOR,
          'insumo' AS TIPO,
          i.nombre_insumo AS ARTICULO,
          i.id_insumo AS id_item,
          det.cantidad_insumo AS cantidad_raw,
          CONCAT(det.cantidad_insumo, ' UNID') AS cantidad,
          1 AS id_unidad_medida,
          'UNIDAD' AS nombre_unidad_medida
      FROM compras c
      JOIN proveedores p ON c.rif_proveedor = p.rif_proveedor
      JOIN insumos_compras det ON c.id_compra = det.id_compra
      JOIN insumos i ON det.id_insumo = i.id_insumo
      WHERE c.status = 1

      UNION ALL

      SELECT 
          c.id_compra,
          c.fecha_compra,
          c.rif_proveedor,
          p.razon_social_proveedor AS PROVEEDOR,
          'materia_prima' AS TIPO,
          mp.nombre_materia_prima AS ARTICULO,
          mp.id_materia_prima AS id_item,
          det.cantidad_materia_prima AS cantidad_raw,
          CONCAT(det.cantidad_materia_prima, ' ', COALESCE(um.simbolo_unidad_medida, 'UNID')) AS cantidad,
          um.id_unidad_medida,
          um.nombre_unidad_medida
      FROM compras c
      JOIN proveedores p ON c.rif_proveedor = p.rif_proveedor
      JOIN materias_primas_compras det ON c.id_compra = det.id_compra
      JOIN materias_primas mp ON det.id_materia_prima = mp.id_materia_prima
      LEFT JOIN unidades_medidas um ON mp.id_unidad_medida = um.id_unidad_medida
      WHERE c.status = 1
      
      ORDER BY fecha_compra DESC
    ";

    try {
      $this->conectar();
      $stmt = $this->conexion->prepare($sql);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      return [];
    }
  }

  public function seleccionarUno($id)
  {
    // Sanitizar y validar: requerido, formato numérico, longitud
    $alerta = $this->limpiar_Verificar([
      [
        'campo_nombre' => 'id_compra',
        'campo_valor' => $id,
        'formulario_nombre' => 'ID de Compra',
        'requerido' => true,
        'minimo' => 1,
        'maximo' => 10,
        'expresion_re' => '^[0-9]+$',
      ]
    ]);
    if ($alerta)
      return $alerta;

    $this->id_compra = (int) $id;
    $resultado = $this->seleccionarUnoP();
    return $resultado;
  }

  private function seleccionarUnoP()
  {
    $sql = "
            SELECT 
                c.id_compra,
                c.fecha_compra,
                c.rif_proveedor,
                p.razon_social_proveedor AS PROVEEDOR,
                'producto' AS TIPO,
                prod.nombre_producto AS ARTICULO,
                prod.id_producto AS id_item,
                det.cantidad_producto AS cantidad_raw,
                CONCAT(det.cantidad_producto, ' ', COALESCE(um.simbolo_unidad_medida, 'UNID')) AS cantidad,
                um.id_unidad_medida,
                um.nombre_unidad_medida
            FROM compras c
            JOIN proveedores p ON c.rif_proveedor = p.rif_proveedor
            JOIN productos_compras det ON c.id_compra = det.id_compra
            JOIN productos prod ON det.id_producto = prod.id_producto
            LEFT JOIN unidades_medidas um ON prod.id_unidad_medida = um.id_unidad_medida
            WHERE c.status = 1 AND c.id_compra = :id

            UNION ALL

            SELECT 
                c.id_compra,
                c.fecha_compra,
                c.rif_proveedor,
                p.razon_social_proveedor AS PROVEEDOR,
                'insumo' AS TIPO,
                i.nombre_insumo AS ARTICULO,
                i.id_insumo AS id_item,
                det.cantidad_insumo AS cantidad_raw,
                CONCAT(det.cantidad_insumo, ' UNID') AS cantidad,
                1 AS id_unidad_medida,
                'UNIDAD' AS nombre_unidad_medida
            FROM compras c
            JOIN proveedores p ON c.rif_proveedor = p.rif_proveedor
            JOIN insumos_compras det ON c.id_compra = det.id_compra
            JOIN insumos i ON det.id_insumo = i.id_insumo
            WHERE c.status = 1 AND c.id_compra = :id

            UNION ALL

            SELECT 
                c.id_compra,
                c.fecha_compra,
                c.rif_proveedor,
                p.razon_social_proveedor AS PROVEEDOR,
                'materia_prima' AS TIPO,
                mp.nombre_materia_prima AS ARTICULO,
                mp.id_materia_prima AS id_item,
                det.cantidad_materia_prima AS cantidad_raw,
                CONCAT(det.cantidad_materia_prima, ' ', COALESCE(um.simbolo_unidad_medida, 'UNID')) AS cantidad,
                um.id_unidad_medida,
                um.nombre_unidad_medida
            FROM compras c
            JOIN proveedores p ON c.rif_proveedor = p.rif_proveedor
            JOIN materias_primas_compras det ON c.id_compra = det.id_compra
            JOIN materias_primas mp ON det.id_materia_prima = mp.id_materia_prima
            LEFT JOIN unidades_medidas um ON mp.id_unidad_medida = um.id_unidad_medida
            WHERE c.status = 1 AND c.id_compra = :id
            
            ORDER BY fecha_compra DESC
        ";

    try {
      $this->conectar();
      $stmt = $this->conexion->prepare($sql);
      $stmt->bindParam(':id', $this->id_compra, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      return [];
    }
  }

  public function registrarCompra($rif_proveedor, $fecha_compra, $detalles = [])
  {
    // Sanitizar y validar usando las constantes del sistema
    $alerta = $this->limpiar_Verificar([
      [
        'campo_nombre' => 'rif_proveedor',
        'campo_valor' => $rif_proveedor,
        'formulario_nombre' => 'RIF del Proveedor',
        'requerido' => true,
        'minimo' => minRegexNombreObj,
        'maximo' => maxRegexNombreObj,
        'expresion_re' => regexNombreObj,
      ],
      [
        'campo_nombre' => 'fecha_compra',
        'campo_valor' => $fecha_compra,
        'formulario_nombre' => 'Fecha de Compra',
        'requerido' => true,
      ],
    ]);
    if ($alerta)
      return $alerta;

    $this->rif_proveedor = $rif_proveedor;
    $this->fecha_compra = $fecha_compra;
    $this->detalles = $detalles;
    $this->status = 1;

    if (empty($this->detalles)) {
      return [
        "tipo" => "simple",
        "titulo" => "Error",
        "texto" => "Debe agregar al menos un artículo",
        "icono" => "warning"
      ];
    }

    $resultado = $this->registrarCompraP();
    return $resultado;
  }

  private function registrarCompraP()
  {
    try {
      // Obtener cédula del usuario en sesión
      $cedula_usuario = $_SESSION['cedula'] ?? null;

      if (!$cedula_usuario) {
        return [
          "tipo" => "simple",
          "titulo" => "Error de Sesión",
          "texto" => "No se pudo identificar al usuario",
          "icono" => "error"
        ];
      }

      /* ---------- CREAR COMPRA ---------- */
      $id_compra = $this->guardarDatos('compras', [
        ["campo_nombre" => "rif_proveedor", "campo_marcador" => ":rif", "campo_valor" => $this->rif_proveedor],
        ["campo_nombre" => "cedula_usuario", "campo_marcador" => ":cedula", "campo_valor" => $cedula_usuario],
        ["campo_nombre" => "fecha_compra", "campo_marcador" => ":fecha", "campo_valor" => $this->fecha_compra],
        ["campo_nombre" => "status", "campo_marcador" => ":status", "campo_valor" => $this->status]
      ]);

      if (!$id_compra || $id_compra <= 0) {
        throw new Exception("No se pudo crear la compra principal");
      }

      /* ---------- PROCESAR DETALLES ---------- */
      foreach ($this->detalles as $detalle) {
        $tipo = $detalle['tipo'] ?? '';
        $id_item = $detalle['id'] ?? 0;
        $cantidad = $detalle['cantidad'] ?? 0;
        $id_unidad_medida = !empty($detalle['id_unidad_medida']) ? $detalle['id_unidad_medida'] : null;

        if ($cantidad <= 0) {
          continue;
        }

        $this->conectar();

        switch ($tipo) {
          case 'producto':
            // Insertar detalle producto
            $stmt = $this->conexion->prepare("
                INSERT INTO productos_compras (id_compra, id_producto, cantidad_producto, status) 
                VALUES (:compra, :producto, :cantidad, 1)
            ");
            $stmt->execute([
              ':compra' => $id_compra,
              ':producto' => $id_item,
              ':cantidad' => $cantidad
            ]);

            // Actualizar stock
            $stmt_stock = $this->conexion->prepare("
                UPDATE productos 
                SET stock_producto = stock_producto + :cant
                WHERE id_producto = :id
            ");
            $stmt_stock->execute([
              ':cant' => $cantidad,
              ':id' => $id_item
            ]);
            break;

          case 'insumo':
            // Insertar detalle insumo
            $stmt = $this->conexion->prepare("
                INSERT INTO insumos_compras (id_compra, id_insumo, cantidad_insumo, status) 
                VALUES (:compra, :insumo, :cantidad, 1)
            ");
            $stmt->execute([
              ':compra' => $id_compra,
              ':insumo' => $id_item,
              ':cantidad' => $cantidad
            ]);

            // Actualizar stock
            $stmt_stock = $this->conexion->prepare("
                UPDATE insumos 
                SET stock_insumo = stock_insumo + :cant
                WHERE id_insumo = :id
            ");
            $stmt_stock->execute([
              ':cant' => $cantidad,
              ':id' => $id_item
            ]);
            break;

          case 'materia_prima':
            // Insertar detalle materia prima
            $stmt = $this->conexion->prepare("
                INSERT INTO materias_primas_compras (id_compra, id_materia_prima, cantidad_materia_prima, status) 
                VALUES (:compra, :materia, :cantidad, 1)
            ");
            $stmt->execute([
              ':compra' => $id_compra,
              ':materia' => $id_item,
              ':cantidad' => $cantidad
            ]);

            // Actualizar stock
            $stmt_stock = $this->conexion->prepare("
                UPDATE materias_primas 
                SET stock_materia_prima = stock_materia_prima + :cant
                WHERE id_materia_prima = :id
            ");
            $stmt_stock->execute([
              ':cant' => $cantidad,
              ':id' => $id_item
            ]);
            break;
        }
      }

      $this->commit();

      return [
        "tipo" => "limpiar",
        "titulo" => "Compra Registrada",
        "texto" => "Compra #{$id_compra} registrada correctamente.",
        "icono" => "success",
        "id_compra" => $id_compra
      ];
    } catch (Exception $e) {
      $this->rollback();
      return [
        "tipo" => "simple",
        "titulo" => "Error",
        "texto" => "Error al registrar la compra: " . $e->getMessage(),
        "icono" => "error"
      ];
    }
  }

  public function actualizarCompra($id_compra, $rif_proveedor, $fecha_compra, $detalles = [])
  {
    // Sanitizar y validar usando las constantes del sistema
    $alerta = $this->limpiar_Verificar([
      [
        'campo_nombre' => 'id_compra',
        'campo_valor' => $id_compra,
        'formulario_nombre' => 'ID de Compra',
        'requerido' => true,
        'minimo' => 1,
        'maximo' => 10,
        'expresion_re' => '^[0-9]+$',
      ],
      [
        'campo_nombre' => 'rif_proveedor',
        'campo_valor' => $rif_proveedor,
        'formulario_nombre' => 'RIF del Proveedor',
        'requerido' => true,
        'minimo' => minRegexNombreObj,
        'maximo' => maxRegexNombreObj,
        'expresion_re' => regexNombreObj,
      ],
      [
        'campo_nombre' => 'fecha_compra',
        'campo_valor' => $fecha_compra,
        'formulario_nombre' => 'Fecha de Compra',
        'requerido' => true,
      ],
    ]);
    if ($alerta)
      return $alerta;

    $this->id_compra = (int) $id_compra;
    $this->rif_proveedor = $rif_proveedor;
    $this->fecha_compra = $fecha_compra;
    $this->detalles = $detalles;

    $resultado = $this->actualizarCompraP();
    return $resultado;
  }

  private function actualizarCompraP()
  {
    try {
      $this->conectar();



      // 1. REVERTIR STOCK (Restar lo que se había sumado)
      // Obtenemos los detalles actuales antes de borrarlos
      // $detallesAnteriores = $this->seleccionarCompraP(); // NOTA: Esto traería TODO, no solo esta compra. usar seleccionarUnoP. 
      // WAIT - seleccionarTodosP NO filtra por ID. Necesitamos seleccionarUnoP pero devuelve solo el que coincide.
      // Para revertir necesitamos los detalles de ESTA compra.
      // Entonces llamamos a seleccionarUnoP para obtener los detalles actuales.

      $detallesAnteriores = $this->seleccionarUnoP();

      // 0. VALIDAR STOCK DISPONIBLE PARA REVERTIR
      // Antes de hacer nada, verificamos que el stock actual sea suficiente para restar la compra original.
      // Si el stock actual es MENOR a lo que se compró, significa que ya se vendió parte de esa compra
      // y no se puede revertir/editar sin dejar el stock en negativo.
      foreach ($detallesAnteriores as $item) {
        $stockActual = 0;
        $itemEncontrado = null;

        if ($item['TIPO'] == 'producto') {
          $modelo = new productosModelo();
          $itemEncontrado = $modelo->seleccionarProductos($item['id_item']);
          // Las funciones seleccionar... devuelven un alert si no encuentran
          // pero si encuentran, devuelven array. Verificamos si tiene la key 'stock_producto'
          if (is_array($itemEncontrado) && isset($itemEncontrado['stock_producto'])) {
            $stockActual = $itemEncontrado['stock_producto'];
          }
        } elseif ($item['TIPO'] == 'insumo') {
          $modelo = new insumosModelo();
          $itemEncontrado = $modelo->seleccionarInsumos($item['id_item']);
          if (is_array($itemEncontrado) && isset($itemEncontrado['stock_insumo'])) {
            $stockActual = $itemEncontrado['stock_insumo'];
          }
        } elseif ($item['TIPO'] == 'materia_prima') {
          $modelo = new materiasPrimasModelo();
          $itemEncontrado = $modelo->seleccionarMateriasPrimas($item['id_item']);
          if (is_array($itemEncontrado) && isset($itemEncontrado['stock_materia_prima'])) {
            $stockActual = $itemEncontrado['stock_materia_prima'];
          }
        }

        // Validación común
        if ($itemEncontrado && !isset($itemEncontrado['tipo'])) { // Si no es un array de error (los errores traen 'tipo')
          if ($stockActual < $item['cantidad_raw']) {
            $this->conexion->rollBack();
            return [
              "tipo" => "simple",
              "titulo" => "Error de Stock",
              "texto" => "No se puede editar. El artículo '{$item['ARTICULO']}' tiene un stock actual ($stockActual) menor a la cantidad original de esta compra ({$item['cantidad_raw']}). Ya se han consumido items.",
              "icono" => "error"
            ];
          }
        }
      }

      foreach ($detallesAnteriores as $item) {
        $tabla = '';
        $campoId = '';
        $campoStock = '';

        if ($item['TIPO'] == 'producto') {
          $tabla = 'productos';
          $campoId = 'id_producto';
          $campoStock = 'stock_producto';
        } elseif ($item['TIPO'] == 'insumo') {
          $tabla = 'insumos';
          $campoId = 'id_insumo';
          $campoStock = 'stock_insumo';
        } elseif ($item['TIPO'] == 'materia_prima') {
          $tabla = 'materias_primas';
          $campoId = 'id_materia_prima';
          $campoStock = 'stock_materia_prima';
        }

        if ($tabla) {
          $sqlRevertir = "UPDATE $tabla SET $campoStock = $campoStock - :cantidad WHERE $campoId = :id";
          $stmt = $this->conexion->prepare($sqlRevertir);
          $stmt->execute([
            ':cantidad' => $item['cantidad_raw'],
            ':id' => $item['id_item']
          ]);
        }
      }

      // 2. ELIMINAR DETALLES ANTERIORES
      $this->conexion->exec("DELETE FROM productos_compras WHERE id_compra = {$this->id_compra}");
      $this->conexion->exec("DELETE FROM insumos_compras WHERE id_compra = {$this->id_compra}");
      $this->conexion->exec("DELETE FROM materias_primas_compras WHERE id_compra = {$this->id_compra}");

      // 3. ACTUALIZAR CABECERA
      $sqlCabecera = "UPDATE compras SET rif_proveedor = :rif, fecha_compra = :fecha WHERE id_compra = :id";
      $stmtCabecera = $this->conexion->prepare($sqlCabecera);
      $stmtCabecera->execute([
        ':rif' => $this->rif_proveedor,
        ':fecha' => $this->fecha_compra,
        ':id' => $this->id_compra
      ]);

      // 4. INSERTAR NUEVOS DETALLES Y ACTUALIZAR STOCK
      if (!empty($this->detalles)) {
        foreach ($this->detalles as $detalle) {
          $tipo = $detalle['tipo'] ?? '';
          $id_item = $detalle['id'] ?? 0;
          $cantidad = $detalle['cantidad'] ?? 0;
          $id_unidad_medida = !empty($detalle['id_unidad_medida']) ? $detalle['id_unidad_medida'] : null;

          if ($cantidad <= 0)
            continue;

          switch ($tipo) {
            case 'producto':
              $stmt = $this->conexion->prepare("INSERT INTO productos_compras (id_compra, id_producto, cantidad_producto, status) VALUES (:compra, :item, :cant, 1)");
              $stmt->execute([':compra' => $this->id_compra, ':item' => $id_item, ':cant' => $cantidad]);

              $stmtStock = $this->conexion->prepare("UPDATE productos SET stock_producto = stock_producto + :cant WHERE id_producto = :id");
              $stmtStock->execute([':cant' => $cantidad, ':id' => $id_item]);
              break;

            case 'insumo':
              $stmt = $this->conexion->prepare("INSERT INTO insumos_compras (id_compra, id_insumo, cantidad_insumo, status) VALUES (:compra, :item, :cant, 1)");
              $stmt->execute([':compra' => $this->id_compra, ':item' => $id_item, ':cant' => $cantidad]);

              $stmtStock = $this->conexion->prepare("UPDATE insumos SET stock_insumo = stock_insumo + :cant WHERE id_insumo = :id");
              $stmtStock->execute([':cant' => $cantidad, ':id' => $id_item]);
              break;

            case 'materia_prima':
              $stmt = $this->conexion->prepare("INSERT INTO materias_primas_compras (id_compra, id_materia_prima, cantidad_materia_prima, status) VALUES (:compra, :item, :cant, 1)");
              $stmt->execute([':compra' => $this->id_compra, ':item' => $id_item, ':cant' => $cantidad]);

              $stmtStock = $this->conexion->prepare("UPDATE materias_primas SET stock_materia_prima = stock_materia_prima + :cant WHERE id_materia_prima = :id");
              $stmtStock->execute([':cant' => $cantidad, ':id' => $id_item]);
              break;
          }
        }
      }

      $this->conexion->commit();

      return [
        "tipo" => "limpiar",
        "titulo" => "Compra Actualizada",
        "texto" => "La compra ha sido modificada correctamente.",
        "icono" => "success"
      ];
    } catch (Exception $e) {
      if ($this->conexion->inTransaction()) {
        $this->conexion->rollBack();
      }
      return [
        "tipo" => "simple",
        "titulo" => "Error",
        "texto" => "Error al actualizar la compra: " . $e->getMessage(),
        "icono" => "error"
      ];
    }
  }
}
