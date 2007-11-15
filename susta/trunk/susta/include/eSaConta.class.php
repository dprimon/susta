<?php

class eSaConta {

	var $_filename;
	
	/* @var int timestamp data creazione del contatore*/
	
	var $_since = null;
	
	/* */
	var $_num = null;
	
	function eSaConta($file = "counter") {
		$this->_filename = $file;
		if (!strpos(@$_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])) {
			$this->_incCounter();
		}
	}
	
	function stampaQuanti() {
		$this->_num or $this->_readCounter();
		echo $this->_num;
	}
		
	function stampaDaQuando($formato = "meseanno") {
		$this->_since or $this->_readCounter();
		switch ($formato) {
			case "meseanno":
				require_once("mytools.inc.php");
				echo strtolower(nomeMese($this->_since)) . " " . strftime("%Y", $this->_since);
				break; 
			default:
				echo strftime("%d-%m-%Y", $this->_since);
		}
		return true;
	}

	function _incCounter() {
		$this->_since or $this->_readCounter();
		$this->_num++;
		$this->_num++;	// 2x temporaneamente
		$this->_writeCounter();
	}
	
	function _writeCounter() {
		$fp = fopen($this->_filename, "w");
		if (!$fp) return false;
		fwrite($fp, "{$this->_num}\n" . strftime("%Y-%m-%d", $this->_since));
		//echo "{$this->_num}\n{$this->_since}";
		fclose($fp);
		//echo "File scritto";
	}
	
	function _readCounter() {
		if (!file_exists($this->_filename)) {
			$this->_since = time();
			$this->_num = 0;
			$this->_writeCounter();
		} else {
			$fp = fopen($this->_filename, "r");
			// la mappatura del file è la seguente!
			!feof($fp) or die("merdazza");
			$dati = fread($fp, filesize($this->_filename));
			list($this->_num, $strSince) = explode("\n", $dati);
			$this->_since = strtotime($strSince);
			//echo(" $strSince "); 
			fclose($fp);
		}
	}
	
}

?>