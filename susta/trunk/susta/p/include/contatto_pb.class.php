<?php 
require_once("contatto.class.php");

/*
NOTE
- il tag <input type="submit"> deve avere name="submit"
- i dati vanno inviati in post

*/
class contatto_pb extends contatto {
    
    function contatto_pb() {
        $this->_mailVars['recipients'] = array("info@professionalbike.it"/*, "signup@cheapweb.it"*/);
        $this->_mailVars['from']       = $_POST['nome'] . " <contatto@professionalbike.it>";
        $this->_mailVars['to']         = $this->_mailVars['recipients'][0];
        $this->_mailVars['ccn']        = "signup@cheapweb.it";
        $this->_mailVars['template']   = "mail_contatto.tpl.php";
        $this->_mailVars['subject']    = $_POST['oggetto'] . " - Messaggio da www.professionalbike.it";
        $this->_fields                 = array( 'nome','recapito','recapito2','oggetto','testo','privacy' );
        $this->contatto();
    }
    
    function _checkData() {
        if (!$_POST['nome'] || !$_POST['recapito'] || !$_POST['oggetto'] || !$_POST['testo']) {
            $this->_errorMessages[] = "Vi preghiamo di compilare tutti i dati.";
            $this->_status          = "badData";
        }
        if (!$_POST['privacy']) {
            $this->_errorMessages[] = "&Eacute; neccessario visionare e accettare l'informativa per poter inviare il messaggio.";
            $this->_status          = "badData";
        } 
        return parent::_checkData();
    }
    
    function _sendMail() {
        $this->_mailVars['replyTo'] = $_POST['email'];
        //echo "Invio della mail<br>";
        return parent::_sendMail();
    }

}
?>