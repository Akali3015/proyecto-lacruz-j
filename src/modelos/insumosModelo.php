<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use PDO;
use PDOException;
use Exception;

class insumosModelo extends conexion
{
  private $idInsumo;
  private $nombreInsumo;
  private $precioInsumo;
  private $stockInsumo;

  public function seleccionarInsumos($id = null)
  {
    $this->idInsumo = $id;

    if ($this->idInsumo != null && $this->idInsumo != "") {
      $campos = [
        [
          "campo_nombre" => 'id_insumo',
          "campo_valor" => $this->idInsumo,
          "formulario_nombre" => "id del insumo",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => 'insumos',
          "debeExistir" => true
        ]
      ];

      $respuesta = $this->limpiar_Verificar($campos);
      if ($respuesta !== false) {
        return $respuesta;
        exit();
      }
    }
    return $this->seleccionarInsumosP();
  }
  public function registrarInsumos($nombre, $precio, $stock)
  {
    try {
      $this->nombreInsumo = $nombre;
      $this->precioInsumo = $precio;
      $this->stockInsumo = $stock;

      $campos = [
        [
          "campo_nombre" => "nombre_insumo",
          "campo_valor" => $this->nombreInsumo,
          "formulario_nombre" => "nombre del insumo",
          "requerido" => true,
          "minimo" => minRegexNombreObj,
          "maximo" => maxRegexNombreObj,
          "expresion_re" => regexNombreObj,
          "tabla" => "insumos",
          "debeSerUnico" => true,
        ],
        [
          "campo_nombre" => "precio_insumo",
          "campo_valor" => $this->precioInsumo,
          "formulario_nombre" => "precio del insumo",
          "requerido" => true,
          "minimo" => minRegexPrecio,
          "maximo" => maxRegexPrecio,
          "expresion_re" => regexPrecio,
          "tabla" => "insumos",
        ],
        [
          "campo_nombre" => "stock_insumo",
          "campo_valor" => $this->stockInsumo,
          "formulario_nombre" => "stock del insumo",
          "requerido" => true,
          "minimo" => minRegexCantidadItem,
          "maximo" => maxRegexCantidadItem,
          "expresion_re" => regexCantidadItem,
          "tabla" => "insumos",
        ],
      ];

      $respuesta = $this->limpiar_Verificar($campos);
      if ($respuesta !== false) {
        return $respuesta;
        exit();
      } else {
        return $this->registrarInsumosP();
      }
    } catch (PDOException $e) {
      error_log("Error: " . $e->getMessage());
      throw new Exception("Error al registrar el insumo en la base de datos: " . $e->getMessage());
    }
  }
  public function actualizarInsumos($id, $nombre, $precio, $stock)
  {
    $this->idInsumo = $id;
    $this->nombreInsumo = $nombre;
    $this->precioInsumo = $precio;
    $this->stockInsumo = $stock;

    $campos = [
      [
        "campo_nombre" => "id_insumo",
        "campo_valor" => $this->idInsumo,
        "formulario_nombre" => "id del insumo",
        "requerido" => true,
        "minimo" => minRegexId,
        "maximo" => maxRegexId,
        "expresion_re" => regexId,
        "tabla" => "insumos",
        "debeExistir" => true,
      ],
      [
        "campo_nombre" => "nombre_insumo",
        "campo_valor" => $this->nombreInsumo,
        "formulario_nombre" => "nombre del insumo",
        "requerido" => true,
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj,
        "tabla" => "insumos",
        "debeSerUnico" => true,
      ],
      [
        "campo_nombre" => "precio_insumo",
        "campo_valor" => $this->precioInsumo,
        "formulario_nombre" => "precio del insumo",
        "requerido" => true,
        "minimo" => minRegexPrecio,
        "maximo" => maxRegexPrecio,
        "expresion_re" => regexPrecio,
        "tabla" => "insumos",
      ],
      [
        "campo_nombre" => "stock_insumo",
        "campo_valor" => $this->stockInsumo,
        "formulario_nombre" => "stock del insumo",
        "requerido" => true,
        "minimo" => minRegexCantidadItem,
        "maximo" => maxRegexCantidadItem,
        "expresion_re" => regexCantidadItem,
        "tabla" => "insumos",
      ],
    ];

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
      exit();
    } else {
      return $this->actualizarInsumosP();
    }
  }
  public function eliminarInsumos($id)
  {
    $this->idInsumo = $id;

    $campos = [
      [
        "campo_nombre" => "id_insumo",
        "campo_valor" => $this->idInsumo,
        "formulario_nombre" => "id del insumo",
        "requerido" => true,
        "minimo" => minRegexId,
        "maximo" => maxRegexId,
        "expresion_re" => regexId,
        "debeExistir" => true,
        "tabla" => "insumos",
      ]
    ];

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
      exit();
    } else {
      return $this->eliminarInsumosP();
    }
  }

  //-- PRIVADOS [ ENCAPSULAMIENTO ]--//
  private function seleccionarInsumosP()
  {
    if ($this->idInsumo == null || $this->idInsumo == "") {
      $instruccionesBD = [
        'campos' => 'id_insumo, nombre_insumo, precio_insumo, stock_insumo',
        'tabla' => 'insumos',
      ];
      $resultado = $this->seleccionarDatos($instruccionesBD);
      $insumos = $resultado->fetchAll(PDO::FETCH_ASSOC);
      return $insumos;
    } else {

      $instruccionesBD = [
        'campos' => 'id_insumo, nombre_insumo, precio_insumo, stock_insumo',
        'tabla' => 'insumos',
        'WHERE' =>
        [
          [
            "condicion_campo" => "id_insumo",
            "condicion_marcador" => ":ID",
            "condicion_valor" => $this->idInsumo,
            "comparacion" => "="
          ]
        ]
      ];
      $resultado = $this->seleccionarDatos($instruccionesBD);
      if ($resultado->rowCount() <= 0) {
        $alerta = [
          "tipo" => "simple",
          "titulo" => "Insumo no encontrado",
          "texto" => "El insumo que ha intentado buscar no se encuentra en la base de datos",
          "icono" => "error"
        ];
        return $alerta;
        exit();
      } else {
        $insumo = $resultado->fetch(PDO::FETCH_ASSOC);
      }
      return $insumo;
    }
  }
  private function registrarInsumosP()
  {
    $datos_registro_insumos = [
      [
        "campo_nombre" => "nombre_insumo",
        "campo_marcador" => ":nombre",
        "campo_valor" => $this->nombreInsumo,
        "ponerEnMayusculas" => true,
      ],
      [
        "campo_nombre" => "precio_insumo",
        "campo_marcador" => ":precio",
        "campo_valor" => $this->precioInsumo,
      ],
      [
        "campo_nombre" => "stock_insumo",
        "campo_marcador" => ":stock",
        "campo_valor" => $this->stockInsumo,
      ],
    ];

    $ultimoId = $this->guardarDatos('insumos', $datos_registro_insumos);
    $modeloBitacora = new bitacoraModelo();
    if ($ultimoId !== false && $ultimoId > 0) {
      $alerta = [
        "tipo" => "limpiarYcerrar",
        "titulo" => "Insumo registrado",
        "texto" => "El insumo ha sido registrado exitosamente",
        "icono" => "success",
      ];
      $modeloBitacora->registrarBitacora("Insumos", "Registrar", "Exito");
      $this->commit();
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Insumo no registrado",
        "texto" => "El insumo no ha sido registrado exitosamente",
        "icono" => "error",
      ];
      $modeloBitacora->registrarBitacora("Insumos", "Registrar", "Fallido");
    }
    return $alerta;
  }
  private function actualizarInsumosP()
  {

    $instruccionesBD = [
      "tabla" => "insumos",
      "datos" => [
        [
          "campo_nombre" => "id_insumo",
          "campo_marcador" => ":Id",
          "campo_valor" => $this->idInsumo,
          "debeExistir" => true,
        ],
        [
          "campo_nombre" => "nombre_insumo",
          "campo_marcador" => ":Nombre",
          "campo_valor" => $this->nombreInsumo,
          "ponerEnMayusculas" => true,
        ],
        [
          "campo_nombre" => "precio_insumo",
          "campo_marcador" => ":precio",
          "campo_valor" => $this->precioInsumo,
        ],
        [
          "campo_nombre" => "stock_insumo",
          "campo_marcador" => ":Stock",
          "campo_valor" => $this->stockInsumo,
        ],
      ],
      "condiciones" => [
        [
          "condicion_campo" => "id_insumo",
          "condicion_marcador" => ":Id",
          "condicion_valor" => $this->idInsumo,
          "comparacion" => "="
        ]
      ]
    ];
    $resultado = $this->actualizarDatos($instruccionesBD);
    $modeloBitacora = new bitacoraModelo();
    if ($resultado == false || $resultado <= 0) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Sin cambios realizados",
        "texto" => "No se realizó ningún cambio en el insumo",
        "icono" => "warning",
      ];
      $modeloBitacora->registrarBitacora("Insumos", "Actualizar", "Fallido");
    } else {
      $alerta = [
        "tipo" => "limpiarYcerrar",
        "titulo" => "Insumo actualizado",
        "texto" => "El insumo ha sido actualizado exitosamente",
        "icono" => "success",
      ];
      $modeloBitacora->registrarBitacora("Insumos", "Actualizar", "Exito");
      $this->commit();
    }
    return $alerta;
  }
  private function eliminarInsumosP()
  {
    $eliminarInsumo = $this->eliminarDatos("insumos", "id_insumo", $this->idInsumo);
    $modeloBitacora = new bitacoraModelo();
    if ($eliminarInsumo->rowCount() == 1) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Insumo eliminado",
        "texto" => "El insumo ha sido eliminado con éxito",
        "icono" => "success"
      ];
      $modeloBitacora->registrarBitacora("Insumos", "Eliminar", "Exito");
      $this->commit();
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Insumo no encontrado",
        "texto" => "El insumo no existe en la Base de Datos",
        "icono" => "error"
      ];
      $modeloBitacora->registrarBitacora("Insumos", "Eliminar", "Fallido");
    }
    return $alerta;
  }
}
