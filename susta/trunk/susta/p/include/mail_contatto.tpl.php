<?php
$nomeSito = "Professional Bike";
$urlSito = "www.professionalbike.it";
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
th {
    font-size: 1em; font-weight: bold; text-align: left;
}
.testo {
    font-size: 14px;
}
</style>
</head>
<body>
<p>Ciao, qualcuno ti ha scritto un messaggio dal sito <strong><?php echo $urlSito; ?></strong>.</p>
<em>Non rispondere direttamente a questo messaggio usando il pulsante rispondi. <br>
 In caso, componi un nuovo messaggio.</em>
<?php
/*
 NOTA!
 - I campi possibili sono quelli della form di invio dei contatti (ad oggi è contatti.php)

*/?>
<table border="0" align="left" cellpadding="0" cellspacing="5" >
    <tr>
       
   <td valign="top" style="border-left: 1px dotted gray; padding-left: 1.5em;">
        <table border="0" cellspacing="0" cellpadding="2" align="center">
            <tr><td height="10" colspan="2"></td></tr>
            <tr align="left" valign="top">
              <th>Cognome e Nome:</th>
              <td class="testo"><?php echo $_POST['nome']; ?></td>
            </tr>
            <tr align="left" valign="top"> 
                <th>Recapito:</th>
              <td class="testo"><?php echo $_POST['recapito']; ?></td>
            </tr>
<?php if ($_POST['recapito2']) { ?>         
            <tr align="left" valign="top"> 
                <th>Altro recapito:</th>
              <td class="testo" ><?php echo $_POST['recapito2']; ?></td>
            </tr>
<?php } ?>          
            <tr align="left" valign="top"> 
                <th>Oggetto:</th>
              <td class="testo" ><?php echo $_POST['oggetto']; ?></td>
            </tr>
            <tr><td height="20" colspan="2"></td></tr>
            <tr align="left" valign="top"> 
                <th>Testo del messaggio:</th>
              <td class="testo" ><?php echo $_POST['testo']; ?></td>
            </tr>
            <tr><td height="20" colspan="2"></td></tr>
    </table></td>
    </tr>
</table>
</body>
</html>