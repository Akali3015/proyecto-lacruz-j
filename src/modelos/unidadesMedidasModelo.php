<?php

namespace src\modelos;

use src\config\connect\conexion;
use PDO;

class unidadesMedidasModelo extends conexion {

  private int $idUnidadMedida = 0;
  private string $nombreUnidadMedida = '';
  private string $simboloUnidadMedida = '';
  private float $equivalenciaUB = 0;

  public function validarUnidadesMedidas(array $instruccionesVal) {
    [
      'infoVal' => &$infoVal,
      'camposVal' => &$camposVal,
    ] = $instruccionesVal;
    $funcionAsignadora = function ($nombreCampo, &$valor) {
      $claveVal = [
        "id_unidad_medida" => [
          "campo_nombre" => "id_unidad_medida",
          "campo_valor" => &$valor,
          "formulario_nombre" => "id de la unidad de medida",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "unidades_medidas",
          "debeExistir" => true,
          "debeSerUnico" => true
        ],
        "nombre_unidad_medida" => [
          "campo_nombre" => "nombre_unidad_medida",
          "campo_valor" => &$valor,
          "formulario_nombre" => "nombre de la unidad de medida",
          "requerido" => true,
          "minimo" => minRegexNombreObj,
          "maximo" => maxRegexNombreObj,
          "expresion_re" => regexNombreObj,
          "tabla" => "unidades_medidas",
          "debeSerUnico" => true
        ],
        "simbolo_unidad_medida" => [
          "campo_nombre" => "simbolo_unidad_medida",
          "campo_valor" => &$valor,
          "formulario_nombre" => "simbolo de la unidad de medida",
          "requerido" => true,
          "minimo" => minRegexSimboloMoneda,
          "maximo" => maxRegexSimboloMoneda,
          "expresion_re" => regexSimboloMoneda,
          "tabla" => "unidades_medidas",
          "debeSerUnico" => true,
        ],
        "equivalencia_ub" => [
          "campo_nombre" => "equivalencia_ub",
          "campo_valor" => &$valor,
          "formulario_nombre" => "equivalencia de la unidad base",
          "requerido" => true,
          "minimo" => minRegexPrecio,
          "maximo" => maxRegexPrecio,
          "expresion_re" => regexPrecio,
          "tabla" => "unidades_medidas",
        ],
      ];
      return $claveVal[$nombreCampo];
    };
    $campos = [];
    foreach ($camposVal as $campo) {
      $campos[] = $funcionAsignadora($campo, $infoVal[$campo]);
    }
    return $this->limpiar_Verificar($campos);
  }
  public function seleccionarUnidadesMedidas(array $info) {
    if (($info['id_unidad_medida'] ?? '') != '') {
      $resultado = $this->validarUnidadesMedidas([
        'infoVal' => &$info,
        'camposVal' => [
          'id_unidad_medida',
        ],
      ]);
      if ($resultado) return $resultado;
      $this->idUnidadMedida = $info['id_unidad_medida'];
    }
    return $this->seleccionarUnidadesMedidasP();
  }
  public function registrarUnidadesMedidas(array $info) {
    $resultado = $this->validarUnidadesMedidas([
      'infoVal' => &$info,
      'camposVal' => [
        'nombre_unidad_medida',
        'simbolo_unidad_medida',
        'equivalencia_ub',
      ],
    ]);
    if ($resultado) return $resultado;

    $this->nombreUnidadMedida = $info['nombre_unidad_medida'];
    $this->simboloUnidadMedida = $info['simbolo_unidad_medida'];
    $this->equivalenciaUB = $info['equivalencia_ub'];


    return $this->registrarUnidadesMedidasP();
  }
  public function actualizarUnidadesMedidas(array $info) {
    $resultado = $this->validarUnidadesMedidas([
      'infoVal' => &$info,
      'camposVal' => [
        'id_unidad_medida',
        'nombre_unidad_medida',
        'simbolo_unidad_medida',
        'equivalencia_ub',
      ],
    ]);
    if ($resultado) return $resultado;

    $this->idUnidadMedida = $info['id_unidad_medida'];
    $this->nombreUnidadMedida = $info['nombre_unidad_medida'];
    $this->simboloUnidadMedida = $info['simbolo_unidad_medida'];
    $this->equivalenciaUB = $info['equivalencia_ub'];
    return $this->actualizarUnidadesMedidasP();
  }
  public function eliminarUnidadesMedidas(array $info) {
    $resultado = $this->validarUnidadesMedidas([
      'infoVal' => &$info,
      'camposVal' => [
        'id_unidad_medida',
      ],
    ]);
    if ($resultado) return $resultado;
    $this->idUnidadMedida = $info['id_unidad_medida'];
    return $this->eliminarUnidadesMedidasP();
  }

  //-- PRIVADOS [ ENCAPSULAMIENTO ]--//
  private function seleccionarUnidadesMedidasP() {
    if ($this->idUnidadMedida == null || $this->idUnidadMedida == "") {
      return $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'v_unidades_medidas_todas',
      ])->fetchAll();
    } else {
      return $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'unidades_medidas',
        'WHERE' => [
          "id_unidad_medida" => $this->idUnidadMedida,
        ]
      ])->fetch(PDO::FETCH_ASSOC);
    }
  }
  private function registrarUnidadesMedidasP() {
    $ultimoId = $this->guardarDatos2([
      'tabla' => 'unidades_medidas',
      'datos' => [
        "nombre_unidad_medida" => $this->nombreUnidadMedida,
        "simbolo_unidad_medida" => $this->simboloUnidadMedida,
        "equivalencia_ub" => $this->equivalenciaUB,
      ]
    ]);
    if ($ultimoId == false || $ultimoId <= 0) {
      $this->rollback();
      return [
        "tipo" => "simple",
        "titulo" => "Unidad de medida no registrada",
        "texto" => "El unidad de medida no ha sido registrada exitosamente",
        "icono" => "error",
      ];
    }
    $this->commit();
    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Unidad de medida registrada",
      "texto" => "El Unidad de medida ha sido registrada exitosamente",
      "icono" => "success",
    ];
  }
  private function actualizarUnidadesMedidasP() {
    $resultado = $this->actualizarDatos2([
      "tabla" => "unidades_medidas",
      "datos" => [
        "nombre_unidad_medida" => $this->nombreUnidadMedida,
        "simbolo_unidad_medida" => $this->simboloUnidadMedida,
        "equivalencia_ub" => $this->equivalenciaUB,
      ],
      "WHERE" => [
        "id_unidad_medida" => $this->idUnidadMedida,
      ]
    ]);

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
  private function eliminarUnidadesMedidasP() {
    $eliminarUsuario = $this->eliminarDatos2([
      'tabla' => "unidades_medidas",
      'WHERE' => [
        "id_unidad_medida" => $this->idUnidadMedida
      ]
    ]);
    if ($eliminarUsuario == 1) {
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
