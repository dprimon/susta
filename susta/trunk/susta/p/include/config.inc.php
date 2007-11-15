<?php

/**
 * 
 *
 * @version $Id$
 * @copyright 2003 
 **/
 
$cfg['place']  = "esa";
$cfg['dbserver'] = "localhost";
$cfg['dbuser']   = "dev";
$cfg['dbpass']   = "123";
$cfg['dbname']   = "personale";
//$cfg['dbname']   = "personale-stiv";

//$cfg['remoto']['dsn']    = "mysql://ristoservice:123@192.168.1.148/ristoservice";
//$cfg['remoto']['rootAdmin'] = "http://tama-j1g/lastminute.eu/admin";
$cfg['locale']['dsn']    = "mysql://{$cfg['dbuser']}:{$cfg['dbpass']}@{$cfg['dbserver']}/{$cfg['dbname']}";
$cfg['root']             = "{$_SERVER['DOCUMENT_ROOT']}stival/personale";
$cfg['include_path']     = "{$cfg['root']}/include";
$cfg['sessionname']      = "stpers";
$cfg['username']         = "stival";
$cfg['password']     	= "stival";

// OpenOffice backend
$cfg['ooodocdir'] = "/var/www/stival/";	
//$cfg['google']['alt']  = "http://debian/lastminute.eu/google_adsense_script.html";

//$cfg['dbserver'] = "192.168.1.148";
//$cfg['httpserver'] = "192.168.1.148";
//$cfg['dbuser'] = "ristoservice";
//$cfg['dbpass'] = "123";
//$cfg['dbname'] = "ristoservice";

     

?>
