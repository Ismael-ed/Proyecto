<?php
require_once 'cabecera.php';

?>
<div class="container">
    <h4>Mis Compras</h4>
    <form action="" method="post">
        <div style="display: flex; flex-direction:row">
            <?php
            $libros = $bd->obtenerLibrosComprados($_SESSION['us']->getId());
            foreach ($libros as $l) {
                echo '<div style="display: flex; flex-direction:column; align-items:center;">';
                echo '<a href="detalle.php?libro=' . $l->getId() . '"><img width="100px" src="https://s3.us-east-1.amazonaws.com/' . $bucket .
                    '/' . $l->getCarpetaS3fotos() . '"></a>';
                echo '<h7>' . $l->getTitulo() . '</h7>';
                echo '<h7>' . $l->getPrecio() . '</h7>';
                echo '<p>' . $l->getDescripcion() . '</p>';
                echo '<p>' . $l->getVendedor()->getNombre() . '</p>';
                if ($l->getValoracion() == null) {
                    echo '<input type="number" name="valoracion" value="3" min="1" max="5">';
                    echo '<button type="submit" name="bVal" value="'.$l->getId().'">Valorar</button>';
                } else {
                    echo '<p>' . $l->getValoracion() . '</p>';
                }
                echo '</div>';
            }
            ?>
        </div>
    </form>
</div>
<?php
require_once 'pie.php';
?>