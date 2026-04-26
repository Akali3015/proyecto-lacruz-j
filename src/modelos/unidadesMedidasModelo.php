<?php

namespace src\modelos;

use src\config\connect\conexion;
use PDO;
use PDOException;
use Exception;

class unidadesMedidasModelo extends conexion
{

  private $idUnidadMedida;
  private $nombreUnidadMedida;
  private $simboloUnidadMedida;
  private $equivalenciaUB;

  public function seleccionarUnidadesMedidas($id = null)
  {
    $this->idUnidadMedida = $id;

    if ($this->idUnidadMedida != null && $this->idUnidadMedida != "") {
      //Arrays para las validaciones
      $campos = [
        [
          "campo_nombre" => 'id_unidad_medida',
          "campo_valor" => $this->idUnidadMedida,
          "formulario_nombre" => "id de la unidad de medida",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => 'unidades_medidas',
          "debeExistir" => true
        ]
      ];

      $respuesta = $this->limpiar_Verificar($campos);
      if ($respuesta !== false) {
        return $respuesta;
        exit();
      } else {
        return $this->seleccionarUnidadesMedidasP();
      }
    } else {
      return $this->seleccionarUnidadesMedidasP();
    }
  }
  public function registrarUnidadesMedidas($nombre, $simbolo, $equivalenciaUB)
  {

    $this->nombreUnidadMedida = $nombre;
    $this->simboloUnidadMedida = $simbolo;
    $this->equivalenciaUB = $equivalenciaUB;

    $campos = [
      [
        "campo_nombre" => "nombre_unidad_medida",
        "campo_valor" => $this->nombreUnidadMedida,
        "formulario_nombre" => "nombre de la unidad de medida",
        "requerido" => true,
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj,
        "tabla" => "unidades_medidas",
        "debeSerUnico" => true,
      ],
      [ 
        "campo_nombre" => "simbolo_unidad_medida",
        "campo_valor" => $this->simboloUnidadMedida,
        "formulario_nombre" => "simbolo de la unidad de medida",
        "requerido" => true,
        "minimo" => minRegexSimboloMoneda,
        "maximo" => maxRegexSimboloMoneda,
        "expresion_re" => regexSimboloMoneda,
        "tabla" => "unidades_medidas",
        "debeSerUnico" => true,
      ],
      [
        "campo_valor" => $this->equivalenciaUB,
        "formulario_nombre" => "equivalencia de la unidad base",
        "requerido" => true,
        "minimo" => minRegexEnteroGrande,
        "maximo" => maxRegexEnteroGrande,
        "expresion_re" => regexEnteroGrande,
      ],
    ];

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
      exit();
    } else {
      return $this->registrarUnidadesMedidasP();
    }
  }
  public function actualizarUnidadesMedidas($id, $nombre, $simbolo, $equivalenciaUB)
  {
    $this->idUnidadMedida = $id;
    $this->nombreUnidadMedida = $nombre;
    $this->simboloUnidadMedida = $simbolo;
    $this->equivalenciaUB = $equivalenciaUB;
    $this->equivalenciaUB = $equivalenciaUB;

    //Arrays para las validaciones
    $campos = [
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
        "debeSerUnico" => true
      ],
      [
        "campo_nombre" => "nombre_unidad_medida",
        "campo_valor" => $this->nombreUnidadMedida,
        "formulario_nombre" => "nombre de la unidad de medida",
        "requerido" => true,
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj,
        "tabla" => "unidades_medidas",
        "debeSerUnico" => true
      ],
      [
        "campo_nombre" => "simbolo_unidad_medida",
        "campo_valor" => $this->simboloUnidadMedida,
        "formulario_nombre" => "simbolo de la unidad de medida",
        "requerido" => true,
        "minimo" => minRegexSimboloMoneda,
        "maximo" => maxRegexSimboloMoneda,
        "expresion_re" => regexSimboloMoneda,
        "tabla" => "unidades_medidas",
        "debeSerUnico" => true,
      ],
      [
        "campo_nombre" => "equivalencia_ub",
        "campo_valor" => $this->equivalenciaUB,
        "formulario_nombre" => "equivalencia de la unidad base",
        "requerido" => true,
        "minimo" => minRegexPrecio,
        "maximo" => maxRegexPrecio,
        "expresion_re" => regexPrecio,
        "tabla" => "unidades_medidas",
      ],
    ];

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
      exit();
    } else {
      return $this->actualizarUnidadesMedidasP();
    }
  }
  public function eliminarUnidadesMedidas($id)
  {
    $this->idUnidadMedida = $id;

    //Arrays para las validaciones
    $campos = [
      [
        "campo_nombre" => "id_unidad_medida",
        "campo_valor" => $this->idUnidadMedida,
        "formulario_nombre" => "id de la unidad de medida",
        "requerido" => true,
        "minimo" => minRegexId,
        "maximo" => maxRegexId,
        "expresion_re" => regexId,
        "debeExistir" => true,
        "camposDiferentes" => 1,
        "tabla" => "unidades_medidas"
      ]
    ];

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
      exit();
    } else {
      return $this->eliminarUnidadesMedidasP();
    }
  }

  //-- PRIVADOS [ ENCAPSULAMIENTO ]--//
  private function seleccionarUnidadesMedidasP()
  {
    if ($this->idUnidadMedida == null || $this->idUnidadMedida == "") {
      $instruccionesBD = [
        'campos' => '*',
        'tabla' => 'unidades_medidas',
      ];
      $resultado = $this->seleccionarDatos($instruccionesBD);
      $unidadesMedidas = $resultado->fetchAll(PDO::FETCH_ASSOC);
      return $unidadesMedidas;
    } else {

        /*Hacemos la consulta */;
      $instruccionesBD = [
        'campos' => '*',
        'tabla' => 'unidades_medidas',
        'WHERE' => [
          [
            "condicion_campo" => "id_unidad_medida",
            "condicion_marcador" => ":ID",
            "condicion_valor" => $this->idUnidadMedida,
            "comparacion" => "="
          ]
        ]
      ];
      $resultado = $this->seleccionarDatos($instruccionesBD);
      if ($resultado->rowCount() <= 0) {
        $alerta = [
          "tipo" => "simple",
          "titulo" => "Unidad de medida no encontrada",
          "texto" => "La unidad de medida que ha intentado buscar no se encuentra en la base de datos",
          "icono" => "error"
        ];
        return $alerta;
        exit();
      } else {
        $unidadMedida = $resultado->fetch(PDO::FETCH_ASSOC);
      }
      return $unidadMedida;
    }
  }
  private function registrarUnidadesMedidasP()
  {
    $datos_registro_unidades_medidas = [
      [
        "campo_nombre" => "nombre_unidad_medida",
        "campo_marcador" => ":Nombre",
        "campo_valor" => $this->nombreUnidadMedida,
        "ponerEnMayusculas" => true
      ],
      [
        "campo_nombre" => "simbolo_unidad_medida",
        "campo_marcador" => ":simbolo",
        "campo_valor" => $this->simboloUnidadMedida,
        "ponerEnMayusculas" => true
      ],
      [
        "campo_nombre" => "equivalencia_ub",
        "campo_marcador" => ":equivalencia",
        "campo_valor" => $this->equivalenciaUB,
      ],
    ];

    $ultimoId = $this->guardarDatos('unidades_medidas', $datos_registro_unidades_medidas);
    if ($ultimoId !== false && $ultimoId > 0) {
      $alerta = [
        "tipo" => "limpiarYcerrar",
        "titulo" => "Unidad de medida registrada",
        "texto" => "El Unidad de medida ha sido registrada exitosamente",
        "icono" => "success",
      ];
      $this->commit();
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Unidad de medida no registrada",
        "texto" => "El unidad de medida no ha sido registrada exitosamente",
        "icono" => "error",
      ];
    }
    return $alerta;
  }
  private function actualizarUnidadesMedidasP()
  {

    $instruccionesBD = [
      "tabla" => "unidades_medidas",
      "datos" => [
        [
          "campo_nombre" => "nombre_unidad_medida",
          "campo_marcador" => ":Nombre",
          "campo_valor" => $this->nombreUnidadMedida,
          "ponerEnMayusculas" => true
        ],
        [
          "campo_nombre" => "simbolo_unidad_medida",
          "campo_marcador" => ":simbolo",
          "campo_valor" => $this->simboloUnidadMedida,
          "ponerEnMayusculas" => true
        ],
        [
          "campo_nombre" => "equivalencia_ub",
          "campo_marcador" => ":equivalencia",
          "campo_valor" => $this->equivalenciaUB,
        ],
        [
          "campo_nombre" => "equivalencia_ub",
          "campo_marcador" => ":equivalencia",
          "campo_valor" => $this->equivalenciaUB,
        ],
      ],
      "WHERE" => [
        [
          "condicion_campo" => "id_unidad_medida",
          "condicion_marcador" => ":id",
          "condicion_valor" => $this->idUnidadMedida,
          "comparacion" => "="
        ]
      ]
    ];
    $resultado = $this->actualizarDatos($instruccionesBD);

    if ($resultado == false || $resultado <= 0) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Sin cambios realizados",
        "texto" => "No se realizó ningún cambio en la unidad de medida",
        "icono" => "warning",
      ];
    } else {
      $alerta = [
        "tipo" => "limpiarYcerrar",
        "titulo" => "Unidad de medida actualizada",
        "texto" => "La unidad de medida ha sido actualizada exitosamente",
        "icono" => "success",
      ];
      $this->commit();
    }
    return $alerta;
  }
  private function eliminarUnidadesMedidasP()
  {
    $eliminarUsuario = $this->eliminarDatos("unidades_medidas", "id_unidad_medida", $this->idUnidadMedida);
    if ($eliminarUsuario->rowCount() == 1) { /*Para verificar si se hizo la eliminación o no */

      $alerta = [
        "tipo" => "simple",
        "titulo" => "Unidad de medida eliminada",
        "texto" => "La unidad de medida ha sido eliminada con éxito",
        "icono" => "success"
      ];
      $this->commit();
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Unidad de medida no encontrada",
        "texto" => "La unidad de medida no existe en la Base de Datos",
        "icono" => "error"
      ];
    }
    return $alerta;
  }
}
