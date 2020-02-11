<?php
	namespace Models;
	
	class Entrada
	{
		private $id;
		private $idCompra;
		private $idFuncion;
		private $qr;

		public function getId()
		{
			return $this->id;
		}

		public function setId($id)
		{
			$this->id = $id;
			return $this;
		}

		public function getIdCompra()
		{
		    return $this->idCompra;
		}

		public function setIdCompra($idCompra)
		{
		    $this->idCompra = $idCompra;
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

		public function getQr()
		{
		    return $this->qr;
		}

		public function setQr($qr)
		{
		    $this->qr = $qr;
		    return $this;
		}
	}
?>