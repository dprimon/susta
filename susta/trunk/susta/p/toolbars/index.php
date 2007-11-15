<?php

require_once "include/struttura.class.php";
require_once("classi/stpersonal.class.php");

$pagina = new struttura();

$pagina->setTitle("Gestione personale");

$stpers =& new stpersonal();

if ( $stpers->utenteAutenticato() ) { 
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

      <label>Utente:</label> <input type="text" name="utente" value="<?php echo $stpers->session['utente'] ?>">
     
      <label>Password:</label> <input type="password" name="password" value="">
     <br>
     <br>
     
     <input type="submit" value="Entra"> 
     <input type="reset" value="Pulisci modulo">
</form>

     <br>
     <br>
     <em><strong>Nota:</strong> E' neccessario avere i cookie abilitati per poter entrare</em>
  
<?php 
} if (true) { ?>


<table id="main" cellpadding="10" cellspacing="1" border="1">
    <tr>
        <th>Amministrazione del personale</th>
        <th>Stampa dei rapporti</th>

    </tr>
    <tr>
        <td>
            <a href="assunzione.php">Assunzione / modifica dati dipendente</a>
            <a href="licenziamento.php">Licenziamento</a>
			<br>
            <a href="ruoli.php">Ruoli</a>
            <a href="formazione.php">Formazione / qualifiche</a>
            
        </td>
        <td>
            <a href="schedaIndividuale.php">Scheda individuale</a>
            <br>
            <a href="report_1.php">Elenco personale in ordine alfabetico</a>
            <a href="report_2.php">Elenco personale in ordine alfabetico con recapiti telefonici</a>
            <a href="report_3.php">Elenco personale in ordine alfabetico con data di nascita</a>
            <a href="report_4.php">Elenco personale per ruolo</a>
            <a href="report_5.php">Elenco personale per reparto</a>

        </td>
    </tr>
</table>
<?php
}
 
$pagina->display();

?>