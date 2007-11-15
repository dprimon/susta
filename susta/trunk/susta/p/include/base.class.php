<?php
require_once("mytools.inc.php");
require_once("PEAR.php");

require_once("DB.php");

class base extends PEAR {

    /**
    * @var $_db DB
    */
    var $_db;

    /**
    * @var $config array contiene la configurazione del sito letta dal file config.inc.php
    */
    var $config = array();
    
    var $lingua;
    
    /**
    * var $session array Variabili di sessione
    */
    var $session;
    
    var $_dbPersistThisConnection;

    var $_dbPersistantConnection = false;
    
    function base($lingua = null) {
        $this->PEAR();

        $cfg = null;
        
        include("config.inc.php");

        $this->config = $cfg;

        if (!$lingua) {
//          evid(dirname($_SERVER['PHP_SELF']));
            $dirs = explode("/", dirname($_SERVER['PHP_SELF']));
            $this->lingua = $dirs[count($dirs)-1];
            strlen($this->lingua) != 2 and $this->lingua = 'it';
        } else {
            $this->lingua = $lingua;
        }
        session_name($this->config['sessionname']);
        session_id() or session_start();
        $this->session =& $_SESSION;

        setIncludePath($this->config['include_path']);


		// gestione dell'autenticazione

		if ( $_POST['utente'] and $_POST['password'] ) {
//			if ( $this->auth( $_POST['utente'],  $_POST['password'] ) ) {
			if ( $_POST['utente'] == $this->config['username'] && 
				$_POST['password'] == $this->config['password'] ) {
				$this->session['utente'] = $_POST['utente'];
				$this->session['password'] = $_POST['password'];
				$this->session['paginaPrec'] and $this->session['paginaPrec'] != basename( $_SERVER['SCRIPT_FILENAME'] ) and
					header("Location: " . $this->session['paginaPrec']);
			}
			
		}

		 
        if (@$_REQUEST['logout'] == 1 or !$this->utenteAutenticato() /* or !$this->controlloPermessi() */ ) {
            
            $this->session['password'] = "";
             
            $this->session['paginaPrec'] = basename( $_SERVER['SCRIPT_FILENAME'] );
            
			if ( basename( $_SERVER['SCRIPT_FILENAME'] ) != "index.php" ) {             
	            header("Location: index.php");
			}
        }   
    }
    
    function _base() {
        
    }
    
/*  function lingue() {
        $statement = "SELECT lingue.* "
                    ."FROM lingue,agenzie " 
                    ."WHERE FIND_IN_SET(lingue_id, agenzie_lingue) > 0 "
                    ."ORDER BY lingue_desc";    
        $this->_dbQuery("getAll", $statement, $elenco);
        foreach ($elenco as $riga) {
            $ret[$riga['lingue_id']] = $riga['lingue_desc'];
        }
        return $ret;
    }
*/


    function _dbQuery($method, $statement, &$result, $params = array()) {
        is_array($params) or trigger_error("Il quarto argomento dev'essere un'array.", E_USER_ERROR);
        in_array($method, array("getAll", "getRow", "getOne", "query", "limitQuery")) or
            trigger_error("metodo non riconosciuto", E_USER_ERROR);

        $res = null;
        $parameters = "";
        foreach ($params as $par) {
            $parameters .= "," . $par;  
        }
            
        $statement = preg_replace("/('|\")/", "\\\\\\1", $statement);
        
        $this->_db or $this->_dbConnect() or trigger_error("connessione al db fallita" . $this->_db->getMessage(), E_USER_ERROR);;
        
        eval("\$res = \$this->_db->$method('$statement' $parameters); ");
        
        $result = $res;
        if ( !$this->_dbPersistantConnection and !$this->_dbPersistThisConnection  )
        {
        	$this->_dbDisconnect();
        }
        else
        	$this->_dbPersistThisConnection = false;
        	
        if (DB::isError($res)) {
//          evid($statement);
            trigger_error("Errore nell'esecuzione della query " . $result->getMessage(), E_USER_WARNING);
            return false;
        }
        return true;
        
    }
    
    function _dbConnect() {
        $this->_db =& DB::connect($this->config['locale']['dsn']);
        if (DB::isError($this->_db)) {
            trigger_error("connessione al db fallita " . $this->_db->getMessage(), E_USER_WARNING);
            //evid($this->config);
            return false;
        }
        $this->_db->setOption( 'persistent', $this->_dbPersistantConnection );
        $this->_db->setFetchMode( DB_FETCHMODE_ASSOC );
        return true;
    }

    function _dbDisconnect() {
        $this->_db and $this->_db->disconnect();
        $this->_db = null;
    }
    
    function mysql_connect_db() {
        $connessione = mysql_connect($this->config['dbserver'], $this->config['dbuser'], $this->config['dbpass']) or trigger_error("Errore di connessione", E_USER_ERROR); 
        if ( $connessione )
        {
        	mysql_select_db($this->config['dbname']) or trigger_error( "Impossibile selezionare il db specificato.", E_USER_ERROR  );
        }
        else
        {
        	trigger_error( "Connessione al db fallita. ", E_USER_ERROR );
        }   
        return $connessione;
    }   
    
    function leggiCSV($filename) {
        $handle = fopen($filename, "r");
        while ($data = fgetcsv($handle, 1000, ",")) {
            $dati[] = $data;
        }
        fclose($handle);            
//        evid($dati);        
        evid($dati);        
    }

	function adjustMySqlDate( $strData )
	{
		return preg_replace( "/(\d+).(\d+).(\d+)/", "\\3/\\2/\\1", $strData );
	}

	function utenteAutenticato() {
		return $this->session['password'] ? true : false;
		
	}

    /**
     *
     * @access public
     * @return mixed    true o false se utente riconosciuto oppure no. DB::Error in caso di altri errori
     **/
    function auth($utente, $password) {
    	$aut_utenti = array();
        $statement = "SELECT * FROM `d-utenti` " .
                     "WHERE ute_utente='$utente' AND ute_password='$password'" ;
        if ($this->_dbQuery("getRow", $statement, $aut_utenti)) {
            return $aut_utenti;
        } else {
            trigger_error("Errore lettura utenti ". $aut_utenti ->getMessage(), E_USER_WARNING);        
            return $aut_utenti;
        }
    }

	function controlloPermessi() {
		return true;
	}
}



?>