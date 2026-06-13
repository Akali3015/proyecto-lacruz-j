<?php

namespace src\modelos;

use src\config\connect\conexion;

class repartidoresModelo extends conexion {
  private string $cedulaRepartidor = '';
  private string $nombreRepartidor = '';
  private string $apellidoRepartidor = '';
  private string $telefonoRepartidor = '';

  public function validarRepartidores(array $instruccionesVal) {
    [
      'infoVal' => &$infoVal,
      'camposVal' => &$camposVal,
    ] = $instruccionesVal;
    $funcionAsignadora = function ($nombreCampo, &$valor) {
      $claveVal = [
        'cedula_repartidor' => [
          "campo_nombre" => "cedula_repartidor",
          "campo_valor" => &$valor,
          "formulario_nombre" => "Cédula del Repartidor",
          "requerido" => true,
          "minimo" => minRegexCedulaRifLetra,
          "maximo" => maxRegexCedulaRifLetra,
          "expresion_re" => regexCedulaRifLetra,
          "tabla" => "repartidores",
          "debeSerUnico" => true
        ],
        'cedula_repartidor_act' => [
          "campo_nombre" => "cedula_repartidor",
          "campo_valor" => &$valor,
          "formulario_nombre" => "Cédula del Repartidor",
          "requerido" => true,
          "minimo" => minRegexCedulaRifLetra,
          "maximo" => maxRegexCedulaRifLetra,
          "expresion_re" => regexCedulaRifLetra,
          "tabla" => "repartidores",
          "debeExistir" => true,
          "debeSerUnico" => true
        ],
        'nombre_repartidor' => [
          "campo_valor" => &$valor,
          "formulario_nombre" => "Nombre del Repartidor",
          "requerido" => true,
          "minimo" => minRegexNombreObj,
          "maximo" => maxRegexNombreObj,
          "expresion_re" => regexNombreObj,
        ],
        'apellido_repartidor' => [
          "campo_valor" => &$valor,
          "formulario_nombre" => "Apellido del Repartidor",
          "requerido" => true,
          "minimo" => minRegexNombreObj,
          "maximo" => maxRegexNombreObj,
          "expresion_re" => regexNombreObj,
        ],
        'telefono_repartidor' => [
          "campo_nombre" => 'telefono_repartidor',
          "campo_valor" => &$valor,
          "formulario_nombre" => "Teléfono del repartidor",
          "requerido" => true,
          "minimo" => minRegexTelefono,
          "maximo" => maxRegexTelefono,
          "expresion_re" => regexTelefono,
          'tabla' => 'repartidores',
          'debeSerUnico' => true
        ],
      ];
      return $claveVal[$nombreCampo];
    };
    $campos = [];
    foreach ($camposVal as $valorForm => $campoVal) {
      if (is_numeric($valorForm)) $valorForm = $campoVal;
      if ($campoVal == 'telefono_repartidor') {
        if (($infoVal['telefono_repartidor'] ?? '') == '') {
          return [
            'tipo' => 'simple',
            'titulo' => 'Telefono vacío',
            'texto' => 'No puede enviar el formulario sin escribir el telefono',
            'icono' => 'error'
          ];
        }
        if (($infoVal['prefijo_telefono_repartidor'] ?? '') == '') {
          return [
            'tipo' => 'simple',
            'titulo' => 'Telefono vacío',
            'texto' => 'No puede enviar el formulario sin elegir el prefijo del teléfono',
            'icono' => 'error'
          ];
        }
        $infoVal['telefono_repartidor'] = $infoVal['prefijo_telefono_repartidor'] . $infoVal['telefono_repartidor'];
      } elseif ($campoVal == 'cedula_repartidor') {
        if (($infoVal['codigo_cedula_repartidor'] ?? '') == '') {
          return [
            'tipo' => 'simple',
            'titulo' => 'Prefijo de cédula vacío',
            'texto' => 'No puede enviar el formulario sin seleccionar el prefijo de la cédula del repartidor',
            'icono' => 'error'
          ];
        }
        if (($infoVal['cedula_repartidor'] ?? '') == '') {
          return [
            'tipo' => 'simple',
            'titulo' => 'Cédula vacía',
            'texto' => 'No puede enviar el formulario sin escribir la cédula del repartidor',
            'icono' => 'error'
          ];
        }
        $infoVal['cedula_repartidor'] = $infoVal['codigo_cedula_repartidor'] . $infoVal['cedula_repartidor'];
      }
      $campos[] = $funcionAsignadora($campoVal, $infoVal[$valorForm]);
    }
    return $this->limpiar_Verificar($campos);
  }
  public function seleccionarRepartidores(array $info) {
    if (($info['cedula_repartidor'] ?? '') != "") {
      $resultado = $this->validarRepartidores([
        'infoVal' => &$info,
        'camposVal' => [
          'cedula_repartidor' => 'cedula_repartidor_act',
        ],
      ]);
      if ($resultado) return $resultado;
      $this->cedulaRepartidor = $info['cedula_repartidor'];
    }
    return $this->seleccionarRepartidoresP();
  }
  public function registrarRepartidores(array $info) {
    $resultado = $this->validarRepartidores([
      'infoVal' => &$info,
      'camposVal' => [
        'cedula_repartidor',
        'nombre_repartidor',
        'apellido_repartidor',
        'telefono_repartidor',
      ],
    ]);
    if ($resultado) return $resultado;

    $this->cedulaRepartidor = $info['cedula_repartidor'];
    $this->nombreRepartidor = $info['nombre_repartidor'];
    $this->apellidoRepartidor = $info['apellido_repartidor'];
    $this->telefonoRepartidor = $info['telefono_repartidor'];

    return $this->registrarRepartidoresP();
  }
  public function actualizarRepartidores(array $info) {
    $resultado = $this->validarRepartidores([
      'infoVal' => &$info,
      'camposVal' => [
        'cedula_repartidor' => 'cedula_repartidor_act',
        'nombre_repartidor',
        'apellido_repartidor',
        'telefono_repartidor',
      ],
    ]);
    if ($resultado) return $resultado;

    $this->cedulaRepartidor = $info['cedula_repartidor'];
    $this->nombreRepartidor = $info['nombre_repartidor'];
    $this->apellidoRepartidor = $info['apellido_repartidor'];
    $this->telefonoRepartidor = $info['telefono_repartidor'];

    return $this->actualizarRepartidoresP();
  }
  public function eliminarRepartidores(array $info) {
    $resultado = $this->validarRepartidores([
      'infoVal' => &$info,
      'camposVal' => [
        'cedula_repartidor_act' => 'cedula_repartidor',
      ],
    ]);
    if ($resultado) return $resultado;
    $this->cedulaRepartidor = $info['cedula_repartidor'];
    return $this->eliminarRepartidoresP();
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
      'WHRE' => [
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
