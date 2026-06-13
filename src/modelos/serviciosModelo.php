<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use PDO;

class serviciosModelo extends conexion {
  private string $idServicio = '';
  private string $idUnidadMedida = '';
  private string $nombreServicio = '';
  private float  $precioServicio = 0;
  private int $mostrarEcommerce = 0;
  private array $fotoServicio = [];
  private array  $productosServicio = [];

  public function validarServicios(array $instruccionesVal) {
    [
      'infoVal' => &$infoVal,
      'camposVal' => &$camposVal,
    ] = $instruccionesVal;
    $funcionAsignadora = function ($nombreCampo, &$valor) {
      $claveVal = [
        'id_servicio' => [
          "campo_nombre" => "id_servicio",
          "campo_valor" => &$valor,
          "formulario_nombre" => "id del servicio",
          "requerido" => true,
          "minimo" => minRegexIdSeguro,
          "maximo" => maxRegexIdSeguro,
          "expresion_re" => regexIdSeguro,
          "tabla" => "servicios",
          "debeSerUnico" => true,
          "debeExistir" => true,
        ],
        'id_unidad_medida' => [
          "campo_nombre" => "id_unidad_medida",
          "campo_valor" => &$valor,
          "formulario_nombre" => "unidad de medida",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "unidades_medidas",
          "debeExistir" => true,
        ],
        'nombre_servicio' => [
          "campo_nombre" => "nombre_servicio",
          "campo_valor" => &$valor,
          "formulario_nombre" => "nombre del servicio",
          "requerido" => true,
          "minimo" => minRegexNombreObj,
          "maximo" => maxRegexNombreObj,
          "expresion_re" => regexNombreObj,
          "tabla" => "servicios",
          "debeSerUnico" => true,
        ],
        'precio_servicio' => [
          "campo_nombre" => "precio_servicio",
          "campo_valor" => &$valor,
          'comaPunto' => true,
          "formulario_nombre" => "precio del servicio",
          "requerido" => true,
          "minimo" => minRegexPrecio,
          "maximo" => maxRegexPrecio,
          "expresion_re" => regexPrecio,
        ],
        'mostrar_ecommerce' => [
          "campo_valor" => &$valor,
          "formulario_nombre" => "mostrar en el ecommerce",
          "requerido" => true,
          "minimo" => minRegexValorBoleano,
          "maximo" => maxRegexValorBoleano,
          "expresion_re" => regexValorBoleano,
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
          "debeExistir" => true,
        ],
        'cantidad_producto' => [
          "campo_valor" => &$valor,
          "comaPunto" => true,
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
      switch ($campo) {
        case 'productos_servicio':
          if (($infoVal['productos_servicio'] ?? []) != []) {
            foreach ($infoVal['productos_servicio'] as &$prod) {
              $campos[] = $funcionAsignadora('id_producto', $prod['id_producto']);
              $campos[] = $funcionAsignadora('cantidad_producto', $prod['cantidad_producto']);
            }
            unset($prod);
          }
          break;
        default:
          $campos[] = $funcionAsignadora($campo, $infoVal[$campo]);
          break;
      }
    }
    return $this->limpiar_Verificar($campos);
  }

  // ─── SELECCIONAR ────────────────────────────────────────────────

  public function seleccionarServicios(array $info) {
    if (($info['id_servicio'] ?? '') != '') {
      $respuesta = $this->validarServicios([
        'infoVal' => &$info,
        'camposVal' => ['id_servicio'],
      ]);
      if ($respuesta !== false) return $respuesta;
      $this->idServicio = $info['id_servicio'];
    }
    return $this->seleccionarServiciosP($info);
  }

  // ─── REGISTRAR ──────────────────────────────────────────────────

  public function registrarServicio(array $info) {
    $respuesta = $this->validarServicios([
      'infoVal' => &$info,
      'camposVal' => [
        'id_unidad_medida',
        'nombre_servicio',
        'precio_servicio',
        'mostrar_ecommerce',
        'productos_servicio',
      ]
    ]);
    if ($respuesta !== false) return $respuesta;

    $this->idUnidadMedida    = $info['id_unidad_medida'];
    $this->nombreServicio    = $info['nombre_servicio'];
    $this->precioServicio    = $info['precio_servicio'];
    $this->mostrarEcommerce  = $info['mostrar_ecommerce'] ?? 0;
    $this->productosServicio = $info['productos_servicio'] ?? [];

    if (isset($info['foto_servicio']) && !empty($info['foto_servicio'])) {
      $this->fotoServicio = $info['foto_servicio'];
    }

    return $this->registrarServicioP();
  }

  // ─── ACTUALIZAR ─────────────────────────────────────────────────

  public function actualizarServicio(array $info) {
    $respuesta = $this->validarServicios([
      'infoVal' => &$info,
      'camposVal' => [
        'id_servicio',
        'id_unidad_medida',
        'nombre_servicio',
        'precio_servicio',
        'mostrar_ecommerce',
        'productos_servicio',
      ]
    ]);
    if ($respuesta !== false) return $respuesta;

    $this->idServicio        = $info['id_servicio'];
    $this->idUnidadMedida    = $info['id_unidad_medida'];
    $this->nombreServicio    = $info['nombre_servicio'];
    $this->precioServicio    = $info['precio_servicio'];
    $this->mostrarEcommerce  = $info['mostrar_ecommerce'] ?? 0;
    $this->productosServicio = $info['productos_servicio'] ?? [];

    if (isset($info['foto_servicio']) && !empty($info['foto_servicio'])) {
      $this->fotoServicio = $info['foto_servicio'];
    }

    return $this->actualizarServicioP();
  }

  // ─── ELIMINAR ───────────────────────────────────────────────────

  public function eliminarServicio(array $info) {
    $respuesta = $this->validarServicios([
      'infoVal' => &$info,
      'camposVal' => ['id_servicio'],
    ]);
    if ($respuesta !== false) return $respuesta;

    $this->idServicio = $info['id_servicio'];
    return $this->eliminarServicioP();
  }

  // ─── ACTUALIZAR FOTO ────────────────────────────────────────────

  public function actualizarFotoServicio(array $info) {
    $respuesta = $this->validarServicios([
      'infoVal' => &$info,
      'camposVal' => ['id_servicio'],
    ]);
    if ($respuesta !== false) return $respuesta;
    $this->idServicio = $info['id_servicio'];
    $this->fotoServicio = $info['foto_servicio'];
    return $this->actualizarFotoServicioP();
  }

  // ─── ELIMINAR FOTO ──────────────────────────────────────────────

  public function eliminarFotoServicio(array $info) {
    $respuesta = $this->validarServicios([
      'infoVal' => &$info,
      'camposVal' => ['id_servicio'],
    ]);
    if ($respuesta !== false) return $respuesta;
    $this->idServicio = $info['id_servicio'];
    return $this->eliminarFotoServicioP();
  }

  //-- PRIVADOS [ ENCAPSULAMIENTO ]--//

  private function seleccionarServiciosP(array $info) {
    if ($this->idServicio == null || $this->idServicio == "") {
      $resultado = $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'servicios as s',
        'datosJoins' => [
          "unidades_medidas as um" => "s.id_unidad_medida = um.id_unidad_medida",
        ]
      ]);
      return $resultado->fetchAll(PDO::FETCH_ASSOC);
    } else {
      // Datos generales
      $resultado = $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'servicios as s',
        'WHERE' => [
          "id_servicio" => $this->idServicio,
        ],
        'datosJoins' => [
          'unidades_medidas as um' => 's.id_unidad_medida = um.id_unidad_medida'
        ]
      ]);
      if ($resultado->rowCount() <= 0) {
        return [
          "tipo" => "simple",
          "titulo" => "Servicio no encontrado",
          "texto" => "El servicio no se encuentra.",
          "icono" => "error"
        ];
      }
      $servicio = $resultado->fetch(PDO::FETCH_ASSOC);

      // Productos del servicio (materias primas)
      $resultado = $this->seleccionarDatos2([
        'campos' => 'id_producto, cantidad_producto',
        'tabla' => 'productos_servicios',
        'WHERE' => [
          "id_servicio" => $this->idServicio
        ]
      ]);
      $productosServicio = $resultado->fetchAll(PDO::FETCH_ASSOC);
      $servicio['detallesExtra'] = [
        'productos_servicio' => $productosServicio,
      ];
      return $servicio;
    }
  }

  private function registrarServicioP() {
    $funcionError = function ($objBi) {
      $this->rollback();
      $objBi->registrarBitacora("servicios", "registrar", "fallido", true);
    };

    $idServicio = $this->generarCodSeg([
      'tablaBD' => 'servicios',
      'prefijo' => 'SERV',
      'campoID' => 'id_servicio'
    ]);

    $objBit = new bitacoraModelo();

    // Foto
    $nombreImagen = '';
    if ($this->fotoServicio != '') {
      $nombreImagen = $this->Imagenes_Reg(
        'servicios',
        $this->fotoServicio,
        'servicios'
      );
    }

    $resultado = $this->guardarDatos2([
      'tabla' => 'servicios',
      'datos' => [
        'id_servicio' => $idServicio,
        "id_unidad_medida" => $this->idUnidadMedida,
        "nombre_servicio" => $this->nombreServicio,
        "precio_servicio" => $this->precioServicio,
        "mostrar_ecommerce" => $this->mostrarEcommerce,
        "foto_servicio" => $nombreImagen,
      ],
    ]);

    if ($resultado == false || $resultado <= 0) {
      $funcionError($objBit);
      if ($nombreImagen != '') {
        $this->Imagenes_Eli2('servicios', $nombreImagen);
      }
      return [
        'tipo' => 'simple',
        'titulo' => 'Error',
        'texto' => 'No se pudo registrar el servicio',
        'icono' => 'error'
      ];
    }

    // Productos del servicio (materias primas)
    foreach ($this->productosServicio as $prod) {
      $idProd = $this->guardarDatos2([
        'tabla' => 'productos_servicios',
        'datos' => [
          "id_servicio" => $idServicio,
          "id_producto" => $prod['id_producto'],
          "cantidad_producto" => $prod['cantidad_producto'],
        ]
      ]);
      if ($idProd == false || $idProd <= 0) {
        $funcionError($objBit);
        if ($nombreImagen != '') {
          $this->Imagenes_Eli2('servicios', $nombreImagen);
        }
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'No se pudieron registrar los productos del servicio',
          'icono' => 'error'
        ];
      }
    }

    $objBit->registrarBitacora("servicios", "registrar", "éxito");
    $this->commit();
    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Servicio registrado",
      "texto" => "El servicio ha sido registrado exitosamente",
      "icono" => "success"
    ];
  }

  private function actualizarServicioP() {
    $SRV = 0;
    $PRS = 0;

    $funcionError = function () {
      $bitacoraModelo = new bitacoraModelo();
      $this->rollback();
      $bitacoraModelo->registrarBitacora("servicios", "actualizar", "fallido", true);
    };

    $servicioActual = $this->seleccionarServicios(['id_servicio' => $this->idServicio]);

    // Datos generales
    $resultado = $this->actualizarDatos2([
      "tabla" => "servicios",
      "datos" => [
        "id_unidad_medida" => $this->idUnidadMedida,
        "nombre_servicio" => $this->nombreServicio,
        "precio_servicio" => $this->precioServicio,
        "mostrar_ecommerce" => $this->mostrarEcommerce,
      ],
      "WHERE" => [
        "id_servicio" => $this->idServicio,
      ]
    ]);
    if ($resultado != false && $resultado > 0) $SRV++;

    // Productos del servicio
    if (($servicioActual['detallesExtra']['productos_servicio'] ?? []) != []) {
      $PRS += $resultado = $this->eliminarDatos2([
        'tabla' => "productos_servicios",
        'WHERE' => [
          "id_servicio" => $this->idServicio
        ],
        'fisico' => true
      ]);
      if ($resultado == false || $resultado <= 0) {
        $funcionError();
        return [
          'tipo' => 'simple',
          'titulo' => 'Productos anteriores no eliminados',
          'texto' => 'No se pudo actualizar el servicio',
          'icono' => 'error',
        ];
      }
    }

    foreach ($this->productosServicio as $prod) {
      $resultado = $this->guardarDatos2([
        'tabla' => 'productos_servicios',
        'datos' => [
          "id_servicio" => $this->idServicio,
          "id_producto" => $prod['id_producto'],
          "cantidad_producto" => $prod['cantidad_producto'],
        ]
      ]);
      if ($resultado != false && $resultado > 0) $PRS++;
    }

    if ($SRV == 0 && $PRS == 0) {
      $funcionError();
      return [
        'icono' => 'warning',
        'titulo' => 'Sin Modificaciones',
        'texto' => 'No se detectaron cambios',
        'tipo' => 'simple'
      ];
    }

    $bitacoraModelo = new bitacoraModelo();
    $resultado = $bitacoraModelo->registrarBitacora("servicios", "actualizar", "éxito");
    if ($resultado) {
      $funcionError();
      return $resultado;
    }

    $this->commit();
    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Servicio actualizado",
      "texto" => "El servicio ha sido actualizado exitosamente",
      "icono" => "success"
    ];
  }

  private function eliminarServicioP() {
    $funcionError = function ($objBi) {
      $this->rollback();
      $objBi->registrarBitacora("servicios", "Eliminar", "Fallido", true);
    };
    $objBi = new bitacoraModelo();

    $servicioActual = $this->seleccionarServicios([
      'id_servicio' => $this->idServicio,
    ]);

    // Productos del servicio
    if (count($servicioActual['detallesExtra']['productos_servicio'] ?? []) > 0) {
      $resultado = $this->eliminarDatos2([
        'tabla' => "productos_servicios",
        'WHERE' => [
          "id_servicio" => $this->idServicio
        ]
      ]);
      if ($resultado <= 0 || $resultado == false) {
        $funcionError($objBi);
        return [
          'tipo' => 'simple',
          'titulo' => 'Error',
          'texto' => 'Ocurrió un error eliminando los productos asociados al servicio',
          'icono' => 'error',
        ];
      }
    }

    // El servicio
    $resultado = $this->eliminarDatos2([
      'tabla' => "servicios",
      'WHERE' => [
        "id_servicio" => $this->idServicio
      ]
    ]);
    if ($resultado <= 0 || $resultado == false) {
      $funcionError($objBi);
      return [
        'tipo' => 'simple',
        'titulo' => 'Error',
        'texto' => 'Ocurrió un error eliminando el servicio',
        'icono' => 'error',
      ];
    }

    if ($objBi->registrarBitacora("servicios", "Eliminar", "Éxito")) {
      $funcionError($objBi);
      return [
        'tipo' => 'simple',
        'titulo' => 'Error',
        'texto' => 'Ocurrió un error registrando el evento en la bitácora',
        'icono' => 'error',
      ];
    }

    // Foto del servicio
    if (($servicioActual['foto_servicio'] ?? '') != '') {
      $this->Imagenes_Eli2('servicios', $servicioActual['foto_servicio']);
    }

    $this->commit();
    return [
      "tipo" => "simple",
      "titulo" => "Servicio eliminado",
      "texto" => "El servicio ha sido eliminado con éxito",
      "icono" => "success"
    ];
  }

  private function actualizarFotoServicioP() {
    $nombreImagen = $this->Imagenes_Reg(
      'servicios',
      $this->fotoServicio,
      'servicios'
    );
    $resultado = $this->actualizarDatos2([
      'tabla' => 'servicios',
      'datos' => [
        'foto_servicio' => $nombreImagen,
      ],
      'WHERE' => [
        'id_servicio' => $this->idServicio,
      ]
    ]);
    if ($resultado == false || $resultado <= 0) {
      $this->Imagenes_Eli2('servicios', $nombreImagen);
      return [
        'tipo' => 'simple',
        'titulo' => 'Error',
        'texto' => 'No se pudo actualizar la foto del servicio',
        'icono' => 'error',
      ];
    }
    $this->commit();
    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Foto actualizada",
      "texto" => "La foto del servicio ha sido actualizada",
      "icono" => "success"
    ];
  }

  private function eliminarFotoServicioP() {
    $servicioActual = $this->seleccionarServicios(['id_servicio' => $this->idServicio]);
    if (($servicioActual['foto_servicio'] ?? '') != '') {
      $this->Imagenes_Eli2('servicios', $servicioActual['foto_servicio']);
    }
    $resultado = $this->actualizarDatos2([
      'tabla' => 'servicios',
      'datos' => [
        'foto_servicio' => '',
      ],
      'WHERE' => [
        'id_servicio' => $this->idServicio,
      ]
    ]);
    if ($resultado == false || $resultado <= 0) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Error',
        'texto' => 'No se pudo eliminar la foto del servicio',
        'icono' => 'error',
      ];
    }
    $this->commit();
    return [
      "tipo" => "simple",
      "titulo" => "Foto eliminada",
      "texto" => "La foto del servicio ha sido eliminada",
      "icono" => "success"
    ];
  }
}
