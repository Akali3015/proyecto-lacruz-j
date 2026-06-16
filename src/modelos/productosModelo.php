<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use src\modelos\categoriasProductosModelo;
use PDO;

class productosModelo extends conexion {
  private string $idProducto = '';
  private string $idPresentacinProd = '';
  private string $idUnidadMedida = '';
  private string $idCategoria = '';
  private string $nombreProducto = '';
  private float $precioProducto = 0;
  private int $stockProducto = 0;
  private int $stockMinimoProducto = 0;
  private array $presentaciones = [];
  private array $materiasPrimas = [];
  private array $fotoPresentacion = [];

  public function validarProductos(array $instruccionesVal) {
    [
      'infoVal' => &$infoVal,
      'camposVal' => &$camposVal,
    ] = $instruccionesVal;
    $funcionAsignadora = function ($nombreCampo, &$valor) {
      $claveVal = [
        'cantidad_materia_prima' => [
          "campo_valor" => &$valor,
          "comaPunto" => true,
          "formulario_nombre" => "cantidad de la materia prima",
          "requerido" => true,
          "minimo" => minRegexCantidadItem,
          "maximo" => maxRegexCantidadItem,
          "expresion_re" => regexCantidadItem,
        ],
        'foto_presentacion' => [
          "imagen" => &$valor,
          "requerido" => true,
        ],
        'id_producto' => [
          "campo_nombre" => "id_producto",
          "campo_valor" => &$valor,
          "formulario_nombre" => "id del producto",
          "requerido" => true,
          "minimo" => minRegexIdSeguro,
          "maximo" => maxRegexIdSeguro,
          "expresion_re" => regexIdSeguro,
          "tabla" => "productos",
          "debeSerUnico" => true,
          "debeExistir" => true,
        ],
        'id_unidad_medida' => [
          "campo_nombre" => "id_unidad_medida",
          "campo_valor" => &$valor,
          "formulario_nombre" => "unidades de medida",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "unidades_medidas",
          "debeExistir" => true,
        ],
        'id_categoria_producto' => [
          "campo_nombre" => "id_categoria_producto",
          "campo_valor" => &$valor,
          "formulario_nombre" => "categoria",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "categorias_productos",
          "debeExistir" => true,
        ],
        'id_presentacion' => [
          "campo_nombre" => "id_presentacion",
          "campo_valor" => &$valor,
          "formulario_nombre" => "id de la presentación",
          "requerido" => true,
          "minimo" => minRegexIdSeguro,
          "maximo" => maxRegexIdSeguro,
          "expresion_re" => regexIdSeguro,
          "tabla" => "presentaciones",
          "debeExistir" => true,
        ],
        'id_presentacion_producto' => [
          "campo_nombre" => "id_presentacion_producto",
          "campo_valor" => &$valor,
          "formulario_nombre" => "id de la presentación del producto",
          "requerido" => true,
          "minimo" => minRegexIdSeguro,
          "maximo" => maxRegexIdSeguro,
          "expresion_re" => regexIdSeguro,
          "tabla" => "presentaciones_productos",
          "debeExistir" => true,
        ],
        'id_materia_prima' => [
          "campo_nombre" => "id_materia_prima",
          "campo_valor" => &$valor,
          "formulario_nombre" => "id de la materia prima",
          "requerido" => true,
          "minimo" => minRegexIdSeguro,
          "maximo" => maxRegexIdSeguro,
          "expresion_re" => regexIdSeguro,
          "tabla" => "materias_primas",
          "debeExistir" => true,
        ],
        'mostrar_ecommerce' => [
          "campo_valor" => &$valor,
          "formulario_nombre" => "mostrar en el ecommerce",
          "requerido" => true,
          "minimo" => minRegexValorBoleano,
          "maximo" => maxRegexValorBoleano,
          "expresion_re" => regexValorBoleano,
        ],
        'nombre_producto' => [
          "campo_nombre" => "nombre_producto",
          "campo_valor" => &$valor,
          "formulario_nombre" => "nombre del producto",
          "requerido" => true,
          "minimo" => minRegexNombreObj,
          "maximo" => maxRegexNombreObj,
          "expresion_re" => regexNombreObj,
          "tabla" => "productos",
          "debeSerUnico" => true,
        ],
        'precio_producto' => [
          "campo_nombre" => "precio_producto_divisa",
          "campo_valor" => &$valor,
          'comaPunto' => true,
          "formulario_nombre" => "precio en divisas",
          "requerido" => true,
          "minimo" => minRegexPrecio,
          "maximo" => maxRegexPrecio,
          "expresion_re" => regexPrecio,
        ],
        'stock_producto' => [
          "campo_nombre" => "stock_producto",
          "campo_valor" => &$valor,
          "formulario_nombre" => "stock",
          "requerido" => true,
          "minimo" => minRegexCantidadItem,
          "maximo" => maxRegexCantidadItem,
          "expresion_re" => regexCantidadItem,
        ],
        'stock_minimo_producto' => [
          "campo_nombre" => "stock_producto",
          "campo_valor" => &$valor,
          "formulario_nombre" => "stock",
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
      switch ($campo) {
        case 'materias_primas':
          $objCategorias = new categoriasProductosModelo();
          $categoriaBD = $objCategorias->seleccionarCategorias(['id_categoria_producto' => $infoVal['id_categoria_producto']]);
          if ($categoriaBD['necesitan_materias_primas'] == 1) {
            if (($infoVal['materias_primas'] ?? []) == [] && $infoVal['id_categoria_producto'] == 1) {
              return [
                'tipo' => 'simple',
                'titulo' => 'Sin Materias Primas',
                'texto' => 'No has enviado las materias primas del producto',
                'icono' => 'warning',
              ];
            }
            foreach ($infoVal['materias_primas'] as &$materiaInd) {
              $campos[] = $funcionAsignadora('id_materia_prima', $materiaInd['id_materia_prima']);
              $campos[] = $funcionAsignadora('cantidad_materia_prima', $materiaInd['cantidad_materia_prima']);
            }
            unset($materiaInd);
          }
          break;
        case 'presentaciones':
          if (($infoVal['presentaciones'] ?? []) == []) {
            return [
              'tipo' => 'simple',
              'titulo' => 'Sin presentaciones',
              'texto' => 'No has enviado las presentaciones del producto',
              'icono' => 'warning',
            ];
          }
          foreach ($infoVal['presentaciones'] as &$pre) {
            $campos[] = $funcionAsignadora('id_presentacion', $pre['id_presentacion']);
            if (isset($pre['mostrar_ecommerce'])) {
              $campos[] = $funcionAsignadora('mostrar_ecommerce', $pre['mostrar_ecommerce']);
            }
          }
          unset($pre);
          break;
        default:
          $campos[] = $funcionAsignadora($campo, $infoVal[$campo]);
          break;
      }
    }
    return $this->limpiar_Verificar($campos);
  }
  public function seleccionarProductos(array $info) {
    $campos = [];

    if (($info['id_producto'] ?? '') != "") $campos[] = 'id_producto';
    if (($info['id_presentacion_producto'] ?? '') != "") $campos[] = 'id_presentacion_producto';

    if (!empty($campos)) {
      $respuesta = $this->validarProductos([
        'infoVal' => &$info,
        'camposVal' => $campos,
      ]);
      if ($respuesta !== false) return $respuesta;
    }

    $this->idProducto = $info['id_producto'] ?? '';
    $this->idPresentacinProd = $info['id_presentacion_producto'] ?? '';
    return $this->seleccionarProductosP($info);
  }
  public function registrarProductos(array $info) {
    $respuesta = $this->validarProductos([
      'infoVal' => &$info,
      'camposVal' => [
        'id_unidad_medida',
        'id_categoria_producto',
        'nombre_producto',
        'precio_producto',
        'stock_producto',
        'stock_minimo_producto',
        'presentaciones',
        'materias_primas',
      ]
    ]);
    if ($respuesta !== false) return $respuesta;

    $this->idUnidadMedida = $info['id_unidad_medida'];
    $this->idCategoria = $info['id_categoria_producto'];
    $this->nombreProducto = $info['nombre_producto'];
    $this->precioProducto = $info['precio_producto'];
    $this->stockProducto = $info['stock_producto'];
    $this->stockMinimoProducto = $info['stock_minimo_producto'];
    $this->presentaciones = $info['presentaciones'];
    $this->materiasPrimas = $info['materias_primas'] ?? [];

    foreach ($this->presentaciones as &$pre) {
      if (isset($datos['foto_presentacion_' . $pre['id_presentacion']])) {
        $pre['foto_presentacion'] = $info['foto_presentacion_' . $pre['id_presentacion']];
      }
    }
    unset($pre);
    return $this->registrarProductosP();
  }
  public function actualizarProductos(array $info) {
    $respuesta = $this->validarProductos([
      'infoVal' => &$info,
      'camposVal' => [
        'id_producto',
        'id_unidad_medida',
        'id_categoria_producto',
        'nombre_producto',
        'precio_producto',
        'stock_producto',
        'stock_minimo_producto',
        'presentaciones',
        'materias_primas',
      ]
    ]);
    if ($respuesta !== false) return $respuesta;
    $this->idProducto = $info['id_producto'];
    $this->idUnidadMedida = $info['id_unidad_medida'];
    $this->idCategoria = $info['id_categoria_producto'];
    $this->nombreProducto = $info['nombre_producto'];
    $this->precioProducto = $info['precio_producto'];
    $this->stockProducto = $info['stock_producto'];
    $this->stockMinimoProducto = $info['stock_minimo_producto'];
    $this->presentaciones = $info['presentaciones'];
    $this->materiasPrimas = $info['materias_primas'] ?? [];

    foreach ($this->presentaciones as &$pre) {
      if (isset($info['foto_presentacion_' . $pre['id_presentacion']])) {
        $pre['foto_presentacion'] = $info['foto_presentacion_' . $pre['id_presentacion']];
      }
      if (!isset($pre['mostrar_ecommerce'])) $pre['mostrar_ecommerce'] = '0';
    }
    unset($pre);
    return $this->actualizarProductosP();
  }
  public function eliminarProductos(array $info) {
    $respuesta = $this->validarProductos([
      'infoVal' => &$info,
      'camposVal' => [
        'id_producto',
      ]
    ]);
    if ($respuesta !== false) return $respuesta;

    $this->idProducto = $info['id_producto'];
    return $this->eliminarProductosP();
  }
  public function actualizarFotPreProd(array $info) {
    $respuesta = $this->validarProductos([
      'infoVal' => &$info,
      'camposVal' => [
        'id_presentacion_producto','foto_presentacion'
      ]
    ]);
    if ($respuesta !== false) return $respuesta;
    $this->idPresentacinProd = $info['id_presentacion_producto'];
    $this->fotoPresentacion = $info['foto_presentacion'];
    return $this->actualizarFotPreProdP();
  }
  public function eliminarFotPreProd(array $info) {
    $respuesta = $this->validarProductos([
      'infoVal' => &$info,
      'camposVal' => [
        'id_presentacion_producto',
      ]
    ]);
    if ($respuesta !== false) return $respuesta;
    $this->idPresentacinProd = $info['id_presentacion_producto'];
    return $this->eliminarFotPreProdP();
  }

  private function seleccionarProductosP(array $info) {
    if ($this->idProducto == null || $this->idProducto == "") {
      switch ($info['tipoConsulta'] ?? '') {
        case 'ecommerce':
          return $this->seleccionarDatos2([
            'campos' => '
              *,
              ROUND((
                (
                  (
                    SELECT mo.valor_moneda FROM monedas as mo WHERE id_moneda=1
                  )*p.precio_producto
                )*pre.cantidad_pmp
              ),2) as precio_bs,
              ROUND((
                p.precio_producto*pre.cantidad_pmp
              ),2) as precio_dolar
            ',
            'tabla' => 'productos as p',
            'datosJoins' => [
              "unidades_medidas as um" => "p.id_unidad_medida = um.id_unidad_medida",
              "categorias_productos as cp" => "p.id_categoria_producto = cp.id_categoria_producto",
              "presentaciones_productos as pp" => "p.id_producto = pp.id_producto",
              "presentaciones as pre" => "pp.id_presentacion = pre.id_presentacion",
            ],
            'WHERE' => [
              'pp.mostrar_ecommerce' => 1,
              'pp.status'=>'!= 0'
            ]
          ])->fetchAll();
        case 'presentacionExp':
          return $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'presentaciones_productos as prpr',
            'datosJoins' => [
              "presentaciones as pr" => "prpr.id_presentacion = pr.id_presentacion",
            ],
            'WHERE' => [
              'prpr.id_presentacion_producto' => $this->idPresentacinProd
            ]
          ])->fetch();
        case 'productosFactura':
          return $this->seleccionarDatos2([
            'tabla' => 'ordenes_entregas_presupuestos as f',
            'campos' => '
              pre.cantidad_pmp,pf.cantidad_producto,pp.foto_presentacion,
              pr.id_categoria_producto,pre.id_presentacion, pp.id_presentacion_producto,
              pr.id_producto,pf.id_producto_factura,
              pr.id_unidad_medida,pre.nombre_presentacion, pr.nombre_producto,
              ROUND(( 
                SELECT prpr.precio_producto 
                FROM precios_productos as prpr 
                WHERE prpr.id_producto = pr.id_producto AND prpr.fecha_cambio <= f.fecha_orden_entrega_presupuesto 
                ORDER BY prpr.id_precio_producto DESC 
                LIMIT 1 
              ),2) AS precio_producto_factura, 
              ROUND((pre.cantidad_pmp * pf.cantidad_producto),2) as cantidad_bruta, 
              ROUND(( 
                SELECT (prpr.precio_producto*pre.cantidad_pmp)  
                FROM precios_productos as prpr 
                WHERE prpr.id_producto = pr.id_producto AND prpr.fecha_cambio <= f.fecha_orden_entrega_presupuesto 
                ORDER BY prpr.id_precio_producto DESC
                LIMIT 1 
              ),2) as precio_presentacion_factura
            ',
            'datosJoins' => [
              'productos_ordenes_entregas_presupuestos as pf' => 'f.id_orden_entrega_presupuesto = pf.id_orden_entrega_presupuesto',
              'presentaciones_productos as pp' => 'pf.id_presentacion_producto = pp.id_presentacion_producto',
              'productos as pr' => 'pp.id_producto = pr.id_producto',
              'presentaciones as pre' => 'pp.id_presentacion = pre.id_presentacion',
            ],
            'WHERE' => [
              'pf.id_orden_entrega_presupuesto' => $info['id_orden_entrega_presupuesto'],
            ],
          ])->fetchAll();
        case 'indexadosPorId':
          return $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'productos as p',
            'datosJoins' => [
              "unidades_medidas as um" => "p.id_unidad_medida = um.id_unidad_medida",
              "categorias_productos as cp" => "p.id_categoria_producto = cp.id_categoria_producto",
            ]
          ])->fetchAll(PDO::FETCH_UNIQUE);
        default:
          return $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'productos as p',
            'datosJoins' => [
              "unidades_medidas as um" => "p.id_unidad_medida = um.id_unidad_medida",
              "categorias_productos as cp" => "p.id_categoria_producto = cp.id_categoria_producto",
            ]
          ])->fetchAll();
      }
    } else {
      $producto = [];
      switch ($info['tipoConsulta'] ?? '') {
        case 'presentaciones':
          $producto = $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'productos as p',
            'datosJoins' => [
              "unidades_medidas as um" => "p.id_unidad_medida = um.id_unidad_medida",
              "presentaciones_productos as pp" => "p.id_producto = pp.id_producto",
              "presentaciones as pre" => "pp.id_presentacion = pre.id_presentacion",
            ],
            'WHERE' => [
              'p.id_producto' => $this->idProducto,
              'pp.status'=>'!= '. 0
            ]
          ])->fetchAll();
          break;
        default:
          //Dato generales  
          $resultado = $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'productos as pr',
            'WHERE' => [
              "id_producto" => $this->idProducto,
            ],
            'datosJoins' => [
              'categorias_productos as cp' => 'pr.id_categoria_producto = cp.id_categoria_producto',
              'unidades_medidas as um' => 'pr.id_unidad_medida = um.id_unidad_medida'
            ]
          ]);
          if ($resultado->rowCount() <= 0) {
            return [
              "tipo" => "simple",
              "titulo" => "Producto no encontrado",
              "texto" => "El producto no se encuentra.",
              "icono" => "error"
            ];
          }
          $producto = $resultado->fetch();

          //Presentaciones
          $resultado = $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'presentaciones_productos',
            'WHERE' => [
              "id_producto" => $this->idProducto,
            ]
          ]);
          $presentaciones = $resultado->fetchAll();
          $resultado = $this->seleccionarDatos2([
            'campos' => 'id_materia_prima, cantidad_materia_prima',
            'tabla' => 'materias_primas_productos',
            'WHERE' => [
              "id_producto" => $this->idProducto
            ]
          ]);
          $materiasPrimas = $resultado->fetchAll();
          $producto['detallesExtra'] = [
            'presentaciones' => $presentaciones,
            'materias_primas' => $materiasPrimas
          ];
      }
      return $producto;
    }
  }
  private function registrarProductosP() {
    $funcionError = function ($objBi, $arrayImg = NULL) {
      $this->rollback();
      $objBi->registrarBitacora("productos", "registrar", "fallido", true);
      if ($arrayImg) {
        foreach ($arrayImg as $nombreImagen) {
          $this->Imagenes_Eli2('presentaciones_productos', $nombreImagen);
        }
      }
    };
    $idProducto = $this->generarCodSeg([
      'tablaBD' => 'productos',
      'prefijo' => 'PROD',
      'campoID' => 'id_producto'
    ]);
    // Registro de la imagen
    $objBit = new bitacoraModelo();
    $resultado = $this->guardarDatos2([
      'tabla' => 'productos',
      'datos' => [
        'id_producto' => $idProducto,
        "id_unidad_medida" => $this->idUnidadMedida,
        "id_categoria_producto" => $this->idCategoria,
        "nombre_producto" => $this->nombreProducto,
        "precio_producto" => $this->precioProducto,
        "stock_producto" => $this->stockProducto,
        "stock_minimo_producto" => $this->stockMinimoProducto,
      ],
    ]);

    if ($resultado == false || $resultado <= 0) {
      $funcionError($objBit);
      return [
        'tipo' => 'simple',
        'titulo' => 'Error',
        'texto' => 'No registrado en BD',
        'icono' => 'error'
      ];
    }

    //Presentaciones
    $arrayImagenes = [];
    foreach ($this->presentaciones as $pre) {
      //Imagen
      if (isset($pre['foto_presentacion']) && $pre['foto_presentacion'] != '') {
        $arrayImagenes[] = $nombreImagen = $this->Imagenes_Reg(
          'presentaciones_productos',
          $pre['foto_presentacion'],
          'presentaciones_productos'
        );
      }

      $idPresentacion = $this->generarCodSeg([
        'tablaBD' => 'presentaciones_productos',
        'prefijo' => 'PRPR',
        'campoID' => 'id_presentacion_producto'
      ]);

      $idPre = $this->guardarDatos2([
        'tabla' => 'presentaciones_productos',
        'datos' => [
          "id_presentacion_producto" => $idPresentacion,
          "id_producto" => $idProducto,
          "id_presentacion" => $pre['id_presentacion'],
          "mostrar_ecommerce" => $pre['mostrar_ecommerce'] ?? 0,
          "foto_presentacion" => $nombreImagen ?? '',
        ]
      ]);
      if ($idPre == false || $idPre <= 0) {
        $funcionError($objBit, $arrayImagenes);
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'No se pudo registrar las presentaciones del producto',
          'icono' => 'error'
        ];
      }
    }

    //Materias primas
    $objCategorias = new categoriasProductosModelo();
    $categoriaBD = $objCategorias->seleccionarCategorias(['id_categoria_producto' => $this->idCategoria]);
    if ($categoriaBD['necesitan_materias_primas'] == 1) {

      $this->materiasPrimas = $this->indexarArrays([
        'indice' => 'id_materia_prima',
        'camposSumar' => 'cantidad_materia_prima',
        'array' => $this->materiasPrimas,
      ]);

      foreach ($this->materiasPrimas as $idMP => $cantidadMP) {
        $idMp = $this->guardarDatos2([
          'tabla' => 'materias_primas_productos',
          'datos' => [
            "id_producto" => $idProducto,
            "id_materia_prima" => $idMP,
            "cantidad_materia_prima" => $cantidadMP,
          ]
        ]);
        if ($idMp == false || $idMp <= 0) {
          $funcionError($objBit, $arrayImagenes);
          return [
            'tipo' => 'simple',
            'titulo' => 'Error',
            'texto' => 'No se pudieron registrar las materias primas del producto',
            'icono' => 'error'
          ];
        }
      }
    }
    $objBit->registrarBitacora("productos", "registrar", "éxito");
    $this->commit();
    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Producto registrado",
      "texto" => "Exitoso",
      "icono" => "success"
    ];
  }
  private function actualizarProductosP() {
    $PRD = 0;
    $MPR = 0;
    $PRE = 0;
    $MAT = 0;

    $imagenesPresentaciones = [];
    $funcionError = function ($codigoError) use ($imagenesPresentaciones) {
      $bitacoraModelo = new bitacoraModelo();
      $this->rollback();
      $bitacoraModelo->registrarBitacora("productos", "actualizar", "fallido", true);
      foreach ($imagenesPresentaciones as $i) {
        $this->Imagenes_Eli2('presentaciones_productos', $i);
      }
      return [
        'titulo' => "Actualización fallida",
        'icono' => "error",
        'texto' => "No se pudo actualizar el producto. Código de error: " . $codigoError,
        'tipo' => "simple",
      ];
    };

    //Datos generales
    $resultado = $this->actualizarDatos2([
      "tabla" => "productos",
      "datos" => [
        "id_unidad_medida" => $this->idUnidadMedida,
        "id_categoria_producto" => $this->idCategoria,
        "nombre_producto" => $this->nombreProducto,
        "precio_producto" => $this->precioProducto,
        "stock_producto" => $this->stockProducto,
        "stock_minimo_producto" => $this->stockMinimoProducto,
      ],
      "WHERE" => [
        "id_producto" => $this->idProducto,
      ]
    ]);
    if ($resultado != false && $resultado > 0) $PRD++;

    // Materias primas
    $this->materiasPrimas = $this->indexarArrays([
      'indice' => 'id_materia_prima',
      'camposSumar' => 'cantidad_materia_prima',
      'camposAgrupar' => ['id_materia_prima', 'cantidad_materia_prima'],
      'indicesNumericos' => true,
      'array' => $this->materiasPrimas,
    ]);

    //Operaciones
    $opMatPrimas = $this->DOAD([
      'arrayNuevo' => $this->materiasPrimas,
      'configArrayViejo' => [
        'tabla' => 'materias_primas_productos',
        'campos' => 'id_materia_prima,cantidad_materia_prima',
        'WHERE' => [
          'id_producto' => $this->idProducto
        ]
      ],
      'campoUnicoDif' => ['id_materia_prima'],
    ]);
    foreach ($opMatPrimas['eliminar'] as $pre) {
      $resultado = $this->eliminarDatos2([
        'tabla' => 'materias_primas_productos',
        'WHERE' => [
          'id_materia_prima' => $pre['id_materia_prima'],
          'id_producto' => $this->idProducto
        ]
      ]);
      if ($resultado <= 0 || $resultado == false) return $funcionError(1);
      $MAT += $resultado;
    }
    foreach ($opMatPrimas['registrar'] as $pres) {
      $resultado = $this->guardarDatos2([
        'tabla' => 'materias_primas_productos',
        'datos' => [
          "id_producto" => $this->idProducto,
          "id_materia_prima" => $pres['id_materia_prima'],
          "cantidad_materia_prima" => $pres['cantidad_materia_prima'],
        ]
      ]);
      if ($resultado == false || $resultado <= 0) return $funcionError(2);
      $MAT++;
    }
    foreach ($opMatPrimas['actualizar'] as $mat) {
      $resultado = $this->actualizarDatos2([
        'tabla' => 'materias_primas_productos',
        'datos' => [
          "id_materia_prima" => $mat['id_materia_prima'],
          "cantidad_materia_prima" => $mat['cantidad_materia_prima'],
        ],
        'WHERE' => [
          'id_producto' => $this->idProducto,
          'id_materia_prima' => $mat['id_materia_prima']
        ]
      ]);
      if ($resultado == false || $resultado <= 0) $funcionError(3);
      $MAT++;
    }

    //Operaciones en las Presentaciones
    $opPresentaciones = $this->DOAD([
      'arrayNuevo' => $this->presentaciones,
      'configArrayViejo' => [
        'tabla' => 'presentaciones_productos',
        'campos' => 'id_presentacion,mostrar_ecommerce',
        'WHERE' => [
          'id_producto' => $this->idProducto
        ]
      ],
      'campoUnicoDif' => 'id_presentacion',
    ]);
    foreach ($opPresentaciones['eliminar'] as $pre) {
      $foto = $this->seleccionarDatos2([
        'tabla' => 'presentaciones_productos',
        'campos' => 'foto_presentacion',
        'WHERE' => [
          'id_producto' => $this->idProducto,
          'id_presentacion' => $pre['id_presentacion']
        ]
      ])->fetch(PDO::FETCH_COLUMN);
      $this->Imagenes_Eli2('presentaciones_productos', $foto);
      $resultado = $this->eliminarDatos2([
        'tabla' => 'presentaciones_productos',
        'WHERE' => [
          'id_presentacion' => $pre['id_presentacion'],
          'id_producto' => $this->idProducto
        ]
      ]);
      if ($resultado <= 0 || $resultado == false) return $funcionError(4);
      $PRE += $resultado;
    }
    foreach ($opPresentaciones['registrar'] as $pres) {
      $foto = '';
      if (isset($pres['foto_presentacion'])) {
        $imagenesPresentaciones[] = $foto = $this->Imagenes_Reg('presentaciones_productos', $pres['foto_presentacion'], 'presentaciones_productos');
      }
      $id_presentacion_producto = $this->generarCodSeg([
        'tablaBD' => 'presentaciones_productos',
        'prefijo' => 'PRPR',
        'campoID' => 'id_presentacion_producto'
      ]);
      $resultado = $this->guardarDatos2([
        'tabla' => 'presentaciones_productos',
        'datos' => [
          "id_presentacion_producto" => $id_presentacion_producto,
          "id_producto" => $this->idProducto,
          "id_presentacion" => $pres['id_presentacion'],
          "mostrar_ecommerce" => $pres['mostrar_ecommerce'],
          "foto_presentacion" => $foto,
        ]
      ]);
      if ($resultado == false || $resultado <= 0) return $funcionError(5);
      $PRE++;
    }
    foreach ($opPresentaciones['actualizar'] as $pres) {
      $foto = '';
      if (isset($pres['foto_presentacion'])) {
        $imagenesPresentaciones[] = $foto = $this->Imagenes_Reg('presentaciones_productos', $pres['foto_presentacion'], 'presentaciones_productos');
      }
      $resultado = $this->actualizarDatos2([
        'tabla' => 'presentaciones_productos',
        'datos' => [
          "mostrar_ecommerce" => $pres['mostrar_ecommerce'],
          "foto_presentacion" => $foto,
        ],
        'WHERE' => [
          'id_producto' => $this->idProducto,
          'id_presentacion' => $pres['id_presentacion']
        ]
      ]);
      if ($resultado == false || $resultado <= 0) return $funcionError(6);
      $PRE++;
    }
    if ($MAT == 0 && $PRD == 0 && $PRE == 0 && $MPR == 0) return $funcionError(7);
    $bitacoraModelo = new bitacoraModelo();
    $resultado = $bitacoraModelo->registrarBitacora("productos", "actualizar", "éxito");
    if ($resultado) return $funcionError(8);

    $this->commit();
    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Producto actualizado",
      "texto" => "Exitoso",
      "icono" => "success"
    ];
  }
  private function eliminarProductosP() {
    $funcionError = function ($objBi) {
      $this->rollback();
      $objBi->registrarBitacora("productos", "Eliminar", "Fallido", true);
    };
    $objBi = new bitacoraModelo();
    $productoActual = $this->seleccionarProductos([
      'id_producto' => $this->idProducto,
    ]);

    //Presentaciones
    $resultado = $this->eliminarDatos2([
      'tabla' => "presentaciones_productos",
      'WHERE' => [
        "id_producto" => $this->idProducto
      ]
    ]);
    if ($resultado <= 0 || $resultado == false) {
      $funcionError($objBi);
      return [
        'tipo' => 'simple',
        'titulo' => 'Error',
        'texto' => 'Ocurrió un error eliminando las presentaciones del producto',
        'icono' => 'error',
      ];
    }

    //Materias Primas
    if (count($productoActual['detallesExtra']['materias_primas']) > 0) {
      $resultado = $this->eliminarDatos2([
        'tabla' => "materias_primas_productos",
        'WHERE' => [
          "id_producto" => $this->idProducto
        ]
      ]);
      if ($resultado <= 0 || $resultado == false) {
        $funcionError($objBi);
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'Ocurrió un error eliminando las materias primas asociadas al producto',
          'icono' => 'error',
        ];
      }
    }

    //Historial de precios
    $resultado = $this->eliminarDatos2([
      'tabla' => "precios_productos",
      'WHERE' => [
        "id_producto" => $this->idProducto
      ]
    ]);
    if ($resultado <= 0 || $resultado == false) {
      $funcionError($objBi);
      return [
        'tipo' => 'simple',
        'titulo' => 'Error',
        'texto' => 'Ocurrió un error eliminando el historial de precios del producto',
        'icono' => 'error',
      ];
    }

    //El producto
    $resultado = $this->eliminarDatos2([
      'tabla' => "productos",
      'WHERE' => [
        "id_producto" => $this->idProducto
      ]
    ]);
    if ($resultado <= 0 || $resultado == false) {
      $funcionError($objBi);
      return [
        'tipo' => 'simple',
        'titulo' => 'Error',
        'texto' => 'Ocurrió un error eliminando el producto',
        'icono' => 'error',
      ];
    }

    if ($objBi->registrarBitacora("productos", "Eliminar", "Éxito")) {
      $funcionError($objBi);
      return [
        'tipo' => 'simple',
        'titulo' => 'Error',
        'texto' => 'Ocurrió un error registrando el evento en la bitacora',
        'icono' => 'error',
      ];
    };

    // Fotos de las presentaciones
    foreach ($productoActual['detallesExtra']['presentaciones'] as $presentacion) {
      $resultado = $this->Imagenes_Eli2('presetaciones_productos', $presentacion['foto_presentacion']);
    }

    $this->commit();
    return [
      "tipo" => "simple",
      "titulo" => "Eliminado",
      "texto" => "El producto ha sido eliminado exitosamente",
      "icono" => "success"
    ];
  }
  private function actualizarFotPreProdP() {
    return $this->Imagenes_Act([
      'subCarpeta' => 'presentaciones_productos',
      'imagen' => $this->fotoPresentacion,
      'tablaBD' => 'presentaciones_productos',
      'nombreCampoFoto' => 'foto_presentacion',
      'nombreCampoId' => 'id_presentacion_producto',
      'valorId' => $this->idPresentacinProd,
    ]);
  }
  private function eliminarFotPreProdP() {
    return $this->Imagenes_Eli([
      'subCarpeta' => 'presentaciones_productos',
      'tablaBD' => 'presentaciones_productos',
      'nombreCampoFoto' => 'foto_presentacion',
      'nombreCampoId' => 'id_presentacion_producto',
      'valorId' => $this->idPresentacinProd,
    ]);
  }
}
