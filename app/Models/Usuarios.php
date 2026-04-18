<?php
    class Usuarios{
        private $id,$email,$nombre,$perfil, $numVentas;

        public function __construct($id,$email,$nombre, $perfil, $numVentas)
        {
            $this->id=$id;
            $this->email=$email;
            $this->nombre=$nombre;
            $this->perfil=$perfil;
            $this->numVentas=$numVentas;
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
         * Get the value of email
         */ 
        public function getEmail()
        {
                return $this->email;
        }

        /**
         * Set the value of email
         *
         * @return  self
         */ 
        public function setEmail($email)
        {
                $this->email = $email;

                return $this;
        }

        /**
         * Get the value of nombre
         */ 
        public function getNombre()
        {
                return $this->nombre;
        }

        /**
         * Set the value of nombre
         *
         * @return  self
         */ 
        public function setNombre($nombre)
        {
                $this->nombre = $nombre;

                return $this;
        }

        /**
         * Get the value of perfil
         */ 
        public function getPerfil()
        {
                return $this->perfil;
        }

        /**
         * Set the value of perfil
         *
         * @return  self
         */ 
        public function setPerfil($perfil)
        {
                $this->perfil = $perfil;

                return $this;
        }

        /**
         * Get the value of numVentas
         */ 
        public function getNumVentas()
        {
                return $this->numVentas;
        }

        /**
         * Set the value of numVentas
         *
         * @return  self
         */ 
        public function setNumVentas($numVentas)
        {
                $this->numVentas = $numVentas;

                return $this;
        }
    }
?>