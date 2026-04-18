<?php

use PHPMailer\PHPMailer\PHPMailer;

require_once 'BD.php';
session_start();
function cargarConfiguracion(){
   $resultado=array();
   if(!file_exists('.env')){
      throw new Exception('No existe fichero .env');
   }
   $f=file('.env',FILE_IGNORE_NEW_LINES);
   foreach($f as $linea){
      $datos=explode('=',$linea);
      $resultado[$datos[0]]=$datos[1];
   }
   if(!array_key_exists('US_CORREO',$resultado) || !array_key_exists('PS_CORREO',$resultado)){
      throw new Exception('Rellena credenciales del correo');
   }
   
   return $resultado;
}
function enviarCorreo($para,$asunto,$texto){
   try {
      $correo = new PHPMailer(true);
      //Configurar datos del servidor saliente
      $correo->isSMTP();
      $correo->Host='smtp.gmail.com';
      $correo->SMTPAuth=true;
      //Cargar datos de acceso a gmail
      $config=cargarConfiguracion();
      $correo->Username=$config['US_CORREO'];
      $correo->Password=$config['PS_CORREO'];
      $correo->SMTPSecure=PHPMailer::ENCRYPTION_SMTPS;
      $correo->Port=465;
      //Datos del email
      $correo->setFrom($config['US_CORREO']);
      $correo->addAddress($para); //Para
      $correo->isHTML(true);
      $correo->CharSet='UTF-8';
      $correo->Subject=$asunto;
      $correo->Body=$texto;
      $correo->AltBody='El correo se visualiza en formato Web';
      //Enviar Correo
      $correo->send();
   } catch (\Throwable $th) {
      global $error;
      $error = 'ERROR MAIL' . $th->getMessage();
   }
}

//Crear conexión a la BD
$bd = new BD();
if ($bd->getConexion() != null) {
   if (isset($_POST['iniciar'])) {
      //comprobaciones
      if (empty($_POST['email']) || empty($_POST['ps'])) {
         $error = 'Rellena datos de acceso';
      } else {
         $u = $bd->login($_POST['email'], $_POST['ps']);
         if ($u == null) {
            $error = (isset($error) ? 'Excepción' . $error : 'Error en el acceso');
         } else {
            //Guardar el us logeado en la sesión
            $_SESSION['us'] = $u;
            //Redirigir a index
            header('location:index.php');
         }
      }
   } elseif (isset($_POST['crearUS'])) {
      //Validaciones
      //Campos vacíos
      if (empty($_POST['nombre']) || empty($_POST['email']) || empty($_POST['ps']) || empty($_POST['ps2'])) {
         $error = 'Todos los campos son obligatorios';
      } elseif ($_POST['ps'] != $_POST['ps2']) {
         $error = 'Las contraseñas deben coincidir';
      } else {
         //Validar si email duplicado
         $u = $bd->obtenerUsuario($_POST['email']);
         if ($u != null) {
            $error = 'Error, email ya está registrado';
         } else {
            //Si llego aquí, $u es null
            $u = new Usuarios(null, $_POST['email'], $_POST['nombre'], 'U',null);
            if ($bd->crearUsuario($u, $_POST['ps'])) {
               $mensaje = 'Usuario creado con ID:' . $u->getId();
            } else {
               $error = (isset($error) ? 'Excepción' . $error : 'Error al crear el usuario');
            }
         }
      }
   } elseif (isset($_POST['cerrar'])) {
      //Cerrar sesión y redirigir a login
      session_destroy();
      header('location:login.php');
   } elseif (isset($_POST['crearL'])) {
      //Chequear campos rellenos
      if (
         empty($_POST['isbn']) || empty($_POST['titulo']) || empty($_POST['descripcion']) ||
         empty($_POST['autor']) || empty($_POST['precio']) || empty($_FILES['foto']['name'])
      ) {
         $error = 'Todos los campos son obligatorios';
      } else {
         $libro = new Libros(
            null,
            date('Y-m-d H:i:s'),
            $_POST['isbn'],
            $_POST['titulo'],
            $_POST['autor'],
            $_POST['descripcion'],
            $_FILES['foto'],
            'Disponible',
            $_POST['precio'],
            $_SESSION['us']->getId(),
            null,null
         );
         if ($bd->crearLibro($libro)) {
            $mensaje = 'Libro Creado con id:' . $libro->getId();
         } else {
            $error = (isset($error) ? 'Excepción' . $error : 'Error al crear el libro');
         }
      }
   } elseif (isset($_POST['borrarL'])) {
      //Chequear que el libro existe y se puede borrar si no hay comprador
      $l = $bd->obtenerLibro($_POST['borrarL']);
      if ($l != null && $l->getComprador() == null) {
         //Se puede borrar
         if ($bd->borrarLibro($l->getId())) {
            $mensaje = 'Libro borrado';
         } else {
            $error = (isset($error) ? 'Excepción' . $error : 'No se puede borrar el libro');
         }
      } else {
         $error = (isset($error) ? 'Excepción' . $error : 'No existe el libro o está vendido');
      }
   } elseif (isset($_POST['guardarL'])) {
      if (
         empty($_POST['isbn']) || empty($_POST['titulo']) || empty($_POST['descripcion']) ||
         empty($_POST['autor']) || empty($_POST['precio'])
      ) {
         $error = 'Todos los campos son obligatorios';
      } else {
         $l = new Libros($_POST['guardarL'], null, $_POST['isbn'], $_POST['titulo'], $_POST['autor'], $_POST['descripcion'], $_FILES['foto'], $_POST['estado'], $_POST['precio'], null, null,null);
         //Modificar el libro
         if ($bd->modificarLibro($l)) {
            $mensaje = "Libro modificado";
         } else {
            $error = (isset($error) ? 'Excepción' . $error : 'Error al modificar el libro');
         }
      }
   } elseif (isset($_POST['bComprar'])) {
      //REcuperar datos del libro que se ha comprado
      $libro = $bd->obtenerLibroAmpliado($_POST['bComprar']);
      if ($libro != null && $libro->getComprador() == null) {
         //Registrar venta
         if ($bd->registrarVenta($libro)) {
            //Enviar correo a comprador
            $asunto='Libro comprado';
            $texto='Has comprado el libro'.$libro->getTitulo().'.';
            $texto.='<br><img width="100px" src="https://s3.us-east-1.amazonaws.com/' . $bucket .
            '/' . $libro->getCarpetaS3fotos() . '">';
            enviarCorreo($_SESSION['us']->getEmail(),$asunto,$texto);
            //Enviar correo a vendedor
            $asunto='Libro vendido';
            $texto='Has vendido el libro <b>'.$libro->getTitulo().'</b>.';
             $texto.='<br><img width="100px" src="https://s3.us-east-1.amazonaws.com/' . $bucket .
            '/' . $libro->getCarpetaS3fotos() . '">';
            enviarCorreo($libro->getVendedor()->getEmail(),$asunto,$texto);
            //echo '<script>alert("Libro comprado")</script>';
            header('location:misCompras.php');
         } else {
            $error = (isset($error) ? 'Excepción' . $error : 'Error al comprar el libro');
         }
      } else {
         $error = (isset($error) ? 'Excepción' . $error : 'El libro no está dsiponible');
      }
   } elseif (isset($_POST['bVal'])) {
      $l=$bd->obtenerLibro($_POST['bVal']);
      if($l==null || $l->getValoracion()!=null || empty($_POST['valoracion'])){
         $error = (isset($error) ? 'Excepción' . $error : 'El libro no se no puede valorar');
      }else{
         if($bd->valorar($l,$_POST['valoracion'])){
            $mensaje='Valoración realizada';
         }
      }

   }
}
