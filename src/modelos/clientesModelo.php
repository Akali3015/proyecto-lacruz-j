<?php

namespace src\modelos;

use src\config\connect\conexion;

class clientesModelo extends conexion {

  private string $rifCedulaCliente = '';
  private string $razonSocialCliente = '';
  private string $telefonoCliente = '';
  private string $correoCliente = '';
  private string $direccionCliente = '';

  public function validarClientes(array $instruccionesVal) {
    [
      'infoVal' => &$infoVal,
      'camposVal' => &$camposVal,
    ] = $instruccionesVal;
    $funcionAsignadora = function ($nombreCampo, &$valor) {
      $claveVal = [
        'rif_cedula_cliente' => [
          "campo_nombre" => "rif_cedula_cliente",
          "campo_valor" => &$valor,
          "formulario_nombre" => "RIF",
          "requerido" => true,
          "minimo" => minRegexCedulaRifLetra,
          "maximo" => maxRegexCedulaRifLetra,
          "expresion_re" => regexCedulaRifLetra,
          "tabla" => "clientes",
          "debeSerUnico" => true,
        ],
        'rif_cedula_cliente_act' => [
          "campo_nombre" => "rif_cedula_cliente",
          "campo_valor" => &$valor,
          "formulario_nombre" => "RIF",
          "requerido" => true,
          "minimo" => minRegexCedulaRifLetra,
          "maximo" => maxRegexCedulaRifLetra,
          "expresion_re" => regexCedulaRifLetra,
          "tabla" => "clientes",
          "debeSerUnico" => true,
          "debeExistir" => true,
        ],
        'razon_social_cliente' => [
          "campo_nombre" => "razon_social_cliente",
          "campo_valor" => &$valor,
          "formulario_nombre" => "razón social del cliente",
          "requerido" => true,
          "minimo" => minRegexNombreObj,
          "maximo" => maxRegexNombreObj,
          "expresion_re" => regexNombreObj,
          "tabla" => "clientes",
          "debeSerUnico" => true,
        ],
        'telefono_cliente' => [
          "campo_nombre" => "telefono_cliente",
          "campo_valor" => &$valor,
          "formulario_nombre" => "teléfono del cliente",
          "requerido" => true,
          "minimo" => minRegexTelefono,
          "maximo" => maxRegexTelefono,
          "expresion_re" => regexTelefono,
        ],
        'correo_cliente' => [
          "campo_nombre" => "correo_cliente",
          "campo_valor" => &$valor,
          "formulario_nombre" => "correo electrónico del cliente",
          "requerido" => true,
          "minimo" => minRegexCorreo,
          "maximo" => maxRegexCorreo,
          "expresion_re" => regexCorreo,
          "debeSerUnico" => true,
          "tabla" => 'clientes',
        ],
        'direccion_cliente' => [
          "campo_nombre" => "direccion_cliente",
          "campo_valor" => &$valor,
          "formulario_nombre" => "dirección del cliente",
          "requerido" => true,
          "minimo" => minRegexDescripcion,
          "maximo" => maxRegexDescripcion,
          "expresion_re" => regexDescripcion,
        ],
      ];
      return $claveVal[$nombreCampo];
    };
    $campos = [];
    foreach ($camposVal as $campo => $valorForm) {
      $clave = is_numeric($campo) ? $valorForm : $campo;
      if ($clave == 'telefono_cliente') {
        if (($infoVal['telefono_cliente'] ?? '') == '') {
          return [
            'tipo' => 'simple',
            'titulo' => 'Telefono vacío',
            'texto' => 'No puede enviar el formulario sin escribir el telefono',
            'icono' => 'error'
          ];
        }
        if (($infoVal['prefijo_telefono_cliente'] ?? '') == '') {
          return [
            'tipo' => 'simple',
            'titulo' => 'Telefono vacío',
            'texto' => 'No puede enviar el formulario sin elegir el prefijo del teléfono',
            'icono' => 'error'
          ];
        }
        $infoVal['telefono_cliente'] = $infoVal['prefijo_telefono_cliente'] . $infoVal['telefono_cliente'];
      }
      $funcionAsignadora($valorForm, $infoVal[$clave]);
    }
    return $this->limpiar_Verificar($campos);
  }
  public function seleccionarClientes(array $info) {
    if (($info['rif_cedula_cliente'] ?? '') != '') {
      $resultado = $this->validarClientes([
        'infoVal' => &$info,
        'camposVal' => [
          'rif_cedula_cliente_act' => 'rif_cedula_cliente',
        ],
      ]);
      if ($resultado) return $resultado;
      $this->rifCedulaCliente = $info['rif_cedula_cliente'];
    }
    return $this->seleccionarClientesP();
  }
  public function registrarClientes(array $info) {
    $info['rif_cedula_cliente'] = $info['codigo_rif_cedula_cliente'] . $info['rif_cedula_cliente'];
    $resultado = $this->validarClientes([
      'infoVal' => &$info,
      'camposVal' => [
        'rif_cedula_cliente',
        'razon_social_cliente',
        'telefono_cliente',
        'correo_cliente',
        'direccion_cliente'
      ]
    ]);
    if ($resultado) return $resultado;

    $this->rifCedulaCliente = $info['rif_cedula_cliente'];
    $this->razonSocialCliente = $info['razon_social_cliente'];
    $this->telefonoCliente = $info['telefono_cliente'];
    $this->correoCliente = $info['correo_cliente'];
    $this->direccionCliente = $info['direccion_cliente'];

    return $this->registrarClientesP();
  }
  public function actualizarClientes(array $info) {
    $resultado = $this->validarClientes([
      'infoVal' => &$info,
      'camposVal' => [
        'rif_cedula_cliente_act' => 'rif_cedula_cliente',
        'razon_social_cliente',
        'telefono_cliente',
        'correo_cliente',
        'direccion_cliente'
      ]
    ]);
    if ($resultado) return $resultado;

    $this->rifCedulaCliente = $info['rif_cedula_cliente'];
    $this->razonSocialCliente = $info['razon_social_cliente'];
    $this->telefonoCliente = $info['telefono_cliente'];
    $this->correoCliente = $info['correo_cliente'];
    $this->direccionCliente = $info['direccion_cliente'];

    return $this->actualizarClientesP();
  }
  public function eliminarClientes(array $info) {
    $resultado = $this->validarClientes([
      'infoVal' => &$info,
      'camposVal' => [
        'rif_cedula_cliente_act' => 'rif_cedula_cliente',
      ]
    ]);
    if ($resultado) return $resultado;
    $this->rifCedulaCliente = $info['rif_cedula_cliente'];
    return $this->eliminarClientesP();
  }

  private function seleccionarClientesP() {
    if ($this->rifCedulaCliente == null || $this->rifCedulaCliente == "") {
      return $this->seleccionarDatos2([
        'campos' => '
          rif_cedula_cliente, razon_social_cliente, 
          telefono_cliente, correo_cliente, direccion_cliente
        ',
        'tabla' => 'v_clientes_todos',
      ])->fetchAll();
    } else {
      return $this->seleccionarDatos2([
        'campos' => '
          rif_cedula_cliente, razon_social_cliente,
          telefono_cliente, correo_cliente, direccion_cliente
        ',
        'tabla' => 'clientes',
        'WHERE' => [
          "rif_cedula_cliente" => $this->rifCedulaCliente,
        ]
      ])->fetch();
    }
  }
  private function registrarClientesP() {
    $ultimoID = $this->guardarDatos2([
      "tabla" => 'clientes',
      'datos' => [
        "rif_cedula_cliente" => $this->rifCedulaCliente,
        "razon_social_cliente" => $this->razonSocialCliente,
        "telefono_cliente" => $this->telefonoCliente,
        "correo_cliente" => $this->correoCliente,
        "direccion_cliente" => $this->direccionCliente
      ],
      'WHERE' => [
        'rif_cedula_cliente' => $this->rifCedulaCliente
      ]
    ]);

    if ($ultimoID !== false && $ultimoID > 0) {
      $alerta = [
        "tipo" => "limpiarYcerrar",
        "titulo" => "Cliente registrado",
        "texto" => "El cliente ha sido registrado exitosamente",
        "icono" => "success",
      ];
      $this->commit();
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Cliente no registrado",
        "texto" => "El cliente no ha sido registrado exitosamente",
        "icono" => "error",
      ];
    }
    return $alerta;
  }
  private function actualizarClientesP() {
    $resultado = $this->actualizarDatos2([
      "tabla" => "clientes",
      "datos" => [
        "rif_cedula_cliente" => $this->rifCedulaCliente,
        "razon_social_cliente" => $this->razonSocialCliente,
        "telefono_cliente" => $this->telefonoCliente,
        "correo_cliente" => $this->correoCliente,
        "direccion_cliente" => $this->direccionCliente
      ],
      "WHERE" => [
        "rif_cedula_cliente" => $this->rifCedulaCliente,
      ]
    ]);
    if ($resultado == false || $resultado <= 0) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Sin cambios realizados",
        "texto" => "No se han realizado cambios en el registro",
        "icono" => "warning",
      ];
    } else {
      $alerta = [
        "tipo" => "limpiarYcerrar",
        "titulo" => "Cliente actualizado",
        "texto" => "El cliente ha sido actualizado exitosamente",
        "icono" => "success",
      ];
      $this->commit();
    }
    return $alerta;
  }
  private function eliminarClientesP() {
    $eliminarCliente = $this->eliminarDatos2([
      'tabla' => 'clientes',
      'WHERE' => [
        'rif_cedula_cliente' => $this->rifCedulaCliente
      ]
    ]);
    if ($eliminarCliente >= 1) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Cliente eliminado",
        "texto" => "El cliente ha sido eliminado exitosamente",
        "icono" => "success",
      ];
      $this->commit();
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Cliente no encontrado",
        "texto" => "El cliente no existe en la base de datos",
        "icono" => "error",
      ];
    }
    return $alerta;
  }
}
