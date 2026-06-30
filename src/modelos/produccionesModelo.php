<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use src\modelos\productosModelo;
use src\modelos\materiasPrimasModelo;
use src\modelos\mensajesWSModelo;

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
      $this->rollback();
      $objBitacora->registrarBitacora([
        'modulo' => 'producciones',
        'accion' => 'Registrar',
        'resultado' => 'Fallido',
        'commit' => true
      ]);
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
        $objBitacora->registrarBitacora([
        'modulo' => 'producciones',
        'accion' => 'Registrar',
        'resultado' => 'Fallido',
        'commit' => true
      ]);
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
        return $infoGeneralProducto;
      }

      $resultado = $this->actualizarDatos2([
        'tabla' => 'productos',
        'datos' => [
          'stock_producto' => ($infoGeneralProducto['stock_producto'] + $cantidad)
        ],
        'WHERE' => [
          'id_producto' => $id
        ]
      ]);

      if ($resultado === false || $resultado <= 0) {
        $this->rollback();
        $objBitacora->registrarBitacora([
        'modulo' => 'producciones',
        'accion' => 'Registrar',
        'resultado' => 'Fallido',
        'commit' => true
      ]);
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'Ocurrió un error actualizando el stock de los productos',
          'icono' => 'error'
        ];
      }

      $materiasPrimaProducto = $infoGeneralProducto['detallesExtra']['materias_primas'] ?? [];

      foreach ($materiasPrimaProducto as $materiaPrima) {
        $cantidadTotalRestar = $cantidad * $materiaPrima['cantidad_materia_prima'];

        $objMateriaPrima = new materiasPrimasModelo();
        $materiaPrimaBD = $objMateriaPrima->seleccionarMateriasPrimas([
          'id_materia_prima' => $materiaPrima['id_materia_prima']
        ]);

        if (isset($materiaPrimaBD['tipo']) && $materiaPrimaBD['tipo'] == 'simple') {
          $this->rollback();
          return $materiaPrimaBD;
        }

        $stockMP = $materiaPrimaBD['stock_materia_prima'];
        $nuevoStock = ($stockMP - $cantidadTotalRestar);

        if ($nuevoStock < 0) {
          $this->rollback();
          $objBitacora->registrarBitacora([
            'modulo' => 'producciones',
            'accion' => 'Registrar',
            'resultado' => 'Fallido',
            'commit' => true
          ]);
          return [
            'tipo' => 'simple',
            'titulo' => 'Error',
            'texto' => 'No se puede descontar una cantidad mayor de materia prima a la disponible dentro del stock de inventario',
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
          $objBitacora->registrarBitacora([
            'modulo' => 'producciones',
            'accion' => 'Registrar',
            'resultado' => 'Fallido',
            'commit' => true
          ]);
          return [
            'tipo' => 'simple',
            'titulo' => 'Error',
            'texto' => 'Ocurrió un error actualizando el stock de las materias primas',
            'icono' => 'error'
          ];
        }
      }
    }
    $objBitacora->registrarBitacora([
      'modulo' => 'producciones',
      'accion' => 'Registrar',
      'resultado' => 'Éxito',
    ]);

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
              'titulo' => 'Produccion registrada',
              'texto' => 'El stock de algunos productos a cambiado',
              'icono' => 'info',
              'notifier' => true,
            ]
          ]
        ],
      ]
    );
    $this->commit();
    return [
      'tipo' => 'limpiarYcerrar',
      'icono' => 'success',
      'titulo' => 'Éxito en el registro',
      'texto' => 'Se registró correctamente la producción: ' . $idProduccion,
    ];
  }
  private function actualizarProduccionesP() {
    $objBitacora = new bitacoraModelo();

    $datosAntes = $this->seleccionarProducciones([
      'id_produccion' => $this->idProduccion
    ]);

    if (isset($datosAntes['tipo']) && $datosAntes['tipo'] == 'simple') {
      $this->rollback();
      return $datosAntes;
    }

    $detallesOriginales = $datosAntes['detalles'] ?? [];

    $detallesIndexados = $this->indexarArrays([
      'indice' => 'id_producto',
      'camposSumar' => 'cantidad_producida',
      'array' => $detallesOriginales,
    ]);

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

    foreach ($detallesIndexados as $idProducto => $cantidadProducto) {

      $objProducto = new productosModelo();
      $infoGeneralProducto = $objProducto->seleccionarProductos([
        'id_producto' => $idProducto
      ]);

      if (isset($infoGeneralProducto['tipo']) && $infoGeneralProducto['tipo'] == 'simple') {
        $this->rollback();
        return $infoGeneralProducto;
      }

      if ($cantidadProducto > $infoGeneralProducto['stock_producto']) {
        $this->rollback();
        $objBitacora->registrarBitacora([
          'modulo' => 'producciones',
          'accion' => 'Actualizar',
          'resultado' => 'Fallido',
          'commit' => true
        ]);
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'No se puede guardar la nueva cantidad de ' . $infoGeneralProducto['nombre_producto'] . ' porque esto dejaría un stock negativo del mismo',
          'icono' => 'error'
        ];
      }

      $resultado = $this->actualizarDatos2([
        'tabla' => 'productos',
        'datos' => [
          'stock_producto' => ($infoGeneralProducto['stock_producto'] - $cantidadProducto)
        ],
        'WHERE' => [
          'id_producto' => $idProducto
        ]
      ]);
      if ($resultado === false || $resultado <= 0) {
        $this->rollback();
        $objBitacora->registrarBitacora([
          'modulo' => 'producciones',
          'accion' => 'Actualizar',
          'resultado' => 'Fallido',
          'commit' => true
        ]);
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'Ocurrió un error actualizando el stock de los productos',
          'icono' => 'error'
        ];
      }

      $materiasPrimaProducto = $infoGeneralProducto['detallesExtra']['materias_primas'] ?? [];

      // Subimos el stock de las materias primas
      foreach ($materiasPrimaProducto as $materiaPrima) {
        $cantidadTotalSumar = $cantidadProducto * $materiaPrima['cantidad_materia_prima'];

        $objMateriaPrima = new materiasPrimasModelo();
        $materiaPrimaBD = $objMateriaPrima->seleccionarMateriasPrimas([
          'id_materia_prima' => $materiaPrima['id_materia_prima']
        ]);

        if (isset($materiaPrimaBD['tipo']) && $materiaPrimaBD['tipo'] == 'simple') {
          $this->rollback();
          return $materiaPrimaBD;
        }

        $stockMP = $materiaPrimaBD['stock_materia_prima'];

        $resultado = $this->actualizarDatos2([
          'tabla' => 'materias_primas',
          'datos' => [
            'stock_materia_prima' => ($stockMP + $cantidadTotalSumar)
          ],
          'WHERE' => [
            'id_materia_prima' => $materiaPrima['id_materia_prima']
          ]
        ]);
        if ($resultado === false || $resultado <= 0) {
          $this->rollback();
          $objBitacora->registrarBitacora([
            'modulo' => 'producciones',
            'accion' => 'Actualizar',
            'resultado' => 'Fallido',
            'commit' => true
          ]);
          return [
            'tipo' => 'simple',
            'titulo' => 'Error',
            'texto' => 'Ocurrió un error actualizando el stock de las materias primas',
            'icono' => 'error'
          ];
        }
      }
    }

    // Insertamos lo nuevo
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
        $objBitacora->registrarBitacora([
          'modulo' => 'producciones',
          'accion' => 'Actualizar',
          'resultado' => 'Fallido',
          'commit' => true
        ]);
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
        return $infoGeneralProducto;
      }

      $resultado = $this->actualizarDatos2([
        'tabla' => 'productos',
        'datos' => [
          'stock_producto' => ($infoGeneralProducto['stock_producto'] + $cantidad)
        ],
        'WHERE' => [
          'id_producto' => $idProducto
        ]
      ]);

      if ($resultado === false || $resultado <= 0) {
        $this->rollback();
        $objBitacora->registrarBitacora([
          'modulo' => 'producciones',
          'accion' => 'Actualizar',
          'resultado' => 'Fallido',
          'commit' => true
        ]);
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'Ocurrió un error actualizando el stock de los productos',
          'icono' => 'error'
        ];
      }

      $materiasPrimaProducto = $infoGeneralProducto['detallesExtra']['materias_primas'] ?? [];

      foreach ($materiasPrimaProducto as $materiaPrima) {
        $cantidadTotalRestar = $cantidad * $materiaPrima['cantidad_materia_prima'];

        $objMateriaPrima = new materiasPrimasModelo();
        $materiaPrimaBD = $objMateriaPrima->seleccionarMateriasPrimas([
          'id_materia_prima' => $materiaPrima['id_materia_prima']
        ]);

        if (isset($materiaPrimaBD['tipo']) && $materiaPrimaBD['tipo'] == 'simple') {
          $this->rollback();
          return $materiaPrimaBD;
        }

        $stockMP = $materiaPrimaBD['stock_materia_prima'];
        $nuevoStock = ($stockMP - $cantidadTotalRestar);

        if ($nuevoStock < 0) {
          $this->rollback();
          $objBitacora->registrarBitacora([
            'modulo' => 'producciones',
            'accion' => 'Actualizar',
            'resultado' => 'Fallido',
            'commit' => true
          ]);
          return [
            'tipo' => 'simple',
            'titulo' => 'Error',
            'texto' => 'No se puede descontar una cantidad mayor de materia prima a la disponible dentro del stock de inventario',
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

        if ($resultado == false || $resultado <= 0) {
          $this->rollback();
          $objBitacora->registrarBitacora([
            'modulo' => 'producciones',
            'accion' => 'Actualizar',
            'resultado' => 'Fallido',
            'commit' => true
          ]);
          return [
            'tipo' => 'simple',
            'titulo' => 'Error',
            'texto' => 'Ocurrió un error actualizando el stock de las materias primas',
            'icono' => 'error'
          ];
        }
      }
    }

    $this->commit();

    $datosDespues = $this->seleccionarProducciones([
      'id_produccion' => $this->idProduccion
    ]);

    $objBitacora->registrarBitacora([
      'modulo' => 'producciones',
      'accion' => 'Actualizar',
      'resultado' => 'Fallido',
      'viejo' => $datosAntes,
      'nuevo' => $datosDespues
    ]);

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
              'titulo' => 'Produccion actualizado',
              'texto' => 'El stock de algunos productos a cambiado',
              'icono' => 'info',
              'notifier' => true,
            ]
          ]
        ],
      ]
    );

    return [
      'icono' => 'success',
      'titulo' => 'Éxito en la actualización',
      'texto' => 'Se actualizó correctamente la producción #' . $this->idProduccion,
      'tipo' => 'limpiarYcerrar'
    ];
  }
}
