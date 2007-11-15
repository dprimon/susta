<?php

/**
 * 
 *
 * @version $Id$
 * @copyright 2003 
 **/
 
$cfg['place']  = "aruba";
$cfg['dbserver'] = "62.149.150.21";
$cfg['dbuser']   = "Sql33017";
$cfg['dbpass']   = "36Rw3T)U";
$cfg['dbname']   = "Sql33017_3";

//$cfg['remoto']['dsn']    = "mysql://ristoservice:123@192.168.1.148/ristoservice";
//$cfg['remoto']['rootAdmin'] = "http://tama-j1g/lastminute.eu/admin";
$cfg['locale']['dsn']    = "mysql://{$cfg['dbuser']}:{$cfg['dbpass']}@{$cfg['dbserver']}/{$cfg['dbname']}";
$cfg['root']             = "{$_SERVER['DOCUMENT_ROOT']}cfvv";
$cfg['include_path']     = "{$cfg['root']}/include";
//$cfg['pathfoto']         = "/lastminute.eu/immagini_appartamenti_ville_residence_monolocali_bungalow";
//$cfg['vitaOrdini']         = "15";    // nr di giorni di vita di un'ordine _non_ confermato
$cfg['sessionname']      = "cfvv";
//$cfg['google']['alt']  = "http://debian/lastminute.eu/google_adsense_script.html";

//$cfg['dbserver'] = "192.168.1.148";
//$cfg['httpserver'] = "192.168.1.148";
//$cfg['dbuser'] = "ristoservice";
//$cfg['dbpass'] = "123";
//$cfg['dbname'] = "ristoservice";

     

?>