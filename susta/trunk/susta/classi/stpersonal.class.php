<?php
require_once ("include/base.class.php");

/**
 * 
 * 
 * */
class stpersonal extends base
{

	function stpersonal()
	{
		$this->base();
		$this->_dbPersistantConnection = true;

		setlocale(LC_ALL, "it_IT");

	}

	function _stpersonal()
	{
		$this->_base();
	}

	function moduloDati($nome_classe)
	{

		require_once ("$nome_classe.class.php");

		$modulo = & new $nome_classe ($this);

		return $modulo;

	}

	/**
	 * Interroga la tabella anagrafica e storico per resituire dati anagrafici e ultima assegnazione di ruolo e reparto presenti  
	 * 
	 * @return array Elenco personale con informazioni complete
	 */
	function elencoPersonale()
	{

		$elenco = array ();

		/*	non corretto		
		 		$statement = "SELECT `d-anagrafica`.*, `d-storico`.Ruolo, `d-storico`.Reparto, "
		                   . "`d-storico`.Data AS dst_data, `d-storico`.Note AS dst_note  "
				           . "FROM `d-anagrafica` LEFT JOIN `d-storico` ON `d-storico`.ID_ana=`d-anagrafica`.id  "
				           . "WHERE assunzioneData <> ''  "
				           . "GROUP BY `d-anagrafica`.id DESC "
				           . "ORDER BY cognome, nome, dst_data DESC ";
		*/

		$conn = $this->mysql_connect_db();

		$statement =<<<statement

				DROP TABLE IF EXISTS `t_stor` ;\n
statement;

		$result = mysql_query($statement, $conn);
		if (!$result)
		{
			echo 'Could not run query: ' . mysql_error();
			exit;
		}

		$statement =<<<statement
				CREATE TEMPORARY TABLE `t_stor` SELECT * 
				FROM `d-storico` AS dstor
				WHERE NOT isnull( `data` ) 
				ORDER BY `data` DESC , `id_ana` DESC ;\n
statement;
		$result = mysql_query($statement, $conn);
		if (!$result)
		{
			echo 'Could not run query: ' . mysql_error();
			exit;
		}

		$statement =<<<statement
				SELECT dana.*,t_stor.Ruolo, t_stor.Reparto,t_stor.`data`, t_stor.Note 
				FROM `d-anagrafica` AS dana
				LEFT JOIN t_stor ON dana.id = t_stor.id_ana
                WHERE assunzioneData <> ''
				GROUP BY dana.id
				ORDER BY cognome, nome
statement;

		$result = mysql_query($statement, $conn);
		if (!$result)
		{
			echo 'Could not run query: ' . mysql_error();
			exit;
		}

		//        evid($statement); 

		// $this->_dbQuery("getAll", $statement, $elenco);

		/* Use the result, assuming we're done with it afterwords */
		while ($row = mysql_fetch_assoc($result))
		{
			$elenco[] = $row;
		};

		//        evid($elenco); 

		//        DB::isError($elenco) and trigger_error("Errore query", E_USER_WARNING);

		return $elenco;

	}

	/**
	 *  @return array Restituisce elenco degli ex-dipendenti presenti in anagrafica
	 *  
	 **/
	function elencoPersonaleAssunto()
	{

		$elenco = array ();

		$statement = "SELECT * FROM `d-anagrafica` " . "WHERE assunzioneData='' " . "ORDER BY assunzioneData, cognome, nome";
		$this->_dbQuery("getAll", $statement, $elenco);
		//        evid($statement);
		DB :: isError($elenco) and trigger_error("Errore query", E_USER_WARNING);

		require_once ("include/moduloDati.class.php");

		return moduloDati :: _stripSlashes($elenco);

	}

	/**
	 *  Restituisce un'elenco stampabile del personale con relativi ruoli
	 * 
	 */
	function elencoRuoliPersonale()
	{

		require_once ("include/moduloDati.class.php");

		$elenco = array ();

		foreach ($this->elencoPersonale() as $rec)
		{

			$rec = moduloDati :: _stripSlashes($rec);

			if (!$rec['Ruolo'] and !$rec['Reparto'])
			{
				$rec['incarico'] = "Nessun ruolo o reparto assegnati";

			}
			elseif (!$rec['Ruolo'])
			{
				$rec['incarico'] = 'assegnato al reparto ' . $rec['Reparto'];

			}
			elseif (!$rec['Reparto'])
			{
				$rec['incarico'] = $rec['Ruolo'] . ' (non assegnato ad alcun reparto)';

			} else
			{
				$rec['incarico'] = $rec['Ruolo'] . ' al reparto ' . $rec['Reparto'];

			}

			$elenco[] = $rec;
		}

		return $elenco;
	}

	function datiDipendente($id)
	{

		$id or trigger_error("Id nullo", E_USER_ERROR);

		$record = array ();

		$statement = "SELECT *, YEAR( FROM_DAYS( TO_DAYS( NOW() ) - TO_DAYS( nascitaData ) ) ) AS eta FROM `d-anagrafica` " . "WHERE id=$id " . "";

		$this->_dbQuery("getRow", $statement, $record);

		//        evid($statement);
		DB :: isError($record) and trigger_error("Errore query", E_USER_WARNING);

		//		evid( $record );
		return $record;

	}

	/**
	 * Restituisce un'elenco di ruoli possibili dalla relativa tabella
	 * 
	 */
	function elencoRuoli()
	{

		$elenco = array ();

		$statement = "SELECT * FROM `s-ruoli` "
			//		           . "WHERE  "
	 . "ORDER BY Ruolo";

		$this->_dbQuery("getAll", $statement, $elenco);

		//        evid($statement);
		DB :: isError($elenco) and trigger_error("Errore query", E_USER_WARNING);

		//		evid($elenco);

		return $elenco;
	}

	/**
	 * Restituisce un'elenco di ruoli possibili dalla relativa tabella
	 * 
	 */
	function elencoReparti()
	{

		$elenco = array ();

		$statement = "SELECT * FROM `s-reparti` "
			//		           . "WHERE  "
	 . "ORDER BY Reparto";

		$this->_dbQuery("getAll", $statement, $elenco);

		//        evid($statement);
		DB :: isError($elenco) and trigger_error("Errore query", E_USER_WARNING);

		//		evid($elenco);

		return $elenco;
	}

	/**
	 * Restituisce un'elenco di ruoli possibili dalla relativa tabella
	 * 
	 */
	function elencoQualifiche()
	{

		$elenco = array ();

		$statement = "SELECT * FROM `s-qualifiche` "
			//		           . "WHERE  "
	 . "ORDER BY Qualifica ASC";

		$this->_dbQuery("getAll", $statement, $elenco);

		//        evid($statement);
		DB :: isError($elenco) and trigger_error("Errore query", E_USER_WARNING);

		//		evid($elenco);

		return $elenco;
	}

	function controlloPermessi()
	{
		$nomePagina = basename($_SERVER['SCRIPT_FILENAME']);
		if ($this->session['utente'] == 'Report')
		{

			return substr($nomePagina, 0, 4) != "mod";
		}

		return true;

	}

	/**
	 * Elenco personale ordinato per cognome e nome
	 */
	function datiReport1_a_3()
	{

		$id = $_REQUEST['id'];

		$malloppo = array ();

		$malloppo['anagrafica'] = array ();

		$statement = "SELECT cognome, nome, telFisso, telCell, nascitaData " . "FROM `d-anagrafica` " . "WHERE assunzioneData<>'' " . "ORDER BY cognome, nome";

		//        evid($statement); 

		$risultato = array ();

		$this->_dbQuery("getAll", $statement, $risultato);

		DB :: isError($risultato) and trigger_error("Errore query", E_USER_WARNING);

		foreach ($risultato as $campo => $rec)
		{
			$risultato[$campo]['nascitaData'] = $this->adjustMySqlDate($rec['nascitaData']);
		}

		$malloppo['anagrafica'] = $risultato;
		//        evid($elenco); 

		require_once ("include/moduloDati.class.php");

		return moduloDati :: _stripSlashes($malloppo);
	}

	/**
	 * Elenco personale ordinato per Ruolo (anche detto Incarico)
	 * 
	 */
	function datiReport4()
	{

		$id = $_REQUEST['id'];

		$malloppo = array ();

		$malloppo['anagrafica'] = array ();

		$statement = "CREATE TEMPORARY TABLE tmp_personaleRuoli " . "SELECT cognome, nome, telFisso, telCell, nascitaData, Ruolo, Reparto " . "FROM `d-anagrafica` AS a LEFT JOIN `d-storico` as b " . "ON a.id=b.ID_ana " . "WHERE assunzioneData<>'' " . "GROUP BY a.id " . "ORDER BY b.data, cognome, nome;\n";

		//        evid($statement); 

		$risultato = array ();

		$this->_dbQuery("query", $statement, $risultato);

		DB :: isError($risultato) and trigger_error("Errore query", E_USER_WARNING);

		$statement = "SELECT * " . "FROM tmp_personaleRuoli " . "ORDER BY Ruolo, cognome, nome;";

		//        evid($statement); 

		$this->_dbQuery("getAll", $statement, $risultato);

		DB :: isError($risultato) and trigger_error("Errore query", E_USER_WARNING);

		foreach ($risultato as $campo => $rec)
		{
			$risultato[$campo]['nascitaData'] = $this->adjustMySqlDate($rec['nascitaData']);
		}

		$malloppo['anagrafica'] = $risultato;
		//        evid($elenco); 

		require_once ("include/moduloDati.class.php");

		return moduloDati :: _stripSlashes($malloppo);
	}

	/**
	 * Elenco personale ordinato per reparto 
	 */
	function datiReport5()
	{

		$id = $_REQUEST['id'];

		$malloppo = array ();

		$malloppo['anagrafica'] = array ();

		$statement = "CREATE TEMPORARY TABLE tmp_personaleRuoli " . "SELECT cognome, nome, telFisso, telCell, nascitaData, Ruolo, Reparto " . "FROM `d-anagrafica` AS a LEFT JOIN `d-storico` as b " . "ON a.id=b.ID_ana " . "WHERE assunzioneData<>'' " . "GROUP BY a.id " . "ORDER BY b.data, cognome, nome;\n";

		//        evid($statement); 

		$risultato = array ();

		$this->_dbQuery("query", $statement, $risultato);

		DB :: isError($risultato) and trigger_error("Errore query", E_USER_WARNING);

		$statement = "SELECT * " . "FROM tmp_personaleRuoli " . "ORDER BY Reparto, cognome, nome;";

		//        evid($statement); 

		$this->_dbQuery("getAll", $statement, $risultato);

		DB :: isError($risultato) and trigger_error("Errore query", E_USER_WARNING);

		foreach ($risultato as $campo => $rec)
		{
			$risultato[$campo]['nascitaData'] = $this->adjustMySqlDate($rec['nascitaData']);
		}

		$malloppo['anagrafica'] = $risultato;
		//        evid($elenco); 

		require_once ("include/moduloDati.class.php");

		return moduloDati :: _stripSlashes($malloppo);
	}

	function datiSchedaIndividuale()
	{

		$id = $_REQUEST['id'];

		$malloppo = array ();

		$risultato = $this->datiDipendente($id);

		$risultato['nascitaData'] = $this->adjustMySqlDate($risultato['nascitaData']);

		if ($risultato['assunzioneData'] == '0000-00-00')
			$risultato['assunzioneData'] = '';
		else
			$risultato['assunzioneData'] = $this->adjustMySqlDate($risultato['assunzioneData']);

		$malloppo = $risultato;

		// carico la qualifiche
		//		$malloppo['qualifiche'] = array();

		for ($i = 1; $i <= 5; $i++)
		{
			if ($risultato["qualifica$i"])
			{
				$qualifica = array ();
				$qualifica['tipo'] = $risultato["qualifica$i"];
				$qualifica['data'] = '';
				if ($risultato["qualifica{$i}Data"] != '0000-00-00')
					$qualifica['data'] = $this->adjustMySqlDate($risultato["qualifica{$i}Data"]);
				$malloppo['qualifiche'][] = $qualifica;
			}
		}

		// non più neccessario dividere in righe
		// $malloppo['valutazioni'] = explode("\n", $risultato['valutazioni']);

		$statement = "SELECT * " . "FROM `d-storico`  " . "WHERE ID_ana = $id  " . "ORDER BY data DESC " . "LIMIT 0,6 ";

		$this->_dbQuery("getAll", $statement, $risultato);

		//        evid($statement); 
		//        evid($elenco); 

		DB :: isError($risultato) and trigger_error("Errore query", E_USER_WARNING);

		$malloppo['ruolo'] = @ $risultato[0]['Ruolo'];
		$malloppo['reparto'] = @ $risultato[0]['Reparto'];

		$malloppo['storico'] = array();
		
		$reccount = 0;
		foreach ($risultato as $rec)
		{
			$rec['Data'] = preg_replace("/(\d+).(\d+).(\d+)/", "\\3/\\2/\\1", $rec['Data']);

			if (!$rec['Ruolo'] and !$rec['Reparto'])
			{
				$rec['incarico'] = "Nessun ruolo o reparto assegnati";

			}
			elseif (!$rec['Ruolo'])
			{
				$rec['incarico'] = 'assegnato al reparto ' . $rec['Reparto'];

			}
			elseif (!$rec['Reparto'])
			{
				$rec['incarico'] = $rec['Ruolo'] . ' (non assegnato ad alcun reparto)';

			} else
			{
				$rec['incarico'] = $rec['Ruolo'] . ' al reparto ' . $rec['Reparto'];

			}

			$malloppo['storico'][] = $rec;
			
			if ( ++$reccount == 7 ) break;
			
		}
		
		$malloppo['storico'] == array() and $malloppo['storico'] = array( 'id' => '' );

/*[0] => Array
(
    [id] => 77
    [ID_ana] => 45
    [Ruolo] => Addetti alla levigatura del fondo
    [Reparto] => Linea levigatura fondo
    [Data] => 04/07/2005
    [Note] => 
    [incarico] => Addetti alla levigatura del fondo al reparto Linea levigatura fondo
)*/


		//		

/* Non è più neccessario
		// processo le note finali		
		$malloppo['noteFinali'] = explode("\n", $malloppo['noteFinali']);

		$noteFinali = array ();
		list (, $nota) = end($malloppo['noteFinali']);
		$noteFinali[] = $nota;
		list (, $nota) = prev($malloppo['noteFinali']);
		$noteFinali[] = $nota;
		list (, $nota) = prev($malloppo['noteFinali']);
		$noteFinali[] = $nota;

		$malloppo['noteFinali'] = $noteFinali;
*/
		$statement = "SELECT * " . "FROM `d-formazione`  " . "WHERE for_dipendente = $id  " . "ORDER BY for_data DESC ";

		$this->_dbQuery("getAll", $statement, $risultato);

		//        evid($statement); 
		//        evid($elenco); 

		DB :: isError($risultato) and trigger_error("Errore query", E_USER_WARNING);

		$malloppo['formazione'] = array();
		$reccount = 0;
		foreach ($risultato as $rec)
		{
			$rec['for_data'] = preg_replace("/(\d+).(\d+).(\d+)/", "\\3/\\2/\\1", $rec['for_data']);

			$malloppo['formazione'][] = $rec;
			if ( ++$reccount == 20 ) break;

		}
		
		$malloppo['formazione'] == array() and $malloppo['formazione'][] = array( 'for_id' => '' );
		
/*[0] => Array
        (
            [for_id] => 128
            [for_dipendente] => 76
            [for_data] => 27/01/1997
            [for_descrizione] => Legge 626-  Formazione ed informazione eseguita all'atto dell'assunzione
            [for_valutazione] => sufficiente
        )*/
        
	require_once ("include/moduloDati.class.php");

		return moduloDati::_stripSlashes($malloppo);
	}

	function StreamDocument( $docName )
	{
		$tmpDocToStream = '';
		// cerco il documento da servire
		foreach ( $this->session['requestedDocs'] as $iDoc => $theDoc )
		{
			if ( $docName == $theDoc['name'] )
			{
				$tmpDocToStream = $theDoc['tmpName'];
				break;
			}
		}

/*		evid( `pwd; ls -l tmp/` );
		evid( $docName . " " . $tmpDocToStream);
		evid( $this->session['generatedDocs'] ); /**/
	
		if ( ! file_exists( $tmpDocToStream ) )
		{
			// Modo grezzo per forzare un 404
			// TODO: Il modo più corretto è di riportare un'header http con il codice d'errore appropriato
			// i.e.: 500 non concesso
			// 404 non trovato...
			header( "Location: index.php" );
			die();
		}
		
		// Modo hackerone di ottenere il mimetype... all'occhio
		
		$docSize = filesize( $tmpDocToStream );

		header( "Content-type: " . $this->_GetMymetype( $tmpDocToStream ) );
		header( "Content-Length: $docSize" ); 
		
		
// 		evid( $this->session['generatedDocs'] );

		// flush del file verso il client
		
		// TODO: supportare il resume?? vedi header http 
	    $fp = @fopen( $tmpDocToStream, 'rb' ); // replace readfile()
	    $bytesSent = fpassthru( $fp );
	    fclose( $fp );
	    
		// se ho effettuato uno stream completo elimino il file temporaneo...
		if ( $docSize === $bytesSent )
		{
	    	@unlink( $tmpDocToStream );
			unset( $this->session['requestedDocs'][$iDoc] );
//			evid( "Documento - size: $docSize, streamed: $bytesSent, key: $iDoc");
		}	

	}

	// vedi clsTinyButStrongOOo::GetPathnameDoc();
	// Quick & dirty
	function _GetDocBaseName()
	{
		// remove tmp dir
		$this->_oooDoc->_RemoveTmpBasenameDir();

		// return path
		return $this->_oooDoc->_ooo_basename;

	}

	/**
	 * @param $params array Contiene nome del modello
	 * 
	 */
	function MakeDocument( $docName, &$params )
	{

		isset( $params['dstFileName'] ) or $params['dstFileName'] = $docName;

		include_once ('include/tbs_class.php');
		include_once ('include/tbsooo_class.php');

		$OOo = & $this->_oooDoc;

		// instantiate a TBS OOo class
		$OOo = new clsTinyButStrongOOo;

		// setting the object
		$OOo->SetZipBinary('zip');
		$OOo->SetUnzipBinary('unzip');
		$OOo->SetProcessDir('tmp/');
		$OOo->SetDataCharset('ISO 8859-1');

		// create a new openoffice document from the template with an unique id 
		$OOo->NewDocFromTpl('reports/modelli/' . $docName);

		// merge data with OOo file content.xml 
		$OOo->LoadXmlFromDoc('content.xml');

		// prelevo token da sostituire nel nome del file risultante
		preg_match_all( "/\{(\w*)\[(\w*)\]\}/", $params['dstFileName'], $tokens );
		
		// Passo i dati al motore del template
		foreach ($params['tplFeed'] as $tplCall )
		{
			$p0 = $tplCall['params'][0];
			$p1 = $tplCall['params'][1];
			$OOo->$tplCall['method']( $p0, $p1 );

			// già che ci sono, controllo se nel blocco corrente ci sono valori da 
			// inserire nel nome del file di destinazione
			while ( list( $key, $blkName ) = each( $tokens[1] ) )
			{
				if ( $blkName == $p0 )
				{
					$valsToInject[$tokens[0][$key]] = $p1[$tokens[2][$key]];
					unset( $tokens[1][$key] );
					reset( $tokens[1] );
				}
				
			}
		}
		// Assegno il nome del file definitivo
		is_array( $valsToInject ) and $params['dstFileName'] = strtr( $params['dstFileName'], $valsToInject );
		
		// "Rimpachetto" il documento odt generato
		$OOo->SaveXmlToDoc();

		// nome del doc temporaneo
		$tmpDoc = $OOo->GetPathnameDoc();

/*		echo "<pre>" . `ls -l tmp/` . "</pre>";
/**/		
		// Converto, se neccessario, nel formato richiesto 
		switch (strtolower($_REQUEST['type']))
		{
			case 'odt' :
				break;

			case 'pdf' :
				
				$tmpOdt = $tmpDoc;
				$a_pathinfo = pathinfo( $tmpDoc );
				$tmpDoc = preg_replace( "/{$a_pathinfo['extension']}$/", "pdf", $tmpDoc );

				$cmd = "sh makepdf.sh " . $this->config['ooodocdir'] . $tmpOdt . " 2>&1";
				$output_debug = `$cmd`;
				
/*				echo "ODT: $tmpOdt <pre>$cmd\n";
				evid( $output_debug );
				evid( `ls -l tmp/` );
				echo '</pre>'; /**/

				unlink( $tmpOdt );
				unset( $tmpOdt );



				break;

			default :
				break;
		}
		
		// Memorizzo nella sessione corrente i parametri per lo streaming 
		$docInfo = array(
			'name' => $params['dstFileName'] . "." . strtolower($_REQUEST['type']),
			'tmpName' => $tmpDoc,
			);
		
		is_array( $this->session['requestedDocs'] ) or $this->session['requestedDocs'] = array();

		// rimuovo eventuali precedenti versioni		
		while ( list( $key, $doc ) = each( $this->session['requestedDocs'] ) )
		{
			if ( $doc['name'] == $docInfo['name'] )
			{
				@unlink( $doc['tmpName'] );	// elimino file temporaneo
				unset( $this->session['requestedDocs'][$key] );
				reset( $this->session['requestedDocs'] );
			}
		}

		$this->session['requestedDocs'][] = $docInfo;


/*		evid( $this->session );
		echo "<pre>" . `ls -l tmp/` . "</pre>";
/**/
	}

	/**
	 * 
	 */
	function _GetMymetype( $file )
	{
		include_once ('include/tbs_class.php');
		include_once ('include/tbsooo_class.php');
		
		// modo poco "pulito"...
		$a_pathinfo = pathinfo( $file );
	    $this->_ooo_file_ext  = $a_pathinfo['extension'];
		$mimetype = clsTinyButStrongOOo::GetMimetypeDoc();
		
		$mimetype or $mimetype = mime_content_type( $file );
		
		return $mimetype;
		
	}

}
?>
