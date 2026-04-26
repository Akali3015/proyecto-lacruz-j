<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use PDO;
use PDOException;
use Exception;

class produccionesModelo extends conexion
{
  private $idProduccion;
  private $detalles = [];

  public function seleccionarProduccion($idProduccion = null)
  {
    $this->idProduccion = $idProduccion;
    if ($this->idProduccion != null && $this->idProduccion != "") {
      $campos = [
        [
          "campo_nombre" => 'id_produccion',
          "campo_valor" => $this->idProduccion,
          "formulario_nombre" => "id de la produccion",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => 'producciones',
          "debeExistir" => true,
        ]
      ];

      $respuesta = $this->limpiar_Verificar($campos);
      if ($respuesta !== false) {
        return $respuesta;
        exit();
      }
    }
    return $this->seleccionarProduccionP();
  }
  public function registrarProduccion($detalles)
  {
    // $detalles = json_decode($detalles, true);

    foreach ($detalles as &$detalle) {

      $campos[] = [
        'campo_valor' => &$detalle['cantidad'],
        'formulario_nombre' => 'cantidad del producto',
        'requerido' => true,
        'minimo' => minRegexCantidadItem,
        'maximo' => maxRegexCantidadItem,
        'expresion_re' => regexCantidadItem,
      ];
      $campos[] = [
        'campo_valor' => &$detalle['id'],
        'formulario_nombre' => 'id del producto',
        'requerido' => true,
        'minimo' => minRegexId,
        'maximo' => maxRegexId,
        'expresion_re' =>  regexId,
      ];
    };
    unset($detalle);
    $resultado = $this->limpiar_Verificar($campos);
    if ($resultado != false) {
      return $resultado;
    }

    $this->detalles = $detalles;

    return $this->registrarProduccionP();
  }
  public function actualizarProduccion($idProduccion, $detalles)
  {
    // $detalles = json_decode($detalles, true);
    $campos = [
      [
        'campo_nombre' => 'id_produccion',
        'campo_valor' => &$idProduccion,
        'formulario_nombre' => 'id de la produccion',
        'requerido' => true,
        'minimo' => minRegexId,
        'maximo' => maxRegexId,
        'expresion_re' => regexId,
      ],
    ];
    foreach ($detalles as &$detalle) {
      $campos[] = [
        'campo_valor' => &$detalle['cantidad'],
        'formulario_nombre' => 'cantidad del producto',
        'requerido' => true,
        'minimo' => minRegexCantidadItem,
        'maximo' => maxRegexCantidadItem,
        'expresion_re' => regexCantidadItem,
      ];
      $campos[] = [
        'campo_valor' => &$detalle['id'],
        'formulario_nombre' => 'id del producto',
        'requerido' => true,
        'minimo' => minRegexId,
        'maximo' => maxRegexId,
        'expresion_re' =>  regexId,
      ];
    };
    unset($detalle);
    $resultado = $this->limpiar_Verificar($campos);
    if ($resultado != false) {
      return $resultado;
    }

    $this->idProduccion = $idProduccion;
    $this->detalles = $detalles;
    return $this->actualizarProduccionP($idProduccion, $detalles);
  }

  private function seleccionarProduccionP()
  {
    if ($this->idProduccion == null || $this->idProduccion == "") {
      $instruccionesBD = [
        'campos' => 'p.id_produccion, p.fecha_produccion',
        'tabla' => 'producciones as p',
        'PEL' => 'p',
      ];
      $resultado = $this->seleccionarDatos($instruccionesBD);
      $Producciones = $resultado->fetchAll(PDO::FETCH_ASSOC);
      return $Producciones;
    } else {
      $instruccionesBD = [
        'campos' => '*',
        'tabla' => 'producciones',
        'WHERE' => [
          "id_produccion" => $this->idProduccion,
        ]
      ];
      $resultado = $this->seleccionarDatos2($instruccionesBD);
      if ($resultado->rowCount() <= 0) {
        $alerta = [
          "tipo" => "simple",
          "titulo" => "Produccion no encontrado",
          "texto" => "La produccion que ha intentado actualizar no se encuentra en la base de datos",
          "icono" => "error"
        ];
        return $alerta;
      }
      $produccion = $resultado->fetch(PDO::FETCH_ASSOC);

      $instruccionesBD = [
        'campos' => '*',
        'tabla' => 'productos_producciones',
        'WHERE' => [
          "id_produccion" => $this->idProduccion,
        ]
      ];
      $resultado = $this->seleccionarDatos2($instruccionesBD);
      if ($resultado->rowCount() <= 0) {
        $alerta = [
          "tipo" => "simple",
          "titulo" => "Detalles no encontrados",
          "texto" => "Los detalles de la produccion que ha intentado actualizar no se encuentran en la base de datos",
          "icono" => "error"
        ];
        return $alerta;
      }
      $detalles = $resultado->fetchAll(PDO::FETCH_ASSOC);
      $produccion['detalles'] = $detalles;
      return $produccion;
    }
  }
  private function registrarProduccionP()
  {
    $idProduccion = $this->guardarDatos2([
      'tabla' => 'producciones',
      'datos' => [
        'fecha_produccion' => $this->FechaHora_Sel('fecha_hora_BD')
      ]
    ]);
    foreach ($this->detalles as $detalle) {
      // registramos en la BD
      $idDetalle = $this->guardarDatos2([
        'tabla' => 'productos_producciones',
        'datos' => [
          'id_produccion' => $idProduccion,
          'id_producto' => $detalle['id'],
          'cantidad_producida' => $detalle['cantidad'],
        ]
      ]);
      if ($idDetalle == 0 || $idDetalle == false) {
        $this->rollback();
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'Ocurrió un error registrando los productos',
          'icono' => 'error'
        ];
      }

      // Disminucion del stock de la materia prima
      $resultado = $this->seleccionarDatos2([
        'tabla' => 'productos',
        'campos' => '*',
        'WHERE' => [
          'id_producto' => $detalle['id']
        ]
      ]);
      $infoGeneralProducto = $resultado->fetch(PDO::FETCH_ASSOC);

      // Incremento del stock del producto
      $resultado = $this->actualizarDatos2([
        'tabla' => 'productos',
        'datos' => [
          'stock_producto' => ($infoGeneralProducto['stock_producto'] + $detalle['cantidad'])
        ],
        'WHERE' => [
          'id_producto' => $detalle['id']
        ]
      ]);
      if ($resultado === false || $resultado <= 0) {
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'Ocurrió un error actualizando el stock de los productos',
          'icono' => 'error'
        ];
      }

      // Consulta de las materias primas que usa dicho producto
      $resultado = $this->seleccionarDatos2([
        'tabla' => 'materias_primas_productos',
        'campos' => '*',
        'WHERE' => [
          'id_producto' => $detalle['id']
        ]
      ]);
      $materiasPrimaProducto = $resultado->fetchAll(PDO::FETCH_ASSOC);

      foreach ($materiasPrimaProducto as $materiaPrima) {
        $resultado = $this->seleccionarDatos2([
          'tabla' => 'materias_primas',
          'campos' => 'stock_materia_prima',
          'WHERE' => [
            'id_materia_prima' => $materiaPrima['id_materia_prima']
          ]
        ]);
        $cantidadTotalRestar = $detalle['cantidad'] * $materiaPrima['cantidad_materia_prima'];

        // Decremento del stock de la materia prima
        $resultado = $this->seleccionarDatos2([
          'tabla' => 'materias_primas',
          'campos' => 'stock_materia_prima',
          'WHERE' => [
            'id_materia_prima' => $materiaPrima['id_materia_prima']
          ]
        ]);
        $stockMP = $resultado->fetch(PDO::FETCH_COLUMN);

        $nuevoStock = ($stockMP - $cantidadTotalRestar);
        if ($nuevoStock < 0) {
          $this->rollback();
          return [
            'tipo' => 'simple',
            'titulo' => 'Error',
            'texto' => '
                    No se puede descontar una cantidad mayor de materia prima a 
                    la disponible dentro del stock de inventario
                ',
            'icono' => 'error'
          ];
        }
        $resultado = $this->actualizarDatos2([
          'tabla' => 'materias_primas',
          'datos' => [
            'stock_materia_prima' => $nuevoStock
          ],
          'WHERE' => [
            'id_materia_prima' => $materiaPrima['id_materia_prima']
          ]
        ]);
        if ($resultado === false || $resultado <= 0) {
          $this->rollback();
          return [
            'tipo' => 'simple',
            'titulo' => 'Error',
            'texto' => 'Ocurrió un error actualizando el stock de los productos',
            'icono' => 'error'
          ];
        }
      }
    }
    $this->commit();
    return [
      'tipo' => 'limpiarYcerrar',
      'icono' => 'success',
      'titulo' => 'Exito en el registro',
      'texto' => 'Se registró correctamente la producción',
    ];
  }
  private function actualizarProduccionP($idProduccion, $detalles)
  {
    $resultado = $this->seleccionarDatos2([
      'tabla' => 'productos_producciones',
      'campos' => '*',
      'WHERE' => [
        'id_produccion' => $idProduccion
      ]
    ]);

    $detallesActuales = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $mapeoProductos = [];
    foreach ($detallesActuales as $detalle) {
      if (!isset($mapeoProductos[$detalle['id_producto']])) {
        $mapeoProductos[$detalle['id_producto']] = $detalle['cantidad_producida'];
      } else {
        $mapeoProductos[$detalle['id_producto']] += $detalle['cantidad_producida'];
      }
    }

    // Limpieza inicial
    $resultado = $this->eliminarDatos2([
      'tabla' => 'productos_producciones',
      'WHERE' => [
        'id_produccion' => $idProduccion
      ]
    ]);

    // Al reves logica de stock
    foreach ($mapeoProductos as $idProducto => $cantidadProducto) {
      $resultado = $this->seleccionarDatos2([
        'tabla' => 'productos',
        'campos' => '*',
        'WHERE' => [
          'id_producto' => $idProducto
        ]
      ]);
      $infoGeneralProducto = $resultado->fetch(PDO::FETCH_ASSOC);

      // Restamos del stock del producto
      $resultado = $this->actualizarDatos2([
        'tabla' => 'productos',
        'datos' => [
          'stock_producto' => ($infoGeneralProducto['stock_producto'] - $cantidadProducto)
        ],
        'WHERE' => [
          'id_producto' => $idProducto
        ]
      ]);
      if ($resultado = false || $resultado <= 0) {
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'Ocurrió un error actualizando el stock de los productos',
          'icono' => 'error'
        ];
      }

      // Consulta de las materias primas que usa dicho producto
      $resultado = $this->seleccionarDatos2([
        'tabla' => 'materias_primas_productos',
        'campos' => '*',
        'WHERE' => [
          'id_producto' => $idProducto
        ]
      ]);
      $materiasPrimaProducto = $resultado->fetchAll(PDO::FETCH_ASSOC);

      foreach ($materiasPrimaProducto as $materiaPrima) {
        $resultado = $this->seleccionarDatos2([
          'tabla' => 'materias_primas',
          'campos' => 'stock_materia_prima',
          'WHERE' => [
            'id_materia_prima' => $materiaPrima['id_materia_prima']
          ]
        ]);
        $cantidadTotalSumar = $cantidadProducto * $materiaPrima['cantidad_materia_prima'];

        // Decremento del stock de la materia prima
        $resultado = $this->seleccionarDatos2([
          'tabla' => 'materias_primas',
          'campos' => 'stock_materia_prima',
          'WHERE' => [
            'id_materia_prima' => $materiaPrima['id_materia_prima']
          ]
        ]);
        $stockMP = $resultado->fetch(PDO::FETCH_COLUMN);

        $nuevoStock = ($stockMP + $cantidadTotalSumar);
        $resultado = $this->actualizarDatos2([
          'tabla' => 'materias_primas',
          'datos' => [
            'stock_materia_prima' => $nuevoStock
          ],
          'WHERE' => [
            'id_materia_prima' => $materiaPrima['id_materia_prima']
          ]
        ]);
        if ($resultado = false || $resultado <= 0) {
          $this->rollback();
          return [
            'tipo' => 'simple',
            'titulo' => 'Error',
            'texto' => 'Ocurrió un error actualizando el stock de los productos',
            'icono' => 'error'
          ];
        }
      }
    }

    // Registramos los detalles nuevos de produccion [productos_producciones]
    $detallesJSON = json_decode($detalles, true);
    foreach ($detallesJSON as $detalle) {
      // registramos en la BD
      $idDetalle = $this->guardarDatos2([
        'tabla' => 'productos_producciones',
        'datos' => [
          'id_produccion' => $idProduccion,
          'id_producto' => $detalle['id'],
          'cantidad_producida' => $detalle['cantidad'],
        ]
      ]);
      if ($idDetalle == 0 || $idDetalle == false) {
        $this->rollback();
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'Ocurrió un error registrando los productos',
          'icono' => 'error'
        ];
      }

      // Disminucion del stock de la materia prima
      $resultado = $this->seleccionarDatos2([
        'tabla' => 'productos',
        'campos' => '*',
        'WHERE' => [
          'id_producto' => $detalle['id']
        ]
      ]);
      $infoGeneralProducto = $resultado->fetch(PDO::FETCH_ASSOC);

      // Incremento del stock del producto
      $resultado = $this->actualizarDatos2([
        'tabla' => 'productos',
        'datos' => [
          'stock_producto' => ($infoGeneralProducto['stock_producto'] + $detalle['cantidad'])
        ],
        'WHERE' => [
          'id_producto' => $detalle['id']
        ]
      ]);
      if ($resultado = false || $resultado <= 0) {
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'Ocurrió un error actualizando el stock de los productos',
          'icono' => 'error'
        ];
      }

      // Consulta de las materias primas que usa dicho producto
      $resultado = $this->seleccionarDatos2([
        'tabla' => 'materias_primas_productos',
        'campos' => '*',
        'WHERE' => [
          'id_producto' => $detalle['id_producto']
        ]
      ]);
      $materiasPrimaProducto = $resultado->fetchAll(PDO::FETCH_ASSOC);

      foreach ($materiasPrimaProducto as $materiaPrima) {
        $resultado = $this->seleccionarDatos2([
          'tabla' => 'materias_primas',
          'campos' => 'stock_materia_prima',
          'WHERE' => [
            'id_materia_prima' => $materiaPrima['id_materia_prima']
          ]
        ]);
        $cantidadTotalRestar = $detalle['cantidad'] * $materiaPrima['cantidad_materia_prima'];

        // Decremento del stock de la materia prima
        $resultado = $this->seleccionarDatos2([
          'tabla' => 'materias_primas',
          'campos' => 'stock_materia_prima',
          'WHERE' => [
            'id_materia_prima' => $materiaPrima['id_materia_prima']
          ]
        ]);
        $stockMP = $resultado->fetch(PDO::FETCH_COLUMN);

        $nuevoStock = ($stockMP - $cantidadTotalRestar);
        if ($nuevoStock < 0) {
          $this->rollback();
          return [
            'tipo' => 'simple',
            'titulo' => 'Error',
            'texto' => '
                    No se puede descontar una cantidad mayor de materia prima a la disponible dentro del stock de inventario
                    ',
            'icono' => 'error'
          ];
        }
        $resultado = $this->actualizarDatos2([
          'tabla' => 'materias_primas',
          'datos' => [
            'stock_materia_prima' => $nuevoStock
          ],
          'WHERE' => [
            'id_materia_prima' => $materiaPrima['id_materia_prima']
          ]
        ]);
        if ($resultado = false || $resultado <= 0) {
          $this->rollback();
          return [
            'tipo' => 'simple',
            'titulo' => 'Error',
            'texto' => 'Ocurrió un error actualizando el stock de los productos',
            'icono' => 'error'
          ];
        }
      }
    }

    $this->commit();
    return [
      'icono' => 'success',
      'titulo' => 'Exito en la actualización',
      'texto' => 'Se actualización correctamente la producción',
      'tipo' => 'simple'
    ];
  }
}
