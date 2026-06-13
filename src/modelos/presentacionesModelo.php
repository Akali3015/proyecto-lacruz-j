<?php

namespace src\modelos;

use src\config\connect\conexion;
use PDO;

class presentacionesModelo extends conexion {
  private string $idPresentacion = '';
  private string  $idUnidadMedida = '';
  private string  $nombrePresentacion = '';
  private float $cantidadPMP = 0;

  public function validarPresentaciones(array $instruccionesVal) {
    [
      'infoVal' => &$infoVal,
      'camposVal' => &$camposVal,
    ] = $instruccionesVal;
    $funcionAsignadora = function ($nombreCampo, &$valor) {
      $claveVal = [
        'id_presentacion' => [
          "campo_nombre" => "id_presentacion",
          "campo_valor" =>  &$valor,
          "formulario_nombre" => "id de la presentación",
          "requerido" => true,
          "minimo" => minRegexIdSeguro,
          "maximo" => maxRegexIdSeguro,
          "expresion_re" => regexIdSeguro,
          "tabla" => "presentaciones",
          "debeExistir" => true,
        ],
        'id_unidad_medida' => [
          "campo_nombre" => "id_unidad_medida",
          "campo_valor" =>  &$valor,
          "formulario_nombre" => "id de la unidad de medida",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "unidades_medidas",
          "debeExistir" => true,
        ],
        'nombre_presentacion' => [
          "campo_nombre" => "nombre_presentacion",
          "campo_valor" =>  &$valor,
          "formulario_nombre" => "nombre de la presentación",
          "requerido" => true,
          "minimo" => minRegexNombreObj,
          "maximo" => maxRegexNombreObj,
          "expresion_re" => regexNombreObj,
          "tabla" => "presentaciones",
          "debeSerUnico" => true,
        ],
        'cantidad_pmp' => [
          "campo_valor" =>  &$valor,
          "formulario_nombre" => "cantidad del producto o materia prima",
          "requerido" => true,
          "minimo" => minRegexPrecio,
          "maximo" => maxRegexPrecio,
          "expresion_re" => regexPrecio,
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
  public function seleccionarPresentaciones(array $info) {
    if (($info['id_presentacion'] ?? '') != '') {
      $resultado = $this->validarPresentaciones([
        'infoVal' => &$info,
        'camposVal' => [
          'id_presentacion',
        ],
      ]);
      if ($resultado) return $resultado;
      $this->idPresentacion = $info['id_presentacion'];
    }
    return $this->seleccionarPresentacionesP($info);
  }
  public function registrarPresentaciones(array $info) {
    $resultado = $this->validarPresentaciones([
      'infoVal' => &$info,
      'camposVal' => [
        'id_unidad_medida',
        'nombre_presentacion',
        'cantidad_pmp',
      ],
    ]);
    if ($resultado) return $resultado;

    $this->idUnidadMedida = $info['id_unidad_medida'];
    $this->nombrePresentacion = $info['nombre_presentacion'];
    $this->cantidadPMP = $info['cantidad_pmp'];

    return $this->registrarPresentacionesP();
  }
  public function actualizarPresentaciones(array $info) {
    $resultado = $this->validarPresentaciones([
      'infoVal' => &$info,
      'camposVal' => [
        'id_presentacion',
        'id_unidad_medida',
        'nombre_presentacion',
        'cantidad_pmp',
      ],
    ]);
    if ($resultado) return $resultado;

    $this->idPresentacion = $info['id_presentacion'];
    $this->idUnidadMedida = $info['id_unidad_medida'];
    $this->nombrePresentacion = $info['nombre_presentacion'];
    $this->cantidadPMP = $info['cantidad_pmp'];

    return $this->actualizarPresentacionesP();
  }
  public function eliminarPresentaciones(array $info) {
    $resultado = $this->validarPresentaciones([
      'infoVal' => &$info,
      'camposVal' => [
        'id_presentacion',
      ],
    ]);
    if ($resultado) return $resultado;
    $this->idPresentacion = $info['id_presentacion'];
    return $this->eliminarPresentacionesP();
  }

  //-- PRIVADOS [ ENCAPSULAMIENTO ]--//
  private function seleccionarPresentacionesP(array $info) {
    if ($this->idPresentacion == null || $this->idPresentacion == "") {
      switch ($info['tipoConsulta'] ?? '') {
        case 'indexadosPorId':
          return $this->seleccionarDatos2([
            'campos' => '
              pr.id_presentacion, pr.nombre_presentacion, pr.cantidad_pmp, 
              um.nombre_unidad_medida
            ',
            'tabla' => 'presentaciones as pr',
            'datosJoins' => [
              'unidades_medidas as um' => 'pr.id_unidad_medida = um.id_unidad_medida',
            ]
          ])->fetchAll(PDO::FETCH_UNIQUE);
        default:
          return $this->seleccionarDatos2([
            'campos' => '
              pr.id_presentacion, pr.nombre_presentacion, pr.cantidad_pmp, 
              um.nombre_unidad_medida
            ',
            'tabla' => 'presentaciones as pr',
            'PEL' => 'pr',
            'datosJoins' => [
              'unidades_medidas as um' => 'pr.id_unidad_medida = um.id_unidad_medida',
            ]
          ])->fetchAll();
      }
    } else {
      $resultado = $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'presentaciones',
        'WHERE' => [
          "id_presentacion" => $this->idPresentacion,
        ]
      ]);
      if ($resultado->rowCount() <= 0) {
        return [
          "tipo" => "simple",
          "titulo" => "Presentacion no encontrada",
          "texto" => "La presentación que ha intentado buscar no se encuentra en la base de datos",
          "icono" => "error"
        ];
      }
      return $resultado->fetch();
    }
  }
  private function registrarPresentacionesP() {
    $ultimoId = $this->guardarDatos2([
      'tabla' => 'presentaciones',
      'datos' => [
        "id_presentacion" => $this->generarCodSeg([
          'tablaBD' => 'presentaciones',
          'prefijo' => 'PRES',
          'campoID' => 'id_presentacion'
        ]),
        "id_unidad_medida" => $this->idUnidadMedida,
        "nombre_presentacion" => $this->nombrePresentacion,
        "cantidad_pmp" => $this->cantidadPMP,
      ],
    ]);
    if ($ultimoId !== false && $ultimoId > 0) {
      $alerta = [
        "tipo" => "limpiarYcerrar",
        "titulo" => "Presentación registrada",
        "texto" => "La presentación ha sido registrada exitosamente",
        "icono" => "success",
      ];
      $this->commit();
    } else {
      $this->rollback();
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Presentación no registrada",
        "texto" => "La presentación no ha sido registrada exitosamente",
        "icono" => "error",
      ];
    }
    return $alerta;
  }
  private function actualizarPresentacionesP() {
    $resultado = $this->actualizarDatos2([
      "tabla" => "presentaciones",
      "datos" => [
        "id_unidad_medida" => $this->idUnidadMedida,
        "nombre_presentacion" => $this->nombrePresentacion,
        "cantidad_pmp" => $this->cantidadPMP,
      ],
      "WHERE" => [
        "id_presentacion" => $this->idPresentacion,
      ]
    ]);

    if ($resultado == false || $resultado <= 0) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Sin cambios realizados",
        "texto" => "No se realizó ningún cambio en la presentación",
        "icono" => "warning",
      ];
      $this->rollback();
    } else {
      $alerta = [
        "tipo" => "limpiarYcerrar",
        "titulo" => "Presentación actualizada",
        "texto" => "La presentación ha sido actualizada exitosamente",
        "icono" => "success",
      ];
      $this->commit();
    }
    return $alerta;
  }
  private function eliminarPresentacionesP() {
    $resultado = $this->eliminarDatos2([
      'tabla' => "presentaciones",
      'WHERE' => [
        "id_presentacion" => $this->idPresentacion
      ]
    ]);
    if ($resultado == 1) { /*Para verificar si se hizo la eliminación o no */
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Presentación eliminada",
        "texto" => "La presentación ha sido eliminada con éxito",
        "icono" => "success"
      ];
      $this->commit();
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Presentación no encontrada",
        "texto" => "La presentación no existe en la Base de Datos",
        "icono" => "error"
      ];
    }
    return $alerta;
  }
}
