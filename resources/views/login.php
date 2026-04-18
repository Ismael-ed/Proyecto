<?php
require_once 'controlador.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <title>Document</title>
</head>

<body>
    <div class="container">
        <h1 class="text-info">Bienvenido a WallaBook</h1>
        <form action="" method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input class="form-control" type="email" name="email" id="email" 
                value="<?php echo (isset($_POST['email'])?$_POST['email']:'')?>">
            </div>
            <div class="mb-3">
                <label class="form-label" for="ps">Constraseña</label>
                <input class="form-control" type="password" name="ps" id="ps">
            </div>

            <button type="submit" name="iniciar" class="btn btn-primary">Iniciar</button>
            <button type="reset" name="cancelar" class="btn btn-danger">Cancelar</button>
            <a href="registro.php" class="btn btn-success">Registrarse</a>
        </form>

        <div>
            <?php
            if (isset($error)) {
                echo '<h3 style="color:red;">' . $error . '</h3>';
            }
            ?>
        </div>
        <div>
            <?php
            if (isset($mensaje)) {
                echo '<h3 style="color:green;">' . $mensaje . '</h3>';
            }
            ?>
        </div>
    </div>

</body>

</html>