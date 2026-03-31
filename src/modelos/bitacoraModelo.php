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

  public function registrarBitacora($modulo, $accion, $resultado)
  {

    $this->moduloBitacora = $modulo;
    $this->accionBitacora = $accion;
    $this->resultadoBitacora = $resultado;

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
      ],
      [
        "campo_nombre" => "nombre_accion",
        "campo_valor" => $this->accionBitacora,
        "formulario_nombre" => "nombre de la accion",
        "requerido" => true,
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj,
        "tabla" => "acciones",
      ],
      [
        "campo_nombre" => "resultado_accion_bitacora",
        "campo_valor" => $this->resultadoBitacora,
        "formulario_nombre" => "resultado",
        "requerido" => true,
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj,
        "tabla" => "bitacora",
      ]
    ];

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
      exit();
    } else {
      return $this->registrarBitacoraP();
    }
  }

  private function seleccionarBitacoraP()
  {
    if ($this->idBitacora == null || $this->idBitacora == "") {
      $instruccionesBD = [
        'campos' => 'b.id_bitacora, u.nombre_usuario, m.nombre_modulo, a.nombre_accion, b.fecha_bitacora, b.resultado_accion_bitacora',
        'tabla' => 'bitacora as b',
        'PEL' => 'b',
        'datosJoins' => [
          [
            "TablaDestino" => "usuarios as u",
            "conexionLo" => "b.cedula_usuario = u.cedula_usuario",
          ],
          [
            "TablaDestino" => "modulos as m",
            "conexionLo" => "b.id_modulo = m.id_modulo",
          ],
          [
            "TablaDestino" => "acciones as a",
            "conexionLo" => "b.id_accion = a.id_accion",
          ]
        ],


      ];
      $resultado = $this->seleccionarDatos($instruccionesBD);
      $Bitacora = $resultado->fetchAll(PDO::FETCH_ASSOC);
      return $Bitacora;
    } else {
      $instruccionesBD = [
        'campos' => '*',
        'tabla' => 'bitacora',
        'WHERE' => [
          [
            "condicion_campo" => "id_bitacora",
            "condicion_marcador" => ":id",
            "condicion_valor" => $this->idBitacora,
            "comparacion" => "=",
          ]
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
      'WHERE' => [
        'nombre_modulo' => $this->moduloBitacora
      ]
    ]);

    //accion
    $idAccion = $this->VEYSNEC([
      'RSEN' => true,
      'campos' => 'id_accion',
      'tabla' => 'acciones',
      'WHERE' => [
        'nombre_accion' => $this->accionBitacora
      ]
    ]);

    //resultado
    $idResultado = $this->VEYSNEC([
      'RSEN' => true,
      'campos' => 'id_resultado_bitacora',
      'tabla' => 'resultados_bitacora',
      'WHERE' => [
        'texto_resultado_bitacora' => $this->resultadoBitacora
      ]
    ]);

    $ultimoId = $this->guardarDatos2([
      'tabla' => 'bitacora',
      'datos' => [
        "cedula_usuario" => $_SESSION['cedula'],
        "id_accion" => $idAccion,
        "id_modulo" => $idModulo,
        "id_resultado_bitacora" => $idResultado,
        "fecha_bitacora" => $this->FechaHora_Sel('fecha_hora_BD'),
      ],
    ]);
    if ($ultimoId !== false && $ultimoId > 0) {
      $this->commit();
      return false;
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Bitacora no registrada",
        "texto" => "La bitacora no se registro correctamente",
        "icono" => "error"
      ];
      $this->rollback();
      return $alerta;
    }
  }
}
