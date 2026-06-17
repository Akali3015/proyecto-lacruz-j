<?php

namespace src\modelos;

use PDO;
use PDOStatement;
use DateTime;
use DateTimeZone;
use DateInterval;
use FPDF;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Logo\Logo;

use src\modelos\accesosModelo;
use src\modelos\cacheModelo;
use src\config\connect\conexion;

trait traitModelo {
  private cacheModelo|null $objCache = null;
  protected array $imgTrans = [];
  private ?conexion $objetoBD = null;
  private array $cacheVal = [];

  public function limpiarCadena(string $cadena, $modo = 'antiSQLInyection') {
    $palabras = [];
    if ($modo == 'antiSQLInyection') {
      $palabras = [
        "<script>",
        "</script>",
        "<script src",
        "<script type=",
        "SELECT * FROM",
        "SELECT ",
        "SELECT",
        "INSERT INTO",
        "UPDATE ",
        "UPDATE",
        "DELETE FROM",
        "SET",
        "SET ",
        "DROP TABLE",
        "DROP DATABASE",
        "TRUNCATE TABLE",
        "SHOW TABLES",
        "SHOW DATABASES",
        "<?php",
        "?>",
        "--",
        "^",
        "<",
        ">",
        "==",
        "=",
        ";",
        "::"
      ];
    } elseif ('antiFuncionesSQL') {
      $palabras = [
        ')',
        'DATE(',
        'CAST(',
        'CONVERT(',
        'AVG(',
        'SUM(',
        'COUNT(',
        'MAX(',
        'MIN(',
        'TRIM(',
        'LOWER(',
        'UPPER(',
        'COALESCE('
      ];
    }

    $cadena = trim($cadena);
    $cadena = stripslashes($cadena);

    foreach ($palabras as $palabra) {
      $cadena = str_ireplace($palabra, "", $cadena);
    }

    $cadena = trim($cadena);
    $cadena = stripslashes($cadena);

    return $cadena;
  }
  public function limpiar_Verificar(array $campos) {
    $registrosExis = [];
    foreach ($campos as &$campo) {
      if (isset($campo['imagen'])) {
        $validarImagenes = function ($imagen) use ($campo) {
          if (isset($campo['requerido']) && (($imagen['name'] ?? '') == "" || ($imagen['size'] ?? '') <= 0)) {
            return [
              "tipo" => "simple",
              "titulo" => "Imagen Obligatoria",
              "texto" => "La imagen de " . $campo['formulario_nombre'] . ' es obligatoria',
              "icono" => "error",
            ];
          }
          if ($imagen['name'] != "" && $imagen['size'] > 0) {
            if (
              mime_content_type($imagen['tmp_name']) != "image/jpeg" &&
              mime_content_type($imagen['tmp_name']) != "image/png"
            ) {
              return [
                "tipo" => "simple",
                "titulo" => "Formato inválido",
                "texto" => "El formato del archivo seleccionado es incorrecto",
                "icono" => "error",
              ];
            }
            if (
              ($imagen['size']) / 1048576 > 5
            ) {
              $alerta = [
                "tipo" => "simple",
                "titulo" => "Archivo muy pesado",
                "texto" => "El tamaño del archivo excede los 5MB permitidos por el sistema",
                "icono" => "error",
              ];
              return $alerta;
            }
          }
          return false;
        };
        if (is_array(($campo['imagen']['name'] ?? ''))) {
          for ($i = 0; $i < count($campo['imagen']['name']); $i++) {
            $archivoIndividual = [
              'full_path' => $campo['imagen']['full_path'][$i],
              'name' => $campo['imagen']['name'][$i],
              'size' => $campo['imagen']['size'][$i],
              'tmp_name' => $campo['imagen']['tmp_name'][$i],
              'type' => $campo['imagen']['type'][$i],
            ];
            $resultado = $validarImagenes($archivoIndividual);
            if ($resultado) return $resultado;
          }
        } else {
          $resultado = $validarImagenes($campo['imagen']);
          if ($resultado) return $resultado;
        }
      } else {
        if (
          $this->objetoBD == null &&
          (isset($campo['debeExistir']) || isset($campo['debeSerUnico']))
        ) {
          $this->objetoBD = new conexion;
        }

        //Para evitar la inyección de SQL
        if (isset($campo['campo_valor'])) {
          $campo['campo_valor'] = $this->limpiarCadena($campo['campo_valor']);
        }

        //Cantidades numericas de tipo float
        if (isset($campo['comaPunto'])) {
          $campo['campo_valor'] = str_replace('.', '', $campo['campo_valor']);
          $campo['campo_valor'] = str_replace(',', '.', $campo['campo_valor']);
          $campo['campo_valor'] = (float)$campo['campo_valor'];
        }
        if (isset($campo['noCero'])) {
          if ($campo['campo_valor'] <= 0) {
            $alerta = [
              "tipo" => "simple",
              "titulo" => "Cantidad en 0",
              "texto" => 'No puedes enviar el formulario con el campo de ' . $campo['formulario_nombre'] . ' en 0',
              "icono" => "error",
            ];
            return ($alerta);
          }
        }

        //Para validar campos requeridos
        if (isset($campo['requerido'])) {
          if (!isset($campo['campo_valor']) || $campo['campo_valor'] == "") {
            $alerta = [
              "tipo" => "simple",
              "titulo" => "Campo de " . $campo['formulario_nombre'] . " obligatorio",
              "texto" => 'No puedes enviar el formulario sin llenar el campo de ' . $campo['formulario_nombre'] . ', por favor verifique e intente de nuevo ',
              "icono" => "error",
            ];
            return ($alerta);
          }
        }

        //Para validar el largo y minimo
        if (isset($campo['maximo'])) {
          if ($campo['campo_valor'] != "") {
            if (mb_strlen($campo['campo_valor']) > $campo['maximo']) {
              $alerta = [
                "tipo" => "simple",
                "titulo" => "Campo de " . $campo['formulario_nombre'] . " muy largo",
                "texto" => "El campo de " . $campo['formulario_nombre'] . " no puede tener más de " . $campo['maximo'] . " carácteres de longitud: " . $campo['campo_valor'],
                "icono" => "error",
              ];
              return $alerta;
            } elseif (mb_strlen($campo['campo_valor']) < $campo['minimo']) {
              $alerta = [
                "tipo" => "simple",
                "titulo" => "Campo de " . $campo['formulario_nombre'] . " muy corto",
                "texto" => "El campo de " . $campo['formulario_nombre'] . " no puede tener menos de " . $campo['minimo'] . " carácteres de longitud: " . $campo['campo_valor'],
                "icono" => "error",
              ];
              return $alerta;
            }
          }
        }

        //Para validar el formato del campo con expresiones regulares
        if (isset($campo['expresion_re'])) {
          if ($campo['campo_valor'] != "") {
            if (!preg_match("/" . $campo['expresion_re'] . "/", $campo['campo_valor'])) {
              $alerta = [
                "tipo" => "simple",
                "titulo" => "Formato de " . $campo['formulario_nombre'] . " inválido",
                "texto" => "El formato del campo " . $campo['formulario_nombre'] . " no es correcto, por favor verifique e intente de nuevo.",
                "icono" => "error",
              ];
              return ($alerta);
              exit();
            }
          }
        }

        //Para verificar la existencia de un registro para su actualización [normalmente solo el ID del registro]
        if (isset($campo['debeExistir'])) {
          $registrosExistentes = $this->objetoBD->seleccionarDatos2([
            'campos' => '*',
            'tabla' =>  $campo['tabla'],
            'BD' => ($campo['BD'] ?? NULL),
            'WHERE' => [
              $campo['campo_nombre'] => $campo['campo_valor']
            ]
          ]);

          if ($registrosExistentes->rowCount() == 0 && isset($campo['requerido'])) {
            return [
              "tipo" => "simple",
              "titulo" => "Dato no encontrado",
              "texto" => "El valor que ha introducido en el campo de " . $campo['formulario_nombre'] . " no se encuentra registrado dentro de la base de datos del sistema, por favor verifique e intente de nuevo: " . $campo['campo_valor'],
              "icono" => "error",
            ];
          } else {
            $registrosExis[$campo['tabla']] = $registrosExistentes->fetch();
          }
        }

        //Para verificar que no haya mas registros con ese valor
        if (isset($campo['debeSerUnico'])) {

          $buscarEnLaBD = false;
          if (isset($registrosExis[$campo['tabla']])) { //es actualizar
            //Verificar si ya la info fue o no obtenida de la BD
            if (!isset($registrosExis[$campo['tabla']][$campo['campo_nombre']])) {
              $resultado = $this->objetoBD->seleccionarDatos2([
                'campos' => '*',
                'tabla' =>  $campo['tabla'],
                'BD' => ($campo['BD'] ?? NULL),
                'WHERE' => [
                  $campo['campo_nombre'] => $campo['campo_valor']
                ]
              ]);
              $resultado = $resultado->fetch();
              $registrosExis[$campo['tabla']] = $resultado;
            }
            //Consultamos entonces si en la BD no hay otro registro con ese valor unico asignado
            if (
              $registrosExis[$campo['tabla']][$campo['campo_nombre']] != $campo['campo_valor'] &&
              strtoupper($registrosExis[$campo['tabla']][$campo['campo_nombre']]) != strtoupper($campo['campo_valor'])
            ) {
              $buscarEnLaBD = true;
            }
          } else { //es registrar
            $buscarEnLaBD = true;
          }

          //Buscamos el dato a ver si existe en la BD
          if ($buscarEnLaBD) {
            $checkRegistro = $this->objetoBD->seleccionarDatos2([
              'campos' => $campo['campo_nombre'],
              'tabla' =>  $campo['tabla'],
              'BD' => ($campo['BD'] ?? NULL),
              'WHERE' => [
                $campo['campo_nombre'] => $campo['campo_valor']
              ]
            ]);
            if ($checkRegistro->rowCount() > 0) {
              return [
                "tipo" => "simple",
                "titulo" => "Valor de " . $campo['formulario_nombre'] . " duplicado",
                "texto" => "El valor que ha introducido en el campo de " . $campo['formulario_nombre'] . " ya se encuentra registrado y no se puede duplicar, por favor verifique e intente de nuevo",
                "icono" => "error",
              ];
            }
          }
        }

        //Para validar si dos campos son iguales
        if (isset($campo['camposIguales'])) {
          if ($campo['campo_valor'] != $campo['camposIguales']) {
            $alerta = [
              "tipo" => "simple",
              "titulo" => "Desigualdad de valores",
              "texto" => "El valor de ambos campos de " . $campo['formulario_nombre'] . " deben ser iguales, verifique e intente nuevamente",
              "icono" => "error",
            ];
            return ($alerta);
          }
        }

        //para evitar que un dato específico sea eliminado o alguna otra operación
        if (isset($campo['camposDiferentes'])) {
          $compararValores = function ($campoCom1, $campoCom2) use ($campo) {
            if ($campoCom1 == $campoCom2) {
              $alerta = [
                "tipo" => "simple",
                "titulo" => "ERROR",
                "texto" => "El valor de " . $campo['formulario_nombre'] . " no puede ser usado en esa operación",
                "icono" => "error",
              ];
              return ($alerta);
            }
            return false;
          };
          if (is_array($campo['camposDiferentes'])) {
            foreach ($campo['camposDiferentes'] as $campoDiferente) {
              $resultado = $compararValores($campo['campo_valor'], $campoDiferente);
              if ($resultado) return $resultado;
            }
          } else {
            $resultado = $compararValores($campo['campo_valor'], $campo['camposDiferentes']);
            if ($resultado) return $resultado;
          }
        }
      }
    }
    return false;
  }
  public function limpiarValidar(mixed &$valor, array $esquema, ?array $config = []) {
    $direccion =  isset($config['direccion']) ? $config['direccion'] : $direccion = ['raiz'];
    $fAlerta = function (array $esquema, string $reglaIncumplida, array $dirFinal) {
      $titulo = '';
      $texto = '';
      switch ($reglaIncumplida) {
        case 'tipo':
          $titulo = 'Tipo de dato invalido';
          $texto = 'El tipo de dato enviado en el campo de ' . $esquema['nombreAlerta'] . ' no es válido';
          break;
        case 'requerido':
          $titulo = 'Campo obligatorio';
          $texto = 'El valor del campo de ' . $esquema['nombreAlerta'] . ' es obligatorio';
          break;
        case 'minL':
          $titulo = 'Valor muy corto';
          $texto = 'El valor introducido en el campo de ' . $esquema['nombreAlerta'] . ' debe tener al menos ' . $esquema['minL'] . ' carácteres de longitud';
          break;
        case 'maxL':
          $titulo = 'Valor muy largo';
          $texto = 'El valor introducido en el campo de ' . $esquema['nombreAlerta'] . ' debe tener máximo ' . $esquema['maxL'] . ' carácteres de longitud';
          break;
        case 'regex':
          $titulo = 'Formato inválido';
          $texto = 'El valor introducido en el campo de ' . $esquema['nombreAlerta'] . ' no coincide con el formato establecido';
          break;
        case 'debeSerUnicoBD':
          $titulo = 'Valor repetido';
          $texto = 'El valor introducido en el campo de ' . $esquema['nombreAlerta'] . ' ya se encuentra registrado en la base de datos, por favor elija otro';
          break;
        case 'debeExistirBD':
          $titulo = 'Valor inexistente';
          $texto = 'El valor introducido en el campo de ' . $esquema['nombreAlerta'] . ' no se encuentra registrado en la base de datos';
          break;
        case 'menorA':
          $titulo = 'Valor inferior al mínimo';
          $texto = 'El valor introducido en el campo de ' . $esquema['nombreAlerta'] . ' no alacanza el mínimo establecido';
          break;
        case 'mayorA':
          $titulo = 'Valor superior al máximo';
          $texto = 'El valor introducido en el campo de ' . $esquema['nombreAlerta'] . ' supera el máximo establecido';
          break;
        case 'direferenteA':
          $titulo = 'Valor inválido';
          $texto = 'El valor introducido en el campo de ' . $esquema['nombreAlerta'] . ' debe ser diferente';
          break;
        case 'igualA':
          $titulo = "Desigualdad de valores";
          $texto = "El valor de los campos de " . $esquema['nombreAlerta'] . " deben ser iguales, verifique e intente nuevamente";
          break;
        case 'sinTablaBD':
        case 'sinNormbreBD':
        case 'sinClaveUnicaBD':
        case 'sinValorUnicoBD':
          $titulo = "Error interno";
          $texto = "Ha ocurrido un error debido a la falta de una configuracion en las validaciones del campo " . $esquema['nombreAlerta'] . " que se viculan a la interacion con la Base de Datos: " . $reglaIncumplida;
          break;
        default:
          return [
            'tipo' => 'simple',
            'titulo' => 'Regla desconocida',
            'texto' => 'No se reconoce la regla de validacion',
            'icono' => 'error',
            'direccion' => $dirFinal
          ];
      }
      return [
        'tipo' => 'simple',
        'titulo' => $titulo,
        'texto' => $texto,
        'icono' => 'error',
        'direccion' => $dirFinal
      ];
    };

    // Tipo de dato
    switch ($esquema['tipo']) {
      case 'boolean':

        if (!is_bool($valor)) return $fAlerta($esquema, 'tipo', $direccion);
        break;
      case 'int':
        if (!is_int($valor)) return $fAlerta($esquema, 'tipo', $direccion);
        break;
      case 'float':
        if (!is_float($valor)) return $fAlerta($esquema, 'tipo', $direccion);
        break;
      case 'string':
        if (!is_string($valor)) return $fAlerta($esquema, 'tipo', $direccion);
        $valor = $this->limpiarCadena($valor);
        break;
      case 'array':
        if (!is_array($valor)) return $fAlerta($esquema, 'tipo', $direccion);
        foreach ($valor as $item) {
          $direccionNu = [...$direccion, 'array'];
          $r = $this->limpiarValidar($item, $esquema['items'], $direccionNu);
          if ($r) return $r;
        }
        break;
      case 'arrayA':
        if (!is_array($valor)) return $fAlerta($esquema, 'tipo', $direccion);
        $valorCampoUnico = isset($esquema['campoUnicoBD']) ? [$esquema['campoUnicoBD'], ($valor[$esquema['campoUnicoBD']] ?? false)] : false;
        foreach ($esquema['propiedades'] as $propiedad => $esquemaInd) {
          $direccionNu = [...$direccion, $propiedad];
          if (!isset($valor[$propiedad]) && in_array($propiedad, $esquema['requerido'])) {
            return $fAlerta($esquemaInd, 'requerido', $direccionNu);
          } elseif (isset($valor[$propiedad])) {
            $validacion = $this->limpiarValidar($valor[$propiedad], $esquemaInd, [
              'direccion' => $direccionNu,
              'valorCampoUnico' => $valorCampoUnico
            ]);
            if ($validacion) return $validacion;
          }
        }
        break;
      default:
        return $fAlerta([], 'none', $direccion);
    }

    // Formateo
    if (isset($esquema['cFloat']) && $valor != '') {
      $valor = str_replace('.', '', $valor);
      $valor = str_replace(',', '.', $valor);
      $valor = (float)$valor;
    }
    if (isset($esquema['cMayusculas']) && $valor != '') {
      $valor = strtoupper($valor);
    }
    if (isset($esquema['cMinuculas']) && $valor != '') {
      $valor = strtolower($valor);
    }

    // Minimo y máximo de caracteres
    if (isset($esquema['minL']) && $valor != "" && mb_strlen($valor) < $esquema['minL']) {
      return $fAlerta($esquema, 'minL', $direccion);
    }
    if (isset($esquema['maxL']) && $valor != "" && mb_strlen($valor) > $esquema['maxL']) {
      return $fAlerta($esquema, 'maxL', $direccion);
    }

    // Formato
    if (isset($esquema['regex']) && $valor != "" && !preg_match("/" . $esquema['regex'] . "/", $valor)) {
      return $fAlerta($esquema, 'regex', $direccion);
    }

    // Existencia y unicidad en la BD
    if (isset($esquema['debeExistirBD']) || isset($esquema['debeSerUnicoBD'])) {
      // Alertas mata tontos
      if (!isset($esquema['tablaBD'])) return $fAlerta($esquema, 'sinTablaBD', $direccion);
      if (!isset($esquema['nombreBD'])) return $fAlerta($esquema, 'sinNombreBD', $direccion);

      // Obtener la conexion si se requiere
      if ($this->objetoBD == null) $this->objetoBD = new conexion;
      if (isset($config['valorCampoUnico'])) {
        $claveUnicaBD = ($config['valorCampoUnico'][0] ?? '');
        $valorUnicoBD = ($config['valorCampoUnico'][1] ?? '');
      }

      /* Escenarios
        1- id normal que es autoincrement (registrar/actualizar); (los dos y como no se envia al registrar fino)
        2- id fijo que no es autoincrement (registrar/actualizar); (se le quita el de debeExistir en la actualizacion)
        3- campo unico fijo (telefono) (registrar/actualizar); (igual se le quita en el registrar para que pasen con ambos y vea que son el mismo id)
      */


      $resultado = [];
      if (!isset($this->cacheVal[$esquema['tablaBD']])) $this->cacheVal[$esquema['tablaBD']] = [];
      foreach ($this->cacheVal[$esquema['tablaBD']] as $registro) {
        if ($registro['resultado'][$esquema['nombreBD']] == $valor) {
          $resultado = $registro;
          break;
        }
      }
      $esAct = $valorUnicoBD != '' ? true : false;
      if ($resultado == []) {
        $r = $this->objetoBD->seleccionarDatos2([
          'campos' => '*',
          'tabla' =>  $esquema['tablaBD'],
          'BD' => ($esquema['BD'] ?? NULL),
          'WHERE' => [$esquema['nombreBD'] => $valor],
          'LIMIT' => '1'
        ]);
        if (!isset($this->cacheVal[$esquema['tablaBD']])) {
          $this->cacheVal[$esquema['tablaBD']] = [];
        }
        $resultado = $this->cacheVal[$esquema['tablaBD']][] = [
          'resultado' => $r->fetch(),
          'count' => $r->count(),
        ];
      }

      if (!$esAct) {
        if (isset($esquema['debeSerUnicoBD']) && !empty($resultado)) {
          return $fAlerta($esquema, 'debeSerUnicoBD', $direccion);
        }
        if (isset($esquema['debeExistirBD']) && empty($resultado)) {
          return $fAlerta($esquema, 'debeExistirBD', $direccion);
        }
      }

      //Registrar un dato en otra tabla
      if (isset($esquema['debeExistirBD']) &&  empty($resultado)) {
        return $fAlerta($esquema, 'debeExistirBD', $direccion);
      }
      //Registrar un dato nuevo 
      if (isset($esquema['debeSerUnicoBD'])) {
        return [$esAct, $resultado[$claveUnicaBD], $valorUnicoBD];
        if (($esAct && $resultado[$claveUnicaBD] != $valorUnicoBD && $resultado[$esquema['nombreBD']] == $valor)) {
          return $fAlerta($esquema, 'debeSerUnicoBD', $direccion);
        } else {
          if ($resultado[$esquema['nombreBD']] == $valor) {
            return $fAlerta($esquema, 'debeSerUnicoBD', $direccion);
          }
        }
        return 'h';
      }

      //Actualizar un dato existente que al ser actualizado no debe ser igual al de otros registros
      if (isset($esquema['debeExistirBD']) && isset($esquema['debeSerUnicoBD'])) {
        //Trampa para tontos
        if ($claveUnicaBD == '') return $fAlerta($esquema, 'sinClaveUnicaBD', $direccion);
        if ($valorUnicoBD == '') return $fAlerta($esquema, 'sinValorUnicoBD', $direccion);
        if ($esAct) {
          //Verficamos que la primary key sea distinta, tan solo asi se dira que es otro registro que ya tiene ese dato
          if ($resultado[$claveUnicaBD] != $valorUnicoBD) {
            if ($resultado[$esquema['nombreBD']] == $valor) {
              return $fAlerta($esquema, 'debeSerUnicoBD', $direccion);
            }
          }
        } else {
          if ($resultado[$esquema['nombreBD']] == $valor) {
            return $fAlerta($esquema, 'debeSerUnicoBD', $direccion);
          }
        }
      }
    }

    return [$resultado];

    // Comparacion
    if (isset($esquema['menorA']) && $valor < $esquema['menorA']) return $fAlerta($esquema, 'menorA', $direccion);
    if (isset($esquema['mayorA']) && $valor > $esquema['mayorA']) return $fAlerta($esquema, 'mayorA', $direccion);
    if (isset($esquema['igualA']) && $valor != $esquema['igualA']) return $fAlerta($esquema, 'igualA', $direccion);
    if (isset($esquema['diferenteA'])) {
      if (is_array($esquema['diferenteA'])) {
        foreach ($esquema['diferenteA'] as $diferente) {
          if ($valor == $diferente) {
            return $fAlerta($esquema, 'diferenteA', $direccion);
          }
        }
      } elseif ($valor == $esquema['diferenteA']) {
        return $fAlerta($esquema, 'diferenteA', $direccion);
      }
    }
    return false;
  }
  public function FechaHora_Sel(string $tipo, $fecha = null, $tiempo = null) {
    if ($tipo === 'Fecha_hora_foto') {
      $fecha = new DateTime('now', new DateTimeZone('America/Caracas')); // Especifica la zona horaria de Venezuela
      $fecha = $fecha->format('Y-m-d H:i:s');
      $fecha = str_replace(' ', '_', $fecha);
      $fecha = str_replace(':', '_', $fecha);
    } elseif ($tipo === 'fecha_hora_BD') {
      $fecha = new DateTime('now', new DateTimeZone('America/Caracas'));
      $fecha = $fecha->format('Y-m-d H:i:s');
    } elseif ($tipo == 'fecha_BD') {
      $fecha = str_replace('-', '/', $fecha);
      $fecha = str_replace(':', '/', $fecha);
      $fecha = DateTime::createFromFormat('d/m/Y', $fecha);
      $fecha = $fecha->format('Y-m-d');
    } elseif ($tipo == 'Fecha_Actual_BD') {
      $fecha = new DateTime('now', new DateTimeZone('America/Caracas'));
      $fecha = $fecha->format('Y-m-d');
    } elseif ($tipo == 'Fecha_Hora_Actual') {
      $fecha = new DateTime('now', new DateTimeZone('America/Caracas'));
      $fecha = $fecha->format('d-m-Y h:i A');
    } elseif ($tipo == 'fecha_hora_AM_PM') {
      $timestamp = strtotime($fecha);
      $fecha = date('d-m-Y h:i A', $timestamp);
    } elseif ($tipo == 'tiempo_antes_BD') {
      $fecha = new DateTime('now', new DateTimeZone('America/Caracas'));
      $intervalo = new DateInterval('P' . $tiempo . 'D');
      $fecha = $fecha->sub($intervalo);
      $fecha = $fecha->format('Y-m-d');
    } elseif ($tipo == 'Fecha_Normal') {
      $fecha = DateTime::createFromFormat('Y-m-d', $fecha);
      $fecha = $fecha->format('d-m-Y');
    }
    return $fecha;
  }
  public function redireccionarUsuario($sinRedireccion = null) {
    $objPermisos = new accesosModelo();
    $urlRedireccion = 'usuarios/login';
    $_SESSION['vistaActual'] = 'usuarios';
    if (!empty($_SESSION['cedula'])) {
      $permisosRedi = [
        'reportes' => 'ver reportes',
        'ventas' => 'ver',
        'pedidos' => 'ver',
      ];
      foreach ($permisosRedi as $modulo => $permiso) {
        if (!isset($objPermisos->validarPermisos($modulo, $permiso)['icono'])) {
          $urlRedireccion = $modulo . '/';
          $_SESSION['vistaActual'] = $modulo;
          break;
        }
      }
    }
    http_response_code(403);
    ob_end_clean();
    if ($sinRedireccion) return APP_URL . $urlRedireccion;
    header("Location: " . APP_URL . $urlRedireccion);
  }
  public function hacerPeticionesAPIs(array $instruccionesPe) {

    $url = $instruccionesPe['url'];
    $datosPe = $instruccionesPe['datosPe'] ?? '';
    $metodo = $instruccionesPe['metodo'] ?? 'POST';
    $enviarComoJSON = $instruccionesPe['enviarComoJSON'] ?? false;

    $peticion = curl_init($url);
    curl_setopt($peticion, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($peticion, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36');

    if ($metodo == 'POST' || $metodo == 'post') {
      if ($enviarComoJSON) {
        $datosPe = json_encode($datosPe);
        curl_setopt($peticion, CURLOPT_HTTPHEADER, [
          'Content-Type: application/json',
          'Content-Length: ' . strlen($datosPe)
        ]);
        curl_setopt($peticion, CURLOPT_POSTFIELDS, $datosPe);
      } else {
        curl_setopt($peticion, CURLOPT_POSTFIELDS, http_build_query($datosPe)); // Formatear datos para POST
      }
      curl_setopt($peticion, CURLOPT_POST, true);
    }
    $respuesta = curl_exec($peticion);
    $codigoEstado = curl_getinfo($peticion, CURLINFO_HTTP_CODE);

    if (preg_match('/2\d{2}/', $codigoEstado)) {
      if (is_string($respuesta)) {
        return json_decode($respuesta, true);
      } else {
        return $respuesta;
      }
    } elseif (preg_match('/4\d{2}/', $codigoEstado)) {
      return [
        'error' => 'El o los mensajes no han sido enviados debido a un error en la peticion, error: ' . $codigoEstado
      ];
    } elseif (preg_match('/5\d{2}/', $codigoEstado)) {
      return [
        'error' => 'Ocurrió un error en la petición de: ' . $codigoEstado
      ];
    } else {
      return [
        'error' => 'El envio de la peticion fallo y no se reconoce el codigo de error que envía: ' . $codigoEstado
      ];
    }
  }
  public function VEYSNEC(array $instrucciones) {
    //Verificar si Existe Y Si No Existe Crearlo
    if ($this->objetoBD == null) $this->objetoBD = new conexion;
    [
      'tabla' => $tabla,
      'WHERE' => $WHERE,
    ] = $instrucciones;
    $camposRetorno = $instrucciones['campos'];
    $RSEN = $instrucciones['RSEN'] ?? false;
    $eliminadosYVigentes = $instrucciones['eliminadosYVigentes'] ?? ($RSEN != false) ?? false;

    $camposArray = explode(',', $instrucciones['campos']);
    $camposArray = array_map(function ($campo) {
      return trim($campo);
    }, $camposArray);

    $resultado = $this->objetoBD->seleccionarDatos2([
      'campos' => '*',
      'tabla' => $tabla,
      'BD' => ($instrucciones['BD'] ?? NULL),
      'WHERE' => $WHERE ?? [],
      'eliminadosYVigentes' => $eliminadosYVigentes,
    ]);

    if ($resultado->rowCount() == 0) {
      $instruccionesRegistro = [
        'tabla' => $tabla,
        'BD' => ($instrucciones['BD'] ?? NULL),
        'datos' => []
      ];
      foreach ($WHERE as $clave => $valor) {
        $instruccionesRegistro['datos'][$clave] = $valor;
      };
      $ultimoId = $this->objetoBD->guardarDatos2($instruccionesRegistro);
      if (isset($ultimoId['error']) || $ultimoId == false || $ultimoId == 0) {
        $alerta = [
          "tipo" => "simple",
          "titulo" => "Registro automático de la tabla {$tabla} no creado",
          "texto" => "El Registro automático no ha podido ser creado",
          "icono" => "error",
        ];
        return $alerta;
      }
      $instruccionesBD = [
        'campos' => $camposRetorno,
        'tabla' => $tabla,
        'BD' => ($instrucciones['BD'] ?? NULL),
        'WHERE' => $WHERE ?? [],
        'eliminadosYVigentes' => $eliminadosYVigentes
      ];
      $resultado = $this->objetoBD->seleccionarDatos2($instruccionesBD);
      if (count($camposArray) == 1) {
        return $resultado->fetch(PDO::FETCH_COLUMN);
      } elseif (count($camposArray) > 1) {
        return $resultado->fetch();
      }
    } else {
      $info = $resultado->fetch();
      if ($RSEN == true) {
        $estado = $info['status'];
        if ($estado == 0) {
          $resultadoAct = $this->objetoBD->actualizarDatos2([
            'tabla' => $tabla,
            'BD' => ($instrucciones['BD'] ?? NULL),
            'datos' => [
              'status' => 1
            ],
            'WHERE' => $WHERE
          ]);
          if ($resultadoAct == false || $resultadoAct <= 0 || isset($resultadoAct['error'])) {
            return $resultadoAct;
          } else {
            $info['status'] = 1;
          }
        }
      }
      if (count($camposArray) == 1) {
        return $info[$camposArray[0]];
      } elseif (count($camposArray) > 1) {
        $resultado = array_intersect_key($info, array_flip($camposArray));
        return $resultado;
      }
    }
  }
  public function DECORE(PDOStatement|FPDF|array|string $respuesta) {

    if ($respuesta instanceof FPDF) {
      $_SESSION['codigoRequest'] = 200;
      $respuesta->Output('I', 'Reporte');
      return;
    }
    if (!$respuesta instanceof FPDF && !is_array($respuesta)) {
      $_SESSION['codigoRequest'] = 400;
      http_response_code(400);
      echo json_encode($respuesta);
      return;
    }

    if (($respuesta['icono'] ?? '') == 'error') {
      $codigoEstado = 400;
    } else {
      $codigoEstado = $respuesta['codigoRequest'] ?? 200;
    }

    $_SESSION['codigoRequest'] = $codigoEstado;
    http_response_code($codigoEstado);
    echo json_encode($respuesta);
  }
  public function Imagenes_Reg(string $subCarpeta, array $imagen, string $tablaBD) {
    $procesarImagenes = function ($subCarpeta, $imagen, $tablaBD) {
      if ($tablaBD == "") {
        return [
          "tipo" => "simple",
          "titulo" => "Ocurrió un error inesperado",
          "texto" => "No puedes registrar una imagen con campos vacíos",
          "icono" => "error",
        ];
      }
      if ($imagen['name'] == "" || $imagen['size'] <= 0) {
        return "";
      }
      if (!file_exists(DIR_FOTOS . $subCarpeta . '/')) {
        if (!mkdir(DIR_FOTOS . $subCarpeta . '/', 0777)) {
          $alerta = [
            "tipo" => "simple",
            "titulo" => "Directorio no creado",
            "texto" => "El directorio para la foto no se pudo crear",
            "icono" => "error",
          ];
          return $alerta;
        }
      }
      if (
        mime_content_type($imagen['tmp_name']) != "image/jpeg" &&
        mime_content_type($imagen['tmp_name']) != "image/png"
      ) {
        $alerta = [
          "tipo" => "simple",
          "titulo" => "Formato inválido",
          "texto" => "El formato del archivo seleccionado es incorrecto",
          "icono" => "error",
        ];
        return $alerta;
      }
      if (
        ($imagen['size']) / 1048576 > 5
      ) {
        $alerta = [
          "tipo" => "simple",
          "titulo" => "Archivo muy pesado",
          "texto" => "El tamaño del archivo excede los 5MB permitidos por el sistema",
          "icono" => "error",
        ];
        return $alerta;
      }

      $nombreFoto = $tablaBD . "_" . $this->FechaHora_Sel("Fecha_hora_foto") . '_' . rand(1, 100);
      $nombreFoto = str_ireplace(" ", "_", $nombreFoto);
      $nombreFoto = str_ireplace("-", "_", $nombreFoto);

      switch (mime_content_type($imagen['tmp_name'])) {
        case "image/jpeg":
          $nombreFoto =  $nombreFoto . ".jpg";
          break;
        case "image/png":
          $nombreFoto =  $nombreFoto . ".png";
          break;
      }

      chmod(DIR_FOTOS . $subCarpeta . '/', 0777);
      if (!move_uploaded_file($imagen['tmp_name'], DIR_FOTOS . $subCarpeta . '/' . $nombreFoto)) {
        return [
          "tipo" => "simple",
          "titulo" => "Archivo no movido",
          "texto" => "El archivo no pude ser movido a la carpeta destino",
          "icono" => "error",
        ];
      }
      return $nombreFoto;
    };
    $nombresFotos = [];
    if (!isset($imagen['name'])) {
      return [];
    } elseif (is_array($imagen['name'])) {
      for ($i = 0; $i < count($imagen['name']); $i++) {
        $archivoIndividual = [
          'full_path' => $imagen['full_path'][$i],
          'name' => $imagen['name'][$i],
          'size' => $imagen['size'][$i],
          'tmp_name' => $imagen['tmp_name'][$i],
          'type' => $imagen['type'][$i],
        ];
        $nombresFotos[] = $procesarImagenes($subCarpeta, $archivoIndividual, $tablaBD);
      }
    } else {
      $nombresFotos = $procesarImagenes($subCarpeta, $imagen, $tablaBD);
    }
    return $nombresFotos;
  }
  public function Imagenes_Act(array $instrucciones) {
    if ($this->objetoBD == null) $this->objetoBD = new conexion;
    $subCarpeta = $instrucciones['subCarpeta'];
    $imagen = $instrucciones['imagen'];
    $tablaBD = $instrucciones['tablaBD'];
    $nombreCampofoto = $instrucciones['nombreCampoFoto'];
    $nombreCampoId = $instrucciones['nombreCampoId'];
    $valorId = $instrucciones['valorId'];
    $BD = $instrucciones['BD'] ?? NULL;

    /*Verificar Campos obligatorios*/
    if (
      $subCarpeta == "" || $nombreCampoId == "" || $valorId == "" || $nombreCampofoto == "" ||  $tablaBD == ""
    ) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Ocurrió un error inesperado",
        "texto" => "No puedes actualizar una imagen con campos vacíos",
        "icono" => "error",
      ];
      return $alerta;
      exit();
    }
    $resultado = $this->objetoBD->seleccionarDatos2([
      'campos' => '*',
      'tabla' => $tablaBD,
      'BD' => $BD,
      'WHERE' => [
        $nombreCampoId => $valorId,
      ]
    ]);

    if ($resultado->rowCount() <= 0) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Imagen no encontrada",
        "texto" => "La imagen que ha intentado actualizar no se encuentra en la base de datos",
        "icono" => "error"
      ];
      return $alerta;
      exit();
    } else {
      $resultado = $resultado->fetch();
    }

    /*Función para comprobar si se seleccionó una imagen*/
    if (
      $imagen['name'] == "" &&
      $imagen['size'] <= 0
    ) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Foto no válida",
        "texto" => "No puede enviar el campo vació o archivos diferentes al formato solicitado",
        "icono" => "error"
      ];
      return $alerta;
      exit();
    }

    /*Función para crear el directorio de las imágenes si este no existe */
    if (!file_exists(DIR_FOTOS . $subCarpeta . '/')) { /*Comprueba si el directorio no existe */
      if (!mkdir(DIR_FOTOS . $subCarpeta . '/', 0777)) { /*Crea el archivo y si no puede, manda una alerta */
        $alerta = [
          "tipo" => "simple",
          "titulo" => "Directorio no creado",
          "texto" => "El directorio para la foto no se pudo crear",
          "icono" => "error",
        ];
        return $alerta;
        exit();
      }
    }

    if (/*Función para verificar el formato de las imágenes*/
      mime_content_type($imagen['tmp_name']) != "image/jpeg" &&
      mime_content_type($imagen['tmp_name']) != "image/png"
    ) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Formato inválido",
        "texto" => "El formato del archivo seleccionado es incorrecto",
        "icono" => "error",
      ];
      return $alerta;
      exit();
    }

    /*Función para verificar que la imagen no sobrepase los 5 MB*/
    if (($imagen['size']) / 1048576 > 5) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Imagen muy pesada",
        "texto" => "El tamaño de la imagen excede los 5MB permitidos por el sistema",
        "icono" => "error",
      ];
      return $alerta;
    }

    /*Creación del nombre de la foto*/
    if ($resultado[$nombreCampofoto] != "") {
      $nombreFoto = explode(".", $resultado[$nombreCampofoto]);
      $nombreFoto = $nombreFoto[0];
    } else {
      $nombreFoto = str_ireplace(" ", "_", $tablaBD);
      $nombreFoto .= "_" . $this->FechaHora_Sel("Fecha_hora_foto"); /*para cambiar el sufijo de la foto por si algún usuario repite el nombre */
    }

    /*Asignación del tipo de archivo */
    switch (mime_content_type($imagen['tmp_name'])) {
      case "image/jpeg":
        $nombreFoto .= ".jpg";
        break;
      case "image/png":
        $nombreFoto .= ".png";
        break;
    }

    /*Permisos de lectura y escritura a la carpeta de imagenes */
    chmod(DIR_FOTOS . $subCarpeta . '/', 0777);

    /*Mover el archivo */
    if (!move_uploaded_file($imagen['tmp_name'], DIR_FOTOS . $subCarpeta . '/' . $nombreFoto)) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Foto no movida",
        "texto" => "La imagen no puede ser movida a la carpeta destino",
        "icono" => "error",
      ];
      return $alerta;
      exit();
    }

    /*Eliminar la imagen anterior*/
    $nombreFotoExis = explode('?', $resultado[$nombreCampofoto]);
    if (is_file(DIR_FOTOS . $subCarpeta . '/' . $nombreFotoExis[0]) && $nombreFotoExis[0] != $nombreFoto) {
      chmod(DIR_FOTOS . $subCarpeta . '/' . $nombreFotoExis[0], 0777); /*damos permiso de lectura y escritura */
      unlink(DIR_FOTOS . $subCarpeta . '/' . $nombreFotoExis[0]); /*eliminamos la foto */
    }

    //Asignamos el numero de version a la foto para forzar que se actualice en el HTML
    $nombreFoto .= "?v=" . $this->FechaHora_Sel("Fecha_hora_foto");
    $resultado = $this->objetoBD->actualizarDatos2([
      "tabla" => $tablaBD,
      'BD' => $BD,
      "datos" => [
        $nombreCampofoto => $nombreFoto
      ],
      "WHERE" => [
        $nombreCampoId => $valorId,
      ]
    ]);
    if ($resultado == false || $resultado <= 0) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Imagen no actualizada",
        "texto" => "No hemos podido actualizar algunos datos del usuario",
        "icono" => "warning",
      ];
    } else {
      /*Para cambiar en tiempo real la foto del usuario que ha iniciado sesión*/
      if (
        $tablaBD == 'usuarios' &&
        $nombreCampofoto == 'foto_usuario' &&
        $valorId == $_SESSION['cedula']
      ) {
        $_SESSION['foto'] = $nombreFoto;
      }
      $rutaImagen = APP_URL . DIR_FOTOS . $subCarpeta . '/' . $nombreFoto;
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Imagen actualizada",
        "texto" => "La imagen ha sido actualizada exitosamente",
        "icono" => "success",
        // "claseImagen" => 'imagenRegistro',
        // "idRegistro" => $valorId,
        // "nuevaRutaImagen" => $rutaImagen
      ];
      $this->objetoBD->commit();
    }
    return $alerta;
  }
  public function Imagenes_Eli(array $instrucciones) {
    if ($this->objetoBD == null) $this->objetoBD = new conexion;
    $subCarpeta = $instrucciones['subCarpeta'];
    $tablaBD = $instrucciones['tablaBD'];
    $nombreCampofoto = $instrucciones['nombreCampoFoto'];
    $nombreCampoId = $instrucciones['nombreCampoId'];
    $valorId = $instrucciones['valorId'];
    $BD = $instrucciones['BD'] ?? NULL;

    $valorId = $this->limpiarCadena($valorId);
    $resultado = $this->objetoBD->seleccionarDatos2([
      'campos' => $nombreCampofoto,
      'tabla' => $tablaBD,
      'BD' => $BD,
      'WHERE' => [
        $nombreCampoId => $valorId,
      ]
    ]);

    if ($resultado->rowCount() <= 0) {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Registro no encontrado",
        "texto" => "El registro que ha intentado eliminar no se encuentra en la base de datos",
        "icono" => "error"
      ];
      return $alerta;
    } else {
      $resultado = $resultado->fetch();
    }

    chmod(DIR_FOTOS . $subCarpeta . '/', 0777);/*damos permiso de lectura y escritura */
    $nombreFoto = $resultado[$nombreCampofoto];
    $nombreFoto = explode("?", $nombreFoto);
    $nombreFoto = $nombreFoto[0];

    if (is_file(DIR_FOTOS . $subCarpeta . '/' . $nombreFoto)) {
      chmod(DIR_FOTOS . $subCarpeta . '/' . $nombreFoto, 0777);
      if (!unlink(DIR_FOTOS . $subCarpeta . '/' . $nombreFoto)) {
        $alerta = [
          "tipo" => "simple",
          "titulo" => "Imagen no eliminada",
          "texto" => "No se ha podido eliminar la imagen",
          "icono" => "error"
        ];
        return $alerta;
        exit();
      }
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Imagen no encontrada",
        "texto" => "La imagen que ha intentado eliminar no se encuentra en la base de datos",
        "icono" => "error"
      ];
      return $alerta;
      exit();
    }

    //Actualizamos el valor en la BD
    $resultado = $this->objetoBD->actualizarDatos2([
      "tabla" => $tablaBD,
      'BD' => $BD,
      "datos" => [
        $nombreCampofoto => ""
      ],
      "WHERE" => [
        $nombreCampoId => $valorId,
      ]
    ]);

    if ($resultado == false || $resultado <= 0) {
      $alerta = [
        "tipo" => "limpiar",
        "titulo" => "Imagen no eliminada",
        "texto" => "No hemos podido actualizar algunos datos, sin embargo la imagen se eliminó con éxito",
        "icono" => "warning",
      ];
    } else {
      if (
        $valorId == $_SESSION['cedula'] &&
        $nombreCampofoto == 'foto_usuario' &&
        $tablaBD == 'usuarios'
      ) {
        $_SESSION['foto'] = "";
      }

      if ($tablaBD == 'usuarios') {
        $rutaImagen = APP_URL . DIR_FOTOS . "default.png";
      } else {
        $rutaImagen = APP_URL . DIR_FOTOS . "default2.png";
      }

      $alerta = [
        "tipo" => "simple",
        "titulo" => "Imagen eliminada",
        "texto" => "La imagen ha sido eliminada exitosamente",
        "icono" => "success",
        // "claseImagen" => 'imagenRegistro',
        // "idRegistro" => $valorId,
        // "nuevaRutaImagen" => $rutaImagen,
        // "reiniciarPreview" => true
      ];
      $this->objetoBD->commit();
    }
    return $alerta;
  }
  public function Imagenes_Eli2(string $subCarpeta, string|array $foto) {
    $eliminarFoto = function ($subCarpeta, $foto) {
      $foto = explode('?', $foto);
      $foto = $foto[0];
      if (is_file(DIR_FOTOS . $subCarpeta . '/' . $foto)) { /*verificamos que se creó */
        chmod(DIR_FOTOS . $subCarpeta . '/' . $foto, 0777); /*le damos permiso a la carpeta para eliminar */
        unlink(DIR_FOTOS . $subCarpeta . '/' . $foto); /*borramos el archivos */
        return false;
      } else {
        $alerta = [
          "tipo" => "simple",
          "titulo" => "Imagen no eliminada",
          "texto" => "La imagen ha sido eliminada con éxito",
          "icono" => "error"
        ];
        return $alerta;
      }
    };
    if (is_array($foto)) {
      foreach ($foto as $fotoI) {
        $eliminarFoto($subCarpeta, $fotoI);
      }
    } else {
      $eliminarFoto($subCarpeta, $foto);
    }
  }
  public function generarCodSeg(array $instrucciones) {
    if ($this->objetoBD == null) $this->objetoBD = new conexion;
    [
      'tablaBD' => $tablaBD,
      'prefijo' => $prefijo,
      'campoID' => $campoID
    ] = $instrucciones;
    $BD = $instrucciones['BD'] ?? NULL;

    $resultado = $this->objetoBD->seleccionarDatos2([
      'eliminadosYVigentes' => true,
      'campos' => $campoID,
      'tabla' => $tablaBD,
      'BD' => $BD,
      'ORDER' => $campoID . ' DESC',
      'LIMIT' => 1,
      'FOR_UPDATE' => $tablaBD
    ]);

    $codigo = 1;
    $datetime = date("yz");

    //Para que se reinicie diario
    if ($resultado->rowCount() > 0) {
      $pedazos = explode('-', $resultado->fetch(PDO::FETCH_COLUMN));
      if (isset($pedazos[1])) {
        if ($datetime == $pedazos[1]) {
          $codigo = ((int)$pedazos[2]) + 1;
        }
      }
    }
    $nroRandom = str_pad(rand(0, 99), 2, "0", STR_PAD_LEFT);
    $idRellenado = str_pad($codigo, 5, "0", STR_PAD_LEFT);
    return $prefijo . '-' . $datetime . '-' . $idRellenado . '-' . $nroRandom;
  }
  public function crearCodigoQR(string $texto, float $tamaño) {
    //Logo
    $rutaLogo = APP_URL . 'app/assets/img/logo-the-vina.png';
    $tamañoLogo = (int)($tamaño / 3);
    $logo = new Logo(
      $rutaLogo,
      $tamañoLogo,
      $tamañoLogo,
    );

    $qrCode = new QrCode(
      $texto,
      new Encoding('UTF-8'),
      ErrorCorrectionLevel::High,
      $tamaño,
      5
    );
    $writer = new PngWriter();
    $tempFileName = tempnam(sys_get_temp_dir(), 'qr_') . '.png';
    $writer->write($qrCode)->saveToFile($tempFileName);
    return $tempFileName;
  }
  public function indexarArrays(array $instrucciones) {
    [
      'indice' => $indice,
      'array' => $array,
    ] = $instrucciones;
    $camposAgrupar = $instrucciones['camposAgrupar'] ?? false;
    $camposSumar = $instrucciones['camposSumar'] ?? false;
    $crearLLave = function ($array, $indices) {
      $llave = [];
      if (is_array($indices)) {
        foreach ($indices as $indice) {
          $indice = trim($indice);
          if (isset($array[$indice])) {
            $llave[$indice] = $array[$indice];
          }
        }
        $llave = json_encode($llave);
      } else {
        $indices = trim($indices);
        $llave = $array[$indices];
      }
      return $llave;
    };
    $aux = [];
    $llave = '';
    foreach ($array as $i) {
      $llave = $crearLLave($i, $indice);
      if (!isset($aux[$llave])) {
        //Agregamos los campos iniciales
        $aux[$llave] = [];
        if ($camposAgrupar) {
          if (is_array($camposAgrupar)) {
            foreach ($camposAgrupar as $campo) {
              $campo = trim($campo);
              if (isset($i[$campo])) $aux[$llave][$campo] = $i[$campo];
            }
          } else {
            $campo = trim($camposAgrupar);
            $aux[$llave] = $i[$camposAgrupar];
          }
        } else {
          $aux[$llave] = 0;
        }
        if (isset($camposSumar) && $camposSumar != '' && $camposSumar != false) {
          if (is_array($camposSumar)) {
            foreach ($camposSumar as $campoS) {
              $campoS = trim($campoS);
              if (!is_array(($aux[$llave]))) {
                $aux[$llave] = (float)$i[$campoS];
              } else {
                $aux[$llave][$campoS] = (float)$i[$campoS];
              }
            }
          } else {
            $camposSumar = trim($camposSumar);
            if (!is_array(($aux[$llave]))) {
              $aux[$llave] = (float)$i[$camposSumar];
            } else {
              $aux[$llave][$camposSumar] = (float)$i[$camposSumar];
            }
          }
        }
      } else {
        if (isset($camposSumar) && $camposSumar != '' && $camposSumar != false) {
          if (is_array($camposSumar)) {
            foreach ($camposSumar as $campoS) {
              $campoS = trim($campoS);
              $aux[$llave][$campoS] += (float) $i[$campoS];
            }
          } else {
            $camposSumar = trim($camposSumar);
            if (is_array($aux[$llave])) {
              $aux[$llave][$camposSumar] += (float)$i[$camposSumar];
            } else {
              $aux[$llave] += (float)$i[$camposSumar];
            }
          }
        }
      }
    }

    if (is_array($indice) || ($instrucciones['indicesNumericos'] ?? false) == true) {
      $aux = array_values($aux);
    }

    return $aux;
  }
  public function calcularKmEntreCoordenadas(array $coordenadas) {
    [
      'lat1' => $lat1,
      'lon1' => $lon1,
      'lat2' => $lat2,
      'lon2' => $lon2,
    ] = $coordenadas;
    $radioTierra = 6371; // Radio de la Tierra en kilómetros

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat / 2) * sin($dLat / 2) +
      cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
      sin($dLon / 2) * sin($dLon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $distancia = $radioTierra * $c;

    return round($distancia, 2); // Retorna la distancia en km

  }
  public function calcularKmPorCarretera(array $coordenadas) {
    [
      'partida' => $partida,
      'llegada' => $llegada,
    ] = $coordenadas;

    $latLngUrl2 = "lat=" . $llegada['latitud'] . "&lon=" . $llegada['longitud'];
    $url2 = "https://nominatim.openstreetmap.org/reverse?format=jsonv2&" . $latLngUrl2;

    $partida = $partida['latitud'] . ',' . $partida['longitud'];
    $llegada = $llegada['latitud'] . ',' . $llegada['longitud'];
    $apiKey = "plFhQVWfX5abG1DPt7jja56Syrqh7rY2";
    $url = "https://api.tomtom.com/routing/1/calculateRoute/{$partida}:{$llegada}/json?key={$apiKey}&travelMode=car";

    $objCache = new cacheModelo();
    $cache = $objCache->getItem('rutasPorCarretera');
    if (!$cache) $cache = [];
    if (isset($cache[$partida . '-' . $llegada])) return $cache[$partida . '-' . $llegada];

    $kmR = $this->hacerPeticionesAPIs([
      'url' => $url,
      'metodo' => 'GET',
    ]);
    $sitio = $this->hacerPeticionesAPIs([
      'url' => $url2,
      'metodo' => 'GET',
    ]);

    $km = 0;
    if (isset($kmR['routes'][0]['summary']['lengthInMeters'])) {
      $km = ceil($kmR['routes'][0]['summary']['lengthInMeters'] / 1000);
    } else {
      return [
        'tipo' => 'simple',
        'titulo' => 'Sin ruta',
        'texto' => 'No se pudo encontrar una ruta transitable por carretera',
        'icono' => 'warning',
      ];
    }
    if (isset($sitio['display_name'])) {
      $sitio = $sitio['display_name'];
    } else {
      $sitio = false;
    }
    $info = [
      'km_recorrido' => $km,
      'nombre_direccion' => $sitio
    ];
    $cache[$partida . '-' . $llegada] = $info;
    $objCache->setItem('rutasPorCarretera', $cache);
    return $info;
  }
  public function intersArray(array $array, array $llavesDeseadas) {
    return array_intersect_key($array, array_flip($llavesDeseadas));
  }
  public function alertaPHPUnit(string $modulo, string $accion) {
    $modulo = strtoupper($modulo);
    $accionN = strtoupper($accion);
    $encabezado = "MODULO: $modulo | ACCION: $accionN: ";
    return $encabezado . "Error: ";
  }
  public function DOAD(array $instrucciones) {
    if ($this->objetoBD == null) $this->objetoBD = new conexion;
    // Disernir Operaciones en Actualizaciones de Detalles
    [
      'arrayNuevo' => $arrayNuevo,
      'configArrayViejo' => $configArrayViejo,
      'campoUnicoDif' => $campoUnicoDif,
    ] = $instrucciones;
    $camposArray = explode(',', $configArrayViejo['campos']);
    $arrayViejo = $this->objetoBD->seleccionarDatos2($configArrayViejo)->fetchAll();

    $op = [
      'actualizar' => [],
      'registrar' => [],
      'eliminar' => [],
    ];

    if (empty($arrayNuevo) && empty($arrayViejo)) return $op;
    if (empty($arrayNuevo) && !empty($arrayViejo)) {
      foreach ($arrayViejo as $a) {
        $op['eliminar'][] = $a;
      }
      return $op;
    };
    if (!empty($arrayNuevo) && empty($arrayViejo)) {
      foreach ($arrayNuevo as $a) {
        $op['registrar'][] = $a;
      }
      return $op;
    };

    $auxViejo = $this->indexarArrays([
      'indice' => $campoUnicoDif,
      'array' => $arrayViejo,
      'camposAgrupar' => $camposArray
    ]);
    $auxNuevo = $this->indexarArrays([
      'indice' => $campoUnicoDif,
      'array' => $arrayNuevo,
      'camposAgrupar' => $camposArray
    ]);

    foreach ($auxNuevo as $clave => $valor) {
      if (!isset($auxViejo[$clave])) {
        $op['registrar'][] = $valor;
        continue;
      }
      if ($auxViejo[$clave] != $valor) {
        $op['actualizar'][] = $valor;
      }
    }
    foreach ($auxViejo as $clave => $valor) {
      if (!isset($auxNuevo[$clave])) {
        $op['eliminar'][] = $valor;
        continue;
      }
    }
    return $op;
  }
}
