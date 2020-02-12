<?php
	namespace DAO;

	use \Exception as Exception;
	use Models\Compra as Compra;
	use Models\Funcion as Funcion;

	class CompraDAO
	{
		private $connection;
		private $tableName = "Compras";

		public function add($compra)
		{
			try
			{
				$query = "INSERT INTO ".$this->tableName." (id_usuario, fecha_hora, cantidad, descuento, total) VALUES (:id_usuario, :fecha_hora, :cantidad, :descuento, :total);";
				
				$parameters["id_usuario"]= $compra->getIdUsuario();
				$parameters["fecha_hora"]=$compra->getFechaHora();
				$parameters["cantidad"]=$compra->getCantidad();
				$parameters["descuento"]=$compra->getDescuento();
				$parameters["total"]=$compra->getTotal();

				$this->connection = Connection::GetInstance();
				$this->connection->ExecuteNonQuery($query, $parameters);
				return true;
			}
			catch(Exception $ex)
			{
				return false;
			}
		}

		function remove($compra)
		{
			try
			{
				$query = "UPDATE ".$this->tableName." SET deleted = 1 WHERE id_compra = :id_compra;";
				
				$parameters['id_compra'] = $compra->getId();
				
				$this->connection = Connection::GetInstance();
				$this->connection->ExecuteNonQuery($query, $parameters);
				return true;
			}
			catch(Exception $ex)
			{
				return false;
			}
		}
		
		public function removeByUsuario($usuario)
		{
			try
			{
				$query = "UPDATE ".$this->tableName." SET deleted = 1 WHERE id_usuario = :id_usuario;";
				
				$parameters["id_usuario"]=$usuario->getId();
				
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
				$query = "SELECT * FROM ".$this->tableName." WHERE deleted = 0;";
				$this->connection = Connection::GetInstance();
				$resultSet = $this->connection->Execute($query);
				
				foreach ($resultSet as $row)
				{
					$compra = new Compra();
					$compra->setId($row["id_compra"]);
					$compra->setIdUsuario($row["id_usuario"]);
					$compra->setFechaHora($row["fecha_hora"]);
					$compra->setCantidad($row["cantidad"]);
					$compra->setDescuento($row["descuento"]);
					$compra->setTotal($row["total"]);
					array_push($list, $compra);
				}				
				return $list;
			}
			catch(Exception $ex)
			{
				return null;
			}
		}

		public function getCompra($compra)
		{
			try
			{
				$query = "SELECT * FROM ".$this->tableName." WHERE id_compra = :id_compra AND deleted = 0;";
				$parameters['id_compra'] = $compra->getId();
				$this->connection = Connection::GetInstance();
				$resultSet = $this->connection->Execute($query,$parameters);
				
				foreach ($resultSet as $row)
				{
					$compra->setId($row["id_compra"]);
					$compra->setIdUsuario($row["id_usuario"]);
					$compra->setFechaHora($row["fecha_hora"]);
					$compra->setCantidad($row["cantidad"]);
					$compra->setDescuento($row["descuento"]);
					$compra->setTotal($row["total"]);
					return $compra;
				}
			}
			catch(Exception $ex)
			{
				return null;
			}
		}

		public function getByUsuario($usuario)
		{
			try
			{
				$list = array();
				$query = "SELECT * FROM ".$this->tableName." WHERE id_usuario = :id_usuario AND deleted = 0;";
				$parameters['id_usuario'] = $usuario->getId();
				$this->connection = Connection::GetInstance();
				$resultSet = $this->connection->Execute($query,$parameters);
				
				foreach ($resultSet as $row)
				{
					$compra = new Compra();
					$compra->setId($row["id_compra"]);
					$compra->setIdUsuario($row["id_usuario"]);
					$compra->setFechaHora($row["fecha_hora"]);
					$compra->setCantidad($row["cantidad"]);
					$compra->setDescuento($row["descuento"]);
					$compra->setTotal($row["total"]);
					array_push($list, $compra);
				}				
				return $list;
			}
			catch(Exception $ex)
			{
				return null;
			}
		}

		public function edit($compra)
		{
			try
			{
				$query = "UPDATE ".$this->tableName." SET id_compra = :id_compra, id_usuario = :id_usuario, fecha_hora = :fecha_hora, cantidad = :cantidad, descuento = :descuento, total = :total WHERE id_compra = :id_compra;";

				$parameters["id_usuario"]=$compra->getIdUsuario();
				$parameters["fecha_hora"]=$compra->getFechaHora();
				$parameters["cantidad"]=$compra->getCantidad();
				$parameters["descuento"]=$compra->getDescuento();
				$parameters["total"]=$compra->getTotal();
				$parameters["id_compra"]=$compra->getId();

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