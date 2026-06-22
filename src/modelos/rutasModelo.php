<?php

namespace src\modelos;

use src\config\connect\conexion;

class rutasModelo extends conexion {
  private int $idRuta = 0;
  private string $nombreRuta = '';
  private float $precioRuta = 0;
  private float $minimoKmRuta = 0;
  private float $maximoKmRuta = 0;
  private float $kmRecorrido = 0;

  public function validarRutas(array $instruccionesVal) {
    [
      'infoVal' => &$infoVal,
      'camposVal' => &$camposVal,
    ] = $instruccionesVal;
    $funcionAsignadora = function ($nombreCampo, &$valor) {
      $claveVal = [
        "id_ruta" => [
          "campo_nombre" => "id_ruta",
          "campo_valor" => &$valor,
          "formulario_nombre" => "id de la ruta",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "rutas",
          "debeExistir" => true,
          "debeSerUnico" => true
        ],
        "nombre_ruta" => [
          "campo_nombre" => "nombre_ruta",
          "campo_valor" => &$valor,
          "formulario_nombre" => "nombre de la ruta",
          "requerido" => true,
          "minimo" => minRegexNombreObj,
          "maximo" => maxRegexNombreObj,
          "expresion_re" => regexNombreObj,
          "tabla" => "rutas",
          "debeSerUnico" => true,
        ],
        "precio_ruta" => [
          "campo_valor" => &$valor,
          "formulario_nombre" => "precio de la ruta",
          "requerido" => true,
          "minimo" => minRegexPrecio,
          "maximo" => maxRegexPrecio,
          "expresion_re" => regexPrecio,
          'comaPunto' => true,
        ],
        "minimo_km_ruta" => [
          "campo_valor" => &$valor,
          "formulario_nombre" => "mínimo de km de la ruta",
          "requerido" => true,
          "minimo" => minRegexPrecio,
          "maximo" => maxRegexPrecio,
          "expresion_re" => regexPrecio,
          'comaPunto' => true,
        ],
        "maximo_km_ruta" => [
          "campo_valor" => &$valor,
          "formulario_nombre" => "máximo de km de la ruta",
          "requerido" => true,
          "minimo" => minRegexPrecio,
          "maximo" => maxRegexPrecio,
          "expresion_re" => regexPrecio,
          'comaPunto' => true,
        ],
        "km_recorrido" => [
          "campo_valor" => &$valor,
          "formulario_nombre" => "la cantidad de km recorridos",
          "requerido" => true,
          "minimo" => minRegexPrecio,
          "maximo" => maxRegexPrecio,
          "expresion_re" => regexPrecio,
        ],
      ];
      return $claveVal[$nombreCampo];
    };
    $campos = [];
    foreach ($camposVal as $campo) {
      $campos[] = $funcionAsignadora($campo, $infoVal[$campo]);
    }
    return $this->limpiar_Verificar($campos);
  }
  public function seleccionarRutas(array $info) {
    $campos = [];
    if (isset($info['id_ruta'])) {
      $campos[] = 'id_ruta';
    }
    if (isset($info['km_recorrido'])) {
      $campos[] = 'km_recorrido';
    }
    if ($campos != []) {
      $resultado = $this->validarRutas([
        'infoVal' => &$info,
        'camposVal' => $campos,
      ]);
      if ($resultado) return $resultado;
    }

    $this->idRuta = $info['id_ruta'] ?? 0;
    $this->kmRecorrido = $info['km_recorrido'] ?? 0;
    return $this->seleccionarRutasP($info);
  }
  public function registrarRutas(array $info) {
    $resultado = $this->validarRutas([
      'infoVal' => &$info,
      'camposVal' => [
        'nombre_ruta',
        'precio_ruta',
        'minimo_km_ruta',
        'maximo_km_ruta'
      ],
    ]);
    if ($resultado) return $resultado;

    $this->nombreRuta = $info['nombre_ruta'];
    $this->precioRuta = (float)$info['precio_ruta'];
    $this->minimoKmRuta = (float)$info['minimo_km_ruta'];
    $this->maximoKmRuta = (float)$info['maximo_km_ruta'];
    return $this->registrarRutasP();
  }
  public function actualizarRutas(array $info) {

    $resultado = $this->validarRutas([
      'infoVal' => &$info,
      'camposVal' => [
        'id_ruta',
        'nombre_ruta',
        'precio_ruta',
        'minimo_km_ruta',
        'maximo_km_ruta'
      ],
    ]);
    if ($resultado) return $resultado;

    $this->idRuta = $info['id_ruta'];
    $this->nombreRuta = $info['nombre_ruta'];
    $this->precioRuta = (float) $info['precio_ruta'];
    $this->minimoKmRuta = (float)$info['minimo_km_ruta'];
    $this->maximoKmRuta = (float)$info['maximo_km_ruta'];


    return $this->actualizarRutasP();
  }
  public function eliminarRutas(array $info) {
    $resultado = $this->validarRutas([
      'infoVal' => &$info,
      'camposVal' => [
        'id_ruta',
      ],
    ]);
    if ($resultado) return $resultado;

    $this->idRuta = $info['id_ruta'];
    return $this->eliminarRutasP();
  }

  //-- PRIVADOS [ ENCAPSULAMIENTO ]--//
  private function seleccionarRutasP(array $info) {
    if ($this->idRuta == null || $this->idRuta == "") {
      switch ($info['tipoConsulta'] ?? '') {
        case 'porKm':
          $this->kmRecorrido = ceil($this->kmRecorrido);
          $resultado = $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'rutas',
            'WHERE' => [
              'minimo_km_ruta' => '<= ' . $this->kmRecorrido,
              'maximo_km_ruta' => '>= ' . $this->kmRecorrido,
            ],
            'ORDER' => 'maximo_km_ruta DESC'
          ])->fetch();
          if (!isset($resultado['precio_ruta'])) {
            return  [
              'tipo' => 'simple',
              'titulo' => 'Sin rutas disponibles',
              'texto' => 'No hay rutas estipuladas para esa direccion',
              'icono' => 'error'
            ];
          }
          return $resultado;
        case 'porCoordenadas':
          $infoDireccion = $this->calcularKmPorCarretera($info['coordenadas']);
          if (isset($infoDireccion['icono'])) return $infoDireccion;
          return [
            'km_recorrido' => $infoDireccion['km_recorrido'],
            'nombre_direccion' => $infoDireccion['nombre_direccion'],
          ];
        default:
          return $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'rutas',
          ])->fetchAll();
      }
    } else {
      switch ($info['tipoConsulta'] ?? '') {
        case 'porFecha':
          return $this->seleccionarDatos2([
            'campos' => '
              ru.id_ruta,ru.nombre_ruta,ru.minimo_km_ruta,ru.maximo_km_ruta,
              (
                SELECT pr.precio_ruta FROM precios_rutas as pr
                WHERE pr.id_ruta = ru.id_ruta && fecha_cambio <= "' . $info['fecha'] . '"
                ORDER BY pr.id_precio_ruta DESC
                LIMIT 1
              ) as precio_ruta_fecha
            ',
            'tabla' => 'rutas as ru',
            'WHERE' => [
              'id_ruta' => $this->idRuta,
            ],
          ])->fetch();
        default:
          return $this->seleccionarDatos2([
            'campos' => '*',
            'tabla' => 'rutas',
            'WHERE' => [
              "id_ruta" => $this->idRuta,
            ]
          ])->fetch();
      }
    }
  }
  private function registrarRutasP() {
    $objBitacora = new bitacoraModelo();
    $alertaError = function () use ($objBitacora) {
      $this->rollback();
      $objBitacora->registrarBitacora('rutas', 'registrar', 'fallido', true);
      return [
        "tipo" => "simple",
        "titulo" => "Ruta no registrada",
        "texto" => "La ruta no ha sido registrada exitosamente",
        "icono" => "error",
      ];
    };
    $resultado = $this->guardarDatos2([
      'tabla' => 'rutas',
      'datos' => [
        "nombre_ruta" => $this->nombreRuta,
        "precio_ruta" => $this->precioRuta,
        "minimo_km_ruta" => $this->minimoKmRuta,
        "maximo_km_ruta" => $this->maximoKmRuta,
      ]
    ]);
    if ($resultado == false || $resultado <= 0) return $alertaError();
    $rb = $objBitacora->registrarBitacora('rutas', 'registrar', 'éxito');
    if (($rb['icono'] ?? '') == 'error') return $rb;

    $this->commit();
    return   [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Ruta registrada",
      "texto" => "La ruta ha sido registrado exitosamente",
      "icono" => "success",
    ];
  }
  private function actualizarRutasP() {
    $objBitacora = new bitacoraModelo();
    $alertaError = function () use ($objBitacora) {
      $this->rollback();
      $objBitacora->registrarBitacora('rutas', 'actualizar', 'fallido', true);
      return [
        "tipo" => "simple",
        "titulo" => "Sin cambios realizados",
        "texto" => "No se realizó ningún cambio en la ruta",
        "icono" => "warning",
      ];
    };
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
    if ($resultado == false || $resultado <= 0) return $alertaError();
    $rb = $objBitacora->registrarBitacora('rutas', 'actualizar', 'éxito');
    if (($rb['icono'] ?? '') == 'error') return $rb;
    $this->commit();;
    return  [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Ruta actualizada",
      "texto" => "La ruta ha sido actualizada exitosamente",
      "icono" => "success",
    ];
  }
  private function eliminarRutasP() {
    $objBitacora = new bitacoraModelo();
    $eliminarUsuario = $this->eliminarDatos2([
      'tabla' => "rutas",
      'WHERE' => [
        "id_ruta" => $this->idRuta
      ]
    ]);
    if ($eliminarUsuario <= 0) {
      $objBitacora->registrarBitacora('rutas', 'eliminar', 'fallido', true);
      return [
        "tipo" => "simple",
        "titulo" => "Ruta no encontrado",
        "texto" => "La ruta no existe en la Base de Datos",
        "icono" => "error"
      ];
    }
    $rb = $objBitacora->registrarBitacora('rutas', 'eliminar', 'éxito');
    if (($rb['icono'] ?? '') == 'error') return $rb;

    $this->commit();
    return [
      "tipo" => "simple",
      "titulo" => "Ruta eliminada",
      "texto" => "La ruta ha sido eliminada con éxito",
      "icono" => "success"
    ];
  }
}
