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
$permiso = "historial ventas";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header('Location: permisos.php');
}

$permisoboton = "consultar ganancias";
$sqlboton = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permisoboton'");
$existepermiso = mysqli_fetch_all($sqlboton);
if (empty($existepermiso) && $id_user != 1) {
    // El usuario no tiene los permisos necesarios, deshabilitar el botón
    $botonDeshabilitado = true;
} else {
    $botonDeshabilitado = false;
}

$permiso_eliminarventa = "eliminar venta";
$sql_eliminarventa = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso_eliminarventa'");
$permiso_eliminarventa = mysqli_fetch_all($sql_eliminarventa);
if (empty($permiso_eliminarventa) && $id_user != 1) {
    // El usuario no tiene los permisos necesarios, deshabilitar el botón
    $botonEliminarDeshabilitado = true;
} else {
    $botonEliminarDeshabilitado = false;
}

// Modificamos la consulta para incluir registros con campo cliente vacío
$query = mysqli_query($conexion, "SELECT v.*, c.idcliente, c.nombre FROM ventas v LEFT JOIN cliente c ON v.id_cliente = c.idcliente ORDER BY `v`.`id` DESC");

include_once "includes/header.php";

// Muestra ventas por sucursal
$consultaSucursal = mysqli_query($conexion, "(SELECT v.*, l.laboratorio AS nombre_sucursal
FROM ventas v
INNER JOIN detalle_venta d ON d.id_venta = v.id
INNER JOIN producto p ON d.id_producto = p.codproducto
INNER JOIN laboratorios l ON p.id_lab = l.id)
UNION
(SELECT v.*, l.laboratorio AS nombre_sucursal
FROM ventas v
INNER JOIN detalle_venta d ON d.id_venta = v.id
INNER JOIN laboratorios l ON d.id_lab_comun = l.id)
ORDER BY id DESC;
");
//$sucursal = mysqli_fetch_assoc($consultaSucursal);

?>
<div class="container">
    <!-- Botón para abrir la ventana modal -->
    <button class="btn btn-primary" id="open-modal" <?php if ($botonDeshabilitado) echo 'disabled'; ?>>Consultar Ganancias</button>


    <!-- Tabla de historial de ventas -->
    <div class="card">
    <div class="card-header">
        Historial ventas
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-light" id="tbl">
                <!-- Encabezados de la tabla -->
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Sucursal</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Fecha</th>
                        <th></th>
                    </tr>
                </thead>
                <!-- Filas de la tabla -->
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($query)) { ?>
                        <?php $sucursal = mysqli_fetch_assoc($consultaSucursal);?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo isset($sucursal['nombre_sucursal']) ? $sucursal['nombre_sucursal']: 'Sucursal no valida'; ?></td>
                            <td><?php echo isset($row['nombre']) ? $row['nombre'] : 'Sin cliente'; ?></td>
                            <td><?php echo '$' . number_format($row['total'], 2, '.', ','); ?></td>
                            <td><?php echo $row['fecha']; ?></td>
                            <td>
                            <a href="pdf/generar.php?cl=<?php echo $row['id_cliente'] ?>&v=<?php echo $row['id'] ?>" target="_blank" class="btn btn-danger"><i class="fas fa-file-pdf"></i></a>
                            <form action="eliminar_venta.php?id=<?php echo $row['id']; ?>" method="post" class="confirmar d-inline ">
                                <button class="btn btn-danger" type="submit" <?php if ($botonEliminarDeshabilitado) echo 'disabled'; ?>><i class='fas fa-trash-alt'></i> </button>
                            </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</div>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Seleccionar un día</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Formulario para seleccionar el día -->
                <form id="date-form">
                    <div class="form-group">
                        <label for="selected-date">Seleccione un día:</label>
                        <input type="date" id="selected-date" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Consultar</button>
                </form>

                <!-- Resultados dentro del modal -->
                <div id="modal-results" style="display: none;">
                    <p>Fecha seleccionada: <span id="modal-date"></span></p>
                    <p>Total vendido: <span id="total-venta"></span></p>
                    <p>Total vendido por Sucursal:</p>
                    <ul id="totalvendido-sucursal"></ul>
                    <p>Ganancias: <span id="ganancia"></span></p>
                    <p>Ganancias por Sucursal:</p>
                    <ul id="ganancias-sucursal"></ul>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>