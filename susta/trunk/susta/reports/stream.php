<?php

chdir("..");
require_once("classi/stpersonal.class.php");


//echo "<h2>OCCIOI - </h2>";
// evid( $_REQUEST );
// evid( $_SERVER );

//$docName = basename( urldecode( $_SERVER['REQUEST_URI'] ) );
$docName = basename( urldecode( $_REQUEST['serve'] ) );


$stpers =& new stpersonal(); 

// $tmpDoc = "/var/chroot/sid-ia32//var/www/stival/tmp/";
// echo "<pre>" . `ls -l $tmpDoc` . "</pre>";

//evid( $stpers->session );
//evid( $docName );
$stpers->StreamDocument( $docName );


// $tmpDoc = "/var/chroot/sid-ia32/var/www/stival/tmp/";
// echo "<pre>" . `ls -l $tmpDoc` . "</pre>";

?>
