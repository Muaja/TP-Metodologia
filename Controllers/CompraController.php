<?php
	namespace Controllers;

	use DAO\CompraDAO as CompraDAO;
	use DAO\CineDAO as CineDAO;
	use DAO\SalaDAO as SalaDAO;
	use DAO\FuncionDAO as FuncionDAO;
	use DAO\PeliculaDAO as PeliculaDAO;
	use DAO\EntradaDAO as EntradaDAO;
	use DAO\CarritoDAO as CarritoDAO;
	use Models\Compra as Compra;
	use Models\Cine as Cine;
	use Models\Sala as Sala;
	use Models\Funcion as Funcion;
	use Models\Usuario as Usuario;
	use Models\Entrada as Entrada;
	use Models\Pelicula as Pelicula;
	use Models\Carrito as Carrito;

	class CompraController extends Administrable
	{
		private $compraDAO;
		private $cineDAO;
		private $salaDAO;
		private $funcionDAO;
		private $peliculaDAO;
		private $entradaDAO;
		private $carritoDAO;

		function __construct()
		{
			$this->compraDAO = new CompraDAO();
			$this->cineDAO = new CineDAO();
			$this->salaDAO = new SalaDAO();
			$this->funcionDAO = new FuncionDAO();
			$this->peliculaDAO = new PeliculaDAO();
			$this->entradaDAO = new EntradaDAO();
			$this->carritoDAO = new CarritoDAO();
		}

		public function Submit($idFuncion,$cantidad,$action)
		{
			if (!$this->loggedIn()) Functions::redirect("Login");

			if($action == "ADD")
			{
				$params = array();
				array_push($params,$idFuncion);
				array_push($params,$cantidad);
				Functions::redirect("Carrito","Add",$params);
			}
			else if($action == "BUY")
			{
				$carrito = new Carrito();
				$carrito->setIdUsuario($_SESSION['loggedUser']->getId());
				$carrito->setIdFuncion($idFuncion);
				$carrito->setCantidad($cantidad);
				$this->ShowPayView($carrito);
			}
		}

		public function ShowPayView($carrito = null)
		{
			if(!$this->loggedIn()) Functions::redirect("Home");

			if($carrito == null)
			{
				$carritoList = $_SESSION['carrito'];
			}
			else
			{
				$carritoList = array();
				array_push($carritoList,$carrito);
			}
			$funcion = new Funcion();
			$pelicula = new Pelicula();
			$cine = new Cine();
			$sala = new Sala();
			$_SESSION['carrito'] = $carritoList;
			require_once(VIEWS_PATH."compra/compra.php");
		}

		public function Pay($name,$mmyy,$number,$cvc)
		{
			if(!$this->loggedIn()) Functions::redirect("Home");

			$name = Functions::validateData($name);
			$mmyy = Functions::validateData($mmyy);
			$number = Functions::validateData($number);
			$cvc = Functions::validateData($cvc);
			if(!$this->validatePay($name,$mmyy,$number,$cvc))
			{			
				Functions::flash("Los datos de la tarjeta son incorrectos. Intenta nuevamente","warning");
				Functions::redirect("Compra","ShowPayView");
			}
			
			$carritoList = $_SESSION['carrito'];
			$funcion = new Funcion();
			$pelicula = new Pelicula();
			$cine = new Cine();
			$sala = new Sala();			

			//Calculos
			$subtotal = 0;
			$descuento= 0;
			$total = 0;
			$cantidadtotal = 0;
			foreach($carritoList as $carrito)
			{
				$idFuncion = $carrito->getIdFuncion();
				$cantidad = $carrito->getCantidad();

				//Datos funcion
				$funcion->setId($idFuncion);
				$funcion = $this->funcionDAO->getFuncion($funcion);

				//Datos pelicula
				$idPelicula = $funcion->getIdPelicula();
				$pelicula->setId($idPelicula);
				$pelicula = $this->peliculaDAO->getPelicula($pelicula);

				//Datos cine			
				$idCine = $funcion->getIdCine();
				$cine->setId($idCine);
				$cine = $this->cineDAO->getCine($cine);

				//Datos sala
				$idSala = $funcion->getIdSala();
				$sala->setId($idSala);
				$sala = $this->salaDAO->getSala($sala);

				//Calculos
				$subtotalcarrito = ($sala->getPrecio()*$cantidad);
				$descuentocarrito = $subtotalcarrito*($this->calcularPorcDescuento($funcion->getFechaHora(), $cantidad)/100);

				$subtotal += $subtotalcarrito;
				$descuento += $descuentocarrito;
				$total += ($subtotalcarrito-$descuentocarrito);
				$cantidadtotal += $cantidad;

				//Generar entradas
				$listCompras = $this->compraDAO->getByUsuario($_SESSION['loggedUser']);
				if($listCompras == null) 
				{
					Functions::flash("Se produjo un error al registrar la compra. Tu pago será devuelto.","danger");
					Functions::redirect("Funcion","ShowFuncionesPelicula", $idPelicula);
				}
				$compra = array_pop($listCompras);
				$idCompra = $compra->getId();

				$listEntradas = array();

				for ($i = 0; $i < $cantidad; $i++)
				{
					$entrada = new Entrada();
					$entrada->setIdCompra($idCompra);
					$entrada->setIdFuncion($idFuncion);
					$entrada->setQr($idCine."-".$idSala."-".$idFuncion."-".$idCompra."-".$i);
					array_push($listEntradas, $entrada);
					if(!$this->entradaDAO->add($entrada)) Functions::flash("Se produjo un error al registrar la entrada de ".$pelicula->getTitulo().".","danger");	
					else Functions::flash("Se emitió una entrada para ".$pelicula->getTitulo()." el dia ".$funcion->getFechaHora().".", "success");
				}
				
				// $subject = "Movie Pass - Tus entradas para ver ".$pelicula->getTitulo();

				// $emailDetails=array();
				// $emailDetails['pelicula'] = $pelicula->getTitulo();
				// $emailDetails['fechaHora'] = $funcion->getFechaHora();
				// $emailDetails['cine'] = $cine->getNombre();
				// $emailDetails['sala'] = $sala->getNombre();
				// $emailDetails['idCompra'] = $idCompra;		
				// Functions::sendEmail($_SESSION['loggedUser']->getEmail(),$subject, $this->compraMailBody($emailDetails));
			}

			//Guardar compra
			$compra = new Compra();
			$compra->setIdUsuario($_SESSION['loggedUser']->getId());
			$compra->setFechaHora(date("Y-m-d H:i:s"));
			$compra->setCantidad($cantidadtotal);
			$compra->setDescuento($descuento);
			$compra->setTotal($total);
			if(!$this->compraDAO->add($compra)) 
			{
				Functions::flash("Se produjo un error al registrar la compra. Tu pago será devuelto.","danger");
				Functions::redirect("Funcion","ShowMovies");
			}
			
			if(!$this->carritoDAO->vaciarCarrito($_SESSION['loggedUser']->getId())) Functions::flash("Tu carrito de compras no pudo ser vaciado.","warning");
			Functions::redirect("Entrada","ShowListView", $_SESSION['loggedUser']->getId());
			unset($_SESSION['carrito']);
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
		
		private function calcularPorcDescuento($fechaHora, $cantidad)
		{
			$descuento = 0;
			$day = date('w', strtotime($fechaHora));
			if(($day == 2 || $day == 3) && $cantidad >= 2) $descuento = 25;
			return $descuento;
		}

		private function compraMailBody($emailDetails){

			$pelicula = $emailDetails['pelicula'];
			$fechaHora = $emailDetails['fechaHora'];
			$cine = $emailDetails['cine'];
			$sala = $emailDetails['sala'];
			$compra = new Compra();
			$entradas = $this->entradaDAO->getByCompra($compra->setId($emailDetails['idCompra']));
			if($entradas == null) Functions::flash("Se produjo un error al obtener los datos de las entradas.","danger");
			
			$message = "<html>
			<body style='background-color:#fff; background-image:url(https://i.imgur.com/t216lYB.jpg); background-size:cover' bgcolor='#fff' >
			&nbsp;
			<table align='center' border='0' cellpadding='0' cellspacing='0' style='font-family: Raleway, Helvetica,sans-serif;border-radius: 30px; background-image: url(https://i.imgur.com/hINcb6A.png); background-size: cover' width='650'>
				<tbody>
					<tr>
						<td style='font-family: Raleway, Helvetica,sans-serif;font-weight:400;font-size:15px;color:#fff;text-align:center;padding:20px;line-height:25px; ' class=''><center><img src='https://i.imgur.com/uSaf2DO.png' style='display: block'></center>
			&nbsp;
			<center><img src='https://i.imgur.com/kvDOOvM.gif' style='display: block; border-radius: 200px' width='200'></center>
			<p style='color: whitesmoke; font-size: 36px; font-weight: 900; line-height: 40px; text-align:center'>Te acercamos tus entradas<br></p></td></tr>
			</tbody>
			</table>
			&nbsp;
			&nbsp;";

			foreach ($entradas as $entrada) { 

				$qr = $entrada->getQr();
			
				$message .= "<table align='center' border='0' cellpadding='0' cellspacing='0' style='font-family: Montserrat, Helvetica, sans-serif;' width='650'>
					<tbody>
						<tr>
							<td bgcolor='#fff' style='color:#666; text-align:left; font-size:14px;font-family:Montserrat, Helvetica, sans-serif; padding:20px 0px 20px 40px; line-height:25px; border-radius:30px 0 0 30px;' valign='middle' width='50%' class=''>
							<h2 style= letter-spacing: 1px; font-weight: 700; font-size: 26px; text-align: center; margin: 0; line-height: normal'>".$pelicula."<br></h2>
													
							<table align='center' border='0' cellpadding='0' cellspacing='0' width='280'>
								<tbody>
									<h4 style= letter-spacing: 1px; font-weight: 700; font-size: 26px; text-align: center; margin: 0; line-height: normal'>".$cine." </h4>
									<h4 style= letter-spacing: 1px; font-weight: 700; font-size: 26px; text-align: center; margin: 0; line-height: normal'>Sala ".$sala."<br></h4>
									<h4 style= letter-spacing: 1px; font-weight: 700; font-size: 26px; text-align: center; margin: 0; line-height: normal'>Fecha: ".$fechaHora."<br></h4>
								</tbody>
							</table>
							</td>
							<td bgcolor='#fff' style='color:#666; text-align:center; font-size:13px; padding:20px 0px 20px 40px; line-height:25px; border-radius:0 30px 30px 0;' valign='middle' width='50%' class=''>
							<center><img src='https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=.$qr style='display:block'></center>
							</td>
						</tr>
					</tbody>
				</table>
				&nbsp;";
			}
			$message .= "<h3 style='color: whitesmoke; text-align:center'>¡Que disfrutes de la función!<br></h3></body></html>";
			return $message;
		}
	}
?>