<?php
/*
 * Created on 30-ago-2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

require_once("classi/stpersonal.class.php");

$stpers =& new stpersonal(); 

$dati = $stpers->datiReport4();

$params = array(
	'tplFeed' => 
		array(
			array(
				'method' => 'MergeBlock', 'params' => array( 'dati', $dati['anagrafica'] ),
			),
		),
	'dstFileName' => "Personale ruoli",
);

$stpers->MakeDocument( "personale ruoli.odt",  $params );

// richiedo dl del documento generato
header( "Location: reports/{$params['dstFileName']}.{$_REQUEST['type']}" );

?>