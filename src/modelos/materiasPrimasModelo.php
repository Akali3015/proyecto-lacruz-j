<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use PDO;
use PDOException;
use Exception;

class materiasPrimasModelo extends conexion
{
  private $idMateriaPrima;
  private $idUnidadMedida;
  private $nombreMateriaPrima;
  private $costoMateriaPrima;
  private $stockMateriaPrima;

  public function seleccionarMateriasPrimas($id = null)
  {
    $this->idMateriaPrima = $id;

    if ($this->idMateriaPrima != null && $this->idMateriaPrima != "") {
      $campos = [
        [
          "campo_nombre" => 'id_materia_prima',
          "campo_valor" => $this->idMateriaPrima,
          "formulario_nombre" => "id de la materia prima",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => 'materias_primas',
          "debeExistir" => true,
        ]
      ];

      $respuesta = $this->limpiar_Verificar($campos);
      if ($respuesta !== false) {
        return $respuesta;
        exit();
      }
    }
    return $this->seleccionarMateriasPrimasP();
  }
  public function registrarMateriasPrimas($idUnidadMedida, $nombre, $stock, $precio, $presentaciones)
  {
    try {
      $this->idUnidadMedida = $idUnidadMedida;
      $this->nombreMateriaPrima = $nombre;
      $this->costoMateriaPrima = $precio;
      $this->stockMateriaPrima = $stock;

      $campos = [
        [
          "campo_nombre" => "id_unidad_medida",
          "campo_valor" => $this->idUnidadMedida,
          "formulario_nombre" => "unidades de medida",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "unidades_medidas",
          "debeExistir" => true,
        ],
        [
          "campo_nombre" => "nombre_materia_prima",
          "campo_valor" => $this->nombreMateriaPrima,
          "formulario_nombre" => "nombre de la materia prima",
          "requerido" => true,
          "minimo" => minRegexNombreObj,
          "maximo" => maxRegexNombreObj,
          "expresion_re" => regexNombreObj,
          "tabla" => "materias_primas",
          "debeSerUnico" => true,
        ],
        [
          "campo_nombre" => "precio_materia_prima",
          "campo_valor" => $this->costoMateriaPrima,
          "formulario_nombre" => "precio de la materia prima",
          "requerido" => true,
          "minimo" => minRegexPrecio,
          "maximo" => maxRegexPrecio,
          "expresion_re" => regexPrecio,
          "tabla" => "materias_primas",
        ],
        [
          "campo_nombre" => "stock_materia_prima",
          "campo_valor" => $this->stockMateriaPrima,
          "formulario_nombre" => "stock de la materia prima",
          "requerido" => true,
          "minimo" => minRegexCantidadItem,
          "maximo" => maxRegexCantidadItem,
          "expresion_re" => regexCantidadItem,
          "tabla" => "materias_primas",
        ],
      ];

      if (!empty($presentaciones)) {
        foreach ($presentaciones as $idPresentacion) {
          if ($idPresentacion != '') {
            $campos[] = [
              "campo_nombre" => "id_presentacion",
              "campo_valor" => $idPresentacion,
              "formulario_nombre" => "presentación",
              "requerido" => false,
              "minimo" => minRegexId,
              "maximo" => maxRegexId,
              "expresion_re" => regexId,
              "tabla" => "presentaciones",
              "debeExistir" => true,
            ];
          }
        }
      }

      $respuesta = $this->limpiar_Verificar($campos);
      if ($respuesta !== false) {
        return $respuesta;
        exit();
      } else {
        return $this->registrarMateriasPrimasP($presentaciones);
      }
    } catch (PDOException $e) {
      error_log("Error en MateriasPrimas->registrar(): " . $e->getMessage());
      throw new Exception("Error al registrar la materia prima en la base de datos: " . $e->getMessage());
    }
  }
  public function actualizarMateriasPrimas($id, $idUnidadMedida, $nombre, $stock, $precio, $presentaciones)
  {
    $this->idMateriaPrima = $id;
    $this->idUnidadMedida = $idUnidadMedida;
    $this->nombreMateriaPrima = $nombre;
    $this->costoMateriaPrima = $precio;
    $this->stockMateriaPrima = $stock;

    $campos = [
      [
        "campo_nombre" => "id_materia_prima",
        "campo_valor" => $this->idMateriaPrima,
        "formulario_nombre" => "id de la materia prima",
        "requerido" => true,
        "minimo" => minRegexId,
        "maximo" => maxRegexId,
        "expresion_re" => regexId,
        "tabla" => "materias_primas",
        "debeExistir" => true,
      ],
      [
        "campo_nombre" => "id_unidad_medida",
        "campo_valor" => $this->idUnidadMedida,
        "formulario_nombre" => "id de la unidad de medida",
        "requerido" => true,
        "minimo" => minRegexId,
        "maximo" => maxRegexId,
        "expresion_re" => regexId,
        "tabla" => "unidades_medidas",
        "debeExistir" => true,
      ],
      [
        "campo_nombre" => "nombre_materia_prima",
        "campo_valor" => $this->nombreMateriaPrima,
        "formulario_nombre" => "nombre de la materia prima",
        "requerido" => true,
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj,
        "tabla" => "materias_primas",
      ],
      [
        "campo_nombre" => "precio_materia_prima",
        "campo_valor" => $this->costoMateriaPrima,
        "formulario_nombre" => "precio de la materia prima",
        "requerido" => true,
        "minimo" => minRegexPrecio,
        "maximo" => maxRegexPrecio,
        "expresion_re" => regexPrecio,
        "tabla" => "materias_primas",
      ],
      [
        "campo_nombre" => "stock_materia_prima",
        "campo_valor" => $this->stockMateriaPrima,
        "formulario_nombre" => "stock de la materia prima",
        "requerido" => true,
        "minimo" => minRegexCantidadItem,
        "maximo" => maxRegexCantidadItem,
        "expresion_re" => regexCantidadItem,
        "tabla" => "materias_primas",
      ],
    ];

    if (!empty($presentaciones)) {
      foreach ($presentaciones as $idPresentacion) {
        if ($idPresentacion != '') {
          $campos[] = [
            "campo_nombre" => "id_presentacion",
            "campo_valor" => $idPresentacion,
            "formulario_nombre" => "presentación",
            "requerido" => false,
            "minimo" => minRegexId,
            "maximo" => maxRegexId,
            "expresion_re" => regexId,
            "tabla" => "presentaciones",
            "debeExistir" => true,
          ];
        }
      }
    }

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
      exit();
    } else {
      return $this->actualizarMateriasPrimasP($presentaciones);
    }
  }
  public function eliminarMateriasPrimas($id)
  {
    $this->idMateriaPrima = $id;

    $campos = [
      [
        "campo_nombre" => "id_materia_prima",
        "campo_valor" => $this->idMateriaPrima,
        "formulario_nombre" => "id",
        "requerido" => true,
        "minimo" => minRegexId,
        "maximo" => maxRegexId,
        "expresion_re" => regexId,
        "debeExistir" => true,
        "tabla" => "materias_primas",
      ]
    ];

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
      exit();
    } else {
      return $this->eliminarMateriasPrimasP();
    }
  }
  public function obtenerPresentacionesMateriasPrimas($idMateriaPrima)
  {
    $this->idMateriaPrima = $idMateriaPrima;

    $campos = [
      [
        "campo_nombre" => 'id_materia_prima',
        "campo_valor" => $this->idMateriaPrima,
        "formulario_nombre" => "id de la materia prima",
        "requerido" => true,
        "minimo" => minRegexId,
        "maximo" => maxRegexId,
        "expresion_re" => regexId,
        "tabla" => 'materias_primas',
        "debeExistir" => true,
      ]
    ];

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
    }

    return $this->obtenerPresentacionesMateriasPrimasP();
  }

  private function seleccionarMateriasPrimasP()
  {
    if ($this->idMateriaPrima == null || $this->idMateriaPrima == "") {
      $instruccionesBD = [
        'campos' => '
          mp.id_materia_prima, mp.nombre_materia_prima,
          um.nombre_unidad_medida, mp.stock_materia_prima, mp.precio_materia_prima
        ',
        'tabla' => 'materias_primas as mp',
        'PEL' => 'mp',
        'datosJoins' => [
          [
            "tablaDestino" => "unidades_medidas as um",
            "conexionLo" => "mp.id_unidad_medida = um.id_unidad_medida",
          ]
        ]
      ];

      $resultado = $this->seleccionarDatos($instruccionesBD);
      $MateriasPrimas = $resultado->fetchAll(PDO::FETCH_ASSOC);

      return $MateriasPrimas;
    } else {
      $instruccionesBD = [
        'campos' => '*',
        'tabla' => 'materias_primas',
        'WHERE' => [
          [
            "condicion_campo" => "id_materia_prima",
            "condicion_marcador" => ":id",
            "condicion_valor" => $this->idMateriaPrima,
            "comparacion" => "=",
          ]
        ]
      ];

      $resultado = $this->seleccionarDatos($instruccionesBD);

      if ($resultado->rowCount() <= 0) {
        $alerta = [
          "tipo" => "simple",
          "titulo" => "Materia prima no encontrada",
          "texto" => "La materia prima que ha intentado actualizar no se encuentra en la base de datos",
          "icono" => "error"
        ];
        return $alerta;
        exit();
      } else {
        $materiaPrima = $resultado->fetch(PDO::FETCH_ASSOC);
      }
      return $materiaPrima;
    }
  }
  private function registrarMateriasPrimasP($presentaciones)
  {
    $datos_registro_materias_primas = [
      [
        "campo_nombre" => "id_unidad_medida",
        "campo_marcador" => ":unidadMedida",
        "campo_valor" => $this->idUnidadMedida,
      ],
      [
        "campo_nombre" => "nombre_materia_prima",
        "campo_marcador" => ":nombre",
        "campo_valor" => $this->nombreMateriaPrima,
        "ponerEnMayusculas" => true,
      ],
      [
        "campo_nombre" => "stock_materia_prima",
        "campo_marcador" => ":stock",
        "campo_valor" => $this->stockMateriaPrima,
      ],
      [
        "campo_nombre" => "precio_materia_prima",
        "campo_marcador" => ":precio",
        "campo_valor" => $this->costoMateriaPrima,
      ],
    ];
    $ultimoId = $this->guardarDatos('materias_primas', $datos_registro_materias_primas);
    if (!empty($presentaciones)) {
      foreach ($presentaciones as $idPresentacion) {
        if ($idPresentacion != '') {
          $datos_presentacion = [
            [
              "campo_nombre" => "id_materia_prima",
              "campo_marcador" => ":id_materia_prima",
              "campo_valor" => $ultimoId,
            ],
            [
              "campo_nombre" => "id_presentacion",
              "campo_marcador" => ":id_presentacion",
              "campo_valor" => $idPresentacion,
            ],
          ];

          $this->guardarDatos('materias_primas_presentaciones', $datos_presentacion);
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
  private function actualizarMateriasPrimasP($presentaciones)
  {
    $instruccionesBD = [
      "tabla" => "materias_primas",
      "datos" => [
        [
          "campo_nombre" => "id_materia_prima",
          "campo_marcador" => ":id",
          "campo_valor" => $this->idMateriaPrima,
          "debeExistir" => true,
        ],
        [
          "campo_nombre" => "id_unidad_medida",
          "campo_marcador" => ":unidadMedida",
          "campo_valor" => $this->idUnidadMedida,
        ],
        [
          "campo_nombre" => "nombre_materia_prima",
          "campo_marcador" => ":nombre",
          "campo_valor" => $this->nombreMateriaPrima,
          "ponerEnMayusculas" => true,
        ],
        [
          "campo_nombre" => "stock_materia_prima",
          "campo_marcador" => ":stock",
          "campo_valor" => $this->stockMateriaPrima,
        ],
        [
          "campo_nombre" => "precio_materia_prima",
          "campo_marcador" => ":precio",
          "campo_valor" => $this->costoMateriaPrima,
        ],
      ],
      "WHERE" => [
        [
          "condicion_campo" => "id_materia_prima",
          "condicion_marcador" => ":id",
          "condicion_valor" => $this->idMateriaPrima,
          "comparacion" => "=",
        ]
      ]
    ];

    $eliminarPresentaciones = $this->eliminarDatos(
      "materias_primas_presentaciones",
      "id_materia_prima",
      $this->idMateriaPrima
    );

    if (!empty($presentaciones)) {
      foreach ($presentaciones as $idPresentacion) {
        if ($idPresentacion != '') {
          $datos_presentacion = [
            [
              "campo_nombre" => "id_materia_prima",
              "campo_marcador" => ":id_materia_prima",
              "campo_valor" => $this->idMateriaPrima,
            ],
            [
              "campo_nombre" => "id_presentacion",
              "campo_marcador" => ":id_presentacion",
              "campo_valor" => $idPresentacion,
            ],
          ];

          $this->guardarDatos('materias_primas_presentaciones', $datos_presentacion);
        }
      }
    }

    $resultado = $this->actualizarDatos($instruccionesBD);
    $modeloBitacora = new bitacoraModelo();
    if ($resultado == false && $resultado > 0) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Sin cambios realizados",
        "texto" => "No se han realizado cambios en el registro",
        "icono" => "warning",
      ];
      $modeloBitacora->registrarBitacora("Materias Primas", "Actualizar", "Fallido");
    } else {
      $alerta = [
        "tipo" => "limpiarYcerrar",
        "titulo" => "Materia prima actualizada",
        "texto" => "La materia prima ha sido actualizada exitosamente",
        "icono" => "success",
      ];
      $modeloBitacora->registrarBitacora("Materias Primas", "Actualizar", "Exito");
      $this->commit();
    }
    return $alerta;
  }
  private function eliminarMateriasPrimasP()
  {

    $eliminarMateriaPrima = $this->eliminarDatos("materias_primas", "id_materia_prima", $this->idMateriaPrima);
    $modeloBitacora = new bitacoraModelo();

    if ($eliminarMateriaPrima->rowCount() == 1) {
      $this->commit();
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Materia prima eliminada",
        "texto" => "La materia prima ha sido eliminada con éxito",
        "icono" => "success"
      ];
      $modeloBitacora->registrarBitacora("Materias Primas", "Eliminar", "Exito");
      $this->commit();
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Materia prima no encontrada",
        "texto" => "La materia prima no existe en la Base de Datos",
        "icono" => "error"
      ];
      $modeloBitacora->registrarBitacora("Materias Primas", "Eliminar", "Fallido");
    }
    return $alerta;
  }
  private function obtenerPresentacionesMateriasPrimasP()
  {
    $instruccionesBD = [
      'campos' => 'mp.id_presentacion, p.nombre_presentacion',
      'tabla' => 'materias_primas_presentaciones as mp',
      'PEL' => 'mp',
      'datosJoins' => [
        [
          "tablaDestino" => "presentaciones as p",
          "conexionLo" => "mp.id_presentacion = p.id_presentacion",
        ]
      ],
      'WHERE' => [
        [
          "condicion_campo" => "mp.id_materia_prima",
          "condicion_marcador" => ":id",
          "condicion_valor" => $this->idMateriaPrima,
          "comparacion" => "=",
        ]
      ]
    ];

    $resultado = $this->seleccionarDatos($instruccionesBD);
    return $resultado->fetchAll(PDO::FETCH_ASSOC);
  }
}
