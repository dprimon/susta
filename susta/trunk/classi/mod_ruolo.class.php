<?php

require_once("include/mytools.inc.php");

require_once("include/moduloDati.class.php");

class mod_ruolo extends moduloDati {
	
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
    var $_campi = array( 'Ruolo', 'Reparto', 'Note', 'Data' );
    
    var $_campiChiave = array( 'id' );


	var $_qualifiche = array();

    function mod_ruolo( &$baseObj ) {


        // inizializzo collegamenti tra questo oggetto e oggeto baseObj);

/*        $this->_nomeAreaSessione = 'mod_ruolo';

		$this->_base =& $baseObj;
		
		is_array($this->_base->session['mod_ruolo']) or $this->_base->session['mod_ruolo'] = array();
		
		// mi appoggio alla sessione di $baseObj 
		$this->_session =& $this->_base->session['mod_ruolo'];
        
		$this->_datiModulo =& $this->_session['_datiModulo'];
		
		is_array( $this->_datiModulo['qualifiche'] ) and
*/			$this->_qualifiche =& $this->_datiModulo['qualifiche'];
		
		$this->moduloDati($baseObj);	// costruttore di default da chiamare

//		if ( $this->_status == "raccoltaDati" ) {
//			// imposto i campi, se neccessario carica da db
//			$this->_caricaCampi();
//		}
//
		 
//		$this->_stampaModulo();
		
    }
    
    function _mod_ruolo () {
		$this->_moduloDati();
	}
    function _controlloDati() {
        /*  if (!$_POST['nome'] || !$_POST['cognome'] || !$_POST['residenzaProvincia'] ||
              !$_POST['residenzaComune'] || !$_POST['residenzaCap'] || 
              !$_POST['residenzaProvincia']  ) {
            $this->_messaggiErrore[] = "Alcuni campi neccessari non sono stati compilati.";
            $this->_status          = "datiInconsistenti";
        } */

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
/*		evid($_POST);
		evid($rec); 
*/		
		$rec['Data'] or $rec['Data'] = date("Y-m-d");
		$rec['ID_ana'] = $this->_session['campi']['id'];

		if ($this->_salva($rec)) {
			$this->_status = "datiElaborati";	
		}
    }
    
	function _caricaCampi() {
		
		$this->_session['campi']['id'] = $_REQUEST['id'];

		// se non ho un id imposto valori di default

		
		if ( !$this->_session['campi']['id'] ) {
			

		}
		else
		{
		
			// arrivato fin qui, leggo i campi dal database 
			$campi = array();
			
/*			$statement = "SELECT nome, cognome, qualifica1, qualifica2, qualifica3, qualifica4, `d-storico`.* "
					   . "FROM `d-anagrafica` "
			           . "LEFT JOIN `d-storico` ON `d-anagrafica`.id = ID_ana "
					   . "WHERE `d-anagrafica`.id = {$this->_session['campi']['id']} " 
					   . "GROUP BY `d-anagrafica`.id "
			           . "ORDER BY `d-storico`.data DESC "
			           . "";
			           
	        $this->_base->_dbQuery("getRow", $statement, $campi);
	
	        DB::isError($campi) and trigger_error("Errore query", E_USER_WARNING);
*/	        

			$conn = $this->_base->mysql_connect_db();
	
			$statement = <<<statement
	
					DROP TABLE IF EXISTS `t_stor` ;\n
statement;
	
	       $result = mysql_query( $statement, $conn );
			if (!$result) {
			    echo 'Could not run query: ' . mysql_error();
			    exit;
			}
				
			$statement = <<<statement
					CREATE TEMPORARY TABLE `t_stor` SELECT * 
					FROM `d-storico` AS dstor
					WHERE NOT isnull( `data` ) AND ID_ana={$this->_session['campi']['id']}
					ORDER BY `data` DESC , `id_ana` DESC ;\n
statement;
	       $result = mysql_query( $statement, $conn );
			if (!$result) {
			    echo 'Could not run query: ' . mysql_error();
			    exit;
			}
	
			$statement = <<<statement
					SELECT dana.*,t_stor.Ruolo, t_stor.Reparto,t_stor.`data`, t_stor.Note 
					FROM `d-anagrafica` AS dana
					LEFT JOIN t_stor ON dana.id = t_stor.id_ana
					WHERE ID_ana={$this->_session['campi']['id']}
					GROUP BY id_ana
					ORDER BY cognome, nome
					           
statement;
	       $result = mysql_query( $statement, $conn );
			if (!$result) {
			    echo 'Could not run query: ' . mysql_error();
			    exit;
			}
	
			$campi = mysql_fetch_assoc($result);

			// carico la qualifiche
			$this->_qualifiche = array();
			for ($i = 1; $i <= 5; $i++) 
				$campi["qualifica$i"] and $this->_qualifiche[] = $campi["qualifica$i"];
			$this->_qualifiche = $this->_stripSlashes( $this->_qualifiche );			
	
	        $this->_datiModulo = array_merge( $this->_datiModulo, $campi );
		}        

		// Di default data attuale
		$this->_datiModulo['Data'] = strftime( "%Y-%m-%d" );

		// sistema i dati per la presentazione
		$this->_accomodaDatiCaricati();

        // questo approccio non è molto corretto ma sufficiente in questo caso
        $_POST = array_merge($_POST, $this->_datiModulo);
		
	}
	
    /*
     * Salva i dati del modulo nel db
     */
    function _salva( $rec ) {
    	
		$this->_base->_dbConnect();
		$dbh =& $this->_base->_db;

		$this->_accomodaDatiDaSalvare($rec);
//		evid( $rec );
	
   		$rec['id'] = $dbh->nextId("d-storico");
		$res = $dbh->autoExecute("`d-storico`", $rec, DB_AUTOQUERY_INSERT) 
			or trigger_error("Inserimento anagrafica dipendente fallito", E_USER_ERROR);

/*		evid($rec);
		evid($res);  /**/
		if (DB::isError($res)) {
			trigger_error("Query fallita:<br> " . $res->getMessage(), E_USER_WARNING);
			$this->_erroriInaspettati = true;
			return false;
		} 
		parent::_salva();
	}
	
    /**
     * @author esa
     * 
     * Elabora eventuali dati caricati da db per accomodare eventuali esigenze (es.: visualizzazione)
     */
    function _accomodaDatiCaricati() {
    	
		$this->_datiModulo['Data'] = base::adjustMysqlDate( $this->_datiModulo['Data'] ); 
		$this->_stripSlashes();
/*		evid ($_POST);	
		evid ($this->_datiModulo);/**/	
    }
	
    /**
     * @author esa
     * 
     * Elabora eventuali dati caricati da db per accomodare eventuali esigenze (es.: visualizzazione)
     */
    function _accomodaDatiDaSalvare( &$rec ) {
    	
		$rec['Data'] = base::adjustMysqlDate( $rec['Data'] ); 
//		$this->_stripSlashes();    	
    }
	
  
  	function _stampaModulo() {
  		
  		require("sch_indiv.php");
  		
  		stampa_scheda($_POST);
  	}
   
	function qualificheAcquisite() {
		return $this->_qualifiche;		
		
	
	}
}
?>
