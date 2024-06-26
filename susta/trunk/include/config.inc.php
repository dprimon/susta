<?php

/**
 * 
 *
 * @version $Id$
 * @copyleft 2005-2006
 **/
 
$cfg['place']  = "dev";				// this indicates the running version. Only for memo purpose
$cfg['dbserver'] = "localhost";		// database server
$cfg['dbuser']   = "user";			// database username
$cfg['dbpass']   = "password";		// password belonging to username
$cfg['dbname']   = "susta";			// database name

$cfg['locale']['dsn'] = "mysql://{$cfg['dbuser']}:{$cfg['dbpass']}@{$cfg['dbserver']}/{$cfg['dbname']}";
$cfg['root']          = "{$_SERVER['DOCUMENT_ROOT']}/susta";	// directory on the web server to point the browser to
$cfg['include_path']  = "{$cfg['root']}/include";	// directory where include files and libraries resides
$cfg['sessionname']   = "susta";	// name of the session. Useful if the app is to be integrated with another

# Front end login and password
$cfg['username']     = "susta";
$cfg['password']     = "susta";

# Open document and pdf tmp dir, needs to be writable by www-data user
#$cfg['ooodocdir'] = "/tmp";
$cfg['ooodocdir'] = "{$cfg['root']}/tmp";	

?>
