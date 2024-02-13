<?php
session_start();
require("../conexion.php");
$id_user = $_SESSION['idUser'];
$permiso = "productos";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header("Location: permisos.php");
}
if (!empty($_GET['id'])) {
    $id = $_GET['id'];
    $query = mysqli_query($conexion, "SELECT * FROM producto WHERE codproducto = $id");
    if ($row = mysqli_fetch_assoc($query)) { // Se verifica si se obtuvo una fila
        $producto = $row['descripcion'];
        $cantidad = $row['existencia'];
        $sucursal = $row['id_lab'];
        //Obtener fecha y hora
        date_default_timezone_set('America/Mexico_City');
        $hora_actual = date('Y-m-d H:i:s');
        $hora_una_hora_menos = date('Y-m-d H:i:s', strtotime('-1 hour', strtotime($hora_actual)));
        $insertar = mysqli_query($conexion, "INSERT INTO movimiento(id_usuario, producto, tipo_mov, stock_producto, stock_anterior, id_sur, fecha_mov) VALUES ($id_user, '$producto', 'Producto eliminado', '0', '$cantidad', $sucursal, '$hora_una_hora_menos')");
        $query_delete = mysqli_query($conexion, "DELETE FROM producto WHERE codproducto = $id");
        mysqli_close($conexion);
        header("Location: productos.php");
    } else {
        echo "Producto no encontrado.";
    }
} else {
    echo "ID de producto no proporcionado.";
}
?>

