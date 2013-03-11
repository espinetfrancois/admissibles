<?php

/**
 * Class pour l'envoi de mail aux élèves polytechniciens
 * @author francois.espinet
 * @version 1.0
 *
 */
class MailX extends Mail
{

    const Action_Canceled = 'anndemande';
    const Action_NvDemande = "nvdemande";

    /**
     * Nom
     * @var string
     */
    public $nom;

    /**
     * Prenom
     * @var string
     */
    public $prenom;

    /**
     * Adresse email de l'X
     * @var string
     */
    public $XEmail;

    /**
     *
     * @author francois.espinet
     * @param string $nom le nom de l'élève
     * @param string $prenom le prénom de l'élève
     * @param string $XEmail l'email de l'élève
     */
    public function __construct($nom, $prenom, $XEmail)
    {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->XEmail = $XEmail;
        parent::__construct();
        $this->AddAddress($this->XEmail, $this->nom.' '.$this->prenom);

    }

    public function demandeAnnulee()
    {
        $this->AltBody = $this->_substitute(self::Action_Canceled, self::CONTENT_TYPE_TXT);

        $this->Body = $this->_substitute(self::Action_Canceled,self::CONTENT_TYPE_HTML);

        $this->Subject = $this->_substitute(self::Action_Canceled, self::CONTENT_TYPE_OBJET);
        $this->psend();

    }

    public function nouvelleDemande()
    {
        $this->AltBody = $this->_substitute(self::Action_NvDemande, self::CONTENT_TYPE_TXT, array('HOST' => $_SERVER['HTTP_HOST']));

        $this->Body = $this->_substitute(self::Action_NvDemande, self::CONTENT_TYPE_HTML, array('HOST' => $_SERVER['HTTP_HOST']));

        $this->Subject = $this->_substitute(self::Action_NvDemande, self::CONTENT_TYPE_OBJET);
        $this->psend();
    }

    protected function _substitute($sAction='', $sType, $aRemplacement = array()) {
        return parent::substitute(self::Pers_X, $sAction, $sType, $aRemplacement);
    }

}