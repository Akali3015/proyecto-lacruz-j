<?php

namespace src\modelos;

use src\config\connect\conexion;
use PDO;

class bancosModelo extends conexion
{
  private $idBanco;
  private $nombreBanco;

  public function seleccionarBancos($id = null)
  {
    $this->idBanco = $id;
    if ($this->idBanco != null && $this->idBanco != "") {
      $campos = [
        [
          "campo_nombre" => 'id_banco',
          "campo_valor" => $this->idBanco,
          "formulario_nombre" => "ID del banco",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => 'bancos',
          "debeExistir" => true
        ]
      ];
      $respuesta = $this->limpiar_Verificar($campos);
      if ($respuesta !== false) {
        return $respuesta;
      }
    }
    return $this->seleccionarBancosP();
  }
  public function registrarBancos($nombre)
  {
    $this->nombreBanco = $nombre;

    $campos = [
      [
        "campo_nombre" => "nombre_banco",
        "campo_valor" => $this->nombreBanco,
        "formulario_nombre" => "nombre del banco",
        "requerido" => true,
        "minimo" => 3,
        "maximo" => 50,
        "expresion_re" => regexNombreObj,
        "tabla" => "bancos",
        "debeSerUnico" => true,
      ]
    ];

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
    }
    return $this->registrarBancosP();
  }
  public function actualizarBancos($id, $nombre)
  {
    $this->idBanco = $id;
    $this->nombreBanco = $nombre;

    $campos = [
      ["campo_nombre" => "id_banco", "campo_valor" => $this->idBanco, "formulario_nombre" => "ID del banco", "requerido" => true, "debeExistir" => true, "tabla" => "bancos"],
      ["campo_nombre" => "nombre_banco", "campo_valor" => $this->nombreBanco, "formulario_nombre" => "nombre del banco", "requerido" => true, "minimo" => 3, "maximo" => 50, "expresion_re" => regexNombreObj, "tabla" => "bancos", "debeSerUnico" => true]
    ];
    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
    }
    return $this->actualizarBancosP();
  }
  public function eliminarBancos($id)
  {
    $this->idBanco = $id;
    $campos = [
      ["campo_nombre" => "id_banco", "campo_valor" => $this->idBanco, "formulario_nombre" => "ID del banco", "requerido" => true, "debeExistir" => true, "tabla" => "bancos"]
    ];
    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
    }
    return $this->eliminarBancosP();
  }
  private function seleccionarBancosP()
  {
    $instrucciones = [
      'campos' => '*',
      'tabla' => 'bancos',
      'ORDER' => 'nombre_banco ASC'
    ];
    if ($this->idBanco != null) {
      $instrucciones['WHERE'] = ["id_banco" => $this->idBanco];
      return $this->seleccionarDatos2($instrucciones)->fetch(PDO::FETCH_ASSOC);
    }
    return $this->seleccionarDatos2($instrucciones)->fetchAll(PDO::FETCH_ASSOC);
  }
  private function registrarBancosP()
  {
    $datos = ["nombre_banco" => $this->nombreBanco, "status" => 1];
    $resultado = $this->guardarDatos2(['tabla' => 'bancos', 'datos' => $datos]);
    if ($resultado > 0) {
      $this->commit();
      return ["tipo" => "limpiarYcerrar", "titulo" => "Registro Exitoso", "texto" => "El banco ha sido registrado", "icono" => "success"];
    }
    $this->rollback();
    return ["tipo" => "simple", "titulo" => "Error", "texto" => "No se pudo registrar", "icono" => "error"];
  }
  private function actualizarBancosP()
  {
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
    return ["tipo" => "simple", "titulo" => "Error", "texto" => "No hubo cambios", "icono" => "info"];
  }
  private function eliminarBancosP()
  {
    $resultado = $this->eliminarDatos2(['tabla' => 'bancos', 'WHERE' => ["id_banco" => $this->idBanco]]);
    $this->commit();
    return ["tipo" => "simple", "titulo" => "Eliminado", "texto" => "El banco ha sido desactivado", "icono" => "success"];
}
}
