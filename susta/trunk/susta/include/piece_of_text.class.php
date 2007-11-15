<?php

require_once "bv_obj.class.php";
require_once "tools.inc.php";

/**
* @package jedzText
*/

/** @desc 
* @version 0.9.0a
*/
class piece_of_text extends bv_obj {

	/** @desc Reference alla stringa contenente l'intero documento
	* @var string 
	* @acces private
	*/
    var $documento;
    
    /** @desc Indice dell'inizio della porzione di testo all'interno di $documento
    * @var int 
    * @access private
    */
    var $inizio;     
    
    /** @desc Lunghezza della porzione di testo all'interno di $documento
    * @var int 
    * @access private
    */
    var $lunghezza;

	/** @desc stringa vuota. utilizzato da tag
	* @access private
	*/
    var $_empty_doc;
	/** @desc Costruttore
	* @param mixed $doc Il documento originario. Può essere una stringa o un piece_of_text e suoi derivati
	* @param int $inizio La posizione iniziale desiderata.
	* @param int $lunghezza La lunghezza iniziale desiderata. Se null verrà considerata la lunghezza massima.
	*  + se $doc un piece_of_text e non sono specificati inizio e lunghezza verranno utilizzare quelle proprie di $doc
	*/

	/** @desc Costruttore
	* @param mixed $doc Il documento originario. Può essere una stringa o un piece_of_text e suoi derivati
	* @param int $inizio La posizione iniziale desiderata.
	* @param int $lunghezza La lunghezza iniziale desiderata. Se null verrà considerata la lunghezza massima.
	*  + se $doc un piece_of_text e non sono specificati inizio e lunghezza verranno utilizzare quelle proprie di $doc
	*/
    function piece_of_text( &$doc, $inizio = 0, $lunghezza = null ) {
        $this->_empty_doc = "";
    	if ( is_a( $doc, "piece_of_text" ) ) 
    	{
			$this->documento = &$doc->documento;
			if ( func_num_args() == 1 )
			{
				$this->inizio = $doc->inizio;
				$this->lunghezza = $doc->lunghezza;
			}
			else 
			{
	        	$this->inizio = $inizio;
        		$this->lunghezza = is_null($lunghezza) ? strlen($this->documento) - $inizio : $lunghezza;
			}				
        }
        else {
        	$this->documento = &$doc;
	        $this->inizio = $inizio;
        	$this->lunghezza = is_null($lunghezza) ? strlen($this->documento) - $inizio : $lunghezza;
        }
    }

	/** @desc Restituisce una copia del piece_of_text specificato
	* @param mixed $p Un piece_of_text o derivato
	* @return piece_of_text
	*/
	function getCopy( $p ) 	{
		$copy = new piece_of_text( $p );
		return $copy;
	}
	
    /** @desc restituisce un piece_of_text vuoto 
    * @return piece_of_text
    */
	function empty_piece( $start = 0 )
	{
		$p = $this;
		$p->inizio =  $start;
		$p->lunghezza = 0;
		return $p;
	}
	
	function is_empty() {
        return $this->lunghezza == 0;
    }


	/**
	* @return int
	* @see end()
	*/
    function start($pos = null) {
        if (!is_null($pos)) {
            if ($pos > $this->end()) {
                trigger_error("La posizione specificata supera quella finale",E_USER_WARNING);
            }
            if ($pos < 0) {
            	trigger_error("La posizione specificata è negativa. Verrà assunto 0.",E_USER_NOTICE);
            	$pos = 0;
            }
            $this->lunghezza += ($this->inizio - $pos);
            $this->inizio = $pos;
         }
        return $this->inizio;
    }



    /** @desc Restituisce la posizione finale del pezzo
    * @param int $pos Se passato imposta la nuova posizione finale
    * @return int la posiozione finale all'interno del testo
    */
    function end($pos = null) {
        if (!is_null($pos)) {
            if ($this->inizio > $pos) {
                trigger_error("La posizione specificata precede quella di inizio",E_USER_WARNING);
            }
            $l = strlen($this->documento);
            if ($pos > $l) {
            	trigger_error("La posizione specificata supera la fine. Verrà assunta la fine del testo",E_USER_NOTICE);
            	$pos = $l;
            }
            $this->lunghezza = $pos - $this->inizio;
         }
        return $this->inizio + $this->lunghezza;
      }




	/** @desc Imposta e restituisce la lunghezza del piece_of_text corrente
	* @param int $l La lunghezza.
	* @return bool La lunghezza corrente (o, se specificata, quella impostata con $l).
	*/
    function length($l = null) {
        if (!is_null($l)) {
            $this->lunghezza = $l;
        }
        return $this->lunghezza;
    }


    /** @desc Determina se il piece_of_text corrente si trova prima di quello specificato
    * @param piece_of_text $p
    * @return bool
    */
    function is_before($p) {
    	return $this->end() <=$p->start();
    }
    
    /** @desc Determina se il piece_of_text corrente si trova dopo quello specificato
    * @param piece_of_text $p
    * @return bool
    */
    function is_after($p) {
    	return $this->start() >= $p->end();
    }
    
    

    /** @desc restituisce il piece_of_text tra $this e $p
    * @param piece_of_text $p 
    * @return piece_of_text
    */
    function piece_between($p) {
        if (!is_a($p,"piece_of_text")) {
            trigger_error("Il parametro non è un piece_of_text", E_USER_WARNING);
            return false;
        }
        if (($p->inizio < $this->inizio && $this->inizio < $p->end()) ||
            (($this->inizio < $p->inizio && $p->inizio < $this->end()))){
            return new piece_of_text($this->documento,0,0); // i pezzi sono accavallati
        } else {
            $inizio = ($this->end() < $p->inizio ? $this->end() : $p->end());
            $lung   = ($this->end() < $p->inizio ? $p->inizio - $this->end() : $this->inizio - $p->end());
            return new piece_of_text($this->documento,$inizio,$lung);
        }
    }

    /** @desc Restituisce un piece_of_text corrispondente al testo trovato (case insensitive)
    * @param mixed $testo Il soggetto da cercare. Può essere una stringa o un piece_of_text
	* @param int $occ L'occorrenza desiderata
	* @return piece_of_text
    */
    function find($testo,$occ=1) {
        if (is_a($testo, "piece_of_text")) {        // assumo che sia un piece_of_text
            $needle = strtolower($testo->get());
        } else {                        // $testo è una stringa
            $needle = strtolower($testo);
        }
        $haystack = strtolower($this->documento);
        $pos = $this->inizio-1;
        do {
            $pos = strpos($haystack,$needle,$pos+1);
            $occ--;
        } while ($pos !== false && $pos < $this->end() && $occ >= 1);
        if ($pos === false || $pos>$this->end()) {
            return new piece_of_text($this->documento,0,0);
        } else {
            return new piece_of_text($this->documento,$pos,strlen($needle));
        }
    }



    // come ::find() ma parte dalla fine per la ricerca (case insensitive, vedi note precedenti)
    function rfind($testo,$occ=1) {
        if (is_a($testo,"piece_of_text")) {        // assumo che sia un piece_of_text
            $needle = strtolower($testo->get());
        } else {                        // $testo è una stringa
            $needle = strtolower($testo);
        }
        $haystack = strtolower($this->documento);
        $pos = $this->end();
        do {
            $pos = strrpos($haystack,$needle,$pos);
            $occ--;
        } while ($pos !== false && $pos < $this->start() && $occ >= 1);
        if ($pos === false || $pos<$this->start()) {
            return new piece_of_text($this->documento,0,0);
        } else {
            return new piece_of_text($this->documento,$pos,strlen($needle));
        }
    }

    function render() { return $this->get(); }

    /**
    * @return string Il piece_of_text sotto forma di stringa
    */
    function get() {
        return substr($this->documento, $this->inizio,$this->lunghezza);
     }

    // restituisce una stringa contente il testo precedente dall'inizio del documento all'inizio di $this
    function preceding_text() {
        return substr($this->documento,0,$this->inizio);
     }



    // restituisce una stringa contente il testo dalla fine this $this alla fine del documento
    function following_text() {
        return substr($this->documento,$this->inizio+$this->lunghezza);
     }



    // restituisce una stringa contente il testo precedente dall'inizio del documento all'inizio di $this

    function &preceding_piece() {

        return new piece_of_text($this->documento,0,$this->inizio);

     }



    // 

    /*
    * @return piece_of_text
    * @desc restituisce un piece_of_text dall'inizio del documento all'inizio di $this
    */
    function &following_piece() {

        return new piece_of_text($this->documento,$this->end());

    }



    // confronta l'inizio e la lunghezza con un altro piece_of_text

    function is_same($p) {

        return ($p->inizio == $this->inizio) && ($p->lunghezza == $this->lunghezza);

    }



    // restituisce un piece_of_text pari all'estensione tra $this e $piece

    function extended_to(&$piece) {

        if (get_class($piece)!=get_class($this)) trigger_error("L'argomento dev'essere un piece_of_text",E_USER_ERROR);

        $inizio = $piece->start() < $this->inizio ? $piece->start() : $this->inizio;

        $fine = $piece->end() > $this->end() ? $piece->end() : $this->end();

        return new piece_of_text($this->documento,$inizio,$fine-$inizio);

     }



    // simile a ::extend_to(), questa allunga (a sinistra oppure a destra a seconda di $indietro) il piece_of_text

    // fino all'n-esima occorrenza di $s

    function extended_to_str($s,$occ=1,$indietro=false) {

        if ($indietro) {

            $resto = $this->preceding_piece();

            $dest = $resto->rfind($s,$occ);

        } else {

            $resto = $this->following_piece();

            $dest = $resto->find($s,$occ);

        }

        if ($dest->length() == 0) {

            return $this;

        } else {

            return $this->extended_to($dest);

        }

    }



    /** @desc Ha il comportamento di preg_match_all ma restituisce un piece_of_text corrispondente all'$occ-esima occorrenza.
	* @param string $pattern Espressione regolare 
	* @param int $occ se è -1 restiuisce l'ultima, -2 la penultima e così via.
	* @param int $sub_pattern Il sub pattern che si vuole restituito. Se è 0 restituisce il risultato dell'intera
	*            espressione.
	* @return piece_of_text
	*/
    function &regex_match($pattern, $occ = 1, $sub_pattern = 0) {
    	if ($occ > 0) {
        	$offset = 0;
        	$m = array();
        	$subject = $this;
        	do {
	        	if (!preg_match($pattern, $subject->get(), $m, PREG_OFFSET_CAPTURE)) {
	        		return new piece_of_text($this, 0, 0);
	        	}
	        	$offset = $m[$sub_pattern][1];
				$result = new piece_of_text($this->documento, $subject->inizio + $offset, strlen( $m[$sub_pattern][0]));        	
	        	if (--$occ) $subject->start($result->end()+1);
	        	//echo "<pre>" . print_r($m) . "</pre>"; 
        	} while ($occ);
        	#echo "<pre>"; print_r($m); echo "</pre>";
			return $result;
        } 
        elseif ($occ < 0) {
        	$m = array();
	    	preg_match_all($pattern, $this->get(), $m, PREG_OFFSET_CAPTURE|PREG_PATTERN_ORDER);
	        if ($this->_verbose) {
	        	evid(count($m[$sub_pattern])." -- ".abs($occ));
	        }
	        if (count($m[$sub_pattern]) < -$occ)
	            return new piece_of_text($this->documento,0,0);
            $occ = count($m[$sub_pattern]) + $occ;
            $gigi = new piece_of_text($this->documento,$m[$sub_pattern][$occ][1] + $this->inizio, strlen($m[$sub_pattern][$occ][0]));
            if ($this->_verbose) $gigi->pD();
	        return $gigi;
        }
        else {
        	return new piece_of_text($this->documento,0,0);
        }

    }



    /** @desc Simile a preg_match_all restituisce in $matches un array di piece_of_text contenente tutti i risultati
	* @param string $pattern
	* @param array $matches
    * @return int ritorna il numero di occorrenza del pattern intero o false in caso di errore
	*/
    function regex_match_all($pattern,&$matches) {
       	$m = array();
        $numero = preg_match_all($pattern, $this->get(), $m, PREG_OFFSET_CAPTURE|PREG_PATTERN_ORDER);
        if ($numero === false) {
            trigger_error("Espressione: <span style=\"font-face: Monospace\">".htmlentities($pattern)."</span> non valida",E_USER_WARNING);
            return false;
        }
        for ($i = 0; $i < count($m); $i++) {
            for ($j = 0; $j < count($m[$i]); $j++) {
                if (is_array($m[$i][$j])) {
                    if ($m[$i][$j][1] != -1) {
                        $matches[$i][$j] = new piece_of_text($this->documento,$m[$i][$j][1] + $this->inizio, strlen($m[$i][$j][0]));
                    }
                    else {
                        $matches[$i][$j] = new piece_of_text($this->documento,$this->inizio, 0);
                    }
                }
                else {
                     $matches[$i][$j] = $m[$i][$j];
                }
            }
        }
        return $numero;
    }



    /** printDebug info
    * @param int Una delle combinazioni di colori (testo/sfondo) disponibili.
    * Le combinazioni disponibili sono:
    *     1 => green/yellow
    *     2 => white/blue
    *     3 => crimson/greenyellow
    *     4 => ghostwhite/midnightblue
    *     5 => lightslategray/whitesmoke
    *     qualsiasi => whitesmoke/purple
    */
    function pD($color = "default") {
        switch ($color) {

            case 1:

                $pp = "green";    $sf = "yellow";

                break;

            case 2:

                $pp = "white";  $sf = "blue";

                break;

            case 3:

                $pp = "crimson";  $sf = "greenyellow";

                break;

            case 4:

                $pp = "ghostwhite";  $sf = "midnightblue";

                break;

            case 5:

                $pp = "lightslategray";  $sf = "whitesmoke";

                break;

            default:

                $pp = "whitesmoke";  $sf = "purple";

                break;

        }

        $i = $this->inizio;
        $f = $this->end();
        echo "&nbsp;";
        evid($this->get(),$pp,$sf);
        echo "&nbsp;<sup style=\"background: $sf; font-face: Courier New; color: $pp; font-size: 90%\" title=\"$i-$f ".sprintf("(0x%x-0x%x)",$i,$f)."\">(*)</sup>&nbsp;";

    }



 }
?>