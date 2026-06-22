<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\pdfModel;
use src\modelos\productosModelo;
use src\modelos\materiasPrimasModelo;
use src\modelos\bitacoraModelo;
use PDO;

class inventarioModelo extends conexion {
  private string $idProducto = '';
  private string $idMateriaPrima = '';
  private string $idPresentacion = '';
  private float $cantidadMovimiento = 0;
  private int $tipoMovimiento = 0;
  private string $motivoMovimiento = '';
  private string $tipo = '';
  private string $tipoItem = '';
  private array $info = [];

  public function validarInventario(array $instruccionesVal) {
    [
      'infoVal' => &$infoVal,
      'camposVal' => &$camposVal,
    ] = $instruccionesVal;

    $arrayValidaciones = [
      "id_producto" => [
        "campo_nombre" => "id_producto",
        "formulario_nombre" => "id del producto",
        "requerido" => true,
        "minimo" => minRegexIdSeguro,
        "maximo" => maxRegexIdSeguro,
        "expresion_re" => regexIdSeguro,
        "tabla" => "productos",
        "debeExistir" => true,
      ],
      "id_materia_prima" => [
        "campo_nombre" => "id_materia_prima",
        "formulario_nombre" => "id de la materia prima",
        "requerido" => true,
        "minimo" => minRegexIdSeguro,
        "maximo" => maxRegexIdSeguro,
        "expresion_re" => regexIdSeguro,
        "tabla" => "materias_primas",
        "debeExistir" => true,
      ],
      "id_presentacion_producto" => [
        "campo_nombre" => "id_presentacion_producto",
        "formulario_nombre" => "presentación del producto",
        "requerido" => true,
        "minimo" => minRegexIdSeguro,
        "maximo" => maxRegexIdSeguro,
        "tabla" => "presentaciones_productos",
        "debeExistir" => true,
      ],
      "cantidad_movimiento" => [
        "campo_nombre" => "cantidad_movimiento",
        "formulario_nombre" => "cantidad del movimiento",
        "requerido" => true,
        "minimo" => minRegexCantidadItem,
        "maximo" => maxRegexCantidadItem,
        "expresion_re" => regexCantidadItem,
        "comaPunto" => true,
      ],
      "tipo_movimiento" => [
        "campo_nombre" => "tipo_movimiento",
        "formulario_nombre" => "tipo del movimiento",
        "requerido" => true,
        "minimo" => minRegexValorBoleano,
        "maximo" => maxRegexValorBoleano,
        "expresion_re" => regexValorBoleano,
      ],
      "motivo_movimiento" => [
        "campo_nombre" => "motivo_movimiento",
        "formulario_nombre" => "motivo del movimiento",
        "requerido" => true,
        "minimo" => minRegexDescripcion,
        "maximo" => maxRegexDescripcion,
        "expresion_re" => regexDescripcion,
      ],
      "fecha_desde" => [
        "campo_nombre" => "fecha_desde",
        "formulario_nombre" => "fecha desde",
        "requerido" => true,
      ],
      "fecha_hasta" => [
        "campo_nombre" => "fecha_hasta",
        "formulario_nombre" => "fecha hasta",
        "requerido" => true,
      ],
    ];

    $totalValidaciones = [];
    foreach ($camposVal as $campo => $valorForm) {
      if (is_numeric($campo)) $campo = $valorForm;
      $validacion = $arrayValidaciones[$campo];
      $validacion['campo_valor'] = &$infoVal[$campo];
      $totalValidaciones[] = $validacion;
    }
    return $this->limpiar_Verificar($totalValidaciones);
  }

  public function registrarMovimientos(array $info) {
    $this->tipoItem = $info['tipo_item'] ?? '';

    if ($this->tipoItem === 'materia_prima') {
      $resultado = $this->validarInventario([
        'infoVal' => &$info,
        'camposVal' => [
          'id_materia_prima',
          'cantidad_movimiento',
          'tipo_movimiento',
          'motivo_movimiento'
        ]
      ]);
    } else {
      $resultado = $this->validarInventario([
        'infoVal' => &$info,
        'camposVal' => [
          'id_presentacion_producto',
          'cantidad_movimiento',
          'tipo_movimiento',
          'motivo_movimiento'
        ],
      ]);
    }

    if ($resultado) return $resultado;

    $this->cantidadMovimiento = (float) $info['cantidad_movimiento'];
    $this->tipoMovimiento = (int) $info['tipo_movimiento'];
    $this->motivoMovimiento = $info['motivo_movimiento'];

    if ($this->tipoItem === 'materia_prima') {
      $this->idMateriaPrima = $info['id_materia_prima'];
      return $this->registrarMovimientosMateriasPrimasP();
    } else {
      $this->idPresentacion = $info['id_presentacion_producto'];
      // Validar materias primas en CARGAS (tipo_movimiento == 1) - se necesita materia prima para producir
      if ($this->tipoMovimiento == 1) {
        $validacionMP = $this->validarStockMateriasPrimas();
        if ($validacionMP !== true) {
          return $validacionMP;
        }
      }
      return $this->registrarMovimientosProductosP();
    }
  }

  public function verEntradasSalidas(array $info) {
    $this->tipo = $info['tipo'] ?? '';

    if ($this->tipo === 'productos') {
      $this->idProducto = $info['id_producto'] ?? '';
      return $this->verMovimientosProductosP();
    } else if ($this->tipo === 'materiasPrimas') {
      $this->idMateriaPrima = $info['id_materia_prima'] ?? '';
      return $this->verMovimientosMateriasPrimasP();
    }

    return [
      "tipo" => "simple",
      "titulo" => "Error",
      "texto" => "Tipo no reconocido",
      "icono" => "error"
    ];
  }

  public function reporteProductos(array $info) {
    if (empty($info['fecha_desde']) || empty($info['fecha_hasta'])) {
      return [
        'tipo' => 'simple',
        'icono' => 'error',
        'titulo' => 'Sin fecha especificada',
        'texto' => 'Debe agregar una fecha de inicio y fin para el reporte'
      ];
    }
    $this->info = $info;
    $this->info['id_producto'] = $info['id_producto'] ?? null;

    $this->info['fecha_desde'] = $this->FechaHora_Sel("fecha_BD", date('d/m/Y', strtotime($info['fecha_desde'])));
    $this->info['fecha_hasta'] = $this->FechaHora_Sel("fecha_BD", date('d/m/Y', strtotime($info['fecha_hasta'])));

    if ($this->info['fecha_desde'] > $this->info['fecha_hasta']) {
      return [
        'tipo' => 'simple',
        'icono' => 'error',
        'titulo' => 'Fecha de inicio mayor a la fecha de fin',
        'texto' => 'La fecha de inicio del reporte debe ser menor a la de fin'
      ];
    }

    return $this->reporteProductosP();
  }

  public function reporteMateriasPrimas(array $info) {
    if (empty($info['fecha_desde']) || empty($info['fecha_hasta'])) {
      return [
        'tipo' => 'simple',
        'icono' => 'error',
        'titulo' => 'Sin fecha especificada',
        'texto' => 'Debe agregar una fecha de inicio y fin para el reporte'
      ];
    }

    $this->info = $info;
    $this->info['id_materia_prima'] = $info['id_materia_prima'] ?? null;

    $this->info['fecha_desde'] = $this->FechaHora_Sel("fecha_BD", date('d/m/Y', strtotime($info['fecha_desde'])));
    $this->info['fecha_hasta'] = $this->FechaHora_Sel("fecha_BD", date('d/m/Y', strtotime($info['fecha_hasta'])));

    if ($this->info['fecha_desde'] > $this->info['fecha_hasta']) {
      return [
        'tipo' => 'simple',
        'icono' => 'error',
        'titulo' => 'Fecha de inicio mayor a la fecha de fin',
        'texto' => 'La fecha de inicio del reporte debe ser menor a la de fin'
      ];
    }

    return $this->reporteMateriasPrimasP();
  }

  //  METODOS PRIVADOS
  private function validarStockMateriasPrimas() {
    $resultado = $this->seleccionarDatos2([
      'campos' => 'id_producto',
      'tabla' => 'presentaciones_productos',
      'WHERE' => [
        "id_presentacion_producto" => $this->idPresentacion,
      ]
    ]);
    if ($resultado->rowCount() <= 0) {
      return [
        "tipo" => "simple",
        "titulo" => "Error",
        "texto" => "Presentación no encontrada",
        "icono" => "error"
      ];
    }

    $idProducto = $resultado->fetch(PDO::FETCH_COLUMN);

    $resultado = $this->seleccionarDatos2([
      'campos' => 'mpp.cantidad_materia_prima, mp.nombre_materia_prima, mp.stock_materia_prima, mp.id_materia_prima',
      'tabla' => 'materias_primas_productos as mpp',
      'PEL' => 'mpp',
      'datosJoins' => [
        "materias_primas as mp" => "mpp.id_materia_prima = mp.id_materia_prima"
      ],
      'WHERE' => [
        "mpp.id_producto" => $idProducto,
        "mpp.status" => 1
      ]
    ]);
    $materiasPrimas = $resultado->fetchAll();

    if (count($materiasPrimas) == 0) return true;

    $presentacionInfo = $this->seleccionarDatos2([
      'campos' => 'p.cantidad_pmp',
      'tabla' => 'presentaciones_productos as pp',
      'datosJoins' => [
        "presentaciones as p" => "pp.id_presentacion = p.id_presentacion"
      ],
      'WHERE' => [
        "pp.id_presentacion_producto" => $this->idPresentacion,
      ]
    ])->fetch();

    $cantidadPMP = $presentacionInfo['cantidad_pmp'] ?? 1;

    foreach ($materiasPrimas as $mp) {
      $cantidadNecesaria = $this->cantidadMovimiento * ($mp['cantidad_materia_prima'] * $cantidadPMP);
      
      if ($mp['stock_materia_prima'] < $cantidadNecesaria) {
        return [
          "tipo" => "simple",
          "titulo" => "Stock de materia prima insuficiente",
          "texto" => "No hay suficiente stock de la materia prima: " . $mp['nombre_materia_prima'] . 
                    " (Stock: " . $mp['stock_materia_prima'] . ", Necesario: " . $cantidadNecesaria . ")",
          "icono" => "error"
        ];
      }
    }
    return true;
  }

  private function registrarMovimientosProductosP() {
    $objBitacora = new bitacoraModelo();
    
    $resultado = $this->seleccionarDatos2([
      'campos' => '
        pp.id_producto, pp.id_presentacion_producto, pr.id_categoria_producto, 
        pr.nombre_producto, pr.stock_producto, pr.stock_minimo_producto,
        p.nombre_presentacion, p.cantidad_pmp
      ',
      'tabla' => 'presentaciones_productos as pp',
      'datosJoins' => [
        "productos as pr" => "pp.id_producto = pr.id_producto",
        "presentaciones as p" => "pp.id_presentacion = p.id_presentacion"
      ],
      'WHERE' => [
        "pp.id_presentacion_producto" => $this->idPresentacion,
      ]
    ]);

    if ($resultado->rowCount() <= 0) {
      return [
        "tipo" => "simple",
        "titulo" => "Presentación no encontrada",
        "texto" => "La presentación seleccionada no existe",
        "icono" => "error"
      ];
    }

    $info = $resultado->fetch(PDO::FETCH_ASSOC);
    $idProducto = $info['id_producto'];
    $nombreProducto = $info['nombre_producto'];
    $nombrePresentacion = $info['nombre_presentacion'];
    $stockActual = $info['stock_producto'];
    $cantidadPMP = $info['cantidad_pmp'];

    $nuevoStock = ($this->tipoMovimiento == 1) ? $stockActual + $this->cantidadMovimiento : $stockActual - $this->cantidadMovimiento;

    if ($this->tipoMovimiento == 0 && $nuevoStock < 0) {
      return [
        "tipo" => "simple",
        "titulo" => "Stock insuficiente",
        "texto" => "No hay suficiente stock del producto",
        "icono" => "error"
      ];
    }

    $datosBitacora = [
      'nombre_producto' => $nombreProducto,
      'nombre_presentacion' => $nombrePresentacion,
      'tipo_movimiento' => $this->tipoMovimiento == 1 ? 'CARGA' : 'DESCARGA',
      'cantidad' => $this->cantidadMovimiento,
      'stock_antes' => $stockActual,
      'stock_despues' => $nuevoStock,
      'fecha' => date('Y-m-d H:i:s')
    ];

    // Registrar movimiento
    $ultimoId = $this->guardarDatos2([
      'tabla' => 'movimientos_anomalos_productos',
      'datos' => [
        "id_presentacion_producto" => $this->idPresentacion,
        "cantidad_movimiento" => $this->cantidadMovimiento,
        "tipo_movimiento" => $this->tipoMovimiento,
        "motivo_movimiento" => $this->motivoMovimiento,
        "fecha_movimiento" => $this->FechaHora_Sel('fecha_hora_BD'),
      ]
    ]);

    if ($ultimoId === false || $ultimoId <= 0) {
      $this->rollback();
      $objBitacora->registrarBitacora("Inventario", "Registrar movimiento de producto", "Error", $datosBitacora, true);
      return [
        "tipo" => "simple",
        "titulo" => "Movimiento no registrado",
        "texto" => "El movimiento no ha podido ser registrado",
        "icono" => "error"
      ];
    }
    $materiasPrimas = $this->seleccionarDatos2([
      'campos' => 'mpp.id_materia_prima, mpp.cantidad_materia_prima',
      'tabla' => 'materias_primas_productos as mpp',
      'WHERE' => [
        "mpp.id_producto" => $idProducto,
        "mpp.status" => 1
      ]
    ])->fetchAll();
    
    foreach ($materiasPrimas as $mp) {
      $cantidadAjuste = $this->cantidadMovimiento * ($mp['cantidad_materia_prima'] * $cantidadPMP);
      
      // Obtener stock actual de la materia prima
      $stockMP = $this->seleccionarDatos2([
        'campos' => 'stock_materia_prima',
        'tabla' => 'materias_primas',
        'WHERE' => [
          'id_materia_prima' => $mp['id_materia_prima']
        ]
      ])->fetch(PDO::FETCH_COLUMN);
      
      $nuevoStockMP = ($this->tipoMovimiento == 1) 
        ? $stockMP - $cantidadAjuste 
        : $stockMP + $cantidadAjuste;
      
      $this->actualizarDatos2([
        'tabla' => 'materias_primas',
        'datos' => [
          'stock_materia_prima' => $nuevoStockMP
        ],
        'WHERE' => [
          'id_materia_prima' => $mp['id_materia_prima']
        ]
      ]);
    }

    // Actualizar stock del producto
    $this->actualizarDatos2([
      'tabla' => 'productos',
      'datos' => ['stock_producto' => $nuevoStock],
      'WHERE' => ['id_producto' => $idProducto]
    ]);

    $this->commit();

    $objBitacora->registrarBitacora("Inventario", "Registrar movimiento de producto", "Éxito", $datosBitacora, true);

    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Movimiento registrado",
      "texto" => "Movimiento registrado correctamente",
      "icono" => "success"
    ];
  }

  private function registrarMovimientosMateriasPrimasP() {
    $objBitacora = new bitacoraModelo();
    
    $resultado = $this->seleccionarDatos2([
      'campos' => 'stock_materia_prima, stock_minimo_materia_prima, nombre_materia_prima',
      'tabla' => 'materias_primas',
      'WHERE' => [
        "id_materia_prima" => $this->idMateriaPrima,
      ]
    ]);

    if ($resultado->rowCount() <= 0) {
      return [
        "tipo" => "simple",
        "titulo" => "Materia prima no encontrada",
        "texto" => "La materia prima seleccionada no existe",
        "icono" => "error"
      ];
    }

    $materiaPrima = $resultado->fetch();
    $stockActual = $materiaPrima['stock_materia_prima'];
    $nombreMP = $materiaPrima['nombre_materia_prima'];

    // Calcular nuevo stock
    $nuevoStock = ($this->tipoMovimiento == 1) ? $stockActual + $this->cantidadMovimiento : $stockActual - $this->cantidadMovimiento;

    if ($this->tipoMovimiento == 0 && $nuevoStock < 0) {
      return [
        "tipo" => "simple",
        "titulo" => "Stock insuficiente",
        "texto" => "No hay suficiente stock de la materia prima",
        "icono" => "error"
      ];
    }

    $datosBitacora = [
      'nombre_materia_prima' => $nombreMP,
      'tipo_movimiento' => $this->tipoMovimiento == 1 ? 'CARGA' : 'DESCARGA',
      'cantidad' => $this->cantidadMovimiento,
      'stock_antes' => $stockActual,
      'stock_despues' => $nuevoStock,
      'fecha' => date('Y-m-d H:i:s')
    ];

    // Registrar movimiento
    $ultimoId = $this->guardarDatos2([
      'tabla' => 'movimientos_anomalos_materias_primas',
      'datos' => [
        "id_materia_prima" => $this->idMateriaPrima,
        "cantidad_movimiento" => $this->cantidadMovimiento,
        "tipo_movimiento" => $this->tipoMovimiento,
        "motivo_movimiento" => $this->motivoMovimiento,
        "fecha_movimiento" => $this->FechaHora_Sel('fecha_hora_BD'),
      ]
    ]);

    if ($ultimoId === false || $ultimoId <= 0) {
      $this->rollback();
      $objBitacora->registrarBitacora("Inventario", "Registrar movimiento de materia prima", "Error", $datosBitacora, true);
      return [
        "tipo" => "simple",
        "titulo" => "Movimiento no registrado",
        "texto" => "El movimiento no ha podido ser registrado",
        "icono" => "error"
      ];
    }

    // Actualizar stock de la materia prima
    $this->actualizarDatos2([
      'tabla' => 'materias_primas',
      'datos' => ['stock_materia_prima' => $nuevoStock],
      'WHERE' => ['id_materia_prima' => $this->idMateriaPrima]
    ]);

    $this->commit();

    $objBitacora->registrarBitacora("Inventario", "Registrar movimiento de materia prima", "Éxito", $datosBitacora, true);

    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Movimiento registrado",
      "texto" => "Movimiento registrado correctamente",
      "icono" => "success"
    ];
  }

  private function verMovimientosProductosP() {
    return $this->seleccionarDatos2([
      'campos' => 'map.id_movimiento_anomalo_producto, p.nombre_presentacion, map.cantidad_movimiento, map.tipo_movimiento, map.motivo_movimiento, map.fecha_movimiento, pr.id_producto, pr.nombre_producto',
      'tabla' => 'movimientos_anomalos_productos as map',
      'PEL' => 'map',
      'datosJoins' => [
        "presentaciones_productos as pp" => "map.id_presentacion_producto = pp.id_presentacion_producto",
        "presentaciones as p" => "pp.id_presentacion = p.id_presentacion",
        "productos as pr" => "pp.id_producto = pr.id_producto"
      ],
      'WHERE' => [
        "pr.id_producto" => $this->idProducto,
      ],
      'ORDER' => 'map.id_movimiento_anomalo_producto DESC'
    ])->fetchAll();
  }

  private function verMovimientosMateriasPrimasP() {
    return $this->seleccionarDatos2([
      'campos' => '
        mamp.id_movimiento_anomalo_materia_prima, 
        mp.nombre_materia_prima, mamp.cantidad_movimiento,
        mamp.tipo_movimiento, mamp.motivo_movimiento, 
        mamp.fecha_movimiento
      ',
      'tabla' => 'movimientos_anomalos_materias_primas as mamp',
      'datosJoins' => [
        "materias_primas as mp" => "mamp.id_materia_prima = mp.id_materia_prima"
      ],
      'WHERE' => [
        "mp.id_materia_prima" => $this->idMateriaPrima,
      ],
      'ORDER' => 'mamp.id_movimiento_anomalo_materia_prima DESC'
    ])->fetchAll();
  }

  private function reporteProductosP() {

    $WHERE = [
      "DATE(map.fecha_movimiento)" => [
        '>=' => $this->info['fecha_desde'],
        '<=' => $this->info['fecha_hasta'],
      ]
    ];
    if (!empty($this->info['id_producto'])) {
      $WHERE['pr.id_producto'] = $this->info['id_producto'];
    }

    $instruccionesDB = [
      'tabla' => 'movimientos_anomalos_productos as map',
      'campos' => '
        map.id_movimiento_anomalo_producto, p.nombre_presentacion,
        map.cantidad_movimiento,map.tipo_movimiento,map.motivo_movimiento,
        map.fecha_movimiento,pr.nombre_producto
      ',
      'datosJoins' => [
        "presentaciones_productos as pp" => "map.id_presentacion_producto = pp.id_presentacion_producto",
        "presentaciones as p" => "pp.id_presentacion = p.id_presentacion",
        "productos as pr" => "pp.id_producto = pr.id_producto"
      ],
      'WHERE' => $WHERE,
      'ORDER' => 'map.fecha_movimiento ASC'
    ];

    $infoCeldas = $this->seleccionarDatos2($instruccionesDB)->fetchAll();

    if (empty($infoCeldas)) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Sin registros',
        'texto' => 'No hay movimientos en el rango de fechas seleccionado',
        'icono' => 'warning',
      ];
    }

    foreach ($infoCeldas as &$fila) {
      $fila['fecha_movimiento'] = $this->FechaHora_Sel('fecha_hora_AM_PM', $fila['fecha_movimiento']);
      $fila['tipo_movimiento'] = $fila['tipo_movimiento'] == 1 ? 'CARGA' : 'DESCARGA';
    }
    unset($fila);

    $objetoPDF = new pdfModel();
    $objetoPDF->SetTitle('REPORTE DE MOVIMIENTOS');

    return $objetoPDF->crearPDF([
      "tituloReporte" => "REPORTE DE MOVIMIENTOS",
      "datosExtCabecera" => [
        "PRODUCTOS Desde: " . date('d-m-Y', strtotime($this->info['fecha_desde'])) . " Hasta: " . date('d-m-Y', strtotime($this->info['fecha_hasta']))
      ],
      "configColumnas" => [
        'id_movimiento_anomalo_producto' => ['ID', 10],
        'nombre_producto' => ['PRODUCTO', 35],
        'nombre_presentacion' => ['PRESENTACION', 35],
        'tipo_movimiento' => ['TIPO', 25],
        'cantidad_movimiento' => ['CANT', 20],
        'motivo_movimiento' => ['MOTIVO', 30],
        'fecha_movimiento' => ['FECHA', 35],
      ],
      "infoBD" => $infoCeldas,
    ]);
  }

  private function reporteMateriasPrimasP() {
    $where = [];
    $where['DATE(mamp.fecha_movimiento)'] = [
      ">=" => $this->info['fecha_desde'],
      "<=" => $this->info['fecha_hasta']
    ];
    if (!empty($this->info['id_materia_prima'])) {
      $where['mp.id_materia_prima'] = $this->info['id_materia_prima'];
    }
    $infoCeldas = $this->seleccionarDatos2([
      'tabla' => 'movimientos_anomalos_materias_primas as mamp',
      'campos' => '
        mamp.id_movimiento_anomalo_materia_prima,
        mp.nombre_materia_prima,
        mamp.cantidad_movimiento,
        mamp.tipo_movimiento,
        mamp.motivo_movimiento,
        mamp.fecha_movimiento
      ',
      'datosJoins' => [
        "materias_primas as mp" => "mamp.id_materia_prima = mp.id_materia_prima"
      ],
      'WHERE' => $where,
      'ORDER' => 'mamp.fecha_movimiento ASC'
    ])->fetchAll();

    if (empty($infoCeldas)) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Sin registros',
        'texto' => 'No hay movimientos en el rango de fechas seleccionado',
        'icono' => 'warning',
      ];
    }

    foreach ($infoCeldas as &$fila) {
      $fila['fecha_movimiento'] = $this->FechaHora_Sel('fecha_hora_AM_PM', $fila['fecha_movimiento']);
      $fila['tipo_movimiento'] = $fila['tipo_movimiento'] == 1 ? 'CARGA' : 'DESCARGA';
    }
    unset($fila);

    $objetoPDF = new pdfModel();
    $objetoPDF->SetTitle('REPORTE DE MOVIMIENTOS ');

    return $objetoPDF->crearPDF([
      "tituloReporte" => "REPORTE DE MOVIMIENTOS",
      "datosExtCabecera" => [
        "MATERIAS PRIMAS Desde: " . date('d-m-Y', strtotime($this->info['fecha_desde'])) . " Hasta: " . date('d-m-Y', strtotime($this->info['fecha_hasta']))
      ],
      "configColumnas" => [
        'id_movimiento_anomalo_materia_prima' => ['ID', 15],
        'nombre_materia_prima' => ['MATERIA PRIMA', 40],
        'tipo_movimiento' => ['TIPO', 25],
        'cantidad_movimiento' => ['CANT', 25],
        'motivo_movimiento' => ['MOTIVO', 45],
        'fecha_movimiento' => ['FECHA', 40],
      ],
      "infoBD" => $infoCeldas,
    ]);
  }
}