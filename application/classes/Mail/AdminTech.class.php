<?php
/**
 * Class pour l'envoi de mail aux administrateurs techniques
 * @author francois.espinet
 * @version 1.0
 *
 */
class Mail_AdminTech extends Mail {

    const Admin_Level_Warning = 'warning';
    const Admin_Level_Error = 'error';
    const Admin_Level_Notice = 'notice';
    const Admin_level_Exception = 'exception';

    public function psend() {
        $this->AddAddress($this->adminMail);
        parent::psend();
    }

    protected function _substitute($sAction='', $sType, $aRemplacement = array()) {
        return parent::substitute(self::Pers_Admin_Tech, $sAction, $sType, $aRemplacement);
    }

    /**
     * Notifie l'administrateur technique (celui qui gère le code) d'une erreur fatale.
     * Cette fonction adjoint les logs au mail
     * @author francois.espinet
     * @param string $sMessage     le message d'erreur à communiquer à l'administrateur
     */
    public function fatalError($sMessage = 'inconnu') {
        $this->AltBody = $this->_substitute(self::Admin_Level_Error, self::CONTENT_TYPE_TXT, array('MESSAGE' => $sMessage,
                                                                                              'HOST' => $_SERVER['HTTP_HOST'],
                                                                                              'APP_LOG' => file_get_contents(LOGS_PATH.'/application.log'),
                                                                                              'PHP_LOG' => file_get_contents(LOGS_PATH.'/php.log')));

        $this->Body = $this->_substitute(self::Admin_Level_Error,self::CONTENT_TYPE_HTML, array('MESSAGE' => $sMessage,
                                                                                              'HOST' => $_SERVER['HTTP_HOST'],
                                                                                              'APP_LOG' => file_get_contents(LOGS_PATH.'/application.log'),
                                                                                              'PHP_LOG' => file_get_contents(LOGS_PATH.'/php.log')));
        $this->Subject = $this->_substitute(self::Admin_Level_Error, self::CONTENT_TYPE_OBJET);

        $this->psend();
    }

    /**
     * Informe l'administrateur technique qu'une erreur grave a été affichée à l'utilisateur (sur la page errors.php)
     * Le texte est l'exception elle-même, avec sa trace.
     * @author francois.espinet
     * @param string $sException l'exception qui est remontée
     */
    public function exception($sException) {
        $this->AltBody = $this->_substitute(self::Admin_Level_Exception, self::CONTENT_TYPE_TXT, array('EXCEPTION' => $sException));

        $this->Body = $this->_substitute(self::Admin_Level_Exception,self::CONTENT_TYPE_HTML, array('EXCEPTION' => $sException));

        $this->Subject = $this->_substitute(self::Admin_Level_Exception, self::CONTENT_TYPE_OBJET);

        $this->psend();
    }

    protected function psend() {
        try {
            parent::psend();
        } catch (Exception_Mail $e) {
            throw new Exception_Mail("Impossible d'envoyer un mail à l'administateur technique.", Exception_Mail::Send_Echec_Admin, $e);
        }
    }
}