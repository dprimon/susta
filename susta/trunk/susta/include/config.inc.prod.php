<?php

/**
 * 
 *
 * @version $Id$
 * @copyleft 2005-2006
 **/
 
$cfg['place']  = "scarpa";
$cfg['dbserver'] = "localhost";
$cfg['dbuser']   = "personale";
$cfg['dbpass']   = "123";
$cfg['dbname']   = "personale";

$cfg['locale']['dsn']    = "mysql://{$cfg['dbuser']}:{$cfg['dbpass']}@{$cfg['dbserver']}/{$cfg['dbname']}";
$cfg['root']             = "{$_SERVER['DOCUMENT_ROOT']}/personale";
$cfg['include_path']     = "{$cfg['root']}/include";
$cfg['sessionname']      = "stpers";

# Login
$cfg['username']         = "stival";
$cfg['password']     	= "stival";

# OpenOffice tmp dir
$cfg['ooodocdir'] = "/var/www/personale/";	


?>
