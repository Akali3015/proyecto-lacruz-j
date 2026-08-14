<?php

namespace src\modelos;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use src\config\connect\conexion;
use src\modelos\accesosModelo;

class correosModelo extends conexion {
  private array $destinatariosCorreo = [];
  private string $asuntoCorreo = '';
  private string $cuerpoCorreo = '';
  private bool $esHTML = false;

  public function validarCorreos(string|null $permiso, array &$info, $requerido = []) {
    if ($permiso) {
      $objAcceso = new accesosModelo();
      $v = $objAcceso->validarPermisos('correos', $permiso);
      if ($v) return $v;
    }
    $esquemaCorreos = [
      'tipo' => 'arrayA',
      'propiedades' => [
        'asunto_correo' => [
          ...molDescripcion,
          "nombreAlerta" => "asunto del correo",
        ],
        'cuerpo_correo' => [
          ...molCuerpoCorreo,
          "nombreAlerta" => "cuerpo del correo",
        ],
        'destinatarios_correo' => [
          'tipo' => ['array', 'arrayA'],
          'items' => [
            'tipo' => 'arrayA',
            'propiedades' => [
              'correo' => [
                ...molCorreo,
                'nombreAlerta' => 'correo del destinatario',
                'BD' => 'seguridad',
                'tablaBD' => 'usuarios',
                'nombreBD' => 'correo_usuario',
                'debeExistirBD' => true,
              ],
              'nombre' => [
                ...molNombreObj,
                'nombreAlerta' => 'nombre del destinatario'
              ]
            ],
            'requerido'=>['correo','nombre'],
          ],
          'minItems' => 1,
          'propiedades' => [
            'correo' => [
              ...molCorreo,
              'nombreAlerta' => 'correo del destinatario',
              'BD' => 'seguridad',
              'tablaBD' => 'usuarios',
              'nombreBD' => 'correo_usuario',
              'debeExistirBD' => true,
            ],
            'nombre' => [
              ...molNombreObj,
              'nombreAlerta' => 'nombre del destinatario'
            ]
          ],
          'requerido'=>['correo','nombre'],
          'nombreAlerta' => 'destinatarios del correo'
        ],
        'esHTML' => [
          ...molBooleano,
          'nombreAlerta' => 'si es o no HTML'
        ]
      ],
      'requerido' => $requerido
    ];
    $v = $this->limpiarValidar($info, $esquemaCorreos);
    if ($v) return $v;

    return false;
  }
  public function enviarCorreos(array $info) {
    $v = $this->validarCorreos(null, $info, [
      'asunto_correo',
      'cuerpo_correo',
      'destinatario_correo',
    ]);
    if ($v) return $v;
    
    $this->asuntoCorreo = $info['asunto_correo'];
    $this->cuerpoCorreo = $info['cuerpo_correo'];
    $this->destinatariosCorreo = $info['destinatarios_correo'];
    $this->esHTML = $info['esHTML'];
    return $this->enviarCorreosP();
  }
  private function enviarCorreosP() {
    $mail = new PHPMailer(true);
    try {
      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com';
      $mail->SMTPAuth = true;
      $mail->Username = 'multiserviciosjlacruz@gmail.com';
      $mail->Password   = 'minn wxww eidd vxlx';
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
      $mail->Port = 465;
      $mail->CharSet = 'UTF-8';
      // $mail->SMTPDebug = \PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;

      $mail->setFrom('multiserviciosjlacruz@gmail.com', 'MULTISERVICIOS JLACRUZ C.A.');
      if (isset($this->destinatariosCorreo['correo'])) {
        $mail->addAddress($this->destinatariosCorreo['correo'], $this->destinatariosCorreo['nombre']);
      } else {
        foreach ($this->destinatariosCorreo as $destinatario) {
          $mail->addAddress($destinatario['correo'], $destinatario['nombre']);
        }
      }

      $mail->isHTML($this->esHTML);
      $mail->Subject = $this->asuntoCorreo;
      
      if ($this->esHTML) {
        $mail->Body = $this->cuerpoCorreo;
      } else {
        $mail->AltBody = $this->cuerpoCorreo;
        $mail->Body = $this->cuerpoCorreo;
      }
      // return [$mail];
      $mail->send();
      return [
        'tipo' => 'simple',
        'titulo' => 'Correo enviado',
        'texto' => 'El correo ha sido enviado satisfactoriamente',
        'icono' => 'success'
      ];
    } catch (Exception $e) {
      return [
        'tipo' => 'simple',
        'titulo' => 'Correo no enviado',
        'texto' => 'El correo no ha sido enviado satisfactoriamente',
        'icono' => 'error',
        'errorCorreo' => $mail->ErrorInfo,
        'errorExcepcion' => $e->getMessage()
      ];
    }
  }
}
