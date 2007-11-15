<?php

require_once("include/mytools.inc.php");

require_once("include/moduloDati.class.php");

class mod_licenzia extends moduloDati {
	
	/**
	 * @var array Riferimento alle variabile di sessione dedicate alla classe 
	 */
	var $_session;
	
	/**
	 *  @var DB Riferimento al db base dell'applicazione
	 */
//	var $_db;
	
	/**
	 * @var base Riferimento alla classe di gestione base dell'applicazione
	 * 
	 */
	var $_base;

    // campi di questo modulo
    var $_campi = array( 'conferma' );
    
    var $_campiChiave = array( 'id' );


    function mod_licenzia( &$baseObj ) {


/*        // inizializzo collegamenti tra questo oggetto e oggeto baseObj);

        $this->_nomeAreaSessione = 'mod_dipendente';

		$this->_base =& $baseObj;
		
		is_array($this->_base->session['mod_dipendente']) or $this->_base->session['mod_dipendente'] = array();
		
		// mi appoggio alla sessione di $baseObj 
		$this->_session =& $this->_base->session['mod_dipendente'];
  */      
		$this->_dati =& $this->_session['campi'];

		$this->moduloDati($baseObj);	// costruttore di default da chiamare

/*       
		if ( $this->_status == "raccoltaDati" ) {
			// imposto i campi, se neccessario carica da db
			$this->_caricaCampi();
		}
*/		
		 
//		$this->_stampaModulo();
		
    }
    
    
	function _controlloDati() {
/*        if ( $_POST['conferma'] == "" ) {
            $this->_messaggiErrore[] = "Alcuni campi neccessari non sono stati compilati.";
            $this->_status          = "datiInconsistenti";
        }

        /*if (!$_POST['privacy']) {
            $this->_messaggiErrore[] = "&Eacute; neccessario visionare e accettare l'informativa per poter inviare il messaggio.";
            $this->_status          = "datiInconsistenti";
        } */
        return parent::_controlloDati();
    }
    
    function _elaboraDati() {
    	if ( $this->_status == 'datiElaborati') return ;
		parent::_elaboraDati();
    	
    	$rec = $this->_prelevaDati();
//		evid($rec);

		if ( $rec['conferma'] == "true" ) {
			$this->_licenzia();
		} else {
			header("Location: licenziamento.php");	
		}
		$this->_status = "datiElaborati";	
    }
    
	function _caricaCampi() {
		
		$this->_session['campi']['id'] = $_REQUEST['id'];

		// se non ho un id non ho niente da caricare
		
		if ( !$this->_session['campi']['id'] ) {
			return false;
		}
		
		
		// arrivato fin qui, leggo i campi dal database 
		$campi = array();
		
		$statement = "SELECT * FROM `d-anagrafica` "
				   . "WHERE `d-anagrafica`.id = {$this->_session['campi']['id']} " 
		           . "ORDER BY assunzioneData, cognome, nome "
		           . "";
		           
        $this->_base->_dbQuery("getRow", $statement, $campi);

//        evid($statement);
        DB::isError($campi) and trigger_error("Errore query", E_USER_WARNING);
        

//        evid($campi);
        $_POST = array_merge($_POST, $campi);
        
		// sistema i dati per la presentazione
		$this->_accomodaDatiCaricati();

		return true;
		
	}
	
    /*
     * Salva i dati del modulo nel db
     */
    function _licenzia() {
    	
		$this->_base->_dbConnect();
		$dbh =& $this->_base->_db;

//		$this->_accomodaDatiDaSalvare($rec);
	
		
		$rec['assunzioneData'] = "";
		
		$this->_base->_dbConnect();
		$dbh =& $this->_base->_db;

		
		$res = $dbh->autoExecute("`d-anagrafica`", $rec, DB_AUTOQUERY_UPDATE, "id={$this->_session['campi']['id']}") 
			or trigger_error("Licenziamento dipendente fallito", E_USER_ERROR);
		
//		evid($res);

		if (DB::isError($res)) {
			trigger_error("Query fallita:<br> " . $res->getMessage(), E_USER_WARNING);
			$this->_erroriInaspettati = true;
			return false;
		}
		 
		$this->_status = "datiElaborati";
	}
	
    /**
     * @author esa
     * 
     * Elabora eventuali dati caricati da db per accomodare eventuali esigenze (es.: visualizzazione)
     */
    function _accomodaDatiCaricati() {
    	
    	$_POST['assunzioneData'] = base::adjustMysqlDate( $_POST['assunzioneData']);
    	$_POST['nascitaData'] = base::adjustMysqlDate($_POST['nascitaData']);
//    	$_POST['assunzioneData'] = base::adjustMysqlDate();
    	
//		$_POST['Data'] = strftime("%d/%m/%Y");
		$this->_stripSlashes();	
    }
	
    /**
     * @author esa
     * 
     * Elabora eventuali dati caricati da db per accomodare eventuali esigenze (es.: visualizzazione)
     */
    function _accomodaDatiDaSalvare( &$rec ) {
    	
		
    	$rec['assunzioneData'] = base::adjustMysqlDate( $rec['assunzioneData']);
    	$rec['nascitaData'] = base::adjustMysqlDate( $rec['nascitaData']);
    	

		$this->_addslashes( $rec );    	
    }
	
  
  	function _stampaModulo() {
  		require("sch_indiv.php");
  		
  		stampa_scheda($_POST);
  	}
    
}
?>