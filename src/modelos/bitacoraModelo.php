<?php

namespace src\modelos;

use src\config\connect\conexion;
use PDO;
use PDOException;
use Exception;

class bitacoraModelo extends conexion
{
  private $idBitacora;
  private $moduloBitacora;
  private $accionBitacora;
  private $resultadoBitacora;
  private $commit;

  public function seleccionarBitacora($id = null)
  {
    $this->idBitacora = $id;

    if ($this->idBitacora != null && $this->idBitacora != "") {
      $campos = [
        [
          "campo_nombre" => 'id_bitacora',
          "campo_valor" => $this->idBitacora,
          "formulario_nombre" => "id de la bitacora",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => 'bitacora',
          'BD' => 'seguridad',
          "debeExistir" => true,
        ]
      ];

      $respuesta = $this->limpiar_Verificar($campos);
      if ($respuesta !== false) {
        return $respuesta;
        exit();
      }
    }
    return $this->seleccionarBitacoraP();
  }
  public function registrarBitacora($modulo, $accion, $resultado, $hacerCommit=null)
  {

    $this->moduloBitacora = $modulo;
    $this->accionBitacora = $accion;
    $this->resultadoBitacora = $resultado;
    $this->commit = $hacerCommit??false;

    $campos = [
      [
        "campo_nombre" => "nombre_modulo",
        "campo_valor" => $this->moduloBitacora,
        "formulario_nombre" => "nombre del modulo",
        "requerido" => true,
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj,
        "tabla" => "modulos",
        'BD' => 'seguridad',
      ],
      [
        "campo_valor" => $this->accionBitacora,
        "formulario_nombre" => "nombre de la accion",
        "requerido" => true,
        "minimo" => minRegexDescripcion,
        "maximo" => maxRegexDescripcion,
        "expresion_re" => regexDescripcion,
      ],
      [
        "campo_valor" => $this->resultadoBitacora,
        "formulario_nombre" => "resultado",
        "requerido" => true,
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj,
      ]
    ];

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) return $respuesta;
    return $this->registrarBitacoraP();
  }
  private function seleccionarBitacoraP()
  {
    if ($this->idBitacora == null || $this->idBitacora == "") {
      $resultado = $this->seleccionarDatos2([
        'campos' => '
          b.id_bitacora, u.nombre_usuario, m.nombre_modulo,
          b.accion, b.fecha_bitacora, b.resultado_bitacora
        ',
        'tabla' => 'bitacora as b',
        'BD' => 'seguridad',
        'datosJoins' => [
          "usuarios as u" => "b.cedula_usuario = u.cedula_usuario",
          "modulos as m" => "b.id_modulo = m.id_modulo",
        ],
      ]);
      $Bitacora = $resultado->fetchAll(PDO::FETCH_ASSOC);
      return $Bitacora;
    } else {
      $instruccionesBD = [
        'campos' => '*',
        'tabla' => 'bitacora',
        'BD' => 'seguridad',
        'WHERE' => [
          "id_bitacora" => $this->idBitacora,
        ]
      ];
      $resultado = $this->seleccionarDatos($instruccionesBD);

      if ($resultado->rowCount() <= 0) {
        $alerta = [
          "tipo" => "simple",
          "titulo" => "Bitacora no encontrada",
          "texto" => "La bitacora seleccionada no ha sido encontrada en la base de datos",
          "icono" => "error"
        ];
        return $alerta;
        exit();
      } else {
        $bitacora = $resultado->fetch(PDO::FETCH_ASSOC);
      }

      return $bitacora;
    }
  }
  private function registrarBitacoraP()
  {
    //modulo
    $idModulo = $this->VEYSNEC([
      'RSEN' => true,
      'campos' => 'id_modulo',
      'tabla' => 'modulos',
      'BD' => 'seguridad',
      'WHERE' => [
        'nombre_modulo' => $this->moduloBitacora
      ]
    ]);

    $ultimoId = $this->guardarDatos2([
      'tabla' => 'bitacora',
      'BD' => 'seguridad',
      'datos' => [
        "cedula_usuario" => $_SESSION['cedula'],
        "id_modulo" => $idModulo,
        "resultado_bitacora" => $this->resultadoBitacora,
        "accion" => $this->accionBitacora,
        "fecha_bitacora" => $this->FechaHora_Sel('fecha_hora_BD'),
      ],
    ]);
    if ($ultimoId !== false && $ultimoId > 0) {
      if($this->commit) $this->commit();
      return false;
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Bitacora no registrada",
        "texto" => "La bitacora no se registro correctamente",
        "icono" => "error"
      ];
      return $alerta;
    }
  }
}
