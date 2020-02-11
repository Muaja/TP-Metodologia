<?php
	namespace Models;
	
	class Usuario
	{
		private $id;
		private $dni;
		private $password;
		private $email;
		private $apellido;
		private $nombre;
		private $id_Rol;
		private $ip;
		private $registerDate;
		private $lastConnection;
		private $loggedIn;
		private $image;
		private $facebookId;

		public function getId()
		{
			return $this->id;
		}

		public function setId($id)
		{
			$this->id = $id;
			return $this;
		}

		public function getDni()
		{
			return $this->dni;
		}

		public function setDni($dni)
		{
			$this->dni = $dni;
			return $this;
		}

		public function getPassword()
		{
			return $this->password;
		}

		public function setPassword($password)
		{
			$this->password = $password;
			return $this;
		}

		public function getEmail()
		{
			return $this->email;
		}

		public function setEmail($email)
		{
			$this->email = $email;
			return $this;
		}

		public function getApellido()
		{
			return $this->apellido;
		}

		public function setApellido($apellido)
		{
			$this->apellido = $apellido;
			return $this;
		}

		public function getNombre()
		{
			return $this->nombre;
		}

		public function setNombre($nombre)
		{
			$this->nombre = $nombre;
			return $this;
		}

		public function getId_Rol()
		{
			return $this->id_Rol;
		}

		public function setId_Rol($id_Rol)
		{
			$this->id_Rol = $id_Rol;
			return $this;
		}

		public function getIp()
		{
			return $this->ip;
		}

		public function setIp($ip)
		{
			$this->ip = $ip;
			return $this;
		}

		public function getRegisterDate()
		{
			return $this->registerDate;
		}

		public function setRegisterDate($registerDate)
		{
			$this->registerDate = $registerDate;
			return $this;
		}

		public function getLastConnection()
		{
			return $this->lastConnection;
		}

		public function setLastConnection($lastConnection)
		{
			$this->lastConnection = $lastConnection;
			return $this;
		}

		public function getLoggedIn()
		{
			return $this->loggedIn;
		}

		public function setLoggedIn($loggedIn)
		{
			$this->loggedIn = $loggedIn;
			return $this;
		}

		public function getImage($noRoot = false)
		{
			return ($this->getFacebookId() == null && !$noRoot) ? FRONT_ROOT.$this->image : $this->image;
		}

		public function setImage($image)
		{
			$this->image = $image;
			return $this;
		}

		public function getFacebookId()
		{
			return $this->facebookId;
		}

		public function setFacebookId($facebookId)
		{
			$this->facebookId = $facebookId;
			return $this;
		}
	}
?>