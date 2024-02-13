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
$pdf = new FPDF('P', 'mm', array(80, 260));
$pdf->AddPage();
$pdf->SetMargins(5, 0, 0);
$pdf->SetTitle("Ventas");
$pdf->SetFont('Arial', 'B', 12);
$id = $_GET['v'];
$idcliente = $_GET['cl'];
$config = mysqli_query($conexion, "SELECT * FROM configuracion");
$datos = mysqli_fetch_assoc($config);
$clientes = mysqli_query($conexion, "SELECT * FROM cliente WHERE idcliente = $idcliente");
$datosC = mysqli_fetch_assoc($clientes);
$ventas = mysqli_query($conexion, "SELECT d.*, COALESCE(p.descripcion, d.producto_c) as producto_descripcion, p.codproducto FROM detalle_venta d LEFT JOIN producto p ON d.id_producto = p.codproducto WHERE d.id_venta = $id");
$ventas_idproducto = mysqli_query($conexion, "SELECT d.*, p.codproducto FROM detalle_venta d LEFT JOIN producto p ON d.id_producto = p.codproducto WHERE d.id_venta = $id");
$nombreQuery = "SELECT v.*, u.nombre FROM ventas v INNER JOIN usuario u ON v.id_usuario = u.idusuario WHERE v.id = $id";
$resultadoNombre = mysqli_query($conexion, $nombreQuery);
$nombreUsuario = mysqli_fetch_assoc($resultadoNombre)['nombre'];
$id_producto = mysqli_fetch_assoc($ventas_idproducto)['codproducto'];
$fecha = mysqli_query($conexion, "SELECT fecha FROM ventas WHERE id = $id");
$fechaventa = mysqli_fetch_assoc($fecha)['fecha'];

$clienteQuery = "SELECT v.*, c.nombre FROM ventas v INNER JOIN cliente c ON v.id_cliente = c.idcliente WHERE v.id = $id";
$resultadoNombreCliente = mysqli_query($conexion, $clienteQuery);
$clienteData = mysqli_fetch_assoc($resultadoNombreCliente);
if ($clienteData) {
    $cliente = $clienteData['nombre'];
} else {
    $cliente = "XXXXXXXXX ";
}

$consultaSucursal = mysqli_query($conexion, "SELECT l.direccion AS nombre_sucursal FROM laboratorios l 
INNER JOIN producto p ON l.id = p.id_lab 
INNER JOIN detalle_venta d ON d.id_producto = p.codproducto 
INNER JOIN ventas v ON d.id_venta = v.id WHERE d.id_venta = $id");

$consultaSucursal1 = mysqli_query($conexion, "SELECT l.direccion AS nombre_sucursal FROM laboratorios l 
INNER JOIN detalle_venta d ON d.id_lab_comun = l.id INNER JOIN ventas v ON d.id_venta = v.id WHERE d.id_venta = $id");


$pdf->Cell(65, 13, utf8_decode(""), 0, 1, 'C');
$pdf->image("../../assets/img/sutec3d_recortado.jpeg",15, 1, 50, 20, 'JPEG');
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->Cell(15, 5, utf8_decode("RFC: "), 0, 0, 'L');
$pdf->SetFont('Arial', '', 8.5);
$pdf->Cell(15, 5, utf8_decode("GASG950303LV4"), 0, 1, 'L');
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->Cell(15, 5, utf8_decode("Teléfono: "), 0, 0, 'L');
$pdf->SetFont('Arial', '', 8.5);
$pdf->Cell(15, 5, $datos['telefono'], 0, 1, 'L');
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->Cell(15, 5, utf8_decode("Dirección: "), 0, 0, 'L');
$pdf->SetFont('Arial', '', 8.5);
if ($id_producto == 0) {
    $sucursal = mysqli_fetch_assoc($consultaSucursal1)['nombre_sucursal'];
    $pdf->MultiCell(50, 5, utf8_decode($sucursal), 0, 'L');
} else {
    $sucursal = mysqli_fetch_assoc($consultaSucursal)['nombre_sucursal'];
    $pdf->MultiCell(50, 5, utf8_decode($sucursal), 0, 'L');
}
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->Cell(15, 5, "Correo: ", 0, 0, 'L');
$pdf->SetFont('Arial', '', 8.5);
$pdf->Cell(15, 5, utf8_decode($datos['email']), 0, 1, 'L');
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->Cell(15, 5, "Cajero: ", 0, 0, 'L');
$pdf->SetFont('Arial', '', 8.5);
$pdf->Cell(15, 5, utf8_decode($nombreUsuario), 0, 1, 'L');
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->Cell(15, 5, "Cliente: ", 0, 0, 'L');
$pdf->SetFont('Arial', '', 8.5);
$pdf->Cell(15, 5, utf8_decode($cliente), 0, 1, 'L');
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->Cell(15, 5, "Fecha: ", 0, 0, 'L');
$pdf->SetFont('Arial', '', 8.5);
$pdf->Cell(15, 5, utf8_decode($fechaventa), 0, 1, 'L');
$pdf->Ln();

// Verificar si se obtuvieron datos del cliente
if (!empty($datosC)) {
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetFillColor(0, 0, 0);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(70, 5, "Datos del cliente", 1, 1, 'C', 1);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(30, 5, utf8_decode('Nombre'), 0, 0, 'L');
    $pdf->Cell(20, 5, utf8_decode('Teléfono'), 0, 0, 'L');
    $pdf->Cell(20, 5, utf8_decode('Dirección'), 0, 1, 'L');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(30, 5, utf8_decode($datosC['nombre']), 0, 0, 'L');
    $pdf->Cell(20, 5, utf8_decode($datosC['telefono']), 0, 0, 'L');
    $pdf->Cell(20, 5, utf8_decode($datosC['direccion']), 0, 1, 'L');
    $pdf->Ln(3);
}

$pdf->SetFont('Arial', 'B', 8);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(70, 5, "Detalle de Producto", 1, 1, 'C', 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(30, 5, utf8_decode('Descripción'), 0, 0, 'L');
$pdf->Cell(10, 5, 'Cant.', 0, 0, 'L');
$pdf->Cell(15, 5, 'Precio', 0, 0, 'L');
$pdf->Cell(15, 5, 'Sub Total.', 0, 1, 'L');
$pdf->SetFont('Arial', '', 7);
$total = 0.00;
$desc = 0.00;
$pago1 = 0.00;
$pago = 0.00;
$cambio = 0.00;

$Y= 68;
while ($row = mysqli_fetch_assoc($ventas)) {
    $pdf->SetFont('Arial', '', 7);
    $pdf->MultiCell(32, 5, $row['producto_descripcion'], 0, 'L');

    $H = $pdf->GetY();
    $height= ($H-$Y) + 1;

    $pdf->SetFont('Arial', '', 7);
    $pdf->SetXY(35,$Y);
    $pdf->Cell(10, $height, $row['cantidad'], 0, 0, 'C');

    $Y = $pdf->getY();
    $pdf->SetXY(45,$Y);
    $pdf->Cell(15, $height, '$' . $row['precio'], 0, 0, 'L'); 

    $sub_total = $row['total'];
    $total = $total + $sub_total;
    $desc = $desc + $row['descuento'];
    $pdf->SetXY(60,$Y);
    $pdf->Cell(15, $height, '$' . number_format($sub_total, 2, '.', ','), 0, 1, 'L');

    $Y=$H;
}

$CambioQuery = "SELECT * FROM ventas WHERE id = $id";
$CambioResult = mysqli_query($conexion, $CambioQuery);
while ($row = mysqli_fetch_assoc($CambioResult)) {
    $pago1 = $pago1 + $row['cambio'];
    $pago = $pago + ($pago1 + $total);
    
    if($pago > 0){
        $cambio = $cambio + ($pago - $total);
    }else{
        $cambio = $cambio;
    }
}

$descuentoVentaQuery = "SELECT d.*, p.codproducto, p.p_venta, CASE WHEN d.id_producto = 0 THEN (d.cantidad * d.precio) ELSE (d.cantidad * p.p_venta) 
    END AS Total FROM detalle_venta d LEFT JOIN producto p ON d.id_producto = p.codproducto WHERE d.id_venta = $id";
$descuentoResult = mysqli_query($conexion, $descuentoVentaQuery);

$totalVenta = 0;

while ($row = mysqli_fetch_assoc($descuentoResult)) {
    $total_nor = $row['Total'];
    $totalVenta += $total_nor;
}

$CtotalQuery = "SELECT SUM(CASE WHEN d.precio <> p.p_venta THEN (p.p_venta - d.precio) * d.cantidad ELSE 0 END) AS descuento_total 
                FROM detalle_venta d 
                LEFT JOIN producto p ON d.id_producto = p.codproducto 
                WHERE d.id_venta = $id";

$CtotalResult = mysqli_query($conexion, $CtotalQuery);
$totalVenta = 0;
while ($row = mysqli_fetch_assoc($CtotalResult)) {
    $total_norm = $row['descuento_total'];
    $totalVenta += $total_norm;
}

$pdf->Ln();
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(65, 5, 'Descuento Total', 0, 1, 'R');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(65, 5, '$' . number_format($totalVenta, 2, '.', ','), 0, 1, 'R');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(65, 5, 'Total Pagar', 0, 1, 'R');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(65, 5, '$' .number_format($total, 2, '.', ','), 0, 1, 'R');
$pdf->SetFont('Arial', '', 9.5);
$pdf->Cell(65, 5, 'Pago con: $' . number_format($pago, 2, '.', ','), 0, 1, 'R');
$pdf->Cell(65, 5, 'Su cambio: $' . number_format($cambio, 2, '.', ','), 0, 1, 'R');

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(0, 10, "", 0, 1, 'C');
$pdf->Cell(0, 5, utf8_decode("¡INFORMACIÓN IMPORTANTE!"), 0, 1, 'C');
$pdf->SetFont('Arial', '', 9);
$pdf->MultiCell(70, 3.5, utf8_decode("*En componentes Electronicos no hay garantia, todos nuestros productos son probados antes de su venta."), 0, 'J');
$pdf->Cell(0, 2, "", 0, 1, 'C');
$pdf->MultiCell(70, 3.5, utf8_decode("*Favor de verificar su mercancia antes de salir del establecimiento."), 0, 'J');
$pdf->Cell(0, 2, "", 0, 1, 'C');
$pdf->MultiCell(70, 3.5, utf8_decode("*Para Garantia en productos de Marca presentar producto y empaque en perfectas condiciones asi como el ticket de compra."), 0, 'J');
$pdf->Cell(0, 2, "", 0, 1, 'C');
$pdf->MultiCell(70, 3.5, utf8_decode("*El tiempo de respuesta de la garantia del producto puede variar dependiendo el fabricante."), 0, 'J');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 2, "", 0, 1, 'C');
$pdf->MultiCell(70, 3.5, utf8_decode("*Para facturacion hasta 10 dias posteriores a su compra y solicitarla via whatsapp."), 0, 'L');
$pdf->Cell(0, 2, "", 0, 1, 'C');
$pdf->MultiCell(70, 3.5, utf8_decode("*No hay cambios ni devoluciones."), 0, 'L');
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(0, 3, "", 0, 1, 'C');
$pdf->Cell(0, 5, utf8_decode("¡GRACIAS POR SU COMPRA!"), 0, 1, 'C');


$pdf->Output("Ventas.pdf", "I");

?>