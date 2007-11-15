<?php
/*
 * Created on 23-lug-2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */





require_once "include/struttura.class.php";

$pagina = new struttura();

$pagina->setTitle("Nuovo dipendente");

//$pagina->setActiveLI(2, 'l1');

//require_once('include/basekit.inc.php');

require_once("classi/stpersonal.class.php");

$stpers =& new stpersonal(); 

$modulo =& $stpers->moduloDati('mod_licenzia');

?>

<div class="crumbline">
	<a href="index.php">Home</a> - 
	<a href="licenziamento.php">Licenziamento personale</a> - 
	Richiesta di conferma
	
</div>

  
<?php 
if ($modulo->visualizzabile()) { ?>

<h2>Licenziamento di <?php echo $_POST['nome'] . " " . $_POST['cognome'] ?></h2>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	
  <input type="hidden" name="nome" value="<?php echo $_POST['nome'] ?>" style="display: none;">
  <input type="hidden" name="cognome" value="<?php echo $_POST['cognome'] ?>" style="display: none;">

  <label for="conferma">Sì, confermo</label>
  <input type="radio" name="conferma" id="conferma" value="true" >

   <br>
   
  <label for="annulla">No, torna indietro</label>
  <input type="radio" name="conferma" id="annulla" value="false">
  
  <br>
  <br>
  
  
  <input type="submit" name="submit" value="OK">
  
  

  
	
</form>

<?php
}
 
if ( $modulo->datiElaborati() ) { ?>
<br>
<hr>

<h2>Licenziamento effettuato</h2>
<p>
	<?php echo $_POST['nome'] . " " . $_POST['cognome'] ?> è stata licenziato/a.
	
	<br>
	<br>
	<br>
	
	<a href="index.php" target="_blank">Home</a>
</p>



<?php 
}

$pagina->display();

?>
