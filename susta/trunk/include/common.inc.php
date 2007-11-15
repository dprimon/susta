<?php

//require("config.inc.php");
/**
 * 
 *
 * @version $Id$
 * @copyright 2003 
 **/


/**
 * Rimuove il nome della tabella dai campi del record passato. Viene considerato 
 * nome della tabella tutti i caratteri del campo fino all'underscore "_" compreso.
 * @param array Record con i campi da sistemare
 * @return void 
 */
function removeTableNameFromFields(&$data)
{
	if (!is_array($data)) {
		trigger_error("removeTableNameFromFields(): L'argomento dev'essere un'array", E_USER_WARNING);
	}
    foreach ($data as $field => $value) {
        $newField = preg_replace("/[^_]*_/", "", $field);
		// se non c'è nome della tabella usciamo
		// restituendo false		
		if ($newField == "") {
		    return false;
		}
        $newRec[$newField] = $value;
    } 
	$data = $newRec;
    return ;
} 

/**
* @param string Tabella oggetto dell'inserimento
* @param array Record con i nomi dei campi da sistemare
* @return boolean false se i campi non sono stati modificati
*/
function addTableNameToFields($table, &$data) {
	foreach ($data as $field => $value) {
		$newArr["{$table}_$field"] = $value;
	}
	$data = $newArr;
}

/**
* Simile ad'array_pop(), toglie dall'array $subject l'elemento con chiave $key e lo restuisce
* @param array oggetto della ricerca
* @param mixed Chiave da togliere
* @return mixed l'elemento
**/
function array_extract(&$subject, $key) {
	if (isset($subject[$key])) {
	    $retVal = $subject[$key];
		unset($subject[$key]);
	} else {
		$retVal = NULL;
	}
	return $retVal;
}

function evid($message, $styleAttr = "") {
	if (is_array($message)) {
	    echo "<pre style=\"width: 800px; $styleAttr\">";
		print_r($message);
		echo "</pre>";
	} else {
		echo "<pre style=\"$styleAttr\">$message</pre>";
	} 
}

/** @desc Tell us if we're running on a windows server
* @return bool true if using Windows
*/
function is_win_server() {
	return ( strtoupper( substr( PHP_OS, 0, 3 ) ) === 'WIN');
}

/**
* @param string	Percorso da impostare
* @param bool	Se aggiungere o rimpiazzare al path di include esistente
*/
function setIncludePath($path, $addToExisting = true) {
	$pathSep = is_win_server() ? ";" : ":";
	
	ini_set("include_path", 
	     $addToExisting ? 
	     	get_cfg_var("include_path") . $pathSep
	     	. ini_get("include_path") . $pathSep . $path
	     :	$path);
	 //evid(ini_get("include_path"));
}

?>