<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use PDO;

class comprasModelo extends conexion {
  private string|null $id_compra='';
  private string $rif_proveedor='';
  private string $fecha_compra='';
  private array $detalles = [];

  // Metodos publicos
  public function validarCompras(array $instruccionesVal) {
    [
      'infoVal'   => &$infoVal,
      'camposVal' => &$camposVal,
    ] = $instruccionesVal;

    $arrayValidaciones = [
      "id_compra" => [
        "campo_nombre"      => "id_compra",
        "formulario_nombre" => "ID de Compra",
        "requerido"         => true,
        "minimo"            => minRegexId,
        "maximo"            => maxRegexId,
        "expresion_re"      => regexId,
        "tabla"             => "compras",
        "debeExistir"       => true,
      ],
      "rif_proveedor" => [
        "campo_nombre"      => "rif_proveedor",
        "formulario_nombre" => "RIF del Proveedor",
        "requerido"         => true,
        "minimo"            => minRegexCedulaRifLetra,
        "maximo"            => maxRegexCedulaRifLetra,
        "expresion_re"      => regexCedulaRifLetra,
      ],
      "fecha_compra" => [
        "campo_nombre"      => "fecha_compra",
        "formulario_nombre" => "Fecha de Compra",
        "requerido"         => true,
      ],
      "cantidad_item" => [
        "campo_nombre"      => "cantidad",
        "formulario_nombre" => "Cantidad del artículo",
        "requerido"         => true,
        "minimo"            => minRegexCantidadItem,
        "maximo"            => maxRegexCantidadItem,
        "expresion_re"      => regexCantidadItem,
        "comaPunto"         => true,
        "noCero"            => true,
      ]
    ];

    $totalValidaciones = [];
    foreach ($camposVal as $campo => $valorForm) {
      if (is_numeric($campo)) $campo = $valorForm;
      $validacion = [];
      switch ($campo) {
        case 'id_compra':
          $validacion = $arrayValidaciones['id_compra'];
          $validacion['campo_valor'] = &$infoVal[$valorForm];
          break;
        case 'rif_proveedor':
          $validacion = $arrayValidaciones['rif_proveedor'];
          $validacion['campo_valor'] = &$infoVal[$valorForm];
          $validacion['tabla']       = 'proveedores';
          $validacion['debeExistir'] = true;
          break;
        case 'fecha_compra':
          $validacion = $arrayValidaciones['fecha_compra'];
          $validacion['campo_valor'] = &$infoVal[$valorForm];
          break;
        case 'cantidad_item':
          $validacion = $arrayValidaciones['cantidad_item'];
          $validacion['campo_valor'] = &$infoVal[$valorForm];
          break;
      }
      if (!empty($validacion)) {
        $totalValidaciones[] = $validacion;
      }
    }
    return $this->limpiar_Verificar($totalValidaciones);
  }
  public function seleccionarCompras(array $info = []) {
    $this->id_compra = $info['id_compra'] ?? null;

    // Si hay ID validamos que exista
    if (!empty($this->id_compra)) {
      $infoVal = ['id_compra' => &$this->id_compra];
      $alerta = $this->validarCompras([
        'infoVal'   => &$infoVal,
        'camposVal' => ['id_compra']
      ]);
      if ($alerta !== false) return $alerta;
    }

    return $this->seleccionarComprasP();
  }
  public function registrarCompras(array $info) {
    $rif_proveedor = $info['rif_proveedor'] ?? '';
    $fecha_compra  = $info['fecha_compra']  ?? '';
    $detalles = [];
    if (!empty($info['detalle_id']) && is_array($info['detalle_id'])) {
      foreach ($info['detalle_id'] as $index => $id) {
        $detalles[] = [
          'proveedorId' => $info['detalle_proveedorId'][$index] ?? '',
          'tipo'        => $info['detalle_tipo'][$index] ?? '',
          'id'          => $id,
          'cantidad'    => $info['detalle_cantidad'][$index] ?? 1
        ];
      }
    }

    // Normalizar fecha para MySQL
    $fecha_compra = str_replace('T', ' ', $fecha_compra);
    if (strlen($fecha_compra) === 16) $fecha_compra .= ':00';

    // Si el RIF viene vacio lo sacamos del detalle
    if (empty($rif_proveedor) && !empty($detalles)) {
      $rif_proveedor = $detalles[0]['proveedorId'] ?? '';
    }

    // Validar cabecera
    $infoVal = [
      'rif_proveedor' => &$rif_proveedor,
      'fecha_compra'  => &$fecha_compra,
    ];
    $alerta = $this->validarCompras([
      'infoVal'   => &$infoVal,
      'camposVal' => ['rif_proveedor', 'fecha_compra']
    ]);
    if ($alerta !== false) return $alerta;

    // Validar que haya al menos un ítem
    if (empty($detalles)) {
      return [
        "tipo"   => "simple",
        "titulo" => "Sin artículos",
        "texto"  => "Debe agregar al menos un artículo a la compra",
        "icono"  => "warning"
      ];
    }

    // Validar tipo y cantidad de cada ítem
    foreach ($detalles as &$det) {
      if (!in_array($det['tipo'] ?? '', ['producto', 'materia_prima'])) {
        return [
          "tipo"   => "simple",
          "titulo" => "Tipo de artículo inválido",
          "texto"  => "Solo se permiten productos o materias primas",
          "icono"  => "error"
        ];
      }
      $infoVal = ['cantidad' => &$det['cantidad']];
      $alerta = $this->validarCompras([
        'infoVal'   => &$infoVal,
        'camposVal' => ['cantidad_item' => 'cantidad']
      ]);
      if ($alerta !== false) return $alerta;
    }
    unset($det);

    $this->rif_proveedor = $rif_proveedor;
    $this->fecha_compra  = $fecha_compra;
    $this->detalles      = $detalles;

    return $this->registrarComprasP();
  }
  public function actualizarCompras(array $info) {
    $id_compra     = $info['id_compra']     ?? '';
    $rif_proveedor = $info['rif_proveedor'] ?? '';
    $fecha_compra  = $info['fecha_compra']  ?? '';
    $detalles = [];
    if (!empty($info['detalle_id']) && is_array($info['detalle_id'])) {
      foreach ($info['detalle_id'] as $index => $id) {
        $detalles[] = [
          'proveedorId' => $info['detalle_proveedorId'][$index] ?? '',
          'tipo'        => $info['detalle_tipo'][$index] ?? '',
          'id'          => $id,
          'cantidad'    => $info['detalle_cantidad'][$index] ?? 1
        ];
      }
    }

    // Normalizar fecha
    $fecha_compra = str_replace('T', ' ', $fecha_compra);
    if (strlen($fecha_compra) === 16) $fecha_compra .= ':00';

    // Validar cabecera
    $infoVal = [
      'id_compra'     => &$id_compra,
      'rif_proveedor' => &$rif_proveedor,
      'fecha_compra'  => &$fecha_compra,
    ];
    $alerta = $this->validarCompras([
      'infoVal'   => &$infoVal,
      'camposVal' => [
        'id_compra',
        'rif_proveedor',
        'fecha_compra'
      ]
    ]);
    if ($alerta !== false) return $alerta;

    // Validar ítems
    if (empty($detalles)) {
      return [
        "tipo"   => "simple",
        "titulo" => "Sin artículos",
        "texto"  => "Debe incluir al menos un artículo",
        "icono"  => "warning"
      ];
    }

    foreach ($detalles as &$det) {
      if (!in_array($det['tipo'] ?? '', ['producto', 'materia_prima'])) {
        return [
          "tipo"   => "simple",
          "titulo" => "Tipo de artículo inválido",
          "texto"  => "Solo se permiten productos o materias primas",
          "icono"  => "error"
        ];
      }
      $infoVal = ['cantidad' => &$det['cantidad']];
      $alerta = $this->validarCompras([
        'infoVal'   => &$infoVal,
        'camposVal' => ['cantidad_item' => 'cantidad']
      ]);
      if ($alerta !== false) return $alerta;
    }
    unset($det);

    $this->id_compra     = $id_compra;
    $this->rif_proveedor = $rif_proveedor;
    $this->fecha_compra  = $fecha_compra;
    $this->detalles      = $detalles;
    return $this->actualizarComprasP();
  }
  public function eliminarCompras(array $info) {
    $id_compra = $info['id_compra'] ?? '';

    // Validar que el ID existe
    $infoVal = ['id_compra' => &$id_compra];
    $alerta = $this->validarCompras([
      'infoVal'   => &$infoVal,
      'camposVal' => ['id_compra']
    ]);
    if ($alerta !== false) return $alerta;

    $this->id_compra = $id_compra;
    return $this->eliminarComprasP();
  }
  public function listarProductosParaCompras(): array {
    $sql = "
      SELECT
        pp.id_presentacion_producto,
        p.nombre_producto AS nombre_producto,
        p.id_unidad_medida,
        um.nombre_unidad_medida,
        um.simbolo_unidad_medida
      FROM presentaciones_productos pp
      INNER JOIN productos p   ON pp.id_producto   = p.id_producto
      INNER JOIN presentaciones pre ON pp.id_presentacion = pre.id_presentacion
      LEFT  JOIN unidades_medidas um ON p.id_unidad_medida = um.id_unidad_medida
      WHERE p.status != 0
      ORDER BY p.nombre_producto, pre.nombre_presentacion
    ";
    $stmt = $this->conectar()->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Metodos privados (BD)
  private function seleccionarComprasP() {
    // Listado general si no hay ID
    if (empty($this->id_compra)) {
      $sql = "
                SELECT
                    c.id_compra,
                    c.fecha_compra,
                    c.rif_proveedor,
                    COALESCE(p.razon_social_proveedor, c.rif_proveedor) AS PROVEEDOR,
                    (
                        (SELECT COUNT(*) FROM productos_compras pc
                            WHERE pc.id_compra = c.id_compra)
                        +
                        (SELECT COUNT(*) FROM materias_primas_compras mpc
                            WHERE mpc.id_compra = c.id_compra)
                    ) AS total_articulos
                FROM compras c
                LEFT JOIN proveedores p ON c.rif_proveedor = p.rif_proveedor
                WHERE c.status != 0
                ORDER BY CAST(c.id_compra AS UNSIGNED) DESC
            ";
      $stmt = $this->conectar()->prepare($sql);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Detalle completo por ID
    $sql = "
            SELECT
                c.id_compra, c.fecha_compra, c.rif_proveedor,
                COALESCE(p.razon_social_proveedor, c.rif_proveedor) AS PROVEEDOR,
                'producto' AS TIPO,
                prod.nombre_producto AS ARTICULO,
                pp.id_presentacion_producto AS id_item,
                det.cantidad_producto AS cantidad_raw,
                CONCAT(det.cantidad_producto, ' ',
                    COALESCE(um.simbolo_unidad_medida, 'UNID')) AS cantidad,
                um.id_unidad_medida,
                um.nombre_unidad_medida
            FROM compras c
            LEFT JOIN proveedores p ON c.rif_proveedor = p.rif_proveedor
            INNER JOIN productos_compras det ON c.id_compra = det.id_compra
            INNER JOIN presentaciones_productos pp ON det.id_presentacion_producto = pp.id_presentacion_producto
            INNER JOIN productos prod ON pp.id_producto = prod.id_producto
            LEFT JOIN unidades_medidas um ON prod.id_unidad_medida = um.id_unidad_medida
            WHERE c.status != 0 AND c.id_compra = :id

            UNION ALL

            SELECT
                c.id_compra, c.fecha_compra, c.rif_proveedor,
                COALESCE(p.razon_social_proveedor, c.rif_proveedor) AS PROVEEDOR,
                'materia_prima' AS TIPO,
                mp.nombre_materia_prima AS ARTICULO,
                det.id_materia_prima AS id_item,
                det.cantidad_materia_prima AS cantidad_raw,
                CONCAT(det.cantidad_materia_prima, ' ',
                    COALESCE(um.simbolo_unidad_medida, 'UNID')) AS cantidad,
                um.id_unidad_medida,
                um.nombre_unidad_medida
            FROM compras c
            LEFT JOIN proveedores p ON c.rif_proveedor = p.rif_proveedor
            INNER JOIN materias_primas_compras det ON c.id_compra = det.id_compra
            INNER JOIN materias_primas mp ON det.id_materia_prima = mp.id_materia_prima
            LEFT JOIN unidades_medidas um ON mp.id_unidad_medida = um.id_unidad_medida
            WHERE c.status != 0 AND c.id_compra = :id

            ORDER BY TIPO DESC
        ";
    $stmt = $this->conectar()->prepare($sql);
    $stmt->bindValue(':id', (string) $this->id_compra, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  private function registrarComprasP() {
    
    $bitacora = new bitacoraModelo();
    $cn = $this->pdo;
    try {
      $cn->beginTransaction();

      // 1. Generar ID de compra (Manual pero robusto)
      $stMax = $cn->query('SELECT MAX(CAST(id_compra AS UNSIGNED)) FROM compras');
      $maxId = $stMax->fetchColumn();
      $id_compra = (string) (($maxId ?: 0) + 1);

      // 2. Insertar cabecera
      $stInsert = $cn->prepare(
        'INSERT INTO compras (id_compra, rif_proveedor, cedula_usuario, fecha_compra, status)
                 VALUES (:id, :rif, :cedula, :fecha, 1)'
      );
      $stInsert->execute([
        ':id'     => $id_compra,
        ':rif'    => $this->rif_proveedor,
        ':cedula' => $_SESSION['cedula'],
        ':fecha'  => $this->fecha_compra,
      ]);

      // 3. Agrupar items duplicados sumando cantidades
      $itemsAgrupados = [];
      foreach ($this->detalles as $det) {
        $clave = $det['tipo'] . '_' . $det['id'];
        if (!isset($itemsAgrupados[$clave])) {
          $itemsAgrupados[$clave] = $det;
        } else {
          $itemsAgrupados[$clave]['cantidad'] += (float)$det['cantidad'];
        }
      }

      // 4. Insertar detalles y actualizar stock
      
      foreach ($itemsAgrupados as $item) {
        $tipo    = $item['tipo'];
        $id_item = $item['id'];
        $cant    = (float) $item['cantidad'];

        if ($tipo === 'producto') {
          $cn->prepare(
            'INSERT INTO productos_compras (id_compra, id_presentacion_producto, cantidad_producto, status)
                         VALUES (:compra, :item, :cant, 1)'
          )->execute([
            ':compra' => $id_compra,
            ':item'   => $id_item,
            ':cant'   => $cant
          ]);

          $cn->prepare(
            'UPDATE productos prod
                         INNER JOIN presentaciones_productos pp ON pp.id_producto = prod.id_producto
                         SET prod.stock_producto = prod.stock_producto + :cant
                         WHERE pp.id_presentacion_producto = :id'
          )->execute([':cant' => $cant, ':id' => $id_item]);
        } elseif ($tipo === 'materia_prima') {
          $cn->prepare(
            'INSERT INTO materias_primas_compras (id_compra, id_materia_prima, cantidad_materia_prima, status)
                         VALUES (:compra, :item, :cant, 1)'
          )->execute([
            ':compra' => $id_compra,
            ':item'   => $id_item,
            ':cant'   => $cant
          ]);

          $cn->prepare(
            'UPDATE materias_primas 
                         SET stock_materia_prima = stock_materia_prima + :cant 
                         WHERE id_materia_prima = :id'
          )->execute([':cant' => $cant, ':id' => $id_item]);
        }
      }

      $bitacora->registrarBitacora('compras', 'Registrar', 'Registro de nueva compra exitoso (' . $id_compra . ')');
      $cn->commit();

      return [
        'tipo'   => 'limpiar',
        'titulo' => 'Compra registrada',
        'texto'  => 'La compra ha sido registrada exitosamente',
        'icono'  => 'success'
      ];
    } catch (\Throwable $th) {
      if ($cn->inTransaction()) $cn->rollBack();
      return [
        'tipo'   => 'simple',
        'titulo' => 'Error de base de datos',
        'texto'  => 'Ocurrió un error al registrar la compra: ' . $th->getMessage(),
        'icono'  => 'error'
      ];
    }
  }
  private function actualizarComprasP() {
    $bitacora = new bitacoraModelo();
    try {
      $cn = $this->pdo;
      $cn->beginTransaction();

      // 1. Revertir stock de ítems anteriores
      $anteriores = $this->seleccionarCompras(['id_compra' => $this->id_compra]);
      foreach ($anteriores as $item) {
        if ($item['TIPO'] === 'producto') {
          $cn->prepare(
            'UPDATE productos prod
                         INNER JOIN presentaciones_productos pp ON pp.id_producto = prod.id_producto
                         SET prod.stock_producto = prod.stock_producto - :cant
                         WHERE pp.id_presentacion_producto = :id'
          )->execute([':cant' => $item['cantidad_raw'], ':id' => $item['id_item']]);
        } elseif ($item['TIPO'] === 'materia_prima') {
          $cn->prepare(
            'UPDATE materias_primas SET stock_materia_prima = stock_materia_prima - :cant WHERE id_materia_prima = :id'
          )->execute([':cant' => $item['cantidad_raw'], ':id' => $item['id_item']]);
        }
      }

      // 2. Eliminar detalles anteriores
      $cn->prepare('DELETE FROM productos_compras WHERE id_compra = :id')
        ->execute([':id' => $this->id_compra]);
      $cn->prepare('DELETE FROM materias_primas_compras WHERE id_compra = :id')
        ->execute([':id' => $this->id_compra]);

      // 3. Actualizar cabecera
      $cn->prepare(
        'UPDATE compras SET rif_proveedor = :rif, fecha_compra = :fecha WHERE id_compra = :id'
      )->execute([
        ':rif'   => $this->rif_proveedor,
        ':fecha' => $this->fecha_compra,
        ':id'    => $this->id_compra,
      ]);

      // 4. Insertar nuevos ítems y actualizar stock
      foreach ($this->detalles as $det) {
        $tipo    = $det['tipo']     ?? '';
        $id_item = (string) ($det['id'] ?? '');
        $cant    = (float)  ($det['cantidad'] ?? 0);

        if (empty($id_item) || $cant <= 0) continue;

        if ($tipo === 'producto') {
          $cn->prepare(
            'INSERT INTO productos_compras (id_compra, id_presentacion_producto, cantidad_producto, status)
                         VALUES (:compra, :item, :cant, 1)'
          )->execute([':compra' => $this->id_compra, ':item' => $id_item, ':cant' => $cant]);
          $cn->prepare(
            'UPDATE productos prod
                         INNER JOIN presentaciones_productos pp ON pp.id_producto = prod.id_producto
                         SET prod.stock_producto = prod.stock_producto + :cant
                         WHERE pp.id_presentacion_producto = :id'
          )->execute([':cant' => $cant, ':id' => $id_item]);
        } elseif ($tipo === 'materia_prima') {
          $cn->prepare(
            'INSERT INTO materias_primas_compras (id_compra, id_materia_prima, cantidad_materia_prima, status)
                         VALUES (:compra, :item, :cant, 1)'
          )->execute([':compra' => $this->id_compra, ':item' => $id_item, ':cant' => $cant]);
          $cn->prepare(
            'UPDATE materias_primas SET stock_materia_prima = stock_materia_prima + :cant WHERE id_materia_prima = :id'
          )->execute([':cant' => $cant, ':id' => $id_item]);
        }
      }

      $bitacora->registrarBitacora('compras', 'Actualizar', 'Actualización de compra exitosa (' . $this->id_compra . ')');
      $cn->commit();

      return [
        "tipo"   => "limpiarYcerrar",
        "titulo" => "Compra Actualizada",
        "texto"  => "La compra fue modificada correctamente",
        "icono"  => "success"
      ];
    } catch (\Throwable $e) {
      if (isset($cn) && $cn->inTransaction()) $cn->rollBack();
      $bitacora->registrarBitacora('compras', 'Actualizar', 'Fallido');
      return [
        "tipo"   => "simple",
        "titulo" => "Error",
        "texto"  => "Error al actualizar la compra: " . $e->getMessage(),
        "icono"  => "error"
      ];
    }
  }
  private function eliminarComprasP() {
    $bitacora = new bitacoraModelo();
    try {
      $cn = $this->pdo;
      $cn->beginTransaction();

      // 1. Revertir stock de PRODUCTOS
      $stmtProd = $cn->prepare("
                SELECT pc.id_presentacion_producto, pc.cantidad_producto,
                       prod.stock_producto, prod.nombre_producto, prod.id_producto
                FROM productos_compras pc
                INNER JOIN presentaciones_productos pp ON pc.id_presentacion_producto = pp.id_presentacion_producto
                INNER JOIN productos prod ON pp.id_producto = prod.id_producto
                WHERE pc.id_compra = :id
            ");
      $stmtProd->execute([':id' => $this->id_compra]);

      foreach ($stmtProd->fetchAll(\PDO::FETCH_ASSOC) as $prod) {
        $nuevoStock = $prod['stock_producto'] - $prod['cantidad_producto'];
        if ($nuevoStock < 0) {
          $cn->rollBack();
          return [
            "tipo"   => "simple",
            "titulo" => "Stock Insuficiente",
            "texto"  => "El producto '{$prod['nombre_producto']}' quedaría con stock negativo ({$nuevoStock}).",
            "icono"  => "error"
          ];
        }
        $cn->prepare("UPDATE productos SET stock_producto = :stock WHERE id_producto = :id")
          ->execute([':stock' => $nuevoStock, ':id' => $prod['id_producto']]);
      }

      // 2. Revertir stock de MATERIAS PRIMAS
      $stmtMp = $cn->prepare("
                SELECT mpc.id_materia_prima, mpc.cantidad_materia_prima, mp.stock_materia_prima, mp.nombre_materia_prima
                FROM materias_primas_compras mpc
                INNER JOIN materias_primas mp ON mpc.id_materia_prima = mp.id_materia_prima
                WHERE mpc.id_compra = :id
            ");
      $stmtMp->execute([':id' => $this->id_compra]);

      foreach ($stmtMp->fetchAll(\PDO::FETCH_ASSOC) as $mp) {
        $nuevoStock = $mp['stock_materia_prima'] - $mp['cantidad_materia_prima'];
        if ($nuevoStock < 0) {
          $cn->rollBack();
          return [
            "tipo"   => "simple",
            "titulo" => "Stock Insuficiente",
            "texto"  => "La materia prima '{$mp['nombre_materia_prima']}' quedaría con stock negativo ({$nuevoStock}).",
            "icono"  => "error"
          ];
        }
        $cn->prepare("UPDATE materias_primas SET stock_materia_prima = :stock WHERE id_materia_prima = :id")
          ->execute([':stock' => $nuevoStock, ':id' => $mp['id_materia_prima']]);
      }

      // 3. Soft delete en cascada
      $cn->prepare('UPDATE compras SET status = 0 WHERE id_compra = :id')
        ->execute([':id' => $this->id_compra]);
      $cn->prepare('UPDATE productos_compras SET status = 0 WHERE id_compra = :id')
        ->execute([':id' => $this->id_compra]);
      $cn->prepare('UPDATE materias_primas_compras SET status = 0 WHERE id_compra = :id')
        ->execute([':id' => $this->id_compra]);

      $bitacora->registrarBitacora('compras', 'Eliminar', 'Eliminación de compra exitosa (' . $this->id_compra . ')');
      $cn->commit();

      return [
        "tipo"   => "simple",
        "titulo" => "Compra eliminada",
        "texto"  => "La compra #{$this->id_compra} fue eliminada y el stock fue revertido correctamente.",
        "icono"  => "success"
      ];
    } catch (\Throwable $e) {
      if (isset($cn) && $cn->inTransaction()) $cn->rollBack();
      $bitacora->registrarBitacora('compras', 'Eliminar', 'Fallido');
      return [
        "tipo"   => "simple",
        "titulo" => "Error",
        "texto"  => "Error al eliminar la compra: " . $e->getMessage(),
        "icono"  => "error"
      ];
    }
  }
}
