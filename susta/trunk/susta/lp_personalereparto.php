<?php
/*
 * Created on 30-ago-2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

require_once("classi/stpersonal.class.php");

$stpers =& new stpersonal(); 

$dati = $stpers->datiReport5();

$params = array(
	'tplFeed' => 
		array(
			array(
				'method' => 'MergeBlock', 'params' => array( 'dati', $dati['anagrafica'] ),
			),
		),
	'dstFileName' => "Personale reparti",
);

$stpers->MakeDocument( "personale reparti.odt",  $params );

// richiedo dl del documento generato
header( "Location: reports/{$params['dstFileName']}.{$_REQUEST['type']}" );

?>
file:///var/www/stival/personale/lp_personalereparto.php