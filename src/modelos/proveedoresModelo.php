<?php

namespace src\modelos;

use src\config\connect\conexion;

class proveedoresModelo extends conexion {
  private string $rifProveedor = '';
  private string $razonSocialProveedor = '';
  private string $telefonoProveedor = '';
  private string $correoProveedor = '';
  private string $direccionProveedor = '';

  public function validarProveedores(string $permiso, array &$info, $requerido = []) {
    $objAcceso = new accesosModelo();
    $v = $objAcceso->validarPermisos('proveedores', $permiso);
    if ($v) return $v;

    $esquemaProveedores = [
      'tipo' => 'arrayA',
      'propiedades' => [
        'rif_proveedor' => [
          ...molCedulaRifLetra,
          "nombreAlerta" => "rif o cédula del proveedor",
          "nombreBD" => "rif_proveedor",
          "tablaBD" => "proveedores",
          "requerido" => true,
          "debeSerUnico" => true
        ],
        'razon_social_proveedor' => [
          ...molDescripcion,
          "nombreAlerta" => "razón social del proveedor",
          "nombreBD" => "razon_social_proveedor",
          "tablaBD" => "proveedores",
          "requerido" => true,
        ],
        'telefono_proveedor' => [
          ...molTelefono,
          "nombreAlerta" => "teléfono del proveedor",
          "nombreBD" => "telefono_proveedor",
          "tablaBD" => "proveedores",
          "requerido" => true,
          'debeSerUnico' => true
        ],
        'correo_proveedor' => [
          ...molCorreo,
          "nombreAlerta" => "correo electrónico del proveedor",
          "nombreBD" => "correo_proveedor",
          "tablaBD" => "proveedores",
          "requerido" => true,
          'debeSerUnico' => true
        ],
        'direccion_proveedor' => [
          ...molDescripcion,
          "nombreAlerta" => "dirección del proveedor",
          "nombreBD" => "direccion_proveedor",
          "tablaBD" => "proveedores",
        ],
      ],
      'requerido' => $requerido
    ];

    if (isset($info['prefijo_telefono_proveedor'])) {
      $info['telefono_proveedor'] = $info['prefijo_telefono_proveedor'] . $info['telefono_proveedor'];
    }
    if (isset($info['codigo_rif_proveedor'])) {
      $info['rif_proveedor'] = $info['codigo_rif_proveedor'] . $info['rif_proveedor'];
    }
    if (isset($info['esR'])) {
      unset($esquemaProveedores['propiedades']['rif_proveedor']['debeExistirBD']);
      unset($info['esR']);
    }
    $v = $this->limpiarValidar($info, $esquemaProveedores);
    if ($v) return $v;

    return false;
  }
  public function seleccionarProveedores(array $info) {
    if (($info['rif_proveedor'] ?? '') != "") {
      $v = $this->validarProveedores('ver detalles de los proveedores', $info);
    } else {
      $v = $this->validarProveedores('listar', $info);
    }
    if ($v) return $v;
    $this->rifProveedor = $info['rif_proveedor'] ?? '';

    return $this->seleccionarProveedoresP();
  }
  public function registrarProveedores(array $info) {
    $info['esR'] = true;

    $v = $this->validarProveedores('registrar', $info, [
      'rif_proveedor',
      'razon_social_proveedor',
      'telefono_proveedor',
      'correo_proveedor',
      'direccion_proveedor',
    ]);
    if ($v) return $v;

    $this->rifProveedor = $info['rif_proveedor'];
    $this->razonSocialProveedor = $info['razon_social_proveedor'];
    $this->telefonoProveedor = $info['telefono_proveedor'];
    $this->correoProveedor = $info['correo_proveedor'];
    $this->direccionProveedor = $info['direccion_proveedor'];

    return $this->registrarProveedoresP();
  }
  public function actualizarProveedores(array $info) {
    $v = $this->validarProveedores('actualizar', $info, [

      'rif_proveedor' => 'rif_proveedor_act',
      'razon_social_proveedor',
      'telefono_proveedor',
      'correo_proveedor',
      'direccion_proveedor',
    ]);

    if ($v) return $v;
    $this->rifProveedor = $info['rif_proveedor'];
    $this->razonSocialProveedor = $info['razon_social_proveedor'];
    $this->telefonoProveedor = $info['telefono_proveedor'];
    $this->correoProveedor = $info['correo_proveedor'];
    $this->direccionProveedor = $info['direccion_proveedor'];

    return $this->actualizarProveedoresP();
  }
  public function eliminarProveedores(array $info) {
    $v = $this->validarProveedores('eliminar', $info, [
      'rif_proveedor',
    ]);
    if ($v) return $v;
    $this->rifProveedor = $info['rif_proveedor'];
    return $this->eliminarProveedoresP($info);
  }

  // PRIVADOS
  private function seleccionarProveedoresP() {
    if ($this->rifProveedor == null || $this->rifProveedor == "") {
      $resultado = $this->seleccionarDatos2([
        'campos' => '
          rif_proveedor, razon_social_proveedor, 
          telefono_proveedor, correo_proveedor, direccion_proveedor
        ',
        'tabla' => 'proveedores',
      ]);
      return $resultado->fetchAll();
    } else {
      return $this->seleccionarDatos2([
        'campos' => '
          rif_proveedor, razon_social_proveedor, 
          telefono_proveedor, correo_proveedor, direccion_proveedor
        ',
        'tabla' => 'proveedores',
        'WHERE' => [
          "rif_proveedor" => $this->rifProveedor,
        ]
      ])->fetch();
    }
  }
  private function registrarProveedoresP() {
    $ultimoID = $this->guardarDatos2([
      'tabla' => 'proveedores',
      'datos' => [
        "rif_proveedor" => $this->rifProveedor,
        "razon_social_proveedor" => $this->razonSocialProveedor,
        "telefono_proveedor" => $this->telefonoProveedor,
        "correo_proveedor" => $this->correoProveedor,
        "direccion_proveedor" => $this->direccionProveedor
      ],
      'WHERE' => [
        "rif_proveedor" => $this->rifProveedor
      ]
    ]);

    if ($ultimoID !== false && $ultimoID > 0) {
      $alerta = [
        "tipo" => "limpiarYcerrar",
        "titulo" => "Proveedor registrado",
        "texto" => "El proveedor ha sido registrado exitosamente",
        "icono" => "success",
      ];
      $this->commit();
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Proveedor no registrado",
        "texto" => "El proveedor no ha sido registrado exitosamente",
        "icono" => "error",
      ];
    }
    return $alerta;
  }
  private function actualizarProveedoresP() {
    $resultado = $this->actualizarDatos2([
      "tabla" => "proveedores",
      "datos" => [
        "rif_proveedor" => $this->rifProveedor,
        "razon_social_proveedor" => $this->razonSocialProveedor,
        "telefono_proveedor" => $this->telefonoProveedor,
        "correo_proveedor" => $this->correoProveedor,
        "direccion_proveedor" => $this->direccionProveedor
      ],
      "WHERE" => [
        "rif_proveedor" => $this->rifProveedor,
      ]
    ]);

    if ($resultado == false || $resultado <= 0) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Proveedor no actualizado",
        "texto" => "El proveedor no ha sido actualizado exitosamente",
        "icono" => "warning",
      ];
    } else {
      $alerta = [
        "tipo" => "limpiarYcerrar",
        "titulo" => "Proveedor actualizado",
        "texto" => "El proveedor ha sido actualizado exitosamente",
        "icono" => "success",
      ];
      $this->commit();
    }
    return $alerta;
  }
  private function eliminarProveedoresP() {
    $eliminarProveedor = $this->eliminarDatos2([
      'tabla' => 'proveedores',
      'WHERE' => [
        'rif_proveedor' => $this->rifProveedor
      ]
    ]);
    if ($eliminarProveedor == 1) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Proveedor eliminado",
        "texto" => "El proveedor ha sido eliminado exitosamente",
        "icono" => "success",
      ];
      $this->commit();
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Proveedor no encontrado",
        "texto" => "El proveedor no existe en la base de datos",
        "icono" => "error",
      ];
    }
    return $alerta;
  }
}
