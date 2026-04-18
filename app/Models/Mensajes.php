<?php
class Mensajes{
    private $id, $fechaC, $texto, $emisor, $libro;
    
    
    public function __construct($id, $fechaC, $texto, $emisor, $libro)
    {
         $this->id=$id;
         $this->fechaC=$fechaC;
         $this->texto=$texto; 
         $this->emisor=$emisor; 
         $this->libro=$libro;
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
     * Get the value of texto
     */ 
    public function getTexto()
    {
        return $this->texto;
    }

    /**
     * Set the value of texto
     *
     * @return  self
     */ 
    public function setTexto($texto)
    {
        $this->texto = $texto;

        return $this;
    }

    /**
     * Get the value of emisor
     */ 
    public function getEmisor()
    {
        return $this->emisor;
    }

    /**
     * Set the value of emisor
     *
     * @return  self
     */ 
    public function setEmisor($emisor)
    {
        $this->emisor = $emisor;

        return $this;
    }

    /**
     * Get the value of libro
     */ 
    public function getLibro()
    {
        return $this->libro;
    }

    /**
     * Set the value of libro
     *
     * @return  self
     */ 
    public function setLibro($libro)
    {
        $this->libro = $libro;

        return $this;
    }
}
?>