<?php
/**
 * Class pour l'envoi de mail techniques
 * @author francois.espinet
 * @version 1.0
 *
 */

class MailAdminTech extends Mail {

    const Admin_Level_Warning = 'warning';
    const Admin_Level_Error = 'error';
    const Admin_Level_Notice = 'notice';


    public function psend() {

        $this->AddAddress($this->adminMail);
        parent::psend();
    }

    protected function _substitute($sAction='', $sType, $aRemplacement = array()) {
    	return parent::substitute(self::Pers_Admin_Tech, $sAction, $sType, $aRemplacement);
    }

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
}