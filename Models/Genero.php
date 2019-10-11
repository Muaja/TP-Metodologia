<?php
	/**
	 * @author Guille
	 * @version 1.0
	 * @created 06-oct.-2019 19:07:11
	 */
	namespace Models;

	class Genero
	{

		private $id;
		private $nombre;

		/**
		 * Getter for Id
		 *
		 * @return [type]
		 */
		public function getId()
		{
			return $this->id;
		}
	
		/**
		 * Setter for Id
		* @var [type] id
		*
		* @return self
		*/
		public function setId($id)
		{
			$this->id = $id;
			return $this;
		}
	
		/**
		 * Getter for Nombre
		*
		* @return [type]
		*/
		public function getNombre()
		{
			return $this->nombre;
		}
	
		/**
		 * Setter for Nombre
		* @var [type] nombre
		*
		* @return self
		*/
		public function setNombre($nombre)
		{
			$this->nombre = $nombre;
			return $this;
		}
	}
?>