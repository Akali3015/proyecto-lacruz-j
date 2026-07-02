<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use src\modelos\mensajesWSModelo;

class categoriasProductosModelo extends conexion {
  private int|string $idCategoria = 0;
  private string $nombreCategoria = '';
  private int $necesitanMateriasPrimas = 0;

  public function validarCategorias(array $instruccionesVal) {
    [
      'infoVal' => &$infoVal,
      'camposVal' => &$camposVal,
    ] = $instruccionesVal;
    $funcionAsignadora = function ($nombreCampo, &$valor) {
      $claveVal = [
        'id_categoria_producto' => [
          "campo_nombre" => "id_categoria_producto",
          "campo_valor" => &$valor,
          "formulario_nombre" => "id",
          "requerido" => true,
          "minimo" => minRegexId,
          "maximo" => maxRegexId,
          "expresion_re" => regexId,
          "tabla" => "categorias_productos",
          "debeExistir" => true
        ],
        'nombre_categoria_producto' => [
          "campo_nombre" => "nombre_categoria_producto",
          "campo_valor" => &$valor,
          "formulario_nombre" => "nombre",
          "requerido" => true,
          "minimo" => minRegexNombreObj,
          "maximo" => maxRegexNombreObj,
          "expresion_re" => regexNombreObj,
          "tabla" => "categorias_productos",
          "debeSerUnico" => true,
        ],
        'necesitan_materias_primas' => [
          "campo_nombre" => "necesitan_materias_primas",
          "campo_valor" => &$valor,
          "formulario_nombre" => "requiere materias primas",
          "requerido" => true,
          "minimo" => minRegexCantidadItem,
          "maximo" => maxRegexCantidadItem,
          "expresion_re" => regexCantidadItem,
          "tabla" => "categorias_productos"
        ]
      ];
      return $claveVal[$nombreCampo];
    };
    $campos = [];
    foreach ($camposVal as $campo) {
      $campos[] = $funcionAsignadora($campo, $infoVal[$campo]);
    }
    return $this->limpiar_Verificar($campos);
  }
  public function seleccionarCategorias(array $info) {
    if (($info['id_categoria_producto'] ?? '') != '') {
      $resultado = $this->validarCategorias([
        'infoVal' => &$info,
        'camposVal' => ['id_categoria_producto'],
      ]);
      if ($resultado) return $resultado;
      $this->idCategoria = $info['id_categoria_producto'];
    }
    return $this->seleccionarCategoriasP();
  }
  public function registrarCategorias(array $info) {
    $resultado = $this->validarCategorias([
      'infoVal' => &$info,
      'camposVal' => ['nombre_categoria_producto'],
    ]);
    if ($resultado) return $resultado;
    $this->nombreCategoria = $info['nombre_categoria_producto'];
    return $this->registrarCategoriasP();
  }
  public function actualizarCategorias(array $info) {
    $resultado = $this->validarCategorias([
      'infoVal' => &$info,
      'camposVal' => [
        'id_categoria_producto',
        'nombre_categoria_producto',
        'necesitan_materias_primas'
      ],
    ]);
    if ($resultado) return $resultado;
    $this->idCategoria = $info['id_categoria_producto'];
    $this->nombreCategoria = $info['nombre_categoria_producto'];
    $this->necesitanMateriasPrimas = $info['necesitan_materias_primas'];
    return $this->actualizarCategoriasP();
  }
  public function eliminarCategorias(array $info) {
    $resultado = $this->validarCategorias([
      'infoVal' => &$info,
      'camposVal' => [
        'id_categoria_producto'
      ]
    ]);
    if ($resultado) return $resultado;
    $this->idCategoria = $info['id_categoria_producto'];
    return $this->eliminarCategoriasP();
  }

  private function seleccionarCategoriasP() {
    if ($this->idCategoria == null || $this->idCategoria == "") {
      return $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'v_categorias_productos_todas',
      ])->fetchAll();
    } else {
      return $this->seleccionarDatos2([
        'campos' => '*',
        'tabla' => 'categorias_productos',
        'WHERE' => [
          "id_categoria_producto" => $this->idCategoria,
        ]
      ])->fetch();
    }
  }
  private function registrarCategoriasP() {
    $ultimoId = $this->guardarDatos2([
      'tabla' => 'categorias_productos',
      'datos' => [
        "nombre_categoria_producto" => $this->nombreCategoria,
        "necesitan_materias_primas" => $this->necesitanMateriasPrimas
      ]
    ]);
    $bitacoraModelo = new bitacoraModelo();

    if ($ultimoId == false || $ultimoId <= 0) {
      $this->rollback();
      $bitacoraModelo->registrarBitacora("categoriasProductos", "Registrar", "Fallido", true);
      return [
        "tipo" => "simple",
        "titulo" => "Error",
        "texto" => "No se pudo registrar.",
        "icono" => "error"
      ];
    }

    $bitacoraModelo->registrarBitacora("Categorias de Productos", "Registrar", "Exito");

    $objetoNot = new mensajesWSModelo();
    $objetoNot->enviarMensajesWS([
      "receptor" => [
        'tipo' => 'permisos',
        'permisos' => ['categoriasProductos' => ['ver']]
      ],
      'cuerpo' => [
        ['accion' => "borrarDataModuloSS", 'modulo' => 'categorias_productos'],
        ['accion' => "actDT", 'modulo' => 'categorias_productos'],
        [
          'accion' => 'alertar',
          'alerta' => [
            'tipo' => 'simple',
            'titulo' => 'Categorías',
            'texto' => "Se ha registrado una nueva categoría de producto",
            'icono' => 'info',
            'notifier' => true,
          ]
        ]
      ],
      'noCommit' => true
    ]);

    $this->commit();
    return [
      "tipo" => "limpiar",
      "titulo" => "Registrada",
      "texto" => "Categoría exitosamente creada.",
      "icono" => "success"
    ];
  }
  private function actualizarCategoriasP() {
    // Actualizamos enviando ambos campos
    $resultado = $this->actualizarDatos2([
      "tabla" => "categorias_productos",
      "datos" => [
        "nombre_categoria_producto" => $this->nombreCategoria,
        "necesitan_materias_primas" => $this->necesitanMateriasPrimas
      ],
      "WHERE" => [
        "id_categoria_producto" => $this->idCategoria,
      ]
    ]);
    $bitacoraModelo = new bitacoraModelo();

    if ($resultado == false || $resultado <= 0) {
      $this->rollback();
      $bitacoraModelo->registrarBitacora("Categorias de Productos", "Actualizar", "Fallido");
      return [
        "tipo" => "simple",
        "titulo" => "Sin cambios",
        "texto" => "No hubo modificaciones.",
        "icono" => "warning"
      ];
    };
    $bitacoraModelo->registrarBitacora("Categorias de Productos", "Actualizar", "Exito");

    $objetoNot = new mensajesWSModelo();
    $objetoNot->enviarMensajesWS([
      "receptor" => [
        'tipo' => 'permisos',
        'permisos' => ['categoriasProductos' => ['ver']]
      ],
      'cuerpo' => [
        ['accion' => "borrarDataModuloSS", 'modulo' => 'categorias_productos'],
        ['accion' => "actDT", 'modulo' => 'categorias_productos'],
        [
          'accion' => 'alertar',
          'alerta' => [
            'tipo' => 'simple',
            'titulo' => 'Categorías',
            'texto' => "Se ha actualizado una categoría de producto",
            'icono' => 'info',
            'notifier' => true,
          ]
        ]
      ],
      'noCommit' => true
    ]);

    $this->commit();
    return [
      "tipo" => "limpiarYcerrar",
      "titulo" => "Actualizada",
      "texto" => "Moficada exitosamente.",
      "icono" => "success"
    ];
  }
  private function eliminarCategoriasP() {
    $resultado = $this->eliminarDatos2([
      'tabla' => "categorias_productos",
      'WHERE' => [
        "id_categoria_producto" => $this->idCategoria
      ]
    ]);
    $modeloBitacora = new bitacoraModelo();

    if ($resultado == 1) {
      $modeloBitacora->registrarBitacora("Categorias de Productos", "Eliminar", "Exito");

      $objetoNot = new mensajesWSModelo();
      $objetoNot->enviarMensajesWS([
        "receptor" => [
          'tipo' => 'permisos',
          'permisos' => ['categoriasProductos' => ['ver']]
        ],
        'cuerpo' => [
          ['accion' => "borrarDataModuloSS", 'modulo' => 'categorias_productos'],
          ['accion' => "actDT", 'modulo' => 'categorias_productos'],
          [
            'accion' => 'alertar',
            'alerta' => [
              'tipo' => 'simple',
              'titulo' => 'Categorías',
              'texto' => "Se ha eliminado una categoría de producto",
              'icono' => 'info',
              'notifier' => true,
            ]
          ]
        ],
        'noCommit' => true
      ]);

      $this->commit();
      return [
        "tipo" => "simple",
        "titulo" => "Eliminado",
        "texto" => "Eliminada con éxito.",
        "icono" => "success"
      ];
    } else {
      $this->rollback();
      $modeloBitacora->registrarBitacora("Categorias de Productos", "Eliminar", "Fallido", true);
      return [
        "tipo" => "simple",
        "titulo" => "Error",
        "texto" => "Fallo al eliminar.",
        "icono" => "error"
      ];
    }
  }
}
