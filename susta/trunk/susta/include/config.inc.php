<?php

/**
 * 
 *
 * @version $Id$
 * @copyleft 2005-2006
 **/
 
$cfg['place']  = "dev";
$cfg['dbserver'] = "localhost";
$cfg['dbuser']   = "dev";
$cfg['dbpass']   = "123";
$cfg['dbname']   = "susta";

$cfg['locale']['dsn']    = "mysql://{$cfg['dbuser']}:{$cfg['dbpass']}@{$cfg['dbserver']}/{$cfg['dbname']}";
$cfg['root']             = "{$_SERVER['DOCUMENT_ROOT']}/personale";
$cfg['include_path']     = "{$cfg['root']}/include";
$cfg['sessionname']      = "susta";

# Login
$cfg['username']         = "susta";
$cfg['password']     	= "susta";

# OpenOffice tmp dir
$cfg['ooodocdir'] = "/var/www/susta/";	


?>
