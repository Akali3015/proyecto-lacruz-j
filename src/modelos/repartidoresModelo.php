<?php

namespace src\modelos;

use src\config\connect\conexion;

class repartidoresModelo extends conexion {
  private string $cedulaRepartidor = '';
  private string $nombreRepartidor = '';
  private string $apellidoRepartidor = '';
  private string $telefonoRepartidor = '';

  public function validarRepartidores(string $permiso, array &$info, $requerido = []) {  
  $objAcceso = new accesosModelo();
    $v = $objAcceso -> validarPermisos('repartidores', $permiso);
    if ($v) return $v;

    $esquemaRepartidores = [
      'tipo' => 'arrayA',
      'propiedades' => [
        'cedula_repartidor' => [
          ...molCedulaRifLetra,
          "nombreAlerta" => "rif o cédula del repartidor",
          "nombreBD" => "cedula_repartidor",
          "tablaBD" => "repartidores",
          "requerido" => true,
          "debeSerUnico" => true
        ],
        'nombre_repartidor' => [
          ...molNombrePer,
          "nombreAlerta" => "nombre del repartidor",
          "nombreBD" => "nombre_repartidor",
          "tablaBD" => "repartidores",
          "requerido" => true,
          "minimo" => minRegexNombreObj,
          "maximo" => maxRegexNombreObj,
          "expresion_re" => regexNombreObj,
        ],
        'apellido_repartidor' => [
          ...molNombrePer,
          "nombreAlerta" => "apellido del repartidor",
          "nombreBD" => "apellido_repartidor",
          "tablaBD" => "repartidores",
          "requerido" => true,
        ],
        'telefono_repartidor' => [
          ...molTelefono,
          "nombreAlerta" => "teléfono del repartidor",
          "nombreBD" => "telefono_repartidor",
          "tablaBD" => "repartidores",
          "requerido" => true,
          'debeSerUnico' => true
        ],
      ],
      'requerido' => $requerido
    ];
    if (isset($info['prefijo_telefono_repartidor'])) {
      $info['telefono_repartidor'] = $info['prefijo_telefono_repartidor'] . $info['telefono_repartidor'];
    }
    if (isset($info['codigo_cedula_repartidor'])) {
      $info['cedula_repartidor'] = $info['codigo_cedula_repartidor'] . $info['cedula_repartidor'];
    }
    if (isset($info['esR'])) {
      unset($esquemaRepartidores['propiedades']['cedula_repartidor']['debeExistirBD']);
      unset($info['esR']);
    }
    $v = $this->limpiarValidar($info, $esquemaRepartidores);
    if ($v) return $v;

    return false;
  }

  public function seleccionarRepartidores(array $info) {
    if (($info['cedula_repartidor'] ?? '') != "") {
      $v = $this->validarRepartidores('ver detalles de los repartidores', $info);
    } else {
      $v = $this->validarRepartidores('listar', $info);
      }
      if ($v) return $v;
      $this->cedulaRepartidor = $info['cedula_repartidor'] ?? '';
    
    return $this->seleccionarRepartidoresP();
  }

  public function registrarRepartidores(array $info) {
      $info['esR'] = true;

    $v = $this->validarRepartidores('registrar', $info, [
        'cedula_repartidor',
        'nombre_repartidor',
        'apellido_repartidor',
        'telefono_repartidor',
    ]);
    if ($v) return $v;

    $this->cedulaRepartidor = $info['cedula_repartidor'];
    $this->nombreRepartidor = $info['nombre_repartidor'];
    $this->apellidoRepartidor = $info['apellido_repartidor'];
    $this->telefonoRepartidor = $info['telefono_repartidor'];

    return $this->registrarRepartidoresP();
  }
  public function actualizarRepartidores(array $info) {
    $v = $this->validarRepartidores('actualizar', $info,[
        'cedula_repartidor' => 'cedula_repartidor_act',
        'nombre_repartidor',
        'apellido_repartidor',
        'telefono_repartidor',
    ]);
    if ($v) return $v;

    $this->cedulaRepartidor = $info['cedula_repartidor'];
    $this->nombreRepartidor = $info['nombre_repartidor'];
    $this->apellidoRepartidor = $info['apellido_repartidor'];
    $this->telefonoRepartidor = $info['telefono_repartidor'];

    return $this->actualizarRepartidoresP();
  }
  public function eliminarRepartidores(array $info) {
    $v = $this->validarRepartidores('eliminar',$info, [
      'cedula_repartidor',
    ]);
    if ($v) return $v;
    $this->cedulaRepartidor = $info['cedula_repartidor'];
    return $this->eliminarRepartidoresP($info);
  }

  // PRIVADOS
  private function seleccionarRepartidoresP() {
    if ($this->cedulaRepartidor == null || $this->cedulaRepartidor == "") {
      return $this->seleccionarDatos2([
        'campos' => '
          cedula_repartidor, nombre_repartidor, 
          apellido_repartidor, telefono_repartidor
        ',
        'tabla' => 'v_repartidores_todos',
      ])->fetchAll();
    } else {
      return $this->seleccionarDatos2([
        'campos' => '
          cedula_repartidor, nombre_repartidor,
          apellido_repartidor, telefono_repartidor
        ',
        'tabla' => 'repartidores',
        'WHERE' => [
          "cedula_repartidor" => $this->cedulaRepartidor,
        ]
      ])->fetch();
    }
  }
  private function registrarRepartidoresP() {

    $ultimoID = $this->guardarDatos2([
      'tabla' => 'repartidores',
      'datos' => [
        "cedula_repartidor" => $this->cedulaRepartidor,
        "nombre_repartidor" => $this->nombreRepartidor,
        "apellido_repartidor" => $this->apellidoRepartidor,
        "telefono_repartidor" => $this->telefonoRepartidor
      ],
      'WHERE' => [
        "cedula_repartidor" => $this->cedulaRepartidor
      ]
    ]);

    if ($ultimoID !== false && $ultimoID > 0) {
      $alerta = [
        "tipo" => "limpiarYcerrar",
        "titulo" => "Repartidor registrado",
        "texto" => "El repartidor ha sido registrado exitosamente",
        "icono" => "success",
      ];
      $this->commit();
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Repartidor no registrado",
        "texto" => "El repartidor no ha sido registrado exitosamente",
        "icono" => "error",
      ];
    }
    return $alerta;
  }
  private function actualizarRepartidoresP() {
    $resultado = $this->actualizarDatos2([
      "tabla" => "repartidores",
      "datos" => [
        "cedula_repartidor" => $this->cedulaRepartidor,
        "nombre_repartidor" => $this->nombreRepartidor,
        "apellido_repartidor" => $this->apellidoRepartidor,
        "telefono_repartidor" => $this->telefonoRepartidor,
      ],
      "WHERE" => [
        "cedula_repartidor" => $this->cedulaRepartidor,
      ]
    ]);
    if ($resultado == false || $resultado <= 0) {
      return [
        "tipo" => "simple",
        "titulo" => "Sin cambios realizados",
        "texto" => "No se han realizado cambios en el registro",
        "icono" => "warning",
      ];
    }
    $this->commit();
    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Repartidor actualizado",
      "texto" => "El repartidor ha sido actualizado exitosamente",
      "icono" => "success",
    ];
  }
  private function eliminarRepartidoresP() {
    $eliminarRepartidor = $this->eliminarDatos2([
      'tabla' => 'repartidores',
      'WHERE' => [
        'cedula_repartidor' => $this->cedulaRepartidor
      ]
    ]);
    if ($eliminarRepartidor == 1) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Repartidor eliminado",
        "texto" => "El repartidor ha sido eliminado exitosamente",
        "icono" => "success",
      ];
      $this->commit();
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Repartidor no encontrado",
        "texto" => "El repartidor no existe en la base de datos",
        "icono" => "error",
      ];
    }
    return $alerta;
  }
}
