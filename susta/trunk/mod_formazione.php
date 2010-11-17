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

$modulo =& $stpers->moduloDati('mod_formazione');

$pagina->setTitle("Formazione e qualifiche di {$modulo->_datiModulo['nome']} e {$modulo->_datiModulo['cognome']}");

?>
<div class="crumbline">
	<a href="index.php">Home</a> - 
	<a href="formazione.php">Formazione / qualifiche</a> - 
	Formazione e qualifiche di <?php echo $modulo->_datiModulo['nome'] . " " . $modulo->_datiModulo['cognome']; ?>
</div>

<?php 
if ($modulo->visualizzabile()) { ?>

<div style="float:right; display: block;">
	<a href="#passato">  Attività di formazione effettuate</a>
</div>
	
<h2>Attività di formazione</h2>
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
	<label for="data" style="width: 16em">Data</label>
	<input type="text" name="for_data" id="data" value="<?php echo $_POST['for_data']; ?>">
	<br>
	<label for="attivita" style="width: 16em">Attività di formazione</label>
	<input type="text" name="for_descrizione" id="attivita" style="width: 40em;" value="<?php echo $_POST['for_descrizione']; ?>">
	<br>
	<label for="verifica" style="width: 16em">Verifica dell'efficacia</label>
	<select style="width: 16em;" name="for_valutazione" id="verifica" value="<?php echo $_POST['for_valutazione']; ?>">
		<option value=""> non assegnato  
<?php 
	foreach ($modulo->valutazioni as $valutazione) {   ?>
		<option value="<?php echo $valutazione?>" <?php $_POST['for_valutazione'] == $valutazione ? "selected=\"selected\"" : ""; ?>"><?php echo $valutazione?> 
<?php }  ?>
	</select>
	<br>
	<br>

  <h2>Qualifiche raggiunte</h2>

<?php 
//evid($_POST);
for ($i = 1; $i < 5; $i++) { ?>
		<input name="qualifica<?php echo $i?>Data" id="qualifica<?php echo $i?>" type="text" value="<?php echo $_POST["qualifica{$i}Data"]; ?>">
		<select name="qualifica<?php echo $i?>" id="qualifica<?php echo $i?>" style="width: 600px">
			<option>
<?php 
	foreach ($stpers->elencoQualifiche() as $qualifica) {   ?>
			<option value="<?php echo $qualifica['Qualifica']?>" <?php echo $_POST["qualifica$i"] == $qualifica['Qualifica'] ? "selected=\"selected\"" : ""; ?>> <?php echo $qualifica['Qualifica']; ?> 
<?php }  ?>
		</select>
		<br> 
<?php } ?>
		<br>
		<input type="submit" name="submit" value="Salva" onClick="return confirm('Sicuro di voler salvare?')">
		<input type="reset" value="Annulla modifiche digitate">
</form>
<br>

	<h4><a name="passato">Attività di formazione passate</a></h4>
<?php 
	if (is_array($modulo->storico) && count($modulo->storico)) :
?>
	<table border="1">
		<tr>
		   <th>Data </th>
		   <th>Attività di formazione</th>
		   <th>Verifica dell'efficacia </th>
		</tr>
<?php
		foreach ( $modulo->storico as $attivita ) : ?>
	
		<tr>
		   <td><?php echo $attivita['for_data']?></td>
		   <td><?php echo $attivita['for_descrizione']?></td>
		   <td><?php echo $attivita['for_valutazione']?></td>
		</tr>
<?php 
		endforeach;
	else :
?> 
	<i>Nessuna attività registrata</i>
<?php
	endif;
?>
	</table>
<?php 
}

if ($modulo->datiElaborati()) {  
?>
<p>
	Salvataggio delle modifiche compiuto.
	
	<a href="mod_ruolo.php?id=<?php echo $modulo->_session['campi']['for_dipendente']; ?>">Modifica / reparto ruolo</a>
	<a href="reports.php?id=<?php echo $modulo->_session['campi']['for_dipendente']; ?>">Scheda individuale</a>
</p>
<?php 
}
$pagina->display();
?>
