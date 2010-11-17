<?php
/*
 * Created on 23-lug-2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 * @author: Daniele Primon
 */

require_once("include/struttura.class.php");
require_once("classi/stpersonal.class.php");

$pagina = new struttura();

$pagina->setTitle("Formazione / qualifiche");

$stpers =& new stpersonal();

$personale = $stpers->elencoPersonale();

?>
<div class="crumbline">
	<a href="index.php">Home</a> - 
	Formazione / qualifiche
</div>

<table cellpadding="10" cellspacing="1" border="0">
   <tr>
     <th>Nome Cognome</th><th></th>
   </tr>
   
<?php foreach ($personale as $persona) { ?>
   <tr>
     <td><a href="mod_formazione.php?id=<?php echo $persona['id']?>"><?php echo $persona['cognome'] . " " . $persona['nome'] ; ?></a></td>
   </tr>

<?php } ?>

</table>

<?php 
$pagina->display();
?>
