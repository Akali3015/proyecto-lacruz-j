<?php

namespace src\modelos;

use src\config\connect\conexion;
use src\modelos\traitModelo;
use PDO;

class validacionesModelo {
  use traitModelo;
  static private conexion $objetoBD;
  private array $esquema = [];

  public function __construct(mixed $valor) {
    $this->esquema = [
      'campo_valor' => $valor
    ];
  }

  //Validaciones de formato
  private function tipo(string $tipo) {
    $this->esquema['tipo_dato'] = $tipo;
  }
  private function requerido($mensajeSiNoSeCumple = false) {
    $this->esquema['requerido'] = true;
    return $this;
  }
  private function min(int $minimo, $mensajeSiNoSeCumple = false) {
    $this->esquema['minimo'] = [$minimo, $mensajeSiNoSeCumple];
    return $this;
  }
  private function max(int $maximo, $mensajeSiNoSeCumple = false) {
    $this->esquema['maximo'] = $maximo;
    return $this;
  }
  private function regex(string $regex, $mensajeSiNoSeCumple = false) {
    $this->esquema['regex'] = [$regex, $mensajeSiNoSeCumple];
    return $this;
  }
  private function nombreAlerta(string $nombre) {
    $this->esquema['nombre_alerta'] = $nombre;
    return $this;
  }
  private function mayorA(int $numero,$mensajeSiNoSeCumple = false){
    $this->esquema['debe_ser_mayor_a'] = [$numero,$mensajeSiNoSeCumple];
    return $this;
  }
  private function menorA(int $numero,$mensajeSiNoSeCumple = false){
    $this->esquema['debe_ser_menor_a'] = [$numero,$mensajeSiNoSeCumple];
    return $this;
  }
  private function entre(int $este,int $esteOtro, $mensajeSiNoSeCumple = false){
    $this->esquema['estar_entre'] = [$este,$esteOtro,$mensajeSiNoSeCumple];
    return $this;
  }
  
  //Validaciones con la BD
  private function nombreAttrBD(string $nombre) {
    $this->esquema['nombre_attr_bd'] = $nombre;
    return $this;
  }
  private function nombreTablaBD(string $nombre) {
    $this->esquema['nombre_tabla_bd'] = $nombre;
    return $this;
  }
  private function debeSerUnicoBD($mensajeSiNoSeCumple = false) {
    $this->esquema['debe_ser_unico_bd'] = [true, $mensajeSiNoSeCumple];
    return $this;
  }
  private function debeExistirBD($mensajeSiNoSeCumple = false) {
    $this->esquema['debe_existir_bd'] = [true, $mensajeSiNoSeCumple];
    return $this;
  }

  //Formateo de valor
  private function comaPunto(){
    $this->esquema['comaPunto']=true;
    return $this;
  }
  private function mayusculas(){
    $this->esquema['mayusculas']=true;
    return $this;
  }
  private function minusculas(){
    $this->esquema['minusculas']=true;
    return $this;
  }

  private function validar() {

    if ($this->esquema['tipo'] != 'array') {
      if (
        $this->objetoBD == null &&
        (isset($this->esquema['debe_ser_unico_bd']) || isset($this->esquema['debe_existir_bd']))
      ) {
        $this->objetoBD = new conexion;
      }

      //Para evitar la inyección de SQL
      if (isset($this->esquema['campo_valor'])) {
        $this->esquema['campo_valor'] = $this->limpiarCadena($this->esquema['campo_valor']);
      }

      //Cantidades numericas de tipo float
      if (isset($this->esquema['comaPunto'])) {
        $this->esquema['campo_valor'] = str_replace('.', '', $this->esquema['campo_valor']);
        $this->esquema['campo_valor'] = str_replace(',', '.', $this->esquema['campo_valor']);
        $this->esquema['campo_valor'] = (float)$this->esquema['campo_valor'];
      }

      if(isset($this->esquema['menor_a'])){
        
      }
      if (isset($this->esquema['noCero'])) {
        if ($this->esquema['campo_valor'] <= 0) {
          $alerta = [
            "tipo" => "simple",
            "titulo" => "Cantidad en 0",
            "texto" => 'No puedes enviar el formulario con el this->esquema de ' . $this->esquema['formulario_nombre'] . ' en 0',
            "icono" => "error",
          ];
          return ($alerta);
        }
      }

      //Para validar campos requeridos
      if (isset($this->esquema['requerido'])) {
        if (!isset($this->esquema['campo_valor']) || $this->esquema['campo_valor'] == "") {
          $alerta = [
            "tipo" => "simple",
            "titulo" => "this->esquema de " . $this->esquema['formulario_nombre'] . " obligatorio",
            "texto" => 'No puedes enviar el formulario sin llenar el this->esquema de ' . $this->esquema['formulario_nombre'] . ', por favor verifique e intente de nuevo ',
            "icono" => "error",
          ];
          return ($alerta);
        }
      }

      //Para validar el largo y minimo
      if (isset($this->esquema['maximo'])) {
        if ($this->esquema['campo_valor'] != "") {
          if (mb_strlen($this->esquema['campo_valor']) > $this->esquema['maximo']) {
            $alerta = [
              "tipo" => "simple",
              "titulo" => "this->esquema de " . $this->esquema['formulario_nombre'] . " muy largo",
              "texto" => "El this->esquema de " . $this->esquema['formulario_nombre'] . " no puede tener más de " . $this->esquema['maximo'] . " carácteres de longitud: " . $this->esquema['campo_valor'],
              "icono" => "error",
            ];
            return $alerta;
          } elseif (mb_strlen($this->esquema['campo_valor']) < $this->esquema['minimo']) {
            $alerta = [
              "tipo" => "simple",
              "titulo" => "this->esquema de " . $this->esquema['formulario_nombre'] . " muy corto",
              "texto" => "El this->esquema de " . $this->esquema['formulario_nombre'] . " no puede tener menos de " . $this->esquema['minimo'] . " carácteres de longitud: " . $this->esquema['campo_valor'],
              "icono" => "error",
            ];
            return $alerta;
          }
        }
      }

      //Para validar el formato del this->esquema con expresiones regulares
      if (isset($this->esquema['expresion_re'])) {
        if ($this->esquema['campo_valor'] != "") {
          if (!preg_match("/" . $this->esquema['expresion_re'] . "/", $this->esquema['campo_valor'])) {
            $alerta = [
              "tipo" => "simple",
              "titulo" => "Formato de " . $this->esquema['formulario_nombre'] . " inválido",
              "texto" => "El formato del this->esquema " . $this->esquema['formulario_nombre'] . " no es correcto, por favor verifique e intente de nuevo.",
              "icono" => "error",
            ];
            return ($alerta);
            exit();
          }
        }
      }

      //Para verificar la existencia de un registro para su actualización [normalmente solo el ID del registro]
      if (isset($this->esquema['debeExistir'])) {
        $registrosExistentes = $this->objetoBD->seleccionarDatos2([
          'campos' => '*',
          'tabla' =>  $this->esquema['tabla'],
          'BD' => ($this->esquema['BD'] ?? NULL),
          'WHERE' => [
            $this->esquema['campo_nombre'] => $this->esquema['campo_valor']
          ]
        ]);

        if ($registrosExistentes->rowCount() == 0 && isset($this->esquema['requerido'])) {
          return [
            "tipo" => "simple",
            "titulo" => "Dato no encontrado",
            "texto" => "El valor que ha introducido en el this->esquema de " . $this->esquema['formulario_nombre'] . " no se encuentra registrado dentro de la base de datos del sistema, por favor verifique e intente de nuevo: " . $this->esquema['campo_valor'],
            "icono" => "error",
          ];
        } else {
          $registrosExis[$this->esquema['tabla']] = $registrosExistentes->fetch();
        }
      }

      //Para verificar que no haya mas registros con ese valor
      if (isset($this->esquema['debeSerUnico'])) {

        $buscarEnLaBD = false;
        if (isset($registrosExis[$this->esquema['tabla']])) { //es actualizar
          //Verificar si ya la info fue o no obtenida de la BD
          if (!isset($registrosExis[$this->esquema['tabla']][$this->esquema['campo_nombre']])) {
            $resultado = $this->objetoBD->seleccionarDatos2([
              'campos' => '*',
              'tabla' =>  $this->esquema['tabla'],
              'BD' => ($this->esquema['BD'] ?? NULL),
              'WHERE' => [
                $this->esquema['campo_nombre'] => $this->esquema['campo_valor']
              ]
            ]);
            $resultado = $resultado->fetch();
            $registrosExis[$this->esquema['tabla']] = $resultado;
          }
          //Consultamos entonces si en la BD no hay otro registro con ese valor unico asignado
          if (
            $registrosExis[$this->esquema['tabla']][$this->esquema['campo_nombre']] != $this->esquema['campo_valor'] &&
            strtoupper($registrosExis[$this->esquema['tabla']][$this->esquema['campo_nombre']]) != strtoupper($this->esquema['campo_valor'])
          ) {
            $buscarEnLaBD = true;
          }
        } else { //es registrar
          $buscarEnLaBD = true;
        }

        //Buscamos el dato a ver si existe en la BD
        if ($buscarEnLaBD) {
          $checkRegistro = $this->objetoBD->seleccionarDatos2([
            'campos' => $this->esquema['campo_nombre'],
            'tabla' =>  $this->esquema['tabla'],
            'BD' => ($this->esquema['BD'] ?? NULL),
            'WHERE' => [
              $this->esquema['campo_nombre'] => $this->esquema['campo_valor']
            ]
          ]);
          if ($checkRegistro->rowCount() > 0) {
            return [
              "tipo" => "simple",
              "titulo" => "Valor de " . $this->esquema['formulario_nombre'] . " duplicado",
              "texto" => "El valor que ha introducido en el this->esquema de " . $this->esquema['formulario_nombre'] . " ya se encuentra registrado y no se puede duplicar, por favor verifique e intente de nuevo",
              "icono" => "error",
            ];
          }
        }
      }

      //Para validar si dos campos son iguales
      if (isset($this->esquema['camposIguales'])) {
        if ($this->esquema['campo_valor'] != $this->esquema['camposIguales']) {
          $alerta = [
            "tipo" => "simple",
            "titulo" => "Desigualdad de valores",
            "texto" => "El valor de ambos campos de " . $this->esquema['formulario_nombre'] . " deben ser iguales, verifique e intente nuevamente",
            "icono" => "error",
          ];
          return ($alerta);
        }
      }

      //para evitar que un dato específico sea eliminado o alguna otra operación
      if (isset($this->esquema['camposDiferentes'])) {
        $compararValores = function ($campoCom1, $campoCom2) use ($this->esquema) {
          if ($campoCom1 == $campoCom2) {
            $alerta = [
              "tipo" => "simple",
              "titulo" => "ERROR",
              "texto" => "El valor de " . $this->esquema['formulario_nombre'] . " no puede ser usado en esa operación",
              "icono" => "error",
            ];
            return ($alerta);
          }
          return false;
        };
        if (is_array($this->esquema['camposDiferentes'])) {
          foreach ($this->esquema['camposDiferentes'] as $campoDiferente) {
            $resultado = $compararValores($this->esquema['campo_valor'], $campoDiferente);
            if ($resultado) return $resultado;
          }
        } else {
          $resultado = $compararValores($this->esquema['campo_valor'], $this->esquema['camposDiferentes']);
          if ($resultado) return $resultado;
        }
      }
    }
  }
}
