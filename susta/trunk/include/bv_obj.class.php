<?php
/**
* @package jedzUtils
*/

/**
*    Classe base per tutte le altre, contiene metodi di debug
**/
class bv_obj {

    var $_verbose;	// se true imposta la modalità di debug

    function bv_obj() {
     }

    function verbose_mode($switch) {
    	$this->_verbose = $switch;
    }

    function pD() {
    	echo "<p>".get_class($this)." is ".mkbytes(strlen(serialize($this)))."</p>";
    }
}
?>