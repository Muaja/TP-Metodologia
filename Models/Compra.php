<?php
	namespace Models;

	class Compra
	{
		private $id;
		private $idUsuario;
		private $fechaHora;
		private $precio;
		private $cantidad;
		private $descuento;
		private $total;

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

		public function getFechaHora()
		{
			return $this->fechaHora;
		}

		public function setFechaHora($fechaHora)
		{
			$this->fechaHora = $fechaHora;
			return $this;
		}

		public function getPrecio()
		{
			return $this->precio;
		}

		public function setPrecio($precio)
		{
			$this->precio = $precio;
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

		public function getDescuento()
		{
			return $this->descuento;
		}

		public function setDescuento($descuento)
		{
			$this->descuento = $descuento;
			return $this;
		}

		public function getTotal()
		{
			return $this->total;
		}

		public function setTotal($total)
		{
			$this->total = $total;
			return $this;
		}
	}
?>