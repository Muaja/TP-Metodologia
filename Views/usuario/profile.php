<?php require_once(VIEWS_PATH."navbar.php"); ?>
<div class="container-fluid mb-4">
    <div class="row">
        <div class="col-md-10 col-lg-3 offset-md-1 bg-light rounded p-4 text-center my-4 shadow">

            <!-- Volver a lista de usuarios solo para admins -->
            <?php if($this->isAdmin($_SESSION["loggedUser"])) { ?>
            <div><a class="btn btn-secondary mb-4 shadow-sm" href="<?php echo FRONT_ROOT ?>Usuario/ShowListView" role="button">Ver lista de usuarios</a></div>
            <?php } ?>

            <?php require_once(VIEWS_PATH."alert.php"); ?>
            <img src="<?php echo $usuario->getImage() ?>" width="140" height="140" class="rounded-circle z-depth-0" alt="avatar image">
            <h3 class="mt-2"><?php echo $usuario->getNombre()." ".$usuario->getApellido(); ?></h3>

            <!-- Dar/Quitar admin: Solo si es main admin y no es su propia cuenta -->
            <?php if(($this->isMainAdmin($_SESSION["loggedUser"])) && ($_SESSION["loggedUser"]->getEmail() != $usuario->getEmail())) { ?>
            <a onclick = "if(toggleAdmin('<?php echo $usuario->getNombre(); ?> <?php echo $usuario->getApellido(); ?>', <?php $usuario->getId_Rol(); ?>)) href='<?php echo FRONT_ROOT ?>Usuario/toggleAdmin/<?php echo $usuario->getId(); ?>' ;" class="btn btn-info btn-md mt-2 shadow-sm" role="button">
                <?php if($usuario->getId_Rol() == 1) { ?>Hacer admin<?php } else { ?>Quitar admin<?php } ?>
            </a>
            <?php } ?>
            
            <!-- Modificar perfil -->
            <a href="<?php echo FRONT_ROOT ?>Usuario/ShowEditView/<?php echo $usuario->getId();?>" class="btn btn-warning btn-md mt-2 shadow-sm" role="button">Modificar</a>
            
            <!-- Eliminar cuenta -->
            <?php if($this->isMainAdmin() || $usuario->getId() == $_SESSION["loggedUser"]->getId()) { ?>
            <a onclick = "if(borrarUsuario('<?php echo $usuario->getNombre(); ?> <?php echo $usuario->getApellido(); ?>')) href='<?php echo FRONT_ROOT ?>Usuario/Remove/<?php echo $usuario->getId(); ?>' ;" class="btn btn-danger btn-md mt-2 shadow-sm" role="button">Eliminar</a>
            <?php } ?>

            <ul class="list-group mt-4">
                <li class="list-group-item">Email: <?php echo $usuario->getEmail(); ?></li>
                <li class="list-group-item">Nombre: <?php echo $usuario->getNombre().", ".$usuario->getApellido(); ?></li>
                <li class="list-group-item">DNI: <?php echo $usuario->getDni(); ?></li>
                <li class="list-group-item">Registro: 
                <?php $date = $usuario->getRegisterDate();
                $registerDate = date("d/m/Y H:i",$date);
                echo $registerDate; ?>
                </li>
            </ul>
        </div>
        <div class="col-md-10 col-lg-6 offset-md-1 bg-light rounded p-4 my-4 shadow">
            <h4 class="pb-2 mb-3">Lista de entradas</h4>
            <table id="sortable" class="table table-striped table-responsive-md align-center">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pelicula</th>
                        <th># Funcion</th>
                        <th># Compra</th>
                        <th>QR</th>
                        <th>Ver</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entradaList as $entrada) { ?>
                    <tr>
                        <td class="align-middle"><?php echo $entrada->getId(); ?></td>
                        <?php 
                            $idFuncion = $entrada->getIdFuncion();
                            $funcion->setId($idFuncion);
                            $funcion = $this->funcionDAO->getFuncion($funcion);
                            $idPelicula = $funcion->getIdPelicula();
                            $pelicula->setId($idPelicula);
                            $pelicula = $this->peliculaDAO->getPelicula($pelicula);
                        ?>
                        <td class="align-middle"><img src="<?php echo $pelicula->getPoster(); ?>"  height="35" width="35" class="rounded-circle z-depth-0 mr-2" alt="pelicula image"><b><?php echo $pelicula->getTitulo(); ?></b></a></td>
                        <td class="align-middle"><?php echo $funcion->getId(); ?></td>
                        <td class="align-middle"><?php echo $entrada->getIdCompra(); ?></td>
                        <td class="align-middle"><a href="#modal<?php echo $entrada->getId();?>" class="view" title="" data-toggle="modal" data-original-title="View Details"><img src="https://chart.googleapis.com/chart?chs=60x60&cht=qr&chl=<?php echo $entrada->getQr(); ?>" class="rounded-circle z-depth-0" alt="qr"></a></td>
                        <td class="align-middle"><a href="#modal<?php echo $entrada->getId();?>" class="view" title="" data-toggle="modal" data-original-title="View Details"><h4><i class="fa fa-arrow-circle-right"></i></h4></a></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#sortable').DataTable( {
        "columnDefs": [
            { "orderable": false, "targets": 5 }
        ]
        } );
    } );
</script>

<!-- Modal que muestra entrada -->
<?php 
foreach($entradaList as $entrada) 
{
    $idFuncion = $entrada->getIdFuncion();
    $funcion->setId($idFuncion);
    $funcion = $this->funcionDAO->getFuncion($funcion);
    $idPelicula = $funcion->getIdPelicula();
    $pelicula->setId($idPelicula);
    $pelicula = $this->peliculaDAO->getPelicula($pelicula);
    require(VIEWS_PATH."entrada/entrada.php");
}
?>