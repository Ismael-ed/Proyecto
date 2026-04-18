<?php
require_once 'cabecera.php';
//Redirigir a index si el perfil no es Admin
if($_SESSION['us']->getPerfil()!='A'){
    header('location:index.php');
}
echo '<h4>Gestión Usuarios</h4>';
require_once 'pie.php';
?>