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
$permiso = "historial servicios";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header('Location: permisos.php');
}
$query = mysqli_query($conexion, "SELECT computadoras.id, computadoras.folio, computadoras.nombre, computadoras.fechaingreso, computadoras.modelo, computadoras.descripcion FROM computadoras");

$consulta = mysqli_query($conexion, "SELECT * FROM computadoras");
$data = mysqli_fetch_all($consulta, MYSQLI_ASSOC); // Obtén todos los resultados como un arreglo asociativo

//Se realiza la petición sql 
$query_text = 'SELECT computadoras.id, computadoras.folio, computadoras.nombre, computadoras.fechaingreso, computadoras.modelo, computadoras.descripcion FROM computadoras';
// echo $query_text;
//Se procesa con la consulta a la BD
$query_res = mysqli_query($conexion, $query_text);
//Arreglo temporal que almacenara la información
$computadoras = array();
//Se verifica si hay un resultado
if (mysqli_num_rows($query_res) != 0) {
    while ($datos = mysqli_fetch_array($query_res, MYSQLI_ASSOC)) {
        $computadoras[] = $datos;
    } //end mientras sigan existiendo registros
} //end if no hay resultados
//end else 

include_once "includes/header.php";
?>
<div class="form-group">
<h3 class="m-0 text-dark">Historial Servicios</h3>
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
                        <th>Folio</th>
                        <th>Cliente</th>
                        <th>Marca y modelo</th>
                        <th>Descripción</th>
                        <th>Fecha</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $html = '';
                    if (isset($computadoras) && sizeof($computadoras) != 0) {
                                    foreach ($computadoras as $computadora) {
                                        $html .= '
                                    <tr>
                                        <td>' . $computadora['id'] . '</td>
                                        <td>' . $computadora['folio'] . '</td>
                                        <td>' . $computadora["nombre"] . '</td>
                                        <td>' . $computadora["modelo"] . '</td>
                                        <td>' . $computadora["descripcion"] . '</td>
                                        <td>' . $computadora["fechaingreso"] . '</td>
                                        <td>';
                                        $html .= '
                                        <a href="./pdf/generarPC2.php?id='.$computadora['id'].'" target="_blank" class="btn btn-danger" id="generarPDFPC"><i class="fas fa-file-pdf"></i></a>
                                        </td>
                                    </tr>
                                  ';
                                    }}
                                    echo $html;
                    ?>
                    <!--<a class="btn btn-danger" href="pdf/generarPC2.php/<?php echo $data[$i]['id']; ?>"><i class="fas fa-file-pdf"></i></a>-->
                    <!-- <a href="pdf/generarPC2.php?cl=<?php echo $row['id'] ?>&v=<?php echo $row['id'] ?>" target="_blank" class="btn btn-danger"><i class="fas fa-file-pdf"></i>-->
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include_once "includes/footer.php"; ?>