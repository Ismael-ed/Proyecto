<?php
require_once 'cabecera.php';

?>
<div class="container">
    <h4>Mis Anuncios</h4>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="form-group row">
            <label for="isbn" class="col-sm-2 col-form-label">ISBN</label>
            <div class="col-sm-2">
                <input type="text" class="form-control" id="isbn" name="isbn">
            </div>
            <label for="titulo" class="col-sm-2 col-form-label">Título</label>
            <div class="col-sm-2">
                <input type="text" class="form-control" id="titulo" name="titulo">
            </div>
            <label for="descripcion" class="col-sm-2 col-form-label">Descripción</label>
            <div class="col-sm-2">
                <input type="text" class="form-control" id="descripcion" name="descripcion">
            </div>
            <label for="autor" class="col-sm-2 col-form-label">Autor</label>
            <div class="col-sm-2">
                <input type="text" class="form-control" id="autor" name="autor">
            </div>
            <label for="precio" class="col-sm-2 col-form-label">Precio</label>
            <div class="col-sm-2">
                <input type="number" step="0.1" class="form-control" id="precio" name="precio">
            </div>
            <label for="foto" class="col-sm-2 col-form-label">Foto</label>
            <div class="col-sm-2">
                <input type="file" class="form-control" id="foto" name="foto">
            </div>
            <button type="submit" class="btn btn-outline-primary col-sm-2" name="crearL">+</button>
        </div>

    </form>
    <form action="" method="post" enctype="multipart/form-data">
        <?php
        //Recuperar los libros del usuario conectado
        $libros = $bd->obtenerMisLibros($_SESSION['us']->getId());
        ?>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">ISBN</th>
                    <th scope="col">Título</th>
                    <th scope="col">Descripción</th>
                    <th scope="col">Autor</th>
                    <th scope="col">Fecha Creación</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Precio</th>
                    <th scope="col">Comprador</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php
                foreach ($libros as $l) {
                    if (isset($_POST['editarL']) && $_POST['editarL'] == $l->getId()) {
                        //Poner campos editables
                        echo '<tr>';
                        echo '<td>' . $l->getId() . '</td>';
                        echo '<td><input type="text" name="isbn" value="' . $l->getIsbn() . '"></td>';
                        echo '<td><input type="text" name="titulo" value="' . $l->getTitulo() . '"></td>';
                        echo '<td><input type="text" name="descripcion" value="' . $l->getDescripcion() . '"></td>';
                        echo '<td><input type="text" name="autor" value="' . $l->getAutor() . '"></td>';
                        echo '<td>' . $l->getFechaC() . '</td>';
                        echo '<td>' ;
                        echo '<select name="estado">';
                        echo '<option '.($l->getEstado()=='Disponible'?'selected':'').'>Disponible</option>';
                        echo '<option '.($l->getEstado()=='Reservado'?'selected':'').'>Reservado</option>';
                        echo '</select>';
                        echo '</td>';
                        echo '<td><input type="number" step="0.1" name="precio" value="' . $l->getPrecio() . '"></td>';
                        echo '<td>' . $l->getComprador() . '</td>';
                        echo '<td>';
                        echo '<input type="file" name ="foto">';
                        echo'<img width="50px" src="https://s3.us-east-1.amazonaws.com/' . $bucket . '/' . $l->getCarpetaS3fotos() . '"/></td>';
                        echo '<td>';
                        echo '<button name="guardarL" value="' . $l->getId() . '" class="btn btn-outline-success">Guardar</button>';
                        echo '<button name="cancelarL" value="' . $l->getId() . '" class="btn btn-outline-danger">Cancelar</button>';
                        echo '</td>';
                        echo '</tr>';
                    } else {
                        echo '<tr>';
                        echo '<td>' . $l->getId() . '</td>';
                        echo '<td>' . $l->getIsbn() . '</td>';
                        echo '<td>' . $l->getTitulo() . '</td>';
                        echo '<td>' . $l->getDescripcion() . '</td>';
                        echo '<td>' . $l->getAutor() . '</td>';
                        echo '<td>' . $l->getFechaC() . '</td>';
                        echo '<td>' . $l->getEstado() . '</td>';
                        echo '<td>' . $l->getPrecio() . '</td>';
                        echo '<td>' . $l->getComprador() . '</td>';
                        echo '<td><img width="50px" src="https://s3.us-east-1.amazonaws.com/' . $bucket . '/' . $l->getCarpetaS3fotos() . '"/></td>';
                        echo '<td>';
                        if ($l->getComprador() == null) {
                            echo '<button name="editarL" value="' . $l->getId() . '" class="btn btn-outline-success">Editar</button>';
                            echo '<button name="borrarL" value="' . $l->getId() . '" class="btn btn-outline-danger">Borrar</button>';
                        }
                        echo '</td>';
                        echo '</tr>';
                    }
                }
                ?>
            </tbody>

        </table>

    </form>
</div>
<?php
require_once 'pie.php';
?>