<?php
require_once("PEAR.php");
//require_once("common.inc.php");
/**
*   @author Daniele Primon
*
* 
*   Classe per la gestione di moduli
* 
*
* 	La classe espleta funzioni di raccolta e controllo dati per la gestione di moduli (form). 
*   Eventuali salvataggi nel db vengono implementati in classi ereditate da questa ridefinendo il moduloDati::elaboraDati().
*  
*/

class moduloDati extends PEAR {

    /*
    * @var string Stato della classe. I valori possibili sono:
    *   "raccoltaDati", "elaborazioneDati", "datiElaborati", "datiInconsistenti"
    */
    var $_status;
    
    var $_erroriInaspettati = array();
    
    /* @var array Contiene i messaggi di errore. Usare Get messages per stamparli
    */
    var $_errorRiscontrati  = array();

	/**
	 * @var array Contiene variabili da mantenere private (es.: id)
	 */ 
	var $_session           = array();
    
    /*
    * @var array Contiene i parametri per l'invio della mail quali recipients, subject, from, template della mail, etc...
    */
//    var $_mailVars = array();

	/**
	 * @var array Contiene i campi visualizzati nel modulo
	 */
    var  $_campi            = array();
    
    var  $_campiChiave      = array();
    
    /**
     *  @access protected 
     *  @var array Dati visualizzati nel modulo di natura dinamica
     */
    var $_datiModulo        = array();

	/**
	* costruttore
	* @param &base Oggetto di classe base che supporta la gestione delle sessioni
	*/
    function moduloDati( &$baseObj ) {
        
        // inizializzazione
        
        $this->PEAR();
        
        // inizializzo collegamenti tra questo oggetto e oggeto baseObj);

		$this->_base =& $baseObj;
		
        $nomeSessione = get_class( $this );

		// se non era già presente la sessione la creo
		is_array($this->_base->session[$nomeSessione]) or $this->_base->session[$nomeSessione] = array();
		
		// mi appoggio alla sessione di $baseObj 
		$this->_session =& $this->_base->session[$nomeSessione];
        
		// aggancio i dati del modulo dalla sessione

		$this->_datiModulo =& $this->_session['dati'];
		$this->_datiModulo or $this->_datiModulo = array(); 

		$this->_datiModulo = array_merge( $this->_datiModulo, $_POST );

		$this->_status =& $this->_session['_status'];

        
//        evid( $this->_status );
//        evid( $this->_session );
        // avvio processo di elaborazione
        if ( isset($_POST['submit']) ) {
			//evid( "dati ricevuti per il salvataggio: $this->_status ");
            $this->_stripSlashes();
            if ( $this->_controlloDati() ) {
                $this->_elaboraDati();
            }   
        } else {
            $this->_status = "raccoltaDati";
			$this->_caricaCampi();
        }
        
        
    }
    
    function _moduloDati() {
        $this->_PEAR();
    }

    /** 
    * @desc Indica se visualizzare o meno la form per l'inserimento dei dati per il contatto 
    * @return boolean
    */
    function visualizzabile() {
        return $this->_status == "raccoltaDati" || $this->_status == "datiInconsistenti";
    }
    
    /** 
    * @desc Indica se la mail di contatto è stata inoltrata
    * @return boolean
    */
    function contieneErrori() {
        return $this->_status == "datiInconsistenti" || $this->_erroriInattesi;
    }
    
    /** 
    * @desc Indica se la mail di contatto è stata inoltrata
    * @return boolean
    */
    function datiElaborati() {
        return $this->_status == "datiElaborati";
    }
    
    /**
    * @desc Indica se ci sono stati errori inaspettati
    * @return boolean
    */
    function erroriInaspettati() {
        return $this->_erroriInaspettati;
    }

    /**
    * @desc Controlla la validità dei dati ricevuti in POST. Restituisce false in caso di errore e imposta lo stato a 'badData'.
    */
    function _controlloDati() {
        // passo htmlentities ai campi inseriti
        foreach ($_POST as $nome => $valore) {
            if (is_string($valore)) {
                $_POST[$nome] = htmlentities($valore);
            }
        }
        return $this->_status != 'datiInconsistenti';
    }
    
    
    /**
    * @desc Funzione 'virtuale'
    * @return bool
    */
    function _elaboraDati() {
      	$this->_status = "elaborazioneDati";
        
        return ;
        
        
        require_once("Mail.php");
        $mailer =& Mail::factory("mail");
        if (PEAR::isError($mailer)) {
            trigger_error($mailer->getMessage(), E_USER_WARNING);
            $this->_status = 'sendError';
            $this->_unexpectedErrors = true;
            return false;
        }
        $recipients = $this->_mailVars['recipients'];
        $headers['From'] = $this->_mailVars['from'];
//        evid($this->_mailVars);
        $this->_mailVars['replyTo'] and $headers['replyTo'] = $this->_mailVars['replyTo'];
        $headers['To'] = $this->_mailVars['to'];
        $headers['Subject'] = $this->_mailVars['subject'];
        $ref =& $this;
        ob_start();
        include($this->_mailVars['template']);
        $body = ob_get_clean();/**/
        if (!$body) {
            trigger_error("Errore nella lettura del template della mail.", E_USER_WARNING);
            $this->_unexpectedErrors = true;
            $this->_status = 'sendError';
            return false;
        }
        //** generate a unique boundry identifier to separate attachments.
        
        $include_path = "{$_SERVER['DOCUMENT_ROOT']}include/";
        setIncludePath($include_path);
        
        require_once("Mail/mime.php");
        // create a new instance of the Mail_Mime class
        $mime = new Mail_Mime();
        // set our plaintext message
        //$mime->setTxtBody($textMessage);
        // set our HTML message
        $mime->setHtmlBody($body);
        // attach the file to the email
//        $mime->addAttachment($attachment);
        // This builds the email message and returns it to $body.
        // This should only be called after all the text, html and
        // attachments are set.

        $body = $mime->get();

//        evid($headers); echo "<hr>";
        $headers = $mime->headers($headers);
//        evid($headers);
        $mailer->send($recipients, $headers, $body);
        if (PEAR::isError($mailer)) {
            $this->_errorMessages[] = "Errore durante l'invio della mail (" . $mailer->getMessage() . ")";
            $this->_unexpectedErrors = true;
            $this->_status = 'sendError';
            return false;
        }
        $this->_status = 'dataSent';
        $this->_unexpectedErrors = false;
        //print_r($this);
        return true;
    }
    
    /**
	 * @author esa
	 *
	 * @access private
	 * 
	 * Ritorna un'array con i campi inseriti nella form. Se sono presenti errori resituisce un'errore fatale.
	 * 
	 * @param boolean $ignoraErrori description
	 * 
	 * @return array Array con 'nomeCampo' => 'valore'
	 */
    function _prelevaDati($ignoraErrori = false) {
		
		
		$tipoErrore = $ignoraErrori ? E_USER_WARNING : E_USER_ERROR; 
		
		if ($this->_status == 'datiInconsistenti') {
			trigger_error("[" . get_class($this) . "] Dati del modulo non validi ", $tipoErrore);
			return ;
		}
	
		if ($this->_status == 'erroriInattesi') {
			trigger_error("[" . get_class($this) . "] Dati del modulo non validi ", $tipoErrore);
			return ;
		}
	
		$ret_data = array();
		
		foreach ($this->_campi as $campo) {
			$ret_data[$campo] = $_POST[$campo];
		}

		
		foreach ($this->_campiChiave as $campo) {
			if (isset($ret_data[$campo])) {
				$this->_erroriInaspettati = true;
				trigger_error("[" . get_class($this) . "] Un campo chiave è già presente", E_USER_WARNING);
			}
			$ret_data[$campo] = $this->_session['campi'][$campo];		
		}
		
		return ($ret_data);
	}
    
    /**
	 * @author esa
	 * @return array Errori stampabili con ciclo foreach
	 */
    function erroriRiscontrati() {
        
        // se l'array non è esiste lo inizializza a vuoto
        is_array($this->_messaggiErrore) or $this->_messaggiErrore = array();
        return $this->_messaggiErrore;
    }

    function _stripSlashes( $setDiDati = null ) {

    	if ( $setDiDati == array() ) return array();
		
    	$backup = array();
		    	
    	if ( is_array( $setDiDati ) ) {
    		$backup = $this->_datiModulo;
    		$this->_datiModulo = $setDiDati;
    	}
    	
		// inizio funzione    	
  		$dati = $this->_datiModulo;
		
		foreach ( $dati as $indice => $contenuto ) {
			if ( is_array( $contenuto ) ) {
				$this->_datiModulo[$indice] = moduloDati::_stripSlashes( $contenuto );							
			} else {
				$this->_datiModulo[$indice] = stripslashes( $contenuto );
			}
		}
   	
    	if ( $setDiDati ) {
    		$setDiDati = $this->_datiModulo;
    		$this->_datiModulo = $backup;
    		return $setDiDati;
    	}
		return $this->_datiModulo;
		
    }    
    

    function _addSlashes( $setDiDati = null ) {

    	$backup = array();
    	
    	if ( $setDiDati ) {
    		$backup = $this->_datiModulo;
    		$this->_datiModulo = $setDiDati;
    	}
    	
		// inizio funzione    	
    	$dati = $this->_datiModulo;
		
		foreach ( $dati as $indice => $contenuto ) {

			if ( is_array( $contenuto ) ) {

				// per implementare la ricorsività devo giocare di copia e sostituzione
				// ingannando ::_addSashes() per farla operare indirettamente su $contenuto 
				
				$copia = $this->_datiModulo;		// salvo la "situazione" 

				$this->_datiModulo = $contenuto;
				$this->_addSlashes();

				$contenuto = $this->_datiModulo;	// $contenuto adesso è stato "processato"
				
				$this->_datiModulo = $copia;		// ripristino la "situazione"
				
				$this->_datiModulo[$indice] = $contenuto; 	// applico il $contenuto processato alla "situazione"
								
			} else {
				$this->_datiModulo[$indice] = addslashes( $contenuto );
			}
		}

   	
    	if ( $setDiDati ) {
    		$setDiDati = $this->_datiModulo;
    		$this->_datiModulo = $backup;
    		return $setDiDati;
    	}
		
		return $this->_datiModulo;
		
    }    
    
	function _caricaCampi () {
		
		
	}
    function _salva() {
		$this->_status = "datiElaborati";
	}
    
}

/*
Local Variables:
mode: php


*/
?>