<?php
session_start();
require("../conexion.php");
$id_user = $_SESSION['idUser'];
$permiso = "eliminar venta";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header("Location: permisos.php");
}
if (!empty($_GET['id'])) {
    $id = $_GET['id'];
    $sqlproducto = mysqli_query($conexion, "SELECT id_producto, cantidad FROM detalle_venta d  WHERE d.id_venta = $id");
    
    $updates = array(); // Arreglo para almacenar las actualizaciones

    while ($row = mysqli_fetch_array($sqlproducto)) {
        $stock = $row['cantidad'];
        $id_producto = $row['id_producto'];
        
        if($id_producto > 0){
        // Agregar una actualización al arreglo
        $updates[] = "UPDATE producto SET existencia = existencia + $stock WHERE codproducto = $id_producto";
        }
    }

    // Ejecutar todas las actualizaciones en una transacción
    mysqli_begin_transaction($conexion);

    foreach ($updates as $update_query) {
        $actualizar = mysqli_query($conexion, $update_query);
        if (!$actualizar) {
            // Si alguna actualización falla, revertir la transacción y mostrar un error
            mysqli_rollback($conexion);
            die("Error al actualizar la base de datos: " . mysqli_error($conexion));
        }
    }

    // Si todas las actualizaciones se completan con éxito, confirmar la transacción
    mysqli_commit($conexion);
    $query_delete_detalle_venta = mysqli_query($conexion, "DELETE FROM detalle_venta WHERE id_venta = $id");
    $query_delete = mysqli_query($conexion, "DELETE FROM ventas WHERE id = $id");
    mysqli_close($conexion);
    header("Location: lista_ventas.php");
}