<?php
require_once "../conexion.php";
session_start();
if (isset($_GET['q'])) {
    $datos = array();
    $nombre = $_GET['q'];
    $cliente = mysqli_query($conexion, "SELECT * FROM cliente WHERE nombre LIKE '%$nombre%'");
    while ($row = mysqli_fetch_assoc($cliente)) {
        $data['id'] = $row['idcliente'];
        $data['label'] = $row['nombre'];
        $data['direccion'] = $row['direccion'];
        $data['telefono'] = $row['telefono'];
        array_push($datos, $data);
    }
    echo json_encode($datos);
    die();
}else if (isset($_GET['pro'])) {
    $datos = array();
    $nombre = $_GET['pro'];
    $hoy = date('Y-m-d');
    $sucursal1 = $_GET['sucursal1'];
    $producto = mysqli_query($conexion, "SELECT * FROM producto WHERE id_lab = $sucursal1 AND descripcion LIKE '%" . $nombre . "%' OR codigo LIKE '%" . $nombre . "%' AND vencimiento > '$hoy' OR vencimiento = '0000-00-00'");
    $productoCodigo = mysqli_query($conexion, "SELECT * FROM producto WHERE codigo LIKE '%" . $nombre . "%' OR descripcion LIKE '%" . $nombre . "%' AND vencimiento > '$hoy' OR vencimiento = '0000-00-00' LIMIT 1");
    if ($row = mysqli_fetch_assoc($productoCodigo)) {
        $data['id'] = $row['codproducto'];
        $data['label'] = $row['codigo'] . ' - ' .$row['descripcion'];
        $data['value'] = $row['descripcion'];
        $data['p_venta'] = $row['p_venta'];
        $data['existencia'] = $row['existencia'];
        array_push($datos, $data);
    }
    while($row = mysqli_fetch_assoc($producto)){
        $data['id'] = $row['codproducto'];
        $data['label'] = $row['codigo'] . ' - ' .$row['descripcion'];
        $data['value'] = $row['descripcion'];
        $data['p_venta'] = $row['p_venta'];
        $data['existencia'] = $row['existencia'];
        array_push($datos, $data);
    }
    echo json_encode($datos);
    die();
}else if (isset($_GET['pro_especial'])) {
    $datos = array();
    $nombre = $_GET['pro'];
    $hoy = date('Y-m-d');
    $producto = mysqli_query($conexion, "SELECT * FROM producto WHERE descripcion LIKE '%" . $nombre . "%' OR codigo LIKE '%" . $nombre . "%' AND vencimiento > '$hoy' OR vencimiento = '0000-00-00'");
    $productoCodigo = mysqli_query($conexion, "SELECT * FROM producto WHERE codigo LIKE '%" . $nombre . "%' OR descripcion LIKE '%" . $nombre . "%' AND vencimiento > '$hoy' OR vencimiento = '0000-00-00' LIMIT 1");
    if ($row = mysqli_fetch_assoc($productoCodigo)) {
        $data['id'] = $row['codproducto'];
        $data['label'] = $row['codigo'] . ' - ' .$row['descripcion'];
        $data['value'] = $row['descripcion'];
        $data['p_venta'] = $row['p_especial'];
        $data['existencia'] = $row['existencia'];
        array_push($datos, $data);
    }
    while($row = mysqli_fetch_assoc($producto)){
        $data['id'] = $row['codproducto'];
        $data['label'] = $row['codigo'] . ' - ' .$row['descripcion'];
        $data['value'] = $row['descripcion'];
        $data['p_venta'] = $row['p_especial'];
        $data['existencia'] = $row['existencia'];
        array_push($datos, $data);
    }
    echo json_encode($datos);
    die();
}if (isset($_POST['actualizar_precio_especial'])) {
    $id_user = $_SESSION['idUser'];
    
    // Realiza una consulta para actualizar el precio especial en detalle_temp
    $actualizar_precio_query = mysqli_query($conexion, "UPDATE detalle_temp AS dt
        JOIN producto AS p ON dt.id_producto = p.codproducto
        SET dt.precio_venta = p.p_especial,
            dt.total = dt.cantidad * p.p_especial
        WHERE dt.id_usuario = $id_user AND dt.id_producto > 0 AND p.p_especial > 0"); // Solo cuando id_producto sea mayor que 0
    
    if ($actualizar_precio_query) {
        // Éxito al actualizar el precio especial
        echo json_encode(['status' => 'success']);
    } else {
        // Error al actualizar el precio especial
        echo json_encode(['status' => 'error']);
    }
    die(); // Termina la ejecución del script
}else if (isset($_POST['actualizar_precio_normal'])) {
    $id_user = $_SESSION['idUser'];
    
    // Realiza una consulta para actualizar el precio especial en detalle_temp
    $actualizar_precio_normal_query = mysqli_query($conexion, "UPDATE detalle_temp AS dt
        JOIN producto AS p ON dt.id_producto = p.codproducto
        SET dt.precio_venta = p.p_venta,
            dt.total = dt.cantidad * p.p_venta
        WHERE dt.id_usuario = $id_user AND dt.id_producto > 0"); // Solo cuando id_producto sea mayor que 0
    
    if ($actualizar_precio_normal_query) {
        // Éxito al actualizar el precio especial
        echo json_encode(['status' => 'success']);
    } else {
        // Error al actualizar el precio especial
        echo json_encode(['status' => 'error']);
    }
    die(); // Termina la ejecución del script
}
else if (isset($_GET['detalle'])) {
    $id = $_SESSION['idUser'];
    $datos = array();
    
    $detalle = mysqli_query($conexion, "SELECT d.*, COALESCE(p.descripcion, d.producto_c) as producto_descripcion, p.codproducto, p.p_especial, p.p_venta FROM detalle_temp d LEFT JOIN producto p ON d.id_producto = p.codproducto WHERE d.id_usuario = $id");
    
    while ($row = mysqli_fetch_assoc($detalle)) {
        $data['id'] = $row['id'];
        $data['descripcion'] = $row['producto_descripcion'];
        $data['cantidad'] = $row['cantidad'];
        $data['p_venta'] = $row['p_venta'];
        $data['p_especial'] = $row['p_especial'];
        $data['precio_venta'] = $row['precio_venta'];
        $data['sub_total'] = $row['total'];
        array_push($datos, $data);
    }
    echo json_encode($datos);
    die();
}else if (isset($_GET['delete_detalle'])) {
    $id_detalle = $_GET['id'];
    $query = mysqli_query($conexion, "DELETE FROM detalle_temp WHERE id = $id_detalle");
    if ($query) {
        $msg = "ok";
    } else {
        $msg = "Error";
    }
    echo $msg;
    die();
} else if (isset($_GET['procesarVenta'])) {
    $id_cliente = $_GET['id'];
    $id_user = $_SESSION['idUser'];
    $cambio = $_GET['cambio'];
    $consulta = mysqli_query($conexion, "SELECT total, SUM(total) AS total_pagar FROM detalle_temp WHERE id_usuario = $id_user");
    $result = mysqli_fetch_assoc($consulta);
    $total = $result['total_pagar'];
    if (empty($cambio)) {
        $cambio = '0';
    } 
    else {
        // Escapar y proteger contra SQL injection
        $cambio = mysqli_real_escape_string($conexion, $cambio);
    }
    //Obtener fecha y hora
    date_default_timezone_set('America/Mexico_City');
    $hora_actual = date('Y-m-d H:i:s');
    
    $hora_una_hora_menos = date('Y-m-d H:i:s', strtotime('-1 hour', strtotime($hora_actual)));
    $insertar = mysqli_query($conexion, "INSERT INTO ventas(id_cliente, total, id_usuario, fecha, cambio) VALUES ($id_cliente, '$total', $id_user, '$hora_una_hora_menos', '$cambio')");
    if ($insertar) {
        $id_maximo = mysqli_query($conexion, "SELECT MAX(id) AS total FROM ventas");
        $resultId = mysqli_fetch_assoc($id_maximo);
        $ultimoId = $resultId['total'];
        $consultaDetalle = mysqli_query($conexion, "SELECT * FROM detalle_temp WHERE id_usuario = $id_user");
        while ($row = mysqli_fetch_assoc($consultaDetalle)) {
            $id_producto = $row['id_producto'];
            $producto_c = $row['producto_c'];
            $cantidad = $row['cantidad'];
            $desc = $row['descuento'];
            $precio = $row['precio_venta'];
            $total = $row['total'];
            $sucursal = $row['laboratorio'];
            $insertarDet = mysqli_query($conexion, "INSERT INTO detalle_venta (id_producto, id_venta, cantidad, precio, descuento, total, producto_c, id_lab_comun) VALUES ($id_producto, $ultimoId, $cantidad, '$precio', '$desc', '$total', '$producto_c', '$sucursal')");
            
            // Verificar si id_producto es mayor a 0 antes de actualizar el stock
            if ($id_producto > 0) {
                $stockActual = mysqli_query($conexion, "SELECT * FROM producto WHERE codproducto = $id_producto");
                $stockNuevo = mysqli_fetch_assoc($stockActual);
                $stockTotal = $stockNuevo['existencia'] - $cantidad;
                $stock = mysqli_query($conexion, "UPDATE producto SET existencia = $stockTotal WHERE codproducto = $id_producto");
            }
            
            $msg = 'registrado';
        } 
        if ($insertarDet) {
            $eliminar = mysqli_query($conexion, "DELETE FROM detalle_temp WHERE id_usuario = $id_user");
            $msg = array('id_cliente' => $id_cliente, 'id_venta' => $ultimoId);
        }
    } else {
        $msg = array('mensaje' => 'error');
    }
    echo json_encode($msg);
    die();
}else if (isset($_GET['descuento'])) {
    $id = $_GET['id'];
    $desc = $_GET['desc'];
    $consulta = mysqli_query($conexion, "SELECT * FROM detalle_temp WHERE id = $id");
    $result = mysqli_fetch_assoc($consulta);
    $total_desc = $desc + $result['descuento'];
    
    $D=$desc/100;
    $T=$D*$result['total'];
    
    $total = $result['total']-$T;
    $insertar = mysqli_query($conexion, "UPDATE detalle_temp SET descuento = $total_desc, total = '$total'  WHERE id = $id");
    if ($insertar) {
        $msg = array('mensaje' => 'descontado');
    }else{
        $msg = array('mensaje' => 'error');
    }
    echo json_encode($msg);
    die();
}else if(isset($_GET['editarCliente'])){
    $idcliente = $_GET['id'];
    $sql = mysqli_query($conexion, "SELECT * FROM cliente WHERE idcliente = $idcliente");
    $data = mysqli_fetch_array($sql);
    echo json_encode($data);
    exit;
} else if (isset($_GET['editarUsuario'])) {
    $idusuario = $_GET['id'];
    $sql = mysqli_query($conexion, "SELECT * FROM usuario WHERE idusuario = $idusuario");
    $data = mysqli_fetch_array($sql);
    echo json_encode($data);
    exit;
} else if (isset($_GET['editarProducto'])) {
    $id = $_GET['id'];
    $sql = mysqli_query($conexion, "SELECT * FROM producto WHERE codproducto = $id");
    $data = mysqli_fetch_array($sql);
    echo json_encode($data);
    exit;
} else if (isset($_GET['editarTipo'])) {
    $id = $_GET['id'];
    $sql = mysqli_query($conexion, "SELECT * FROM tipos WHERE id = $id");
    $data = mysqli_fetch_array($sql);
    echo json_encode($data);
    exit;
} else if (isset($_GET['editarPresent'])) {
    $id = $_GET['id'];
    $sql = mysqli_query($conexion, "SELECT * FROM presentacion WHERE id = $id");
    $data = mysqli_fetch_array($sql);
    echo json_encode($data);
    exit;
} else if (isset($_GET['editarLab'])) {
    $id = $_GET['id'];
    $sql = mysqli_query($conexion, "SELECT * FROM laboratorios WHERE id = $id");
    $data = mysqli_fetch_array($sql);
    echo json_encode($data);
    exit;
}
if (isset($_POST['regDetalle'])) {
    $id = $_POST['id'];
    $cant = $_POST['cant'];
    $precio = $_POST['precio'];
    $id_user = $_SESSION['idUser'];
    $total = $precio * $cant;
    $verificar = mysqli_query($conexion, "SELECT * FROM detalle_temp WHERE id_producto = $id AND id_usuario = $id_user");
    $result = mysqli_num_rows($verificar);
    $datos = mysqli_fetch_assoc($verificar);
    if ($result > 0) {
        $cantidad = $datos['cantidad'] + $cant;
        $total_precio = ($cantidad * $total);
        $query = mysqli_query($conexion, "UPDATE detalle_temp SET cantidad = $cantidad, total = '$total_precio' WHERE id_producto = $id AND id_usuario = $id_user");
        if ($query) {
            $msg = "actualizado";
        } else {
            $msg = "Error al ingresar";
        }
    }else{
        $query = mysqli_query($conexion, "INSERT INTO detalle_temp(id_usuario, id_producto, cantidad, precio_venta, total, producto_c, laboratorio) VALUES ($id_user, $id, $cant, '$precio', '$total', '', '0')");
        if ($query) {
            $msg = "registrado";
        }else{
            $msg = "Error al ingresar";
        }
    }
    echo json_encode($msg);
    die();
}else if (isset($_POST['cambio'])) {
    if (empty($_POST['actual']) || empty($_POST['nueva'])) {
        $msg = 'Los campos estan vacios';
    } else {
        $id = $_SESSION['idUser'];
        $actual = md5($_POST['actual']);
        $nueva = md5($_POST['nueva']);
        $consulta = mysqli_query($conexion, "SELECT * FROM usuario WHERE clave = '$actual' AND idusuario = $id");
        $result = mysqli_num_rows($consulta);
        if ($result == 1) {
            $query = mysqli_query($conexion, "UPDATE usuario SET clave = '$nueva' WHERE idusuario = $id");
            if ($query) {
                $msg = 'ok';
            }else{
                $msg = 'error';
            }
        } else {
            $msg = 'dif';
        }
        
    }
    echo $msg;
    die();
    
}
else if (isset($_GET['obtenerCantidadDisponible'])) {
    $id = $_GET['id'];
    $consulta = mysqli_query($conexion, "SELECT existencia FROM producto WHERE codproducto = $id");
    $result = mysqli_fetch_assoc($consulta);
    $cantidad_disponible = $result['existencia'];
    echo json_encode(array('cantidad_disponible' => $cantidad_disponible));
    die();
}
if (isset($_POST['regDetalleComun'])) {
    //$id = $_GET['id_producto'];
    $productoC = $_POST['productoC'];
    $cant = $_POST['cant'];
    $precio = $_POST['precio'];
    $id_user = $_SESSION['idUser'];
    $sucursal = $_POST['sucursal'];
    $total = $precio * $cant;
    $verificar = mysqli_query($conexion, "SELECT * FROM detalle_temp WHERE total = $precio AND id_usuario = $id_user");
    $result = mysqli_num_rows($verificar);
    $datos = mysqli_fetch_assoc($verificar);
    if ($result > 0) {
        $cantidad = $datos['cantidad'] + $cant;
        $total_precio = ($cantidad * $total);
        $query = mysqli_query($conexion, "UPDATE detalle_temp SET cantidad = $cantidad, total = '$total_precio' WHERE total = $precio AND id_usuario = $id_user");
        if ($query) {
            $msg = "actualizado";
        } else {
            $msg = "Error al ingresar";
        }
    }else{
        $query = mysqli_query($conexion, "INSERT INTO detalle_temp(id_usuario, cantidad ,precio_venta, total, producto_c, laboratorio) VALUES ($id_user ,$cant,'$precio', '$total', '$productoC', '$sucursal')");
        if ($query) {
            $msg = "registrado";
        }else{
            $msg = "Error al ingresar";
        }
    }
    echo json_encode($msg);
    die();
}
else if (isset($_GET['obtenerCantidadDisponible'])) {
    $id = $_GET['id'];
    $consulta = mysqli_query($conexion, "SELECT existencia FROM producto WHERE codproducto = $id");
    $result = mysqli_fetch_assoc($consulta);
    $cantidad_disponible = $result['existencia'];
    echo json_encode(array('cantidad_disponible' => $cantidad_disponible));
    die();
}
if (isset($_POST['regDetalleComun'])) {
    //$id = $_GET['id_producto'];
    $productoC = $_POST['productoC'];
    $cant = $_POST['cant'];
    $precio = $_POST['precio'];
    $id_user = $_SESSION['idUser'];
    $total = $precio * $cant;
    $verificar = mysqli_query($conexion, "SELECT * FROM detalle_temp WHERE total = $precio AND id_usuario = $id_user");
    $result = mysqli_num_rows($verificar);
    $datos = mysqli_fetch_assoc($verificar);
    if ($result > 0) {
        $cantidad = $datos['cantidad'] + $cant;
        $total_precio = ($cantidad * $total);
        $query = mysqli_query($conexion, "UPDATE detalle_temp SET cantidad = $cantidad, total = '$total_precio' WHERE total = $precio AND id_usuario = $id_user");
        if ($query) {
            $msg = "actualizado";
        } else {
            $msg = "Error al ingresar";
        }
    }else{
        $query = mysqli_query($conexion, "INSERT INTO detalle_temp(id_usuario, cantidad ,precio_venta, total, producto_c) VALUES ($id_user ,$cant,'$precio', '$total', '$productoC')");
        if ($query) {
            $msg = "registrado";
        }else{
            $msg = "Error al ingresar";
        }
    }
    echo json_encode($msg);
    die();
}
if (isset($_GET['obtenerVentasDia'])) {
    $fecha = $_GET['fecha'];

    // Consulta para obtener las ganancias por sucursal y el total vendido en la fecha seleccionada
    $consultaSucursal_totalganancias = mysqli_query($conexion, "SELECT sucursal, SUM(total_ventas) AS total_ventas
    FROM (
        SELECT l.laboratorio AS sucursal, SUM(d.total) AS total_ventas 
        FROM detalle_venta d 
        INNER JOIN producto p ON d.id_producto = p.codproducto 
        INNER JOIN laboratorios l ON p.id_lab = l.id 
        WHERE d.id_venta IN (SELECT id FROM ventas WHERE DATE(fecha) = '$fecha') 
        GROUP BY l.laboratorio
    
        UNION
    
        SELECT l.laboratorio AS sucursal, SUM(d.total) AS total_ventas 
        FROM detalle_venta d 
        INNER JOIN laboratorios l ON d.id_lab_comun = l.id 
        WHERE d.id_producto = 0 AND d.id_venta IN (SELECT id FROM ventas WHERE DATE(fecha) = '$fecha') 
        GROUP BY l.laboratorio
    ) AS ventas_totales
    GROUP BY sucursal");

    $gananciasSucursal = array();
    while ($row = mysqli_fetch_assoc($consultaSucursal_totalganancias)) {
        $sucursal = $row['sucursal'];
        $totalVentasSucursal = $row['total_ventas'];

        // Consulta para obtener las ganancias por sucursal
        $consultaSucursal = mysqli_query($conexion, "SELECT SUM(d.cantidad * p.precio) AS ganancias
            FROM detalle_venta d
            INNER JOIN producto p ON d.id_producto = p.codproducto
            INNER JOIN ventas v ON d.id_venta = v.id
            INNER JOIN laboratorios l ON p.id_lab = l.id
            WHERE d.id_venta IN (SELECT id FROM ventas WHERE DATE(fecha) = '$fecha' AND l.laboratorio = '$sucursal')");

        $resultSucursal = mysqli_fetch_assoc($consultaSucursal);
        $gananciaSucursal = $resultSucursal['ganancias'];

        // Calcular las ganancias por sucursal restando el total vendido en la fecha seleccionada
        // al total vendido en esa sucursal
        $gananciasSucursal[] = array(
            'sucursal' => $sucursal,
            'ganancias' => $totalVentasSucursal - $gananciaSucursal 
        );
    }

// Consulta para obtener la ganancia para la fecha seleccionada
$consultaTotal = mysqli_query($conexion, "SELECT SUM(d.cantidad * p.precio) AS ganancia 
    FROM detalle_venta d INNER JOIN producto p ON d.id_producto = p.codproducto
    WHERE d.id_venta IN (SELECT id FROM ventas WHERE DATE(fecha) = '$fecha')");
//$consultaTotal = mysqli_query($conexion, "SELECT SUM(total) AS total_ventas FROM ventas WHERE DATE(fecha) = '$fecha'");
$resultTotal = mysqli_fetch_assoc($consultaTotal);
$totalVentas = $resultTotal['ganancia'];

// Consulta para obtener el total vendido para la fecha seleccionada
$consultaGanancia = mysqli_query($conexion, "SELECT SUM(total) AS ventas_totales FROM ventas WHERE DATE(fecha) = '$fecha'");
$resultGanancia = mysqli_fetch_assoc($consultaGanancia);
$ganancia = $resultGanancia['ventas_totales'];

// Realizar la resta entre totalVentas y ganancia
$total_ventas = $ganancia - $totalVentas;

// Consulta para obtener las ventas totales por sucursal y el total vendido en la fecha seleccionada
$consultaSucursal_totalventas = mysqli_query($conexion, "SELECT sucursal, SUM(total_ventas) AS total_ventas
FROM (
    SELECT l.laboratorio AS sucursal, SUM(d.total) AS total_ventas 
    FROM detalle_venta d 
    INNER JOIN producto p ON d.id_producto = p.codproducto 
    INNER JOIN laboratorios l ON p.id_lab = l.id 
    WHERE d.id_venta IN (SELECT id FROM ventas WHERE DATE(fecha) = '$fecha') 
    GROUP BY l.laboratorio

    UNION

    SELECT l.laboratorio AS sucursal, SUM(d.total) AS total_ventas 
    FROM detalle_venta d 
    INNER JOIN laboratorios l ON d.id_lab_comun = l.id 
    WHERE d.id_producto = 0 AND d.id_venta IN (SELECT id FROM ventas WHERE DATE(fecha) = '$fecha') 
    GROUP BY l.laboratorio
) AS ventas_totales
GROUP BY sucursal");
$ventastotalSucursal = array();
while ($row = mysqli_fetch_assoc($consultaSucursal_totalventas)) {
    $sucursal = $row['sucursal'];
    $totalVentasSucursal = $row['total_ventas'];
    // Mostrar las ventas totales por sucursal
    $ventastotalSucursal[] = array(
        'sucursal' => $sucursal,
        'ganancias' => $totalVentasSucursal
    );
}

echo json_encode(['ganancia' => $ganancia, 'total_ventas' => $total_ventas, 'ganancias_sucursal' => $gananciasSucursal, 'totalvendido_sucursal' => $ventastotalSucursal]);
die();

}


