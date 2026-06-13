<?php

namespace src\modelos;

use src\config\connect\conexion;
use PDO;

class proveedoresModelo extends conexion {
  private string $rifProveedor = '';
  private string $razonSocialProveedor = '';
  private string $telefonoProveedor = '';
  private string $correoProveedor = '';
  private string $direccionProveedor = '';

  public function validarProveedores(array $instruccionesVal) {
    [
      'infoVal' => &$infoVal,
      'camposVal' => &$camposVal,
    ] = $instruccionesVal;

    $funcionAsignadora = function ($nombreCampo, &$valor) {
      $claveVal = [
        'rif_proveedor' => [
          "campo_nombre" => "rif_proveedor",
          "campo_valor" => &$valor,
          "formulario_nombre" => "RIF",
          "requerido" => true,
          "minimo" => minRegexCedulaRifLetra,
          "maximo" => maxRegexCedulaRifLetra,
          "expresion_re" => regexCedulaRifLetra,
          "tabla" => "proveedores",
          "debeSerUnico" => true,
        ],
        'rif_proveedor_act' => [
          "campo_nombre" => "rif_proveedor",
          "campo_valor" => &$valor,
          "formulario_nombre" => "RIF",
          "requerido" => true,
          "minimo" => minRegexCedulaRifLetra,
          "maximo" => maxRegexCedulaRifLetra,
          "expresion_re" => regexCedulaRifLetra,
          "tabla" => "proveedores",
          "debeExistir" => true,
          "debeSerUnico" => true,
        ],
        'razon_social_proveedor' => [
          "campo_nombre" => "razon_social_proveedor",
          "campo_valor" => &$valor,
          "formulario_nombre" => "razón social del proveedor",
          "requerido" => true,
          "minimo" => minRegexNombreObj,
          "maximo" => maxRegexNombreObj,
          "expresion_re" => regexNombreObj,
          "tabla" => "proveedores",
          "debeSerUnico" => true,
        ],
        'telefono_proveedor' => [
          "campo_nombre" => "telefono_proveedor",
          "campo_valor" => &$valor,
          "formulario_nombre" => "teléfono del proveedor",
          "requerido" => true,
          "minimo" => minRegexTelefono,
          "maximo" => maxRegexTelefono,
          "expresion_re" => regexTelefono,
          "tabla" => 'proveedores',
          "debeSerUnico" => true,
        ],
        'correo_proveedor' => [
          "campo_nombre" => "correo_proveedor",
          "campo_valor" => &$valor,
          "formulario_nombre" => "correo electrónico del proveedor",
          "requerido" => true,
          "minimo" => minRegexCorreo,
          "maximo" => maxRegexCorreo,
          "expresion_re" => regexCorreo,
          "tabla" => 'proveedores',
          "debeSerUnico" => true,
        ],
        'direccion_proveedor' => [
          "campo_valor" => &$valor,
          "formulario_nombre" => "dirección del proveedor",
          "requerido" => true,
          "minimo" => minRegexDescripcion,
          "maximo" => maxRegexDescripcion,
          "expresion_re" => regexDescripcion,
        ],
      ];
      return $claveVal[$nombreCampo];
    };
    $campos = [];
    foreach ($camposVal as $valorForm  => $campoVal) {
      if (is_numeric($valorForm)) $valorForm = $campoVal;
      if ($campoVal == 'telefono_proveedor') {
        if (($infoVal['telefono_proveedor'] ?? '') == '') {
          return [
            'tipo' => 'simple',
            'titulo' => 'Telefono vacío',
            'texto' => 'No puede enviar el formulario sin escribir el telefono',
            'icono' => 'error'
          ];
        }
        if (($infoVal['prefijo_telefono_proveedor'] ?? '') == '') {
          return [
            'tipo' => 'simple',
            'titulo' => 'Telefono vacío',
            'texto' => 'No puede enviar el formulario sin elegir el prefijo del teléfono',
            'icono' => 'error'
          ];
        }
        $infoVal['telefono_proveedor'] = $infoVal['prefijo_telefono_proveedor'] . $infoVal['telefono_proveedor'];
      } elseif ($campoVal == 'rif_proveedor') {
        if (($infoVal['codigo_rif_proveedor'] ?? '') == '') {
          return [
            'tipo' => 'simple',
            'titulo' => 'RIF vacío',
            'texto' => 'No puede enviar el formulario sin seleccionar el prefijo del RIF del proveedor',
            'icono' => 'error'
          ];
        }
        if (($infoVal['rif_proveedor'] ?? '') == '') {
          return [
            'tipo' => 'simple',
            'titulo' => 'RIF vacío',
            'texto' => 'No puede enviar el formulario sin escribir el RIF del proveedor',
            'icono' => 'error'
          ];
        }
        $infoVal['rif_proveedor'] = $infoVal['codigo_rif_proveedor'] . $infoVal['rif_proveedor'];
      }
      $campos[] = $funcionAsignadora($campoVal, $infoVal[$valorForm]);
    }
    return $this->limpiar_Verificar($campos);
  }
  public function seleccionarProveedores(array $info) {
    if (($info['rif_proveedor'] ?? '') != "") {
      $resultado = $this->validarProveedores([
        'infoVal' => &$info,
        'camposVal' => [
          'rif_proveedor_act' => 'rif_proveedor',
        ],
      ]);
      if ($resultado) return $resultado;
      $this->rifProveedor = $info['rif_proveedor'];
    }
    return $this->seleccionarProveedoresP();
  }
  public function registrarProveedores(array $info) {
    $resultado = $this->validarProveedores([
      'infoVal' => &$info,
      'camposVal' => [
        'rif_proveedor',
        'razon_social_proveedor',
        'telefono_proveedor',
        'correo_proveedor',
        'direccion_proveedor',
      ],
    ]);
    if ($resultado) return $resultado;

    $this->rifProveedor = $info['rif_proveedor'];
    $this->razonSocialProveedor = $info['razon_social_proveedor'];
    $this->telefonoProveedor = $info['telefono_proveedor'];
    $this->correoProveedor = $info['correo_proveedor'];
    $this->direccionProveedor = $info['direccion_proveedor'];

    return $this->registrarProveedoresP();
  }
  public function actualizarProveedores(array $info) {
    $resultado = $this->validarProveedores([
      'infoVal' => &$info,
      'camposVal' => [
        'rif_proveedor' => 'rif_proveedor_act',
        'razon_social_proveedor',
        'telefono_proveedor',
        'correo_proveedor',
        'direccion_proveedor',
      ],
    ]);

    if ($resultado) return $resultado;
    $this->rifProveedor = $info['rif_proveedor'];
    $this->razonSocialProveedor = $info['razon_social_proveedor'];
    $this->telefonoProveedor = $info['telefono_proveedor'];
    $this->correoProveedor = $info['correo_proveedor'];
    $this->direccionProveedor = $info['direccion_proveedor'];

    return $this->actualizarProveedoresP();
  }
  public function eliminarProveedores(array $info) {
    $resultado = $this->validarProveedores([
      'infoVal' => &$info,
      'camposVal' => [
        'rif_proveedor_act' => 'rif_proveedor',
      ],
    ]);
    if ($resultado) return $resultado;
    $this->rifProveedor = $info['rif_proveedor'];

    return $this->eliminarProveedoresP();
  }

  // PRIVADOS
  private function seleccionarProveedoresP() {
    if ($this->rifProveedor == null || $this->rifProveedor == "") {
      $resultado = $this->seleccionarDatos2([
        'campos' => '
          rif_proveedor, razon_social_proveedor, 
          telefono_proveedor, correo_proveedor, direccion_proveedor
        ',
        'tabla' => 'proveedores',
      ]);
      return $resultado->fetchAll();
    } else {
      return $this->seleccionarDatos2([
        'campos' => '
          rif_proveedor, razon_social_proveedor, 
          telefono_proveedor, correo_proveedor, direccion_proveedor
        ',
        'tabla' => 'proveedores',
        'WHERE' => [
          "rif_proveedor" => $this->rifProveedor,
        ]
      ])->fetch();
    }
  }
  private function registrarProveedoresP() {
    $ultimoID = $this->guardarDatos2([
      'tabla' => 'proveedores',
      'datos' => [
        "rif_proveedor" => $this->rifProveedor,
        "razon_social_proveedor" => $this->razonSocialProveedor,
        "telefono_proveedor" => $this->telefonoProveedor,
        "correo_proveedor" => $this->correoProveedor,
        "direccion_proveedor" => $this->direccionProveedor
      ],
      'WHERE' => [
        "rif_proveedor" => $this->rifProveedor
      ]
    ]);

    if ($ultimoID !== false && $ultimoID > 0) {
      $alerta = [
        "tipo" => "limpiarYcerrar",
        "titulo" => "Proveedor registrado",
        "texto" => "El proveedor ha sido registrado exitosamente",
        "icono" => "success",
      ];
      $this->commit();
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Proveedor no registrado",
        "texto" => "El proveedor no ha sido registrado exitosamente",
        "icono" => "error",
      ];
    }
    return $alerta;
  }
  private function actualizarProveedoresP() {
    $resultado = $this->seleccionarDatos2([
      "campos" => "rif_proveedor",
      "tabla" => "proveedores",
      'WHERE' => [
        "rif_proveedor" => $this->rifProveedor,
      ]
    ]);
    $proveedoresExistente = $resultado->fetch();

    if ($this->rifProveedor == '') {
      $this->rifProveedor = $proveedoresExistente['rif_proveedor'];
    }

    $resultado = $this->actualizarDatos2([
      "tabla" => "proveedores",
      "datos" => [
        [
          "campo_nombre" => "rif_proveedor",
          "campo_marcador" => ":RIF",
          "campo_valor" => $this->rifProveedor
        ],
        [
          "campo_nombre" => "razon_social_proveedor",
          "campo_marcador" => ":razon_social",
          "campo_valor" => $this->razonSocialProveedor,
          "ponerEnMayusculas" => true
        ],
        [
          "campo_nombre" => "telefono_proveedor",
          "campo_marcador" => ":telefono",
          "campo_valor" => $this->telefonoProveedor
        ],
        [
          "campo_nombre" => "correo_proveedor",
          "campo_marcador" => ":correo",
          "campo_valor" => $this->correoProveedor
        ],
        [
          "campo_nombre" => "direccion_proveedor",
          "campo_marcador" => ":direccion",
          "campo_valor" => $this->direccionProveedor
        ]
      ],
      "WHERE" => [
        [
          "condicion_campo" => "rif_proveedor",
          "condicion_marcador" => ":rif_proveedor",
          "condicion_valor" => $this->rifProveedor,
          "comparacion" => "="
        ]
      ]
    ]);
    if ($resultado == false || $resultado <= 0) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Proveedor no actualizado",
        "texto" => "El proveedor no ha sido actualizado exitosamente",
        "icono" => "warning",
      ];
    } else {
      $alerta = [
        "tipo" => "limpiarYcerrar",
        "titulo" => "Proveedor actualizado",
        "texto" => "El proveedor ha sido actualizado exitosamente",
        "icono" => "success",
      ];
      $this->commit();
    }
    return $alerta;
  }
  private function eliminarProveedoresP() {
    $eliminarProveedor = $this->eliminarDatos2([
      'tabla' => 'proveedores',
      'WHERE' => [
        'rif_proveedor' => $this->rifProveedor
      ]
    ]);
    if ($eliminarProveedor == 1) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Proveedor eliminado",
        "texto" => "El proveedor ha sido eliminado exitosamente",
        "icono" => "success",
      ];
      $this->commit();
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Proveedor no encontrado",
        "texto" => "El proveedor no existe en la base de datos",
        "icono" => "error",
      ];
    }
    return $alerta;
  }
}
