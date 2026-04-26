<?php

namespace src\modelos;

use src\config\connect\conexion;
use PDO;
use PDOException;
use Exception;

class repartidoresModelo extends conexion
{
  private $cedula_repartidor;
  private $nombre_repartidor;
  private $apellido_repartidor;
  private $telefono_repartidor;

  public function seleccionarRepartidor($cedula = null)
  {
    $this->cedula_repartidor = $cedula;

    if ($this->cedula_repartidor != null && $this->cedula_repartidor != "") {
      // Arrays para las validaciones
      $campos = [
        [
          "campo_valor" => $this->cedula_repartidor,
          "formulario_nombre" => "Cédula del Repartidor",
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
        return $this->seleccionarRepartidorP();
      }
    } else {
      return $this->seleccionarRepartidorP();
    }
  }
  public function registrarRepartidor($cedula, $nombre, $apellido, $telefono)
  {
    try {
      $this->cedula_repartidor = $cedula;
      $this->nombre_repartidor = $nombre;
      $this->apellido_repartidor = $apellido;
      $this->telefono_repartidor = $telefono;
      $campos = [
        [
          "campo_nombre" => "cedula_repartidor",
          "campo_valor" => $this->cedula_repartidor,
          "formulario_nombre" => "Cédula del Repartidor",
          "requerido" => true,
          "minimo" => minRegexCedulaRifLetra,
          "maximo" => maxRegexCedulaRifLetra,
          "expresion_re" => regexCedulaRifLetra,
          "tabla" => "repartidores",
          "debeSerUnico" => true,
        ],
        [
          "campo_nombre" => "nombre_repartidor",
          "campo_valor" => $this->nombre_repartidor,
          "formulario_nombre" => "Nombre del Repartidor",
          "requerido" => true,
          "minimo" => minRegexNombreObj,
          "maximo" => maxRegexNombreObj,
          "expresion_re" => regexNombreObj,
          "tabla" => "repartidores",
        ],
        [
          "campo_nombre" => "apellido_repartidor",
          "campo_valor" => $this->apellido_repartidor,
          "formulario_nombre" => "Apellido del Repartidor",
          "requerido" => true,
          "minimo" => minRegexNombreObj,
          "maximo" => maxRegexNombreObj,
          "expresion_re" => regexNombreObj,
          "tabla" => "repartidores",
        ],
        [
          "campo_nombre" => "telefono_repartidor",
          "campo_valor" => $this->telefono_repartidor,
          "formulario_nombre" => "Teléfono del Repartidor",
          "requerido" => true,
          "minimo" => minRegexTelefono,
          "maximo" => maxRegexTelefono,
          "expresion_re" => regexTelefono,
        ],
      ];

      $respuesta = $this->limpiar_Verificar($campos);

      if ($respuesta !== false) {
        return $respuesta;
        exit();
      } else {
        return $this->registrarRepartidoresP();
      }
    } catch (PDOException $e) {
      error_log("Error: " . $e->getMessage());
      throw new Exception("Error al registrar el repartidor en la base de datos: " . $e->getMessage());
    }
  }
  public function actualizarRepartidor($cedula, $nombre, $apellido, $telefono)
  {
    $this->cedula_repartidor = $cedula;
    $this->nombre_repartidor = $nombre;
    $this->apellido_repartidor = $apellido;
    $this->telefono_repartidor = $telefono;

    $campos = [
      [
        "campo_nombre" => "cedula_repartidor",
        "campo_valor" => $this->cedula_repartidor,
        "formulario_nombre" => "Cédula del Repartidor",
        "requerido" => true,
        "minimo" => minRegexCedulaRifLetra,
        "maximo" => maxRegexCedulaRifLetra,
        "expresion_re" => regexCedulaRifLetra,
        "tabla" => "repartidores",
        "debeExistir" => true,
        "debeSerUnico" => true
      ],
      [
        "campo_nombre" => "nombre_repartidor",
        "campo_valor" => $this->nombre_repartidor,
        "formulario_nombre" => "Nombre del Repartidor",
        "requerido" => true,
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj,
        "tabla" => "repartidores"
      ],
      [
        "campo_nombre" => "apellido_repartidor",
        "campo_valor" => $this->apellido_repartidor,
        "formulario_nombre" => "Apellido del Repartidor",
        "requerido" => true,
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj,
        "tabla" => "repartidores"
      ],
      [
        "campo_nombre" => "telefono_repartidor",
        "campo_valor" => $this->telefono_repartidor,
        "formulario_nombre" => "Teléfono del repartidor",
        "requerido" => true,
        "minimo" => minRegexTelefono,
        "maximo" => maxRegexTelefono,
        "expresion_re" => regexTelefono,
      ],
    ];
    $respuesta = $this->limpiar_Verificar($campos);

    if ($respuesta !== false) {
      return $respuesta;
      exit();
    } else {
      return $this->actualizarRepartidoresP();
    }
  }
  public function eliminarRepartidor($cedula)
  {
    $this->cedula_repartidor = $cedula;

    $campos = [
      [
        "campo_nombre" => "cedula_repartidor",
        "campo_valor" => $this->cedula_repartidor,
        "formulario_nombre" => "Cédula del Repartidor",
        "requerido" => true,
        "minimo" => minRegexCedulaRifLetra,
        "maximo" => maxRegexCedulaRifLetra,
        "expresion_re" => regexCedulaRifLetra,
        "tabla" => "repartidores",
        "debeExistir" => true,
        "camposDiferentes" => 1
      ]
    ];

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
      exit();
    } else {
      return $this->eliminarRepartidoresP();
    }
  }

  // PRIVADOS
  private function seleccionarRepartidorP()
  {
    if ($this->cedula_repartidor == null || $this->cedula_repartidor == "") {
      $resultado = $this->seleccionarDatos([
        'campos' => '
          cedula_repartidor, nombre_repartidor, 
          apellido_repartidor, telefono_repartidor
        ',
        'tabla' => 'repartidores',
      ]);
      $repartidores = $resultado->fetchAll(PDO::FETCH_ASSOC);
      return $repartidores;
    } else {
      $resultado = $this->seleccionarDatos2([
        'campos' => '
          cedula_repartidor, nombre_repartidor,
          apellido_repartidor, telefono_repartidor
        ',
        'tabla' => 'repartidores',
        'WHERE' => [
          "cedula_repartidor" => $this->cedula_repartidor,
        ]
      ]);
      if ($resultado->rowCount() <= 0) {
        $alerta = [
          "tipo" => "simple",
          "titulo" => "Repartidor no encontrado",
          "texto" => "El repartidor que ha intentado buscar no se encuentra en la base de datos",
          "icono" => "error"
        ];
        return $alerta;
        exit();
      } else {
        $repartidores = $resultado->fetch(PDO::FETCH_ASSOC);

        //$codigoCedulaRif = preg_replace('/[0-9]/', '', $repartidores['cedula_repartidor']);
        // $cedulaRif = preg_replace('/[a-zA-Z]/', '', $repartidores['cedula_repartidor']);
        //$repartidores['codigo_cedula_rif_repartidor'] = $codigoCedulaRif;
        //$repartidores['cedula_repartidor'] = $cedulaRif;
      }
      return $repartidores;
    }
  }
  private function registrarRepartidoresP()
  {
    $datos_registro_repartidor = [
      [
        "campo_nombre" => "cedula_repartidor",
        "campo_marcador" => ":cedula_repartidor",
        "campo_valor" => $this->cedula_repartidor
      ],
      [
        "campo_nombre" => "nombre_repartidor",
        "campo_marcador" => ":nombre_repartidor",
        "campo_valor" => $this->nombre_repartidor,
        "ponerEnMayusculas" => true
      ],
      [
        "campo_nombre" => "apellido_repartidor",
        "campo_marcador" => ":apellido_repartidor",
        "campo_valor" => $this->apellido_repartidor,
        "ponerEnMayusculas" => true
      ],
      [
        "campo_nombre" => "telefono_repartidor",
        "campo_marcador" => ":telefono_repartidor",
        "campo_valor" => $this->telefono_repartidor
      ],
    ];

    $condicion = [
      "condicion_campo" => "cedula_repartidor",
      "condicion_marcador" => ":cedula_repartidor",
      "condicion_valor" => $this->cedula_repartidor
    ];

    $ultimoID = $this->guardarDatos('repartidores', $datos_registro_repartidor, $condicion);

    if ($ultimoID !== false && $ultimoID > 0) {
      $alerta = [
        "tipo" => "limpiar",
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
  private function actualizarRepartidoresP()
  {
    $instruccionesBD = [
      "campos" => "cedula_repartidor",
      "tabla" => "repartidores",
      'WHERE' => [
        [
          "condicion_campo" => "cedula_repartidor",
          "condicion_marcador" => ":cedula_repartidor",
          "condicion_valor" => $this->cedula_repartidor,
          "comparacion" => "="
        ]
      ]
    ];
    $resultado = $this->seleccionarDatos($instruccionesBD);

    $instruccionesBD = [
      "tabla" => "repartidores",
      "datos" => [
        [
          "campo_nombre" => "cedula_repartidor",
          "campo_marcador" => ":cedula_repartidor",
          "campo_valor" => $this->cedula_repartidor
        ],
        [
          "campo_nombre" => "nombre_repartidor",
          "campo_marcador" => ":nombre_repartidor",
          "campo_valor" => $this->nombre_repartidor,
          "ponerEnMayusculas" => true
        ],
        [
          "campo_nombre" => "apellido_repartidor",
          "campo_marcador" => ":apellido_repartidor",
          "campo_valor" => $this->apellido_repartidor,
          "ponerEnMayusculas" => true
        ],
        [
          "campo_nombre" => "telefono_repartidor",
          "campo_marcador" => ":telefono_repartidor",
          "campo_valor" => $this->telefono_repartidor
        ],
      ],
      "WHERE" => [
        [
          "condicion_campo" => "cedula_repartidor",
          "condicion_marcador" => ":cedula_repartidor",
          "condicion_valor" => $this->cedula_repartidor,
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
        "titulo" => "Repartidor actualizado",
        "texto" => "El repartidor ha sido actualizado exitosamente",
        "icono" => "success",
      ];
      $this->commit();
    }
    return $alerta;
  }
  private function eliminarRepartidoresP()
  {
    $eliminarRepartidor = $this->eliminarDatos('repartidores', 'cedula_repartidor', $this->cedula_repartidor);
    if ($eliminarRepartidor->rowCount() == 1) {
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
