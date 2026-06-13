<?php

namespace src\modelos;

use src\config\connect\conexion;
use PDO;
use src\modelos\bitacoraModelo;

class empresasEnviosModelo extends conexion
{
  private string $idEmpresaEnvios = '';
  private string $nombreEmpresaEnvios = '';

  public function validarEmpresasEnvios(array $instruccionesVal)
  {
    [
      'infoVal' => &$infoVal,
      'camposVal' => &$camposVal,
    ] = $instruccionesVal;
    $funcionAsignadora = function ($nombreCampo, &$valor) {
      $claveVal = [
        'id_empresa_envios' => [
          "campo_nombre" => "id_empresa_envios",
          "campo_valor" => &$valor,
          "formulario_nombre" => "id de la empresa de envíos",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "empresas_envios",
          "debeSerUnico" => true,
          "debeExistir" => true,
        ],
        'nombre_empresa' => [
          "campo_nombre" => "nombre_empresa",
          "campo_valor" => &$valor,
          "formulario_nombre" => "nombre de la empresa",
          "requerido" => true,
          "minimo" => minRegexNombreObj,
          "maximo" => maxRegexNombreObj,
          "expresion_re" => regexNombreObj,
          "tabla" => "empresas_envios",
          "debeSerUnico" => true,
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
  public function seleccionarEmpresasEnvios(array $info)
  {
    if (($info['id_empresa_envios'] ?? '') != '') {
      $resultado = $this->validarEmpresasEnvios([
        'infoVal' => &$info,
        'camposVal' => ['id_empresa_envios'],
      ]);
      if ($resultado) return $resultado;
      $this->idEmpresaEnvios = $info['id_empresa_envios'];
    }
    return $this->seleccionarEmpresasEnviosP();
  }
  public function registrarEmpresasEnvios(array $info)
  {
    $resultado = $this->validarEmpresasEnvios([
      'infoVal' => &$info,
      'camposVal' => ['nombre_empresa'],
    ]);
    if ($resultado) return $resultado;
    $this->nombreEmpresaEnvios = $info['nombre_empresa'];
    return $this->registrarEmpresasEnviosP();
  }
  public function actualizarEmpresasEnvios(array $info)
  {
    $resultado = $this->validarEmpresasEnvios([
      'infoVal' => &$info,
      'camposVal' => ['id_empresa_envios', 'nombre_empresa'],
    ]);
    if ($resultado) return $resultado;

    $this->idEmpresaEnvios = $info['id_empresa_envios'];
    $this->nombreEmpresaEnvios = $info['nombre_empresa'];

    return $this->actualizarEmpresasEnviosP();
  }
  public function eliminarEmpresasEnvios(array $info)
  {
    $resultado = $this->validarEmpresasEnvios([
      'infoVal' => &$info,
      'camposVal' => ['id_empresa_envios'],
    ]);
    if ($resultado) return $resultado;

    $this->idEmpresaEnvios = $info['id_empresa_envios'];
    return $this->eliminarEmpresasEnviosP();
  }

  //-- PRIVADOS [ ENCAPSULAMIENTO ]--//
  private function seleccionarEmpresasEnviosP()
  {
    if ($this->idEmpresaEnvios == null || $this->idEmpresaEnvios == "") {
      $resultado = $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'v_empresas_envios_todas',
      ]);
      return $resultado->fetchAll(PDO::FETCH_ASSOC);
    } else {
      $resultado = $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'empresas_envios',
        'WHERE' => [
          "id_empresa_envios" => $this->idEmpresaEnvios,
        ]
      ]);
      if ($resultado->rowCount() <= 0) {
        return [
          "tipo" => "simple",
          "titulo" => "Empresa de envíos no encontrada",
          "texto" => "La empresa de envíos que ha intentado buscar no se encuentra en la base de datos",
          "icono" => "error"
        ];
      }
      return $resultado->fetch(PDO::FETCH_ASSOC);
    }
  }
  private function registrarEmpresasEnviosP()
  {
    $objBitacora = new bitacoraModelo();
    $ultimoId = $this->guardarDatos2([
      'tabla' => 'empresas_envios',
      'datos' => [
        "nombre_empresa" => $this->nombreEmpresaEnvios,
      ]
    ]);
    if ($ultimoId == false || $ultimoId <= 0) {
      $this->rollback();
      $objBitacora->registrarBitacora('roles', 'Registrar', 'Error', true);
      return [
        "tipo" => "simple",
        "titulo" => "Empresa de envíos no registrada",
        "texto" => "La empresa de envíos no ha sido registrada exitosamente",
        "icono" => "error",
      ];
    }

    $objBitacora->registrarBitacora('empresasEnvios', 'Registrar', 'Éxito');
    $this->commit();
    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Empresa registrada",
      "texto" => "La empresa ha sido registrada exitosamente",
      "icono" => "success",
    ];
  }
  private function actualizarEmpresasEnviosP()
  {
    $objBitacora = new bitacoraModelo();
    $resultado = $this->actualizarDatos2([
      'tabla' => 'empresas_envios',
      'datos' => [
        "nombre_empresa" => $this->nombreEmpresaEnvios,
      ],
      "WHERE" => [
        "id_empresa_envios" => $this->idEmpresaEnvios,
      ]
    ]);
    if ($resultado == false || $resultado <= 0) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Sin cambios realizados",
        "texto" => "No se realizó ningún cambio",
        "icono" => "warning",
      ];
      $resultadoB = $objBitacora->registrarBitacora('roles', 'actualizar Empresa de envíos con id: ' . $this->idEmpresaEnvios, 'Error', true);
    } else {
      $alerta = [
        "tipo" => "limpiarYcerrar",
        "titulo" => "Empresa de envíos actualizada",
        "texto" => "La empresa de envíos ha sido actualizada exitosamente",
        "icono" => "success",
      ];
      $resultadoB = $objBitacora->registrarBitacora('roles', 'actualizar Empresa de envíos con id: ' . $this->idEmpresaEnvios, 'Éxito');
      $this->commit();
    }
    if ($resultadoB) {
      $this->rollback();
      return $resultadoB;
    }
    return $alerta;
  }
  private function eliminarEmpresasEnviosP()
  {
    $objBitacora = new bitacoraModelo();
    $eliminarUsuario = $this->eliminarDatos2([
      "tabla" => "empresas_envios",
      "WHERE" => [
        "id_empresa_envios" => $this->idEmpresaEnvios
      ]
    ]);
    if ($eliminarUsuario == 1) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Empresa eliminada",
        "texto" => "La empresa ha sido eliminada con éxito",
        "icono" => "success"
      ];
      $resultadoB = $objBitacora->registrarBitacora('empresasEnvios', 'eliminar Empresa de envíos con id: ' . $this->idEmpresaEnvios, 'Éxito');
      $this->commit();
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Empresa de envíos no encontrada",
        "texto" => "La empresa de envíos no existe en la Base de Datos",
        "icono" => "error"
      ];
      $resultadoB = $objBitacora->registrarBitacora('empresasEnvios', 'eliminar Empresa de envíos con id: ' . $this->idEmpresaEnvios, 'Error');
    }
    if ($resultadoB != false) {
      $this->rollback();
      return $resultadoB;
    }
    return $alerta;
  }
}
