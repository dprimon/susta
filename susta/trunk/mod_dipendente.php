<?php
/*
 * Created on 23-lug-2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 * 
 * @author: Daniele Primon
 */

require_once("include/struttura.class.php");
require_once("classi/stpersonal.class.php");

$pagina = new struttura();

$stpers =& new stpersonal(); 

$modulo =& $stpers->moduloDati('mod_dipendente');

$pageName = @$_REQUEST['id'] ? 'Modifica dipendente' : 'Nuovo dipendente'; 

$pagina->setTitle( $pageName );
?>
<div class="crumbline">
	<a href="index.php">Home</a> - 
	<a href="assunzione.php">Assunzione / modifica dati</a> - <?php echo $pageName ?>
</div>
 
<?php 
if ($modulo->visualizzabile()) { ?>
<h2>Dati anagrafici</h2>
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
		<label for="cognome">Cognome</label>
		<input type="text" value="<?php echo $_POST['cognome']; ?>" id="cognome" name="cognome">

		<label for="nome">Nome</label>
		<input type="text" value="<?php echo $_POST['nome']; ?>" id="nome" name="nome">

		<br>

		<label for="nascitaLuogo">Nato a</label>
		<input type="text" value="<?php echo $_POST['nascitaLuogo']; ?>" id="nascitaLuogo" name="nascitaLuogo">

		<label for="nascitaProvincia">provincia di</label>
		<input style="width: 10em" type="text" value="<?php echo $_POST['nascitaProvincia']; ?>" id="nascitaProvincia" name="nascitaProvincia">

		<label for="nascitaData" style="width: 2em">il</label>
		<input type="text" style="width: 8em" value="<?php echo $_POST['nascitaData']; ?>" id="nascitaData" name="nascitaData">
        	<br>
        
		<label for="residenzaComune">Residente nel comune di</label>
		<input type="text" value="<?php echo $_POST['residenzaComune']; ?>" id="residenzaComune" name="residenzaComune">
		
		<label for="residenzaProvincia">provincia di</label>
		<input type="text" value="<?php echo $_POST['residenzaProvincia']; ?>" id="residenzaProvincia" name="residenzaProvincia">
		<br> 
		
		<label for="residenzaIndirizzo">In via</label>
		<input type="text" value="<?php echo $_POST['residenzaIndirizzo']; ?>" id="residenzaIndirizzo" name="residenzaIndirizzo" >
		
		<label for="residenzaCap">CAP</label>
		<input class="tre_campi" type="text" value="<?php echo $_POST['residenzaCap']; ?>" id="residenzaCap" name="residenzaCap">
		
		<label for="assunzioneData" style="width: 6em">Assunto il</label>
		<input style="width: 8em" type="text" value="<?php echo $_POST['assunzioneData']; ?>" id="assunzioneData" name="assunzioneData">
		<br>
		
		<label for="telFisso">telefono N�</label>
		<input type="text" value="<?php echo $_POST['telFisso']; ?>" id="telFisso" name="telFisso">
		
		<label for="telCell">Cellulare N�</label>
		<input type="text" value="<?php echo $_POST['telCell']; ?>" id="telCell" name="telCell">
		<br>
		
		<label for="noteAnagrafica">Note</label>
		<textarea rows="3" cols="80" name="noteAnagrafica" id="noteAnagrafica"><?php echo $_POST['noteAnagrafica']; ?></textarea>

<br>		
<br>
<hr>
  <h2>Valutazioni periodiche</h2>
	<textarea name="valutazioni" rows="7" cols="40" id="valutazioni"><?php echo $_POST['valutazioni']; ?></textarea>		

<br>		
<br>
<hr>
  <h2>Note finali</h2>
	<textarea name="noteFinali" rows="3" cols="40" id="noteFinali"><?php echo $_POST['noteFinali']; ?></textarea>		
	<br>
	<br>
	<input type="submit" name="submit" value="Salva" onClick="return confirm('Sicuro di voler salvare?')">
	<input type="reset" value="Annulla modifiche digitate">
</form>

<?php 
}

if ($modulo->datiElaborati()) { ?>
<br>
<hr>

<h2>Salvataggio dati</h2>

I dati su <?php echo $_POST['nome'] . " " .$_POST['cognome']  ?> sono stati memorizzati.

<br>
<br>
<br>
<a href="mod_ruolo.php?id=<?php echo $modulo->_session['campi']['id'] ?>">Modifica / reparto ruolo</a>
<a href="reports.php?id=<?php echo $modulo->_session['campi']['id'] ?>">Scheda individuale</a>
 
</p>

<?php 
}
$pagina->display();
?>
