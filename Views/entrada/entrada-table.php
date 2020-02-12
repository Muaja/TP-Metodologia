<table id="sortable" class="table table-striped table-responsive-md text-light align-center">
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