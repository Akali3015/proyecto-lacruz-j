<?php

namespace src\modelos;

use src\config\connect\conexion;

class clientesModelo extends conexion {
  
  private string $rifCedulaCliente = '';
  private string $razonSocialCliente = '';
  private string $telefonoCliente = '';
  private string $correoCliente = '';
  private string $direccionCliente = '';

  public function validarClientes(array &$info, $requerido = []) {
    $info['telefono_cliente']='1234567';
    $esquemaClientes = [
      'tipo' => 'arrayA',
      'propiedades' => [
        'rif_cedula_cliente' => [
          'tipo' => 'string',
          'regex' => regexCedulaRifLetra,
          'minL' => minRegexCedulaRifLetra,
          'maxL' => maxRegexCedulaRifLetra,
          'nombreAlerta' => 'rif o cédula del cliente',
          'tablaBD' => 'clientes',
          'nombreBD' => 'rif_cedula_cliente',
          'debeSerUnicoBD' => true,
        ],
        'razon_social_cliente' => [
          'tipo' => 'string',
          "nombreAlerta" => "razón social del cliente",
          "requerido" => true,
          "minL" => minRegexNombreObj,
          "maxL" => maxRegexNombreObj,
          "regex" => regexNombreObj,
          "tablaBD" => "clientes",
          "nombreBD" => "razon_social_cliente",
          "debeSerUnicoBD" => true,
        ],
        'telefono_cliente' => [
          'tipo' => 'string',
          "nombreAlerta" => "teléfono del cliente",
          "requerido" => true,
          "minL" => minRegexTelefono,
          "maxL" => maxRegexTelefono,
          "regex" => regexTelefono,
          "tablaBD" => "clientes",
          "nombreBD" => "telefono_cliente",
          "debeSerUnicoBD" => true,
        ],
        'correo_cliente' => [
          'tipo' => 'string',
          "nombreAlerta" => "correo electrónico del cliente",
          "requerido" => true,
          "minL" => minRegexCorreo,
          "maxL" => maxRegexCorreo,
          "regex" => regexCorreo,
          "tablaBD" => 'clientes',
          "nombreBD" => "correo_cliente",
          "debeSerUnicoBD" => true,
        ],
        'direccion_cliente' => [
          'tipo' => 'string',
          "nombreAlerta" => "dirección del cliente",
          "requerido" => true,
          "minL" => minRegexDescripcion,
          "maxL" => maxRegexDescripcion,
          "regex" => regexDescripcion,
        ],
      ],
      'campoUnicoBD' => 'rif_cedula_cliente',
      'requerido' => $requerido
    ];
    if (isset($info['cedula_exis'])) $esquemaClientes['propiedades']['rif_cedula_cliente']['debeExistirBD'] = true;
    if (isset($info['prefijo_telefono_cliente'])) {
      $info['telefono_cliente'] = $info['prefijo_telefono_cliente'] . $info['telefono_cliente'];
    }
    if (isset($info['codigo_rif_cedula_cliente'])) {
      $info['rif_cedula_cliente'] = $info['codigo_rif_cedula_cliente'] . $info['rif_cedula_cliente'];
    }
    return $this->limpiarValidar($info, $esquemaClientes);
  }
  public function seleccionarClientes(array $info) {
    if (($info['rif_cedula_cliente'] ?? '') != '') {
      $info['cedula_exis'] = true;
      $resultado = $this->validarClientes($info, [
        'rif_cedula_cliente'
      ]);
      if ($resultado) return $resultado;
      $this->rifCedulaCliente = $info['rif_cedula_cliente'];
    }
    return $this->seleccionarClientesP();
  }
  public function registrarClientes(array $info) {
    $resultado = $this->validarClientes($info, [
      'rif_cedula_cliente',
      'razon_social_cliente',
      'telefono_cliente',
      'correo_cliente',
      'direccion_cliente'
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
    $info['cedula_exis'] = true;
    $resultado = $this->validarClientes($info, [
      'rif_cedula_cliente',
      'razon_social_cliente',
      'telefono_cliente',
      'correo_cliente',
      'direccion_cliente'
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
    $info['cedula_exis'] = true;
    $resultado = $this->validarClientes($info, [
      'rif_cedula_cliente_act' => 'rif_cedula_cliente',
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
