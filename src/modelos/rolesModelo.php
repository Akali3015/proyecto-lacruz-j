<?php

namespace src\modelos;

use src\config\connect\conexion;
use PDO;
use src\modelos\bitacoraModelo;

class rolesModelo extends conexion {
  private int $idRol = 0;
  private string $nombreRol = '';

  public function validarRoles(array $instruccionesVal) {
    [
      'infoVal' => &$infoVal,
      'camposVal' => &$camposVal,
    ] = $instruccionesVal;
    $funcionAsignadora = function ($nombreCampo, &$valor) {
      $claveVal = [
        "id_rol" => [
          "campo_nombre" => "id_rol",
          "campo_valor" => &$valor,
          "formulario_nombre" => "id del rol",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "roles",
          'BD' => 'seguridad',
          "debeExistir" => true,
          "debeSerUnico" => true
        ],
        "nombre_rol" => [
          "campo_nombre" => "nombre_rol",
          "campo_valor" => &$valor,
          "formulario_nombre" => "nombre del rol",
          "requerido" => true,
          "minimo" => minRegexNombrePer,
          "maximo" => maxRegexNombrePer,
          "expresion_re" => regexNombrePer,
          "tabla" => "roles",
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
  public function seleccionarRoles($info = NULL) {
    if (($info['id_rol'] ?? '') != "") {
      $resultado = $this->validarRoles([
        'infoVal' => &$info,
        'camposVal' => [
          'id_rol',
        ],
      ]);
      if ($resultado) return $resultado;
      $this->idRol = $info['id_rol'];
    }
    return $this->seleccionarRolesP();
  }
  public function registrarRoles(array $info) {
    $resultado = $this->validarRoles([
      'infoVal' => &$info,
      'camposVal' => [
        'nombre_rol',
      ],
    ]);
    if ($resultado) return $resultado;
    $this->nombreRol = $info['nombre_rol'];

    return $this->registrarRolesP();
  }
  public function actualizarRoles(array $info) {
    $resultado = $this->validarRoles([
      'infoVal' => &$info,
      'camposVal' => [
        'id_rol',
        'nombre_rol',
      ],
    ]);
    if ($resultado) return $resultado;
    $this->idRol = $info['id_rol'];
    $this->nombreRol = $info['nombre_rol'];

    return $this->actualizarRolesP();
  }
  public function eliminarRoles(array $info) {
    $resultado = $this->validarRoles([
      'infoVal' => &$info,
      'camposVal' => [
        'id_rol',
      ],
    ]);
    if ($resultado) return $resultado;
    $this->idRol = $info['id_rol'];
    return $this->eliminarRolesP();
  }

  //-- PRIVADOS [ ENCAPSULAMIENTO ]--//
  private function seleccionarRolesP() {
    if ($this->idRol == null || $this->idRol == "") {
      $resultado = $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'roles',
        'BD' => 'seguridad',
      ]);
      return $resultado->fetchAll(PDO::FETCH_ASSOC);
    } else {
      $resultado = $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'roles',
        'BD' => 'seguridad',
        'WHERE' => [
          "id_rol" => $this->idRol,
        ]
      ]);
      if ($resultado->rowCount() <= 0) {
        $alerta = [
          "tipo" => "simple",
          "titulo" => "Rol no encontrado",
          "texto" => "El rol que ha intentado buscar no se encuentra en la base de datos",
          "icono" => "error"
        ];
        return $alerta;
        exit();
      } else {
        $rol = $resultado->fetch(PDO::FETCH_ASSOC);
      }
      return $rol;
    }
  }
  private function registrarRolesP() {
    $objBitacora = new bitacoraModelo();
    $ultimoId = $this->guardarDatos2([
      'tabla' => 'roles',
      'BD' => 'seguridad',
      'datos' => [
        "nombre_rol" => $this->nombreRol,
      ]
    ]);
    if ($ultimoId !== false && $ultimoId > 0) {
      $alerta = [
        "tipo" => "limpiarYcerrar",
        "titulo" => "Rol registrado",
        "texto" => "El rol ha sido registrado exitosamente",
        "icono" => "success",
      ];
      $resultadoB = $objBitacora->registrarBitacora('roles', 'Registrar', 'Éxito');
      $this->commit();
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Rol no registrado",
        "texto" => "El rol no ha sido registrado exitosamente",
        "icono" => "error",
      ];
      $resultadoB = $objBitacora->registrarBitacora('roles', 'Registrar', 'Error');
      $this->rollback();
    }
    if ($resultadoB != false) {
      $this->rollback();
      return $resultadoB;
    }
    return $alerta;
  }
  private function actualizarRolesP() {
    $objBitacora = new bitacoraModelo();
    $resultado = $this->actualizarDatos2([
      'tabla' => 'roles',
      'BD' => 'seguridad',
      'datos' => [
        "nombre_rol" => $this->nombreRol,
      ],
      "WHERE" => [
        "id_rol" => $this->idRol,
      ]
    ]);
    if ($resultado == false || $resultado <= 0) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Sin cambios realizados",
        "texto" => "No se realizó ningún cambio en el rol",
        "icono" => "warning",
      ];
      $resultadoB = $objBitacora->registrarBitacora('roles', 'actualizar rol con id: ' . $this->idRol, 'Error');
    } else {
      $alerta = [
        "tipo" => "limpiarYcerrar",
        "titulo" => "Rol actualizado",
        "texto" => "El rol ha sido actualizado exitosamente",
        "icono" => "success",
      ];
      $resultadoB = $objBitacora->registrarBitacora('roles', 'actualizar rol con id: ' . $this->idRol, 'Éxito');
      $this->commit();
    }
    if ($resultadoB != false) {
      $this->rollback();
      return $resultadoB;
    }
    return $alerta;
  }
  private function eliminarRolesP() {
    $objBitacora = new bitacoraModelo();
    $eliminarUsuario = $this->eliminarDatos2([
      "tabla" => "roles",
      'BD' => 'seguridad',
      "WHERE" => [
        "id_rol" => $this->idRol
      ]
    ]);
    if ($eliminarUsuario == 1) { /*Para verificar si se hizo la eliminación o no */
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Rol eliminado",
        "texto" => "El rol ha sido eliminado con éxito",
        "icono" => "success"
      ];
      $resultadoB = $objBitacora->registrarBitacora('roles', 'eliminar rol con id: ' . $this->idRol, 'Éxito');
      $this->commit();
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Rol no encontrado",
        "texto" => "El rol no existe en la Base de Datos",
        "icono" => "error"
      ];
      $resultadoB = $objBitacora->registrarBitacora('roles', 'eliminar rol con id: ' . $this->idRol, 'Error');
    }
    if ($resultadoB != false) {
      $this->rollback();
      return $resultadoB;
    }

    return $alerta;
  }
}
