<?php

namespace src\modelos;

use src\config\connect\conexion;
use PDO;
use PDOException;
use Exception;

class rutasModelo extends conexion
{
  private $idRuta;
  private $nombreRuta;
  private $precioRuta;
  private $minimoKmRuta;
  private $maximoKmRuta;
  private $kmRecorrido;

  public function seleccionarRutas($id = null, $tipoConsulta = null, $km = null)
  {
    $this->idRuta = $id;
    $this->kmRecorrido = $km;

    $campos = [];
    if ($this->idRuta != null && $this->idRuta != "") {
      $campos[] = [
        "campo_nombre" => 'id_ruta',
        "campo_valor" => $this->idRuta,
        "formulario_nombre" => "id de la ruta",
        "requerido" => true,
        "minimo" => minRegexId,
        "maximo" => maxRegexId,
        "expresion_re" => regexId,
        "tabla" => 'rutas',
        "debeExistir" => true
      ];
    }
    if ($this->kmRecorrido != null && $this->kmRecorrido != "") {
      $campos[] = [
        "campo_valor" => $this->kmRecorrido,
        "formulario_nombre" => "km de recorrido",
        "requerido" => true,
        "minimo" => minRegexCantidadItem,
        "maximo" => maxRegexCantidadItem,
        "expresion_re" => regexCantidadItem,
      ];
    }
    if ($campos != []) {
      $respuesta = $this->limpiar_Verificar($campos);
      if ($respuesta !== false) {
        return $respuesta;
        exit();
      }
    }
    return $this->seleccionarRutasP($tipoConsulta);
  }
  public function registrarRutas($nombre, $precio, $minimoKm, $maximoKm)
  {
    try {
      $this->nombreRuta = $nombre;
      $this->precioRuta = $precio;
      $this->minimoKmRuta = $minimoKm;
      $this->maximoKmRuta = $maximoKm;
      $campos = [
        [
          "campo_nombre" => "nombre_ruta",
          "campo_valor" => $this->nombreRuta,
          "formulario_nombre" => "nombre de la ruta",
          "requerido" => true,
          "minimo" => minRegexNombreObj,
          "maximo" => maxRegexNombreObj,
          "expresion_re" => regexNombreObj,
          "tabla" => "rutas",
          "debeSerUnico" => true,
        ],
        [
          "campo_valor" => $this->precioRuta,
          "formulario_nombre" => "precio de la ruta",
          "requerido" => true,
          "minimo" => minRegexPrecio,
          "maximo" => maxRegexPrecio,
          "expresion_re" => regexPrecio,
        ],
        [
          "campo_valor" => $this->minimoKmRuta,
          "formulario_nombre" => "mínimo de km de la ruta",
          "requerido" => true,
          "minimo" => minRegexPrecio,
          "maximo" => maxRegexPrecio,
          "expresion_re" => regexPrecio,
        ],
        [
          "campo_valor" => $this->maximoKmRuta,
          "formulario_nombre" => "máximo de km de la ruta",
          "requerido" => true,
          "minimo" => minRegexPrecio,
          "maximo" => maxRegexPrecio,
          "expresion_re" => regexPrecio,
        ],
      ];
      $respuesta = $this->limpiar_Verificar($campos);
      if ($respuesta !== false) {
        return $respuesta;
        exit();
      } else {
        return $this->registrarRutasP();
      }
    } catch (PDOException $e) {
      error_log("Error: " . $e->getMessage());
      throw new Exception("Error al registrar el ruta en la base de datos: " . $e->getMessage());
    }
  }
  public function actualizarRutas($id, $nombre, $precio, $minimoKm, $maximoKm)
  {
    $this->idRuta = $id;
    $this->nombreRuta = $nombre;
    $this->precioRuta = $precio;
    $this->minimoKmRuta = $minimoKm;
    $this->maximoKmRuta = $maximoKm;

    //Arrays para las validaciones
    $respuesta = $this->limpiar_Verificar([
      [
        "campo_nombre" => "id_ruta",
        "campo_valor" => $this->idRuta,
        "formulario_nombre" => "id de la ruta",
        "requerido" => true,
        "minimo" => minRegexId,
        "maximo" => maxRegexId,
        "expresion_re" => regexId,
        "tabla" => "rutas",
        "debeExistir" => true,
        "debeSerUnico" => true
      ],
      [
        "campo_nombre" => "nombre_ruta",
        "campo_valor" => $this->nombreRuta,
        "formulario_nombre" => "nombre de la ruta",
        "requerido" => true,
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj,
        "tabla" => "rutas",
        "debeSerUnico" => true,
      ],
      [
        "campo_valor" => $this->precioRuta,
        "formulario_nombre" => "precio de la ruta",
        "requerido" => true,
        "minimo" => minRegexPrecio,
        "maximo" => maxRegexPrecio,
        "expresion_re" => regexPrecio,
      ],
      [
        "campo_valor" => $this->minimoKmRuta,
        "formulario_nombre" => "mínimo de km de la ruta",
        "requerido" => true,
        "minimo" => minRegexPrecio,
        "maximo" => maxRegexPrecio,
        "expresion_re" => regexPrecio,
      ],
      [
        "campo_valor" => $this->maximoKmRuta,
        "formulario_nombre" => "máximo de km de la ruta",
        "requerido" => true,
        "minimo" => minRegexPrecio,
        "maximo" => maxRegexPrecio,
        "expresion_re" => regexPrecio,
      ],
    ]);
    if ($respuesta !== false) {
      return $respuesta;
      exit();
    }
    return $this->actualizarRutasP();
  }
  public function eliminarRutas($id)
  {
    $this->idRuta = $id;
    $respuesta = $this->limpiar_Verificar([[
      "campo_nombre" => "id_ruta",
      "campo_valor" => $this->idRuta,
      "formulario_nombre" => "id de la ruta",
      "requerido" => true,
      "minimo" => minRegexId,
      "maximo" => maxRegexId,
      "expresion_re" => regexId,
      "debeExistir" => true,
      "tabla" => "rutas"
    ]]);
    if ($respuesta !== false) {
      return $respuesta;
    } else {
      return $this->eliminarRutasP();
    }
  }

  //-- PRIVADOS [ ENCAPSULAMIENTO ]--//
  private function seleccionarRutasP($tipoConsulta)
  {
    if ($this->idRuta == null || $this->idRuta == "") {
      $resultado = $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'rutas',
      ]);
      $resultado = $resultado->fetchAll(PDO::FETCH_ASSOC);
    } else {
      $instruccionesBD = [
        'campos' => '*',
        'tabla' => 'rutas',
        'WHERE' => [
          "id_ruta" => $this->idRuta,
        ]
      ];
      if ($tipoConsulta == 'porKm') {
        $instruccionesBD['WHERE'] = [
          'minimo_km_ruta' => '<= ' . $this->kmRecorrido,
          'maximo_km_ruta' => '>= ' . $this->kmRecorrido,
        ];
      };
      $resultado = $this->seleccionarDatos2($instruccionesBD);
      if ($resultado->rowCount() <= 0) {
        $alerta = [
          "tipo" => "simple",
          "titulo" => "ruta no encontrada",
          "texto" => "La ruta que ha intentado buscar no se encuentra en la base de datos",
          "icono" => "error"
        ];
        return $alerta;
      }
      $resultado = $resultado->fetch(PDO::FETCH_ASSOC);
    }
    return $resultado;
  }
  private function registrarRutasP()
  {
    $ultimoId = $this->guardarDatos2([
      'tabla' => 'rutas',
      'datos' => [
        "nombre_ruta" => $this->nombreRuta,
        "precio_ruta" => $this->precioRuta,
        "minimo_km_ruta" => $this->minimoKmRuta,
        "maximo_km_ruta" => $this->maximoKmRuta,
      ]
    ]);
    if ($ultimoId !== false && $ultimoId > 0) {
      $alerta = [
        "tipo" => "limpiarYcerrar",
        "titulo" => "Ruta registrada",
        "texto" => "La ruta ha sido registrado exitosamente",
        "icono" => "success",
      ];
      $this->commit();
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Ruta no registrada",
        "texto" => "La ruta no ha sido registrada exitosamente",
        "icono" => "error",
      ];
    }
    return $alerta;
  }
  private function actualizarRutasP()
  {
    $resultado = $this->actualizarDatos2([
      "tabla" => "rutas",
      "datos" => [
        "nombre_ruta" => $this->nombreRuta,
        "precio_ruta" => $this->precioRuta,
        "minimo_km_ruta" => $this->minimoKmRuta,
        "maximo_km_ruta" => $this->maximoKmRuta,
      ],
      "WHERE" => [
        "id_ruta" => $this->idRuta,
      ]
    ]);

    if ($resultado == false || $resultado <= 0) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Sin cambios realizados",
        "texto" => "No se realizó ningún cambio en la ruta",
        "icono" => "warning",
      ];
    } else {
      $alerta = [
        "tipo" => "limpiarYcerrar",
        "titulo" => "Ruta actualizada",
        "texto" => "La ruta ha sido actualizada exitosamente",
        "icono" => "success",
      ];
      $this->commit();
    }
    return $alerta;
  }
  private function eliminarRutasP()
  {
    $eliminarUsuario = $this->eliminarDatos2([
      'tabla' => "rutas",
      'WHERE' => [
        "id_ruta" => $this->idRuta
      ]
    ]);
    if ($eliminarUsuario->rowCount() == 1) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Ruta eliminada",
        "texto" => "La ruta ha sido eliminada con éxito",
        "icono" => "success"
      ];
      $this->commit();
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Ruta no encontrado",
        "texto" => "La ruta no existe en la Base de Datos",
        "icono" => "error"
      ];
    }
    return $alerta;
  }
}
