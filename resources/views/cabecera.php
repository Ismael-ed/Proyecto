<?php
require_once 'controlador.php';
//Si no estoy logueado, redirigir a login
if (!isset($_SESSION['us'])) {
    header('location:login.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <title>WallaBook</title>
</head>

<body>
    <div class="container">
        <ul class="nav">
            <li class="nav-item">
                <a class="nav-link active" href="index.php">Inicio</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="misAnuncios.php">Mis anuncios</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="misCompras.php">Mis compras</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="buzon.php">Buzón</a>
            </li>
            <?php
            if ($_SESSION['us']->getPerfil() == 'A') {
                echo '<li class="nav-item">
                <a class="nav-link active" href="gestionUS.php">Usuarios</a>
            </li>';
            }
            ?>
            <li class="nav-item">
                <form action="" method="post">
                    <button type="submit" class="btn btn-outline-primary" name="cerrar">Cerrar</a>
                </form>
            </li>
            <li class="nav-item">
                <h4 style="margin-left: 20px;"><?php echo $_SESSION['us']->getNombre();
                                                ?>
                </h4>
            </li>
            <li class="nav-item">
                <?php
                if ($_SESSION['us']->getPerfil() == 'U') {
                    $estadistica = $bd->obtenerEstadistica($_SESSION['us']->getId());
                    if (!empty($estadistica)) {
                        echo 'Libros en Venta:' . $estadistica['numLibrosVenta'];
                        echo '-Libros Vendidos:' . $estadistica['numLibrosVendidos'];
                        echo '-Saldo Obtenido:' . $estadistica['saldoTotal'];
                    }
                }
                ?>
            </li>
        </ul>
    </div>
    <?php

    ?>