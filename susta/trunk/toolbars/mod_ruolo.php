<?php
/*
 * Created on 23-lug-2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */





require_once "include/struttura.class.php";

$pagina = new struttura();


//$pagina->setActiveLI(2, 'l1');

require_once('include/basekit.inc.php');

require_once("classi/stpersonal.class.php");

$stpers =& new stpersonal();

$modulo =& $stpers->moduloDati('mod_ruolo');

$ruoli = $stpers->elencoRuoli();
$reparti = $stpers->elencoReparti();

$pagina->setTitle("Ruolo e reparto per {$_POST['nome']} {$_POST['cognome']}");

//evid( $modulo );
?>

<div class="crumbline">
	<a href="index.php">Home</a> - 
	<a href="ruoli.php">Modifica ruolo / reparto</a> -
	Assegnazione nuovo reparto/ruolo  
</div>

<div style="float: right; margin-right: 4em;"> <h4>Qualifiche ad oggi acquisite</h4>
<ul>
<?php
foreach ( $modulo->qualificheAcquisite() as $qualifica) { ?>
	<li><?php echo $qualifica; ?></li>
<?php 
} ?>
	
</ul>

 </div>
 
 
<?php 
if ($modulo->visualizzabile()) { ?>

<h2>Assegnazione nuovo reparto/ruolo</h2>

Dipendente: <?php echo $_POST['nome'] . " " . $_POST['cognome'] ?>.

<br>
<br>
<?php
	if ($modulo->contieneErrori()) {
?>        
      <div>
    <ul>
<?php 	foreach ($modulo->erroriRiscontrati() as $messaggio) { ?>    
       <li><?php echo $messaggio ?></li>
<?php 	} ?>
    </ul>
      
      </div>
<?php 	} ?>
  
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<label for="ruolo">Ruolo</label>
		<select name="Ruolo" id="ruolo">
			<option value=""> non assegnato 
<?php 
foreach ($stpers->elencoRuoli() as $ruolo) { ?>
			<option value="<?php echo $ruolo['Ruolo']; ?>" <?php echo $ruolo['Ruolo'] == $_POST['Ruolo'] ? "selected=\"selected\"" : ""; ?>> <?php echo $ruolo['Ruolo']; ?> 
<?php 

} ?>
		</select>
		<br>

		<label for="reparto">Reparto</label>
		
		<select name="Reparto" id="reparto">
			<option value=""> non assegnato 
<?php 
foreach ($stpers->elencoReparti() as $reparto) { ?>
			<option value="<?php echo $reparto['Reparto']; ?>" <?php echo $reparto['Reparto'] == $_POST['Reparto'] ? "selected=\"selected\"" : ""; ?>> <?php echo $reparto['Reparto']; ?>
<?php 

} ?>
		</select>

		<br>

		<label for="Note">Note</label>
		<input type="text" name="Note" id="Note" value="<?php echo $reparto['Note']; ?>">

		<br>
		<br>
		<input type="submit" name="submit" value="Salva" onClick="return confirm('Sicuro di voler salvare?')">
		<input type="reset" value="Ripristina il modulo">
		
		


</form>
<?php } ?>

<?php if ($modulo->datiElaborati()) { ?>

<?php //evid($modulo) ?>
<p>

Salvataggio delle modifiche compiuto.

<a href="prova.sxw">Apri rapporto di formazione</a>
<a href="sch_indiv.php?id=<?php echo($modulo->_session['campi']['id']) ?>" target="_blank">Stampa scheda individuale</a> 
</p>


<?php } ?>


<?php $pagina->display(); ?>