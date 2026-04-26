<?php

namespace src\modelos;

use PDO;
use PDOException;
use DateTime;
use DateTimeZone;
use DateInterval;
use Exception;
use Throwable;
use FPDF;
use src\modelos\permisosModelo;

class errorBD extends Exception
{
  protected $detalles;
  public function __construct($mensaje, $detalles, $codigo, Throwable $anterior)
  {
    parent::__construct($mensaje, $codigo, $anterior);
    $this->detalles = $detalles;
  }
  public function getDetalles()
  {
    return $this->detalles;
  }
}
trait traitModelo
{
  private $servidorDB = DB_SERVER;
  private $nombreDB = DB_NAME;
  private $userDB = DB_USER;
  private $passwordDB = DB_PASS;
  protected static $conexion = null;
  protected static $conexion2 = null;
  private $BDactual;

  // #region [PUBLIC]
  public function limpiarCadena($cadena, $modo = 'antiSQLInyection')
  {
    if ($modo == 'antiSQLInyection') {
      $palabras = ["<script>", "</script>", "<script src", "<script type=", "SELECT * FROM", "SELECT ", " SELECT ", "DELETE FROM", "INSERT INTO", "DROP TABLE", "DROP DATABASE", "TRUNCATE TABLE", "SHOW TABLES", "SHOW DATABASES", "<?php", "?>", "--", "^", "<", ">", "==", "=", ";", "::"];
    } elseif ('antiFuncionesSQL') {
      $palabras = [')', 'DATE(', 'CAST(', 'CONVERT(', 'AVG(', 'SUM(', 'COUNT(', 'MAX(', 'MIN(', 'TRIM(', 'LOWER(', 'UPPER(', 'COALESCE('];
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
  public function limpiar_Verificar($campos)
  {
    foreach ($campos as &$campo) {

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
          exit();
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
            exit();
          } elseif (mb_strlen($campo['campo_valor']) < $campo['minimo']) {
            $alerta = [
              "tipo" => "simple",
              "titulo" => "Campo de " . $campo['formulario_nombre'] . " muy corto",
              "texto" => "El campo de" . $campo['formulario_nombre'] . " no puede tener menos de " . $campo['minimo'] . " carácteres de longitud: " . $campo['campo_valor'],
              "icono" => "error",
            ];
            return $alerta;
            exit();
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
        $registrosExistentes = $this->seleccionarDatos2([
          'campos' => '*',
          'tabla' =>  $campo['tabla'],
          'BD' => ($campo['BD'] ?? NULL),
          'WHERE' => [
            $campo['campo_nombre'] => $campo['campo_valor']
          ]
        ]);

        if ($registrosExistentes->rowCount() == 0 && isset($campo['requerido'])) {
          $alerta = [
            "tipo" => "simple",
            "titulo" => "Dato no encontrado",
            "texto" => "El valor que ha introducido en el campo de " . $campo['formulario_nombre'] . " no se encuentra registrado dentro de la base de datos del sistema, por favor verifique e intente de nuevo: " . $campo['campo_valor'],
            "icono" => "error",
          ];
          return ($alerta);
          exit();
        } else {
          $registrosExis[$campo['tabla']] = $registrosExistentes->fetch(PDO::FETCH_ASSOC);/*hacemos el arrays */
        }
      }

      //Para verificar que no haya mas registros con ese valor
      if (isset($campo['debeSerUnico'])) {

        $buscarEnLaBD = false;
        if (isset($registrosExis[$campo['tabla']])) { //es actualizar
          //Verificar si ya la info fue o no obtenida de la BD
          if (!isset($registrosExis[$campo['tabla']][$campo['campo_nombre']])) {
            $resultado = $this->seleccionarDatos2([
              'campos' => '*',
              'tabla' =>  $campo['tabla'],
              'BD' => ($campo['BD'] ?? NULL),
              'WHERE' => [
                $campo['campo_nombre'] => $campo['campo_valor']
              ]
            ]);
            $resultado = $resultado->fetch(PDO::FETCH_ASSOC);
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
          $checkRegistro = $this->seleccionarDatos2([
            'campos' => $campo['campo_nombre'],
            'tabla' =>  $campo['tabla'],
            'BD' => ($campo['BD'] ?? NULL),
            'WHERE' => [
              $campo['campo_nombre'] => $campo['campo_valor']
            ]
          ]);
          if ($checkRegistro->rowCount() > 0) {
            $alerta = [
              "tipo" => "simple",
              "titulo" => "Valor de " . $campo['formulario_nombre'] . " duplicado",
              "texto" => "El valor que ha introducido en el campo de " . $campo['formulario_nombre'] . " ya se encuentra registrado y no se puede duplicar, por favor verifique e intente de nuevo",
              "icono" => "error",
            ];
            return ($alerta);
            exit();
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
          exit();
        }
      }

      //para evitar que un dato específico sea eliminado o alguna otra operación
      if (isset($campo['camposDiferentes'])) {
        if ($campo['campo_valor'] == $campo['camposDiferentes']) {
          $alerta = [
            "tipo" => "simple",
            "titulo" => "ERROR",
            "texto" => "El valor de " . $campo['formulario_nombre'] . " no puede ser usado en esa transacción",
            "icono" => "error",
          ];
          return ($alerta);
          exit();
        }
      }
    }
    return false;
  }
  public function FechaHora_Sel($tipo, $fecha = null, $tiempo = null)
  {
    if ($tipo === 'Fecha_hora_foto') {
      $this->fecha = new DateTime('now', new DateTimeZone('America/Caracas')); // Especifica la zona horaria de Venezuela
      $this->fecha = $this->fecha->format('Y-m-d H:i:s');
      $this->fecha = str_replace(' ', '_', $this->fecha);
      $this->fecha = str_replace(':', '_', $this->fecha);
    } elseif ($tipo === 'fecha_hora_BD') {

      $this->fecha = new DateTime('now', new DateTimeZone('America/Caracas'));
      $this->fecha = $this->fecha->format('Y-m-d H:i:s');
    } elseif ($tipo == 'fecha_BD') {
      $this->fecha = str_replace('-', '/', $this->fecha);
      $this->fecha = str_replace(':', '/', $this->fecha);
      $this->fecha = DateTime::createFromFormat('d-m-Y', $fecha);
      $this->fecha = $this->fecha->format('Y-m-d');
    } elseif ($tipo == 'Fecha_Actual_BD') {
      $this->fecha = new DateTime('now', new DateTimeZone('America/Caracas'));
      $this->fecha = $this->fecha->format('Y-m-d');
    } elseif ($tipo == 'Fecha_Hora_Actual') {
      $this->fecha = new DateTime('now', new DateTimeZone('America/Caracas'));
      $this->fecha = $this->fecha->format('d-m-Y h:i A');
    } elseif ($tipo == 'fecha_hora_AM_PM') {
      $timestamp = strtotime($fecha);
      $this->fecha = date('d-m-Y h:i A', $timestamp);
    } elseif ($tipo == 'tiempo_antes_BD') {
      $this->fecha = new DateTime('now', new DateTimeZone('America/Caracas'));
      $intervalo = new DateInterval('P' . $tiempo . 'D');
      $this->fecha = $this->fecha->sub($intervalo);
      $this->fecha = $this->fecha->format('Y-m-d');
    } elseif ($tipo == 'Fecha_Normal') {
      $this->fecha = DateTime::createFromFormat('Y-m-d', $fecha);
      $this->fecha = $this->fecha->format('d-m-Y');
    }
    return $this->fecha;
  }
  public function redireccionarUsuario($sinRedireccion = null)
  {
    $objPermisos = new permisosModelo();
    $urlRedireccion = '';

    if (!empty($_SESSION['cedula'])) {
      $validacion = true;
      if (
        $validacion &&
        !isset($objPermisos->validarPermisos('dashboard', 'ver dashboard')['icono'])
      ) {
        $validacion = false;
        $urlRedireccion = 'dashboard';
        $_SESSION['vistaActual'] = 'dashboard';
      };
      if (
        $validacion &&
        !isset($objPermisos->validarPermisos('ventas', 'ver')['icono'])
      ) {
        $validacion = false;
        $urlRedireccion = 'ventas/';
        $_SESSION['vistaActual'] = 'ventas';
      }
      if (
        $validacion &&
        !isset($objPermisos->validarPermisos('pedidos', 'ver')['icono'])
      ) {
        $validacion = false;
        $urlRedireccion = 'pedidos/';
        $_SESSION['vistaActual'] = 'pedidos';
      }
      if ($validacion) {
        $validacion = false;
        $urlRedireccion = 'usuarios/login';
        $_SESSION['vistaActual'] = 'usuarios';
      }
    } else {
      $urlRedireccion = 'usuarios/login';
      $_SESSION['vistaActual'] = 'usuarios';
    }
    http_response_code(403);
    ob_end_clean();
    if ($sinRedireccion) return APP_URL . $urlRedireccion;
    header("Location: " . APP_URL . $urlRedireccion);
  }
  public function __destruct()
  {
    if (self::$conexion instanceof PDO && !self::$conexion->inTransaction()) {
      self::$conexion = null;
    }
    if (self::$conexion2 instanceof PDO && !self::$conexion2->inTransaction()) {
      self::$conexion2 = null;
    }
  }
  public function hacerPeticionesAPIs(array $instruccionesPe)
  {
    $url = $instruccionesPe['url'];
    $datosPe = $instruccionesPe['datosPe'] ?? '';
    $metodo = $instruccionesPe['metodo'] ?? 'POST';
    $enviarComoJSON = $instruccionesPe['enviarComoJSON'] ?? false;
    $peticion = curl_init($url);
    curl_setopt($peticion, CURLOPT_RETURNTRANSFER, true);
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

    // 4. Obtener el código de estado HTTP
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
  public function seleccionarDatos2($instrucciones)
  {
    return $this->seleccionarDatosP2($instrucciones);
  }
  public function guardarDatos2($instrucciones)
  {
    return $this->guardarDatosP2($instrucciones);
  }
  public function actualizarDatos2($instrucciones)
  {
    return $this->actualizarDatosP2($instrucciones);
  }
  public function eliminarDatos2($instrucciones)
  {
    return $this->eliminarDatosP2($instrucciones);
  }
  public function VEYSNEC($instrucciones)
  {
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

    $instruccionesBD = [
      'campos' => '*',
      'tabla' => $tabla,
      'BD' => ($instrucciones['BD'] ?? NULL),
      'WHERE' => $WHERE ?? [],
      'eliminadosYVigentes' => $eliminadosYVigentes,
    ];
    $resultado = $this->seleccionarDatos2($instruccionesBD);

    if ($resultado->rowCount() == 0) {
      $instruccionesRegistro = [
        'tabla' => $tabla,
        'BD' => ($instrucciones['BD'] ?? NULL),
        'datos' => []
      ];
      foreach ($WHERE as $clave => $valor) {
        $instruccionesRegistro['datos'][$clave] = $valor;
      };
      $ultimoId = $this->guardarDatos2($instruccionesRegistro);
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
      $resultado = $this->seleccionarDatos2($instruccionesBD);
      if (count($camposArray) == 1) {
        return $resultado->fetch(PDO::FETCH_COLUMN);
      } elseif (count($camposArray) > 1) {
        return $resultado->fetch(PDO::FETCH_ASSOC);
      }
    } else {
      $info = $resultado->fetch(PDO::FETCH_ASSOC);
      if ($RSEN == true) {
        $estado = $info['status'];
        if ($estado == 0) {
          $resultadoAct = $this->actualizarDatos2([
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
  public function DECORE($respuesta)
  {
    if ($respuesta instanceof FPDF) {
      $_SESSION['codigoRequest'] = 200;
      $respuesta->Output('I', 'Reporte');
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
  public function Imagenes_Reg($subCarpeta, $imagen, $tablaBD)
  {
    $procesarImagenes = function ($subCarpeta, $imagen, $tablaBD) {
      if ($tablaBD == "") {
        $alerta = [
          "tipo" => "simple",
          "titulo" => "Ocurrió un error inesperado",
          "texto" => "No puedes registrar una imagen con campos vacíos",
          "icono" => "error",
        ];
        return $alerta;
        exit();
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
          exit();
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
        exit();
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
        exit();
      }

      $nombreFoto = str_ireplace(" ", "_", $tablaBD);
      $nombreFoto = $nombreFoto . "_" . $this->FechaHora_Sel("Fecha_hora_foto") . '_' . rand(1, 100);
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
        $alerta = [
          "tipo" => "simple",
          "titulo" => "Archivo no movido",
          "texto" => "El archivo no pude ser movido a la carpeta destino",
          "icono" => "error",
        ];
        return $alerta;
      }
      return $nombreFoto;
    };
    $nombresFotos = [];
    if (is_array($imagen['name'])) {
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
  public function Imagenes_Act($instrucciones)
  {
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
    $resultado = $this->seleccionarDatos2([
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
      exit();
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
    $resultado = $this->actualizarDatos2([
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
      $this->commit();
    }
    return $alerta;
  }
  public function Imagenes_Eli($instrucciones)
  {
    $subCarpeta = $instrucciones['subCarpeta'];
    $tablaBD = $instrucciones['tablaBD'];
    $nombreCampofoto = $instrucciones['nombreCampoFoto'];
    $nombreCampoId = $instrucciones['nombreCampoId'];
    $valorId = $instrucciones['valorId'];
    $BD = $instrucciones['BD'] ?? NULL;

    $valorId = $this->limpiarCadena($valorId);
    $resultado = $this->seleccionarDatos2([
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
      exit();
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
    $resultado = $this->actualizarDatos2([
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
      $this->commit();
    }
    return $alerta;
  }
  public function Imagenes_Eli2($subCarpeta, $foto)
  {
    $foto = explode('?', $foto);
    $foto = $foto[0];
    if (is_file(DIR_FOTOS . $subCarpeta . '/' . $foto)) { /*verificamos que se creó */
      chmod(DIR_FOTOS . $subCarpeta . '/' . $foto, 0777); /*le damos permiso a la carpeta para eliminar */
      unlink(DIR_FOTOS . $subCarpeta . '/' . $foto); /*borramos el archivos */
      return true;
    } else {
      $alerta = [
        "tipo" => "simple",
        "titulo" => "Imagen no eliminada",
        "texto" => "La imagen ha sido eliminada con éxito",
        "icono" => "error"
      ];
      return $alerta;
    }
  }
  public function generarCodSeg($instrucciones)
  {
    [
      'tablaBD' => $tablaBD,
      'prefijo' => $prefijo,
      'campoID' => $campoID
    ] = $instrucciones;
    $BD = $instrucciones['BD'] ?? NULL;

    $resultado = $this->seleccionarDatosP2([
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
  // #endregion [PUBLIC]

  // #region [ PROTECTED ]
  protected function conectar($BD = null)
  {
    return $this->conectarP($BD);
  }
  protected function commit()
  {
    return $this->commitP();
  }
  protected function rollback()
  {
    return $this->rollbackP();
  }
  protected function seleccionarDatos(array $datos)
  {
    return $this->seleccionarDatosP($datos);
  }
  protected function guardarDatos($tabla, $datos, $condicion = null)
  {

    foreach ($datos as &$dato) {
      //Pasar a mayúsculas
      if (isset($dato['ponerEnMayusculas'])) {
        $dato['campo_valor'] = mb_strtoupper($dato['campo_valor']);
      }

      //Pasar la coma a punto
      if (isset($dato['comaPunto'])) {
        $dato['campo_valor'] = str_ireplace(',', '.', $dato['campo_valor']);
      }
    }

    return $this->guardarDatosP($tabla, $datos, $condicion);
  }
  protected function actualizarDatos($instrucciones)
  {

    foreach ($instrucciones['datos'] as &$dato) {
      //Pasar a mayúsculas
      if (isset($dato['ponerEnMayusculas'])) {
        $dato['campo_valor'] = mb_strtoupper($dato['campo_valor']);
      }

      //Pasar la coma a punto
      if (isset($dato['comaPunto'])) {
        $dato['campo_valor'] = str_ireplace(',', '.', $dato['campo_valor']);
      }
    }

    return $this->actualizarDatosP($instrucciones);
  }
  protected function eliminarDatos($tabla, $campo, $id, $permanente = null)
  {
    return $this->eliminarDatosP($tabla, $campo, $id, $permanente);
  }
  // #endregion [ PROTECTED ]

  // #region [ PRIVATE ]
  private function conectarP($BD)
  {
    $conexionElegida = self::$conexion;
    $this->nombreDB = 'proyecto_lacruz';
    if ($BD == 'seguridad') {
      $conexionElegida = self::$conexion2;
      $this->nombreDB = 'proyecto_lacruz_seguridad';
    }

    if ($conexionElegida instanceof PDO && $conexionElegida->inTransaction()) {
      return $conexionElegida;
    }
    if ($conexionElegida == null) {
      try {
        $conexionElegida = new PDO(
          "mysql:host=" . $this->servidorDB . ";dbname=" . $this->nombreDB,
          $this->userDB,
          $this->passwordDB,
          [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            //PDO::ATTR_EMULATE_PREPARES => false, // Mejor seguridad y rendimiento
          ]
        );
        $conexionElegida->exec("SET CHARACTER SET utf8");
      } catch (PDOException $e) {
        // Para obtener el error
        throw new PDOException("Error al establecer la conexión con la base de datos: " . $e->getMessage(), " Código de error: " . $e->getCode());
      }
    }
    if (!$conexionElegida->inTransaction()) {
      try {
        $conexionElegida->beginTransaction();
      } catch (PDOException $e) {
        throw new PDOException("Error al iniciar la transacción: " . $e->getMessage(), " Este es el código: " . $e->getCode());
      }
    }

    //Guardar la conexion
    if ($BD == 'seguridad') {
      self::$conexion2 = $conexionElegida;
    } else {
      self::$conexion = $conexionElegida;
    }

    return $conexionElegida;
  }
  private function commitP()
  {
    //verificamos que la conexión esté abierta y esté en una transacción
    if (self::$conexion instanceof PDO && self::$conexion->inTransaction()) {
      try {
        self::$conexion->commit();
      } catch (PDOException $e) {
        throw new PDOException("Error al confirmar la transacción (COMMIT): " . $e->getMessage(), (int)$e->getCode());
      }
    }
    //verificamos que la conexión esté abierta y esté en una transacción
    if (self::$conexion2 instanceof PDO && self::$conexion2->inTransaction()) {
      try {
        self::$conexion2->commit();
      } catch (PDOException $e) {
        throw new PDOException("Error al confirmar la transacción (COMMIT): " . $e->getMessage(), (int)$e->getCode());
      }
    }
  }
  private function rollbackP()
  {
    if (self::$conexion instanceof PDO && self::$conexion->inTransaction()) {
      try {
        self::$conexion->rollBack();
      } catch (PDOException $e) {
        throw new PDOException("Error al revertir la transacción (ROLLBACK): " . $e->getMessage(), (int)$e->getCode());
      }
    }
    if (self::$conexion2 instanceof PDO && self::$conexion2->inTransaction()) {
      try {
        self::$conexion2->rollBack();
      } catch (PDOException $e) {
        throw new PDOException("Error al revertir la transacción (ROLLBACK): " . $e->getMessage(), (int)$e->getCode());
      }
    }
  }
  private function seleccionarDatosP($datos)
  {
    try {
      //LOS CAMPOS Y LA TABLA
      $consulta = 'SELECT ' . $datos['campos'] . ' FROM ' . $datos['tabla'] . ' ';

      //INNER JOINS
      if (isset($datos['datosJoins'])) {
        foreach ($datos['datosJoins'] as $join) {
          if (is_array($join) && isset($join["tablaDestino"]) && isset($join["conexionLo"])) {
            if (isset($join['tipoJoin'])) {
              $consulta .=
                " " . $join['tipoJoin'] . " JOIN " . $join["tablaDestino"] .
                " ON " . $join["conexionLo"];
            } else {
              $consulta .=
                " INNER JOIN " . $join["tablaDestino"] .
                " ON " . $join["conexionLo"];
            }
          }
        }
      }
      //CONDICIÓN DE ELIMINADO LÓGICO
      if (isset($datos['registrosEli'])) {
        $consulta .= ' WHERE ';
        //Prefijo de Eliminado Lógico
        if (isset($datos['PEL'])) {
          $consulta .= '' . $datos['PEL'] . '.';
        }
        $consulta .= 'status = 0 ';
      } elseif (isset($datos['eliminadosYVigentes'])) {
        $consulta .= ' WHERE ';
        if (isset($datos['PEL'])) {
          $consulta .= '' . $datos['PEL'] . '.';
        }
        $consulta .= 'status > -1 ';
      } else {
        $consulta .= ' WHERE ';
        if (isset($datos['PEL'])) {
          $consulta .= '' . $datos['PEL'] . '.';
        }
        $consulta .= 'status != 0 ';
      }
      //WHERE EXTRAS
      if (isset($datos['WHERE'])) {
        foreach ($datos['WHERE'] as $condicion) {
          $consulta .= 'AND ' . $condicion['condicion_campo'] . ' ';
          $consulta .= $condicion['comparacion'] . ' ';
          $consulta .= $condicion['condicion_marcador'] . ' ';
        }
      }
      //GROUP BY
      if (isset($datos['GROUP']) && !empty($datos['GROUP'])) {
        $consulta .= " GROUP BY " . $datos['GROUP'];
      }
      // HAVING
      if (isset($datos['HAVING'])) {
        $consulta .= ' HAVING ';
        $ch = 0;
        foreach ($datos['HAVING'] as $condicion) {
          if ($ch > 0) {
            $consulta .= 'AND ';
          }
          $consulta .= $condicion['condicion_campo'] . ' ';
          $consulta .= $condicion['comparacion'] . ' ';
          $consulta .= $condicion['condicion_marcador'] . ' ';
          $ch++;
        }
      }
      //ORDER BY
      if (isset($datos['ORDER']) && !empty($datos['ORDER'])) {
        $consulta .= " ORDER BY " . $datos['ORDER'];
      }
      //LIMIT
      if (isset($datos['LIMIT']) && !empty($datos['LIMIT'])) {
        $consulta .= " LIMIT " . $datos['LIMIT'];
      }

      //PREPARACIÓN (ANTI-SQL-INYECTION)
      $conexion = $this->conectar($datos['BD'] ?? NULL);
      $consulta = $conexion->prepare($consulta);

      //HACEMOS EL BIND DE MARCADORES POR VALORES
      if (isset($datos['WHERE'])) {
        foreach ($datos['WHERE'] as $condicion) {
          $consulta->bindParam($condicion["condicion_marcador"], $condicion["condicion_valor"]);
        }
      }
      if (isset($datos['HAVING'])) {
        foreach ($datos['HAVING'] as $condicion) {
          $consulta->bindParam($condicion["condicion_marcador"], $condicion["condicion_valor"]);
        }
      }
      $consulta->execute();
      return $consulta;
    } catch (\Throwable $th) {
      $this->rollback();
      $error = [
        'titulo' => 'Error en la selección',
        'linea' => $th->getLine(),
        'código de error' => $th->getCode(),
        'mensaje de error' => $th->getMessage(),
        'Rastro' => $th->getTrace(),
        'instrucciones' => $datos
      ];
      throw new errorBD($th->getMessage(), $error, (int)$th->getCode(), $th);
    }
  }
  private function guardarDatosP($tabla, $datos, $condicion)
  {
    try {
      $conexion = $this->conectar();
      if ($condicion != null) {
        /*Para verificar si el id, cedula, placa o cualquier código que se esté intentando ingresar ya se encuentra en la BD */
        $instruccionesBD = [
          'campos' => '*',
          'registrosEli' => true,
          'tabla' => $tabla,
          'WHERE' => [
            [
              'condicion_campo' => $condicion["condicion_campo"],
              'condicion_marcador' => ':Id',
              'condicion_valor' => $condicion["condicion_valor"],
              'comparacion' => '=',
            ]
          ]
        ];

        $registroExistente = $this->seleccionarDatos($instruccionesBD);
        if ($registroExistente->rowCount() > 0) {
          //comenzamos la consulta SQL
          $query = "UPDATE $tabla SET ";

          //recorremos el arrays con los campos de la misma
          $C = 0;
          foreach ($datos as $clave) {

            if ($C >= 1) {
              $query .= ",";
            }
            $query .= $clave["campo_nombre"] . "=" .  $clave["campo_marcador"];
            $C++;
          }

          $query .= ", status = 1";
          $query .= " WHERE " . $condicion["condicion_campo"] . "=" . $condicion["condicion_marcador"];

          //la preparamos para evitar la inyeccion de sql
          $sql = $conexion->prepare($query);

          //recorremos el array con la condicion de la misma
          foreach ($datos as $clave) {
            $sql->bindParam($clave["campo_marcador"], $clave["campo_valor"]);
            $C++;
          }

          $sql->bindParam($condicion["condicion_marcador"], $condicion["condicion_valor"]);
          $sql->execute(); //ejecutamos la consulta

          return $sql->rowCount();
        } else {
          $query = "INSERT INTO $tabla (";

          $C = 0;
          foreach ($datos as $clave) {
            if ($C >= 1) {
              $query .= ", ";
            }
            $query .= $clave["campo_nombre"];
            $C++;
          }

          $query .= ") VALUES (";

          $C = 0;
          foreach ($datos as $clave) {
            if ($C >= 1) {
              $query .= ", ";
            }
            $query .= $clave["campo_marcador"];
            $C++;
          }

          $query .= " ) ";
          $sql = $conexion->prepare($query);

          foreach ($datos as $clave) {
            $sql->bindParam($clave["campo_marcador"], $clave["campo_valor"]);
          }

          $sql->execute();

          //Porque el ID puede o no ser autoincremental
          if ($conexion->lastInsertId() > 0) {
            return $conexion->lastInsertId();
          } else {
            return $sql->rowCount();
          }
        }
      } else {
        $query = "INSERT INTO $tabla (";

        $C = 0;
        foreach ($datos as $clave) {
          if ($C >= 1) {
            $query .= ", ";
          }
          $query .= $clave["campo_nombre"];
          $C++;
        }
        $query .= ") VALUES (";
        $C = 0;
        foreach ($datos as $clave) {
          if ($C >= 1) {
            $query .= ", ";
          }
          $query .= $clave["campo_marcador"];
          $C++;
        }
        $query .= ")";
        $sql = $conexion->prepare($query);
        foreach ($datos as $clave) {
          $sql->bindParam($clave["campo_marcador"], $clave["campo_valor"]);
        }
        $sql->execute();
        return $conexion->lastInsertId();
      }
    } catch (\Throwable $th) {
      $this->rollback();
      $error = [
        'titulo' => 'Error en el guardado',
        'linea' => $th->getLine(),
        'código de error' => $th->getCode(),
        'mensaje de error' => $th->getMessage(),
        'Rastro' => $th->getTrace(),
        'instrucciones' => $datos
      ];
      throw new errorBD($th->getMessage(), $error, (int)$th->getCode(), $th);
    }
  }
  private function actualizarDatosP($instrucciones)
  {
    try {
      $conexion = $this->conectar();
      $query = "UPDATE " . $instrucciones['tabla'] . " SET ";
      $C = 0;
      foreach ($instrucciones['datos'] as $clave) {
        if ($C >= 1) {
          $query .= ", ";
        }
        $query .= $clave["campo_nombre"] . " = " .  $clave["campo_marcador"];
        $C++;
      }
      $query .= " WHERE ";

      $co = 0;
      $numeroCondi = count($instrucciones['WHERE']);
      foreach ($instrucciones['WHERE'] as $condicion) {

        $query .= $condicion["condicion_campo"] . ' ';
        $query .= $condicion['comparacion'] . ' ';
        $query .= $condicion["condicion_marcador"] . ' ';

        if ($numeroCondi > 1 && $co == 0) {
          $query .= " AND (";
        }

        $co++;

        if ($co > 1 && $numeroCondi > 2 && $numeroCondi > $co) {
          $query .= " OR ";
        }
      }
      if ($numeroCondi > 1) {
        $query .= " )";
      }
      $sql = $conexion->prepare($query);
      foreach ($instrucciones['datos'] as $dato) {
        $sql->bindParam($dato["campo_marcador"], $dato["campo_valor"]);
      }
      foreach ($instrucciones['WHERE'] as $condicion) {
        $sql->bindParam($condicion["condicion_marcador"], $condicion["condicion_valor"]);
      }
      $sql->execute(); //ejecutamos la consulta
      return $sql->rowCount();
    } catch (\Throwable $th) {
      $this->rollback();
      $error = [
        'titulo' => 'Error en la actualización',
        'linea' => $th->getLine(),
        'código de error' => $th->getCode(),
        'mensaje de error' => $th->getMessage(),
        'Rastro' => $th->getTrace(),
        'instrucciones' => $instrucciones
      ];
      throw new errorBD($th->getMessage(), $error, (int)$th->getCode(), $th);
    }
  }
  private function eliminarDatosP($tabla, $campo, $id, $permanente)
  {
    try {
      $conexion = $this->conectar();
      if ($permanente == true) {
        $sql = $conexion->prepare("DELETE FROM $tabla WHERE $campo = :id");
      } else {
        $sql = $conexion->prepare("UPDATE $tabla SET status = 0 WHERE $campo = :id");
      }
      $sql->bindParam(":id", $id);
      $sql->execute();
      return $sql;
    } catch (\Throwable $th) {
      $this->rollback();
      $error = [
        'titulo' => 'Error en la eliminación',
        'linea' => $th->getLine(),
        'código de error' => $th->getCode(),
        'mensaje de error' => $th->getMessage(),
        'Rastro' => $th->getTrace(),
        'instrucciones' => [
          'tabla' => $tabla,
          'campo' => $campo,
          'id' => $id,
          'permanente' => $permanente,
        ]
      ];
      throw new errorBD($th->getMessage(), $error, (int)$th->getCode(), $th);
    }
  }
  private function seleccionarDatosP2($instrucciones)
  {
    try {
      $conexion = $this->conectar(($instrucciones['BD'] ?? NULL));
      $campos = $instrucciones['campos'];
      $tabla = trim($instrucciones['tabla']);
      $datosJoins = $instrucciones['datosJoins'] ?? false;
      $registrosEli = $instrucciones['registrosEli'] ?? false;
      $eliminadosYVigentes = $instrucciones['eliminadosYVigentes'] ?? false;
      $WHERE = $instrucciones['WHERE'] ?? false;
      $GROUP = $instrucciones['GROUP'] ?? false;
      $HAVING = $instrucciones['HAVING'] ?? false;
      $ORDER = $instrucciones['ORDER'] ?? false;
      $LIMIT = $instrucciones['LIMIT'] ?? false;
      $FOR_UPDATE = $instrucciones['FOR_UPDATE'] ?? false;
      $patronAS_captura = '/\b(as|AS)\b\s*(.*)/i';
      $PEL = '';
      if (preg_match($patronAS_captura, $tabla, $matches)) {
        $PEL = trim($matches[2]) . '.';
      }
      //LOS CAMPOS Y LA TABLA
      $consulta = 'SELECT ' . $campos . ' FROM ' . $tabla . ' ';

      //INNER JOINS        
      if ($datosJoins) {
        foreach ($datosJoins as $tablaDestino => &$conexionLo) {
          $patron = '/^\b(LEFT|RIGHT|FULL|OUTER|CROSS|NATURAL)\b/i';
          $tipoJoin = 'INNER';
          if (preg_match($patron, $tablaDestino, $matches)) {
            $tipoJoin = $matches[0] ?? 'INNER';
            $palabras = ['INNER', 'LEFT', "RIGHT", "FULL", "OUTER", "CROSS", "NATURAL"];
            foreach ($palabras as $palabra) {
              $tablaDestino = str_ireplace($palabra, "", $tablaDestino);
            }
          }
          $consulta .= " " . $tipoJoin . " JOIN " . $tablaDestino . " ON " . $conexionLo;
        }
      }

      //CONDICIÓN DE ELIMINADO LÓGICO
      $consulta .= ' WHERE ' . $PEL . 'status ';
      if ($registrosEli) {
        $consulta .= '= 0 ';
      } elseif ($eliminadosYVigentes == true) {
        $consulta .= '> -1 ';
      } else {
        $consulta .= '!= 0 ';
      }

      $guardarMarcador = function (&$almacenamiento, $campo) {
        $campo = $this->limpiarCadena($campo, 'antiFuncionesSQL');
        $marcador = ':' . str_ireplace('.', "_", $campo);

        if (!isset($almacenamiento[$campo])) {
          $almacenamiento[$campo] = [$marcador];
        } else {
          $marcador .= count($almacenamiento[$campo]);
          $almacenamiento[$campo][] = $marcador;
        }
        return $marcador;
      };

      if ($WHERE) {
        $marcadoresWHERE = [];
        foreach ($WHERE as $claveW1 => &$valorW1) {
          if (!is_array($valorW1)) {
            $patron_operador = '/^(<=|>=|<>|<|>|!==|===|!=)/';
            $operadorLo = '=';
            if (preg_match($patron_operador, $valorW1, $matches)) {
              $operadorLo = $matches[0];
              $palabras = ["<=", ">=", "<>", "<", ">", "=", "!==", "===", "!=", "==", "!"];
              foreach ($palabras as $palabra) {
                $valorW1 = str_ireplace($palabra, "", $valorW1);
              }
            }

            $marcador = $guardarMarcador($marcadoresWHERE, $claveW1);
            $consulta .= ' AND ' . $claveW1 . ' ' . $operadorLo . ' ' . $marcador . ' ';
          } else {
            foreach ($valorW1 as $operadorW1 => $valoresW1) {
              if (!is_array($valoresW1)) {
                $marcador = $guardarMarcador($marcadoresWHERE, $claveW1);
                $consulta .= ' AND ' . $claveW1 . ' ' . $operadorW1 . ' ' . $marcador . ' ';
              } else {
                $CV = 0;
                $operadorW1 = trim($operadorW1);
                foreach ($valoresW1 as $v) {
                  if ($operadorW1 == '=' && $CV > 0) {
                    $UNION = ' OR ';
                  } else {
                    $UNION = ' AND ';
                  }
                  $marcador = $guardarMarcador($marcadoresWHERE, $claveW1);
                  $consulta .= $UNION . $claveW1 . ' ' . $operadorW1 . ' ' . $marcador . ' ';
                  $CV++;
                }
              }
            }
          }
        }
        unset($valorW1);
      }
      if ($HAVING) {
        $marcadoresHAVING = [];
        $consulta .= ' HAVING ';
        $ch = 0;
        foreach ($HAVING as $claveH1 => &$valorH1) {
          if ($ch > 0) {
            $consulta .= ' AND ';
          }

          if (!is_array($valorH1)) {
            $patron_operador = '/^(<=|>=|<>|<|>|!=)/';
            $operadorLo = '=';
            if (preg_match($patron_operador, $valorH1, $matches)) {
              $operadorLo = $matches[0];
              $palabras = ["<=", ">=", "<>", "<", ">", "!="];
              foreach ($palabras as $palabra) {
                $valorH1 = str_ireplace($palabra, "", $valorH1);
              }
            }
            $marcador = $guardarMarcador($marcadoresHAVING, $claveH1);
            $consulta .= $claveH1 . ' ' . $operadorLo . ' ' . $marcador . ' ';
          } else {
            foreach ($valorH1 as $operadorH1 => $valoresH1) {
              if (is_array($valoresH1)) {
                foreach ($valoresH1 as $valor) {
                  $marcador = $guardarMarcador($marcadoresHAVING, $claveH1);
                  if (is_array($claveH1) || is_array($operadorH1) || is_array($marcador)) {
                    return ['aqui10'];
                  }
                  $consulta .= ' AND ' . $claveH1 . ' ' . $operadorH1 . ' ' . $marcador . ' ';
                }
              } else {
                $marcador = $guardarMarcador($marcadoresHAVING, $claveH1);
                $consulta .= ' AND ' . $claveH1 . ' ' . $operadorH1 . ' ' . $marcador . ' ';
              }
            }
          }
          $ch++;
        }
        unset($valorH1);
      }
      if ($GROUP) {
        $consulta .= " GROUP BY " . $GROUP;
      }
      if ($ORDER) {
        $consulta .= " ORDER BY " . $ORDER;
      }
      if ($LIMIT) {
        $consulta .= " LIMIT " . $LIMIT;
      }
      if ($FOR_UPDATE) {
        if (!is_array($FOR_UPDATE)) {
          $FOR_UPDATE = [$FOR_UPDATE];
        }
        for ($i = 0; $i < count($FOR_UPDATE); $i++) {
          if ($i == 0) {
            if ($datosJoins) {
              $consulta .= " FOR UPDATE OF " . $FOR_UPDATE[$i];
            } else {
              $consulta .= " FOR UPDATE";
            }
          } else {
            $consulta .= ", " . $FOR_UPDATE[$i];
          }
        };
      }

      $consulta = preg_replace('/\s+/', ' ', $consulta);
      $consulta = $conexion->prepare($consulta);
      if ($WHERE) {
        foreach ($WHERE as $claveW2 => $valorW2) {
          $claveW2 = $this->limpiarCadena($claveW2, 'antiFuncionesSQL');
          if (!is_array($valorW2)) {
            $marcador = array_shift($marcadoresWHERE[$claveW2]);
            $consulta->bindValue($marcador, $valorW2);
          } else {
            foreach ($valorW2 as $operadorW2 => $valoresW2) {
              if (is_array($valoresW2)) {
                foreach ($valoresW2 as $valorInd) {
                  $marcador = array_shift($marcadoresWHERE[$claveW2]);
                  $consulta->bindValue($marcador, $valorInd);
                }
              } else {
                $marcador = array_shift($marcadoresWHERE[$claveW2]);
                $consulta->bindValue($marcador, $valoresW2);
              }
            }
          }
        }
      }
      if ($HAVING) {
        foreach ($HAVING as $claveH2 => $valorH2) {
          if (!is_array($valorH2)) {
            $claveH2 = $this->limpiarCadena($claveH2, 'antiFuncionesSQL');
            $marcador = array_shift($marcadoresHAVING[$claveH2]);
            $consulta->bindValue($marcador, $valorH2);
          } else {
            foreach ($valorH2 as $operadorH2 => $valoresH2) {
              if (is_array($valoresH2)) {
                foreach ($valoresH2 as $valorInd) {
                  $marcador = array_shift($marcadoresHAVING[$claveH2]);
                  $consulta->bindValue($marcador, $valorInd);
                }
              } else {
                $marcador = array_shift($marcadoresHAVING[$claveH2]);
                $consulta->bindValue($marcador, $valoresH2);
              }
            }
          }
        }
      }
      $consulta->execute();
      return $consulta;
    } catch (\Throwable $th) {
      $this->rollback();
      $error = [
        'titulo' => 'Error en la selección',
        'linea' => (int)$th->getLine(),
        'código de error' => (int)$th->getCode(),
        'mensaje de error' => $th->getMessage(),
        'rastro' => $th->getTrace(),
        'instrucciones' => $instrucciones,
        'consulta' => $consulta ?? 'Sin consulta',
      ];
      throw new errorBD($th->getMessage(), $error, (int)$th->getCode(), $th);
    }
  }
  private function guardarDatosP2($instrucciones)
  {
    try {
      $conexion = $this->conectar($instrucciones['BD'] ?? NULL);
      [
        'tabla' => $tabla,
        'datos' => $datos,
      ] = $instrucciones;
      $WHERE =  $instrucciones['WHERE'] ?? $instrucciones['camposReciclado'] ?? false;
      if (empty($datos) || !is_array($datos)) {
        throw new \Exception("El parámetro 'datos' está vacío o no es un array en guardarDatosP.");
      }
      if (empty($tabla) || $tabla == '') {
        throw new \Exception("El parámetro 'tabla' está vacío dentro del metodo de guardarDatosP");
      }

      $esActualizacion = false;
      if ($WHERE != false) {
        $instruccionesBD = [
          'campos' => '*',
          'registrosEli' => true,
          'tabla' => $tabla,
          'BD' => ($instrucciones['BD'] ?? NULL),
          'WHERE' => $WHERE
        ];

        $resultado = $this->seleccionarDatosP2($instruccionesBD);
        if ($resultado->rowCount() > 0) {
          $esActualizacion = true;
        }
      }
      if ($esActualizacion) {
        $instrucciones['reciclaje'] = true;
        return $this->actualizarDatos2($instrucciones);
      } else {
        $query = "INSERT INTO $tabla ";

        $C = 0;
        $stringCampos = '';
        $stringMarcadores = '';

        foreach ($datos as $clave => $valor) {
          if ($C >= 1) {
            $stringCampos .= ', ';
            $stringMarcadores .= ', ';
          }
          $stringCampos .= $clave;
          $marcador = ':' . str_ireplace('.', "_", $clave);
          $stringMarcadores .= $marcador;
          $C++;
        }
        $query .= '(' . $stringCampos . ') VALUES (' . $stringMarcadores . ')';
        $sql = $conexion->prepare($query);

        foreach ($datos as $clave => $valor) {
          $marcador = ':' . str_ireplace('.', "_", $clave);
          $sql->bindValue($marcador, $valor);
        }
        $sql->execute();

        //Porque el ID puede o no ser autoincremental
        if ($conexion->lastInsertId() > 0) {
          return $conexion->lastInsertId();
        } else {
          return $sql->rowCount();
        }
      }
    } catch (\Throwable $th) {
      $this->rollback();
      $error = [
        'titulo' => 'Error en la inserción',
        'linea' => $th->getLine(),
        'código de error' => $th->getCode(),
        'mensaje de error' => $th->getMessage(),
        'rastro' => $th->getTrace(),
        'instrucciones' => $instrucciones
      ];
      throw new errorBD($th->getMessage(), $error, (int)$th->getCode(), $th);
    }
  }
  private function actualizarDatosP2($instrucciones)
  {
    try {
      $conexion = $this->conectar($instrucciones['BD'] ?? NULL);
      [
        'tabla' => $tabla,
        'datos' => $datos,
      ] = $instrucciones;
      $WHERE = $instrucciones['camposReciclado'] ?? $instrucciones['WHERE'];
      $reciclaje = $instrucciones['reciclaje'] ?? false;

      //comenzamos la consulta SQL
      $consulta = "UPDATE $tabla SET ";

      $guardarMarcador = function (&$almacenamiento, $campo) {
        $campo = $this->limpiarCadena($campo, 'antiFuncionesSQL');
        $marcador = ':' . str_ireplace('.', "_", $campo);
        if (!isset($almacenamiento[$campo])) {
          $almacenamiento[$campo] = [$marcador];
        } else {
          $marcador .= count($almacenamiento[$campo]);
          $almacenamiento[$campo][] = $marcador;
        }
        return $marcador;
      };
      $marcadoresConsulta = [];

      $C = 0;
      foreach ($datos as $campoClave => $campoValor) {
        if ($C >= 1) {
          $consulta .= ",";
        }
        $marcador = $guardarMarcador($marcadoresConsulta, $campoClave);
        $consulta .= $campoClave . " = " . $marcador;
        $C++;
      }

      if ($reciclaje) {
        $consulta .= ', status = 1';
      }
      $consulta .= " WHERE ";

      $c2 = 0;
      foreach ($WHERE as $claveW1 => &$valorW1) {
        if ($c2 != 0) {
          $consulta .= ' AND ';
        }
        if (!is_array($valorW1)) {
          $patron_operador = '/^(<=|>=|<>|<|>|!==|===|!=)/';
          $operadorLo = '=';
          if (preg_match($patron_operador, $valorW1, $matches)) {
            $operadorLo = $matches[0];
            $palabras = ["<=", ">=", "<>", "<", ">", "=", "!==", "===", "!=", "==", "!"];
            foreach ($palabras as $palabra) {
              $valorW1 = str_ireplace($palabra, "", $valorW1);
            }
          }
          $marcador = $guardarMarcador($marcadoresConsulta, $claveW1);
          $consulta .= $claveW1 . ' ' . $operadorLo . ' ' . $marcador . ' ';
        } else {
          foreach ($valorW1 as $operadorW1 => $valoresW1) {
            if (!is_array($valoresW1)) {
              $marcador = $guardarMarcador($marcadoresWHERE, $claveW1);
              $consulta .= ' AND ' . $claveW1 . ' ' . $operadorW1 . ' ' . $marcador . ' ';
            } else {
              $CV = 0;
              $operadorW1 = trim($operadorW1);
              foreach ($valoresW1 as $v) {
                if ($operadorW1 == '=' && $CV > 0) {
                  $UNION = ' OR ';
                } else {
                  $UNION = ' AND ';
                }
                $marcador = $guardarMarcador($marcadoresConsulta, $claveW1);
                $consulta .= $UNION . $claveW1 . ' ' . $operadorW1 . ' ' . $marcador . ' ';
                $CV++;
              }
            }
          }
        }
        $c2++;
      }
      unset($valorW1);
      $sql = $conexion->prepare($consulta);
      foreach ($datos as $campoClave2 => $valorCampo2) {
        $marcador = array_shift($marcadoresConsulta[$campoClave2]);
        $sql->bindValue($marcador, $valorCampo2);
      }
      foreach ($WHERE as $claveW2 => $valorW2) {
        $claveW2 = $this->limpiarCadena($claveW2, 'antiFuncionesSQL');
        if (!is_array($valorW2)) {
          $marcador = array_shift($marcadoresConsulta[$claveW2]);
          $sql->bindValue($marcador, $valorW2);
        } else {
          foreach ($valorW2 as $operadorW2 => $valoresW2) {
            if (is_array($valoresW2)) {
              foreach ($valoresW2 as $valorInd) {
                $marcador = array_shift($marcadoresConsulta[$claveW2]);
                $sql->bindValue($marcador, $valorInd);
              }
            } else {
              $marcador = array_shift($marcadoresConsulta[$claveW2]);
              $sql->bindValue($marcador, $valoresW2);
            }
          }
        }
      }
      $sql->execute();
      return $sql->rowCount();
    } catch (\Throwable $th) {
      $this->rollback();
      $error = [
        'titulo' => 'Error en la actualización',
        'linea' => $th->getLine(),
        'código de error' => $th->getCode(),
        'mensaje de error' => $th->getMessage(),
        'consulta' => $consulta ?? 'SIN CONSULTA',
        'rastro' => $th->getTrace(),
        'instrucciones' => $instrucciones
      ];
      throw new errorBD($th->getMessage(), $error, (int)$th->getCode(), $th);
    }
  }
  private function eliminarDatosP2($instrucciones)
  {
    try {
      $conexion = $this->conectar($instrucciones['BD'] ?? NULL);
      [
        'tabla' => $tabla,
        'WHERE' => $WHERE,
      ] = $instrucciones;
      $permanentemente = $instrucciones['fisico'] ?? false;

      $consulta = '';
      if ($permanentemente == true) {
        $consulta .= "DELETE FROM $tabla";
      } else {
        $consulta .= "UPDATE $tabla SET status = 0";
      }
      $consulta .= " WHERE ";

      $guardarMarcador = function (&$almacenamiento, $campo) {
        $campo = $this->limpiarCadena($campo, 'antiFuncionesSQL');
        $marcador = ':' . str_ireplace('.', "_", $campo);

        if (!isset($almacenamiento[$campo])) {
          $almacenamiento[$campo] = [$marcador];
        } else {
          $marcador .= count($almacenamiento[$campo]);
          $almacenamiento[$campo][] = $marcador;
        }
        return $marcador;
      };

      $marcadoresWHERE = [];
      $c = 0;
      foreach ($WHERE as $claveW1 => &$valorW1) {
        if ($c > 0) {
          $consulta .= ' AND ';
        }

        if (!is_array($valorW1)) {
          $patron_operador = '/\b(<=|>=|<>|<|>|=|!==|===|!=|==)\b/';
          $operadorLo = '=';
          if (preg_match($patron_operador, $valorW1, $matches)) {
            $operadorLo = $matches[0];
            $palabras = ["<=", ">=", "<>", "<", ">", "=", "!==", "===", "!=", "==", "!"];
            foreach ($palabras as $palabra) {
              $valorW1 = str_ireplace($palabra, "", $valorW1);
            }
          }

          $marcador = $guardarMarcador($marcadoresWHERE, $claveW1);
          $consulta .= $claveW1 . ' ' . $operadorLo . ' ' . $marcador . ' ';
        } else {
          foreach ($valorW1 as $operadorW1 => $valoresW1) {
            if (!is_array($valoresW1)) {
              $marcador = $guardarMarcador($marcadoresWHERE, $claveW1);
              $consulta .= $claveW1 . ' ' . $operadorW1 . ' ' . $marcador . ' ';
            } else {
              foreach ($valoresW1 as $v) {
                $marcador = $guardarMarcador($marcadoresWHERE, $claveW1);
                $consulta .= $claveW1 . ' ' . $operadorW1 . ' ' . $marcador . ' ';
              }
            }
          }
        }
        $c++;
      }
      unset($valorW1);

      $consulta = preg_replace('/\s+/', ' ', $consulta);
      $consulta = $conexion->prepare($consulta);

      foreach ($WHERE as $claveW2 => $valorW2) {
        $claveW2 = $this->limpiarCadena($claveW2, 'antiFuncionesSQL');
        if (!is_array($valorW2)) {
          $marcador = array_shift($marcadoresWHERE[$claveW2]);
          $consulta->bindValue($marcador, $valorW2);
        } else {
          foreach ($valorW2 as $operadorW2 => $valoresW2) {
            if (is_array($valoresW2)) {
              foreach ($valoresW2 as $valorInd) {
                $marcador = array_shift($marcadoresWHERE[$claveW2]);
                $consulta->bindValue($marcador, $valorInd);
              }
            } else {
              $marcador = array_shift($marcadoresWHERE[$claveW2]);
              $consulta->bindValue($marcador, $valoresW2);
            }
          }
        }
      }
      $consulta->execute();
      return $consulta->rowCount();
    } catch (\Throwable $th) {
      $this->rollback();
      $error = [
        'titulo' => 'Error en la eliminación',
        'linea' => $th->getLine(),
        'código de error' => $th->getCode(),
        'mensaje de error' => $th->getMessage(),
        'rastro' => $th->getTrace(),
        'instrucciones' => $instrucciones
      ];
      throw new errorBD($th->getMessage(), $error, (int)$th->getCode(), $th);
    }
  }
  // #endregion [ PRIVATE ]
}
