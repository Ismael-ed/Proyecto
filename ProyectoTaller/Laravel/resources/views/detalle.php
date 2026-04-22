<?php
require_once 'cabecera.php';
?>
<div class="container">
    <form action="" method="POST">
        <?php
        if (!isset($_GET['libro'])) {
            header('location:index.php');
        }
        $libro = $bd->obtenerLibroAmpliado($_GET['libro']);
        if ($libro == null) {
            header('location:index.php');
        }
        echo '<h3>Detalle Libro: ' . $libro->getTitulo() . '</h3>';
        echo '<h3>Vendedor: ' . $libro->getVendedor()->getNombre() .' - Valoración:'.
        $bd->obtenerValoracionMedia($libro->getVendedor()->getId()). '</h3>';
        echo '<div style="display: flex; flex-direction:column; align-items:center;">';
        echo '<img width="100px" src="https://s3.us-east-1.amazonaws.com/' . $bucket .
            '/' . $libro->getCarpetaS3fotos() . '">';
        echo '<h7>' . $libro->getTitulo() . '</h7>';
        echo '<h7>' . $libro->getPrecio() . '€</h7>';
        echo '<p>' . $libro->getDescripcion() . '</p>';
        echo '<div>';
        echo '<button type="submit" name="bMensaje" value="' . $libro->getId() . '" class="btn btn-outline-info">Mensaje</button>';
        echo '<button type="submit" name="bComprar"  value="' . $libro->getId() . '" class="btn btn-outline-success">Comprar</button>';
        echo '</div>';
        echo '</div>';
        ?>
    </form>
</div>
<?php
require_once 'pie.php';
?>