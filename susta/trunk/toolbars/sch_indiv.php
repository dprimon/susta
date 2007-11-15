<?php

/***************************************************************************
 *   Copyright (C) 2005 by user,,,                                         *
 *   esa@debian                                                            *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation; either version 2 of the License, or     *
 *   (at your option) any later version.                                   *
 *                                                                         *
 *   This program is distributed in the hope that it will be useful,       *
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of        *
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         *
 *   GNU General Public License for more details.                          *
 *                                                                         *
 *   You should have received a copy of the GNU General Public License     *
 *   along with this program; if not, write to the                         *
 *   Free Software Foundation, Inc.,                                       *
 *   59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.             *
 ***************************************************************************/


require_once("classi/stpersonal.class.php");

$stpers =& new stpersonal(); 

$dati = $stpers->datiSchedaIndividuale();

// misure area stampabile, tutte espresse in mm
// A4 --> 210mm x 297mm;


	printHeader();
	
	
	if ($_REQUEST['debug']) {
		evid ( $dati );
	}

/*	$dati = array();
	
	$dati["nome"] = "Daniele Armando Luigi Filippo Ermanno";
	$dati["cognome"] = "della Sierra pelada y contron nonche Primon della nina";
	$dati["età"] = "28";
	$dati["nato_loc"] = "Pordenone";
	$dati["nato_prov"] = "PN";
	$dati["nato_data"] = "28/12/1977";
	$dati["resid_loc"] = "Pravisdomini";
	$dati["resid_prov"] = "Pordenone";
	$dati["resid_via"] = "Piazza dei Martiri di Villanova, 34/b";
	$dati["resid_cap"] = "33076";
	$dati["assun_data"] = "20 dicembre 1988";
	$dati["tel"] = "+39 0434/644392266";
	$dati["cell"] = "+39 340/7898387";
	$dati["note"] = "Epsum factorial non deposit quid pro quo hic escorol. Olypian quarrels et gorilla congolium sic ad nauseum. Souvlaki ignitus carborundum e pluribus unum. Defacto lingo est igpay atinlay. ";
*/
?>
<script type="text/javascript"> //window.print();</script>
<!-- PAGINA 1  -->
<div class="foglio" style="page-break-after: always;">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<thead>
	<tr><!-- INTESTAZIONE -->
	<td class="intestazione">
		<?php printIntestazione("SCHEDA INDIVIDUALE", "MOD. 18.001", "Rev. 0 Del 14-05-03", 1, 2); ?>
	</td>
	</tr>
</thead>
<tbody>
    <tr><!-- DATI ANAGRAFICI -->
       <td>
       
           <table cellpadding="0" cellspacing="0" border="0" width="100%" class="steno" style="height: 60mm">
                  <caption>DATI ANAGRAFICI</caption>
                  <tr>
                      <td width="45%"><span>Cognome</span><div><?php echo $dati["cognome"];?> </div>

                      </td>
                      <td colspan="3"><span>Nome</span><div><?php echo $dati["nome"];?></div></td>
                      <td width="18%"><span>di anni</span><div class="destro"><?php echo $dati["eta"];?></div></td>
                  </tr>
                  <tr>
                      <td colspan="1"><span>Nato a</span><div><?php echo $dati["nascitaLuogo"];?></div></td>
                      <td colspan="2"><span>Provincia di</span><div><?php echo $dati["nascitaProvincia"];?></div></td>
	                <td colspan="2"><span>il</span><div class="destro"><?php echo $dati["nascitaData"];?></div></td>
                  </tr>
                  <tr>
                          <td colspan="2"><span>Residente nel comune di</span><div><?php echo $dati["residenzaComune"];?></div></td>
                      <td colspan="3"><span>provincia di</span><div><?php echo $dati["residenzaProvincia"];?></div></td>
                  </tr>
                  <tr>
                      <td colspan="3"><span>In via</span><div><?php echo $dati["residenzaIndirizzo"];?></div></td>
                      <td colspan="2" width="20%"><span>C.A.P.</span><div class="destro"><?php echo $dati["residenzaCap"];?></div></td>
                  </tr>
                  <tr class="noborder">
                      <td colspan="1"><span>Assunto il:</span><div><?php echo $dati["assunzioneData"];?></div></td>
                      <td colspan="4"><span style="width: 6em;">telefono N°</span><div><?php echo $dati["telFisso"];?></div></td>
                  </tr>
                  <tr>
                      <td colspan="1"><span><!-- intenzionalmente vuota --></span><div></div></td>
                      <td colspan="4"><span style="width: 6em;">cell N°</span><div><?php echo $dati["telCell"];?></div></td>
                  </tr>
                  <tr>
                      <td colspan="5">
                         <!-- <div class="righe" style="height: 40pt;"><table class="steno" style="position: fixed; height:30pt; " width="100%"><tr><td></td></tr><tr><td></td></tr></table></div> -->
                         
                         <span style="height: 1.6cm;">Note</span><div><?php echo $dati["noteAnagrafica"];?></div>
                      </td>
                  </tr>
            </table>

           <table cellpadding="0" cellspacing="0" border="0" width="100%" class="steno" style="height: 55mm">
                  <caption>VALUTAZIONI PERIODICHE</caption>
<?php 
	for ( $i = 0; $i < 7; $i++) { 
		@list(,$val) = each( $dati['valutazioni'] ); ?>
                  <tr><td><?php if ($val) echo $val; ?></td></tr>
<?php 
	} ?>
            </table>

           <table cellpadding="0" cellspacing="0" border="0" width="100%" class="steno" style="height: 30mm">
                  <caption>COMPITI ED OBIETTIVI DI LAVORO ASSEGNATI</caption>
                  <tr><td><span>Ruolo</span><div><?php echo $dati['ruolo']; ?></div></td></tr>
                  <tr><td><span>Reparto</span><div><?php echo $dati['reparto']; ?></div></td></tr>
            </table>

           <table cellpadding="0" cellspacing="0" border="0" width="100%" class="steno steno2" style="40mm">
                  <caption>QUALIFICHE</caption>
<?php 
	for ( $i = 0; $i < 4; $i++) { 
		@list(,$val) = each( $dati['qualifiche'] ); 
		if ( $val['tipo'] )
		{
?>
                  <tr>
                     <td><div><?php echo $val['data']; ?> <?php  echo $val['tipo']; ?></div></td>
                  </tr>
<?php
		}
		else
		{
?>
                  <tr>
                     <td><div>&nbsp;</div><td>
                  </tr>
<?php
		} 
	} ?>
            </table>

       </td>
    </tr>
</tbody>
</table>
</div>

<!-- PAGINA 2  -->
<div class="foglio" style="page-break-after:avoid;">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<thead>
    <tr><!-- INTESTAZIONE -->
       <td class="intestazione">
        <?php printIntestazione("SCHEDA INDIVIDUALE", "MOD. 18.001", "Rev. 0 Del 14-05-03", 2, 2); ?>
       </td>
    </tr>
</thead>
<tbody>    
	<tr>
       <td>
			<?php printRows( 207, 40, 3.5, 21 );  $once = false; ?>
       		<!-- VARIAZIONI INCARICHI / RUOLI -->
           <table id="incarichiruoli" cellpadding="0" cellspacing="0" border="0" width="100%" class="stono">
			<caption>VARIAZIONI DI RUOLO</caption>
			<thead>
				  <tr>
				  	<th style="width: 6.5em;">DATA</th>
				  	<th>RUOLO</th>
				  	<th style="width: 11em;">VALUTAZIONI - NOTE</th>
                  </tr>
			</thead>
			<tbody style="height: 4cm;">
<?php 
	$once=true;
	for ($i=0; $i<9; $i++) { 
		@list( , $riga ) = each( $dati['storico'] ); 
		if ( !$riga ) {
			?>
                  <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
<?php 	} else { 
?>
                  <tr><td><?php echo $riga['Data']; ?></td><td><?php echo $riga['incarico']; ?></td><td><?php echo $riga['Note']; ?></td></tr>
<?php
		}
//		if ( !$riga ) break;  
	} ?>
			</tbody>
            </table>

       		<!-- FORMAZIONE / ADDESTRAMENTO -->
			<!-- <?php printRows( 207, 90, 7.4, 20 );  $once = false; ?> -->
           <table id="formazione" celldpadding="0" cellspacing="0" border="0" width="100%" class="stono">
			<caption>FORMAZIONE / ADDESTRAMENTO</caption>
            <thead>
				  <tr>
				  	<th style="width: 6.5em;">DATA</th>
				  	<th>ATTIVIT&Agrave; DI FORMAZIONE / ADDESTRAMENTO</th>
				  	<th>VERIFICA DELL'EFFICACIA</th>
                  </tr>
			</thead>
			<tbody style="height: 10cm;">
<?php 
	for ($i=0; $i<16; $i++) { 
		@list( , $riga ) = each( $dati['formazione'] ); 
		if ( !$riga ) { 
?>
                  <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
<?php 	} else {?>
                  <tr><td><?php echo $riga['for_data']; ?></td><td><?php echo $riga['for_descrizione']; ?></td><td><?php echo $riga['for_note']; ?></td></tr>
<?php
		} 
//		if ( !$riga ) break;  
	} ?>
			</tbody>
            </table>
<span style="font: bold 9pt 'Times New Roman',serif">
   Allegare alla presente copia degli attestati consegnati al partecipante</span>
		
		<!-- NOTE FINALI -->           
		   <table cellpadding="0" cellspacing="0" border="0" width="100%" class="stono">
			<caption style="font-size: 9pt; text-align: left; padding-top: 4mm;">NOTE</caption>
			<tbody>
<?php 
	for ( $i = 0; $i < 3; $i++) { 
		@list(,$val) = each( $dati['noteFinali'] ); ?>
                  <tr><td><?php if ($val) echo $val; ?></td></tr>
<?php 
	} ?>
			</tbody>
            </table>
       </td>
    </tr>
</tbody>
</table>
</div>


<?php
	printFooter();
	
	function printHeader(){
	    echo <<<EOF
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
	<html lang="it">
	<head>
	    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
	    <meta name="description" content="">
	    <meta name="author" content="user">
	    <meta name="keywords" content="">
	    <title>Personale</title>
	    <link rel="stylesheet" href="sch_indiv.css">
	</head>
	<body>
	
EOF;
	}
	
	function printFooter(){
	    echo "</body>\n</html>";
	}
	
	
	function printIntestazione($titolo, $modulo, $rev, $pag, $maxpag) {
?>
<!--[if IE]>
<style>
@media print 
{
  .stono {
  	margin-bottom: 0;
  }

}

</style>
		 
<![endif]-->	

		 <table cellpadding="0" cellspacing="0" border="0" width="100%" style="height: 20mm">
        <tr>
          <td rowspan="3" class="sx">STIVAL s.r.l.</td>
          <td rowspan="3" class="centro"><?php echo $titolo; ?></td>
          <td class="dx1"><?php echo $modulo; ?></td>
        </tr>
        <tr>
          <td class="dx2"><?php echo $rev; ?></td>
        </tr>
        <tr>
          <td class="dx3">Pag. <strong><?php echo $pag; ?></strong> di <strong><?php echo $maxpag; ?></strong></td>
        </tr>
         </table>

<?php 
	}
	
	
	/** Stampa un riquadro con righe orizzontali
	*
	* Misure in mm
	*/
	function printRows( $width, $height, $lineHeight, $margin )
	{
		return ;
		$totHeight = number_format( $height + $margin, 2, '.', '' );
		$height = number_format( $height, 2, '.', '' );
		$lineHeight = number_format( $lineHeight, 2, '.', '' );
		$margin = number_format( $margin, 2, '.', '' );

?>
		<table cellpadding="0" cellspacing="0" border="0" style="/* top: <?php echo $margin ?>mm;*/ margin-top: <?php echo $margin ?>mm; position: static; z-index: 10; border: 1px dotted violet; overflow: hidden; /*background: url(images/sfu.png); */ width: 100%; /*<?php echo $width ?>mm;*/">
			<tbody style="overflow: hidden; height: <?php echo $height ?>mm;">
<?php
	for ( $i=0; $i < $height/$lineHeight; $i++ ) {
?>
				<tr><td style="height: <?php echo $lineHeight?>mm; border-bottom: 1px solid red;">&nbsp;</td></tr>
<?php 
	} 
?>
 			</tbody>
		</table>
		<div style="margin: -<?php echo $totHeight?>mm 0 0 0 ;"></div>
<?php
	}
 
?>
