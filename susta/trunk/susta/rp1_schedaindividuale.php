<?php
/*
 * Created on 23-lug-2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

require_once "include/struttura.class.php";

$pageName = "Scheda individuale";

$pagina = new struttura();

$pagina->setTitle( $pageName );


require_once("classi/stpersonal.class.php");

$stpers =& new stpersonal(); 

$dipe = $stpers->datiDipendente( $_REQUEST['id'] );
?>

<div class="crumbline">
	<a href="index.php">Home</a> - 
	<a href="rp_schedaIndividuale.php"><?php echo $pageName; ?></a> - Opzioni documento
	
</div>

 <h2>Opzioni</h2>
 Opzioni disponibili per la scheda individuale di <?php echo $dipe['nome'] . " " . $dipe['cognome'] ?>.
 <br>

	<a href="lp_schedaindividuale.php?type=odt&amp;id=<?php echo $_REQUEST['id'] ?>">Scarica documento (ODT)</a>
	<a href="lp_schedaindividuale.php?type=pdf&amp;id=<?php echo $_REQUEST['id'] ?>">Scarica documento (PDF)</a>
<!--	<a href="#">Stampa</a> -->

	<br>
	<br>
	<br>
	
	<a href="index.php">Home</a>



<?php 

$pagina->display();
?>
