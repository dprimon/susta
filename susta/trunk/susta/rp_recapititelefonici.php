<?php
/*
 * Created on 23-lug-2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

require_once "include/struttura.class.php";

$pageName = "Recapiti telefonici personale";

$pagina = new struttura();

$pagina->setTitle( $pageName );


require_once("classi/stpersonal.class.php");

$stpers =& new stpersonal(); 


?>

<div class="crumbline">
	<a href="index.php">Home</a> - 
	<?php echo $pageName ?>
	
</div>

 <h2><?php echo $pageName ?></h2>
 Opzioni disponibili per il rapporto:
 <br>

	<a href="recapiti_tel.php?type=odt">Scarica documento (ODT)</a>
	<a href="recapiti_tel.php?type=pdf">Scarica documento (PDF)</a>
<!--	<a href="#">Stampa</a> -->

	<br>
	<br>
	<br>
	
	<a href="index.php">Home</a>



<?php 

$pagina->display();
?>