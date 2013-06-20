<?php

/**
 * Classe pour l'envoi de mail administrateur fonctionnelle.
 * Elle sert à prévenir les administrateurs d'un problème de fonctionnement métier de l'application.
 *
 * @author francois.espinet
 * @version 1.0
 * @package Mail
 */
class Mail_AdminFonc extends Mail {

    const Aucun_X = 'nox';

    /**
     * Nom.
     *
     * @var string
     */
    public $nom;

    /**
     * Prenom.
     *
     * @var string
     */
    public $prenom;

    protected function psend()
    {
    	try {
    	    $this->AddAddress($this->adminFoncMail);
    		parent::psend();
    	} catch (Exception_Mail $e) {
    		throw new Exception_Mail("Impossible d'envoyer un mail à l'administateur fonctionnel.", Exception_Mail::Send_Echec_Admin, $e);
    	}
    }

    public function aucunX($admEmail = '') {
        $this->AltBody = $this->_substitute(self::Aucun_X, self::CONTENT_TYPE_TXT,array('EMAIL' => $admEmail));

        $this->Body = $this->_substitute(self::Aucun_X, self::CONTENT_TYPE_HTML,array('EMAIL' => $admEmail));

        $this->Subject = $this->_substitute(self::Aucun_X, self::CONTENT_TYPE_OBJET);

        $this->psend();
    }

    protected function _substitute($sAction = '', $sType, $aRemplacement = array())
    {
    	return parent::substitute(self::Pers_Admin_Fonc, $sAction, $sType, $aRemplacement);
    }
}