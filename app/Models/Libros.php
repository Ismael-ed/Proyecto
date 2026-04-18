<?php
    class Libros{
        private $id,$fechaC,$isbn,$titulo,$autor,$descripcion,$carpetaS3fotos,
        $estado,$precio,$vendedor,$comprador,$valoracion;

        public function __construct($id,$fechaC,$isbn,$titulo,$autor,$descripcion,$carpetaS3fotos,
        $estado,$precio,$vendedor,$comprador,$valoracion)
        {
            $this->id=$id;
            $this->fechaC=$fechaC;
            $this->isbn=$isbn;
            $this->titulo=$titulo;
            $this->autor=$autor;
            $this->descripcion=$descripcion;
            $this->carpetaS3fotos=$carpetaS3fotos;
            $this->estado=$estado;
            $this->precio=$precio;
            $this->vendedor=$vendedor;
            $this->comprador=$comprador;
            $this->valoracion=$valoracion;
        }

        /**
         * Get the value of id
         */ 
        public function getId()
        {
                return $this->id;
        }

        /**
         * Set the value of id
         *
         * @return  self
         */ 
        public function setId($id)
        {
                $this->id = $id;

                return $this;
        }

        /**
         * Get the value of fechaC
         */ 
        public function getFechaC()
        {
                return $this->fechaC;
        }

        /**
         * Set the value of fechaC
         *
         * @return  self
         */ 
        public function setFechaC($fechaC)
        {
                $this->fechaC = $fechaC;

                return $this;
        }

        /**
         * Get the value of isbn
         */ 
        public function getIsbn()
        {
                return $this->isbn;
        }

        /**
         * Set the value of isbn
         *
         * @return  self
         */ 
        public function setIsbn($isbn)
        {
                $this->isbn = $isbn;

                return $this;
        }

        /**
         * Get the value of titulo
         */ 
        public function getTitulo()
        {
                return $this->titulo;
        }

        /**
         * Set the value of titulo
         *
         * @return  self
         */ 
        public function setTitulo($titulo)
        {
                $this->titulo = $titulo;

                return $this;
        }

        /**
         * Get the value of autor
         */ 
        public function getAutor()
        {
                return $this->autor;
        }

        /**
         * Set the value of autor
         *
         * @return  self
         */ 
        public function setAutor($autor)
        {
                $this->autor = $autor;

                return $this;
        }

        /**
         * Get the value of descripcion
         */ 
        public function getDescripcion()
        {
                return $this->descripcion;
        }

        /**
         * Set the value of descripcion
         *
         * @return  self
         */ 
        public function setDescripcion($descripcion)
        {
                $this->descripcion = $descripcion;

                return $this;
        }

        /**
         * Get the value of carpetaS3fotos
         */ 
        public function getCarpetaS3fotos()
        {
                return $this->carpetaS3fotos;
        }

        /**
         * Set the value of carpetaS3fotos
         *
         * @return  self
         */ 
        public function setCarpetaS3fotos($carpetaS3fotos)
        {
                $this->carpetaS3fotos = $carpetaS3fotos;

                return $this;
        }

        /**
         * Get the value of estado
         */ 
        public function getEstado()
        {
                return $this->estado;
        }

        /**
         * Set the value of estado
         *
         * @return  self
         */ 
        public function setEstado($estado)
        {
                $this->estado = $estado;

                return $this;
        }

        /**
         * Get the value of precio
         */ 
        public function getPrecio()
        {
                return $this->precio;
        }

        /**
         * Set the value of precio
         *
         * @return  self
         */ 
        public function setPrecio($precio)
        {
                $this->precio = $precio;

                return $this;
        }

        /**
         * Get the value of vendedor
         */ 
        public function getVendedor()
        {
                return $this->vendedor;
        }

        /**
         * Set the value of vendedor
         *
         * @return  self
         */ 
        public function setVendedor($vendedor)
        {
                $this->vendedor = $vendedor;

                return $this;
        }

        /**
         * Get the value of comprador
         */ 
        public function getComprador()
        {
                return $this->comprador;
        }

        /**
         * Set the value of comprador
         *
         * @return  self
         */ 
        public function setComprador($comprador)
        {
                $this->comprador = $comprador;

                return $this;
        }

        /**
         * Get the value of valoracion
         */ 
        public function getValoracion()
        {
                return $this->valoracion;
        }

        /**
         * Set the value of valoracion
         *
         * @return  self
         */ 
        public function setValoracion($valoracion)
        {
                $this->valoracion = $valoracion;

                return $this;
        }
    }
?>