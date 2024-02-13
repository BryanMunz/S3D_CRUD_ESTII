<?php
session_start();
require_once "../conexion.php";
if (!isset($_SESSION['idUser'])) {
    echo '<script>
                alert("Error, no ha iniciado sesión y no se puede redirigir a la página deseada.");
                window.location = "../index.php";
                </script>';
    exit;
}
$id_user = $_SESSION['idUser'];
$permiso = "historial movimientos";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header('Location: permisos.php');
}
$movimientoQuery = mysqli_query($conexion, "SELECT m.*, u.nombre, l.laboratorio FROM movimiento m INNER JOIN usuario u ON m.id_usuario = u.idusuario LEFT JOIN laboratorios l ON m.id_sur = l.id");

//Arreglo temporal que almacenará la información
$movimientos = array();
//Se verifica si hay un resultado
if (mysqli_num_rows($movimientoQuery) != 0) {
    while ($datos = mysqli_fetch_array($movimientoQuery, MYSQLI_ASSOC)) {
        $movimientos[] = $datos;
    }
}

include_once "includes/header.php";
?>
<div class="form-group">
<h3 class="m-0 text-dark">Historial Movimientos</h3>
</div>
<div class="card">
    <div class="card-header">
    </div>
    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-light" id="tbl">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Usuario</th>
                    <th>Nombre del Producto</th>
                    <th>Anterior</th>
                    <th>Diferencia</th>
                    <th>Actual</th>
                    <th>Movimiento</th>
                    <th>Sucursal</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
            <?php 
$html = '';
if (isset($movimientos) && sizeof($movimientos) != 0) {
    foreach ($movimientos as $movimiento) {
        // Calcula la diferencia entre Stock actual y Stock anterior
        $diferencia = $movimiento["stock_producto"] - $movimiento["stock_anterior"];

        if ($diferencia > 0) {
            $diferencia = '+' . $diferencia;
        }

        $html .= '
        <tr>
            <td>' . $movimiento['id_movimiento'] . '</td>
            <td>' . $movimiento['nombre'] . '</td>
            <td>' . $movimiento["producto"] . '</td>
            <td>' . $movimiento["stock_anterior"] . '</td>
            <td>' . $diferencia . '</td>
            <td>' . $movimiento["stock_producto"] . '</td>
            <td>' . $movimiento["tipo_mov"] . '</td>
            <td>' . $movimiento["laboratorio"] . '</td>
            <td>' . $movimiento["fecha_mov"] . '</td>
        </tr>
        ';
    }
}
echo $html;
?>

            </tbody>
        </table>
    </div>
    </div>
</div>
<?php include_once "includes/footer.php"; ?>