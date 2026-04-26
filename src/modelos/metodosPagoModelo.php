<?php

namespace src\modelos;

use src\config\connect\conexion;
use PDO;

class metodosPagoModelo extends conexion
{
  private $idMetodo;
  private $nombre;
  private $nMoneda;
  private $nBancoEmi;
  private $nBancoRec;
  private $nReferencia;

  public function seleccionarMetodosPagos($id = null)
  {
    $this->idMetodo = $id;
    if ($this->idMetodo != null && $this->idMetodo != "") {
      $campos = [
        ["campo_nombre" => 'id_metodo_pago', "campo_valor" => $this->idMetodo, "formulario_nombre" => "ID", "requerido" => true, "expresion_re" => regexId, "tabla" => 'metodos_pagos', "debeExistir" => true]
      ];
      $respuesta = $this->limpiar_Verificar($campos);
      if ($respuesta !== false) return $respuesta;
    }
    return $this->seleccionarMetodosPagosP();
  }

  public function registrarMetodosPagos($datos)
  {
    $this->idMetodo = $datos['id_metodo_pago'];
    $this->nombre = $datos['nombre_metodo_pago'];
    $this->nMoneda = $datos['necesita_moneda'] ?? 0;
    $this->nBancoEmi = $datos['necesita_banco_emisor'] ?? 0;
    $this->nBancoRec = $datos['necesita_banco_receptor'] ?? 0;
    $this->nReferencia = $datos['necesita_referencia'] ?? 0;

    $campos = [
      [
        "campo_nombre" => "id_metodo_pago",
        "campo_valor" => &$this->idMetodo,
        "formulario_nombre" => "ID",
        "requerido" => true,
        "minimo" => minRegexId,
        "maximo" => maxRegexId,
        "expresion_re" => regexId,
        "tabla" => "metodos_pagos",
        "debeSerUnico" => true
      ],
      [
        "campo_nombre" => "nombre_metodo_pago",
        "campo_valor" => &$this->nombre,
        "formulario_nombre" => "nombre",
        "requerido" => true,
        "minimo" => 3,
        "maximo" => 50,
        "expresion_re" => regexNombreObj,
        "tabla" => "metodos_pagos",
        "debeSerUnico" => true
      ],
      [
        "campo_nombre" => "necesita_moneda",
        "campo_valor" => &$this->nMoneda,
        "formulario_nombre" => "especificar moneda"
      ],
      [
        "campo_nombre" => "necesita_banco_emisor",
        "campo_valor" => &$this->nBancoEmi,
        "formulario_nombre" => "banco emisor"
      ],
      [
        "campo_nombre" => "necesita_banco_receptor",
        "campo_valor" => &$this->nBancoRec,
        "formulario_nombre" => "banco receptor"
      ],
      [
        "campo_nombre" => "necesita_referencia",
        "campo_valor" => &$this->nReferencia,
        "formulario_nombre" => "número de referencia"
      ]
    ];

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) return $respuesta;

    return $this->registrarMetodosPagosP();
  }

  public function actualizarMetodosPagos($id, $datos)
  {
    $this->idMetodo = $id;
    $this->nombre = $datos['nombre_metodo_pago'];
    $this->nMoneda = $datos['necesita_moneda'] ?? 0;
    $this->nBancoEmi = $datos['necesita_banco_emisor'] ?? 0;
    $this->nBancoRec = $datos['necesita_banco_receptor'] ?? 0;
    $this->nReferencia = $datos['necesita_referencia'] ?? 0;

    $campos = [
      [
        "campo_nombre" => "id_metodo_pago",
        "campo_valor" => &$this->idMetodo,
        "formulario_nombre" => "ID",
        "requerido" => true,
        "debeExistir" => true,
        "tabla" => "metodos_pagos"
      ],
      [
        "campo_nombre" => "nombre_metodo_pago",
        "campo_valor" => &$this->nombre,
        "formulario_nombre" => "nombre",
        "requerido" => true,
        "minimo" => 3,
        "maximo" => 50,
        "expresion_re" => regexNombreObj,
        "tabla" => "metodos_pagos",
        "debeSerUnico" => true
      ],
      [
        "campo_nombre" => "necesita_moneda",
        "campo_valor" => &$this->nMoneda,
        "formulario_nombre" => "especificar moneda"
      ],
      [
        "campo_nombre" => "necesita_banco_emisor",
        "campo_valor" => &$this->nBancoEmi,
        "formulario_nombre" => "banco emisor"
      ],
      [
        "campo_nombre" => "necesita_banco_receptor",
        "campo_valor" => &$this->nBancoRec,
        "formulario_nombre" => "banco receptor"
      ],
      [
        "campo_nombre" => "necesita_referencia",
        "campo_valor" => &$this->nReferencia,
        "formulario_nombre" => "número de referencia"
      ]
    ];
    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) return $respuesta;
    return $this->actualizarMetodosPagosP();
  }

  public function eliminarMetodosPagos($id)
  {
    $this->idMetodo = $id;
    $campos = [
      ["campo_nombre" => "id_metodo_pago", "campo_valor" => $this->idMetodo, "formulario_nombre" => "ID", "requerido" => true, "debeExistir" => true, "tabla" => "metodos_pagos"]
    ];
    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) return $respuesta;

    return $this->eliminarMetodosPagosP();
  }

  private function seleccionarMetodosPagosP()
  {
    $instrucciones = [
      'campos' => '*',
      'tabla' => 'metodos_pagos',
      'ORDER' => 'nombre_metodo_pago ASC'
    ];
    if ($this->idMetodo != null) {
      $instrucciones['WHERE'] = ["id_metodo_pago" => $this->idMetodo];
      return $this->seleccionarDatos2($instrucciones)->fetch(PDO::FETCH_ASSOC);
    }
    return $this->seleccionarDatos2($instrucciones)->fetchAll(PDO::FETCH_ASSOC);
  }

  private function registrarMetodosPagosP()
  {
    $datos = [
      "id_metodo_pago" => $this->idMetodo,
      "nombre_metodo_pago" => $this->nombre,
      "necesita_moneda" => $this->nMoneda,
      "necesita_banco_emisor" => $this->nBancoEmi,
      "necesita_banco_receptor" => $this->nBancoRec,
      "necesita_referencia" => $this->nReferencia,
      "status" => 1
    ];
    $resultado = $this->guardarDatos2(['tabla' => 'metodos_pagos', 'datos' => $datos]);
    if ($resultado > 0) {
      $this->commit();
      return ["tipo" => "limpiarYcerrar", "titulo" => "Registro Exitoso", "texto" => "Método de pago registrado", "icono" => "success"];
    }
    $this->rollback();
    return ["tipo" => "simple", "titulo" => "Error", "texto" => "No se pudo registrar", "icono" => "error"];
  }

  private function actualizarMetodosPagosP()
  {
    $instrucciones = [
      'tabla' => 'metodos_pagos',
      'datos' => [
        "nombre_metodo_pago" => $this->nombre,
        "necesita_moneda" => $this->nMoneda,
        "necesita_banco_emisor" => $this->nBancoEmi,
        "necesita_banco_receptor" => $this->nBancoRec,
        "necesita_referencia" => $this->nReferencia
      ],
      'WHERE' => ["id_metodo_pago" => $this->idMetodo]
    ];
    $resultado = $this->actualizarDatos2($instrucciones);
    if ($resultado !== false) {
      $this->commit();
      return ["tipo" => "limpiarYcerrar", "titulo" => "Actualización Exitosa", "texto" => "Método actualizado", "icono" => "success"];
    }
    $this->rollback();
    return ["tipo" => "simple", "titulo" => "Error", "texto" => "Ocurrió un error al procesar la solicitud", "icono" => "error"];
  }

  private function eliminarMetodosPagosP()
  {
    $resultado = $this->eliminarDatos2(['tabla' => 'metodos_pagos', 'WHERE' => ["id_metodo_pago" => $this->idMetodo]]);
    if ($resultado) {
      $this->commit();
      return ["tipo" => "simple", "titulo" => "Eliminado", "texto" => "El método ha sido desactivado", "icono" => "success"];
    }
    $this->rollback();
    return ["tipo" => "simple", "titulo" => "Error", "texto" => "No se pudo eliminar", "icono" => "error"];
  }
}
