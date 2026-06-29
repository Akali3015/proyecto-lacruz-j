<?php

namespace src\modelos;

use src\config\connect\conexion;
use PDO;
use src\modelos\bitacoraModelo;

class modulosModelo extends conexion {
  private int $idModulo = 0;
  private string $nombreModulo = '';

  public function validarModulos(array $instruccionesVal) {
    [
      'infoVal' => &$infoVal,
      'camposVal' => &$camposVal,
    ] = $instruccionesVal;
    $funcionAsignadora = function ($nombreCampo, &$valor) {
      $claveVal = [
        "id_modulo" => [
          "campo_nombre" => "id_modulo",
          "campo_valor" => &$valor,
          "formulario_nombre" => "id del módulo",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "modulos",
          'BD' => 'seguridad',
          "debeExistir" => true,
          "debeSerUnico" => true
        ],
        "nombre_modulo" => [
          "campo_nombre" => "nombre_modulo",
          "campo_valor" => &$valor,
          "formulario_nombre" => "nombre del módulo",
          "requerido" => true,
          "minimo" => minRegexNombrePer,
          "maximo" => maxRegexNombrePer,
          "expresion_re" => regexNombrePer,
          "tabla" => "modulos",
          'BD' => 'seguridad',
          "debeSerUnico" => true
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
  public function seleccionarModulos($info = NULL) {
    if (($info['id_modulo'] ?? '') != "") {
      $resultado = $this->validarModulos([
        'infoVal' => &$info,
        'camposVal' => [
          'id_modulo',
        ],
      ]);
      if ($resultado) return $resultado;
      $this->idModulo = $info['id_modulo'];
    }
    return $this->seleccionarModulosP();
  }
  public function registrarModulos(array $info) {
    $resultado = $this->validarModulos([
      'infoVal' => &$info,
      'camposVal' => [
        'nombre_modulo',
      ],
    ]);
    if ($resultado) return $resultado;
    $this->nombreModulo = $info['nombre_modulo'];

    return $this->registrarModulosP();
  }
  public function actualizarModulos(array $info) {
    $resultado = $this->validarModulos([
      'infoVal' => &$info,
      'camposVal' => [
        'id_modulo',
        'nombre_modulo',
      ],
    ]);
    if ($resultado) return $resultado;
    $this->idModulo = $info['id_modulo'];
    $this->nombreModulo = $info['nombre_modulo'];

    return $this->actualizarModulosP();
  }
  public function eliminarModulos(array $info) {
    $resultado = $this->validarModulos([
      'infoVal' => &$info,
      'camposVal' => [
        'id_modulo',
      ],
    ]);
    if ($resultado) return $resultado;
    $this->idModulo = $info['id_modulo'];
    return $this->eliminarModulosP();
  }

  //-- PRIVADOS [ ENCAPSULAMIENTO ]--//
  private function seleccionarModulosP() {
    if ($this->idModulo == null || $this->idModulo == "") {
      $resultado = $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'modulos',
        'BD' => 'seguridad',
      ]);
      return $resultado->fetchAll(PDO::FETCH_ASSOC);
    } else {
      $resultado = $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'modulos',
        'BD' => 'seguridad',
        'WHERE' => [
          "id_modulo" => $this->idModulo,
        ]
      ]);
      if ($resultado->rowCount() <= 0) {
        $alerta = [
          "tipo" => "simple",
          "titulo" => "Módulo no encontrado",
          "texto" => "El módulo que ha intentado buscar no se encuentra en la base de datos",
          "icono" => "error"
        ];
        return $alerta;
        exit();
      } else {
        $modulos = $resultado->fetch(PDO::FETCH_ASSOC);
      }
      return $modulos;
    }
  }
  private function registrarModulosP() {
    $objBitacora = new bitacoraModelo();
    $ultimoId = $this->guardarDatos2([
      'tabla' => 'modulos',
      'BD' => 'seguridad',
      'datos' => [
        "nombre_modulo" => $this->nombreModulo,
      ]
    ]);
    if ($ultimoId !== false && $ultimoId > 0) {
      $alerta = [
        "tipo" => "limpiarYcerrar",
        "titulo" => "Módulo registrado",
        "texto" => "El módulo ha sido registrado exitosamente",
        "icono" => "success",
      ];
      $resultadoB = $objBitacora->registrarBitacora('modulos', 'Registrar', 'Éxito');
      $this->commit();
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Módulo no registrado",
        "texto" => "El módulo no ha sido registrado exitosamente",
        "icono" => "error",
      ];
      $resultadoB = $objBitacora->registrarBitacora('modulos', 'Registrar', 'Error');
      $this->rollback();
    }
    if ($resultadoB != false) {
      $this->rollback();
      return $resultadoB;
    }
    return $alerta;
  }
  private function actualizarModulosP() {
    $objBitacora = new bitacoraModelo();
    $resultado = $this->actualizarDatos2([
      'tabla' => 'modulos',
      'BD' => 'seguridad',
      'datos' => [
        "nombre_modulo" => $this->nombreModulo,
      ],
      "WHERE" => [
        "id_modulo" => $this->idModulo,
      ]
    ]);
    if ($resultado == false || $resultado <= 0) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Sin cambios realizados",
        "texto" => "No se realizó ningún cambio en el módulo",
        "icono" => "warning",
      ];
      $resultadoB = $objBitacora->registrarBitacora('modulos', 'actualizar módulo con id: ' . $this->idModulo, 'Error');
    } else {
      $alerta = [
        "tipo" => "limpiarYcerrar",
        "titulo" => "Módulo actualizado",
        "texto" => "El módulo ha sido actualizado exitosamente",
        "icono" => "success",
      ];
      $resultadoB = $objBitacora->registrarBitacora('modulos', 'actualizar módulo con id: ' . $this->idModulo, 'Éxito');
      $this->commit();
    }
    if ($resultadoB != false) {
      $this->rollback();
      return $resultadoB;
    }
    return $alerta;
  }
  private function eliminarModulosP() {
    $objBitacora = new bitacoraModelo();
    $eliminarUsuario = $this->eliminarDatos2([
      "tabla" => "modulos",
      'BD' => 'seguridad',
      "WHERE" => [
        "id_modulo" => $this->idModulo
      ]
    ]);
    if ($eliminarUsuario == 1) { /*Para verificar si se hizo la eliminación o no */
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Módulo eliminado",
        "texto" => "El módulo ha sido eliminado con éxito",
        "icono" => "success"
      ];
      $resultadoB = $objBitacora->registrarBitacora('modulos', 'eliminar módulo con id: ' . $this->idModulo, 'Éxito');
      $this->commit();
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Módulo no encontrado",
        "texto" => "El módulo no existe en la Base de Datos",
        "icono" => "error"
      ];
      $resultadoB = $objBitacora->registrarBitacora('modulos', 'eliminar módulo con id: ' . $this->idModulo, 'Error');
    }
    if ($resultadoB != false) {
      $this->rollback();
      return $resultadoB;
    }

    return $alerta;
  }
}
