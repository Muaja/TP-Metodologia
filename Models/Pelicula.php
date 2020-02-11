<?php
	namespace Models;

	class Pelicula
	{
		private $id;
		private $idTMDB;
		private $titulo;
		private $generos;
		private $duracion;
		private $descripcion;
		private $idioma;
		private $clasificacion;
		private $fechaDeEstreno;
		private $poster;
		private $video;
		private $popularidad;

		public function getId()
		{
			return $this->id;
		}

		public function setId($id)
		{
			$this->id = $id;
			return $this;
		}

		public function getIdTMDB()
		{
			return $this->idTMDB;
		}

		public function setIdTMDB($idTMDB)
		{
			$this->idTMDB = $idTMDB;
			return $this;
		}

		public function getTitulo()
		{
			return $this->titulo;
		}

		public function setTitulo($titulo)
		{
			$this->titulo = $titulo;
			return $this;
		}

		public function getGeneros()
		{
			return $this->generos;
		}

		public function setGeneros($generos)
		{
			$this->generos = $generos;
			return $this;
		}

		public function getDuracion()
		{
			return $this->duracion;
		}

		public function setDuracion($duracion)
		{
			$this->duracion = $duracion;
			return $this;
		}

		public function getDescripcion()
		{
			return $this->descripcion;
		}

		public function setDescripcion($descripcion)
		{
			$this->descripcion = $descripcion;
			return $this;
		}

		public function getIdioma()
		{
			return $this->idioma;
		}

		public function setIdioma($idioma)
		{
			$this->idioma = $idioma;
			return $this;
		}

		public function getClasificacion()
		{
			return $this->clasificacion;
		}

		public function setClasificacion($clasificacion)
		{
			$this->clasificacion = $clasificacion;
			return $this;
		}

		public function getFechaDeEstreno()
		{
			return $this->fechaDeEstreno;
		}

		public function setFechaDeEstreno($fechaDeEstreno)
		{
			$this->fechaDeEstreno = $fechaDeEstreno;
			return $this;
		}

		public function getPoster()
		{
			return (strpos($this->poster, 'https://image.tmdb.org') === false) ? FRONT_ROOT.$this->poster : $this->poster;
		}

		public function setPoster($poster)
		{
			$this->poster = $poster;
			return $this;
		}

		public function getVideo()
		{
			return $this->video;
		}

		public function setVideo($video)
		{
			$this->video = $video;
			return $this;
		}

		public function getPopularidad()
		{
			return $this->popularidad;
		}

		public function setPopularidad($popularidad)
		{
			$this->popularidad = $popularidad;
			return $this;
		}
	}
?>