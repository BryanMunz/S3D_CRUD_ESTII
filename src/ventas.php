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
$permiso = "nueva_venta";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header('Location: permisos.php');
}
include_once "includes/header.php";
?>
<div class="form-group">
<h3 class="m-0 text-dark">Nueva Venta</h3>
</div>
<div class="row">
    <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-success  text-white text-center">
                    Datos del Cliente
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="hidden" id="idcliente" value="1" name="idcliente" required>
                                    <label>Nombre</label>
                                    <input type="text" name="nom_cliente" id="nom_cliente" class="form-control" placeholder="Ingrese nombre del cliente" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Teléfono</label>
                                    <input type="number" name="tel_cliente" id="tel_cliente" class="form-control" disabled required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Dirreción</label>
                                    <input type="text" name="dir_cliente" id="dir_cliente" class="form-control" disabled required>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
            <div class="card-header bg-primary text-white text-center">
                <div class="row">
                    <div class="card-header bg-primary text-white text-center">
                        Sucursal:
                    </div>
                    <div class="">
                        <div class="select-container">
                                <select id="sucursal1" class="select-box" name="sucursal1" required>
                                <?php
                                $query_lab = mysqli_query($conexion, "SELECT * FROM laboratorios ORDER BY id DESC");
                                while ($datos = mysqli_fetch_assoc($query_lab)) { ?>
                                <option value="<?php echo $datos['id'] ?>"><?php echo $datos['laboratorio'] ?></option>
                                <?php } ?>
                                </select>
                        </div>
                    </div>
                    <div class="card-header bg-primary text-white text-center col-lg-6">
                        Buscar Productos
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="producto">Código o Nombre</label>
                            <input id="producto" class="form-control" type="text" name="producto" placeholder="Ingresa el código o nombre">
                            <input id="id" type="hidden" name="id">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label for="cantidad">Cantidad</label>
                            <input type="number" id="cantidad" class="form-control" type="text" name="cantidad" placeholder="Cantidad" onkeyup="calcularPrecio(event)">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class ="form-group">
                            <label for="precio">Precio</label>
                            <input id="precio" class="form-control" type="text" name="precio" placeholder="Precio" disabled>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label for="sub_total">Sub Total</label>
                            <input id="sub_total" class="form-control" type="text" name="sub_total" placeholder="Sub Total" disabled>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-info  text-white text-center">
                Producto Común
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="producto_normal">Nombre del producto común</label>
                            <input id="producto_normal" class="form-control" type="text" name="producto" placeholder="Ingresa el nombre del producto común">
                        </div>
                    </div>
                    <div class="col-lg-2">
                    <div class="form-group">
                        <label for="sucursal">Sucursal</label>
                            <select id="sucursal" class="form-control" name="sucursal" required disabled>
                                <?php
                                $query_lab = mysqli_query($conexion, "SELECT * FROM laboratorios ORDER BY id DESC");
                                while ($datos = mysqli_fetch_assoc($query_lab)) { ?>
                                    <option value="<?php echo $datos['id'] ?>"><?php echo $datos['laboratorio'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label for="cantidad_normal">Cantidad</label>
                            <input type="number" id="cantidad_normal" class="form-control" type="text" name="cantidad" placeholder="Cantidad"  min="0" onkeyup="calcularPrecio2(event)">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label for="precio_normal">Precio</label>
                            <input type="number" placeholder="Precio" id="precio_normal" class="form-control" name="precio" data-decimal="2" oninput="enforceNumberValidation(this)" placeholder="2 decimal places"  min="0" value="" step="0.01" onkeyup="calcularPrecio2(event)">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label for="sub_total_normal">Sub Total</label>
                            <input id="sub_total_normal" class="form-control" type="text" name="sub_total" placeholder="Sub Total" disabled>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover" id="tblDetalle">
                <thead class="thead-dark">
                    <tr>
                        <th>Id</th>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        <th>Precio Venta</th>
                        <th>Precio Total</th>
                        <th>Accion</th>
                    </tr>
                </thead>
                <tbody id="detalle_venta">

                </tbody>
                <tfoot>
                    <tr class="font-weight-bold">
                        <td>Total Pagar</td>
                        <td></td>
                    </tr>
                </tfoot>

                <tfoot>
                    <tr class="font-weight-bold">
                        <td>Total cambio</td>
                        <td><div id="cambioTotal">0.00</div></td>
                    </tr>
                </tfoot>
            </table>

        </div>
    </div>
    <div class="col-md-6">
        <a href="#" class="btn btn-primary" id="btn_generar"><i class="fas fa-save"></i> Generar Venta</a>
        <a href="#" class="btn btn-success" id="btn_mostrar_pdf"><i class="fas fa-money-bill-wave"></i> Calcular cambio</a>
        <a href="#" class="btn btn-info" id="btn_mostrar_precios"><i class="fas fa-dollar-sign"></i> Tipos de precio</a>
    </div>

</div>

<!-- Modal para calcular el cambio -->
<div class="modal fade" id="cambioModal" tabindex="-1" role="dialog" aria-labelledby="cambioModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cambioModalLabel">Calcular Cambio</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="totalVenta">Total de la Venta:</label>
          <input type="text" class="form-control" id="totalVenta" placeholder="Ingrese el total de la venta" disabled>
        </div>
        <div class="form-group">
          <label for="montoRecibido">Monto Recibido:</label>
          <input type="number" class="form-control" id="montoRecibido" placeholder="Ingrese el monto recibido">
        </div>
        <div class="form-group">
          <label for="cambio">Cambio:</label>
          <input type="text" class="form-control" id="cambio" readonly>
        </div>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="calcularCambio">Calcular Cambio</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal para selecionar tipos de precios -->
<div class="modal fade" id="cambioModalPrecio" tabindex="-1" role="dialog" aria-labelledby="cambioModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cambioModalLabel">Selecciona el tipo de precio</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group text-center">
            <a href="#" class="btn btn-success btn-lg btn-block" id="btn_precio_normal"><i class="fas fa-money-bill-wave"></i> Precio normal</a>
        </div>
        <div class="form-group text-center">
            <a href="#" class="btn btn-info btn-lg btn-block" id="btn_precio_especial"><i class="fas fa-money-bill-wave"></i> Precio especial</a>
        </div>
    </div>

      
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<?php include_once "includes/footer.php"; ?>