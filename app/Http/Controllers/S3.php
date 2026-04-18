<?php
require_once 'vendor/autoload.php';

use Aws\S3\S3Client;

$bucket = 'rmmateosv01.app.wallabook';


class S3
{
    private $region = 'us-east-1';
    private $conexion;

    public function __construct()
    {
        try {
            //Conectar con S3
            $this->conexion = new S3Client([
                'version' => 'latest',
                'region' => $this->region
            ]);
        } catch (\Throwable $th) {
            global $error;
            $error = $th->getMessage();
        }
    }


    public function cargarObjeto($rutaObjeto, $nombreObjeto)
    {
        $resultado = false;
        global $bucket;
        try {
            $this->conexion->putObject([
                'Bucket' => $bucket,
                'Key' => $nombreObjeto,
                'SourceFile' => $rutaObjeto,
                'ContentType' => mime_content_type($rutaObjeto)
            ]);
            $resultado = true;
        } catch (\Throwable $th) {
            global  $error;
            $error = $th->getMessage();
        }
        return $resultado;
    }
    
    public function descargarObjeto($objeto)
    {
        $resultado = null;
         global $bucket;
        try {
            $r = $this->conexion->getObject([
                'Bucket' => $bucket,
                'Key' => $objeto
            ]);
            if (isset($r['Body'])) {
                $resultado['tipo'] = $r['ContentType'];
                $resultado['contenido'] = $r['Body'];
            }
        } catch (\Throwable $th) {
            global  $error;
            $error = $th->getMessage();
        }
        return $resultado;
    }
    public function borrarObjeto($objeto){
        $resultado = false;
         global $bucket;
        try {
            $r = $this->conexion->deleteObject([
                'Bucket' => $bucket,
                'Key' => $objeto
            ]);
            $resultado=true;
            
        } catch (\Throwable $th) {
            global  $error;
            $error = $th->getMessage();
        }
        return $resultado;
    }
}
?>