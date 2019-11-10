<?php
	namespace Controllers;

	use DAO\CompraDAO as CompraDAO;
	use DAO\CineDAO as CineDAO;
	use DAO\SalaDAO as SalaDAO;
	use DAO\FuncionDAO as FuncionDAO;
	use DAO\PeliculaDAO as PeliculaDAO;
	use DAO\EntradaDAO as EntradaDAO;
	use Models\Compra as Compra;
	use Models\Cine as Cine;
	use Models\Sala as Sala;
	use Models\Funcion as Funcion;
	use Models\Usuario as Usuario;
	use Models\Entrada as Entrada;
	use Models\Pelicula as Pelicula;

	class CompraController extends Administrable
	{
		private $compraDAO;
		private $cineDAO;
		private $salaDAO;
		private $funcionDAO;
		private $peliculaDAO;
		private $entradaDAO;

		function __construct()
		{
			$this->compraDAO = new CompraDAO();
			$this->cineDAO = new CineDAO();
			$this->salaDAO = new SalaDAO();
			$this->funcionDAO = new FuncionDAO();
			$this->peliculaDAO = new PeliculaDAO();
			$this->entradaDAO = new EntradaDAO();
		}

		public function Pay($idFuncion,$cantidad)
		{
			if(!$this->loggedIn()) Functions::redirect("Home");

			//Datos funcion
			$funcion = new Funcion();
			$funcion->setId($idFuncion);
			$funcion = $this->funcionDAO->getFuncion($funcion);
			if($funcion == null)
			{
				Functions::flash("La funcion seleccionada no existe.","warning");
				Functions::redirect("Home");
			}

			//Datos pelicula
			$pelicula = new Pelicula();
			$pelicula->setId($funcion->getIdPelicula());
			$pelicula = $this->peliculaDAO->getPelicula($pelicula);

			$fechaHora = $funcion->getFechaHora();

			//Datos cine			
			$idCine = $funcion->getIdCine();
			$cine = new Cine();
			$cine->setId($idCine);
			$cine = $this->cineDAO->getCine($cine);

			//Datos sala
			$idSala = $funcion->getIdSala();
			$sala = new Sala();
			$sala->setId($idSala);
			$sala = $this->salaDAO->getSala($sala);
			$precio = $sala->getPrecio();

			//Calculos
			$subtotal = ($precio*$cantidad);
			$descuento = $this->calcularDescuento($fechaHora, $cantidad);
			$total = $subtotal*($descuento/100);

			require_once(VIEWS_PATH."compra/compra.php");
		}

		public function Payout($idFuncion,$cantidad,$name,$mmyy,$number,$cvc)
		{
			if(!$this->loggedIn()) Functions::redirect("Home");
			
			$name = Functions::validateData($name);
			$mmyy = Functions::validateData($mmyy);
			$number = Functions::validateData($number);
			$cvc = Functions::validateData($cvc);
			if(!$this->validatePay($name,$mmyy,$number,$cvc))
			{			
				$params = array();
				array_push($params,$idFuncion);
				array_push($params,$cantidad);
				Functions::flash("Los datos de la tarjeta son incorrectos.","warning");
				Functions::redirect("Compra","Pay",$params);
			}

			//Datos funcion
			$funcion = new Funcion();
			$funcion->setId($idFuncion);
			$funcion = $this->funcionDAO->getFuncion($funcion);
			if($funcion == null)
			{
				Functions::flash("La funcion seleccionada no existe.","warning");
				Functions::redirect("Home");
			}

			//Datos pelicula
			$pelicula = new Pelicula();
			$pelicula->setId($funcion->getIdPelicula());
			$pelicula = $this->peliculaDAO->getPelicula($pelicula);
			if($pelicula == null)
			{
				Functions::flash("La pelicula de la funcion no existe.","warning");
				Functions::redirect("Home");
			}

			$fechaHora = $funcion->getFechaHora();

			//Datos cine			
			$idCine = $funcion->getIdCine();
			$cine = new Cine();
			$cine->setId($idCine);
			$cine = $this->cineDAO->getCine($cine);
			if($cine == null)
			{
				Functions::flash("El cine de la funcion no existe.","warning");
				Functions::redirect("Home");
			}

			//Datos sala
			$idSala = $funcion->getIdSala();
			$sala = new Sala();
			$sala->setId($idSala);
			$sala = $this->salaDAO->getSala($sala);
			$precio = $sala->getPrecio();

			//Calculos
			$descuento = $this->calcularDescuento($fechaHora, $cantidad);
			$total = ($precio*$cantidad)*($descuento/100);

			//Guardar compra
			$compra = new Compra();
			$compra->setIdUsuario($_SESSION['loggedUser']->getId());
			$compra->setFechaHora(date("Y-m-d H:i:s"));
			$compra->setPrecio($precio);
			$compra->setCantidad($cantidad);
			$compra->setDescuento($descuento);
			$compra->setTotal($total);
			if(!$this->compraDAO->add($compra)) 
			{
				Functions::flash("Se produjo un error al registrar la compra. Tu pago será devuelto.","danger");
				Functions::redirect("Funcion","ShowFuncionesPelicula", $idPelicula);
			}			

			//Generar entradas
			$listCompras = $this->compraDAO->getByUsuario($_SESSION['loggedUser']);
			$compra = array_pop($listCompras);
			$idCompra = $compra->getId();
			for ($i = 1; $i <= $cantidad; $i++)
			{
				$entrada = new Entrada();
				$entrada->setIdCompra($idCompra);
				$entrada->setIdFuncion($idFuncion);
				$entrada->setQr($idCine."-".$idSala."-".$idFuncion."-".$idCompra."-".$i);
				if(!$this->entradaDAO->add($entrada)) Functions::flash("Se produjo un error al registrar la entrada ".$i.".","danger");
			}
			Functions::flash("Se completo la compra de ".$cantidad." entrada(s) para ver ".$pelicula->getTitulo()."!", "success");
			Functions::redirect("Entrada","ShowListView", $_SESSION['loggedUser']->getId());
		}

		private function validatePay($name,$mmyy,$number,$cvc)
		{
			//Validamos numeros de la tarjeta
			$validateCard = CreditCard::validCreditCard($number);
			if($validateCard['valid'] == false) return false;

			//Validamos codigo de seguridad
			$validateCvc = CreditCard::validCvc($cvc, $validateCard['type']);
			if($validateCvc == false) return false;

			//Validamos fecha de expiracion
			$date = explode(" / ", $mmyy);
			$validateDate = CreditCard::validDate("20".$date[1], $date[0]);
			if(!$validateDate) return false;

			//Si pasa todas las validaciones procesamos la compra
			Functions::flash("Tu compra con tarjeta ".$validateCard['type']." fue procesada con éxito.","success");
			return true;
		}
		
		private function calcularDescuento($fechaHora, $cantidad)
		{
			$descuento = 100;
			$day = date('w', strtotime($fechaHora));
			if(($day == 2 || $day == 3) && $cantidad >= 2) $descuento = 25;
			return $descuento;
		}
	}
?>