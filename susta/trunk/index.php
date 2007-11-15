<?php

require_once "include/struttura.class.php";
require_once("classi/stpersonal.class.php");

$pagina = new struttura();

$pagina->setTitle("Gestione personale");

$stpers =& new stpersonal();


if ( ! $stpers->utenteAutenticato() ) 
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
<!--	<br>
	<a href="report_1.php">Elenco personale</a>
	<a href="report_2.php">Elenco personale con recapiti telefonici</a>
	<a href="report_3.php">Elenco personale con data di nascita</a>
	<a href="report_4.php">Elenco personale per ruolo</a>
	<a href="report_5.php">Elenco personale per reparto</a>

-->

</div>

<?php 

}

$pagina->display();

?>