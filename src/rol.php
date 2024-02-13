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

$id = $_GET['id'];
$sqlpermisos = mysqli_query($conexion, "SELECT * FROM permisos");
$usuarios = mysqli_query($conexion, "SELECT * FROM usuario WHERE idusuario = $id");
$consulta = mysqli_query($conexion, "SELECT * FROM detalle_permisos WHERE id_usuario = $id");
$resultUsuario = mysqli_num_rows($usuarios);

if (empty($resultUsuario)) {
    header("Location: usuarios.php");
    exit(); // Terminar el script para evitar ejecución adicional
}

$datos = array();
foreach ($consulta as $asignado) {
    $datos[$asignado['id_permiso']] = true;
}

if (isset($_POST['permisos'])) {
    $id_user = $_GET['id'];
    $permisos = $_POST['permisos'];

    // Eliminar los permisos anteriores del usuario
    mysqli_query($conexion, "DELETE FROM detalle_permisos WHERE id_usuario = $id_user");

    if (!empty($permisos)) {
        foreach ($permisos as $permiso) {
            // Evitar inyección SQL utilizando consultas preparadas
            $stmt = mysqli_prepare($conexion, "INSERT INTO detalle_permisos(id_usuario, id_permiso) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "ii", $id_user, $permiso);
            mysqli_stmt_execute($stmt);
        }
        $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Permisos Asignados
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
    }
}

include_once "includes/header.php";
?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card shadow-lg">
            <div class="card-header card-header-primary">
                Permisos
            </div>
            <div class="card-body">
                <form method="post" action="">
                    <?php echo (isset($alert)) ? $alert : ''; ?>
                    <?php while ($row = mysqli_fetch_assoc($sqlpermisos)) { ?>
                        <div class="form-check form-check-inline m-4">
                            <label for="permisos_<?php echo $row['id']; ?>" class="p-2 text-uppercase"><?php echo $row['nombre']; ?></label>
                            <input id="permisos_<?php echo $row['id']; ?>" type="checkbox" name="permisos[]" value="<?php echo $row['id']; ?>" <?php
                                                                                                                                    if (isset($datos[$row['id']])) {
                                                                                                                                        echo "checked";
                                                                                                                                    }
                                                                                                                                    ?>>
                        </div>
                    <?php } ?>
                    <br>
                    <button class="btn btn-primary btn-block" type="submit">Modificar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include_once "includes/footer.php"; ?>
