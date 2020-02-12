<?php
	namespace DAO;
	
	use \Exception as Exception;
    use DAO\Connection as Connection;
	use Models\Carrito as Carrito;

	class CarritoDAO
	{
		private $connection;
		private $tableName = "Carritos";
		
		public function add($carrito)
		{
			try
			{
				$query = "INSERT INTO ".$this->tableName." (id_usuario, id_funcion, cantidad) VALUES (:id_usuario, :id_funcion, :cantidad);";
				
				$parameters["id_usuario"]=$carrito->getIdUsuario();
				$parameters["id_funcion"]=$carrito->getIdFuncion();
				$parameters["cantidad"]=$carrito->getCantidad();

				$this->connection = Connection::GetInstance();
				$this->connection->ExecuteNonQuery($query, $parameters);
				return true;
			}
			catch(Exception $ex)
			{
				return false;
			}
		}

		function remove($carrito)
		{
			try
			{
				$query = "UPDATE ".$this->tableName." SET deleted = 1 WHERE id_carrito = :id_carrito;";

				$parameters["id_carrito"]=$carrito->getId();
				
				$this->connection = Connection::GetInstance();
				$this->connection->ExecuteNonQuery($query, $parameters);
				return true;
			}
			catch(Exception $ex)
			{
				return false;
			}
		}

		public function getAll()
		{
			try
			{
				$list = array();
				$query = "SELECT * FROM ".$this->tableName." WHERE deleted = 0 ORDER BY id_carrito ASC;";
				$this->connection = Connection::GetInstance();
				$resultSet = $this->connection->Execute($query);
				
				foreach ($resultSet as $row)
				{
					$carrito = new Carrito();
					$carrito->setId($row["id_carrito"]);
					$carrito->setIdUsuario($row["id_usuario"]);
					$carrito->setIdFuncion($row["id_funcion"]);
					$carrito->setCantidad($row["cantidad"]);
					array_push($list, $carrito);
				}				
				return $list;
			}
			catch(Exception $ex)
			{
				return null;
			}
		}

		public function getCarrito($carrito)
		{
			try
			{
				$query = "SELECT * FROM ".$this->tableName." WHERE id_carrito = :id_carrito AND deleted = 0;";
				$parameters["id_carrito"]=$carrito->getId();
				$this->connection = Connection::GetInstance();
				$resultSet = $this->connection->Execute($query,$parameters);
				
				foreach ($resultSet as $row)
				{
					$carrito->setId($row["id_carrito"]);
					$carrito->setIdUsuario($row["id_usuario"]);
					$carrito->setIdFuncion($row["id_funcion"]);
					$carrito->setCantidad($row["cantidad"]);
					return $carrito;
				}
			}
			catch(Exception $ex)
			{
				return null;
			}
		}

		public function getByIdUsuario($carrito)
		{
			try
			{
				$list = array();
				$query = "SELECT * FROM ".$this->tableName." WHERE id_usuario = :id_usuario AND deleted = 0;";
				$parameters["id_usuario"]=$carrito->getIdUsuario();
				$this->connection = Connection::GetInstance();
				$resultSet = $this->connection->Execute($query,$parameters);
				
				foreach ($resultSet as $row)
				{
					$carrito = new Carrito();
					$carrito->setId($row["id_carrito"]);
					$carrito->setIdUsuario($row["id_usuario"]);
					$carrito->setIdFuncion($row["id_funcion"]);
					$carrito->setCantidad($row["cantidad"]);
					array_push($list, $carrito);
				}
				return $list;
			}
			catch(Exception $ex)
			{
				return null;
			}
		}

		public function edit($carrito)
		{
			try
			{
				$query = "UPDATE ".$this->tableName." SET id_usuario = :id_usuario, id_funcion = :id_funcion, cantidad = :cantidad WHERE id_carrito = :id_carrito;";

				$parameters["id_usuario"]= $carrito->getIdUsuario();
				$parameters["id_funcion"]= $carrito->getIdFuncion();
				$parameters["cantidad"]= $carrito->getCantidad();
				$parameters["id_carrito"] = $carrito->getId();

				$this->connection = Connection::GetInstance();
				$this->connection->ExecuteNonQuery($query, $parameters);
				return true;
			}
			catch(Exception $ex)
			{
				return false;
			}
		}
	}
?>