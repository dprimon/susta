<?php

require_once "piece_of_text.class.php";

/**
* @package jedzText
*/

define ("TAG_CONTEXT_BEFORE", 	0x01);
define ("TAG_CONTEXT_AFTER",    0x02);
define ("TAG_CONTEXT_INSIDE",   0x03);
define ("TAG_CONTEXT_FULL",     0x00);

/* Classe per la ricerca e l'analisi di un tag
* @author eSa
* @version 1.0.0
*/
class tag extends piece_of_text {
    /*
    * @access
    * @var &piece_of_text Il tag di apertura
    */
    var $apertura;

    /*
    * @access
    * @var &piece_of_text Il tag di chiusura
    */
    var $chiusura;

    /*
    * @access
    * @var &piece_of_text Il contenuto del tag
    */
    var $contenuto;


    /*
    * @access private
    * @var string Il nome del tag corrente (se il tag corrente è valido)
    */
    var $_nome;
    
    /*
    * @access private
    * @var array Array contenente gli eventuali attributi del tag corrente (se valido)
    */
    var $_attributes;
    
    /*
    * @access private
    * @var bool true se il tag corrente è singolo o ha un suo tag di chiusura, null se lo stato è indefinito
    */
    var $_single;
    
    /*
    * @access private
    * @var bool true se il tag è valido, null se lo stato è indefinito
    */
    var $_valid;
    
    /** @desc Costruttore della classe
    * @param piece_of_text $a il tag d'apertura
    * @param piece_of_text $c il tag di chiusura.
    *  + Il piece_of_text compreso tra apertura e chiusura diviene il contenuto.
    *  + Se specificato, il tag di chiusura si deve trovare in una posizione successiva a quello di apertura.
    *    Si presuppone inoltre che il tag di apertura non sia vuoto
    */
    function tag($a, $c = NULL) {
        $this->_valid = null;
		$this->_single = null;
        $this->_nome = "";
		$this->_attributes = null;
		$this->apertura = null;
		$this->chiusura = null;
//		if (!is_null($a)) {
		if ( !is_a( $a, "piece_of_text" ) )
		{
			trigger_error("Il paramentro 1 non è un piece_of_text",E_USER_ERROR);
		}
		$this->set_opening($a);
//		}
//		else {
//			$this->piece_of_text($this->_empty_doc,0,0);
//		}
		if (!is_null($c)) {
			if (!is_a($c,"piece_of_text")) {
				trigger_error("Il paramentro 2 non è un piece_of_text",E_USER_ERROR);
			}
			$this->set_closing($c);
		}
//        $this->is_valid();
    }

    /** @desc Imposta il piece_of_text specificato come apertura
    * @param piece_of_text
    * @return bool true se l'impostazione è andata a buon fine
    */
    function set_opening( $p ) {
    	if ( !is_a( $p, "piece_of_text" ) ) {
    		trigger_error( "Il parametro non è un piece_of_text", E_USER_ERROR );
    	}
    	$this->apertura = piece_of_text::getCopy( $p );
    	$this->piece_of_text( $this->apertura );
    	$this->contenuto = null;
    	$this->chiusura = null;
    	$this->_valid = null;
    	$this->_single = null;
    	return true;
    }

    /** @desc Imposta il piece_of_text specificato come chiusura
    * @param piece_of_text
    * @return bool true se la coppia risultante è valida
    */
    function set_closing($p) {
    	if (!is_a($p,"piece_of_text")) {
    		trigger_error("Il parametro non è un piece_of_text",E_USER_ERROR);
    	}
    	if (is_null($this->apertura) || $this->apertura->is_empty()) {
    		trigger_error("Il tag di apertura non è stato impostato o è vuoto.",E_USER_ERROR);
    	}
    	if ($p->start() < $this->apertura->end() || $p->is_empty()) {
    		trigger_error("Il piece_of_text è vuoto o precede il tag di apertura.",E_USER_WARNING);
    	}
    	$this->chiusura = $p;
    	$this->contenuto = $this->apertura->piece_between($p);
    	$this->end($this->contenuto->end() + $p->length());
    	$this->_valid = null;
    	$this->_single = null;
    	return $this->is_valid();
    }
    
    /** @desc Imposta una coppia di tag
    * @param piece_of_text $tag1
    * @param piece_of_text $tag2
    * @return void
    */
    function set_pair($tag1,$tag2) {
		if (!is_null($tag1)) {
			if (!is_a($tag1,"piece_of_text")) {
				trigger_error("Il paramentro 1 non è un piece_of_text",E_USER_ERROR);
			}
			$this->set_opening($tag1);
		}
		if (!is_null($tag2)) {
			if (!is_a($tag2,"piece_of_text")) {
				trigger_error("Il paramentro 2 non è un piece_of_text",E_USER_ERROR);
			}
			$this->set_closing($tag2);
		}
    }
    /** @desc Trova e restituisce il tag specificato. Se si cerca solo un tag di apertura usare {@inline find_opening_tag()}
    * @param string $tag Il tag da cercare (senza '<','>')
    * @param array $attributes opzionale degli attributi che il tag specificato dovrà contenere. Il contenuto
    *              di ogni attributo verrà interpretato come expressione regolare
    * @param int $context Indica se effettuare la ricerca prima, dentro oppure fuori del tag corrente
    *            (vedi costanti TAG_CONTEXT_XXXX all'inizio)
    * @param bool $reverse se true significa che la ricerca partirà dalla fine (anzichè il primo trovato, verrà restituito l'ultimo).+
    *             Se non viene specificato sarà true se $context è TAG_CONTEXT_BEFORE, altrimenti false.
    * @return bool true se il tag è stato trovato.
    */
    function find_tag($tag, $attributes = null, $context = TAG_CONTEXT_INSIDE, $reverse = null) {
		if (is_null($reverse)) {
			$reverse = $context == TAG_CONTEXT_BEFORE ? true : false;
		}
    	$found_tag = $this;
		$found_tag = $found_tag->find_opening_tag($tag, $attributes, $context, $reverse);
		if ($found_tag->is_empty()) {
			return $found_tag;
		}
		if (!$found_tag->is_valid() && !$found_tag->is_single()) {
			$chiude = $found_tag->match_closing_tag();
			if (!$chiude->is_empty()) {
				$found_tag->set_closing($chiude);
			}
		}
		return $found_tag;
      }

    /** @desc Restituisce il tag (tipo xhtml) che racchiude quello corrente
    * @param array $attributes Array opzionale degli attributi che il tag specificato dovrà contenere. Il contenuto
    *              di ogni attributo verrà interpretato come expressione regolare
    * @param string $tag Il tag da cercare (senza '<','>')
    * @return &tag
    *  + se il parametro $tag è null o una stringa vuota viene restituito il primo tag trovato
    *  + se il tag specificato non viene trovato viene restituito false e generato un Warning
    * <b>Nota</b>: Non è neccessario che il tag corrente sia valido!
    */
    function enclosing_tag($tag = null, $attributes = null) {
    	$enc_tag = $this;
		do {
			$enc_tag = $enc_tag->find_tag($tag,$attributes,TAG_CONTEXT_BEFORE,true);
			if ($enc_tag->is_empty()) {
				return $enc_tag;
			}
			$c = &$enc_tag->chiusura;
			$found = $c && !$c->is_empty() && $c->start() >= $this->end();
		} while (!$found);
		if ($c->is_empty()) {
			return new tag($c);
		}
		return $enc_tag;
		
/*		VECCHIO CODICE PER LA RICERCA DEL TAG DI CHIUSURA
	
			$p_1 = $apre->piece_between($this->apertura);
            if ($this->_verbose) {
                $apre->pD();
                $p_1->pD(2);
            }
			// effettuo la ricerca su un piece_of_text da cui è stato tolto $this->get();
			$text = $apre->get().$p_1->get().$this->chiusura->following_text();
	        $chiude = new piece_of_text($text);
            $t = new tag($chiude);
            $t = $t->find_tag($tag);
			// calcolo il piece_of_text trovato nel contesto corrente
			$nuovo_offset = $apre->start() + $t->chiusura->start() +
            	($t->chiusura->start() < $this->start() - $apre->start() ?  0 : $this->length());
            $t = new tag(new piece_of_text($this->documento,$apre->start(), $apre->length()),
            			 new piece_of_text($this->documento, $nuovo_offset ,$t->chiusura->length()));
            $occ++;
#            $t->chiusura->pD(4);
#            evid($t->chiusura->start()." < ".$this->apertura->start(),"white","dimgray");
    	} while ($t && $t->chiusura->start() < $this->apertura->start());	// mi assicuro che il tag di chiusura stia dopo il tag corrente, altrimenti ne cerco un'altro'
		return $t;/**/
    }

	/** @desc Cerca un tag d'apertura che soddisfa gli eventuali attributi specificati.
    * @param string $tag Il tag da cercare senza le parentesi angolari ('<','>')
    * @param array $attributes opzionale degli attributi che il tag specificato dovrà contenere. Il conenuto
    *              di ogni attributo verrà interpretato come expressione regolare
    * @param int $context Indica se effettuare la ricerca prima, dentro oppure fuori del tag corrente
    *            (vedi costanti TAG_CONTEXT_XXXX all'inizio)
    * @param bool $reverse Fa sì che la ricerca parta dalla fine verso l'inizio. Come $reverse in find_tag()
    * @return tag Se il tag restituito è vuoto significa che la ricerca non ha avuto buon fine
    * + Se tag non è specificato ne viene cercato uno generico
    */
    function find_opening_tag($tag = "", $attributes = null, $context = TAG_CONTEXT_INSIDE, $reverse = null) {
		if (is_null($reverse)) {
			$reverse = $context == TAG_CONTEXT_BEFORE ? true : false;
		}
        switch ($context) {
           case TAG_CONTEXT_BEFORE:
               $haystack = $this->preceding_piece();
               break;
           case TAG_CONTEXT_INSIDE:
            	if (!is_null($this->contenuto)) {
           			$haystack = $this->contenuto;
	               	break;
            	} 
            	$context = TAG_CONTEXT_AFTER;
           case TAG_CONTEXT_AFTER:
               $haystack = $this->following_piece();
               break;
           case TAG_CONTEXT_FULL:
               $haystack = new piece_of_text( $this->documento );
               break;
        }
        if (!$tag) {
            $pattern = "/(<(\w+)[^>]*>)/i";
        } else {
            $pattern = "/(<".$tag."[^>]*>)/i";
        }
        do {
            $apre = $haystack->regex_match($pattern,$reverse ? -1 : 1);
            $t = new tag($apre);
            $attributesOK = !$attributes || $t->check_attributes($attributes);
            if (!$apre->is_empty() && !$attributesOK)  {
            	// restringiamo il campo di ricerca
            	if ($context == TAG_CONTEXT_BEFORE) {
            		if ($reverse) {
            			$haystack = $apre->preceding_piece();
            		} 
            		else {
            			$haystack = $apre->piece_between($this);
            		}
            	}
            	if ($context == TAG_CONTEXT_INSIDE) {
            		if ($reverse) {
            			$haystack = $apre->piece_between($this->apertura);
            		} 
            		else {
            			$haystack = $apre->piece_between($this->chiusura);
            		}
            	}
            	if ($context == TAG_CONTEXT_AFTER) {
            		if ($reverse) {
            			$haystack = $apre->piece_between($this);
            		} 
            		else {
            			$haystack = $apre->following_piece();
            		}
            	}
            	if ($context == TAG_CONTEXT_FULL) {
            		if ($reverse) {
            			$haystack = $apre->preceding_piece();
            		} 
            		else {
            			$haystack = $apre->following_piece();
            		}
            	}
            } 
        } while (!$apre->is_empty() && !$attributesOK);
        return $t;
    }
	
    /** @desc Indica se il tag specificato è singolo.
    * Se tag non è specificato effettua il controllo sul tag corrente corrente è singolo (come ad es.: <br> o <td />)
    * oppure comprende la chiusura.
    * + Supporta la chiusura XHTML di un tag singolo
    * @return bool true se è singolo
    * <b>Nota:</b> Per accertarsi che il tag corrente sia un tag (x)html singolo usare anche ::is_valid();
    */
    function is_single($tag = null) {
    	// Controllo $tag
    	if (!is_null($tag)) {
    		if (is_a($tag, "piece_of_text")) {
    			$tag = $tag->get();
    		}
    		$lonely_tags = explode(" ", NON_CLOSED_TAGS);
    		$tag_matchs = array();
    		preg_match("/(?:<?)(\w+)(?:.*?(\/>)?)/i", $tag, $tag_matchs);
    		return in_array($tag_matchs[1], $lonely_tags) || array_key_exists(2, $tag_matchs);
    	}
    	// Altrimenti controllo il tag corrente
/*    	if (!$this->_valid) { 
    		trigger_error("Tag non valido",E_USER_WARNING);
    		return null;
    	}
    	if (is_null($this->_single)) {
    		preg_match("/(?:<?)(\w+)(?:.*?(\/>)?)/i",$tag,$t);
			$this->_single =in_array($t[1], $lonely_tags) || array_key_exists(2,$t);
    	}*/
    	$this->is_valid();
		return $this->_single;
    }
    
    /** @desc Trova il tag di chiusura corrispondente a quello specificato
    * @param piece_of_text $apertura Il tag di apertura. Se non specificato verrà considerato quello corrente.
    * @return piece_of_text Il tag trovato. Se il tag non è stato trovato viene resituito un piece_of_text vuoto.
    * @see find_opening_tag()
    */
    function match_closing_tag($apertura = null) {
    	if (is_null($apertura)) $apertura = $this->apertura;
    	if (!$tag = $this->is_tag($apertura)) {
    		trigger_error("Impossibile cercare il tag di chiusura perchè quello di apertura non è valido.", E_USER_WARNING);
    		return false;
    	}
    	if ($this->is_single($tag)) {
    		trigger_error("Non cerco la chiusura poichè il tag corrente è singolo", E_USER_WARNING);
    		return false;
    	}
    	$ap = $apertura;
    	$ch = $apertura;
        do {
        	$ap = $ap->following_piece();
        	$ch = $ch->following_piece();
        	$ap = $ap->regex_match("/(<($tag)[^>]*>)/i");
        	$ch = $ch->regex_match("/(<\/($tag)[^>]*>)/i");
        } while ( $ap->start() < $ch->start() && !$ch->is_empty() && !$ap->is_empty() );
		return $ch;	
    }

    /** @desc Restituisce il nome del tag corrente oppure false se il tag non è valido.
    * @return bool
    */
    function get_name() {
    	return $this->is_valid() ? $this->_nome : false;
    }
    
    /** @desc Restituisce un array $attributo => $valore contenente gli attributi trovati nel tag corrente
    * @return array L'array contenente gli attributi oppure un array vuoto se non ce ne sono
    */
    function get_attributes() {
    	if (is_null($this->_valid)) {
    		$this->is_valid();
    	}
    	if (!$this->is_tag($this->apertura)) {
    		$lineNr = substr_count($this->apertura->preceding_text(), "\n");
    		trigger_error(($this->_single ? "single " : "coppia ")." Il tag corrente non è valido (Riga $lineNr della sorgente)",E_USER_WARNING);
    		return array();
    	}
		if ($this->_attributes) {
			return $this->_attributes;
		}
		$data = array();	// per contenere i risultati del regex
		preg_match_all("/(\w+)\s*=\s*([\"'])(.*?\\2)/i",$this->apertura->get(),$data);
		$this->_attributes = array();
		for ($i = 0; $i < count($data[0]); $i++) {
			$this->_attributes[$data[1][$i]] = substr($data[3][$i],0,-1);
        }
        if ($this->_verbose) {
        	echo "<pre>Attributi: ";
        	print_r($this->_attributes);
        	echo "</pre>";
        }
        return $this->_attributes;
    }

	/** @desc Controlla il contenuto degli attributi specificati. Il valore di ogni attributo è interpretato
    * come espressione regolare
    * @param array $attributes l'array degli attributi nel formato ritornato da {@link get_attributes()}.
    * @return bool Restituisce true se tutti gli attributi soddisfano le relative espressioni
    * @see get_attributes()
    */
	function check_attributes($attributes) 
    {
        $myAttributes = $this->get_attributes();
        if (!is_array($myAttributes)) { 
        	return false; 
        }
        // $attributes dev'essere nullo o un'array
        if (!is_array($attributes) && !is_null($attributes)) {
        	var_dump($attributes);
        	evid($attributes);
        	return false;
        }
        if (is_array($attributes)) {
	        foreach ($attributes as $key => $value) {
	            if (array_key_exists($key,$myAttributes)) {
	            	/* se $value non è un'espressione regolare la imposto per il match "esatto" */
	            	if ($value{0} != $value{strlen($value)}) {
	            		$value = "/^$value\$/";
	            	}
	            	if (!preg_match($value, $myAttributes[$key])) {
	                    return false;
	                }
	                unset($myAttributes[$key]);
	            }
	            else {
	               return false;
	            }
	        }
        }
        return true;
    }
	/** @desc Rivela se $tag è contenuto all'interno di quello corrente
    * @param tag $tag 
    * @return bool Restituisce true se $tag è contenuto
    */
	function contains($tag) 
    {
		get_class( $tag ) == 'piece_of_text' or get_class( $tag ) == 'tag' or trigger_error( 'Classe ' . get_class( $tag ) . ' non riconosciuta' , E_USER_WARNING );
        return !$this->is_empty() and !$tag->is_empty() and $this->apertura->is_before($tag) and $this->chiusura->is_after($tag) ;
    }

	function verbose_mode($switch) 
	{
        parent::verbose_mode($switch);
        if ($this->apertura) $this->apertura->verbose_mode($switch);
        if ($this->chiusura) $this->chiusura->verbose_mode($switch);
        if ($this->contenuto) $this->contenuto->verbose_mode($switch);
    }

    
    /** @desc Rivela se l'argomento è un tag, ovvero un pezzo di testo racchiuso tra parentesi angolari
    * @param mixed $subject Testo sul quale viene effettuato il controllo (di tipo stringa o piece_of_text)
    * @return string Restituisce una stringa col nome del tag se $subject ne ha la forma altrimenti una stringa vuota
    *
    * Per conoscere la validità del tag corrente utilizzare {@link is_valid()}
    */
    function is_tag(&$subject) {
    	if ($subject) {
   			$test_string = is_a($subject,"piece_of_text") ? $subject->get() : $subject;
   			$tag_matches = array();		// per contenere i risultati della regex
   			return preg_match("/(?:<)(\w+)(?:.*?(?:(\/?)>))/i",$test_string, $tag_matches) ? $tag_matches[1] : false;
    	}
    	return false;
    }
    
	/** @desc Verifica se il tag corrente è valido.
	* Controlla che il tag di chiusura corrisponda a quello di apertura e che si trovi dopo di esso
    * @return bool Restituisce true se il tag è valido
    */
    function is_valid() {
    	if (!is_null($this->_valid)) {
    		return $this->_valid;
    	}
    	$a = &$this->apertura;		// alias di comodo
    	$c = &$this->chiusura;		// alias di comodo
    	$tag_matches = array();		// per contenere i rusultati della regex
    	if (preg_match("/(?:<)(\w+)(?:.*?(?:(\/?)>))/i", $a->get(), $tag_matches)) {
    		$this->_nome = $tag_matches[1];
    		$lonely_tags = explode(" ", NON_CLOSED_TAGS);
    		if (in_array($this->_nome, $lonely_tags) || ($tag_matches[2] == "/")) {
	    		$this->_single = !$c || $c->is_empty();
	    		$this->_valid = $this->_single;
    		} else {
				$this->_single = false;
    			$this->_valid = $c && preg_match("/(<\/".$this->_nome."[^>]*>)/i", $c->get()) && $a->is_before($c);
    		}
   			return ($this->_valid);
		}
		$this->_valid = false;
		$this->_single = is_null($c);
		$this->_nome = "";
		return false;
    }
    
    /** @desc Stampa informazioni per il debug
    * @return void
    */
    function pD() {
    	echo '<div style="margin: 3px 3px 3px 3px; border: dotted 1px blue; border-bottom: solid 1px navy; border-right: solid 1px navy; background: antiquewhite">';
    	if ($this->_valid) {
			if ($this->_single) {
				echo "xhtml-like valid single <i>{$this->_nome}</i> tag.<br>";
			}
			else {
				echo "xhtml-like valid <i>{$this->_nome}</i> tag.<br>";
			}
        } else {
        	echo "Generic ".($this->apertura->is_empty() ? "empty" : "")." tag.<br>";
        }
        echo '<span style="font: bold 9px Serif; color: black;">Opening </span>';
        $this->apertura->pD(4);
	    if ($this->chiusura) {
        	echo "&nbsp;&nbsp;";
	    	echo '<span style="font: bold 9px Serif; color: black;">Closing </span>';
	        $this->chiusura->pD(4);
	    }
        echo "</div>";
    }

}

?>