<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use src\modelos\productosModelo;
use src\modelos\materiasPrimasModelo;
use src\modelos\mensajesWSModelo;
use PDO;

class produccionesModelo extends conexion {
  private string $idProduccion = '';
  private array $productos = [];

  public function validarProducciones(array $instruccionesVal) {
    [
      'infoVal' => &$infoVal,
      'camposVal' => &$camposVal,
    ] = $instruccionesVal;

    $funcionAsignadora = function ($nombreCampo, &$valor) {
      $claveVal = [
        "id_produccion" => [
          "campo_nombre" => "id_produccion",
          "campo_valor" => &$valor,
          "formulario_nombre" => "id de la producción",
          "requerido" => true,
          "minimo" => minRegexIdSeguro,
          "maximo" => maxRegexIdSeguro,
          "expresion_re" => regexIdSeguro,
          "tabla" => "producciones",
          "debeExistir" => true,
          "debeSerUnico" => true
        ],
        "id_producto" => [
          "campo_nombre" => "id_producto",
          "campo_valor" => &$valor,
          "formulario_nombre" => "id del producto",
          "requerido" => true,
          "minimo" => minRegexIdSeguro,
          "maximo" => maxRegexIdSeguro,
          "expresion_re" => regexIdSeguro,
          "tabla" => "productos",
          "debeExistir" => true,
        ],
        "cantidad_producida" => [
          "campo_valor" => &$valor,
          "formulario_nombre" => "cantidad del producto",
          "requerido" => true,
          "minimo" => minRegexCantidadItem,
          "maximo" => maxRegexCantidadItem,
          "expresion_re" => regexCantidadItem,
        ],
      ];
      return $claveVal[$nombreCampo];
    };
    $campos = [];
    foreach ($camposVal as $campo) {
      if ($campo == 'productos') {
        if (empty($infoVal['productos']) || !is_array($infoVal['productos'])) {
          return [
            'tipo' => 'simple',
            'titulo' => 'Error',
            'texto' => 'No se recibieron detalles de productos',
            'icono' => 'error'
          ];
        }
        foreach ($infoVal['productos'] as &$detalle) {
          $campos[] = $funcionAsignadora('id_producto', $detalle['id_producto']);
          $campos[] = $funcionAsignadora('cantidad_producida', $detalle['cantidad_producida']);
        };
        unset($detalle);
      } else {
        $campos[] = $funcionAsignadora($campo, $infoVal[$campo]);
      }
    }
    return $this->limpiar_Verificar($campos);
  }

  public function seleccionarProducciones(array $info) {
    if (($info['id_produccion'] ?? '') != '') {
      $resultado = $this->validarProducciones([
        'infoVal' => &$info,
        'camposVal' => [
          'id_produccion',
        ],
      ]);
      if ($resultado) return $resultado;
      $this->idProduccion = $info['id_produccion'];
    }
    return $this->seleccionarProduccionesP();
  }

  public function registrarProducciones(array $info) {
    $resultado = $this->validarProducciones([
      'infoVal' => &$info,
      'camposVal' => [
        'productos',
      ],
    ]);
    if ($resultado) return $resultado;
    $this->productos = $info['productos'];
    return $this->registrarProduccionesP();
  }

  public function actualizarProducciones(array $info) {
    $resultado = $this->validarProducciones([
      'infoVal' => &$info,
      'camposVal' => [
        'id_produccion',
        'productos',
      ],
    ]);
    if ($resultado) return $resultado;
    $this->idProduccion = $info['id_produccion'];
    $this->productos = $info['productos'];
    return $this->actualizarProduccionesP();
  }

  private function seleccionarProduccionesP() {
    if ($this->idProduccion == null || $this->idProduccion == "") {
      $resultado = $this->seleccionarDatos2([
        'campos' => 'id_produccion, fecha_produccion',
        'tabla' => 'producciones',
      ]);
      $Producciones = $resultado->fetchAll();
      return $Producciones;
    } else {
      $resultado = $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'producciones',
        'WHERE' => [
          "id_produccion" => $this->idProduccion,
        ]
      ]);
      if ($resultado->rowCount() <= 0) {
        return [
          "tipo" => "simple",
          "titulo" => "Produccion no encontrada",
          "texto" => "La produccion que ha intentado consultar no se encuentra en la base de datos",
          "icono" => "error"
        ];
      }
      $produccion = $resultado->fetch();
      $resultado = $this->seleccionarDatos2([
        'campos' => 'id_producto, cantidad_producida',
        'tabla' => 'productos_producciones',
        'WHERE' => [
          "id_produccion" => $this->idProduccion,
        ]
      ]);
      if ($resultado->rowCount() <= 0) {
        $produccion['detalles'] = [];
      } else {
        $detalles = $resultado->fetchAll();
        $produccion['detalles'] = $detalles;
      }
      return $produccion;
    }
  }

  private function registrarProduccionesP() {
    $objBitacora = new bitacoraModelo();
    $idProduccion = $this->generarCodSeg([
      'tablaBD' => 'producciones',
      'prefijo' => 'PROD',
      'campoID' => 'id_produccion'
    ]);

    $resultadoGuardar = $this->guardarDatos2([
      'tabla' => 'producciones',
      'datos' => [
        'id_produccion' => $idProduccion,
        'fecha_produccion' => $this->FechaHora_Sel('fecha_hora_BD')
      ]
    ]);

    if ($resultadoGuardar === false || $resultadoGuardar <= 0) {
      $objBitacora->registrarBitacora("producciones", "Registrar producción", "Error", [
        'id_produccion' => $idProduccion,
        'error' => 'No se pudo crear la producción'
      ], true);
      return [
        'tipo' => 'simple',
        'titulo' => 'Error',
        'texto' => 'Ocurrió un error al crear la producción',
        'icono' => 'error'
      ];
    }

    $this->productos = $this->indexarArrays([
      'indice' => 'id_producto',
      'camposSumar' => 'cantidad_producida',
      'array' => $this->productos,
    ]);

    $productosProcesados = [];
    $materiasPrimasDescontadas = [];

    foreach ($this->productos as $id => $cantidad) {
      $idDetalle = $this->guardarDatos2([
        'tabla' => 'productos_producciones',
        'datos' => [
          'id_produccion' => $idProduccion,
          'id_producto' => $id,
          'cantidad_producida' => $cantidad,
        ]
      ]);

      if ($idDetalle == 0 || $idDetalle == false) {
        $this->rollback();
        $objBitacora->registrarBitacora("producciones", "Registrar producción", "Error", [
          'id_produccion' => $idProduccion,
          'error' => 'Error registrando productos'
        ], true);
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'Ocurrió un error registrando los productos',
          'icono' => 'error'
        ];
      }

      $objProducto = new productosModelo();
      $infoGeneralProducto = $objProducto->seleccionarProductos([
        'id_producto' => $id
      ]);

      if (isset($infoGeneralProducto['tipo']) && $infoGeneralProducto['tipo'] == 'simple') {
        $this->rollback();
        $objBitacora->registrarBitacora("producciones", "Registrar producción", "Error", [
          'id_produccion' => $idProduccion,
          'id_producto' => $id,
          'error' => 'Producto no encontrado'
        ], true);
        return $infoGeneralProducto;
      }

      $stockAnterior = $infoGeneralProducto['stock_producto'];
      $nuevoStock = $stockAnterior + $cantidad;

      $resultado = $this->actualizarDatos2([
        'tabla' => 'productos',
        'datos' => [
          'stock_producto' => $nuevoStock
        ],
        'WHERE' => [
          'id_producto' => $id
        ]
      ]);

      if ($resultado === false || $resultado <= 0) {
        $this->rollback();
        $objBitacora->registrarBitacora("producciones", "Registrar producción", "Error", [
          'id_produccion' => $idProduccion,
          'id_producto' => $id,
          'nombre_producto' => $infoGeneralProducto['nombre_producto'],
          'error' => 'Error actualizando stock del producto'
        ], true);
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'Ocurrió un error actualizando el stock de los productos',
          'icono' => 'error'
        ];
      }

      $productosProcesados[] = [
        'id_producto' => $id,
        'nombre_producto' => $infoGeneralProducto['nombre_producto'],
        'cantidad_producida' => $cantidad,
        'stock_anterior' => $stockAnterior,
        'stock_nuevo' => $nuevoStock
      ];

      // Descontar materias primas
      $materiasPrimaProducto = $infoGeneralProducto['detallesExtra']['materias_primas'] ?? [];

      foreach ($materiasPrimaProducto as $materiaPrima) {
        $cantidadTotalRestar = $cantidad * $materiaPrima['cantidad_materia_prima'];

        $objMateriaPrima = new materiasPrimasModelo();
        $materiaPrimaBD = $objMateriaPrima->seleccionarMateriasPrimas([
          'id_materia_prima' => $materiaPrima['id_materia_prima']
        ]);

        if (isset($materiaPrimaBD['tipo']) && $materiaPrimaBD['tipo'] == 'simple') {
          $this->rollback();
          $objBitacora->registrarBitacora("producciones", "Registrar producción", "Error", [
            'id_produccion' => $idProduccion,
            'id_materia_prima' => $materiaPrima['id_materia_prima'],
            'error' => 'Materia prima no encontrada'
          ], true);
          return $materiaPrimaBD;
        }

        $stockMPAnterior = $materiaPrimaBD['stock_materia_prima'];
        $nuevoStockMP = $stockMPAnterior - $cantidadTotalRestar;

        if ($nuevoStockMP < 0) {
          $this->rollback();
          $errorMsg = "No hay suficiente stock de la materia prima: " . $materiaPrimaBD['nombre_materia_prima'] . 
                       " (Stock: " . $stockMPAnterior . ", Necesario: " . $cantidadTotalRestar . ")";
          $objBitacora->registrarBitacora("producciones", "Registrar producción", "Error", [
            'id_produccion' => $idProduccion,
            'id_materia_prima' => $materiaPrima['id_materia_prima'],
            'nombre_materia_prima' => $materiaPrimaBD['nombre_materia_prima'],
            'stock_anterior' => $stockMPAnterior,
            'necesario' => $cantidadTotalRestar,
            'error' => $errorMsg
          ], true);
          return [
            'tipo' => 'simple',
            'titulo' => 'Error - Stock insuficiente',
            'texto' => $errorMsg,
            'icono' => 'error'
          ];
        }

        $resultado = $this->actualizarDatos2([
          'tabla' => 'materias_primas',
          'datos' => [
            'stock_materia_prima' => $nuevoStockMP
          ],
          'WHERE' => [
            'id_materia_prima' => $materiaPrima['id_materia_prima']
          ]
        ]);

        if ($resultado === false || $resultado <= 0) {
          $this->rollback();
          $objBitacora->registrarBitacora("producciones", "Registrar producción", "Error", [
            'id_produccion' => $idProduccion,
            'id_materia_prima' => $materiaPrima['id_materia_prima'],
            'nombre_materia_prima' => $materiaPrimaBD['nombre_materia_prima'],
            'error' => 'Error actualizando stock de materia prima'
          ], true);
          return [
            'tipo' => 'simple',
            'titulo' => 'Error',
            'texto' => 'Ocurrió un error actualizando el stock de las materias primas',
            'icono' => 'error'
          ];
        }

        $materiasPrimasDescontadas[] = [
          'id_materia_prima' => $materiaPrima['id_materia_prima'],
          'nombre_materia_prima' => $materiaPrimaBD['nombre_materia_prima'],
          'cantidad_descontada' => $cantidadTotalRestar,
          'stock_anterior' => $stockMPAnterior,
          'stock_nuevo' => $nuevoStockMP
        ];
      }
    }

    $this->commit();

    $detallesBitacora = [
      'id_produccion' => $idProduccion,
      'fecha' => date('Y-m-d H:i:s'),
      'productos_producidos' => $productosProcesados,
      'materias_primas_descontadas' => $materiasPrimasDescontadas,
    ];
    $objBitacora->registrarBitacora("producciones", "Registrar producción", "Éxito", $detallesBitacora, true);

    $objetoNot = new mensajesWSModelo();
    $objetoNot->enviarMensajesWS(
      [
        "receptor" => [
          'tipo' => 'todosSinExcepcion',
        ],
        'cuerpo' => [
          [
            'accion' => "borrarDataModuloSS",
            'modulo' => 'producciones'
          ],
          [
            'accion' => "actDT",
            'modulo' => 'producciones'
          ],
          [
            'accion' => 'alertar',
            'alerta' => [
              'tipo' => 'simple',
              'titulo' => 'Producción registrada',
              'texto' => 'Se ha registrado una nueva producción. El stock ha sido actualizado.',
              'icono' => 'info',
              'notifier' => true,
            ]
          ]
        ],
        'noCommit' => true
      ]
    );

    return [
      'tipo' => 'limpiarYcerrar',
      'icono' => 'success',
      'titulo' => 'Éxito en el registro',
      'texto' => 'Se registró correctamente la producción #' . $idProduccion,
    ];
  }

  private function actualizarProduccionesP() {
    $objBitacora = new bitacoraModelo();
    
    //Data actual
    $dataActual = $this->seleccionarProducciones([
      'id_produccion' => $this->idProduccion
    ]);

    if (isset($dataActual['tipo']) && $dataActual['tipo'] == 'simple') {
      $this->rollback();
      return $dataActual;
    }

    $dataActual['detalles'] = $this->indexarArrays([
      'indice' => 'id_producto',
      'camposSumar' => 'cantidad_producida',
      'array' => $dataActual['detalles'] ?? [],
    ]);

    // Nueva data
    $this->productos = $this->indexarArrays([
      'indice' => 'id_producto',
      'camposSumar' => 'cantidad_producida',
      'array' => $this->productos,
    ]);

    $resultado = $this->eliminarDatos2([
      'tabla' => 'productos_producciones',
      'WHERE' => [
        'id_produccion' => $this->idProduccion
      ],
      'fisico' => true
    ]);

    // Arrays para guardar los cambios
    $productosRevertidos = [];
    $productosAgregados = [];
    $materiasPrimasDevueltas = [];
    $materiasPrimasDescontadas = [];

    //Revertimos lo viejo (deshacemos la producción anterior)
    foreach ($dataActual['detalles'] as $idProducto => $cantidadProducto) {

      $objProducto = new productosModelo();
      $infoGeneralProducto = $objProducto->seleccionarProductos([
        'id_producto' => $idProducto
      ]);

      if (isset($infoGeneralProducto['tipo']) && $infoGeneralProducto['tipo'] == 'simple') {
        $this->rollback();
        $objBitacora->registrarBitacora("producciones", "Actualizar producción", "Error", [
          'id_produccion' => $this->idProduccion,
          'id_producto' => $idProducto,
          'error' => 'Producto no encontrado'
        ], true);
        return $infoGeneralProducto;
      }

      $stockAnterior = $infoGeneralProducto['stock_producto'];
      $nuevoStock = $stockAnterior - $cantidadProducto;

      if ($nuevoStock < 0) {
        $this->rollback();
        $errorMsg = 'No se puede descontar ' . $cantidadProducto . ' unidades de ' . $infoGeneralProducto['nombre_producto'] . 
                    ' porque el stock actual es ' . $stockAnterior;
        $objBitacora->registrarBitacora("producciones", "Actualizar producción", "Error", [
          'id_produccion' => $this->idProduccion,
          'id_producto' => $idProducto,
          'nombre_producto' => $infoGeneralProducto['nombre_producto'],
          'stock_anterior' => $stockAnterior,
          'intento_descontar' => $cantidadProducto,
          'error' => $errorMsg
        ], true);
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => $errorMsg,
          'icono' => 'error'
        ];
      }

      $resultado = $this->actualizarDatos2([
        'tabla' => 'productos',
        'datos' => [
          'stock_producto' => $nuevoStock
        ],
        'WHERE' => [
          'id_producto' => $idProducto
        ]
      ]);
      if ($resultado === false || $resultado <= 0) {
        $this->rollback();
        $objBitacora->registrarBitacora("producciones", "Actualizar producción", "Error", [
          'id_produccion' => $this->idProduccion,
          'id_producto' => $idProducto,
          'error' => 'Error actualizando stock del producto'
        ], true);
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'Ocurrió un error actualizando el stock de los productos',
          'icono' => 'error'
        ];
      }

      $productosRevertidos[] = [
        'id_producto' => $idProducto,
        'nombre_producto' => $infoGeneralProducto['nombre_producto'],
        'cantidad_quitada' => $cantidadProducto,
        'stock_anterior' => $stockAnterior,
        'stock_nuevo' => $nuevoStock
      ];

      //Devolver materias primas
      $materiasPrimaProducto = $infoGeneralProducto['detallesExtra']['materias_primas'] ?? [];

      foreach ($materiasPrimaProducto as $materiaPrima) {
        $cantidadTotalSumar = $cantidadProducto * $materiaPrima['cantidad_materia_prima'];

        $objMateriaPrima = new materiasPrimasModelo();
        $materiaPrimaBD = $objMateriaPrima->seleccionarMateriasPrimas([
          'id_materia_prima' => $materiaPrima['id_materia_prima']
        ]);

        if (isset($materiaPrimaBD['tipo']) && $materiaPrimaBD['tipo'] == 'simple') {
          $this->rollback();
          $objBitacora->registrarBitacora("producciones", "Actualizar producción", "Error", [
            'id_produccion' => $this->idProduccion,
            'id_materia_prima' => $materiaPrima['id_materia_prima'],
            'error' => 'Materia prima no encontrada'
          ], true);
          return $materiaPrimaBD;
        }

        $stockAnteriorMP = $materiaPrimaBD['stock_materia_prima'];
        $nuevoStockMP = $stockAnteriorMP + $cantidadTotalSumar;

        $resultado = $this->actualizarDatos2([
          'tabla' => 'materias_primas',
          'datos' => [
            'stock_materia_prima' => $nuevoStockMP
          ],
          'WHERE' => [
            'id_materia_prima' => $materiaPrima['id_materia_prima']
          ]
        ]);
        if ($resultado === false || $resultado <= 0) {
          $this->rollback();
          $objBitacora->registrarBitacora("producciones", "Actualizar producción", "Error", [
            'id_produccion' => $this->idProduccion,
            'id_materia_prima' => $materiaPrima['id_materia_prima'],
            'error' => 'Error actualizando stock de materia prima'
          ], true);
          return [
            'tipo' => 'simple',
            'titulo' => 'Error',
            'texto' => 'Ocurrió un error actualizando el stock de las materias primas',
            'icono' => 'error'
          ];
        }

        $materiasPrimasDevueltas[] = [
          'id_materia_prima' => $materiaPrima['id_materia_prima'],
          'nombre_materia_prima' => $materiaPrimaBD['nombre_materia_prima'],
          'cantidad_devuelta' => $cantidadTotalSumar,
          'stock_anterior' => $stockAnteriorMP,
          'stock_nuevo' => $nuevoStockMP
        ];
      }
    }

    //Insertamos lo nuevo
    foreach ($this->productos as $idProducto => $cantidad) {
      $idDetalle = $this->guardarDatos2([
        'tabla' => 'productos_producciones',
        'datos' => [
          'id_produccion' => $this->idProduccion,
          'id_producto' => $idProducto,
          'cantidad_producida' => $cantidad,
        ]
      ]);

      if ($idDetalle == 0 || $idDetalle === false) {
        $this->rollback();
        $objBitacora->registrarBitacora("producciones", "Actualizar producción", "Error", [
          'id_produccion' => $this->idProduccion,
          'error' => 'Error registrando productos'
        ], true);
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'Ocurrió un error registrando los productos',
          'icono' => 'error'
        ];
      }

      $objProducto = new productosModelo();
      $infoGeneralProducto = $objProducto->seleccionarProductos([
        'id_producto' => $idProducto
      ]);

      if (isset($infoGeneralProducto['tipo']) && $infoGeneralProducto['tipo'] == 'simple') {
        $this->rollback();
        $objBitacora->registrarBitacora("producciones", "Actualizar producción", "Error", [
          'id_produccion' => $this->idProduccion,
          'id_producto' => $idProducto,
          'error' => 'Producto no encontrado'
        ], true);
        return $infoGeneralProducto;
      }

      $stockAnterior = $infoGeneralProducto['stock_producto'];
      $nuevoStock = $stockAnterior + $cantidad;

      $resultado = $this->actualizarDatos2([
        'tabla' => 'productos',
        'datos' => [
          'stock_producto' => $nuevoStock
        ],
        'WHERE' => [
          'id_producto' => $idProducto
        ]
      ]);

      if ($resultado === false || $resultado <= 0) {
        $this->rollback();
        $objBitacora->registrarBitacora("producciones", "Actualizar producción", "Error", [
          'id_produccion' => $this->idProduccion,
          'id_producto' => $idProducto,
          'error' => 'Error actualizando stock del producto'
        ], true);
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'Ocurrió un error actualizando el stock de los productos',
          'icono' => 'error'
        ];
      }

      $productosAgregados[] = [
        'id_producto' => $idProducto,
        'nombre_producto' => $infoGeneralProducto['nombre_producto'],
        'cantidad_agregada' => $cantidad,
        'stock_anterior' => $stockAnterior,
        'stock_nuevo' => $nuevoStock
      ];

      // Descontar materias primas
      $materiasPrimaProducto = $infoGeneralProducto['detallesExtra']['materias_primas'] ?? [];

      foreach ($materiasPrimaProducto as $materiaPrima) {
        $cantidadTotalRestar = $cantidad * $materiaPrima['cantidad_materia_prima'];

        $objMateriaPrima = new materiasPrimasModelo();
        $materiaPrimaBD = $objMateriaPrima->seleccionarMateriasPrimas([
          'id_materia_prima' => $materiaPrima['id_materia_prima']
        ]);

        if (isset($materiaPrimaBD['tipo']) && $materiaPrimaBD['tipo'] == 'simple') {
          $this->rollback();
          $objBitacora->registrarBitacora("producciones", "Actualizar producción", "Error", [
            'id_produccion' => $this->idProduccion,
            'id_materia_prima' => $materiaPrima['id_materia_prima'],
            'error' => 'Materia prima no encontrada'
          ], true);
          return $materiaPrimaBD;
        }

        $stockAnteriorMP = $materiaPrimaBD['stock_materia_prima'];
        $nuevoStockMP = $stockAnteriorMP - $cantidadTotalRestar;

        if ($nuevoStockMP < 0) {
          $this->rollback();
          $errorMsg = "No hay suficiente stock de la materia prima: " . $materiaPrimaBD['nombre_materia_prima'] . 
                       " (Stock: " . $stockAnteriorMP . ", Necesario: " . $cantidadTotalRestar . ")";
          $objBitacora->registrarBitacora("producciones", "Actualizar producción", "Error", [
            'id_produccion' => $this->idProduccion,
            'id_materia_prima' => $materiaPrima['id_materia_prima'],
            'nombre_materia_prima' => $materiaPrimaBD['nombre_materia_prima'],
            'stock_anterior' => $stockAnteriorMP,
            'necesario' => $cantidadTotalRestar,
            'error' => $errorMsg
          ], true);
          return [
            'tipo' => 'simple',
            'titulo' => 'Error - Stock insuficiente',
            'texto' => $errorMsg,
            'icono' => 'error'
          ];
        }

        $resultado = $this->actualizarDatos2([
          'tabla' => 'materias_primas',
          'datos' => [
            'stock_materia_prima' => $nuevoStockMP
          ],
          'WHERE' => [
            'id_materia_prima' => $materiaPrima['id_materia_prima']
          ]
        ]);

        if ($resultado == false || $resultado <= 0) {
          $this->rollback();
          $objBitacora->registrarBitacora("producciones", "Actualizar producción", "Error", [
            'id_produccion' => $this->idProduccion,
            'id_materia_prima' => $materiaPrima['id_materia_prima'],
            'nombre_materia_prima' => $materiaPrimaBD['nombre_materia_prima'],
          ], true);
          return [
            'tipo' => 'simple',
            'titulo' => 'Error',
            'texto' => 'Ocurrió un error actualizando el stock de las materias primas',
            'icono' => 'error'
          ];
        }

        $materiasPrimasDescontadas[] = [
          'id_materia_prima' => $materiaPrima['id_materia_prima'],
          'nombre_materia_prima' => $materiaPrimaBD['nombre_materia_prima'],
          'cantidad_descontada' => $cantidadTotalRestar,
          'stock_anterior' => $stockAnteriorMP,
          'stock_nuevo' => $nuevoStockMP
        ];
      }
    }

    $this->commit();

    $detallesBitacora = [
      'id_produccion' => $this->idProduccion,
      'fecha' => date('Y-m-d H:i:s'),
      'productos_eliminados' => $productosRevertidos,
      'productos_agregados' => $productosAgregados,
      'materias_primas_devueltas' => $materiasPrimasDevueltas,
      'materias_primas_descontadas' => $materiasPrimasDescontadas,
    ];
    $objBitacora->registrarBitacora("producciones", "Actualizar producción", "Éxito", $detallesBitacora, true);

    $objetoNot = new mensajesWSModelo();
    $objetoNot->enviarMensajesWS(
      [
        "receptor" => [
          'tipo' => 'todosSinExcepcion',
        ],
        'cuerpo' => [
          [
            'accion' => "borrarDataModuloSS",
            'modulo' => 'producciones'
          ],
          [
            'accion' => "actDT",
            'modulo' => 'producciones'
          ],
          [
            'accion' => 'alertar',
            'alerta' => [
              'tipo' => 'simple',
              'titulo' => 'Producción actualizada',
              'texto' => 'El stock de algunos productos ha cambiado',
              'icono' => 'info',
              'notifier' => true,
            ]
          ]
        ],
        'noCommit' => true
      ]);

    return [
      'icono' => 'success',
      'titulo' => 'Éxito en la actualización',
      'texto' => 'Se actualizó correctamente la producción #' . $this->idProduccion,
      'tipo' => 'limpiarYcerrar'
    ];
  }
}