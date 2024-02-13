<?php
session_start();
require_once "../../conexion.php";
if (!isset($_SESSION['idUser'])) {
    echo '<script>
                alert("Error, no ha iniciado sesión y no se puede redirigir a la página deseada.");
                window.location = "../index.php";
                </script>';
    exit;
}
$id_user = $_SESSION['idUser'];
require_once 'fpdf/fpdf.php';
$pdf = new FPDF('L', 'mm',array(210,270));
$pdf->AddPage();

$pdf->SetTitle("computadoras");
$pdf->SetFont('Arial', 'B', 16);

$config = mysqli_query($conexion, "SELECT * FROM configuracion");
$datos = mysqli_fetch_assoc($config);


$Compus = mysqli_query($conexion, "SELECT * FROM computadoras ORDER BY id DESC LIMIT 1");
$datosCompus = mysqli_fetch_assoc($Compus);


$pdf->SetX(140);
$pdf->Cell(125, 5, utf8_decode($datos['nombre']), 0, 1, 'C');
$pdf->image("../../assets/img/Icono.jpg", 243, 6, 20, 20, 'JPG');
$pdf->image("../../assets/img/sutec3d.jpg", 140, -5, 29, 45, 'JPG');



$pdf->SetFont('Arial', '', 7);
$pdf->SetX(140);
$pdf->Cell(125, 3.5, utf8_decode("RFC: GASG950303LV4 "), 0, 1, 'C');

$pdf->SetFont('Arial', '', 7);
$pdf->SetX(140);
$pdf->Cell(125, 3.5, utf8_decode($datos['direccion']), 0, 1, 'C');

$pdf->SetFont('Arial', '', 7);
$pdf->SetX(140);
$pdf->Cell(125, 3.5, utf8_decode($datos['telefono']), 0, 1, 'C');


$pdf->SetFont('Arial', '', 7);
$pdf->SetX(140);
$pdf->Cell(125, 3.5, utf8_decode($datos['email']), 0, 2, 'C');



$pdf->SetFillColor(0,166,211,255);
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetX(140);
$pdf->Cell(125, 10, "HOJA DE SERVICIO", 1, 1, 'C', 1);

$pdf->SetFillColor(200, 200, 200);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', 'B', 7);
$pdf->SetX(140);
$pdf->Cell(62.5, 4, utf8_decode("Datos del cliente"), 1, 0, 'C', 1);
$pdf->SetFont('Arial', 'B', 7);
$pdf->SetX(202.5);
$pdf->Cell(26, 4, utf8_decode("Fecha de ingreso"), 1, 0, 'C', 1);
$pdf->SetFont('Arial', '', 7);
$pdf->SetX(228.5);
$pdf->Cell(36.5, 4, utf8_decode($datosCompus['fechaingreso']), 1, 1, 'C');

$pdf->SetFont('Arial', 'B', 7);
$pdf->SetX(140);
$pdf->Cell(26, 4, utf8_decode("Nombre"), 1, 0, 'C', 1);
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(36.5, 4, utf8_decode($datosCompus['nombre']), 1, 0, 'C');
$pdf->SetFont('Arial', 'B', 7);
$pdf->SetX(202.5);
$pdf->Cell(26, 4, utf8_decode("Folio"), 1, 0, 'C', 1);
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(36.5, 4, utf8_decode($datosCompus['folio']), 1, 1, 'C');
$pdf->SetFont('Arial', 'B', 7);
$pdf->SetX(140);
$pdf->Cell(26, 4, utf8_decode("Celular"), 1, 0, 'C', 1);
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(36.5, 4, utf8_decode($datosCompus['telefono']), 1, 0, 'C');
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(26, 4, utf8_decode("Costo de revision"), 1, 0, 'C', 1);
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(36.5, 4, utf8_decode($datosCompus['costoR']), 1, 1, 'C');
$pdf->SetFont('Arial', 'B', 7);
$pdf->SetX(140);
$pdf->Cell(26, 4, utf8_decode("Direccion"), 1, 0, 'C', 1);
$pdf->SetFont('Arial', '', 7);
$pdf->SetX(166);
$pdf->Cell(36.5, 4, utf8_decode($datosCompus['direccion']), 1, 0, 'C');
$pdf->SetFillColor(200, 200, 200);
$pdf->SetFont('Arial', 'B', 7);
$pdf->SetX(202.5);
$pdf->Cell(26, 4, utf8_decode("Personal"), 1, 0, 'C', 1);
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(36.5, 4, utf8_decode($datosCompus['recibe']), 1, 1, 'C');

$pdf->SetFillColor(0,166,211,255);
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetX(140);
$pdf->Cell(125, 6, "DATOS DEL EQUIPO", 1, 1, 'C', 1);

$pdf->SetFillColor(200, 200, 200);
$pdf->SetTextColor(0,0,0 );
$pdf->SetFont('Arial', 'B', 7);
$pdf->SetX(140);
$pdf->Cell(31, 3.5, utf8_decode("No de piezas"), 1, 0, 'C',1);
$pdf->Cell(46, 3.5, utf8_decode("Marca y modelo"), 1, 0, 'C',1);
$pdf->Cell(48, 3.5, utf8_decode("No de serie"), 1, 1, 'C',1);

$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0,0,0 );

//$y = 95;
$pdf->SetFont('Arial', '', 7);

// Obtener el contenido de las variables
$pdf->SetX(140);
$piezas = utf8_decode($datosCompus['piezas']);
$modelo = utf8_decode($datosCompus['modelo']);
$serir = utf8_decode($datosCompus['serir']);

// Dividir el contenido en líneas
$piezasLines = explode("\n", $piezas);
$modeloLines = explode("\n", $modelo);
$serirLines = explode("\n", $serir);

// Calcular la altura necesaria para cada celda basada en el número de líneas
$alturaMaxima = max(
  count($piezasLines),
  count($modeloLines),
  count($serirLines)
);

$alturaMaximaH1 =
count($piezasLines);

$alturaMaximaH2 =
count($modeloLines);

$alturaMaximaH3 =
count($serirLines);

$alturaCelda = 5; // Altura deseada para todas las celdas
$alturaNecesaria = $alturaCelda * $alturaMaxima;

// Generar las celdas MultiCell con la altura máxima
// Código para la celda $piezas
// Sorry por el código espagueti, es lo que había :c
if (count($piezasLines) == 1 && count($modeloLines) == 1 && count($serirLines) == 1){
$pdf->MultiCell(31, $alturaNecesaria, $piezas, 1, 'C', 0);
}
else if (count($piezasLines) ===3 && count($modeloLines) === 2 && count($serirLines) === 2){
$pdf->MultiCell(31, $alturaNecesaria - 10, $piezas, 1, 'C', 0);
}
else if (count($piezasLines) ===2 && count($modeloLines) === 3 && count($serirLines) <= 2){
$pdf->MultiCell(31, $alturaNecesaria - 7.5, $piezas, 1, 'C', 0);
}
else if (count($piezasLines) ===3 && count($modeloLines) === 3 && count($serirLines) <= 2){
$pdf->MultiCell(31, $alturaNecesaria - 10, $piezas, 1, 'C', 0);
}
else if (count($piezasLines) > 2 && count($modeloLines) > 2 && count($serirLines) > 2){
$pdf->MultiCell(31, ($alturaNecesaria - $alturaCelda) - $alturaCelda, $piezas, 1, 'C', 0);
}
else if ($alturaMaximaH1 == $alturaMaximaH2 && $alturaMaximaH1 == $alturaMaximaH3){
$pdf->MultiCell(31, $alturaNecesaria - $alturaCelda, $piezas, 1, 'C', 0);
}
else if (count($piezasLines) === 2 && count($modeloLines) === 2 && count($serirLines) <2){
$pdf->MultiCell(31, $alturaNecesaria - $alturaCelda, $piezas, 1, 'C', 0);
}
else if ($alturaMaximaH1 < $alturaMaximaH2){
$pdf->MultiCell(31, $alturaNecesaria, $piezas, 1, 'C', 0);
} 
else if ($alturaMaximaH2 < $alturaMaximaH3 && $alturaMaximaH1 != $alturaMaximaH3){
$pdf->MultiCell(31, $alturaNecesaria, $piezas, 1, 'C', 0);
} 
else if ($alturaMaximaH3 >= $alturaMaximaH2){
$pdf->MultiCell(31, $alturaNecesaria - $alturaCelda, $piezas, 1, 'C', 0);
} 
else if($alturaMaximaH3 < $alturaMaximaH2){
$pdf->MultiCell(31, $alturaNecesaria, $piezas, 1, 'C', 0);
}
else if (count($piezasLines) === 2 && count($modeloLines) ===3 && count($serirLines) <=3){
$pdf->MultiCell(31, ($alturaNecesaria - $alturaCelda) - 2.5, $piezas, 1, 'C', 0);
}
else if (count($piezasLines) === 3 && count($modeloLines) ===2 && count($serirLines) === 3){
$pdf->MultiCell(31, $alturaNecesaria - 10, $piezas, 1, 'C', 0);
}
else if (count($piezasLines) === 1 && count($modeloLines) ===2 && count($serirLines) === 3){
$pdf->MultiCell(31, $alturaNecesaria , $piezas, 1, 'C', 0);
}
else if (count($piezasLines) === 3 && count($modeloLines) ===3 && count($serirLines) ===3){
$pdf->MultiCell(31, ($alturaNecesaria - $alturaCelda) - $alturaCelda, $piezas, 1, 'C', 0);
}
else if (count($piezasLines) ===3 && count($modeloLines) <3 && count($serirLines) ===3){
$pdf->MultiCell(31, $alturaNecesaria - 7.5, $piezas, 1, 'C', 0);
}
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetXY($x + 161, $y - $alturaNecesaria);

// Código para la celda $modelo
if (count($piezasLines) == 1 && count($modeloLines) == 1 && count($serirLines) == 1){
$pdf->MultiCell(46, $alturaNecesaria, $modelo, 1, 'C', 0);
}
else if (count($piezasLines) > 2 && count($modeloLines) > 2 && count($serirLines) > 2){
$pdf->MultiCell(46, ($alturaNecesaria - $alturaCelda) - $alturaCelda, $modelo, 1, 'C', 0);
}
else if (count($piezasLines) <=3 && count($modeloLines) >2 && count($serirLines) <=3){
$pdf->MultiCell(46, ($alturaNecesaria - $alturaCelda) - $alturaCelda, $modelo, 1, 'C', 0);
}
else if (count($piezasLines) <3 && count($modeloLines) >2 && count($serirLines) <3){
$pdf->MultiCell(46, ($alturaNecesaria - $alturaCelda) - $alturaCelda, $modelo, 1, 'C', 0);
}
else if (count($piezasLines) <= 3 && count($modeloLines) ===2 && count($serirLines) === 3){
$pdf->MultiCell(46, ($alturaNecesaria - $alturaCelda) - 2.5, $modelo, 1, 'C', 0);
}
else if (count($piezasLines) === 3 && count($modeloLines) <3 && count($serirLines) <3){
$pdf->MultiCell(46, ($alturaNecesaria - 7.5), $modelo, 1, 'C', 0);
}
else if (count($piezasLines) === 2 && count($modeloLines) === 2 && count($serirLines) <2){
$pdf->MultiCell(46, $alturaNecesaria - $alturaCelda, $modelo, 1, 'C', 0);
}
else if ($alturaMaximaH1 == $alturaMaximaH2 && $alturaMaximaH1 == $alturaMaximaH3){
$pdf->MultiCell(46, $alturaNecesaria - $alturaCelda, $modelo, 1, 'C', 0);
}
else if ($alturaMaximaH2 > $alturaMaximaH1){
$pdf->MultiCell(46, $alturaNecesaria - $alturaCelda, $modelo, 1, 'C', 0);
} 
else if ($alturaMaximaH2 < $alturaMaximaH3 && $alturaMaximaH2 == $alturaMaximaH1){
$pdf->MultiCell(46, $alturaNecesaria - $alturaCelda, $modelo, 1, 'C', 0);
}
else {
$pdf->MultiCell(46, $alturaNecesaria, $modelo, 1, 'C', 0);
}
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetXY($x + 207, $y - $alturaNecesaria);

// Código para la celda $serir
if (count($piezasLines) == 1 && count($modeloLines) == 1 && count($serirLines) == 1){
$pdf->MultiCell(48, $alturaNecesaria, $serir, 1, 'C', 0);
}
else if (count($piezasLines) > 2 && count($modeloLines) > 2 && count($serirLines) > 2){
$pdf->MultiCell(48, ($alturaNecesaria - $alturaCelda) - $alturaCelda, $serir, 1, 'C', 0);
}
else if (count($piezasLines) <2 && count($modeloLines) === 3 && count($serirLines) === 3){
$pdf->MultiCell(48, $alturaNecesaria - 10, $serir, 1, 'C', 0);
}
else if ($alturaMaximaH1 == $alturaMaximaH2 && $alturaMaximaH1 == $alturaMaximaH3){
$pdf->MultiCell(48, $alturaNecesaria - $alturaCelda, $serir, 1, 'C', 0);
}
else if($alturaMaximaH3 > $alturaMaximaH2){
$pdf->MultiCell(48, $alturaNecesaria - $alturaCelda, $serir, 1, 'C', 1);
}
else if($alturaMaximaH2 == $alturaMaximaH3 && $alturaMaximaH1 < $alturaMaximaH2){
$pdf->MultiCell(48, $alturaNecesaria - $alturaCelda, $serir, 1, 'C', 1);
} 
else if (count($piezasLines) <3 && count($modeloLines) >2 && count($serirLines) === 2){
$pdf->MultiCell(48, ($alturaNecesaria - $alturaCelda) - 2.5, $serir, 1, 'C', 0);
}
else if (count($piezasLines) <=3 && count($modeloLines) >2 && count($serirLines) === 1){
$pdf->MultiCell(48, $alturaNecesaria, $serir, 1, 'C', 0);
}
else if (count($piezasLines) ===3 && count($modeloLines) >2 && count($serirLines) === 2){
$pdf->MultiCell(48,  $alturaCelda + 2.5, $serir, 1, 'C', 0);
}
else if (count($piezasLines) <=3 && count($modeloLines) >2 && count($serirLines) <=3){
$pdf->MultiCell(48, ($alturaNecesaria - $alturaCelda) - $alturaCelda, $serir, 1, 'C', 0);
}
else if (count($piezasLines) ===3 && count($modeloLines) <3 && count($serirLines) ===3){
$pdf->MultiCell(48, ($alturaNecesaria - $alturaCelda) - $alturaCelda, $serir, 1, 'C', 0);
}
else if (count($piezasLines) ===3 && count($modeloLines) === 2 && count($serirLines) === 2){
$pdf->MultiCell(48, $alturaNecesaria - 7.5, $serir, 1, 'C', 0);
}
else {
$pdf->MultiCell(48, $alturaNecesaria , $serir, 1, 'C', 1);
}

// Restablecer la posición Y después de las celdas
$pdf->SetY($y);


function addBulletPoint($text) {
return preg_replace('/^/m', '- ', $text);
}

// Restablecer la posición Y después de las celdas
$pdf->SetX(140);
$descripcion = utf8_decode($datosCompus['descripcion']);
$descripcion = addBulletPoint($descripcion);

$pdf->MultiCell(125, 7, $descripcion, 1, 'C', false);
$pdf->SetFont('Arial', 'B', 7);
$pdf->SetX(140);
$pdf->Cell(125, 49, utf8_decode(""), 1, 1, 'C',1);
$pdf->SetX(140);
$pdf->Cell(125, -90, utf8_decode("¡INFORMACIÓN IMPORTANTE¡"), 0, 1, 'C');
$pdf->SetX(140);
$pdf->Cell(125, 98, utf8_decode("PARA DAR SEGIMIENTO A SU SERVICIO DIRECTO A:"), 0, 1, 'C');
$pdf->SetX(140);
$pdf->Cell(125, -90, utf8_decode("sutec.st@gmail.com/ 22-27-10-65-10"), 0, 1, 'C');
$pdf->SetFont('Arial', '', 7);
$pdf->SetX(140);
$pdf->Cell(125, 98, utf8_decode("*Es indispensable presentar este formato para recoger su equipo de lo contrario no se entregara"), 0, 1, 'C');
$pdf->SetX(140);
$pdf->Cell(125, -90, utf8_decode("*En caso de perdida del formato tendra un costo de $50.00 además de presentar una identificacion "), 0, 1, 'C');
$pdf->SetX(140);
$pdf->Cell(125, 98, utf8_decode("*El tiempo estimado de su diagnostico es de 2hrs hasta 24hrs despues de ser ingresado"), 0, 1, 'C');
$pdf->SetX(140);
$pdf->Cell(125, -90, utf8_decode("*La empresa no se hace responsable de su equipo habiendo transcurrido 15 dias despues de su resolucion"), 0, 1, 'C');
$pdf->SetX(140);
$pdf->Cell(125, 98, utf8_decode("*La empresa no se hace responsable por fallos o errores extras que se llegasen a presentar en su equipo"), 0, 1, 'C');
$pdf->SetX(140);
$pdf->Cell(125, -90, utf8_decode("*Se solicita estar pendiente de las llamadas o de su whatsApp"), 0, 1, 'C');
$pdf->SetX(140);
$pdf->Cell(125, 98, utf8_decode("*Si el sello de garantia viene violado no podra hacerse valida la misma"), 0, 1, 'C');
$pdf->SetFont('Arial', '', 6.5);
$pdf->SetX(140);
$pdf->Cell(125, -90, utf8_decode("*Para validar la garantía, presenta los empaques, el equipo donde se instaló el componente y la nota de servicio o venta"), 0, 1, 'C');
$pdf->SetX(140);
$pdf->Cell(193, 75, utf8_decode(""), 0, 1, 'C');
$pdf->SetX($pdf->GetX() + 147);
$pdf->Cell(90, 0, utf8_decode(""), 1, 1, 'C');
$pdf->SetX(140);
$pdf->Cell(125, 4, utf8_decode("NOMBRE Y FIRMA DEL CLIENTE"), 0, 1, 'C');


$pdf->Output("computadoras.pdf", "I");

?>