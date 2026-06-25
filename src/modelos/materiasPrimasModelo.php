<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;

class materiasPrimasModelo extends conexion
{
  private string $idMateriaPrima = '';
  private string $idUnidadMedida = '';
  private string $nombreMateriaPrima = '';
  private float $precioMateriaPrima = 0;
  private int $stockMateriaPrima = 0;
  private int $stockMinimoMateriaPrima = 0;
  private array $presentaciones = [];

  public function modificarStock(string $id_materia_prima, float $cantidad, $conexionTransaction = null) {
    try {
      $cn = $conexionTransaction ?? $this->conectar();
      $stmt = $cn->prepare("UPDATE materias_primas SET stock_materia_prima = stock_materia_prima + :cant WHERE id_materia_prima = :id");
      $stmt->execute([
        ':cant' => $cantidad,
        ':id' => $id_materia_prima
      ]);
      return true;
    } catch (\Throwable $th) {
      return $th->getMessage();
    }
  }

  public function validarMateriasPrimas(array $instruccionesVal)
  {
    [
      'infoVal' => &$infoVal,
      'camposVal' => &$camposVal,
    ] = $instruccionesVal;
    $funcionAsignadora = function ($nombreCampo, &$valor) {
      $claveVal = [
        'id_materia_prima' => [
          "campo_nombre" => "id_materia_prima",
          "campo_valor" => &$valor,
          "formulario_nombre" => "id de la materia prima",
          "requerido" => true,
          "minimo" => minRegexIdSeguro,
          "maximo" => maxRegexIdSeguro,
          "expresion_re" => regexIdSeguro,
          "tabla" => "materias_primas",
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
        'nombre_materia_prima' => [
          "campo_nombre" => "nombre_materia_prima",
          "campo_valor" => &$valor,
          "formulario_nombre" => "nombre de la materia prima",
          "requerido" => true,
          "minimo" => minRegexNombreObj,
          "maximo" => maxRegexNombreObj,
          "expresion_re" => regexNombreObj,
          "tabla" => "materias_primas",
          "debeSerUnico" => true,
        ],
        'precio_materia_prima' => [
          "campo_valor" => &$valor,
          'comaPunto' => true,
          "formulario_nombre" => "precio de la matería prima",
          "requerido" => true,
          "minimo" => minRegexPrecio,
          "maximo" => maxRegexPrecio,
          "expresion_re" => regexPrecio,
        ],
        'stock_materia_prima' => [
          "campo_nombre" => "stock_materia_prima",
          "campo_valor" => &$valor,
          "formulario_nombre" => "stock",
          "requerido" => true,
          "minimo" => minRegexCantidadItem,
          "maximo" => maxRegexCantidadItem,
          "expresion_re" => regexCantidadItem,
        ],
        'stock_minimo_materia_prima' => [
          "campo_nombre" => "stock_minimo_materia_prima",
          "campo_valor" => &$valor,
          "formulario_nombre" => "stock mínimo",
          "requerido" => true,
          "minimo" => minRegexCantidadItem,
          "maximo" => maxRegexCantidadItem,
          "expresion_re" => regexCantidadItem,
        ],
        'id_presentacion' => [
          "campo_nombre" => "id_presentacion",
          "campo_valor" => &$valor,
          "formulario_nombre" => "presentación",
          "requerido" => true,
          "minimo" => minRegexIdSeguro,
          "maximo" => maxRegexIdSeguro,
          "expresion_re" => regexIdSeguro,
          "tabla" => "presentaciones",
          "debeExistir" => true,
        ],
      ];
      return $claveVal[$nombreCampo];
    };
    $campos = [];
    foreach ($camposVal as $campo) {
      switch ($campo) {
        case 'presentaciones':
          if (($infoVal['presentaciones'] ?? []) == []) {
            return [
              'tipo' => 'simple',
              'titulo' => 'Sin presentaciones',
              'texto' => 'No has enviado las presentaciones de la materia prima',
              'icono' => 'warning',
            ];
          }
          foreach ($infoVal['presentaciones'] as &$pre) {
            $campos[] = $funcionAsignadora('id_presentacion', $pre);
          }
          unset($idPre);
          break;

        default:
          $campos[] = $funcionAsignadora($campo, $infoVal[$campo]);
          break;
      }
    }
    return $this->limpiar_Verificar($campos);
  }
  public function seleccionarMateriasPrimas($info = null)
  {
    if (isset($info['id_materia_prima'])) {
      $resultado = $this->validarMateriasPrimas([
        'infoVal' => &$info,
        'camposVal' => [
          'id_materia_prima',
        ],
      ]);
      if ($resultado) return $resultado;
      $this->idMateriaPrima = $info['id_materia_prima'];
    }
    return $this->seleccionarMateriasPrimasP();
  }
  public function registrarMateriasPrimas(array $info)
  {
    $resultado = $this->validarMateriasPrimas([
      'infoVal' => &$info,
      'camposVal' => [
        'id_unidad_medida',
        'nombre_materia_prima',
        'precio_materia_prima',
        'stock_materia_prima',
        'stock_minimo_materia_prima',
        'presentaciones',
      ],
    ]);
    if ($resultado) return $resultado;

    $this->idUnidadMedida = $info['id_unidad_medida'];
    $this->nombreMateriaPrima = $info['nombre_materia_prima'];
    $this->precioMateriaPrima = $info['precio_materia_prima'];
    $this->stockMateriaPrima = $info['stock_materia_prima'];
    $this->stockMinimoMateriaPrima = $info['stock_minimo_materia_prima'];
    $this->presentaciones = $info['presentaciones'];

    return $this->registrarMateriasPrimasP();
  }
  public function actualizarMateriasPrimas(array $info)
  {
    $resultado = $this->validarMateriasPrimas([
      'infoVal' => &$info,
      'camposVal' => [
        'id_materia_prima',
        'id_unidad_medida',
        'nombre_materia_prima',
        'precio_materia_prima',
        'stock_materia_prima',
        'stock_minimo_materia_prima',
        'presentaciones',
      ],
    ]);
    if ($resultado) return $resultado;

    $this->idMateriaPrima = $info['id_materia_prima'];
    $this->idUnidadMedida = $info['id_unidad_medida'];
    $this->nombreMateriaPrima = $info['nombre_materia_prima'];
    $this->precioMateriaPrima = $info['precio_materia_prima'];
    $this->stockMateriaPrima = $info['stock_materia_prima'];
    $this->stockMinimoMateriaPrima = $info['stock_minimo_materia_prima'];
    $this->presentaciones = $info['presentaciones'];

    return $this->actualizarMateriasPrimasP();
  }
  public function eliminarMateriasPrimas(array $info)
  {
    $resultado = $this->validarMateriasPrimas([
      'infoVal' => &$info,
      'camposVal' => [
        'id_materia_prima',
      ],
    ]);
    if ($resultado) return $resultado;
    $this->idMateriaPrima = $info['id_materia_prima'];
    return $this->eliminarMateriasPrimasP();
  }

  private function seleccionarMateriasPrimasP()
  {
    if ($this->idMateriaPrima == null || $this->idMateriaPrima == "") {
      $instruccionesBD = [
        'campos' => '
          mp.id_materia_prima, mp.nombre_materia_prima,
          um.nombre_unidad_medida, mp.stock_materia_prima, 
          mp.stock_minimo_materia_prima, 
          mp.precio_materia_prima,mp.id_unidad_medida
        ',
        'tabla' => 'materias_primas as mp',
        'datosJoins' => [
          "unidades_medidas as um" => "mp.id_unidad_medida = um.id_unidad_medida",
        ]
      ];
      return $this->seleccionarDatos2($instruccionesBD)->fetchAll();
    } else {
      //Datos generales
      $materiaPrima = $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'materias_primas as mp',
        'WHERE' => [
          "id_materia_prima" => $this->idMateriaPrima,
        ],
        'datosJoins' => [
          'unidades_medidas as um' => 'mp.id_unidad_medida = um.id_unidad_medida'
        ]
      ])->fetch();

      //Presentaciones
      $presentaciones = $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'presentaciones_materias_primas as prmp',
        'WHERE' => [
          "id_materia_prima" => $this->idMateriaPrima,
        ],
        'datosJoins' => [
          'presentaciones as pr' => 'prmp.id_presentacion = pr.id_presentacion'
        ]
      ])->fetchAll() ?? [];
      $materiaPrima['presentaciones'] = $presentaciones;
      return $materiaPrima;
    }
  }
  private function registrarMateriasPrimasP()
  {
    $idMateriaPrima = $this->generarCodSeg([
      'tablaBD' => 'materias_primas',
      'prefijo' => 'MATE',
      'campoID' => 'id_materia_prima'
    ]);
    $ultimoId = $this->guardarDatos2([
      'tabla' => 'materias_primas',
      'datos' => [
        'id_materia_prima' => $idMateriaPrima,
        "id_unidad_medida" => $this->idUnidadMedida,
        "nombre_materia_prima" => $this->nombreMateriaPrima,
        "stock_materia_prima" => $this->stockMateriaPrima,
        "stock_minimo_materia_prima" => $this->stockMinimoMateriaPrima,
        "precio_materia_prima" => $this->precioMateriaPrima,
      ]
    ]);
    if ($ultimoId == false || $ultimoId < 1) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Materia prima no registrada',
        'texto' => 'La materia prima no ha podido ser registrada',
        'icono' => 'error',
      ];
    }
    if (!empty($this->presentaciones)) {
      foreach ($this->presentaciones as $idPresentacion) {
        if ($idPresentacion != '') {
          $ultimoId = $this->guardarDatos2([
            'tabla' => 'presentaciones_materias_primas',
            'datos' => [
              'id_materia_prima' => $idMateriaPrima,
              "id_presentacion" => $idPresentacion,
            ]
          ]);
          if ($ultimoId == false || $ultimoId < 1) {
            return [
              'tipo' => 'simple',
              'titulo' => 'Presentación no registrada',
              'texto' => 'La presentación de la materia prima no ha podido ser registrada',
              'icono' => 'error',
            ];
          }
        }
      }
    }
    $modeloBitacora = new bitacoraModelo();
    if ($ultimoId !== false && $ultimoId > 0) {
      $alerta = [
        "tipo" => "limpiarYcerrar",
        "titulo" => "Materia prima registrada",
        "texto" => "La materia prima ha sido registrada exitosamente",
        "icono" => "success"
      ];
      $modeloBitacora->registrarBitacora("Materias Primas", "Registrar", "Exito");
      $this->commit();
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Materia prima no registrada",
        "texto" => "Error al registrar la materia prima",
        "icono" => "error",
      ];
      $modeloBitacora->registrarBitacora("Materias Primas", "Registrar", "Fallido");
    }
    return $alerta;
  }
  private function actualizarMateriasPrimasP()
  {
    $MAT = 0;
    $PRE = 0;

    $objBitacora = new bitacoraModelo();
    $error = function ($objBi) {
      $this->rollback();
      $objBi->registrarBitacora("Materias Primas", "Registrar", "Fallido", true);
    };

    $dataActual = $this->seleccionarMateriasPrimas([
      'id_materia_prima' => $this->idMateriaPrima
    ]);

    $resultado = $this->actualizarDatos2([
      'tabla' => 'materias_primas',
      'datos' => [
        "id_unidad_medida" => $this->idUnidadMedida,
        "nombre_materia_prima" => $this->nombreMateriaPrima,
        "stock_materia_prima" => $this->stockMateriaPrima,
        "stock_minimo_materia_prima" => $this->stockMinimoMateriaPrima,
        "precio_materia_prima" => $this->precioMateriaPrima,
      ],
      'WHERE' => [
        'id_materia_prima' => $this->idMateriaPrima
      ]
    ]);
    if ($resultado != false && $resultado > 0) $MAT++;

    if ($dataActual['presentaciones'] != []) {
      $resultado = $this->eliminarDatos2([
        'tabla' => "presentaciones_materias_primas",
        'WHERE' => ["id_materia_prima" => $this->idMateriaPrima],
        'fisico' => true
      ]);
      if ($resultado <= 0) {
        $error($objBitacora);
        return [
          'tipo' => 'simple',
          'titulo' => 'Presentaciones no eliminadas',
          'texto' => 'Las presentaciones de la materia prima no han podido ser eliminadas',
          'icono' => 'error',
        ];
      }
      $PRE += $resultado;
    }
    if (!empty($this->presentaciones)) {
      foreach ($this->presentaciones as $idPresentacion) {
        if ($idPresentacion != '') {
          $ultimoId = $this->guardarDatos2([
            'tabla' => 'presentaciones_materias_primas',
            'datos' => [
              'id_materia_prima' => $this->idMateriaPrima,
              "id_presentacion" => $idPresentacion,
            ]
          ]);
          if ($ultimoId == false || $ultimoId < 1) {
            $error($objBitacora);
            return [
              'tipo' => 'simple',
              'titulo' => 'Presentación no registrada',
              'texto' => 'La presentación de la materia prima no ha podido ser registrada',
              'icono' => 'error',
            ];
          }
          $PRE++;
        }
      }
    };

    if ($PRE == 0 && $MAT == 0) {
      $error($objBitacora);
      return [
        'tipo' => 'simple',
        'titulo' => 'Sin cambios',
        'texto' => 'La materia prima no ha sido actualizada',
        'icono' => 'warning',
      ];
    }
    $objBitacora->registrarBitacora("Materias Primas", "Actualizar", "Exito");
    $this->commit();
    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Materia prima actualizada",
      "texto" => "La materia prima ha sido actualizada exitosamente",
      "icono" => "success"
    ];
  }
  private function eliminarMateriasPrimasP()
  {
    $objBitacora = new bitacoraModelo();
    $error = function ($objBi) {
      $this->rollback();
      $objBi->registrarBitacora("Materias Primas", "Eliminar", "Fallido", true);
    };
    $dataActual = $this->seleccionarMateriasPrimas([
      'id_materia_prima' => $this->idMateriaPrima
    ]);

    // Presentaciones
    if ($dataActual['presentaciones'] != []) {
      $resultado = $this->eliminarDatos2([
        'tabla' => "presentaciones_materias_primas",
        'WHERE' => ["id_materia_prima" => $this->idMateriaPrima],
      ]);
      if ($resultado <= 0) {
        $error($objBitacora);
        return [
          'tipo' => 'simple',
          'titulo' => 'Presentaciones no eliminadas',
          'texto' => 'Las presentaciones de la materia prima no han podido ser eliminadas',
          'icono' => 'error',
        ];
      }
    }

    //Registro principal
    $resultado = $this->eliminarDatos2([
      'tabla' => "materias_primas",
      'WHERE' => ["id_materia_prima" => $this->idMateriaPrima],
    ]);
    if ($resultado <= 0) {
      $error($objBitacora);
      return [
        'tipo' => 'simple',
        'titulo' => 'Materia Prima no eliminada',
        'texto' => 'La materia prima no ha podido ser eliminada',
        'icono' => 'error',
      ];
    }

    $objBitacora->registrarBitacora("Materias Primas", "Eliminar", "Exito");
    $this->commit();
    return [
      "tipo" => "simple",
      "titulo" => "Materia prima eliminada",
      "texto" => "La materia prima ha sido eliminada con éxito",
      "icono" => "success"
    ];
  }
}
