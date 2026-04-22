<?php
require_once 'Usuarios.php';
require_once 'Libros.php';
require_once 'Mensajes.php';
require_once 'S3.php';
class BD
{
    private $conexion = null;

    public function __construct()
    {
        //Conexión con la BD
        try {
            //Recuperamos datos de conexión de variables de entorno del sistema
            $host = getenv('RDS_HOST');
            $puerto = getenv('RDS_PUERTO');
            $bd = getenv('RDS_BD');
            $us = getenv('RDS_USUARIO');
            $ps = getenv('RDS_PS');
            if (!$host || !$puerto || !$bd || !$us || !$ps) {
                throw new Exception('Rellena variables de entorno con las credenciales de acceso');
            }
            $url = 'mysql:host=' . $host . ";port=$puerto;dbname=$bd";
            //$url = 'mysql:host='.$host.';port='.$puerto.';dbname='.$bd;
            $this->conexion = new PDO($url, $us, $ps);
        } catch (PDOException $e) {
            global $error;
            $error = 'ERROR BD' . $e->getMessage();
        } catch (\Throwable $th) {
            global $error;
            $error = 'ERROR GENÉRCO' . $th->getMessage();
        }
    }
    public function login($email, $ps)
    {
        $resultado = null;
        try {
            //Select para comprobar si hay un registro con ese email y esa contraseña
            $consulta = $this->conexion->prepare('SELECT * from usuarios 
                where email=? and ps=sha2(?,512)');
            $params = array($email, $ps);
            //Ejecutar la consulta pasando los parámetros que sustituye a las ?
            //execute: Devuelve true si el select se ejecuta, pero eso 
            //no quiere decir que el select devuelva algún registro.
            if ($consulta->execute($params)) {
                //Comprobar si el select ha encontrado el usuario por email y ps
                if ($fila = $consulta->fetch()) {
                    //Los datos son correctos, se encuentra un usuario
                    $resultado = new Usuarios($fila['id'], $fila['email'], $fila['nombre'], $fila['perfil'],$fila['numVentas']);
                }
            }
        } catch (PDOException $e) {
            global $error;
            $error = 'ERROR BD' . $e->getMessage();
        } catch (\Throwable $th) {
            global $error;
            $error = 'ERROR GENÉRCO' . $th->getMessage();
        }
        return $resultado;
    }
    public function crearUsuario(Usuarios $us, $ps)
    {
        $resultado = false;
        try {
            //Consulta INSERT con parámetros
            $consulta = $this->conexion->prepare('INSERT into usuarios 
                            values(default,?,sha2(?,512),?,?,default)');
            $params = array($us->getEmail(), $ps, $us->getNombre(), $us->getPerfil());
            if ($consulta->execute($params)) {
                //Comprobar si se ha insertado realmente
                if ($consulta->rowCount() == 1) {
                    //REcuperar el id del registro insertado y ponerlo en $us
                    $us->setId($this->conexion->lastInsertId());
                    $resultado = true;
                }
            }
        } catch (PDOException $e) {
            global $error;
            $error = 'ERROR BD' . $e->getMessage();
        } catch (\Throwable $th) {
            global $error;
            $error = 'ERROR GENÉRCO' . $th->getMessage();
        }
        return $resultado;
    }
    public function obtenerUsuario($email)
    {
        $resultado = null;
        try {
            $consulta = $this->conexion->prepare('SELECT * from usuarios 
                        where email = ?');
            $params = array($email);
            if ($consulta->execute($params)) {
                if ($fila = $consulta->fetch()) {
                    $resultado = new Usuarios(
                        $fila['id'],
                        $fila['email'],
                        $fila['nombre'],
                        $fila['perfil'],
                        $fila['numVentas']
                    );
                }
            }
        } catch (PDOException $e) {
            global $error;
            $error = 'ERROR BD' . $e->getMessage();
        } catch (\Throwable $th) {
            global $error;
            $error = 'ERROR GENÉRCO' . $th->getMessage();
        }

        return $resultado;
    }
    public function crearLibro(Libros $libro)
    {
        $resultado = false;
        try {
            //Crear el libro
            $rutaS3 = $libro->getVendedor() . '/' . $libro->getFechaC() . $libro->getCarpetaS3fotos()['name'];
            $consulta = $this->conexion->prepare('INSERT into libros values 
            (default,?,?,?,?,?,?,?,?,?,null,null)');
            $params = array(
                $libro->getFechaC(),
                $libro->getIsbn(),
                $libro->getTitulo(),
                $libro->getAutor(),
                $libro->getDescripcion(),
                $rutaS3,
                $libro->getEstado(),
                $libro->getPrecio(),
                $libro->getVendedor()
            );
            if ($consulta->execute($params) && $consulta->rowCount() == 1) {
                //Rellenar el id del libro
                $libro->setId($this->conexion->lastInsertId());
                //Subir la foto a S3
                $s3 = new S3();
                if ($s3->cargarObjeto($libro->getCarpetaS3fotos()['tmp_name'], $rutaS3)) {
                    $resultado = true;
                } else {
                    //SI NO SE SUBE LA FOTO, BORRAMOS EL LIBRO
                    $consulta = $this->conexion->prepare('DELETE from libros where id=?');
                    $params = array($libro->getId());
                    if ($consulta->execute($params) && $consulta->rowCount() == 1) {
                        $error = 'Error, libro no creado porque no se ha podido subir la foto';
                    } else {
                        //No ha subido foto y libro está creado
                        $error = 'Error, libro se ha creado, pero no se ha podido subir la foto';
                    }
                }
            }
        } catch (PDOException $e) {
            global $error;
            $error = 'ERROR BD' . $e->getMessage();
        } catch (\Throwable $th) {
            global $error;
            $error = 'ERROR GENÉRCO' . $th->getMessage();
        }
        return $resultado;
    }
    public function obtenerMisLibros($idUsuario)
    {
        $resultado = array();
        try {
            $consulta = $this->conexion->prepare('SELECT * from libros where vendedor = ?');
            $params =  array($idUsuario);
            if ($consulta->execute($params)) {
                while ($fila = $consulta->fetch()) {
                    $resultado[] = new Libros(
                        $fila['id'],
                        $fila['fechaC'],
                        $fila['isbn'],
                        $fila['titulo'],
                        $fila['autor'],
                        $fila['descripcion'],
                        $fila['carpetaS3Fotos'],
                        $fila['estado'],
                        $fila['precio'],
                        $fila['vendedor'],
                        $fila['comprador'],
                        $fila['valoracion']
                    );
                }
            }
        } catch (PDOException $e) {
            global $error;
            $error = 'ERROR BD' . $e->getMessage();
        } catch (\Throwable $th) {
            global $error;
            $error = 'ERROR GENÉRCO' . $th->getMessage();
        }
        return $resultado;
    }
    public function obtenerLibro($id)
    {
        $resultado = null;
        try {
            $consulta = $this->conexion->prepare('SELECT * from libros where id = ?');
            $params = array($id);
            if ($consulta->execute($params)) {
                if ($fila = $consulta->fetch()) {
                    $resultado = new Libros(
                        $fila['id'],
                        $fila['fechaC'],
                        $fila['isbn'],
                        $fila['titulo'],
                        $fila['autor'],
                        $fila['descripcion'],
                        $fila['carpetaS3Fotos'],
                        $fila['estado'],
                        $fila['precio'],
                        $fila['vendedor'],
                        $fila['comprador'],
                        $fila['valoracion']
                    );
                }
            }
        } catch (PDOException $e) {
            global $error;
            $error = 'ERROR BD' . $e->getMessage();
        } catch (\Throwable $th) {
            global $error;
            $error = 'ERROR GENÉRCO' . $th->getMessage();
        }

        return $resultado;
    }
    public function obtenerLibroAmpliado($id)
    {
        $resultado = null;
        try {
            $consulta = $this->conexion->prepare('SELECT * from libros l
                                                        join usuarios u on vendedor = u.id
                                                        left join usuarios u2 on comprador = u2.id
                                                        where l.id = ?');
            $params = array($id);
            if ($consulta->execute($params)) {
                if ($fila = $consulta->fetch()) {
                    $resultado = new Libros(
                        $fila[0], //Id de posición 0
                        $fila['fechaC'],
                        $fila['isbn'],
                        $fila['titulo'],
                        $fila['autor'],
                        $fila['descripcion'],
                        $fila['carpetaS3Fotos'],
                        $fila['estado'],
                        $fila['precio'],
                        new Usuarios($fila['vendedor'],$fila[13],$fila[15],$fila[16],$fila['numVentas']),
                        ($fila['comprador']==null?null:new Usuarios($fila['comprador'],$fila[20],$fila[22],$fila[23],$fila['numVentas'])),
                        $fila['valoracion']
                    );
                }
            }
        } catch (PDOException $e) {
            global $error;
            $error = 'ERROR BD' . $e->getMessage();
        } catch (\Throwable $th) {
            global $error;
            $error = 'ERROR GENÉRCO' . $th->getMessage();
        }

        return $resultado;
    }
    public function obtenerLibrosComprados($id)
    {
        $resultado = array();
        try {
            $consulta = $this->conexion->prepare('SELECT * from libros l
                                                        join usuarios u on vendedor = u.id
                                                        left join usuarios u2 on comprador = u2.id
                                                        where u2.id = ?');
            $params = array($id);
            if ($consulta->execute($params)) {
                while ($fila = $consulta->fetch()) {
                    $resultado[] = new Libros(
                        $fila[0], //Id de posición 0
                        $fila['fechaC'],
                        $fila['isbn'],
                        $fila['titulo'],
                        $fila['autor'],
                        $fila['descripcion'],
                        $fila['carpetaS3Fotos'],
                        $fila['estado'],
                        $fila['precio'],
                        new Usuarios($fila['vendedor'],$fila[13],$fila[15],$fila[16],$fila['numVentas']),
                        ($fila['comprador']==null?null:new Usuarios($fila['comprador'],$fila[20],$fila[22],$fila[23],$fila['numVentas'])),
                        $fila['valoracion']
                        
                    );
                }
            }
        } catch (PDOException $e) {
            global $error;
            $error = 'ERROR BD' . $e->getMessage();
        } catch (\Throwable $th) {
            global $error;
            $error = 'ERROR GENÉRCO' . $th->getMessage();
        }

        return $resultado;
    }
    public function borrarLibro($id)
    {
        $resultado = false;
        try {
            $consulta = $this->conexion->prepare('DELETE from libros where id = ?');
            $params = array($id);
            if ($consulta->execute($params) && $consulta->rowCount() == 1) {
                $resultado = true;
            }
        } catch (PDOException $e) {
            global $error;
            $error = 'ERROR BD' . $e->getMessage();
        } catch (\Throwable $th) {
            global $error;
            $error = 'ERROR GENÉRCO' . $th->getMessage();
        }

        return $resultado;
    }
    public function modificarLibro(Libros $libro)
    {
        $resultado = false;
        try {
            //Comprobar si hay foto
            if (!empty($libro->getCarpetaS3fotos()['name'])) {
                $rutaS3 = $_SESSION['us']->getId() . '/' . date('Y-m-d H:i:s') . $libro->getCarpetaS3fotos()['name'];
                $consulta = $this->conexion->prepare('UPDATE libros set
            isbn =?, titulo=?, descripcion=?, autor=?, precio=?, estado=?, carpetaS3Fotos= ?
            where id = ?');
                $params = array(
                    $libro->getIsbn(),
                    $libro->getTitulo(),
                    $libro->getDescripcion(),
                    $libro->getAutor(),
                    $libro->getPrecio(),
                    $libro->getEstado(),
                    $rutaS3,
                    $libro->getId()
                );
            } else {
                $consulta = $this->conexion->prepare('UPDATE libros set
                            isbn =?, titulo=?, descripcion=?, autor=?, precio=?, estado=?
                            where id = ?');
                $params = array(
                    $libro->getIsbn(),
                    $libro->getTitulo(),
                    $libro->getDescripcion(),
                    $libro->getAutor(),
                    $libro->getPrecio(),
                    $libro->getEstado(),
                    $libro->getId()
                );
            }
            if ($consulta->execute($params) && $consulta->rowCount() == 1) {
                if(isset($rutaS3)){
                    $s3 = new S3();
                    if(!$s3->cargarObjeto($libro->getCarpetaS3fotos()['tmp_name'],$rutaS3)){
                        $error = 'Anuncio modificado, pero foto no cargada';
                    }
                }
                $resultado = true;
            }
        } catch (PDOException $e) {
            global $error;
            $error = 'ERROR BD' . $e->getMessage();
        } catch (\Throwable $th) {
            global $error;
            $error = 'ERROR GENÉRCO' . $th->getMessage();
        }
        return $resultado;
    }
    public function obtenerAnuncios(){
        $resultado = array();
        try {
            $consulta = $this->conexion->prepare('SELECT * from libros
                where estado = ? and vendedor != ? order by fechaC desc');
                $params = array('disponible',$_SESSION['us']->getId());
            if($consulta->execute($params)){
                while($fila=$consulta->fetch()){
                    $resultado[]=new Libros(
                        $fila['id'],
                        $fila['fechaC'],
                        $fila['isbn'],
                        $fila['titulo'],
                        $fila['autor'],
                        $fila['descripcion'],
                        $fila['carpetaS3Fotos'],
                        $fila['estado'],
                        $fila['precio'],
                        $fila['vendedor'],
                        $fila['comprador'],
                        $fila['valoracion']
                    ); 
                }
            }
        } catch (PDOException $e) {
            global $error;
            $error = 'ERROR BD' . $e->getMessage();
        } catch (\Throwable $th) {
            global $error;
            $error = 'ERROR GENÉRCO' . $th->getMessage();
        }
        return $resultado;
    }
    public function registrarVenta(Libros $l){
        $resultado = false;
        try {
            //La venta supone, rellenar el comprador en la tabla libros y
            //Aumentar el nº de ventas del usuario vendedor.
            //Como hay que hacer dos operaciones (dos updates)
            //tenemos que usar un transacción, para asegurar que 
            //o se hacen las dos o no se hace ninguna

            //Iniciar transacción
            $this->conexion->beginTransaction();
            $consulta = $this->conexion->prepare('UPDATE libros set comprador = ?, estado=? where id=?');
            $params = array($_SESSION['us']->getId(),'Vendido',$l->getId());
            if($consulta->execute($params) && $consulta->rowCount()==1){
                //Actualizar el nº de ventas
                $consulta=$this->conexion->prepare('UPDATE usuarios set numVentas = numVentas+1 where id=?');
                $params = array($l->getVendedor()->getId());
                if($consulta->execute($params) && $consulta->rowCount()==1){
                    $this->conexion->commit();
                    $resultado=true;
                }else{
                    $this->conexion->rollback();
                }
            }
        }  catch (PDOException $e) {
            global $error;
            $error = 'ERROR BD' . $e->getMessage();
        } catch (\Throwable $th) {
            global $error;
            $error = 'ERROR GENÉRCO' . $th->getMessage();
        }
        return $resultado;
    }
    public function valorar(Libros $l,$num){
        $resultado = false;
        try {
            $consulta = $this->conexion->prepare('UPDATE libros set valoracion = ? 
            where id = ?');
            $params=array($num,$l->getId());
            if($consulta->execute($params) && $consulta->rowCount()==1){
                $resultado=true;
            }
        } catch (PDOException $e) {
            global $error;
            $error = 'ERROR BD' . $e->getMessage();
        } catch (\Throwable $th) {
            global $error;
            $error = 'ERROR GENÉRCO' . $th->getMessage();
        }
        return $resultado;
    }
    public function obtenerValoracionMedia($vendedor){
        $resultado = 0;
        try {
            $consulta = $this->conexion->prepare('SELECT obtenerMediaValoracion(?) as m');
            $params=array($vendedor);
            if($consulta->execute($params)){
                if($fila=$consulta->fetch()){
                    $resultado=$fila['m'];
                }
            }
        } catch (PDOException $e) {
            global $error;
            $error = 'ERROR BD' . $e->getMessage();
        } catch (\Throwable $th) {
            global $error;
            $error = 'ERROR GENÉRCO' . $th->getMessage();
        }
        return $resultado;
    }
    public function obtenerEstadistica($idUsuario){
        $resultado=array();
        try {
            $consulta = $this->conexion->prepare('CALL  estadistica(?)');
            $params=array($idUsuario);
            if($consulta->execute($params)){
                if($fila=$consulta->fetch()){
                    $resultado['numLibrosVenta']=$fila['numLibrosVenta'];
                    $resultado['numLibrosVendidos']=$fila['numVentas'];
                    $resultado['saldoTotal']=$fila['saldo'];
                }
            }
        } catch (PDOException $e) {
            global $error;
            $error = 'ERROR BD' . $e->getMessage();
        } catch (\Throwable $th) {
            global $error;
            $error = 'ERROR GENÉRCO' . $th->getMessage();
        }
        return $resultado;
    }
    /**
     * Get the value of conexion
     */
    public function getConexion()
    {
        return $this->conexion;
    }

    /**
     * Set the value of conexion
     *
     * @return  self
     */
    public function setConexion($conexion)
    {
        $this->conexion = $conexion;

        return $this;
    }
}
