<?php
/*
 * Created on 23-lug-2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

require_once "include/struttura.class.php";

$pagina = new struttura();

$pagina->setTitle("Scheda individuale");


require_once("classi/stpersonal.class.php");

$stpers =& new stpersonal(); 


?>

<div class="crumbline">
	<a href="index.php">Home</a> - 
	Recapiti telefonici personale
	
</div>

 <h2>Recapiti telefonici personale</h2>
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