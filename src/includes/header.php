<?php
if (empty($_SESSION['active'])) {
    header('Location: ../');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" type="image/png" href="../assets/img/suteclogo_SF_logo.png"/>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Panel de Administración</title>
    <link href="../assets/css/overlayScrollbars/css/OverlayScrollbars.min.css" rel="stylesheet" />
    <link href="../assets/css/material-dashboard.css" rel="stylesheet" />
    <link href="../assets/css/ventas.css" rel="stylesheet" />
    <link href="../assets/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link rel="stylesheet" href="../assets/js/jquery-ui/jquery-ui.min.css">
    <!-- overlayScrollbars -->
    <script src="../assets/js/all.min.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="wrapper">
        <style>
            .bg-custom {
                background: linear-gradient(to bottom, #b1c2c9, #b1c2c9);
            }
            div.wrapper .sidebar .nav-link.active{
                background-color: #a024b4;
                color: #fff;
                border-radius: 8px;
            }
            div.wrapper .sidebar .nav-link.active:hover {
                background-color: #a024b4;
            }

        </style>
        <div class="sidebar" data-color="purple" data-background-color="blue" data-image="../assets/img/Degradado.png">
            <div class="logo bg-custom">
                <a href="./" class="simple-text logo-normal">
                    <img src="../assets/img/suteclogo_SF.png" alt="SUTEC 3D" style="width: 40%; height: 40%;">
                </a>
            </div>
            <div class="sidebar-wrapper">
                <ul class="nav">
                    <li class="nav-item">
                        <a class="nav-link d-flex nav_link" href="usuarios.php">
                            <i class="fas fa-user mr-2 fa-2x"></i>
                            <p> Usuarios</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex nav_link" href="config.php">
                            <i class="fas fa-cogs mr-2 fa-2x"></i>
                            <p> Configuración</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex nav_link" href="categoria.php">
                            <i class=" fas fa-tags mr-2 fa-2x"></i>
                            <p> Categorias</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link d-flex nav_link" href="sucursal.php">
                            <i class=" fas fa-store mr-2 fa-2x"></i>
                            <p> Sucursales</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex nav_link" href="productos.php">
                            <i class="fab fa-product-hunt mr-2 fa-2x"></i>
                            <p> Productos</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex nav_link" href="clientes.php">
                            <i class=" fas fa-users mr-2 fa-2x"></i>
                            <p> Clientes</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex nav_link" href="ventas.php">
                            <i class="fas fa-cash-register mr-2 fa-2x"></i>
                            <p> Nueva Venta</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex nav_link" href="lista_ventas.php">
                            <i class="fas fa-cart-plus mr-2 fa-2x"></i>
                            <p> Historial Ventas</p>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link d-flex nav_link" href="computadora.php">
                            <i class="fas fa-desktop mr-2 fa-2x"></i>
                            <p> Servicios</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link d-flex nav_link" href="historial_computadora.php">
                            <i class="fas fa-th-list mr-2 fa-2x"></i>
                            <p> Historial Servicios</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link d-flex nav_link" href="historial_movimientos.php">
                            <i class="fas fa-history mr-2 fa-2x"></i>
                            <p> Historial movimientos</p>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            const navLinkEls = document.querySelectorAll('.nav_link');
            const windowPathname = window.location.pathname;
            navLinkEls.forEach(navLinkEl => {
                const navLinkPathname = new URL(navLinkEl.href).pathname;
                if (windowPathname === navLinkPathname) {
                    navLinkEl.classList.add('active');
                }
            });
        });
        </script>
        <div class="main-panel">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-absolute fixed-top">
                <div class="container-fluid">
                    <div class="navbar-wrapper">
                        <a class="navbar-brand" href="javascript:;">Sistema de Venta</a>
                    </div>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="navbar-toggler-icon icon-bar"></span>
                        <span class="navbar-toggler-icon icon-bar"></span>
                        <span class="navbar-toggler-icon icon-bar"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-end">

                        <ul class="navbar-nav">
                            <div class="info">
                                <a href="javascript:;" class="nav-link" id="navbarDropdownProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $_SESSION["nombre"];?></a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownProfile">
                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#nuevo_pass">Perfil</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="salir.php">Cerrar Sesión</a>
                                </div>
                            </div>
                            <li class="nav-item dropdown">
                                <a class="nav-link" href="javascript:;" id="navbarDropdownProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-user"></i>
                                    <p class="d-lg-none d-md-block">
                                        Cuenta
                                    </p>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownProfile">
                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#nuevo_pass">Perfil</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="salir.php">Cerrar Sesión</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- End Navbar -->
            <div class="content bg">
                <div class="container-fluid">