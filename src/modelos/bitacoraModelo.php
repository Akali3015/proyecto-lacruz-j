<?php

namespace src\modelos;

use src\config\connect\conexion;
use PDO;

class bitacoraModelo extends conexion {
  private string $moduloBitacora = '';
  private string $accionBitacora = '';
  private string $resultadoBitacora = '';
  private string $ipDispositivo = '';
  private ?array $cambiosEfectuados = null;
  private int $commit = 0;
  private array $clavesIdentificacion = [];

  public function seleccionarBitacora() {
      return $this->seleccionarBitacoraP();
  }
  public function registrarBitacora(string $modulo, string $accion, string $resultado, bool|null $hacerCommit = null, $datos = null, $datosDespues = null,array|null $clavesIdentificacion = null) {
    $this->moduloBitacora = $modulo;
    $this->accionBitacora = $accion;
    $this->resultadoBitacora = $resultado;
    $this->commit = $hacerCommit ?? false;
    $this->ipDispositivo = $_SERVER['HTTP_X_IP_DISPOSITIVO'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $this->clavesIdentificacion = $clavesIdentificacion ?? [];

    $this->cambiosEfectuados = $this->procesarDatosBitacora($datos, $datosDespues);

    $campos = [
      [
        "campo_valor" => $this->moduloBitacora,
        "formulario_nombre" => "nombre del modulo",
        "requerido" => true,
        "minimo" => minRegexNombreObj,
        "maximo" => maxRegexNombreObj,
        "expresion_re" => regexNombreObj,
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
      ],
      [
        "campo_valor" => $this->ipDispositivo,
        "formulario_nombre" => "IP del dispositivo",
        "requerido" => false,
        "minimo" => 5,
        "maximo" => 25,
      ]
    ];

    $respuesta = $this->limpiar_Verificar($campos);
    if ($respuesta !== false) return $respuesta;
    return $this->registrarBitacoraP();
  }

  private function procesarDatosBitacora($datos = null, $datosDespues = null): ?array {
    if ($datos === null && $datosDespues === null) {
        return null;
    }

    if (is_array($datos) && is_array($datosDespues)) {
        $cambios = $this->compararRecursivo($datos, $datosDespues);
        return empty($cambios) ? null : $cambios;
    }

    if (is_array($datos) && !empty($datos)) {
        return $datos;
    }

    return null;
  }
  private function compararRecursivo(array $datosAntes, array $datosDespues): array {
    $cambios = [];
    $camposIgnorar = ['contrasena_usuario'];
    
    $esLista = $this->esArrayIndexado($datosAntes) || $this->esArrayIndexado($datosDespues);
    
    if ($esLista) {
        $resultado = $this->compararListas($datosAntes, $datosDespues);
        return $resultado;
    }
    
    foreach ($datosDespues as $campo => $nuevoValor) {
        if (in_array($campo, $camposIgnorar)) continue;
        
        if (!array_key_exists($campo, $datosAntes)) {
            $cambios[$campo] = ['anterior' => null, 'nuevo' => $nuevoValor];
            continue;
        }
        
        $valorAnterior = $datosAntes[$campo];
        
        if (is_array($nuevoValor) && is_array($valorAnterior)) {
            $cambiosAnidados = $this->compararRecursivo($valorAnterior, $nuevoValor);
            if (!empty($cambiosAnidados)) {
                $cambios[$campo] = $cambiosAnidados;
            }
            continue;
        }
        
        if (is_array($nuevoValor) || is_array($valorAnterior)) {
            $cambios[$campo] = ['anterior' => $valorAnterior, 'nuevo' => $nuevoValor];
            continue;
        }
        
        if ($valorAnterior !== $nuevoValor) {
            $cambios[$campo] = ['anterior' => $valorAnterior, 'nuevo' => $nuevoValor];
        }
    }
    
    foreach ($datosAntes as $campo => $valorAnterior) {
        if (!array_key_exists($campo, $datosDespues) && !in_array($campo, $camposIgnorar)) {
            $cambios[$campo . '_eliminado'] = ['anterior' => $valorAnterior, 'nuevo' => null];
        }
    }
    
    return $cambios;
  }
  private function esArrayIndexado(array $array): bool {
    if (empty($array)) return true;
    
    $keys = array_keys($array);
    return $keys === range(0, count($array) - 1);
  }
  private function detectarClaveIdentificacion(array $lista): ?string {
    if (empty($lista)) return null;
    
    $primerElemento = $lista[0];
    if (!is_array($primerElemento)) return null;
    
    if (!empty($this->clavesIdentificacion)) {
        foreach ($this->clavesIdentificacion as $clave) {
            if (isset($primerElemento[$clave])) {
                return $clave;
            }
        }
    }
    
    $prioridad = [
        '/^id_/' => 10,
        '/_id$/' => 10,
        '/id/' => 5,
        '/rif/' => 4,
        '/cedula/' => 4,
        '/codigo/' => 3,
    ];
    
    $mejorClave = null;
    $mejorPuntaje = 0;
    
    foreach (array_keys($primerElemento) as $campo) {
        $campoLower = strtolower($campo);
        $puntaje = 0;
        
        foreach ($prioridad as $patron => $valor) {
            if (preg_match($patron, $campoLower)) {
                $puntaje += $valor;
            }
        }
        
        $valor = $primerElemento[$campo];
        if (is_string($valor) && preg_match('/^[A-Z0-9\-]+$/', $valor)) {
            $puntaje += 2;
        }
        
        if ($puntaje > $mejorPuntaje) {
            $mejorPuntaje = $puntaje;
            $mejorClave = $campo;
        }
    }
    
    return $mejorClave;
  }
  private function obtenerNombreItem(array $item, string $claveId): string {
      foreach ($item as $campo => $valor) {
          $campoLower = strtolower($campo);
  
          if ((strpos($campoLower, 'nombre') !== false || 
               strpos($campoLower, 'razon') !== false ||
               strpos($campoLower, 'descripcion') !== false) && 
              is_string($valor) && 
              trim($valor) !== '' && 
              $valor != ($item[$claveId] ?? '')) {
              return $valor;
          }
      }
      
      return $item[$claveId] ?? 'Sin nombre';
  }
  private function compararListas(array $listaAntes, array $listaDespues): array {
    $cambios = [];
    $cambios['_lista'] = true;

    $claveId = $this->detectarClaveIdentificacion($listaAntes) 
               ?? $this->detectarClaveIdentificacion($listaDespues);

    if ($claveId === null) {
        foreach ($listaAntes as $itemAntes) {
            if (!in_array($itemAntes, $listaDespues, true)) {
                $cambios['_eliminados'][] = $itemAntes;
            }
        }
        foreach ($listaDespues as $itemDespues) {
            if (!in_array($itemDespues, $listaAntes, true)) {
                $cambios['_agregados'][] = $itemDespues;
            }
        }
        
        if (!isset($cambios['_eliminados']) && !isset($cambios['_agregados'])) {
            return [];
        }
        
        return $cambios;
    }

    $modificados = [];
    foreach ($listaAntes as $itemAntes) {
        if (!is_array($itemAntes)) continue;
        
        foreach ($listaDespues as $itemDespues) {
            if (!is_array($itemDespues)) continue;
            
            if (isset($itemAntes[$claveId]) && isset($itemDespues[$claveId])) {
                if ($itemAntes[$claveId] === $itemDespues[$claveId]) {
                    $diferencias = $this->compararRecursivo($itemAntes, $itemDespues);
                    if (!empty($diferencias)) {
                        $nombre = $this->obtenerNombreItem($itemAntes, $claveId);
                        
                        if ($nombre != $itemAntes[$claveId]) {
                            $modificados[] = [
                                'id' => $itemAntes[$claveId],
                                'nombre' => $nombre,
                                'cambios' => $diferencias
                            ];
                        } else {
                            $modificados[] = [
                                'id' => $itemAntes[$claveId],
                                'cambios' => $diferencias
                            ];
                        }
                    }
                }
            }
        }
    }

    $idsDespues = array_column($listaDespues, $claveId);
    $eliminados = [];
    foreach ($listaAntes as $itemAntes) {
        if (!is_array($itemAntes)) continue;
        if (isset($itemAntes[$claveId]) && !in_array($itemAntes[$claveId], $idsDespues)) {
            $eliminados[] = $itemAntes;
        }
    }

    $idsAntes = array_column($listaAntes, $claveId);
    $agregados = [];
    foreach ($listaDespues as $itemDespues) {
        if (!is_array($itemDespues)) continue;
        if (isset($itemDespues[$claveId]) && !in_array($itemDespues[$claveId], $idsAntes)) {
            $agregados[] = $itemDespues;
        }
    }

    $hayCambios = !empty($modificados) || !empty($eliminados) || !empty($agregados);
    
    if (!$hayCambios) {
        return [];
    }

    if (!empty($modificados)) {
        $cambios['_modificados'] = $modificados;
    }
    
    if (!empty($eliminados)) {
        $cambios['_eliminados'] = $eliminados;
    }
    
    if (!empty($agregados)) {
        $cambios['_agregados'] = $agregados;
    }

    return $cambios;
  }
  private function elementosIguales(array $a, array $b): bool {
    if (count($a) !== count($b)) return false;
    
    foreach ($a as $key => $value) {
        if (!isset($b[$key])) return false;
        if ($value !== $b[$key]) return false;
    }
    return true;
  }
  private function seleccionarBitacoraP() {
    $resultado = $this->seleccionarDatos2([
      'campos' => '
        b.id_bitacora, 
        u.nombre_usuario, 
        u.apellido_usuario,
        m.nombre_modulo as modulo_bitacora,
        b.accion, 
        b.fecha_bitacora, 
        b.resultado_bitacora, 
        b.ip_dispositivo, 
        b.cambios_efectuados
      ',
      'tabla' => 'bitacora as b',
      'BD' => 'seguridad',
      'datosJoins' => [
        "usuarios as u" => "b.cedula_usuario = u.cedula_usuario",
        "modulos as m" => "b.id_modulo = m.id_modulo"
      ],
      'ORDER' => 'b.id_bitacora DESC'
    ]);
    
    $Bitacora = $resultado->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($Bitacora as &$item) {
      if (!empty($item['cambios_efectuados'])) {
        $item['cambios_efectuados'] = json_decode($item['cambios_efectuados'], true);
      }
    }
    unset($item);
    
    return $Bitacora;
  }
  private function registrarBitacoraP() {
    $idModulo = $this->VEYSNEC([
      'RSEN' => true,
      'campos' => 'id_modulo',
      'tabla' => 'modulos',
      'BD' => 'seguridad',
      'WHERE' => [
        'nombre_modulo' => $this->moduloBitacora
      ]
    ]);

    $datosBitacora = [
      "cedula_usuario" => $_SESSION['cedula'],
      "id_modulo" => $idModulo,
      "resultado_bitacora" => $this->resultadoBitacora,
      "accion" => $this->accionBitacora,
      "ip_dispositivo" => $this->ipDispositivo,
      "fecha_bitacora" => $this->FechaHora_Sel('fecha_hora_BD'),
    ];

    if ($this->cambiosEfectuados !== null && !empty($this->cambiosEfectuados)) {
      $datosBitacora["cambios_efectuados"] = json_encode($this->cambiosEfectuados, JSON_UNESCAPED_UNICODE);
    }

    $ultimoId = $this->guardarDatos2([
      'tabla' => 'bitacora',
      'BD' => 'seguridad',
      'datos' => $datosBitacora,
    ]);

    if ($ultimoId !== false && $ultimoId > 0) {
      if ($this->commit) $this->commit();
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