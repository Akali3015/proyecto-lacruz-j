<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use src\modelos\rutasModelo;

class sucursalesEmpresasEnviosModelo extends conexion {

  private int $idSucursal = 0;
  private int $idEmpresaEnvios = 0;
  private string $latitudSucursal = '';
  private string $longitudSucursal = '';
  private string $nombreSucursal = '';

  public function validarSucursalesEmpresasEnvios(array $instruccionesVal) {
    [
      'infoVal' => &$infoVal,
      'camposVal' => &$camposVal,
    ] = $instruccionesVal;
    $funcionAsignadora = function ($nombreCampo, &$valor) {
      $claveVal = [
        "id_sucursal_empresa_envios" => [
          "campo_nombre" => "id_sucursal_empresa_envios",
          "campo_valor" => &$valor,
          "formulario_nombre" => "sucursal de empresas de envíos",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "sucursales_empresas_envios",
          "debeExistir" => true,
          "debeSerUnico" => true
        ],
        "id_empresa_envios" => [
          "campo_nombre" => "id_empresa_envios",
          "campo_valor" => &$valor,
          "formulario_nombre" => "empresa de envíos",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "empresas_envios",
          "debeExistir" => true,
        ],
        "nombre_sucursal_empresa" => [
          "campo_nombre" => "nombre_sucursal_empresa",
          "campo_valor" => &$valor,
          "formulario_nombre" => "nombre de la sucursal",
          "requerido" => true,
          "minimo" => minRegexNombreObj,
          "maximo" => maxRegexNombreObj,
          "expresion_re" => regexNombreObj,
          "tabla" => "sucursales_empresas_envios",
          "debeSerUnico" => true,
        ],
        "latitud_sucursal" => [
          "campo_valor" => &$valor,
          "formulario_nombre" => "latitud de la dirección",
          "requerido" => true,
          "minimo" => minRegexCoordenadas,
          "maximo" => maxRegexCoordenadas,
          "expresion_re" => regexCoordenadas,
        ],
        "longitud_sucursal" => [
          "campo_valor" => &$valor,
          "formulario_nombre" => "longitud de la dirección",
          "requerido" => true,
          "minimo" => minRegexCoordenadas,
          "maximo" => maxRegexCoordenadas,
          "expresion_re" => regexCoordenadas,
        ],
      ];
      return $claveVal[$nombreCampo];
    };
    $campos = [];
    foreach ($camposVal as $campo => $valorForm) {
      if (is_numeric($campo)) $campo = $valorForm;
      $campos[] =  $funcionAsignadora($campo, $infoVal[$valorForm]);
    }
    return $this->limpiar_Verificar($campos);
  }
  public function seleccionarSucursalesEmpresasEnvios($info = NULL) {
    if (($info['id_sucursal_empresa_envios'] ?? '') != "") {
      $resultado = $this->validarSucursalesEmpresasEnvios([
        'infoVal' => &$info,
        'camposVal' => [
          'id_sucursal_empresa_envios',
        ],
      ]);
      if ($resultado) return $resultado;
      $this->idSucursal = $info['id_sucursal_empresa_envios'];
    }
    return $this->seleccionarSucursalesEmpresasEnviosP();
  }
  public function registrarSucursalesEmpresasEnvios(array $info) {
    $resultado = $this->validarSucursalesEmpresasEnvios([
      'infoVal' => &$info,
      'camposVal' => [
        'id_empresa_envios',
        'nombre_sucursal_empresa',
        'latitud_sucursal',
        'longitud_sucursal',
      ],
    ]);
    if ($resultado) return $resultado;
    $this->idEmpresaEnvios = $info['id_empresa_envios'];
    $this->nombreSucursal = $info['nombre_sucursal_empresa'];
    $this->latitudSucursal = $info['latitud_sucursal'];
    $this->longitudSucursal = $info['longitud_sucursal'];

    return $this->registrarSucursalesEmpresasEnviosP();
  }
  public function actualizarSucursalesEmpresasEnvios(array $info) {
    $resultado = $this->validarSucursalesEmpresasEnvios([
      'infoVal' => &$info,
      'camposVal' => [
        'id_sucursal_empresa_envios',
        'id_empresa_envios',
        'nombre_sucursal_empresa',
        'latitud_sucursal',
        'longitud_sucursal',
      ],
    ]);
    if ($resultado) return $resultado;
    $this->idSucursal = $info['id_sucursal_empresa_envios'];
    $this->idEmpresaEnvios = $info['id_empresa_envios'];
    $this->nombreSucursal = $info['nombre_sucursal_empresa'];
    $this->latitudSucursal = $info['latitud_sucursal'];
    $this->longitudSucursal = $info['longitud_sucursal'];

    return $this->actualizarSucursalesEmpresasEnviosP();
  }
  public function eliminarSucursalesEmpresasEnvios(array $info) {
    $resultado = $this->validarSucursalesEmpresasEnvios([
      'infoVal' => &$info,
      'camposVal' => [
        'id_sucursal_empresa_envios',
      ],
    ]);
    if ($resultado) return $resultado;
    $this->idSucursal = $info['id_sucursal_empresa_envios'];
    return $this->eliminarSucursalesEmpresasEnviosP();
  }

  //-- PRIVADOS [ ENCAPSULAMIENTO ]--//
  private function seleccionarSucursalesEmpresasEnviosP() {
    if ($this->idSucursal == null || $this->idSucursal == "") {
      return $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'sucursales_empresas_envios AS se',
        'datosJoins' => [
          'empresas_envios AS ee' => 'se.id_empresa_envios = ee.id_empresa_envios'
        ]
      ])->fetchAll();
    } else {
      return $this->seleccionarDatos2([
        'campos' => '
          seen.id_sucursal_empresa_envios,
          seen.id_empresa_envios, seen.nombre_sucursal_empresa,
          latd.coordenada_latitud, lond.coordenada_longitud
        ',
        'tabla' => 'sucursales_empresas_envios as seen',
        'WHERE' => [
          "seen.id_sucursal_empresa_envios" => $this->idSucursal,
        ],
        'datosJoins' => [
          'direcciones as di' => 'seen.id_direccion = di.id_direccion',
          'latitudes_direcciones as latd' => 'di.id_latitud_direccion = latd.id_latitud_direccion',
          'longitudes_direcciones as lond' => 'di.id_longitud_direccion = lond.id_longitud_direccion',
        ]
      ])->fetch();
    }
  }
  private function registrarSucursalesEmpresasEnviosP() {
    $objBitacora = new bitacoraModelo();
    $error = function () use ($objBitacora) {
      $this->rollback();
      $objBitacora->registrarBitacora('sucursalesEmpresasEnvios', 'registrar', 'Fallido', true);
    };
    $idLatitud = $this->VEYSNEC([
      'tabla' => 'latitudes_direcciones',
      'campos' => 'id_latitud_direccion',
      'WHERE' => [
        'coordenada_latitud' => $this->latitudSucursal
      ],
    ]);
    $idLongitud = $this->VEYSNEC([
      'tabla' => 'longitudes_direcciones',
      'campos' => 'id_longitud_direccion',
      'WHERE' => [
        'coordenada_longitud' => $this->longitudSucursal
      ],
    ]);
    $nroKm = $this->calcularKmEntreCoordenadas([
      'lat1' => $this->latitudSucursal,
      'lon1' => $this->longitudSucursal,
      'lat2' => coorJLACRUZ['latitud'],
      'lon2' => coorJLACRUZ['longitud'],
    ]);
    $objRutas = new rutasModelo();
    $rutaBD = $objRutas->seleccionarRutas([
      'km_recorrido' => $nroKm,
      'tipoConsulta' => 'porKm'
    ]);
    if (!$rutaBD) {
      $error();
      return [
        'tipo' => 'simple',
        'titulo' => 'Sin rutas estipuladas',
        'texto' => 'No hay rutas estipuladas para esa distancia',
        'icono' => 'error',
      ];
    }
    $idDireccion = $this->VEYSNEC([
      'tabla' => 'direcciones',
      'WHERE' => [
        'id_latitud_direccion' => $idLatitud,
        'id_longitud_direccion' => $idLongitud,
        'id_ruta' => $rutaBD['id_ruta']
      ],
      'campos' => 'id_direccion',
    ]);
    $ultimoId = $this->guardarDatos2([
      'tabla' => 'sucursales_empresas_envios',
      'datos' => [
        "id_empresa_envios" => $this->idEmpresaEnvios,
        "id_direccion" => $idDireccion,
        "nombre_sucursal_empresa" => $this->nombreSucursal,
      ]
    ]);
    if ($ultimoId == false || $ultimoId <= 0) {
      $error();
      return [
        "tipo" => "simple",
        "titulo" => "Sucursal no registrado",
        "texto" => "La sucursal no ha sido registrada exitosamente",
        "icono" => "error",
      ];
    }

    $objBitacora->registrarBitacora('sucursalesEmpresasEnvios', 'Registrar', 'Éxito');
    $this->commit();
    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Sucursal registrado",
      "texto" => "La sucursal ha sido registrada exitosamente",
      "icono" => "success",
    ];
  }
  private function actualizarSucursalesEmpresasEnviosP() {
    $objBitacora = new bitacoraModelo();
    $error = function () use ($objBitacora) {
      $this->rollback();
      $objBitacora->registrarBitacora('sucursalesEmpresasEnvios', 'registrar', 'Fallido', true);
    };
    $idLatitud = $this->VEYSNEC([
      'tabla' => 'latitudes_direcciones',
      'campos' => 'id_latitud_direccion',
      'WHERE' => [
        'coordenada_latitud' => $this->latitudSucursal
      ],
    ]);
    $idLongitud = $this->VEYSNEC([
      'tabla' => 'longitudes_direcciones',
      'campos' => 'id_longitud_direccion',
      'WHERE' => [
        'coordenada_longitud' => $this->longitudSucursal
      ],
    ]);
    $nroKm = $this->calcularKmEntreCoordenadas([
      'lat1' => $this->latitudSucursal,
      'lon1' => $this->longitudSucursal,
      'lat2' => coorJLACRUZ['latitud'],
      'lon2' => coorJLACRUZ['longitud'],
    ]);
    $objRutas = new rutasModelo();
    $rutaBD = $objRutas->seleccionarRutas([
      'km_recorrido' => $nroKm,
      'tipoConsulta' => 'porKm'
    ]);
    if (!$rutaBD) {
      $error();
      return [
        'tipo' => 'simple',
        'titulo' => 'Sin rutas estipuladas',
        'texto' => 'No hay rutas estipuladas para esa ubicación',
        'icono' => 'error',
      ];
    }
    $idDireccion = $this->VEYSNEC([
      'tabla' => 'direcciones',
      'WHERE' => [
        'id_latitud_direccion' => $idLatitud,
        'id_longitud_direccion' => $idLongitud,
        'id_ruta' => $rutaBD['id_ruta']
      ],
      'campos' => 'id_direccion',
    ]);
    $ultimoId = $this->actualizarDatos2([
      'tabla' => 'sucursales_empresas_envios',
      'datos' => [
        "id_empresa_envios" => $this->idEmpresaEnvios,
        "id_direccion" => $idDireccion,
        "nombre_sucursal_empresa" => $this->nombreSucursal,
      ],
      'WHERE' => [
        'id_sucursal_empresa_envios' => $this->idSucursal
      ]
    ]);
    if ($ultimoId == false || $ultimoId <= 0) {
      $error();
      return [
        "tipo" => "simple",
        "titulo" => "Sucursal no actualizada",
        "texto" => "La sucursal no ha sido actualizada",
        "icono" => "warning",
      ];
    }

    $objBitacora->registrarBitacora('sucursalesEmpresasEnvios', 'Actualizar', 'Éxito');
    $this->commit();
    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Sucursal actualizada",
      "texto" => "La sucursal ha sido actualizada exitosamente",
      "icono" => "success",
    ];
  }
  private function eliminarSucursalesEmpresasEnviosP() {
    $objBitacora = new bitacoraModelo();
    $eliminarUsuario = $this->eliminarDatos2([
      "tabla" => "sucursales_empresas_envios",
      "WHERE" => [
        "id_sucursal_empresa_envios" => $this->idSucursal
      ]
    ]);
    if ($eliminarUsuario <= 0) { /*Para verificar si se hizo la eliminación o no */
      $this->rollback();
      $objBitacora->registrarBitacora('sucursalesEmpresasEnvios', 'eliminar sucursal con id: ' . $this->idSucursal, 'Error', true);
      return [
        "tipo" => "simple",
        "titulo" => "Sucursal no encontrada",
        "texto" => "La sucursal no existe en la Base de Datos",
        "icono" => "error"
      ];
    }

    $objBitacora->registrarBitacora('sucursalesEmpresasEnvios', 'eliminar Sucursal con id: ' . $this->idSucursal, 'Éxito');
    $this->commit();
    return [
      "tipo" => "simple",
      "titulo" => "Sucursal eliminada",
      "texto" => "La sucursal ha sido eliminada con éxito",
      "icono" => "success"
    ];
  }
}
