<?php
session_start();
include "../conexion.php";

$id_user = $_SESSION['idUser'];
if (!isset($_SESSION['idUser'])) {
    echo '<script>
                alert("Error, no ha iniciado sesión y no se puede redirigir a la página deseada.");
                window.location = "../index.php";
                </script>';
  }
$permiso = "servicios";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header('Location: permisos.php');
}
if (!empty($_POST)) {
    $alert = "";
    $id = $_POST['id'];
    $nombre = $_POST['nombreC'];
    $telefonoC = $_POST['telefonoC'];
    $direccion = $_POST['direccionC'];
    $Fechaingreso = $_POST['ingresoC'];
    $folio = $_POST['folioC'];
    $Crevision = $_POST['precioC'];
    $resive = $_POST['resiveC'];

   $piezas = $_POST['piezasC'];
   $Modelo = $_POST['marcamodelo'];
   $serie = $_POST['serieC'];
   $descripcion = $_POST['descripcionC'];   

    
    if (empty($nombre)) {
        $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        Todo los campos son obligatorios
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
    } else {
       
              
            $query_insert = mysqli_query($conexion, "INSERT INTO computadoras(nombre,direccion,telefono, fechaingreso,folio,costoR,recibe, piezas,  modelo, serir, descripcion)
            values ('$nombre','$direccion','$telefonoC','$Fechaingreso','$folio','$Crevision', '$resive','$piezas','$Modelo','$serie', '$descripcion')");

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
}

//Se realiza la petición sql 
$query_text = 'SELECT computadoras.id FROM computadoras WHERE computadoras.id;';
// echo $query_text;
//Se procesa con la consulta a la BD
$query_res = mysqli_query($conexion, $query_text);
while($datos = mysqli_fetch_array($query_res, MYSQLI_ASSOC)){
    $usuarios = $datos;}

    if(isset($usuarios) && sizeof($usuarios) > 0){
        $num = 0;}

include_once "includes/header.php";
?>
<div class="form-group">
<h3 class="m-0 text-dark">Servicios</h3>
</div>
<div class="card shadow-lg">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white">
                        Datos del cliente
                    </div>
                    <div class="card-body">
                        <form action="" method="post" autocomplete="off" id="formulario">
                            <?php echo isset($alert) ? $alert : ''; ?>
                            <div class="row">

                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label for="nombreC" class=" text-dark font-weight-bold"> Nombre</label>
                                        <input type="text" placeholder="Ingrese el nombre" name="nombreC" id="nombreC" class="form-control">
                                        <input type="hidden" id="id" name="id">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="telefonoC" class=" text-dark font-weight-bold">Celular</label>
                                        <input type="number" placeholder="Ingrese numero telefonico" name="telefonoC" id="telefonoC" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="direccionC" class=" text-dark font-weight-bold">Dirección</label>
                                        <input type="text" placeholder="Ingrese la direccion" class="form-control" name="direccionC" id="direccionC">
                                    </div>
                                </div>

                                 <div class="col-md-3">
                                    <div class="form-group">
                                      
                                        <label for="ingresoC">Fecha de ingreso</label>
                                        <input id="ingresoC" class="form-control" type="date" name="ingresoC">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="folioC" class=" text-dark font-weight-bold">Folio</label>
                                        <input type="text" placeholder="Ingrese el folio" class="form-control" name="folioC" id="folioC" value="<?php echo $usuarios['id'] + 1; ?>">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="precioC" class=" text-dark font-weight-bold">Costo de revisión</label>
                                        <input type="text" placeholder="Ingrese el costo de revisión" class="form-control" name="precioC" id="precioC" value="$">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="resiveC" class=" text-dark font-weight-bold">Nombre del personal</label>
                                        <input type="text" placeholder="Personal que recibe el equipo" class="form-control" name="resiveC" id="resiveC">
                                    </div>
                                </div>

                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white">
                        Datos del equipo
                    </div>
                      </div>

                      <div class="col-md-5">
                            <div class="form-group">
                                <label for="piezasC" class="text-dark font-weight-bold">Piezas</label>
                              <textarea placeholder="Ingrese el número de piezas" name="piezasC" id="piezasC" class="form-control"></textarea>
                        </div>
                    </div>
                      <div class="col-md-6">
                            <div class="form-group">
                                <label for="marcamodelo" class="text-dark font-weight-bold">Marca y modelo</label>
                              <textarea placeholder="Ingrese la marca y el modelo" name="marcamodelo" id="marcamodelo" class="form-control"></textarea>
                        </div>
                    </div>
                      <div class="col-md-4">
                            <div class="form-group">
                                <label for="serieC" class="text-dark font-weight-bold">No de serie</label>
                              <textarea placeholder="Ingrese el número de serie" class="form-control" name="serieC" id="serieC"></textarea>
                        </div>
                    </div>
                      <div class="col-md-7">
                            <div class="form-group">
                                <label for="descripcionC" class="text-dark font-weight-bold">Descripción</label>
                              <textarea placeholder="Ingrese la descripción" class="form-control" name="descripcionC" id="descripcionC"></textarea>
                        </div>
                    </div>



                                </div>
                           </div>
        
                           
                               <div class="col-md-6">
                                    <input type="submit" value="Registrar"  class="btn btn-primary" id="btnAccion">
                                    <input type="button" value="Generar PDF"  class="btn btn-success" id="btn_pc"  >
                                </div>
                                
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
     


</div>
<?php include_once "includes/footer.php"; ?>