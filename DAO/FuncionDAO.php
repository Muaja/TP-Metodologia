<?php
	namespace DAO;

    use \Exception as Exception;
	use Models\Funcion as Funcion;
	use Models\Genero as Genero;
	use Controllers\Functions as Functions;

	class FuncionDAO
	{
		private $connection;
		private $tableName = "Funciones";
		private $peliculasPorGenerosTableName= "PeliculasXGeneros";

		public function add($funcion)
		{
			try 
			{
				$query = "INSERT INTO ".$this->tableName." (id_cine, id_sala, id_pelicula, fecha_hora) VALUES (:id_cine, :id_sala, :id_pelicula, :fecha_hora);";

				$parameters["id_cine"] = $funcion->getIdCine();
				$parameters["id_sala"] = $funcion->getIdSala();
				$parameters["id_pelicula"] = $funcion->getIdPelicula();
				$parameters["fecha_hora"] = $funcion->getFechaHora();

				$this->connection = Connection::GetInstance();
				$this->connection->ExecuteNonQuery($query, $parameters);
				return true;
			} 
			catch (Exception $ex) 
			{
				return false;
			}
		}

		function remove($funcion)
		{
			try 
			{
				$query = "UPDATE ".$this->tableName." SET deleted = 1 WHERE id_funcion = :id_funcion;";

				$parameters['id_funcion'] = $funcion->getId();

				$this->connection = Connection::GetInstance();
				$this->connection->ExecuteNonQuery($query, $parameters);
				return true;
			} 
			catch (Exception $ex) 
			{
				return false;
			}
		}

		public function getDistinctPeliculas()
		{
			try 
			{
				$list = array();
				$query = "SELECT DISTINCT id_pelicula FROM ".$this->tableName." WHERE deleted = 0;";
				$this->connection = Connection::GetInstance();
				$resultSet = $this->connection->Execute($query);

				foreach ($resultSet as $row) {
					$funcion = new Funcion();
					$funcion->setIdPelicula($row["id_pelicula"]);
					array_push($list, $funcion);
				}
				return $list;
			} 
			catch (Exception $ex) 
			{
				return null;
			}
		}

		public function getDistinctCines()
		{
			try 
			{
				$list = array();
				$query = "SELECT DISTINCT id_cine FROM ".$this->tableName." WHERE deleted = 0;";
				$this->connection = Connection::GetInstance();
				$resultSet = $this->connection->Execute($query);

				foreach ($resultSet as $row) 
				{
					$funcion = new Funcion();
					$funcion->setIdCine($row["id_cine"]);
					array_push($list, $funcion);
				}
				return $list;
			} 
			catch (Exception $ex) 
			{
				return null;
			}
		}

		public function getPeliculasDisponibles()
		{
			try 
			{
				$list = array();
				
				$query = "SELECT DISTINCT id_pelicula FROM ".$this->tableName." WHERE (fecha_hora > now()) AND deleted = 0";
				$this->connection = Connection::GetInstance();
				$resultSet = $this->connection->Execute($query);

				foreach ($resultSet as $row) {
					$funcion = new Funcion();
					$funcion->setIdPelicula($row["id_pelicula"]);
					array_push($list, $funcion);
				}
				return $list;
			} 
			catch (Exception $ex) 
			{
				return null;
			}
		}

		public function getMoviesByGenreAndDate($genre = null, $inicio = null, $fin = null, $now = true)
		{
			try
			{
				$list = array();
				if($inicio != null && $fin == null) 
				{
					$end = date_create($inicio.' 00:00:00');
					date_add($end, date_interval_create_from_date_string('1 days'));
				}

				$query="SELECT DISTINCT f.id_pelicula FROM ".$this->tableName." f";
				if($genre != null) $query=$query." JOIN ".$this->peliculasPorGenerosTableName." pxg ON f.id_pelicula = pxg.id_pelicula";
				$query = $query." WHERE deleted = 0";
				if($genre != null) $query = $query." AND pxg.id_genero = ". $genre;
				if($inicio != null && $fin != null) $query = $query." AND fecha_hora BETWEEN '" . $inicio . "' AND '".$fin."'";
				else if($inicio != null) $query = $query." AND fecha_hora BETWEEN '" . $inicio . "' AND '".date_format($end,"Y-m-d H:i")."'";
				else if($fin != null) $query = $query." AND fecha_hora <= '" . $fin . "'";
				else if($now) $query=$query." AND f.fecha_hora >= now()";
				$query = $query.";";

				$this->connection = Connection::GetInstance();
				$resultSet=$this->connection->Execute($query);

				foreach ($resultSet as $row)
				{
					$funcion = new Funcion();
					$funcion->setIdPelicula($row["id_pelicula"]);
					array_push($list, $funcion);
				}
				return $list;
			}
			catch(Exception $ex)
			{
				return null;
			}
		}

		public function getFuncion($funcion)
		{
			try 
			{
				$query = "SELECT * FROM ".$this->tableName." WHERE id_funcion = '" . $funcion->getId() ."' AND deleted = 0";
				$this->connection = Connection::GetInstance();
				$resultSet = $this->connection->Execute($query);

				foreach ($resultSet as $row) 
				{
					$funcion->setId($row["id_funcion"]);
					$funcion->setIdCine($row["id_cine"]);
					$funcion->setIdSala($row["id_sala"]);
					$funcion->setIdPelicula($row["id_pelicula"]);
					$funcion->setFechaHora($row["fecha_hora"]);
					return $funcion;
				}
			} 
			catch (Exception $ex) 
			{
				return null;
			}
		}

		public function getByCine($cine, $inicio = null, $fin = null)
		{
			try 
			{
				$list = array();

				$query = "SELECT * FROM ".$this->tableName." WHERE id_cine = " . $cine->getId() ." AND deleted = 0";
				if($inicio != null && $fin != null) $query = $query." AND fecha_hora BETWEEN '" . $inicio . "' and '".$fin."'";
				else if($inicio != null) $query = $query." AND fecha_hora >= '" . $inicio . "'";
				else if($fin != null) $query = $query." AND fecha_hora <= '" . $fin . "'";
				$query = $query.";";
				$this->connection = Connection::GetInstance();
				$resultSet = $this->connection->Execute($query);

				foreach ($resultSet as $row) 
				{
					$funcion = new Funcion();
					$funcion->setId($row["id_funcion"]);
					$funcion->setIdCine($row["id_cine"]);
					$funcion->setIdSala($row["id_sala"]);
					$funcion->setIdPelicula($row["id_pelicula"]);
					$funcion->setFechaHora($row["fecha_hora"]);
					array_push($list, $funcion);
				}
				return $list;
			} 
			catch (Exception $ex) 
			{
				return null;
			}
		}

		public function getByCineSala($cine, $sala, $inicio = null, $fin = null)
		{
			try 
			{
				$list = array();

				$query = "SELECT * FROM ".$this->tableName." WHERE id_cine = " . $cine->getId() . " AND id_sala = " . $sala->getId()." AND deleted = 0";
				if($inicio != null && $fin != null) $query = $query." AND fecha_hora BETWEEN '" . $inicio . "' and '".$fin."'";
				else if($inicio != null) $query = $query." AND fecha_hora >= '" . $inicio . "'";
				else if($fin != null) $query = $query." AND fecha_hora <= '" . $fin . "'";
				$query = $query.";";
				$this->connection = Connection::GetInstance();
				$resultSet = $this->connection->Execute($query);

				foreach ($resultSet as $row) 
				{
					$funcion = new Funcion();
					$funcion->setId($row["id_funcion"]);
					$funcion->setIdCine($row["id_cine"]);
					$funcion->setIdSala($row["id_sala"]);
					$funcion->setIdPelicula($row["id_pelicula"]);
					$funcion->setFechaHora($row["fecha_hora"]);
					array_push($list, $funcion);
				}
				return $list;
			} 
			catch (Exception $ex) 
			{
				return null;
			}
		}

		public function getByPelicula($pelicula, $inicio = null, $fin = null, $now = true)
		{
			try 
			{
				$list = array();

				$query = "SELECT * FROM ".$this->tableName." WHERE id_pelicula = " . $pelicula->getId()." AND deleted = 0";
				if($inicio != null && $fin != null) $query = $query." AND fecha_hora BETWEEN '" . $inicio . "' and '".$fin."'";
				else if($inicio != null) $query = $query." AND fecha_hora >= '" . $inicio . "'";
				else if($fin != null) $query = $query." AND fecha_hora <= '" . $fin . "'";
				else if($now) $query = $query." AND fecha_hora >= NOW()";
				$query = $query.";";
				
				$this->connection = Connection::GetInstance();
				$resultSet = $this->connection->Execute($query);

				foreach ($resultSet as $row) 
				{
					$funcion = new Funcion();
					$funcion->setId($row["id_funcion"]);
					$funcion->setIdCine($row["id_cine"]);
					$funcion->setIdSala($row["id_sala"]);
					$funcion->setIdPelicula($row["id_pelicula"]);
					$funcion->setFechaHora($row["fecha_hora"]);
					array_push($list, $funcion);
				}
				return $list;
			} 
			catch (Exception $ex) 
			{
				return null;
			}
		}

		public function getByCinePelicula($cine, $pelicula, $inicio = null, $fin = null)
		{
			try 
			{
				$list = array();

				$query = "SELECT * FROM ".$this->tableName." WHERE id_cine = " . $cine->getId() . " AND id_pelicula = " . $pelicula->getId();
				if($inicio != null && $fin != null) $query = $query." AND fecha_hora BETWEEN '" . $inicio . "' and '".$fin."'";
				else if($inicio != null) $query = $query." AND fecha_hora >= '" . $inicio . "'";
				else if($fin != null) $query = $query." AND fecha_hora <= '" . $fin . "'";
				else $query = $query." AND fecha_hora >= NOW()";
				$query = $query.";";
				$this->connection = Connection::GetInstance();
				$resultSet = $this->connection->Execute($query);

				foreach ($resultSet as $row) 
				{
					$funcion = new Funcion();
					$funcion->setId($row["id_funcion"]);
					$funcion->setIdCine($row["id_cine"]);
					$funcion->setIdSala($row["id_sala"]);
					$funcion->setIdPelicula($row["id_pelicula"]);
					$funcion->setFechaHora($row["fecha_hora"]);
					array_push($list, $funcion);
				}
				return $list;
			} 
			catch (Exception $ex) 
			{
				return null;
			}
		}

		public function getAll($inicio = null, $fin = null)
		{
			try 
			{
				$list = array();

				$query = "SELECT * FROM ".$this->tableName." WHERE deleted = 0";
				if($inicio != null && $fin != null) $query = $query." AND fecha_hora BETWEEN '" . $inicio . "' and '".$fin."'";
				else if($inicio != null) $query = $query." AND fecha_hora >= '" . $inicio . "'";
				else if($fin != null) $query = $query." AND fecha_hora <= '" . $fin . "'";
				$query = $query." ORDER BY fecha_hora ASC;";

				$this->connection = Connection::GetInstance();
				$resultSet = $this->connection->Execute($query);

				foreach ($resultSet as $row) {
					$funcion = new Funcion();
					$funcion->setId($row["id_funcion"]);
					$funcion->setIdCine($row["id_cine"]);
					$funcion->setIdSala($row["id_sala"]);
					$funcion->setIdPelicula($row["id_pelicula"]);
					$funcion->setFechaHora($row["fecha_hora"]);
					array_push($list, $funcion);
				}
				return $list;
			}
			catch (Exception $ex) 
			{
				return null;
			}
		}

		public function checkAvailablePelicula($idPelicula, $fecha)
		{
			try 
			{
				$list = array();
				$query = "SELECT id_cine, id_sala FROM ".$this->tableName." WHERE id_pelicula = " . $idPelicula . " AND deleted = 0 AND fecha_hora LIKE '" . $fecha . "%';";
				$this->connection = Connection::GetInstance();
				$resultSet = $this->connection->Execute($query);

				foreach ($resultSet as $row) 
				{
					$funcion = new Funcion();
					$funcion->setIdCine($row["id_cine"]);
					$funcion->setIdSala($row["id_sala"]);
					array_push($list, $funcion);
				}
				return $list;
			} 
			catch (Exception $ex) 
			{
				return null;
			}
		}

		public function getDistinctCineByPelicula($idPelicula)
		{
			try 
			{
				$list = array();
				$query = "SELECT DISTINCT id_cine, id_pelicula FROM ".$this->tableName." WHERE id_pelicula = '" . $idPelicula . "' AND deleted = 0 AND fecha_hora>= NOW();";
				$this->connection = Connection::GetInstance();
				$resultSet = $this->connection->Execute($query);

				foreach ($resultSet as $row) 
				{
					$funcion = new Funcion();
					$funcion->setIdCine($row["id_cine"]);
					$funcion->setIdPelicula($row["id_pelicula"]);
					array_push($list, $funcion);
				}
				return $list;
			} 
			catch (Exception $ex) 
			{
				return null;
			}
		}

		public function edit($funcion)
		{
			try 
			{
				$query = "UPDATE ".$this->tableName." SET id_cine = :id_cine, id_sala = :id_sala, id_pelicula = :id_pelicula, fecha_hora = :fecha_hora WHERE id_funcion = :id_funcion;";

				$parameters["id_cine"] = $funcion->getIdCine();
				$parameters["id_sala"] = $funcion->getIdSala();
				$parameters["id_pelicula"] = $funcion->getIdPelicula();
				$parameters["fecha_hora"] = $funcion->getFechaHora();
				$parameters["id_funcion"] = $funcion->getId();

				$this->connection = Connection::GetInstance();
				$this->connection->ExecuteNonQuery($query, $parameters);
				return true;
			} 
			catch (Exception $ex) 
			{
				return false;
			}
		}
			
	}
?>
