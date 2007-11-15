<?php 
require_once("include/basekit.inc.php");
require_once("include/eSaConta.class.php");
$counter =& new eSaConta();

//print_r( $dati );

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php echo $this->printTitle(); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php 
$this->printMeta(); 
$this->printStylesheets();
 ?><script language="javascript" src="functions.js" type="text/javascript"></script>
<link href="style.css" rel="stylesheet" type="text/css">
<!-- compliance patch for microsoft browsers -->
<!--[if lt IE 7]>
<script language="Javascript">
//IE7_PNG_SUFFIX = ".trans.png";
</script>
<script src="ie7/ie7-standard.js" type="text/javascript">
</script>
<![endif]-->
</head>

<body>
  <h1>Sistema di gestione del personale</h1>
  <?php  $this->printContent(); ?>


</body>
</html>
<!-- Pagina generata in <?php echo number_format((time()-$clockStart)/1000,6); ?> sec -->

