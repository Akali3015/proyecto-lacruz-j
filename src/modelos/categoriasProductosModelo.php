<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\bitacoraModelo;
use PDO;
use Exception;

class categoriasProductosModelo extends conexion
{
  use traitModelo; // Para las validaciones

  private $idCategoriaProducto;
  private $nombreCategoria;
  private $necesitanMateriasPrimas; // ← Nueva variable

  // 1. LISTAR O SELECCIONAR UNO
  public function seleccionarCategoriaProducto($id = null)
  {
    $this->idCategoriaProducto = $id;

    if ($this->idCategoriaProducto == null || $this->idCategoriaProducto == "") {
      $instruccionesBD = [
        'campos' => 'c.id_categoria_producto, c.nombre_categoria, c.necesitan_materias_primas, c.status, COUNT(p.id_producto) as cantidad_productos',
        'tabla' => 'categorias_productos c',
        'PEL' => 'c',
        'datosJoins' => [
          [
            'tablaDestino' => 'productos p',
            'tipoJoin' => 'LEFT',
            'conexionLo' => 'c.id_categoria_producto = p.id_categoria_producto AND p.status = 1'
          ]
        ],
        'GROUP' => 'c.id_categoria_producto'
      ];
      $resultado = $this->seleccionarDatos($instruccionesBD);
      return $resultado->fetchAll(PDO::FETCH_ASSOC);
    } else {
      $instruccionesBD = [
        'campos' => '*',
        'tabla' => 'categorias_productos',
        'WHERE' => [
          [
            "condicion_campo" => "id_categoria_producto",
            "condicion_marcador" => ":id",
            "condicion_valor" => $this->idCategoriaProducto,
            "comparacion" => "="
          ]
        ]
      ];
      $resultado = $this->seleccionarDatos($instruccionesBD);
      if ($resultado->rowCount() <= 0) {
        return ["tipo" => "simple", "titulo" => "Error", "texto" => "No se encontró la categoría", "icono" => "error"];
      } else {
        return $resultado->fetch(PDO::FETCH_ASSOC);
      }
    }
  }

  // 2. REGISTRAR
  public function registrarCategoriaProducto($nombre, $necesitan)
  {
    $this->nombreCategoria = $nombre;
    $this->necesitanMateriasPrimas = $necesitan;

    // Validamos ambos campos
    $campos = [
      [
        "campo_nombre" => "nombre_categoria",
        "campo_valor" => &$this->nombreCategoria,
        "formulario_nombre" => "nombre de la categoría",
        "requerido" => true,
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj,
        "tabla" => "categorias_productos",
        "debeSerUnico" => true
      ],
      [
        "campo_nombre" => "necesitan_materias_primas",
        "campo_valor" => &$this->necesitanMateriasPrimas,
        "formulario_nombre" => "requiere materias primas",
        "requerido" => true,
        "minimo" => minRegexCantidadItem,
        "maximo" => maxRegexCantidadItem,
        "expresion_re" => regexCantidadItem,
        "tabla" => "categorias_productos"
      ]
    ];

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
    }

    try {
      $this->conectar();

      // Pasamos ambas variables al arreglo de guardado
      $datos_registro = [
        [
          "campo_nombre" => "nombre_categoria",
          "campo_marcador" => ":nombre",
          "campo_valor" => $this->nombreCategoria,
          "ponerEnMayusculas" => true
        ],
        [
          "campo_nombre" => "necesitan_materias_primas",
          "campo_marcador" => ":necesitan",
          "campo_valor" => $this->necesitanMateriasPrimas
        ]
      ];

      $ultimoId = $this->guardarDatos('categorias_productos', $datos_registro);
      $bitacoraModelo = new bitacoraModelo();

      if ($ultimoId == false || $ultimoId <= 0) {
        $bitacoraModelo->registrarBitacora("Categorias de Productos", "Registrar", "Fallido");
        $this->rollback();
        return ["tipo" => "simple", "titulo" => "Error", "texto" => "No se pudo registrar.", "icono" => "error"];
      }

      $bitacoraModelo->registrarBitacora("Categorias de Productos", "Registrar", "Exito");
      $this->commit();
      return ["tipo" => "limpiar", "titulo" => "Registrada", "texto" => "Categoría exitosamente creada.", "icono" => "success"];
    } catch (Exception $e) {
      $this->rollback();
      return ["tipo" => "simple", "titulo" => "Error", "texto" => $e->getMessage(), "icono" => "error"];
    }
  }

  // 3. ACTUALIZAR
  public function actualizarCategoriaProducto($id, $nombre, $necesitan)
  {
    $this->idCategoriaProducto = $id;
    $this->nombreCategoria = $nombre;
    $this->necesitanMateriasPrimas = $necesitan;

    $campos = [
      [
        "campo_nombre" => "id_categoria_producto",
        "campo_valor" => $this->idCategoriaProducto,
        "formulario_nombre" => "id",
        "requerido" => true,
        "minimo" => minRegexId,
        "maximo" => maxRegexId,
        "expresion_re" => regexId,
        "tabla" => "categorias_productos",
        "debeExistir" => true
      ],
      [
        "campo_nombre" => "nombre_categoria",
        "campo_valor" => &$this->nombreCategoria,
        "formulario_nombre" => "nombre",
        "requerido" => true,
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj,
        "tabla" => "categorias_productos"
      ],
      [
        "campo_nombre" => "necesitan_materias_primas",
        "campo_valor" => &$this->necesitanMateriasPrimas,
        "formulario_nombre" => "requiere materias primas",
        "requerido" => true,
        "minimo" => minRegexCantidadItem,
        "maximo" => maxRegexCantidadItem,
        "expresion_re" => regexCantidadItem,
        "tabla" => "categorias_productos"
      ]
    ];

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
    }

    try {
      $this->conectar();
      $sqlCheck = "SELECT * FROM categorias_productos WHERE nombre_categoria = :nombre AND id_categoria_producto != :id AND status = 1";
      $stmtCheck = $this->conexion->prepare($sqlCheck);
      $stmtCheck->execute([':nombre' => strtoupper($this->nombreCategoria), ':id' => $this->idCategoriaProducto]);

      if ($stmtCheck->rowCount() > 0) {
        return ["tipo" => "simple", "titulo" => "Error", "texto" => "Nombre ya en uso.", "icono" => "error"];
      }

      // Actualizamos enviando ambos campos
      $instruccionesBD = [
        "tabla" => "categorias_productos",
        "datos" => [
          [
            "campo_nombre" => "nombre_categoria",
            "campo_marcador" => ":nombre",
            "campo_valor" => $this->nombreCategoria,
            "ponerEnMayusculas" => true
          ],
          [
            "campo_nombre" => "necesitan_materias_primas",
            "campo_marcador" => ":necesitan",
            "campo_valor" => $this->necesitanMateriasPrimas
          ]
        ],
        "WHERE" => [
          [
            "condicion_campo" => "id_categoria_producto",
            "condicion_marcador" => ":id",
            "condicion_valor" => $this->idCategoriaProducto,
            "comparacion" => "="
          ]
        ]
      ];

      $resultado = $this->actualizarDatos($instruccionesBD);
      $bitacoraModelo = new bitacoraModelo();

      if ($resultado != false && $resultado > 0) {
        $bitacoraModelo->registrarBitacora("Categorias de Productos", "Actualizar", "Exito");
        $this->commit();
        return ["tipo" => "limpiarYcerrar", "titulo" => "Actualizada", "texto" => "Moficada exitosamente.", "icono" => "success"];
      } else {
        $this->rollback();
        return ["tipo" => "simple", "titulo" => "Sin cambios", "texto" => "No hubo modificaciones.", "icono" => "warning"];
      }
    } catch (Exception $e) {
      $this->rollback();
      return ["tipo" => "simple", "titulo" => "Error", "texto" => $e->getMessage(), "icono" => "error"];
    }
  }

  // 4. ELIMINAR LÓGICO
  public function eliminarCategoriaProducto($id)
  {
    $this->idCategoriaProducto = $id;

    $campos = [
      [
        "campo_nombre" => "id_categoria_producto",
        "campo_valor" => &$this->idCategoriaProducto,
        "formulario_nombre" => "id",
        "requerido" => true,
        "minimo" => minRegexId,
        "maximo" => maxRegexId,
        "expresion_re" => regexId,
        "debeExistir" => true,
        "tabla" => "categorias_productos"
      ]
    ];

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) {
      return $respuesta;
    }

    try {
      $this->conectar();

      // VERIFICACIÓN DEL BLOQUEO INTELIGENTE DE ELIMINACIÓN
      $sqlCheck = "SELECT COUNT(id_producto) as total FROM productos WHERE id_categoria_producto = :id AND status = 1";
      $stmtCheck = $this->conexion->prepare($sqlCheck);
      $stmtCheck->execute([':id' => $this->idCategoriaProducto]);
      $productosActivos = $stmtCheck->fetch(PDO::FETCH_ASSOC)['total'];

      if ($productosActivos > 0) {
        $this->rollback();
        return [
          "tipo" => "simple",
          "titulo" => "Acción Bloqueada",
          "texto" => "No puedes eliminar esta categoría porque hay " . $productosActivos . " producto(s) asignado(s) a ella. Transfiere los productos a otra categoría primero.",
          "icono" => "warning"
        ];
      }

      $resultado = $this->eliminarDatos("categorias_productos", "id_categoria_producto", $this->idCategoriaProducto);
      $modeloBitacora = new bitacoraModelo();

      if ($resultado->rowCount() == 1) {
        $modeloBitacora->registrarBitacora("Categorias de Productos", "Eliminar", "Exito");
        $this->commit();
        return ["tipo" => "simple", "titulo" => "Eliminado", "texto" => "Eliminada con éxito.", "icono" => "success"];
      } else {
        $modeloBitacora->registrarBitacora("Categorias de Productos", "Eliminar", "Fallido");
        $this->rollback();
        return ["tipo" => "simple", "titulo" => "Error", "texto" => "Fallo al eliminar.", "icono" => "error"];
      }
    } catch (Exception $e) {
      $this->rollback();
      return ["tipo" => "simple", "titulo" => "Error", "texto" => $e->getMessage(), "icono" => "error"];
    }
  }
}
