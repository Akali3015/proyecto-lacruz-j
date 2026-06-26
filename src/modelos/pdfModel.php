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
  public array|bool $dataNotaEntrega = false;

  public function __construct($instrucciones = null) {
    $this->header = $instrucciones['header'] ?? true;
    $this->footer = $instrucciones['footer'] ?? true;
    $this->dataNotaEntrega = $instrucciones['datosNotaEntrega'] ?? false;
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
      default:
        $instrucciones['tamanoPagina'] = [216, 280];
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
    if ($this->dataNotaEntrega) {
      // Cuadro exterior superior (Opcional, para delimitar la zona superior si se desea)
      $this->Rect(10, 10, 190, 28);
      $this->Image($_SERVER['DOCUMENT_ROOT'] . '/proyecto-lacruz-j/src/assets/images/logo2.png', 12, 15, 23, 20);

      // Datos de la Empresa (Izquierda)
      $this->SetFont('Arial', '', 7);
      $this->SetTextColor(0);
      $this->SetXY(35, 12);
      $this->Cell(70, 3, 'J. LACRUZ C.A.', 0, 1);
      $this->SetX(35);
      $this->Cell(70, 3, 'MULTISERVICIOS GENERALES', 0, 1);
      $this->SetX(35);
      $this->Cell(70, 3, $this->CSE('Telefonos: +58 424-5085666 / 0414-5718890'), 0, 1);
      $this->SetX(35);
      $this->Cell(70, 3, $this->CSE('Dirección: Vda 21 Calle 6 Nro C-52'), 0, 1);
      $this->SetX(35);
      $this->Cell(70, 3, $this->CSE('Barrio José Gregorio Hernández'), 0, 1);
      $this->SetX(35);
      $this->Cell(70, 3, $this->CSE('Barquisimeto, Estado Lara'), 0, 1);
      $this->SetX(35);
      $this->Cell(70, 3, $this->CSE('Zona Postal 3001'), 0, 1);
      $this->SetX(35);
      $this->Cell(70, 3, $this->CSE('RIF: J-412192701'), 0, 1);

      // Bloque de la Nota de Entrega (Derecha)
      $this->SetXY(115, 11);
      $this->SetFont('Arial', 'B', 12);
      $this->Cell(80, 5, 'NOTA DE ENTREGA', 0, 1, 'C');
      $this->Ln(3);

      $this->SetFont('Arial', '', 7);
      $this->SetX(115);

      $this->Cell(40, 3.5, $this->CSE('Fecha Emisión:'), 0, 0);
      $this->Cell(40, 3.5, $this->FechaHora_Sel('fecha_hora_AM_PM', $this->dataNotaEntrega['fecha_orden']), 0, 1, 'R');
      $this->SetX(115);
      $this->Cell(40, 3.5, $this->CSE('Fecha Impresión:'), 0, 0);
      $this->Cell(40, 3.5, $this->FechaHora_Sel('Fecha_Hora_Actual'), 0, 1, 'R');

      $this->SetXY(115, 28);
      $this->Cell(35, 6, $this->CSE('Cod. Orden Entrega:'), 1, 0, 'C');
      $this->SetFont('Arial', 'B', 9);
      $this->Cell(45, 6, $this->dataNotaEntrega['id_orden_entrega_presupuesto'], 1, 1, 'C');

      $this->Ln(5);
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
  public function crearPDF(array $datosPdf) {
    $procesarSecciones = function ($datosPdf, $nroSeccion = null) {

      $configColumnas = $datosPdf['configColumnas'];
      $infoBD = $datosPdf['infoBD'];
      $this->tituloEncabezado = $this->CSE($datosPdf['tituloReporte'] ?? '');
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
            $this->Cell(0, 10, $this->CSE($dato), 0, 1, 'C');
          }
        }
      }

      //Dibujamos los encabezados
      $this->SetFont("Arial", "B", "12");
      foreach ($configColumnas as $configInd) {
        $this->Cell($configInd[1], 10, $this->CSE($configInd[0]), 1, 0, 'C');
      }
      $this->Ln();

      //Dibujamos las filas de datos
      $this->SetFont("Arial", "", "10");
      $alturaFila = 0;

      // Guardar la posición actual X y Y
      $x = $this->GetX();
      $y = $this->GetY();

      $clavesArreglo = array_keys($configColumnas);
      foreach ($infoBD as $fila) {
        $xActual = $x;

        // Calculamos el ancho de la fila
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
            $this->Cell($configInd[1], 10, $this->CSE($configInd[0]), 1, 0, 'C');
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
          $this->MultiCell($configColumnas[$claveInd][1], 5, $this->CSE($fila[$claveInd]), 0, 'C');
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
        $this->SetY($this->GetY() + ($alturaFila ?? 0) - 6);
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
  public function notaEntrega() {
    $this->SetMargins(10, 10, 10);
    $this->AddPage();

    $this->Rect(10, 43, 190, 15);
    $this->SetFont('Arial', '', 8);

    $itemPedido = $this->dataNotaEntrega ?: [];
    $cliente = $itemPedido['cliente'] ?? [];

    $this->SetXY(12, 45);
    $this->SetFont('Arial', 'B', 8);
    $this->Cell(20, 4, 'Cliente:', 0, 0,);
    $this->SetFont('Arial', '', 8);
    $this->Cell(60, 4, $this->CSE($cliente['razon_social_cliente'] ?? ''), 0, 1);

    $this->SetX(12);
    $this->SetFont('Arial', 'B', 8);
    $this->Cell(20, 4, $this->CSE('RIF/CÉDULA:'), 0, 0);
    $this->SetFont('Arial', '', 8);
    $this->Cell(40, 4, $this->CSE($cliente['rif_cedula_cliente'] ?? ''), 0, 0);

    $this->SetFont('Arial', 'B', 8);
    $this->Cell(20, 4, $this->CSE('Teléfono:'), 0, 0);
    $this->SetFont('Arial', '', 8);
    $this->Cell(80, 4, $this->CSE($cliente['telefono_cliente'] ?? ''), 0, 1);

    $this->SetX(12);
    $this->SetFont('Arial', 'B', 8);
    $this->Cell(20, 4, $this->CSE('Dirección:'), 0, 0);
    $this->SetFont('Arial', '', 8);
    $this->Cell(100, 4, $this->CSE($cliente['direccion_cliente'] ?? ''), 0, 1);

    $this->SetY(60);
    $this->Cell(30, 5, $this->CSE('Condición de Pago:'), 'B', 0);
    $this->SetFont('Arial', 'B', 8);
    $this->Cell(70, 5, $this->CSE('CANCELADO'), 'B', 0);

    $this->SetFont('Arial', 'B', 8);
    $this->Cell(15, 5, 'Asesor:', 'B', 0);
    $this->SetFont('Arial', '', 8);
    $this->Cell(75, 5,  $this->CSE($cliente['vendedor'] ?? 'NO ATENDIDO'), 'B', 1);

    $this->Ln(4);

    $fnBolivares = function ($valor) {
      $d = (float)$this->dataNotaEntrega['calculos']['dolar']['valor_fecha_moneda'];
      return round((((float) $valor) * $d), 2);
      
    };

    //Encabezados
    $anchosE = array(30, 65, 20, 20, 15, 15, 25);
    $encabezados = array('Código', 'Descripción', 'Cantidad', 'Precio Unit.', 'Des', 'I.V.A', 'Total');
    $this->SetFont('Arial', 'B', 8);
    for ($i = 0; $i < count($encabezados); $i++) {
      $this->Cell($anchosE[$i], 5,  $this->CSE($encabezados[$i]), 'B', 0, 'C');
    }
    $this->Ln();

    $this->SetFont('Arial', '', 7.5);
    foreach (($itemPedido['productos'] ?? []) as $producto) {
      $this->Cell(30, 5, $this->CSE($producto['id_presentacion_producto']), 0, 0, 'C');
      $this->Cell(65, 5, $this->CSE($producto['nombre_producto'] . ' - ' . $producto['nombre_presentacion']), 0, 0, 'C');
      $this->Cell(20, 5, $this->CSE($producto['cantidad_producto']), 0, 0, 'C');
      $this->Cell(20, 5, $this->CSE($fnBolivares($producto['precio_producto_factura'])) . ' Bs', 0, 0, 'C');
      $this->Cell(15, 5, $this->CSE($fnBolivares($producto['descuento'])) . ' Bs', 0, 0, 'C');
      $this->Cell(15, 5, $this->CSE($itemPedido['calculos']['porcentaje_IVA'] ?? 'C') . '%', 0, 0, 'C');
      $this->Cell(25, 5, $this->CSE($fnBolivares($producto['subtotal_factura'])) . ' Bs', 0, 1, 'R');
    }

    //Delivery
    $this->Cell(30, 5, '--------', 0, 0, 'C');
    $this->Cell(65, 5, $this->CSE('ENVÍO'), 0, 0, 'C');
    $this->Cell(20, 5, '1.00', 0, 0, 'C');
    $this->Cell(20, 5, $this->CSE($fnBolivares($this->dataNotaEntrega['calculos']['totalEnvio'])).' Bs', 0, 0, 'C');
    $this->Cell(15, 5, '0.00 Bs', 0, 0, 'C');
    $this->Cell(15, 5, $this->CSE($itemPedido['calculos']['porcentaje_IVA'] ?? 'C') . '%', 0, 0, 'C');
    $this->Cell(25, 5, $this->CSE($fnBolivares($this->dataNotaEntrega['calculos']['totalEnvio'])).' Bs', 0, 1, 'R');

    $this->SetY(225);
    $this->Rect(10, 225, 105, 25);
    $this->SetXY(12, 227);
    $this->SetFont('Arial', 'B', 8);
    $this->Cell(30, 4, 'Observaciones:', 0, 1);

    $this->Rect(120, 225, 80, 25);
    $this->SetXY(122, 226);
    $this->SetFont('Arial', '', 8);

    $this->Cell(40, 4.5, 'Subtotal:', 0, 0);

    $this->Cell(36, 4.5, $fnBolivares($this->dataNotaEntrega['calculos']['total']).' Bs', 0, 1, 'R');

    $this->SetX(122);
    $this->Cell(20, 4.5, 'Descuentos:', 0, 0);
    $this->Cell(56, 4.5, $fnBolivares($this->dataNotaEntrega['calculos']['totalDescuento']).' Bs', 0, 1, 'R');

    $this->SetX(122);
    $this->Cell(20, 4.5, 'I.V.A ('.$this->dataNotaEntrega['calculos']['porcentaje_IVA'].'%):', 0, 0);
    $this->Cell(56, 4.5, $fnBolivares($this->dataNotaEntrega['calculos']['monto_IVA']).' Bs', 0, 1, 'R');

    $this->Line(120, 240, 200, 240);

    $this->SetXY(122, 245)
    ;
    $this->SetFont('Arial', 'B', 9);
    $this->Cell(40, 5, 'TOTAL:', 0, 0);
    $this->Cell(36, 5, $fnBolivares($this->dataNotaEntrega['calculos']['total_IVA']).' Bs', 0, 1, 'R');

    return $this;
  }
  public function CSE(string $string) {
    return mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8');
  }
}
