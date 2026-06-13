<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use PDO;

class permisosModelo extends conexion {
  private int $idPermiso = 0;
  private string $nombrePermiso = '';

  public function validarPermisos(array $instruccionesVal) {
    [
      'infoVal' => &$infoVal,
      'camposVal' => &$camposVal,
    ] = $instruccionesVal;
    $funcionAsignadora = function ($nombreCampo, &$valor) {
      $claveVal = [
        "id_permiso" => [
          "campo_nombre" => "id_permiso",
          "campo_valor" => &$valor,
          "formulario_nombre" => "id del permiso",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "permisos",
          'BD' => 'seguridad',
          "debeSerUnico" => true,
          "debeExistir" => true,
        ],
        "nombre_permiso" => [
          "campo_nombre" => "nombre_permiso",
          "campo_valor" => &$valor,
          "formulario_nombre" => "nombre del permiso",
          "requerido" => true,
          "minimo" => minRegexNombreObj,
          "maximo" => maxRegexNombreObj,
          "expresion_re" => regexNombreObj,
          "tabla" => "permisos",
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
  public function seleccionarPermisos(array $info) {
    if (($info['id_permiso'] ?? '') != '') {
      $resultado = $this->validarPermisos([
        'infoVal' => &$info,
        'camposVal' => [
          'id_permiso',
        ],
      ]);
      if ($resultado) return $resultado;
      $this->idPermiso = $info['id_permiso'];
    }
    return $this->seleccionarPermisosP();
  }
  public function registrarPermisos(array $info) {
    $resultado = $this->validarPermisos([
      'infoVal' => &$info,
      'camposVal' => [
        'nombre_permiso',
      ],
    ]);
    if ($resultado) return $resultado;

    $this->nombrePermiso = $info['nombre_permiso'];

    return $this->registrarPermisosP();
  }
  public function actualizarPermisos(array $info) {

    $resultado = $this->validarPermisos([
      'infoVal' => &$info,
      'camposVal' => [
        'id_permiso',
        'nombre_permiso',
      ],
    ]);
    if ($resultado) return $resultado;
    $this->idPermiso = $info['id_permiso'];
    $this->nombrePermiso = $info['nombre_permiso'];

    return $this->actualizarPermisosP();
  }
  public function eliminarPermisos(array $info) {
    $resultado = $this->validarPermisos([
      'infoVal' => &$info,
      'camposVal' => [
        'id_permiso',
      ],
    ]);
    if ($resultado) return $resultado;
    $this->idPermiso = $info['id_permiso'];
    return $this->eliminarPermisosP();
  }

  private function seleccionarPermisosP() {
    if ($this->idPermiso == null || $this->idPermiso == "") {
      $resultado = $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'permisos',
        'BD' => 'seguridad',
      ]);
      return $resultado->fetchAll(PDO::FETCH_ASSOC);
    } else {
      $resultado = $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'permisos',
        'BD' => 'seguridad',
        'WHERE' => [
          "id_permiso" => $this->idPermiso,
        ]
      ]);
      if ($resultado->rowCount() <= 0) {
        $alerta = [
          "tipo" => "simple",
          "titulo" => "Permiso no encontrado",
          "texto" => "El permiso que ha intentado buscar no se encuentra en la base de datos",
          "icono" => "error"
        ];
        return $alerta;
        exit();
      } else {
        $permiso = $resultado->fetch(PDO::FETCH_ASSOC);
      }
      return $permiso;
    }
  }
  private function registrarPermisosP() {
    $objBitacora = new bitacoraModelo();
    $ultimoId = $this->guardarDatos2([
      'tabla' => 'permisos',
      'BD' => 'seguridad',
      'datos' => [
        "nombre_permiso" => $this->nombrePermiso,
      ]
    ]);
    if ($ultimoId !== false && $ultimoId > 0) {
      $alerta = [
        "tipo" => "limpiarYcerrar",
        "titulo" => "Permiso registrado",
        "texto" => "El permiso ha sido registrado exitosamente",
        "icono" => "success",
      ];
      $resultadoB = $objBitacora->registrarBitacora('permisos', 'Registrar', 'Éxito');
      $this->commit();
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Permiso no registrado",
        "texto" => "El permiso no ha sido registrado exitosamente",
        "icono" => "error",
      ];
      $resultadoB = $objBitacora->registrarBitacora('permisos', 'Registrar', 'Error');
      $this->rollback();
    }
    if ($resultadoB != false) {
      $this->rollback();
      return $resultadoB;
    }
    return $alerta;
  }
  private function actualizarPermisosP() {
    $objBitacora = new bitacoraModelo();
    $resultado = $this->actualizarDatos2([
      'tabla' => 'permisos',
      'BD' => 'seguridad',
      'datos' => [
        "nombre_permiso" => $this->nombrePermiso,
      ],
      "WHERE" => [
        "id_permiso" => $this->idPermiso,
      ]
    ]);
    if ($resultado == false || $resultado <= 0) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Sin cambios realizados",
        "texto" => "No se realizó ningún cambio en el permiso",
        "icono" => "warning",
      ];
      $resultadoB = $objBitacora->registrarBitacora('permisos', 'actualizar permiso con id: ' . $this->idPermiso, 'Error');
    } else {
      $alerta = [
        "tipo" => "limpiarYcerrar",
        "titulo" => "Permiso actualizado",
        "texto" => "El permiso ha sido actualizado exitosamente",
        "icono" => "success",
      ];
      $resultadoB = $objBitacora->registrarBitacora('permisos', 'actualizar permiso con id: ' . $this->idPermiso, 'Éxito');
      $this->commit();
    }
    if ($resultadoB != false) {
      $this->rollback();
      return $resultadoB;
    }
    return $alerta;
  }
  private function eliminarPermisosP() {
    $objBitacora = new bitacoraModelo();
    $eliminarPermisos = $this->eliminarDatos2([
      "tabla" => "permisos",
      'BD' => 'seguridad',
      "WHERE" => [
        "id_permiso" => $this->idPermiso
      ]
    ]);
    if ($eliminarPermisos == 1) { /*Para verificar si se hizo la eliminación o no */
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Permiso eliminado",
        "texto" => "El permiso ha sido eliminado con éxito",
        "icono" => "success"
      ];
      $resultadoB = $objBitacora->registrarBitacora('permisos', 'eliminar permiso con id: ' . $this->idPermiso, 'Éxito');
      $this->commit();
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Permiso no encontrado",
        "texto" => "El permiso no existe en la Base de Datos",
        "icono" => "error"
      ];
      $resultadoB = $objBitacora->registrarBitacora('permiso', 'eliminar permiso con id: ' . $this->idPermiso, 'Error');
    }
    if ($resultadoB != false) {
      $this->rollback();
      return $resultadoB;
    }

    return $alerta;
  }
}
