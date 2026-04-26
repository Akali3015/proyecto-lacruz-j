<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use PDO;

class productosModelo extends conexion
{
  private $idProducto;
  private $idUnidadMedida;
  private $idCategoria;
  private $nombreProducto;
  private $precioProducto;
  private $stockProducto;
  private $stockMinimoProducto;
  private $mostrarEcommerce;
  private $fotoProducto;
  private $presentaciones;
  private $materiasPrimas;

  public function validacionesProductos($camposVal)
  {
    $campos = [];
    $claveVal = [
      'id_producto' => [
        "campo_nombre" => "id_producto",
        "campo_valor" => &$this->idProducto,
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
        "campo_valor" => &$this->idUnidadMedida,
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
        "campo_valor" => &$this->idCategoria,
        "formulario_nombre" => "categoria",
        "requerido" => true,
        "minimo" => minRegexId,
        "maximo" => maxRegexId,
        "expresion_re" => regexId,
        "tabla" => "categorias_productos",
        "debeExistir" => true,
      ],
      'nombre_producto' => [
        "campo_nombre" => "nombre_producto",
        "campo_valor" => &$this->nombreProducto,
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
        "campo_valor" => &$this->precioProducto,
        'comaPunto' => true,
        "formulario_nombre" => "precio en divisas",
        "requerido" => true,
        "minimo" => minRegexPrecio,
        "maximo" => maxRegexPrecio,
        "expresion_re" => regexPrecio,
      ],
      'stock_producto' => [
        "campo_nombre" => "stock_producto",
        "campo_valor" => &$this->stockProducto,
        "formulario_nombre" => "stock",
        "requerido" => true,
        "minimo" => minRegexCantidadItem,
        "maximo" => maxRegexCantidadItem,
        "expresion_re" => regexCantidadItem,
      ],
      'stock_producto' => [
        "campo_nombre" => "stock_minimo_producto",
        "campo_valor" => &$this->stockMinimoProducto,
        "formulario_nombre" => "stock mínimo",
        "requerido" => true,
        "minimo" => minRegexCantidadItem,
        "maximo" => maxRegexCantidadItem,
        "expresion_re" => regexCantidadItem,
      ],
      'mostrar_ecommerce' => [
        "campo_valor" => &$this->mostrarEcommerce,
        "formulario_nombre" => "mostrar ecommerce",
        "requerido" => true,
        "minimo" => minRegexValorBoleano,
        "maximo" => maxRegexValorBoleano,
        "expresion_re" => regexValorBoleano,
      ],
    ];
    foreach ($camposVal as $campo) {
      if (
        isset($claveVal[$campo]) &&
        $campo != 'presentaciones' &&
        $campo != 'materias_primas'
      ) $campos[] = $claveVal[$campo];
      if ($campo == 'presentaciones') {
        if ($this->presentaciones == []) {
          return [
            'tipo' => 'simple',
            'titulo' => 'Sin presentaciones',
            'texto' => 'No has enviado las presentaciones del producto',
            'icono' => 'warning',
          ];
        }
        foreach ($this->presentaciones as &$idPre) {
          $campos[] = [
            "campo_nombre" => "id_presentacion",
            "campo_valor" => &$idPre,
            "formulario_nombre" => "id de la presentación",
            "requerido" => true,
            "minimo" => minRegexId,
            "maximo" => maxRegexId,
            "expresion_re" => regexId,
            "tabla" => "presentaciones",
            "debeExistir" => true,
          ];
        }
        unset($idPre);
      }
      if ($campo == 'materias_primas' && $this->idCategoria == 1) {
        if ($this->materiasPrimas == []) {
          return [
            'tipo' => 'simple',
            'titulo' => 'Sin presentaciones',
            'texto' => 'No has enviado las presentaciones del producto',
            'icono' => 'warning',
          ];
        }
        foreach ($this->materiasPrimas as &$materiaInd) {
          $campos[] = [
            "campo_nombre" => "id_materia_prima",
            "campo_valor" => &$materiaInd['id_materia_prima'],
            "formulario_nombre" => "id de la materia prima",
            "requerido" => true,
            "minimo" => minRegexId,
            "maximo" => maxRegexId,
            "expresion_re" => regexId,
            "tabla" => "materias_primas",
            "debeExistir" => true,
          ];
          $campos[] = [
            "campo_valor" => &$materiaInd['cantidad_materia_prima'],
            "comaPunto" => true,
            "formulario_nombre" => "cantidad de la materia prima",
            "requerido" => true,
            "minimo" => minRegexCantidadItem,
            "maximo" => maxRegexCantidadItem,
            "expresion_re" => regexCantidadItem,
          ];
        }
        unset($materiaInd);
      }
    }
    return $this->limpiar_Verificar($campos);
  }
  public function seleccionarProductos($datos)
  {
    $this->idProducto = $datos['id_producto'] ?? null;
    if ($this->idProducto != null && $this->idProducto != "") {
      $respuesta = $this->validacionesProductos([
        'id_producto',
      ]);
      if ($respuesta !== false) return $respuesta;
    }
    return $this->seleccionarProductosP();
  }
  public function registrarProductos($datos)
  {
    // return $datos;
    $this->idUnidadMedida = $datos['id_unidad_medida'] ?? '';
    $this->idCategoria = $datos['id_categoria_producto'] ?? '';
    $this->nombreProducto = $datos['nombre_producto'] ?? '';
    $this->precioProducto = $datos['precio_producto'] ?? '';
    $this->stockProducto = $datos['stock_producto'] ?? '';
    $this->stockMinimoProducto = $datos['stock_minimo_producto'] ?? '';
    $this->mostrarEcommerce = $datos['mostrar_ecommerce'] ?? '';
    $this->presentaciones = $datos['presentaciones'] ?? [];
    $this->materiasPrimas = $datos['materias_primas'] ?? [];
    $this->fotoProducto = $datos['foto_producto'] ?? [];

    $respuesta = $this->validacionesProductos([
      'id_unidad_medida',
      'id_categoria_producto',
      'nombre_producto',
      'precio_producto',
      'precio_producto_bcv',
      'stock_producto',
      'stock_minimo_producto',
      'mostrar_ecommerce',
      'presentaciones',
      'materias_primas',
    ]);
    if ($respuesta !== false) return $respuesta;
    return $this->registrarProductosP();
  }
  public function actualizarProductos($datos)
  {
    $this->idProducto = $datos['id_producto'] ?? '';
    $this->idUnidadMedida = $datos['id_unidad_medida'] ?? '';
    $this->idCategoria = $datos['id_categoria_producto'] ?? '';
    $this->nombreProducto = $datos['nombre_producto'] ?? '';
    $this->precioProducto = $datos['precio_producto'] ?? '';
    $this->stockProducto = $datos['stock_producto'] ?? '';
    $this->stockMinimoProducto = $datos['stock_minimo_producto'] ?? '';
    $this->mostrarEcommerce = $datos['mostrar_ecommerce'] ?? '';
    $this->presentaciones = $datos['presentaciones'] ?? [];
    $this->materiasPrimas = $datos['materias_primas'] ?? [];

    $respuesta = $this->validacionesProductos([
      'id_producto',
      'id_unidad_medida',
      'id_categoria_producto',
      'nombre_producto',
      'precio_producto',
      'stock_producto',
      'stock_minimo_producto',
      'mostrar_ecommerce',
      'presentaciones',
      'materias_primas',
    ]);
    if ($respuesta !== false) return $respuesta;
    return $this->actualizarProductosP();
  }
  public function eliminarProductos($datos)
  {
    $this->idProducto = $datos['id_producto'];
    $respuesta = $this->validacionesProductos([
      'id_producto',
    ]);
    if ($respuesta !== false) return $respuesta;
    return $this->eliminarProductosP();
  }
  public function actualizarFotosProductos($datos)
  {
    $this->idProducto = $datos['id_producto'];
    $this->fotoProducto = $datos['foto_producto'];
    $respuesta = $this->validacionesProductos([
      'id_producto',
    ]);
    if ($respuesta !== false) return $respuesta;
    return $this->actualizarFotosProductosP();
  }
  public function eliminarFotosProductos($datos)
  {
    $this->idProducto = $datos['id_producto'];
    $respuesta = $this->validacionesProductos([
      'id_producto',
    ]);
    if ($respuesta !== false) return $respuesta;
    return $this->eliminarFotosProductosP();
  }

  private function seleccionarProductosP()
  {
    if ($this->idProducto == null || $this->idProducto == "") {
      $resultado = $this->seleccionarDatos2([
        'campos' => '
          p.id_producto, p.nombre_producto, 
          um.nombre_unidad_medida, p.stock_producto, p.stock_minimo_producto,
          p.precio_producto, p.mostrar_ecommerce, cp.nombre_categoria,foto_producto
        ',
        'tabla' => 'productos as p',
        'datosJoins' => [
          "unidades_medidas as um" => "p.id_unidad_medida = um.id_unidad_medida",
          "categorias_productos as cp" => "p.id_categoria_producto = cp.id_categoria_producto",
        ]
      ]);
      return $resultado->fetchAll(PDO::FETCH_ASSOC);
    } else {

      //Dato generales  
      $resultado = $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'productos as pr',
        'WHERE' => [
          "id_producto" => $this->idProducto,
        ],
        'datosJoins' => [
          'categorias_productos as cp' => 'pr.id_categoria_producto = cp.id_categoria_producto'
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
      $producto = $resultado->fetch(PDO::FETCH_ASSOC);

      $resultado = $this->seleccionarDatos2([
        'campos' => 'id_presentacion',
        'tabla' => 'presentaciones_productos',
        'WHERE' => [
          "id_producto" => $this->idProducto,
        ]
      ]);
      $idsPresentaciones = $resultado->fetchAll(PDO::FETCH_COLUMN);
      $resultado = $this->seleccionarDatos2([
        'campos' => 'id_materia_prima, cantidad_materia_prima',
        'tabla' => 'materias_primas_productos',
        'WHERE' => [
          "id_producto" => $this->idProducto
        ]
      ]);
      $materiasPrimas = $resultado->fetchAll(PDO::FETCH_ASSOC);
      $producto['detallesExtra'] = [
        'presentaciones' => $idsPresentaciones,
        'materias_primas' => $materiasPrimas
      ];
      return $producto;
    }
  }
  private function registrarProductosP()
  {
    $funcionError = function ($objBi, $NI) {
      $this->rollback();
      $objBi->registrarBitacora("productos", "registrar", "fallido", true);
      $this->Imagenes_Eli2('productos', $NI);
    };
    $idProducto = $this->generarCodSeg([
      'tablaBD' => 'productos',
      'prefijo' => 'PROD',
      'campoID' => 'id_producto'
    ]);
    // Registro de la imagen
    $nombreImagen = $this->Imagenes_Reg('productos', $this->fotoProducto, 'productos');
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
        "mostrar_ecommerce" => $this->mostrarEcommerce,
        "foto_producto" => $nombreImagen,
      ],
    ]);

    if ($resultado == false || $resultado <= 0) {
      $funcionError($objBit, $nombreImagen);
      return [
        'tipo' => 'simple',
        'titulo' => 'Error',
        'texto' => 'No registrado en BD',
        'icono' => 'error'
      ];
    }

    // Historial Precios
    $resultado = $this->guardarDatos2([
      'tabla' => 'precios_productos',
      'datos' => [
        "id_producto" => $idProducto,
        "precio_producto" => $this->precioProducto,
        "fecha_cambio" => date('Y-m-d H:i:s'),
      ]
    ]);
    if ($resultado == false || $resultado <= 0) {
      $funcionError($objBit, $nombreImagen);
      return [
        'tipo' => 'simple',
        'titulo' => 'Error',
        'texto' => 'Error al registrar el precio del producto',
        'icono' => 'error'
      ];
    }

    foreach ($this->presentaciones as $idPresentacion) {
      $idPre = $this->guardarDatos2([
        'tabla' => 'presentaciones_productos',
        'datos' => [
          "id_producto" => $idProducto,
          "id_presentacion" => $idPresentacion,
        ]
      ]);
      if ($idPre == false || $idPre <= 0) {
        $funcionError($objBit, $nombreImagen);
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'No se pudo registrar las presentaciones del producto',
          'icono' => 'error'
        ];
      }
    }
    if ($this->idCategoria == 1) {
      foreach ($this->materiasPrimas as $mp) {
        $idMp = $this->guardarDatos2([
          'tabla' => 'materias_primas_productos',
          'datos' => [
            "id_producto" => $idProducto,
            "id_materia_prima" => $mp['id_materia_prima'],
            "cantidad_materia_prima" => $mp['cantidad_materia_prima'],
          ]
        ]);
        if ($idMp == false || $idMp <= 0) {
          $funcionError($objBit, $nombreImagen);
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
  private function actualizarProductosP()
  {
    $PRD = 0;
    $MPR = 0;
    $PRE = 0;

    $funcionError = function () {
      $bitacoraModelo = new bitacoraModelo();
      $this->rollback();
      $bitacoraModelo->registrarBitacora("productos", "actualizar", "fallido", true);
    };

    $productoActual = $this->seleccionarProductos(['id_producto' => $this->idProducto]);

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
        "mostrar_ecommerce" => $this->mostrarEcommerce
      ],
      "WHERE" => [
        "id_producto" => $this->idProducto,
      ]
    ]);
    if ($resultado != false && $resultado > 0) $PRD++;

    // Verificar si el precio cambió
    if ($productoActual['precio_producto'] != $this->precioProducto) {
      $resultado = $this->guardarDatos2([
        'tabla' => 'precios_productos',
        'datos' => [
          "id_producto" => $this->idProducto,
          "precio_producto" => $this->precioProducto,
          "fecha_cambio" => date('Y-m-d H:i:s')
        ]
      ]);
      if ($resultado != false && $resultado > 0) $PRD++;
    }

    //Materias primas
    $aux = [];
    foreach ($this->materiasPrimas as $mp) {
      if (isset($aux[$mp['id_materia_prima']])) {
        $aux[$mp['id_materia_prima']] += $mp['cantidad_materia_prima'];
      } else {
        $aux[$mp['id_materia_prima']] = $mp['cantidad_materia_prima'];
      }
    }
    if (isset($productoActual['detallesExtra']['materias_primas'])) {
      $MPR += $resultado = $this->eliminarDatos2([
        'tabla' => "materias_primas_productos",
        'WHERE' => [
          "id_producto" => $this->idProducto
        ],
        'fisico' => true
      ]);
      if ($resultado == false || $resultado <= 0) {
        $funcionError();
        return [
          'tipo' => 'simple',
          'titulo' => 'Materias primas anteriores no eliminadas',
          'texto' => 'No se pudo actualizar el producto',
          'icono' => 'error',
        ];
      }
    }
    foreach ($aux as $id => $cantidad) {
      $resultado = $this->guardarDatos2([
        'tabla' => 'materias_primas_productos',
        'datos' => [
          "id_producto" => $this->idProducto,
          "id_materia_prima" => $id,
          "cantidad_materia_prima" => $cantidad,
        ]
      ]);
      if ($resultado != false && $resultado > 0) $MPR++;
    }

    //Presentaciones
    if (isset($productoActual['detallesExtra']['presentaciones'])) {
      $PRE += $resultado = $this->eliminarDatos2([
        'tabla' => "presentaciones_productos",
        'WHERE' => [
          "id_producto" => $this->idProducto
        ],
        'fisico' => true
      ]);
      if ($resultado == false || $resultado <= 0) {
        $funcionError();
        return [
          'tipo' => 'simple',
          'titulo' => 'Presentaciones anteriores no eliminadas',
          'texto' => 'No se pudo actualizar el producto',
          'icono' => 'error',
        ];
      }
    }
    foreach ($this->presentaciones as $idP) {
      $resultado = $this->guardarDatos2([
        'tabla' => 'presentaciones_productos',
        'datos' => [
          "id_producto" => $this->idProducto,
          "id_presentacion" => $idP,
        ]
      ]);
      if ($resultado != false && $resultado > 0) $PRE++;
    }

    if ($PRD == 0 && $PRE == 0 && $MPR == 0) {
      $funcionError();
      return [
        'icono' => 'warning',
        'titulo' => 'Sin Modificaciones',
        'texto' => 'No se detectaron cambios',
        'tipo' => 'simple'
      ];
    }

    // return [$PRD, $PRE, $MPR];

    $bitacoraModelo = new bitacoraModelo();
    $resultado = $bitacoraModelo->registrarBitacora("productos", "actualizar", "éxito");
    if ($resultado) {
      $funcionError();
      return $resultado;
    }

    $this->commit();
    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Producto actualizado",
      "texto" => "Exitoso",
      "icono" => "success"
    ];
  }
  private function eliminarProductosP()
  {
    $funcionError = function ($objBi) {
      $this->rollback();
      $objBi->registrarBitacora("productos", "Eliminar", "Fallido", true);
    };
    $objBi = new bitacoraModelo();
    $productoActual = $this->seleccionarProductos([
      'id_producto' => $this->idProducto
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

    //La foto
    $resultado = $this->eliminarFotosProductosP();
    if ($resultado['icono'] != 'success') {
      $funcionError($objBi);
      return [
        'tipo' => 'simple',
        'titulo' => 'Error',
        'texto' => 'Ocurrió un error eliminando la foto del producto',
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
    $this->commit();
    return [
      "tipo" => "simple",
      "titulo" => "Eliminado",
      "texto" => "El producto ha sido eliminado exitosamente",
      "icono" => "success"
    ];
  }
  private function actualizarFotosProductosP()
  {
    return $this->Imagenes_Act([
      'subCarpeta' => 'productos',
      'imagen' => $this->fotoProducto,
      'tablaBD' => 'productos',
      'nombreCampoFoto' => 'foto_producto',
      'nombreCampoId' => 'id_producto',
      'valorId' => $this->idProducto,
    ]);
  }
  private function eliminarFotosProductosP()
  {
    return $this->Imagenes_Eli([
      'subCarpeta' => 'productos',
      'tablaBD' => 'productos',
      'nombreCampoFoto' => 'foto_producto',
      'nombreCampoId' => 'id_producto',
      'valorId' => $this->idProducto,
    ]);
  }
}
