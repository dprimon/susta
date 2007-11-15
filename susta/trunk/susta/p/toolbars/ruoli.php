<?php
/*
 * Created on 23-lug-2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */




require_once("classi/stpersonal.class.php");

require_once "include/struttura.class.php";

$pagina = new struttura();

$pagina->setTitle("Ruoli");


$stpers =& new stpersonal();

$personale = $stpers->elencoRuoliPersonale( );



?>
<div class="crumbline">
	<a href="index.php">Home</a> - 
	Ruoli
</div>


<table cellpadding="10" cellspacing="1" border="0">
   <tr>
     <th>Nome Cognome</th>
     <th>Ruolo / reparto</th>
   </tr>
   
<?php foreach ($personale as $persona) { ?>
   <tr>
     <td><a href="mod_ruolo.php?id=<?php echo $persona['id']?>"><?php echo $persona['cognome'] . " " . $persona['nome'] ; ?></a></td>
     <td><?php echo $persona['incarico']?></td>
   </tr>

<?php } ?>


</table>


<?php 
$pagina->display();

?>