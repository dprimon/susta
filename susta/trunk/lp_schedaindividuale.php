<?php
/*
 * Created on 30-ago-2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 *
 * @author: Daniele Primon
 */

require_once("classi/stpersonal.class.php");

$stpers =& new stpersonal(); 

$dati = $stpers->datiSchedaIndividuale();

$params = array(
	'tplFeed' => 
		array(
			array(
				'method' => 'MergeField', 'params' => array( 'ditta', $dati['ditta'] ),
			),
			array(
				'method' => 'MergeField', 'params' => array( 'dati', $dati ),
			),
			array(
				'method' => 'MergeBlock', 'params' => array( 'blkstor', $dati['storico'] ),
			),
			array(
				'method' => 'MergeBlock', 'params' => array( 'blkfor', $dati['formazione'] ),
			),
		),
	'dstFileName' => "MOD.18.001 - Scheda individuale {dati[cognome]} {dati[nome]}",
);

$stpers->MakeDocument("mod.18.001-schindiv.odt", $params);

// richiedo dl del documento generato
header("Location: reports/{$params['dstFileName']}.{$_REQUEST['type']}");
?>
