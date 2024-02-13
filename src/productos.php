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
$permiso = "productos";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header('Location: permisos.php');
}
if (!empty($_POST)) {
      $alert = "";
    $id = $_POST['id'];
    $codigo = $_POST['codigo'];
    $producto = $_POST['producto'];
    $precio = $_POST['precio'];
    $cantidad = $_POST['cantidad'];
    $tipo = $_POST['tipo'];
    $laboratorio = $_POST['laboratorio'];
    $vencimiento = '';
    $p_venta = $_POST['p_venta'];
    $p_especial = $_POST['p_especial'];
    
    if (!empty($_POST['accion'])) {
        $vencimiento = $_POST['vencimiento'];
    }
    if (empty($codigo) || empty($producto) || empty($tipo) || empty($laboratorio) || empty($precio) || $precio < 0 || empty($cantidad) || $cantidad < 0 || empty($p_venta)) {
        $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        Todos los campos son obligatorios
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
    } elseif ($precio > $p_venta) {
        $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        El precio de costo no puede ser mayor que el precio de venta.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
    } else {
        if (empty($id)) {
            $query = mysqli_query($conexion, "SELECT * FROM producto WHERE codigo = '$codigo'");
            $result = mysqli_fetch_array($query);
            if ($result > 0) {
                $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        El codigo ya existe
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
            } else {
                $query_insert = mysqli_query($conexion,"INSERT INTO producto(codigo,descripcion,precio,existencia,id_lab,id_tipo,vencimiento,p_venta,p_especial) values ('$codigo', '$producto', '$precio', '$cantidad', $laboratorio, $tipo, '$vencimiento','$p_venta', '$p_especial')");
                //Obtener fecha y hora
                date_default_timezone_set('America/Mexico_City');
                $hora_actual = date('Y-m-d H:i:s');
                
                $hora_una_hora_menos = date('Y-m-d H:i:s', strtotime('-1 hour', strtotime($hora_actual)));
                $insertar = mysqli_query($conexion, "INSERT INTO movimiento(id_usuario, producto, tipo_mov, stock_producto, stock_anterior, id_sur, fecha_mov) VALUES ($id_user, '$producto', 'Producto nuevo', '$cantidad', '0', '$laboratorio', '$hora_una_hora_menos')");
                if ($query_insert) {
                    $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Producto registrado
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
                } else {
                    $alert = '<div class="alert alert-danger" role="alert">
                    Error al registrar el producto
                  </div>';
                }
            }
        } else {
            if (empty($p_especial)) {
                $p_especial = '0';
            } 
            else {
                // Escapar y proteger contra SQL injection
                $p_especial = mysqli_real_escape_string($conexion, $p_especial);
            }
        
            $query = mysqli_query($conexion, "SELECT * FROM producto WHERE codproducto = $id");
            if (!empty($id)) {
                $query = mysqli_query($conexion, "SELECT * FROM producto WHERE codproducto = $id");
                $row = mysqli_fetch_array($query);
            
                // Verificar si se encontró el producto
                if ($row) {
                    $precio_anterior = $row['precio'];
                    $precio_venta_anterior = $row['p_venta'];
                    $precio_especial_anterior = $row['p_especial'];
                    $existencia = $row['existencia'];
                
                $query_update = mysqli_query($conexion, "UPDATE producto SET codigo = '$codigo', descripcion = '$producto', precio = $precio, existencia = '$cantidad', id_lab = '$laboratorio', vencimiento = '$vencimiento', p_venta = '$p_venta', p_especial = '$p_especial' WHERE codproducto = $id");
                
                    $movimiento = '';
                    $stock_producto = '';
                    $anterior = '';
                
                    if ($existencia < $cantidad) {
                        $movimiento = 'Aumento';
                        $stock_producto = $cantidad;
                        $anterior = $existencia;
                    } else if ($existencia > $cantidad) {
                        $movimiento = 'Disminucion';
                        $stock_producto = $cantidad;
                        $anterior = $existencia;
                    } else if ($precio_anterior < $precio) {
                        $movimiento = 'Aumento precio';
                        $stock_producto = $precio;
                        $anterior = $precio_anterior;
                    } else if ($precio_anterior > $precio) {
                        $movimiento = 'Disminucion precio';
                        $stock_producto = $precio;
                        $anterior = $precio_anterior;
                    } else if ($precio_venta_anterior < $p_venta) {
                        $movimiento = 'Aumento precio venta';
                        $stock_producto = $p_venta;
                        $anterior = $precio_venta_anterior;
                    } else if ($precio_venta_anterior > $p_venta) {
                        $movimiento = 'Disminucion precio venta';
                        $stock_producto = $p_venta;
                        $anterior = $precio_venta_anterior;
                    } else if ($precio_especial_anterior < $p_especial) {
                        $movimiento = 'Aumento precio especial';
                        $stock_producto = $p_especial;
                        $anterior = $precio_especial_anterior;
                    } else if ($precio_especial_anterior > $p_especial) {
                        $movimiento = 'Disminucion precio especial';
                        $stock_producto = $p_especial;
                        $anterior = $precio_especial_anterior;
                    }
                
                    if (!empty($movimiento)) {
                        date_default_timezone_set('America/Mexico_City');
                        $hora_actual = date('Y-m-d H:i:s');
                        
                        $hora_una_hora_menos = date('Y-m-d H:i:s', strtotime('-1 hour', strtotime($hora_actual)));
                        
                        $insertar = mysqli_query($conexion, "INSERT INTO movimiento(id_usuario, producto, tipo_mov, stock_producto, stock_anterior, id_sur, fecha_mov) VALUES ($id_user, '$producto', '$movimiento', '$stock_producto', '$anterior', '$laboratorio', '$hora_una_hora_menos')");

                    }
                }
                
            }
                
            if ($query_update) {
                $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            Producto Modificado
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>';
            } else {
                $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                            Error al modificar
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>';
            }
        }
        
    }
}
include_once "includes/header.php";
?>
<div class="form-group">
<h3 class="m-0 text-dark">Productos</h3>
</div>
<div class="card shadow-lg">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white">
                        Productos
                    </div>
                    <div class="card-body">
                        <form action="" method="post" autocomplete="off" id="formulario">
                            <?php echo isset($alert) ? $alert : ''; ?>
                            <div class="row">
                                
                               <div class="row">
                                <div class="col-md-3">
                                    
                                    <div class="form-group">
                                        <label for="codigo" class=" text-dark font-weight-bold"><i class="fas fa-barcode"></i> Código de Barras</label>
                                        <input type="text" placeholder="Ingrese código de barras" name="codigo" id="codigo" class="form-control">
                                        <input type="hidden" id="id" name="id">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="producto" class=" text-dark font-weight-bold">Producto</label>
                                        <input type="text" placeholder="Ingrese nombre del producto" name="producto" id="producto" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="p_venta" class=" text-dark font-weight-bold">Precio venta</label>
                                        <input type="number" placeholder="Precio de venta" class="form-control" name="p_venta" id="p_venta" data-decimal="2" oninput="enforceNumberValidation(this)" placeholder="2 decimal places"  min="0" value="" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="precio" class=" text-dark font-weight-bold">Precio costo</label>
                                        <input type="number" placeholder="Precio de costo" class="form-control" name="precio" id="precio" data-decimal="2" oninput="enforceNumberValidation(this)" placeholder="2 decimal places" value="" min="0" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="p_especial" class=" text-dark font-weight-bold">Precio especial</label>
                                        <input type="number" placeholder="Precio especial" class="form-control" name="p_especial" id="p_especial" data-decimal="2" oninput="enforceNumberValidation(this)" placeholder="2 decimal places" min="0" value="" step="0.01">
                                    </div>
                                </div>
                        
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="cantidad" class=" text-dark font-weight-bold">Cantidad</label>
                                        <input type="number" placeholder="Ingrese cantidad" class="form-control" name="cantidad" id="cantidad">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="tipo">Categoria</label>
                                        <select id="tipo" class="form-control" name="tipo" required>
                                            <?php
                                            $query_tipo = mysqli_query($conexion, "SELECT * FROM tipos");
                                            while ($datos = mysqli_fetch_assoc($query_tipo)) { ?>
                                                <option value="<?php echo $datos['id'] ?>"><?php echo $datos['tipo'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="laboratorio">Sucursal</label>
                                        <select id="laboratorio" class="form-control" name="laboratorio" required>
                                            <?php
                                            $query_lab = mysqli_query($conexion, "SELECT * FROM laboratorios");
                                            while ($datos = mysqli_fetch_assoc($query_lab)) { ?>
                                                <option value="<?php echo $datos['id'] ?>"><?php echo $datos['laboratorio'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <input type="submit" value="Registrar" class="btn btn-primary" id="btnAccion">
                                    <input type="button" value="Nuevo" onclick="limpiar()" class="btn btn-success" id="btnNuevo">
                                </div>
                            </div>      
                                    
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="tbl">
                    <thead class="thead-dark">
                        <tr>
                             <th>#</th>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Categoria</th>
                            <th>Precio venta</th>
                            <th>Precio costo</th>
                            <th>Precio especial</th>
                            <th>Sucursal</th>
                            <th>Stock</th>
                            <th></th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include "../conexion.php";

                        $query = mysqli_query($conexion, "SELECT p.*, t.id, t.tipo, laboratorios.laboratorio FROM producto p INNER JOIN tipos t ON p.id_tipo = t.id INNER JOIN laboratorios ON laboratorios.id = p.id_lab");
                        $result = mysqli_num_rows($query);
                        if ($result > 0) {
                            while ($data = mysqli_fetch_assoc($query)) { ?>
                                <tr>
                                    <td><?php echo $data['codproducto']; ?></td>
                                    <td><?php echo $data['codigo']; ?></td>
                                    <td><?php echo $data['descripcion']; ?></td>
                                    <td><?php echo $data['tipo']; ?></td>
                                    <td><?php echo '$' . number_format($data['p_venta'], 2, '.', ','); ?></td>
                                    <td><?php echo '$' . number_format($data['precio'], 2, '.', ','); ?></td>
                                    <td><?php echo '$' . number_format($data['p_especial'], 2, '.', ','); ?></td>
                                    <td><?php echo $data['laboratorio']; ?></td>
                                    <td><?php echo $data['existencia']; ?></td>
                                    <td>
                                        <a href="#" onclick="editarProducto(<?php echo $data['codproducto']; ?>)" class="btn btn-primary"><i class='fas fa-edit'></i></a>

                                        <form action="eliminar_producto.php?id=<?php echo $data['codproducto']; ?>" method="post" class="confirmar d-inline">
                                            <button class="btn btn-danger" type="submit"><i class='fas fa-trash-alt'></i> </button>
                                        </form>
                                    </td>
                                </tr>
                        <?php }
                        } ?>
                    </tbody>

                </table>
            </div>
        </div>
        <?php
            include "../conexion.php";
            $query = mysqli_query($conexion, "SELECT SUM(p_venta * existencia) as suma, SUM(existencia) as existencia_total FROM producto p");
            $result = mysqli_num_rows($query);
            $data = mysqli_fetch_assoc($query)?>
        <div class="col-md-2">
            <div class="form-group" style="background-color: white; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-shadow: 0 0 4px #888;">
                <label for="total_stock" style="font-weight: bold;" class="text-dark">Total Stock:</label>
                <input type="text" placeholder="Ingrese nombre del producto" name="total_stock" id="total_stock" class="form-control" value="<?php echo $data['existencia_total'] ?>" disabled>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group" style="background-color: white; padding: 10px; border: 1px solid #ccc; border-radius: 5px; border-radius: 5px; box-shadow: 0 0 4px #888;">
                <label for="total_precio" style="font-weight: bold;" class="text-dark">Total precio venta:</label>
                <input type="text" placeholder="Precio de venta" class="form-control" name="total_precio" id="total_precio" value="$<?php echo number_format($data['suma'], 2, '.', ',') ?>" disabled>
            </div>
        </div>
    </div>
<?php include_once "includes/footer.php"; ?>