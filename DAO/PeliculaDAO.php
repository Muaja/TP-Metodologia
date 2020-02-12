<?php
	namespace DAO;

	use \Exception as Exception;
	use Models\Pelicula as Pelicula;
	use Models\Genero as Genero;
	use DAO\Connection as Connection;

	class PeliculaDAO
	{
		private $connection;
		private $tableName = "Peliculas";
		private $generoTableName = "PeliculasXGeneros";

		public function getAll()
		{
			try 
			{
				$list = array();
				$query = "SELECT * FROM ".$this->tableName." WHERE deleted = 0 ORDER BY titulo ASC;";
				$this->connection = Connection::GetInstance();
				$resultSet = $this->connection->Execute($query);

				foreach ($resultSet as $row) 
				{
					$pelicula = new Pelicula();
					$pelicula->setId($row["id_pelicula"]);
					$pelicula->setIdTMDB($row["id_TMDB"]);
					$pelicula->setTitulo($row["titulo"]);
					$generos = $this->getGeneros($pelicula);
					$pelicula->setGeneros($generos);
					$pelicula->setDuracion($row["duracion"]);
					$pelicula->setDescripcion($row["descripcion"]);
					$pelicula->setIdioma($row["idioma"]);
					$pelicula->setClasificacion($row["clasificacion"]);
					$pelicula->setFechaDeEstreno($row["fechaDeEstreno"]);
					$pelicula->setPoster($row["poster"]);
					$pelicula->setVideo($row["video"]);
					$pelicula->setPopularidad($row["popularidad"]);
					array_push($list, $pelicula);
				}
				return $list;
			} 
			catch (Exception $ex) 
			{
				return null;
			}
		}

		public function add($pelicula)
		{
			try 
			{
				$query = "INSERT INTO ".$this->tableName." (id_TMDB, titulo, duracion, descripcion, idioma, clasificacion, fechaDeEstreno, poster, video, popularidad) VALUES (:id_TMDB, :titulo, :duracion, :descripcion, :idioma, :clasificacion, :fechaDeEstreno, :poster, :video, :popularidad);";
				$parameters["id_TMDB"] = $pelicula->getIdTMDB();
				$parameters["titulo"] = $pelicula->getTitulo();
				$parameters["duracion"] = $pelicula->getDuracion();
				$parameters["descripcion"] = $pelicula->getDescripcion();
				$parameters["idioma"] = $pelicula->getIdioma();
				$parameters["clasificacion"] = $pelicula->getClasificacion();
				$parameters["fechaDeEstreno"] = $pelicula->getFechaDeEstreno();
				$parameters["poster"] = $pelicula->getPoster();
				$parameters["video"] = $pelicula->getVideo();
				$parameters["popularidad"] = $pelicula->getPopularidad();

				$this->connection = Connection::GetInstance();
				$this->connection->ExecuteNonQuery($query, $parameters);

				$generos = $pelicula->getGeneros();
				$pelicula = $this->getByIdTMDB($pelicula->getIdTMDB()); // Get ID to save genres
				$this->saveGeneros($pelicula, $generos);
				return true;
			} 
			catch (Exception $ex) 
			{
				return false;
			}
		}

		function remove($pelicula)
		{
			try 
			{
				$parameters["id_pelicula"] = $pelicula->getId();

				$query = "UPDATE ".$this->tableName." SET deleted = 1 WHERE id_pelicula = :id_pelicula;";				

				$this->connection = Connection::GetInstance();
				$this->connection->ExecuteNonQuery($query,$parameters);

				$query = "UPDATE ".$this->generoTableName." SET deleted = 1 WHERE id_pelicula = :id_pelicula;";

				$this->connection = Connection::GetInstance();
				$this->connection->ExecuteNonQuery($query,$parameters);
				return true;
			} 
			catch (Exception $ex) 
			{
				return false;
			}
		}

		public function getPelicula($pelicula)
		{
			try
			{
				$query = "SELECT * FROM ".$this->tableName." WHERE id_pelicula = :id_pelicula AND deleted = 0;";
				$parameters["id_pelicula"] = $pelicula->getId();
				$this->connection = Connection::GetInstance();
				$resultSet = $this->connection->Execute($query,$parameters);

				foreach ($resultSet as $row) 
				{
					$pelicula->setId($row["id_pelicula"]);
					$pelicula->setIdTMDB($row["id_TMDB"]);
					$pelicula->setTitulo($row["titulo"]);
					$generos = $this->getGeneros($pelicula);
					$pelicula->setGeneros($generos);
					$pelicula->setDuracion($row["duracion"]);
					$pelicula->setDescripcion($row["descripcion"]);
					$pelicula->setIdioma($row["idioma"]);
					$pelicula->setClasificacion($row["clasificacion"]);
					$pelicula->setFechaDeEstreno($row["fechaDeEstreno"]);
					$pelicula->setPoster($row["poster"]);
					$pelicula->setVideo($row["video"]);
					$pelicula->setPopularidad($row["popularidad"]);
					return $pelicula;
				}			
			} 
			catch (Exception $ex) 
			{
				return null;
			}
		}

		public function getGeneros($pelicula)
		{
			try
			{
				$query = "SELECT * FROM ".$this->generoTableName." WHERE id_pelicula = :id_pelicula AND deleted = 0;";
				$parameters["id_pelicula"] = $pelicula->getId();
				$this->connection = Connection::GetInstance();
				$resultSet = $this->connection->Execute($query,$parameters);

				$generos = array();
				foreach ($resultSet as $row) 
				{
					array_push($generos, $row["id_genero"]);
				}
				return $generos;
			} 
			catch (Exception $ex) 
			{
				return null;
			}
		}

		public function saveGeneros($pelicula, $generos)
		{
			try
			{
				foreach($generos as $genero)
				{
					$query = "INSERT INTO ".$this->generoTableName." (id_pelicula, id_genero) VALUES (:id_pelicula, :id_genero);";
					$parameters["id_pelicula"] = $pelicula->getId();
					$parameters["id_genero"] = $genero;

					$this->connection = Connection::GetInstance();
					$this->connection->ExecuteNonQuery($query, $parameters);
				}
			} 
			catch (Exception $ex) 
			{
				throw $ex;
			}
		}

		public function getByIdTMDB($pelicula)
		{
			try
			{
				$query = "SELECT * FROM ".$this->tableName." WHERE id_TMDB = :id_TMDB AND deleted = 0;";
				$parameters["id_TMDB"] = $pelicula->getIdTMDB();
				$this->connection = Connection::GetInstance();
				$resultSet = $this->connection->Execute($query,$parameters);

				foreach ($resultSet as $row) 
				{
					$pelicula = new Pelicula();
					$pelicula->setId($row["id_pelicula"]);
					$pelicula->setIdTMDB($row["id_TMDB"]);
					$pelicula->setTitulo($row["titulo"]);
					$generos = $this->getGeneros($pelicula);
					$pelicula->setGeneros($generos);
					$pelicula->setDuracion($row["duracion"]);
					$pelicula->setDescripcion($row["descripcion"]);
					$pelicula->setIdioma($row["idioma"]);
					$pelicula->setClasificacion($row["clasificacion"]);
					$pelicula->setFechaDeEstreno($row["fechaDeEstreno"]);
					$pelicula->setPoster($row["poster"]);
					$pelicula->setVideo($row["video"]);
					$pelicula->setPopularidad($row["popularidad"]);
					return $pelicula;
				}			
			} 
			catch (Exception $ex) 
			{
				return null;
			}
		}

		public function edit($pelicula)
		{
			try 
			{
				$query = "UPDATE ".$this->tableName." SET id_TMDB = :id_TMDB, titulo = :titulo, duracion = :duracion, descripcion = :descripcion, idioma = :idioma, clasificacion = :clasificacion, fechaDeEstreno = :fechaDeEstreno, poster = :poster, video = :video, popularidad = :popularidad WHERE id_pelicula = :id_pelicula;";
				$parameters["id_TMDB"] = $pelicula->getIdTMDB();
				$parameters["titulo"] = $pelicula->getTitulo();
				$parameters["duracion"] = $pelicula->getDuracion();
				$parameters["descripcion"] = $pelicula->getDescripcion();
				$parameters["idioma"] = $pelicula->getIdioma();
				$parameters["clasificacion"] = $pelicula->getClasificacion();
				$parameters["fechaDeEstreno"] = $pelicula->getFechaDeEstreno();
				$parameters["poster"] = $pelicula->getPoster();
				$parameters["video"] = $pelicula->getVideo();
				$parameters["popularidad"] = $pelicula->getPopularidad();
				$parameters["id_pelicula"] = $pelicula->getId();

				$this->connection = Connection::GetInstance();
				$this->connection->ExecuteNonQuery($query, $parameters);
			} 
			catch (Exception $ex) 
			{
				throw $ex;
			}
		}
	}
?>