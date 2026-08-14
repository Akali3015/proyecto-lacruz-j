<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use src\modelos\mensajesWSModelo;
use src\modelos\traitModelo;
use PDO;

class categoriasProductosModelo extends conexion {
  use traitModelo;

  private int|string $idCategoria = 0;
  private string $nombreCategoria = '';
  private int $necesitanMateriasPrimas = 0;

  public function validarCategorias(string $permiso, ?array &$info = null, ?array $requerido = null) {
    $objAcceso = new accesosModelo();
    $v = $objAcceso->validarPermisos('categoriasProductos', $permiso);
    if ($v) return $v;

    if ($info === null) return false;

    $esquema = [
      'tipo' => 'arrayA',
      'propiedades' => [
        'id_categoria_producto' => [
          ...molId,
          "nombreAlerta" => "id de la categoría",
          "nombreBD" => "id_categoria_producto",
          "tablaBD" => "categorias_productos",
          "debeExistirBD" => true
        ],
        'nombre_categoria_producto' => [
          ...molNombreObj,
          "nombreAlerta" => "nombre de la categoría",
          "nombreBD" => "nombre_categoria_producto",
          "tablaBD" => "categorias_productos",
          "debeSerUnicoBD" => true
        ],
        'necesitan_materias_primas' => [
          ...molBooleanoInt,
          "nombreAlerta" => "requiere materias primas"
        ]
      ],
      'requerido' => $requerido ?? []
    ];

    $v = $this->limpiarValidar($info, $esquema);
    if ($v) return $v;
    return false;
  }
  public function seleccionarCategorias(array $info) {
    $requerido = [];
    if (($info['id_categoria_producto'] ?? '') != "") $requerido[] = 'id_categoria_producto';
    $r = $this->validarCategorias('ver', $info, $requerido);
    if (($info['id_categoria_producto'] ?? '') != "") $this->idCategoria = $info['id_categoria_producto'];
    if ($r) return $r;
    return $this->seleccionarCategoriasP();
  }
  public function registrarCategorias(array $info) {
    $resultado = $this->validarCategorias('registrar', $info, [
      'nombre_categoria_producto',
      'necesitan_materias_primas'
    ]);
    if ($resultado) return $resultado;

    [
      'nombre_categoria_producto' => $this->nombreCategoria,
      'necesitan_materias_primas' => $this->necesitanMateriasPrimas
    ] = $info;

    return $this->registrarCategoriasP();
  }
  public function actualizarCategorias(array $info) {
    $resultado = $this->validarCategorias('actualizar', $info, [
      'id_categoria_producto',
      'nombre_categoria_producto',
      'necesitan_materias_primas'
    ]);
    if ($resultado) return $resultado;

    [
      'id_categoria_producto' => $this->idCategoria,
      'nombre_categoria_producto' => $this->nombreCategoria,
      'necesitan_materias_primas' => $this->necesitanMateriasPrimas
    ] = $info;

    return $this->actualizarCategoriasP();
  }
  public function eliminarCategorias(array $info) {
    $resultado = $this->validarCategorias('eliminar', $info, [
      'id_categoria_producto'
    ]);
    if ($resultado) return $resultado;

    $this->idCategoria = $info['id_categoria_producto'];
    return $this->eliminarCategoriasP();
  }

  private function seleccionarCategoriasP() {
    if ($this->idCategoria == 0) {
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
      $bitacoraModelo->registrarBitacora([
        'modulo' => 'categoriasProductos',
        'accion' => 'registrar',
        'resultado' => 'Fallido',
        'viejo' => [],
        'nuevo' => [
          'nombre_categoria_producto' => $this->nombreCategoria,
          'necesitan_materias_primas' => $this->necesitanMateriasPrimas
        ]
      ]);
      return [
        "tipo" => "simple",
        "titulo" => "Error",
        "texto" => "No se pudo registrar la categoría.",
        "icono" => "error"
      ];
    }

    $bitacoraModelo->registrarBitacora([
      'modulo' => 'categoriasProductos',
      'accion' => 'registrar',
      'resultado' => 'Éxito',
      'viejo' => [],
      'nuevo' => [
        'id_categoria_producto' => $ultimoId,
        'nombre_categoria_producto' => $this->nombreCategoria,
        'necesitan_materias_primas' => $this->necesitanMateriasPrimas
      ]
    ]);

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
            'texto' => "Se ha registrado una nueva categoría de producto: {$this->nombreCategoria}",
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
    $viejo = $this->seleccionarDatos2([
      'campos' => '*',
      'tabla' => 'categorias_productos',
      'WHERE' => ['id_categoria_producto' => $this->idCategoria]
    ])->fetch(PDO::FETCH_ASSOC);

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
      $bitacoraModelo->registrarBitacora([
        'modulo' => 'categoriasProductos',
        'accion' => 'actualizar',
        'resultado' => 'Fallido',
        'viejo' => $viejo,
        'nuevo' => [
          'id_categoria_producto' => $this->idCategoria,
          'nombre_categoria_producto' => $this->nombreCategoria,
          'necesitan_materias_primas' => $this->necesitanMateriasPrimas
        ]
      ]);
      return [
        "tipo" => "simple",
        "titulo" => "Sin cambios",
        "texto" => "No hubo modificaciones.",
        "icono" => "warning"
      ];
    }

    $bitacoraModelo->registrarBitacora([
      'modulo' => 'categoriasProductos',
      'accion' => 'actualizar',
      'resultado' => 'Éxito',
      'viejo' => $viejo,
      'nuevo' => [
        'id_categoria_producto' => $this->idCategoria,
        'nombre_categoria_producto' => $this->nombreCategoria,
        'necesitan_materias_primas' => $this->necesitanMateriasPrimas
      ]
    ]);

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
      "texto" => "Modificada exitosamente.",
      "icono" => "success"
    ];
  }
  private function eliminarCategoriasP() {
    $viejo = $this->seleccionarDatos2([
      'campos' => '*',
      'tabla' => 'categorias_productos',
      'WHERE' => ['id_categoria_producto' => $this->idCategoria]
    ])->fetch(PDO::FETCH_ASSOC);

    $resultado = $this->eliminarDatos2([
      'tabla' => "categorias_productos",
      'WHERE' => [
        "id_categoria_producto" => $this->idCategoria
      ]
    ]);
    $modeloBitacora = new bitacoraModelo();

    if ($resultado == 1) {
      $modeloBitacora->registrarBitacora([
        'modulo' => 'categoriasProductos',
        'accion' => 'eliminar',
        'resultado' => 'Éxito',
        'viejo' => $viejo,
        'nuevo' => []
      ]);

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
      $modeloBitacora->registrarBitacora([
        'modulo' => 'categoriasProductos',
        'accion' => 'eliminar',
        'resultado' => 'Fallido',
        'viejo' => ['id_categoria_producto' => $this->idCategoria],
        'nuevo' => []
      ]);
      return [
        "tipo" => "simple",
        "titulo" => "Error",
        "texto" => "Fallo al eliminar.",
        "icono" => "error"
      ];
    }
  }
}
