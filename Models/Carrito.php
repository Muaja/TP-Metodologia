<?php
    namespace Models;

    class Carrito
    {
        private $id;
        private $idUsuario;
        private $idFuncion;
        private $cantidad;

        public function getId()
        {
            return $this->id;
        }

        public function setId($id)
        {
            $this->id = $id;
            return $this;
        }

        public function getIdUsuario()
        {
            return $this->idUsuario;
        }

        public function setIdUsuario($idUsuario)
        {
            $this->idUsuario = $idUsuario;
            return $this;
        }

        public function getIdFuncion()
        {
            return $this->idFuncion;
        }

        public function setIdFuncion($idFuncion)
        {
            $this->idFuncion = $idFuncion;
            return $this;
        }

        public function getCantidad()
        {
            return $this->cantidad;
        }

        public function setCantidad($cantidad)
        {
            $this->cantidad = $cantidad;
            return $this;
        }
    }
?>