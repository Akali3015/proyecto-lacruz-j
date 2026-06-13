<?php

namespace src\modelos;

use FPDF;
use DateTime;

class pdfModel extends FPDF {
  use traitModelo;
  private string $tituloEncabezado = '';
  private bool $header = true;
  private bool $footer = true;
  private array $datosExtraCabecera = [];

  public function __construct($instrucciones = null) {
    $this->header = $instrucciones['header'] ?? true;
    $this->footer = $instrucciones['footer'] ?? true;
    switch ($instrucciones['tamanoPagina'] ?? '') {
      case 'carta':
        $instrucciones['tamanoPagina'] = [216, 280];
        break;
      case 'oficio':
        $instrucciones['tamanoPagina'] = [215, 356];
        break;
      case 'factura55':
        $instrucciones['tamanoPagina'] = [55, 600];
        break;
      case 'factura80':
        $instrucciones['tamanoPagina'] = [80, 600];
        break;
    }
    parent::__construct(
      $instrucciones['orientacion'] ?? 'P',
      $instrucciones['unidadMedida'] ?? 'mm',
      $instrucciones['tamanoPagina'] ?? 'A4'
    );
  }
  public function calcularLineas(int $ancho, string $textoFila) {
    $cw = &$this->CurrentFont['cw'];
    if ($ancho == 0) {
      $ancho = $this->w - $this->rMargin - $this->x;
    }
    $anchoMaximo = ($ancho - 2 * $this->cMargin) * 1000 / $this->FontSize;
    $s = str_replace("\r", '', $textoFila);
    $nb = strlen($s);

    if ($nb > 0 && $s[$nb - 1] == "\n") {
      $nb--;
    }

    $sep = -1;
    $i = 0;
    $j = 0;
    $l = 0;
    $nl = 1;

    while ($i < $nb) {
      $c = $s[$i];
      if ($c == "\n") {
        $i++;
        $sep = -1;
        $j = $i;
        $l = 0;
        $nl++;
        continue;
      }
      if ($c == ' ') {
        $sep = $i;
      }
      if (!isset($cw[$c])) { // Manejo de caracteres no definidos en la fuente
        $l += 0;
      } else {
        $l += $cw[$c];
      }
      if ($l > $anchoMaximo) {
        if ($sep == -1) {
          if ($i == $j) {
            $i++;
          }
        } else {
          $i = $sep + 1;
        }
        $sep = -1;
        $j = $i;
        $l = 0;
        $nl++;
      } else {
        $i++;
      }
    }
    return $nl;
  }
  function Header() {
    if ($this->header == true) {
      // Logo
      $this->Image($_SERVER['DOCUMENT_ROOT'] . '/proyecto-lacruz-j/src/assets/images/logo2.png', 10, 10, 17, 15);
      //Encabezado
      $fechaActual = new DateTime();
      $fechaActual = $fechaActual->format('d-m-Y');

      $this->SetFont('Arial', 'B', 12);
      $this->Cell(30, 10);
      $this->Cell(135, 10, $this->tituloEncabezado, 0, 0, 'C');
      $this->Cell(30, 10, 'EMITIDO EL: ' . $fechaActual, 0, 0, 'R');
      $this->Ln(20);

      if (count($this->datosExtraCabecera) > 0) {
        foreach ($this->datosExtraCabecera as $dato) {
          $dato = mb_convert_encoding($dato, 'ISO-8859-1', 'UTF-8');
          $this->Cell(0, 10, $dato, 0, 1, 'C');
        }
      }
    }
  }
  function Footer() {
    if ($this->footer) {
      // Posición: a 1,5 cm del final
      $this->SetY(-10);
      // Arial italic 8
      $this->SetFont('Arial', 'I', 10);
      // Número de página
      $pagina = mb_convert_encoding("Página", 'ISO-8859-1', 'UTF-8');
      $this->Cell(0, 10, $pagina . ' ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
  }
  function RoundedRect(int $x, int $y, int $w, int $h, int $r, string $corners = '1234', string $style = '') {
    $k = $this->k;
    $hp = $this->h;
    if ($style == 'F')
      $op = 'f';
    elseif ($style == 'FD' || $style == 'DF')
      $op = 'B';
    else
      $op = 'S';
    $MyArc = 4 / 3 * (sqrt(2) - 1);
    $this->_out(sprintf('%.2F %.2F m', ($x + $r) * $k, ($hp - $y) * $k));

    $xc = $x + $w - $r;
    $yc = $y + $r;
    $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - $y) * $k));
    if (strpos($corners, '2') === false)
      $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - $y) * $k));
    else
      $this->_Arc($xc + $r * $MyArc, $yc - $r, $xc + $r, $yc - $r * $MyArc, $xc + $r, $yc);

    $xc = $x + $w - $r;
    $yc = $y + $h - $r;
    $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - $yc) * $k));
    if (strpos($corners, '3') === false)
      $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - ($y + $h)) * $k));
    else
      $this->_Arc($xc + $r, $yc + $r * $MyArc, $xc + $r * $MyArc, $yc + $r, $xc, $yc + $r);

    $xc = $x + $r;
    $yc = $y + $h - $r;
    $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - ($y + $h)) * $k));
    if (strpos($corners, '4') === false)
      $this->_out(sprintf('%.2F %.2F l', ($x) * $k, ($hp - ($y + $h)) * $k));
    else
      $this->_Arc($xc - $r * $MyArc, $yc + $r, $xc - $r, $yc + $r * $MyArc, $xc - $r, $yc);

    $xc = $x + $r;
    $yc = $y + $r;
    $this->_out(sprintf('%.2F %.2F l', ($x) * $k, ($hp - $yc) * $k));
    if (strpos($corners, '1') === false) {
      $this->_out(sprintf('%.2F %.2F l', ($x) * $k, ($hp - $y) * $k));
      $this->_out(sprintf('%.2F %.2F l', ($x + $r) * $k, ($hp - $y) * $k));
    } else
      $this->_Arc($xc - $r, $yc - $r * $MyArc, $xc - $r * $MyArc, $yc - $r, $xc, $yc - $r);
    $this->_out($op);
  }
  function _Arc(int $x1, int $y1, int  $x2, int  $y2, int $x3, int $y3) {
    $h = $this->h;
    $this->_out(sprintf(
      '%.2F %.2F %.2F %.2F %.2F %.2F c ',
      $x1 * $this->k,
      ($h - $y1) * $this->k,
      $x2 * $this->k,
      ($h - $y2) * $this->k,
      $x3 * $this->k,
      ($h - $y3) * $this->k
    ));
  }
  function RoundedCell(int $w, int $h, string $txt, int  $radius, array $colorCelda = array(255, 255, 255), array $colorTexto = array(0, 0, 0)) {
    $x = $this->GetX();
    $y = $this->GetY();
    $this->SetFillColor($colorCelda[0], $colorCelda[1], $colorCelda[2]);
    $this->RoundedRect($x, $y, $w, $h, $radius, 'DF');
    $this->SetTextColor($colorTexto[0], $colorTexto[1], $colorTexto[2]);
    $this->Cell($w, $h, $txt, 0, 0, 'C');
  }
  public function crearPDF(array $datosPdf) {
    $procesarSecciones = function ($datosPdf, $nroSeccion = null) {

      $configColumnas = $datosPdf['configColumnas'];
      $infoBD = $datosPdf['infoBD'];
      $this->tituloEncabezado = mb_convert_encoding($datosPdf['tituloReporte'] ?? '', 'ISO-8859-1', 'UTF-8');
      $this->datosExtraCabecera = $datosPdf['datosExtCabecera'] ?? [];

      // Primera sección o sección única: inicializar el documento
      if ($nroSeccion === null || $nroSeccion == 0) {
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetMargins(10, 10, 10);
        $this->SetAutoPageBreak(true, 10);
      } else {
        // Secciones adicionales: agregar nueva página con separador
        $this->AddPage();
        $this->SetFont('Arial', 'B', 12);
        $this->Ln();
        if (count($this->datosExtraCabecera) > 0) {
          foreach ($this->datosExtraCabecera as $dato) {
            $dato = mb_convert_encoding($dato, 'ISO-8859-1', 'UTF-8');
            $this->Cell(0, 10, $dato, 0, 1, 'C');
          }
        }
      }

      //Dibujamos los encabezados
      $this->SetFont("Arial", "B", "12");
      foreach ($configColumnas as $configInd) {
        $encabezado_convertido = mb_convert_encoding($configInd[0], 'ISO-8859-1', 'UTF-8');
        $this->Cell($configInd[1], 10, $encabezado_convertido, 1, 0, 'C');
      }
      $this->Ln();

      //Dibujamos las filas de datos
      $this->SetFont("Arial", "", "10");

      // Guardar la posición actual X y Y
      $x = $this->GetX();
      $y = $this->GetY();

      $clavesArreglo = array_keys($configColumnas);
      foreach ($infoBD as $fila) {
        $xActual = $x;

        //Calculamos el ancho de la fila
        $anchoCeldaMasGrande = 0;
        foreach ($clavesArreglo as $clave) {
          $anchoCeldaMasGrande = max($anchoCeldaMasGrande, $this->calcularLineas($configColumnas[$clave][1], $fila[$clave]));
        }
        $alturaFila = 5 * $anchoCeldaMasGrande;

        if ($y + $alturaFila > $this->PageBreakTrigger) {
          $this->AddPage();
          //Redibujamos los encabezados
          $this->SetFont("Arial", "B", "12");
          foreach ($configColumnas as $configInd) {
            $encabezado_convertido = mb_convert_encoding($configInd[0], 'ISO-8859-1', 'UTF-8');
            $this->Cell($configInd[1], 10, $encabezado_convertido, 1, 0, 'C');
          }
          $this->Ln();
          $this->SetFont("Arial", "", "10");

          $y = $this->GetY();
          $x = $this->GetX();
          $xActual = $x;
        }

        foreach ($clavesArreglo as $claveInd) {
          // Borde
          $this->Rect($xActual, $y, $configColumnas[$claveInd][1], $alturaFila);
          $this->SetXY($xActual, $y);
          $textoFila = mb_convert_encoding($fila[$claveInd], 'ISO-8859-1', 'UTF-8');
          $this->MultiCell($configColumnas[$claveInd][1], 5, $textoFila, 0, 'C');
          $xActual += $configColumnas[$claveInd][1];
        }
        // Mover a la siguiente fila actualizando la posición Y
        $y += $alturaFila;
      }
    };
    if (isset($datosPdf[0])) {
      foreach ($datosPdf as $numeroArray => &$datoPdf) {
        $procesarSecciones($datoPdf, $numeroArray);
      }
    } else {
      $procesarSecciones($datosPdf);
    }
    return $this;
  }
  public function crearComanda(array $secciones) {
    $this->AliasNbPages();
    $this->AddPage();
    $this->SetMargins(2, 0, 2);
    $this->SetAutoPageBreak(true, 10);
    $this->SetFont("Arial", "B", "10");
    $this->Cell(0, 1, '', 0, 1);
    $encabezado = mb_convert_encoding('THE VIÑA FAST FOOD', 'ISO-8859-1', 'UTF-8');
    $this->Cell(0, 3, $encabezado, 0, 1, 'C');

    foreach ($secciones as $seccion) {

      $this->SetFont("Arial", "B", "8");
      $this->Cell(0, 5, '', 0, 1, '');
      $this->Cell(0, 5, '---------------------------------------------------------', 0, 1, 'C');

      $encabezado = mb_convert_encoding($seccion['tituloSeccion'], 'ISO-8859-1', 'UTF-8');
      $this->Cell(0, 3, $encabezado, 0, 1, 'C');

      $datosExtraArriba = isset($seccion['datosExtra']['arriba']) ? $seccion['datosExtra']['arriba'] : false;
      $datosExtraAbajo = isset($seccion['datosExtra']['abajo']) ? $seccion['datosExtra']['abajo'] : false;

      // DATOS EXTRA DE ARRIBA
      if ($datosExtraArriba) {
        $this->SetFont("Arial", "B", "8");
        $this->Ln();
        $CDA = 0;
        foreach ($datosExtraArriba['datosCeldas'] as $datoEx) {
          $this->Cell(0, 3, $datoEx, 0, 1, $datosExtraArriba['alineaciones'][$CDA]);
          $CDA++;
        }
      }
      foreach ($seccion['datosMedio'] as &$datoCo) {
        $anchoColumnas = $datoCo['anchoColumnas'];
        $encabezados = $datoCo['encabezados'];
        $datosCeldas = $datoCo['datosCeldas'];
        $tituloEncabezado = $datoCo['encabezadoPrin'];
        $alineaciones = $datoCo['alineaciones'];

        if (count($datosCeldas) == 0) {
          continue;
        }
        $this->SetY($this->GetY());
        $this->SetFont("Arial", "B", "8");
        $this->Ln();
        $this->Cell(0, 5, $tituloEncabezado, 0, 1, 'C');

        //Dibujamos los encabezados
        $c = 0;
        $this->SetFont("Arial", "B", "8");
        foreach ($encabezados as $encabezado) {
          $encabezado_convertido = mb_convert_encoding($encabezado, 'ISO-8859-1', 'UTF-8');
          $this->Cell($anchoColumnas[$c], 3, $encabezado_convertido, 0, 0, 'C');
          $c++;
        }
        $this->Ln();

        //Dibujamos las filas de datos
        $this->SetFont("Arial", "B", "8");

        // Guardar la posición actual X y Y
        $x = $this->GetX();
        $y = $this->GetY();

        // Dibujamos las celdas
        $clavesArreglo = array_keys($datosCeldas[0]);

        $datosCalculo = [];
        foreach ($datosCeldas as $fila) {
          $xActual = $x;

          //Calculamos el ancho de las filas para saber cual sera la mas ancha
          $ancho = 0;
          $anchoCeldaMasGrande = 0;
          foreach ($clavesArreglo as $clave) {

            if (!isset($anchoColumnas[$ancho])) {
              return [
                'fila' => $fila,
                'anchos' => $anchoColumnas,
                'posicionAncho' => $ancho,
                'claves' => $clavesArreglo,
                'datosCeldas' => $clavesArreglo,
              ];
            }
            $anchoCeldaMasGrande = max(
              $anchoCeldaMasGrande,
              $this->calcularLineas(
                $anchoColumnas[$ancho],
                $fila[$clave]
              )
            );
            $ancho++;
          }
          $alturaFila = 3 * $anchoCeldaMasGrande;

          //para cambiar de pagina en caso de faltar espacio
          if ($y + $alturaFila > $this->PageBreakTrigger) {
            $this->AddPage();
            $y = $this->GetY();
            $x = $this->GetX();
            $xActual = $x;
          }

          $ancho2 = 0;

          foreach ($clavesArreglo as $claveInd) {
            // Posición del cursor en la celda
            $this->SetXY($xActual, $y);
            // Imprimir el texto
            $textoFila = mb_convert_encoding($fila[$claveInd], 'ISO-8859-1', 'UTF-8');

            if ($anchoColumnas[$ancho2] * 2 < mb_strlen($textoFila)) {
              $this->SetFont("Arial", "B", "8");
            }
            $datosCalculo[] = [
              "Ancho" => $anchoColumnas[$ancho2],
              "texto" => $textoFila,
              "Nro caracteres" => mb_strlen($textoFila),
            ];
            $this->MultiCell($anchoColumnas[$ancho2], 3, $textoFila, 0, $alineaciones[$ancho2]);

            // Mover a la siguiente columna
            $xActual += $anchoColumnas[$ancho2];
            $ancho2++;
            $this->SetFont("Arial", "B", "8");
          }

          // Mover a la siguiente fila actualizando la posición Y
          $y += $alturaFila;
        }
        $yFinalProductos = $this->GetY() + $alturaFila - 6;
        $this->SetY($yFinalProductos);
      }

      $this->Cell(0, 5, '', 0, 1);
      //DATOS EXTRA DE ABAJO
      if ($datosExtraAbajo) {
        $this->SetFont("Arial", "B", "8");
        $CDEA = 0;
        foreach ($datosExtraAbajo['datosCeldas'] as $datoExt) {
          $this->Cell(0, 3, $datoExt, 0, 1, $datosExtraAbajo['alineaciones'][$CDEA]);
          $CDEA++;
        }
      }
      if (isset($seccion['QR'])) {
        $imagenTemporalQR = $this->crearCodigoQR($seccion['QR']['dataQR'], $seccion['QR']['tamaño']);
        $tituloQR = mb_convert_encoding($seccion['QR']['tituloQR'], 'ISO-8859-1', 'UTF-8');
        $this->Cell(0, 5, '', 0, 1);
        $this->Cell(0, 3, $tituloQR, 0, 1, 'C');
        $this->Image(
          $imagenTemporalQR,
          $this->GetX() + 16,
          $this->GetY(),
          20,
          20,
          'PNG',
          $seccion['QR']['dataQR']
        );
        $this->SetY($this->GetY() + 15);

        if (file_exists($imagenTemporalQR)) {
          unlink($imagenTemporalQR);
        }
      }
    }
    return $this;
  }
}
