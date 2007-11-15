<?php
/**
* @package jedzUtils
*/

//define("NON_CLOSED_TAGS", "meta br p hr");


//echo ini_get("include_path");

/**
 * Rimuove il nome della tabella dai campi del record passato. Viene considerato 
 * nome della tabella tutti i caratteri del campo fino all'underscore "_" compreso.
 * @param array Record con i campi da sistemare
 * @return void 
 */
if (!function_exists("removeTableNameFromFields")) {

function removeTableNameFromFields(&$data)
{
    if (!is_array($data)) {
        trigger_error("removeTableNameFromFields(): L'argomento dev'essere un'array", E_USER_WARNING);
        return ;
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

}

/**
* @param string Tabella oggetto dell'inserimento
* @param array Record con i nomi dei campi da sistemare
* @return boolean false se i campi non sono stati modificati
**/
if (!function_exists("addTableNameToFields")) {

function addTableNameToFields($table, &$data) {
    foreach ($data as $field => $value) {
        $newArr["{$table}_$field"] = $value;
    }
    $data = $newArr;
}

}

/**
* Simile ad'array_pop(), toglie dall'array $subject l'elemento con chiave $key e lo restuisce
* @param array oggetto della ricerca
* @param mixed Chiave da togliere
* @return mixed l'elemento
**/
if (!function_exists("array_extract")) {

function array_extract(&$subject, $key) {
    if (isset($subject[$key])) {
        $retVal = $subject[$key];
        unset($subject[$key]);
    } else {
        $retVal = NULL;
    }
    return $retVal;
}

}

if (!function_exists("evid")) {
/**
 *  Evidenzia del testo
 *
 * Scrive il testo in una pagina html convertendo gli eventuali caratteri riservati di HTML per renderli visibili</p>
 * Evidenzia anche il testo.
 *
 *     @access protected
 *     @param string $txt Il testo che verrà stampato
 *     @param string $front Il colore del testo
 *     @param string $back Il colore dello sfondo
 *     @return void
 **/
function evid($input, $front = "ghostwhite", $back = "tomato") {
    $valore = ini_get("display_errors");
    switch (strtolower($valore)) {
        case "off": 
        case "false": 
        case "0":
            $trigErr = true;
            break;
        default:
            $trigErr = false;
            ;
    } // switch(ini_get("displayerrors")) 
    if (is_resource($input) and get_resource_type($input) == 'mysql result') {
        while ($riga = mysql_fetch_assoc($input)) {
            $input2[] = $riga;
        } // while
        $input = $input2;
    }
    $testo = "";
    if (is_object($input) || is_array($input)) {
         $trigErr and  
             ob_start();
         evid_r($input);
         $trigErr and 
             $testo = ob_get_clean();
    } else {
        $more_style = "";
        if (strlen($input) > 5 * 1024) {
            $more_style = " display: block; overflow: scroll; height: 200px;";
        }
        $testo = $trigErr ?
                   $input
                   : "<span style=\"background: $back; color: $front;$more_style\">".htmlspecialchars($input, ENT_QUOTES).'</span>&nbsp;';
    }
    if ($trigErr) {
        trigger_error("$testo", E_USER_NOTICE);  
    } else {
        echo $testo; 
    }
 }

}

// come le #define del C.... scazzo/esci
if (!function_exists("evid_r")) {

function evid_r($input, $front="dimgray", $back="darkseagreen") {
    $valore = ini_get("display_errors");
    switch (strtolower($valore)) {
        case "off": 
        case "false": 
        case "0":
            $trigErr = true;
            break;
        default:
            $trigErr = false;
            ;
    } // switch(ini_get("displayerrors")) 
    if (!$trigErr) echo "<span style=\"background: $back; color: $front;\"><pre style=\"float: left;\">";
    print_r($input);
    if (!$trigErr) echo "</pre><hr width=\"100px\"></span>&nbsp;";
}

}


/*
* @param array $exclude Elementi dell'array da non stampare
*/
if (!function_exists("evid_except")) {

function evid_except($arr, $exclude, $front="dimgray", $back="darkseagreen") {
    function exclude(&$src, $exclude) {
        if (is_object($src)) {
            foreach ($exclude as $e) {
                eval("unset(\$src->$e);");
            }
        } elseif (is_array($src)) {
            foreach ($exclude as $e) {
                unset($src[$e]);
            }
        }
            
    }
    is_array($exclude) || trigger_error("\$exclude dev'essere un'array", E_USER_WARNING);
    exclude($arr, $exclude);
    evid_r($arr, $front, $back);
}

}

if (!function_exists("evid_offset")) {

function evid_offset($txt, $offset, $front="white", $back="red") {
    $p1 = substr($txt, 0, $offset);
    $p2 = substr($txt, $offset, 1);
    $p3 = substr($txt, $offset + 1);
    echo '<span style="background: '.$back.'; color: '.$front.';">'.
            htmlspecialchars($p1, ENT_QUOTES).
            '<span style="border: 1px dotted '.$front.'">'.htmlspecialchars($p2, ENT_QUOTES).'</span>'.
            htmlspecialchars($p3, ENT_QUOTES).
            '</span>&nbsp;';
 }
 
}
 
/** @desc resituisce l'indice di una chiave dell'array
* @param array $arr
* @param string $key
* @return int L'indice della chiave oppure null se la chiave non esiste
*/
if (!function_exists("array_key_offset")) {

function array_key_offset( $arr, $key )  
{
    $indice = 0;
    reset ( $arr );
    while ( ( list( $aKey ) = each ( $arr ) ) && $aKey != $key) 
    {
        $indice++;
    } 
    return $aKey == $key ? $indice : null;
}

}


/**
* @desc Restituisce gli elementi di $array1 le cui chiavi sono presenti fra gli elementi di array2
* @param array
* @param array
* @return array
*/
if (!function_exists("array_filter_keys")) {

function array_filter_keys($array1, $array2) 
{
    $filteredKeys = array_intersect(array_keys($array1), $array2);
    foreach ($filteredKeys as $key) {
        $resultingArray[$key] = $array1[$key];
    }
    return $resultingArray;
}

}

/** @desc 
* Restituisce la stringa con le entit… convertite nei rispettivi caratteri. 
* Effettua anche un urldecode() della stringa.
* @param string $string
* @return string
*/
if (!function_exists("unhtmlentities")) {

function unhtmlentities($string) {
    $trans_tbl = get_html_translation_table (HTML_ENTITIES);
    $trans_tbl = array_flip ($trans_tbl);
    return urldecode(preg_replace("/&#(\d+);/e","chr(\\1)",strtr ($string, $trans_tbl)));
}

}

if (!function_exists("mkBytes")) {

function mkBytes($d) {
    $neg = $d < 0;
    if ($neg) $d = -$d;
    $div = 0;
    $type = "bytes";
    if ($d > 1*pow(1024,3)) {
        $div = pow(1024,3)*1;
        $type = "Gb";
    } elseif ($d > 1*pow(1024,2))    {
        $div = pow(1024,2)*1;
        $type = "Mb";
    } elseif ($d > 1*1024) {
        $div = 1024*1;
        $type = "Kb";
    }
    if ($div) {
        $d = ($d/$div);
    }
    if ($neg) $d = -$d;
    return ((number_format($d, 2, ',', '.'))."".$type);
}

}

if (!function_exists("function_exists")) {

function getmicrotime(){ 
    list($usec, $sec) = explode(" ",microtime()); 
    return ((float)$usec + (float)$sec); 
} 

}

/**
* @desc Aspetta $msec millisecondi.
*/
if (!function_exists("waste_time")) {

function waste_time($msec) 
{
    $time_start = getmicrotime();
    while ($msec > (getmicrotime() - $time_start)) {    
        //do nothing
        ;
    }
}

}

/** @desc Tell us if we're running on a windows server
* @return bool true if using Windows
*/
if (!function_exists("is_win_server")) {

function is_win_server() {
    return ( strtoupper( substr( PHP_OS, 0, 3 ) ) === 'WIN');
}

}

/**
* @param string Percorso da impostare
* @param bool   Se aggiungere o rimpiazzare al path di include esistente
*/
if (!function_exists("setIncludePath")) {

function setIncludePath($path, $addToExisting = true) {
    $pathSep = is_win_server() ? ";" : ":";
    ini_set("include_path", 
         $addToExisting ? 
            get_cfg_var("include_path") . $pathSep
            . ini_get("include_path") . $pathSep . $path
         :  $path);
}

}

/** @desc Prints php memory usage (if running on a non-Windows server)
* @return void
*/
if (!function_exists("print_memory_usage")) {

function print_memory_usage() 
{
    if (!is_win_server()) { 
        $prev = &$GLOBALS['mem_usage'];     // lettura precedente della memoria in uso
        $cur = memory_get_usage();
        if (!isset($prev)) {
            $prev = $cur;
        }
        $diff = $cur - $prev;
?><span style="font: normal 12px monospace;"><?php 
        print "Total used memory is ".mkBytes($cur).($diff ?  " (".($diff > 0 ? "+" : "").mkBytes($diff).")" : "")."."; 
?></span><br /><?php
        $prev = $cur;
    }
}

}

function nomeMese($data) {
    $mesi = array('Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno',
                 'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre');
    $d = is_string($data) ? getdate(strtotime($data)) : getdate($data);
    return $mesi[$d['mon']-1];
}

function nomeGiorno($data, $short = true) {
    $giorni = array('Domenica', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato', 'Lunedì');
    $d = getdate(strtotime($data));
    if ($short) {
        return substr($giorni[$d['wday']], 0, 3);
    } else {
        return $giorni[$d['wday']];
    }
}
    
function postData($url, $dataArray, $dontSend = false) {
    headers_sent() and trigger_error("Impossibile inviare dati in POST poichè è già stato effettuato dell'output.", E_USER_WARNING);
    $event = $dontSend ? "" : "onLoad=\"document.forms['postData'].submit();\"";
    echo "<html><head><title>Invio dati...</title></head><body $event><form name=\"postData\" action=\"$url\" method=\"POST\">";
    foreach ($dataArray as $campo => $valore) {
        echo "<input name=\"$campo\" value=\"$valore\" type=\"hidden\">";
    }
    echo "</form></body></html>";
}

/**
 * Esegue l'utility *nix file per ottenere il tipo mime anzichè appoggiarsi ad apache.
 */
function get_mime_content_type( $f )
{
	return exec( trim( 'file -bi ' . escapeshellarg ( $f ) ) );
}


?>