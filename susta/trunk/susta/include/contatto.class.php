<?php
require_once("PEAR.php");
require_once("common.inc.php");
/**
*   Classe per la gestione delle form dei contatti
*/

class contatto extends PEAR {

    /*
    * @var string Lo stato della gestione dei contatti: può essere "collectData", "dataSent", "badData", "sendError"
    */
    var $_status;
    
    var $_unexpectedErrors;
    
    /* @var array Contiene i messaggi di errore. Usare Get messages per stamparli
    */
    var $_errorMessages;
    
    /*
    * @var array Contiene i parametri per l'invio della mail quali recipients, subject, from, template della mail, etc...
    */
    var $_mailVars = array();
    var $_fields = array();

    function contatto() {
        $this->PEAR();
        $this->_errorMessages = array();
        if (isset($_POST['submit'])) {
            $this->_stripSlashes();
            if ($this->_checkData()) {
                $this->_sendMail();
            }   
        } else {
            $this->_status = "collectData";
        }
    }
    
    function _contatto() {
    }

    /** 
    * @desc Indica se visualizzare o meno la form per l'inserimento dei dati per il contatto 
    * @return boolean
    */
    function displayForm() {
        return $this->_status == "collectData" || $this->_status == "badData";
    }
    
    /** 
    * @desc Indica se la mail di contatto è stata inoltrata
    * @return boolean
    */
    function hasErrors() {
        return $this->_status == "badData" || $this->_unexpectedErrors;
    }
    
    /** 
    * @desc Indica se la mail di contatto è stata inoltrata
    * @return boolean
    */
    function mailSent() {
        return $this->_status == "dataSent";
    }
    
    /**
    * @desc Indica se ci sono stati errori inaspettati
    * @return boolean
    */
    function unexpectedErrors() {
        return $this->_unexpectedErrors;
    }

    /**
    * @desc Controlla la validità dei dati ricevuti in POST. Restituisce false in caso di errore e imposta lo stato a 'badData'.
    */
    function _checkData() {
        // passo htmlentities ai campi inseriti
        foreach ($_POST as $nome => $valore) {
            if (is_string($valore)) {
                $_POST[$nome] = htmlentities($valore);
            }
        }
        return $this->_status != 'badData';
    }
    
    
    /**
    * @desc Invia una mail con i dati ricevuti in POST. Restituisce false in caso di errore e imposta _unexpectedErrors;
    * @return bool
    */
    function _sendMail() {
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
    
    function loopPrintErrors() {
        return (is_array($this->_errorMessages) && count($this->_errorMessages));
    }
    /**
    * @desc restituisce l'ultimo messaggio di errore di compilazione e lo rimuove dalla coda.
    * @return mixed Restituisce il testo del messaggio oppure false se non ce ne sono.
    */
    function printError() {
        if (is_array($this->_errorMessages) && count($this->_errorMessages)) {
            echo array_pop($this->_errorMessages);
            return true;
        } else 
            return false;
    }

    function _stripSlashes() {
        foreach ($this->_fields as $campo) {
           $_POST[$campo] = StripSlashes($_POST[$campo]);
        }
    }
}


?>