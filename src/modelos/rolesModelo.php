<?php

namespace src\modelos;

use src\config\connect\conexion;
use PDO;
use PDOException;
use Exception;
use src\modelos\bitacoraModelo;

class rolesModelo extends conexion
{
  private $idRol;
  private $nombreRol;

  public function seleccionarRoles($id = null)
  {
    $this->idRol = $id;

    if ($this->idRol != null && $this->idRol != "") {
      //Arrays para las validaciones
      $campos = [
        [
          "campo_nombre" => 'id_rol',
          "campo_valor" => $this->idRol,
          "formulario_nombre" => "id del rol",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => 'roles',
          'BD' => 'seguridad',
          "debeExistir" => true
        ]
      ];

      $respuesta = $this->limpiar_Verificar($campos);
      if ($respuesta !== false) {
        return $respuesta;
        exit();
      }
    }
    return $this->seleccionarRolesP();
  }
  public function registrarRoles($nombre)
  {
    try {
      $this->nombreRol = $nombre;
      $campos = [
        [
          "campo_nombre" => "nombre_rol",
          "campo_valor" => $this->nombreRol,
          "formulario_nombre" => "nombre del rol",
          "requerido" => true,
          "minimo" => minRegexNombrePer,
          "maximo" => maxRegexNombrePer,
          "expresion_re" => regexNombrePer,
          "tabla" => "roles",
          'BD' => 'seguridad',
          "debeSerUnico" => true,
        ]
      ];

      $respuesta = $this->limpiar_Verificar($campos);
      if ($respuesta !== false) return $respuesta;
      return $this->registrarRolesP();
    } catch (PDOException $e) {
      error_log("Error: " . $e->getMessage());
      throw new Exception("Error al registrar el rol en la base de datos: " . $e->getMessage());
    }
  }
  public function actualizarRoles($id, $nombre)
  {
    $this->idRol = $id;
    $this->nombreRol = $nombre;

    //Arrays para las validaciones
    $campos = [
      [
        "campo_nombre" => "id_rol",
        "campo_valor" => $this->idRol,
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
      [
        "campo_nombre" => "nombre_rol",
        "campo_valor" => $this->nombreRol,
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

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
      exit();
    } else {
      return $this->actualizarRolesP();
    }
  }
  public function eliminarRoles($id)
  {
    /*Limpiar Inyección de SQL */
    $this->idRol = $id;

    //Arrays para las validaciones
    $campos = [
      [
        "campo_nombre" => "id_rol",
        "campo_valor" => $this->idRol,
        "formulario_nombre" => "id del rol",
        "requerido" => true,
        "minimo" => minRegexId,
        "maximo" => maxRegexId,
        "expresion_re" => regexId,
        "debeExistir" => true,
        "camposDiferentes" => 1,
        "tabla" => "roles",
        'BD' => 'seguridad',
      ]
    ];

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
      exit();
    } else {
      return $this->eliminarRolesP();
    }
  }

  //-- PRIVADOS [ ENCAPSULAMIENTO ]--//
  private function seleccionarRolesP()
  {
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
  private function registrarRolesP()
  {
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
  private function actualizarRolesP()
  {
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
  private function eliminarRolesP()
  {
    $objBitacora = new bitacoraModelo();
    $eliminarUsuario = $this->eliminarDatos2([
      "tabla" => "roles",
      'BD' => 'seguridad',
      "WHERE" => [
        "id_rol" => $this->idRol
      ]
    ]);
    if ($eliminarUsuario->rowCount() == 1) { /*Para verificar si se hizo la eliminación o no */
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
