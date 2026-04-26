<?php

namespace src\modelos;

use src\config\connect\conexion;
use PDO;
use PDOException;
use Exception;

class clientesModelo extends conexion
{
  private $rif_cedula_cliente;
  private $razon_social_cliente;
  private $telefono_cliente;
  private $correo_cliente;
  private $direccion_cliente;

  public function seleccionarCliente($rif = null)
  {
    $this->rif_cedula_cliente = $rif;

    if ($this->rif_cedula_cliente != null && $this->rif_cedula_cliente != "") {
      // Arrays para las validaciones
      $campos = [
        [
          "campo_valor" => $this->rif_cedula_cliente,
          "formulario_nombre" => "RIF",
          "requerido" => true,
          "minimo" => minRegexNombreObj,
          "maximo" => maxRegexNombreObj,
          "expresion_re" => regexNombreObj
        ]
      ];

      $respuesta = $this->limpiar_Verificar($campos);
      if ($respuesta !== false) {
        return $respuesta;
        exit();
      } else {
        return $this->seleccionarClienteP();
      }
    } else {
      return $this->seleccionarClienteP();
    }
  }
  public function registrarCliente($rif, $razon, $telefono, $correo, $direccion)
  {
    try {
      $this->rif_cedula_cliente = $rif;
      $this->razon_social_cliente = $razon;
      $this->telefono_cliente = $telefono;
      $this->correo_cliente = $correo;
      $this->direccion_cliente = $direccion;
      $campos = [
        [
          "campo_nombre" => "rif_cedula_cliente",
          "campo_valor" => $this->rif_cedula_cliente,
          "formulario_nombre" => "RIF o cédula del cliente",
          "requerido" => true,
          "minimo" => minRegexCedulaRifLetra,
          "maximo" => maxRegexCedulaRifLetra,
          "expresion_re" => regexCedulaRifLetra,
          "tabla" => "clientes",
          "debeSerUnico" => true,
        ],
        [
          "campo_nombre" => "razon_social_cliente",
          "campo_valor" => $this->razon_social_cliente,
          "formulario_nombre" => "razón social del cliente",
          "requerido" => true,
          "minimo" => minRegexNombreObj,
          "maximo" => maxRegexNombreObj,
          "expresion_re" => regexNombreObj,
          "tabla" => "clientes",
          "debeSerUnico" => true,
        ],
        [
          "campo_nombre" => "telefono_cliente",
          "campo_valor" => $this->telefono_cliente,
          "formulario_nombre" => "teléfono del cliente",
          "requerido" => true,
          "minimo" => minRegexTelefono,
          "maximo" => maxRegexTelefono,
          "expresion_re" => regexTelefono,
        ],
        [
          "campo_nombre" => "correo_cliente",
          "campo_valor" => $this->correo_cliente,
          "formulario_nombre" => "correo electrónico del cliente",
          "requerido" => true,
          "minimo" => minRegexCorreo,
          "maximo" => maxRegexCorreo,
          "expresion_re" => regexCorreo,
        ],
        [
          "campo_nombre" => "direccion_cliente",
          "campo_valor" => $this->direccion_cliente,
          "formulario_nombre" => "dirección del cliente",
          "requerido" => true,
          "minimo" => minRegexDescripcion,
          "maximo" => maxRegexDescripcion,
          "expresion_re" => regexDescripcion,
        ]
      ];


      $respuesta = $this->limpiar_Verificar($campos);

      if ($respuesta !== false) {
        return $respuesta;
        exit();
      } else {
        return $this->registrarClientesP();
      }
    } catch (PDOException $e) {
      error_log("Error: " . $e->getMessage());
      throw new Exception("Error al registrar el cliente en la base de datos: " . $e->getMessage());
    }
  }
  public function actualizarCliente($rif, $razon, $telefono, $correo, $direccion)
  {
    $this->rif_cedula_cliente = $rif;
    $this->razon_social_cliente = $razon;
    $this->telefono_cliente = $telefono;
    $this->correo_cliente = $correo;
    $this->direccion_cliente = $direccion;
    $campos = [
      [
        "campo_nombre" => "rif_cedula_cliente",
        "campo_valor" => $this->rif_cedula_cliente,
        "formulario_nombre" => "RIF",
        "requerido" => true,
        "minimo" => minRegexCedulaRifLetra,
        "maximo" => maxRegexCedulaRifLetra,
        "expresion_re" => regexCedulaRifLetra,
        "tabla" => "clientes",
        "debeExistir" => true,
      ],
      [
        "campo_nombre" => "razon_social_cliente",
        "campo_valor" => $this->razon_social_cliente,
        "formulario_nombre" => "razón social del cliente",
        "requerido" => true,
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj,
        "tabla" => "clientes",
        "debeSerUnico" => true,
      ],
      [
        "campo_nombre" => "telefono_cliente",
        "campo_valor" => $this->telefono_cliente,
        "formulario_nombre" => "teléfono del cliente",
        "requerido" => true,
        "minimo" => minRegexTelefono,
        "maximo" => maxRegexTelefono,
        "expresion_re" => regexTelefono,
      ],
      [
        "campo_nombre" => "correo_cliente",
        "campo_valor" => $this->correo_cliente,
        "formulario_nombre" => "correo electrónico del cliente",
        "requerido" => true,
        "minimo" => minRegexCorreo,
        "maximo" => maxRegexCorreo,
        "expresion_re" => regexCorreo,
        "debeSerUnico" => true,
        "tabla" => 'clientes',
      ],
      [
        "campo_nombre" => "direccion_cliente",
        "campo_valor" => $this->direccion_cliente,
        "formulario_nombre" => "dirección del cliente",
        "requerido" => true,
        "minimo" => minRegexDescripcion,
        "maximo" => maxRegexDescripcion,
        "expresion_re" => regexDescripcion,
      ],
    ];
    $respuesta = $this->limpiar_Verificar($campos);

    if ($respuesta !== false) {
      return $respuesta;
      exit();
    } else {
      return $this->actualizarClientesP();
    }
  }
  public function eliminarCliente($rif)
  {
    $this->rif_cedula_cliente = $rif;

    $campos = [
      [
        "campo_nombre" => "rif_cedula_cliente",
        "campo_valor" => $this->rif_cedula_cliente,
        "formulario_nombre" => "RIF o cédula del cliente",
        "requerido" => true,
        "minimo" => minRegexCedulaRifLetra,
        "maximo" => maxRegexCedulaRifLetra,
        "expresion_re" => regexCedulaRifLetra,
        "tabla" => "clientes",
        "debeExistir" => true,
        "camposDiferentes" => 1
      ]
    ];

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
      exit();
    } else {
      return $this->eliminarClientesP();
    }
  }

  // PRIVADOS
  private function seleccionarClienteP()
  {
    if ($this->rif_cedula_cliente == null || $this->rif_cedula_cliente == "") {
      $resultado = $this->seleccionarDatos([
        'campos' => '
          rif_cedula_cliente, razon_social_cliente, 
          telefono_cliente, correo_cliente, direccion_cliente
        ',
        'tabla' => 'clientes',
      ]);
      $clientes = $resultado->fetchAll(PDO::FETCH_ASSOC);
      return $clientes;
    } else {
      $resultado = $this->seleccionarDatos2([
        'campos' => '
          rif_cedula_cliente, razon_social_cliente,
          telefono_cliente, correo_cliente, direccion_cliente
        ',
        'tabla' => 'clientes',
        'WHERE' => [
          "rif_cedula_cliente" => $this->rif_cedula_cliente,
        ]
      ]);
      if ($resultado->rowCount() <= 0) {
        $alerta = [
          "tipo" => "simple",
          "titulo" => "Cliente no encontrado",
          "texto" => "El cliente que ha intentado buscar no se encuentra en la base de datos",
          "icono" => "error"
        ];
        return $alerta;
      } else {
        $clientes = $resultado->fetch(PDO::FETCH_ASSOC);
        $codigoCedulaRif = preg_replace('/[0-9]/', '', $clientes['rif_cedula_cliente']);
        $cedulaRif = preg_replace('/[a-zA-Z]/', '', $clientes['rif_cedula_cliente']);
        $clientes['codigo_cedula_rif_cliente'] = $codigoCedulaRif;
        $clientes['rif_cedula_cliente'] = $cedulaRif;
      }
      return $clientes;
    }
  }
  private function registrarClientesP()
  {
    $ultimoID = $this->guardarDatos2([
      "tabla" => 'clientes',
      'datos'=> [
        "rif_cedula_cliente"=> $this->rif_cedula_cliente,
        "razon_social_cliente"=>$this->razon_social_cliente,
        "telefono_cliente"=>$this->telefono_cliente,
        "correo_cliente"=> $this->correo_cliente,
        "direccion_cliente"=>$this->direccion_cliente
      ],
      'WHERE'=>[
        'rif_cedula_cliente'=>$this->rif_cedula_cliente
      ]
    ]);

    if ($ultimoID !== false && $ultimoID > 0) {
      $alerta = [
        "tipo" => "limpiar",
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
  private function actualizarClientesP()
  {
    $instruccionesBD = [
      "campos" => "rif_cedula_cliente",
      "tabla" => "clientes",
      'WHERE' => [
        [
          "condicion_campo" => "rif_cedula_cliente",
          "condicion_marcador" => ":rif_cedula",
          "condicion_valor" => $this->rif_cedula_cliente,
          "comparacion" => "="
        ]
      ]
    ];
    $resultado = $this->seleccionarDatos($instruccionesBD);

    $instruccionesBD = [
      "tabla" => "clientes",
      "datos" => [
        [
          "campo_nombre" => "rif_cedula_cliente",
          "campo_marcador" => ":RIF",
          "campo_valor" => $this->rif_cedula_cliente
        ],
        [
          "campo_nombre" => "razon_social_cliente",
          "campo_marcador" => ":razon_social",
          "campo_valor" => $this->razon_social_cliente,
          "ponerEnMayusculas" => true
        ],
        [
          "campo_nombre" => "telefono_cliente",
          "campo_marcador" => ":telefono",
          "campo_valor" => $this->telefono_cliente
        ],
        [
          "campo_nombre" => "correo_cliente",
          "campo_marcador" => ":correo",
          "campo_valor" => $this->correo_cliente
        ],
        [
          "campo_nombre" => "direccion_cliente",
          "campo_marcador" => ":direccion",
          "campo_valor" => $this->direccion_cliente
        ]
      ],
      "WHERE" => [
        [
          "condicion_campo" => "rif_cedula_cliente",
          "condicion_marcador" => ":rif_cedula",
          "condicion_valor" => $this->rif_cedula_cliente,
          "comparacion" => "="
        ]
      ]
    ];

    $resultado = $this->actualizarDatos($instruccionesBD);
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
  private function eliminarClientesP()
  {
    $eliminarCliente = $this->eliminarDatos('clientes', 'rif_cedula_cliente', $this->rif_cedula_cliente);
    if ($eliminarCliente->rowCount() == 1) {
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
