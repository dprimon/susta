<?php
/*
 * Created on 23-lug-2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */





require_once "include/struttura.class.php";

require_once("classi/stpersonal.class.php");

$pagina = new struttura();

$pagina->setTitle("Assunzione / modifica dati");


$stpers =& new stpersonal();

$personale = $stpers->elencoPersonale( );
$exdipendenti = $stpers->elencoPersonaleAssunto( );



?>
<div class="crumbline">
	<a href="index.php">Home</a> - 
	Assunzione / modifica dati
</div>


<a href="mod_dipendente.php">Nuovo dipendente</a>

<table cellpadding="10" cellspacing="1" border="0" style="float:left">
   <tr>
     <th>Personale in forza</th>
   </tr>
   
<?php foreach ($personale as $persona) { ?>
   <tr>
     <td><a href="mod_dipendente.php?id=<?php echo $persona['id']?>"><?php echo $persona['cognome'] . " " . $persona['nome'] ; ?></a></td>
   </tr>

<?php } ?>

</table>

<table cellpadding="10" cellspacing="1" border="0">
   <tr>
     <th>Ex-dipendenti</th>
   </tr>
   
<?php foreach ($exdipendenti as $persona) { ?>
   <tr>
     <td><a href="mod_dipendente.php?id=<?php echo $persona['id']?>"><?php echo $persona['cognome'] . " " . $persona['nome'] ; ?></a></td>
   </tr>

<?php } ?>

</table>


<?php 
$pagina->display();

?>