<?php

namespace src\modelos;

use src\config\connect\conexion;
use PDO;

class bancosModelo extends conexion {
  private int $idBanco = 0;
  private string $nombreBanco = '';

  public function validarBancos(array $instruccionesVal) {
    [
      'infoVal' => &$infoVal,
      'camposVal' => $camposVal,
    ] = $instruccionesVal;

    $funcionAsignadora = function ($nombreCampo, &$valor) {
      $claveVal = [
        'id_banco' => [
          "campo_nombre" => "id_banco",
          "campo_valor" => &$valor,
          "formulario_nombre" => "id del banco",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "bancos",
          "debeSerUnico" => true,
          "debeExistir" => true,
        ],
        'nombre_banco' => [
          "campo_nombre" => "nombre_banco",
          "campo_valor" => &$valor,
          "formulario_nombre" => "nombre del banco",
          "requerido" => true,
          "minimo" => minRegexNombreObj,
          "maximo" => maxRegexNombreObj,
          "expresion_re" => regexNombreObj,
          "tabla" => "bancos",
          "debeSerUnico" => true,
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
  public function seleccionarBancos(array $info) {
    if (($info['id_banco'] ?? '') != "") {
      $resultado = $this->validarBancos([
        'infoVal' => &$info,
        'camposVal' => ['id_banco'],
      ]);
      if ($resultado) return $resultado;
      $this->idBanco = $info['id_banco'];
    }
    return $this->seleccionarBancosP($info);
  }
  public function registrarBancos(array $info) {
    $resultado = $this->validarBancos([
      'infoVal' => &$info,
      'camposVal' => ['nombre_banco'],
    ]);
    if ($resultado) return $resultado;

    $this->nombreBanco = $info['nombre_banco'];
    return $this->registrarBancosP();
  }
  public function actualizarBancos(array $info) {
    $resultado = $this->validarBancos([
      'infoVal' => &$info,
      'camposVal' => ['id_banco', 'nombre_banco'],
    ]);

    if ($resultado) return $resultado;
    $this->idBanco = $info['id_banco'];
    $this->nombreBanco = $info['nombre_banco'];
    return $this->actualizarBancosP();
  }
  public function eliminarBancos(array $info) {
    $resultado = $this->validarBancos([
      'infoVal' => &$info,
      'camposVal' => ['id_banco'],
    ]);
    if ($resultado) return $resultado;
    $this->idBanco = $info['id_banco'];
    return $this->eliminarBancosP();
  }

  private function seleccionarBancosP(array $info) {
    if ($this->idBanco != '' && $this->idBanco != 0) {
      return $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'bancos',
        'WHERE' => [
          'id_banco' => $this->idBanco
        ]
      ])->fetch();
    } else {
      switch (($info['tipoConsulta'] ?? '')) {
        case 'indexadosPorId':
          return $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'bancos',
          ])->fetchAll(PDO::FETCH_UNIQUE);
        default:
          return $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'bancos',
          ])->fetchAll();
      }
    }
  }
  private function registrarBancosP() {
    $resultado = $this->guardarDatos2([
      'tabla' => 'bancos',
      'datos' =>  [
        "nombre_banco" => $this->nombreBanco
      ]
    ]);
    if ($resultado <= 0) {
      $this->rollback();
      return [
        "tipo" => "simple",
        "titulo" => "Error",
        "texto" => "No se pudo registrar",
        "icono" => "error"
      ];
    }
    $this->commit();
    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Registro Exitoso",
      "texto" => "El banco ha sido registrado",
      "icono" => "success"
    ];
  }
  private function actualizarBancosP() {
    $resultado = $this->actualizarDatos2([
      'tabla' => 'bancos',
      'datos' => [
        "nombre_banco" => $this->nombreBanco
      ],
      'WHERE' => [
        "id_banco" => $this->idBanco
      ]
    ]);
    if ($resultado > 0) {
      $this->commit();
      return [
        "tipo" => "limpiarYcerrar",
        "titulo" => "Actualización Exitosa",
        "texto" => "El banco ha sido actualizado",
        "icono" => "success"
      ];
    }
    $this->rollback();
    return [
      "tipo" => "simple",
      "titulo" => "Error",
      "texto" => "No hubo cambios",
      "icono" => "info"
    ];
  }
  private function eliminarBancosP() {
    $resultado = $this->eliminarDatos2([
      'tabla' => 'bancos',
      'WHERE' => [
        "id_banco" => $this->idBanco
      ]
    ]);
    if ($resultado <= 0) {
      $this->rollback();
      return [
        "tipo" => "simple",
        "titulo" => "Eliminación Fallida",
        "texto" => "El banco no pudo ser eliminado",
        "icono" => "error"
      ];
    }
    $this->commit();
    return [
      "tipo" => "simple",
      "titulo" => "Banco eliminado",
      "texto" => "El banco ha sido eliminado exitosamente",
      "icono" => "success"
    ];
  }
}
