<?php
    namespace Controllers;

    use DAO\CarritoDAO as CarritoDAO;
    use DAO\CineDAO as CineDAO;
	use DAO\SalaDAO as SalaDAO;
	use DAO\FuncionDAO as FuncionDAO;
	use DAO\PeliculaDAO as PeliculaDAO;
    use Models\Carrito as Carrito;
    use Models\Cine as Cine;
	use Models\Sala as Sala;
	use Models\Funcion as Funcion;
	use Models\Pelicula as Pelicula;

    class CarritoController extends Administrable
    {
        private $carritoDAO;
        private $cineDAO;
		private $salaDAO;
		private $funcionDAO;
		private $peliculaDAO;

        public function __construct()
        {
            $this->carritoDAO = new CarritoDAO();
            $this->cineDAO = new CineDAO();
			$this->salaDAO = new SalaDAO();
			$this->funcionDAO = new FuncionDAO();
			$this->peliculaDAO = new PeliculaDAO();
        }

        public function ShowCarritoView()
        {
            if (!$this->loggedIn()) Functions::redirect("Home");
            $carrito = new Carrito();
            $carrito->setIdUsuario($_SESSION['loggedUser']->getId());
            $carritoList = $this->carritoDAO->getByIdUsuario($carrito);
            $funcion = new Funcion();
            $pelicula = new Pelicula();
            $cine = new Cine();
            $sala = new Sala();
            $_SESSION['carrito'] = $carritoList;
            require_once(VIEWS_PATH . "carrito/carrito.php");
        }

        public function Remove($id)
		{
			if (!$this->loggedIn()) Functions::redirect("Home");

			$carrito = new Carrito();
			$carrito->setId($id);
			$carrito = $this->carritoDAO->getCarrito($carrito);

			if($this->carritoDAO->remove($carrito) != null) Functions::flash("El item se ha eliminado correctamente del carrito.","success");
			else Functions::flash("Se produjo un error al eliminar el item del carrito.", "danger");
			Functions::redirect("Carrito", "ShowCarritoView");
		}

		public function Add($idFuncion, $cantidad)
		{
			if (!$this->loggedIn()) Functions::redirect("Home");

            $carrito = new Carrito();
            $carrito->setIdUsuario($_SESSION['loggedUser']->getId());
			$carrito->setIdFuncion($idFuncion);
			$carrito->setCantidad($cantidad);

			if($this->carritoDAO->add($carrito) != null) Functions::flash("La compra se agrego al carrito correctamente.","success");
            else Functions::flash("Se produjo un error al agregar la compra al carrito.","danger");
            Functions::redirect("Funcion", "ShowMovies");
		}
    }
?>