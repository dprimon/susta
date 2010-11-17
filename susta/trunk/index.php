<?php
// @author: Daniele Primon

require_once("include/struttura.class.php");
require_once("classi/stpersonal.class.php");

$pagina = new struttura();

$pagina->setTitle("Gestione personale");

$stpers =& new stpersonal();

if (!$stpers->utenteAutenticato()) 
{ ?>
<div class="panel">
	<h3>Ingresso utente</h3>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<label>Utente:</label> <input type="text" name="utente" value="<?php echo $stpers->session['utente'] ?>">
		<label>Password:</label>  <input type="password" name="password" value="">
		<br>
		<br>
		<input type="submit" value="Entra"> 
		<input type="reset" value="Pulisci modulo">
	</form>
</div>
     <br>
     <br>
     <em><strong>Nota:</strong> E' neccessario avere i cookie abilitati per poter entrare</em>
<?php 
} 
else
{ ?>

<div class="panel">
	<h3>Amministrazione</h3>
	<a href="assunzione.php">Assunzione / modifica dati dipendente</a>
	<a href="licenziamento.php">Licenziamento</a>
	<br>
	<a href="ruoli.php">Ruoli</a>
	<a href="formazione.php">Formazione / qualifiche</a>
</div>

<div class="panel">
	<h3>Modulistica / rapporti</h3>
	<a href="rp_schedaIndividuale.php">Scheda individuale</a>
	<a href="rp_personale.php">Elenco personale</a>
	<a href="rp_recapititelefonici.php">Recapiti telefonici personale</a>
	<a href="rp_personalenato.php">Date di nascita del personale</a>
	<a href="rp_personaleruolo.php">Ruoli del personale</a>
	<a href="rp_personalereparto.php">Reparti del personale</a>
</div>

<?php 
}
$pagina->display();
?>
