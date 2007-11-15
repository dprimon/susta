<?php

require_once("include/mytools.inc.php");

require_once("include/moduloDati.class.php");

class mod_formazione extends moduloDati {
	
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
    var $_campi = array( 'for_data', 'for_descrizione' ,'for_valutazione', 'qualifica1', 'qualifica2', 'qualifica3', 'qualifica4', 
						'qualifica1Data', 'qualifica2Data', 'qualifica3Data', 'qualifica4Data',  
     );
    
    var $_campiChiave = array( 'for_id', 'for_dipendente' );

	/**
	 * @access public
	 * @var array Possibili valutazioni assegnabili visualizzate nella select
	 */
	var $valutazioni = array( "insufficiente", "sufficiente", "buona", "ottima" );
	
	/**
	 * @var array Viene popolato con lo storico delle valutazioni del dipendente per la visualizzazione 
	 */
	var $storico = array();

    function mod_formazione( &$baseObj ) {

		$this->storico =& $this->_datiModulo['storico'];
		
		$this->moduloDati( $baseObj );	// costruttore di default da chiamare


/*		if ( $this->_status == "raccoltaDati" ) {
			// imposto i campi, se neccessario carica da db
			$this->_caricaCampi();
		}
		 */
//		$this->_stampaModulo();
		
    }
    
    
       function _controlloDati() {
       	//evid( $_POST );
//        if ( $_POST['for_data'] and $_POST['for_descrizione'] ) 
//        {
//            $this->_messaggiErrore[] = "Alcuni campi neccessari non sono stati compilati.";
//            $this->_status          = "datiInconsistenti";
//        }

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
	  	

		if ($this->_salva($rec)) {
			$this->_status = "datiElaborati";
			$this->_caricaCampi();	
		}
		
    }
    
	function _caricaCampi() {
		
/*		evid( $this->_session );
		evid( $_REQUEST['id'] );

		if ( $this->_session['campi']['for_dipendente'] && 
			$_REQUEST['id'] &&
			$this->_session['campi']['for_dipendente'] != $_REQUEST['id'] )
		{
			evid( $this->_session );
		}
*/		
		$_REQUEST['id'] and $this->_session['campi']['for_dipendente'] = $_REQUEST['id'];

		// se non ho un id non ho niente da caricare
		
		if ( !$this->_session['campi']['for_dipendente'] ) {
			evid( "RETURNED");
			return false;
		}
		
		
		// arrivato fin qui, leggo i campi dal database 

		// nota: qui carico i dati solo per la visualizzazione.
		// Questo modulo gestisce l'inserimento ma non la modifica 
		$campi = array();
		
		$statement = "SELECT * FROM `d-formazione` "
				   . "WHERE `d-formazione`.for_dipendente = {$this->_session['campi']['for_dipendente']} " 
		           . "ORDER BY for_data ASC "
		           . "";
		           
        $this->_base->_dbQuery("getAll", $statement, $campi);

//        evid($statement);
        DB::isError($campi) and trigger_error("Errore query", E_USER_WARNING);
        
//        evid($campi);
        

		$this->storico = $campi;
		
		
		$campi = array();
		
		$statement = "SELECT nome, cognome, qualifica1, qualifica2, qualifica3, qualifica4, "
		           . "qualifica1Data, qualifica2Data, qualifica3Data, qualifica4Data "
		           . "FROM `d-anagrafica` "
				   . "WHERE `d-anagrafica`.id = {$this->_session['campi']['for_dipendente']} " 
		           . "";
		           
        $this->_base->_dbQuery("getRow", $statement, $campi);

        $_POST = array_merge($_POST, $campi);
        $this->_datiModulo = array_merge( $this->_datiModulo, $_POST );
        
		// Di default data attuale
		$this->_datiModulo['for_data'] = strftime( "%Y-%m-%d" );
		$this->_datiModulo['for_descrizione'] = "";
		$this->_datiModulo['for_valutazione'] = "";

		// sistema i dati per la presentazione
		$this->_accomodaDatiCaricati();

        $_POST = array_merge($_POST, $this->_datiModulo );


		return true;
		
	}
	
    /*
     * Salva i dati del modulo nel db
     */
    function _salva($rec) {
    	
		$this->_base->_dbConnect();
		$dbh =& $this->_base->_db;

		$this->_accomodaDatiDaSalvare( $rec );
		
		//evid( $this->_datiModulo );
    	
    	// salvo qualifiche 
    	$rec_ana = array();
		$rec_ana = array_filter_keys( $rec,	
					array(
						'qualifica1', 'qualifica2', 'qualifica3', 'qualifica4', 
						'qualifica1Data', 'qualifica2Data', 'qualifica3Data', 'qualifica4Data',  
					));
    	
//    	evid( $rec_ana );
		$res = $dbh->autoExecute("`d-anagrafica`", $rec_ana, DB_AUTOQUERY_UPDATE, "id={$this->_session['campi']['for_dipendente']}") 
				or trigger_error("Aggiornamento anagrafica dipendente fallito", E_USER_ERROR);
		
//		evid( $this->_session['campi'] );
		
//		evid($res);
		if (DB::isError($res)) {
			trigger_error("Query fallita:<br> " . $res->getMessage(), E_USER_WARNING);
			$this->_erroriInaspettati = true;
			return false;
		} 
		
		// aggiungo record formazione
		$rec_for = array();
		$rec_for = array_filter_keys( $rec,	
					array(
						'for_data', 'for_descrizione', 'for_valutazione', 'for_id', 'for_dipendente',  
					));
//    	evid( $rec_for );
		
		if ( $rec_for['for_data'] && $rec_for['for_descrizione'] )
		{
			$rec_for['for_id'] = $dbh->nextId("d-formazione");
			$res = $dbh->autoExecute("`d-formazione`", $rec_for, DB_AUTOQUERY_INSERT) 
				or trigger_error("Inserimento attività di formazione dipendente fallito", E_USER_ERROR);
					
//			evid($res);
			if (DB::isError($res)) {
				trigger_error("Query fallita:<br> " . $res->getMessage(), E_USER_WARNING);
				$this->_erroriInaspettati = true;
				return false;
			}
		} 
		
		$this->_status = "datiElaborati";
	}
	
    /**
     * @author Daniele Primon
     * 
     * Elabora eventuali dati caricati da db per accomodare eventuali esigenze (es.: visualizzazione)
     */
    function _accomodaDatiCaricati() {
		
		foreach ( $this->storico as $chiave => $riga ) {
			$this->storico[$chiave]['for_data'] = base::adjustMySqlDate( $riga['for_data'] );
		}
		
    	$this->_datiModulo['for_data'] = base::adjustMySqlDate( $this->_datiModulo['for_data'] );
	
		for ( $i = 1; $i <= 4; $i++ )
		{
			if ( $this->_datiModulo["qualifica{$i}Data"] != '0000-00-00' )
			{
				$this->_datiModulo["qualifica{$i}Data"] = base::adjustMySqlDate( $this->_datiModulo["qualifica{$i}Data"] );
			}
			else
			{
				$this->_datiModulo["qualifica{$i}Data"] = "";
			}
				
		}
	
		
		$this->_stripSlashes();

    }
	
    /**
     * @author esa
     * 
     * Elabora eventuali dati caricati da db per accomodare eventuali esigenze (es.: visualizzazione)
     */
    function _accomodaDatiDaSalvare( &$rec ) {
    	
    	$rec['for_data'] = base::adjustMySqlDate( $rec['for_data'] );

		for ( $i=1; $i < 5; $i++ )
		{
			//echo "--" . $rec["qualifica{$i}"] .  "--" ;
	    	if ( $rec["qualifica{$i}"] == "" )
	    		$rec["qualifica{$i}Data"] = "";
	    	else
   		    	$rec["qualifica{$i}Data"] = base::adjustMySqlDate( $rec["qualifica{$i}Data"] );
	    	
	    }

/*
*     	$rec['nascitaData'] = preg_replace("/(\d+).(\d+).(\d+)/", "\\3-\\2-\\1", $rec['nascitaData']);
    	
*/
//		evid( $rec );
		$rec = $this->_stripSlashes( $rec );    	
    }
	
  
  	function _stampaModulo() {
  		require("sch_indiv.php");
  		
  		stampa_scheda($_POST);
  	}
    
}
?>