<?php

/**
 * Class pour l'envoi de mail aux élèves polytechniciens
 * @author francois.espinet
 * @version 1.0
 *
 */
class Mail_X extends Mail
{
    const Action_Canceled = 'anndemande';
    const Action_NvDemande = "nvdemande";

    /**
     * Nom de l'X
     * @var string
     */
    public $nom;

    /**
     * Prenom de l'X
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

    /**
     * Génération et envoi d'un mail dans la cas ou l'admissible a annulé sa demande.
     * Préviens l'X qu'il ne doit plus s'attendre à recevoir quelqu'un pour le moment
     * Ce mail est purement informatif
     * @author francois.espinet
     */
    public function demandeAnnulee()
    {
        $this->AltBody = $this->_substitute(self::Action_Canceled, self::CONTENT_TYPE_TXT);

        $this->Body = $this->_substitute(self::Action_Canceled,self::CONTENT_TYPE_HTML);

        $this->Subject = $this->_substitute(self::Action_Canceled, self::CONTENT_TYPE_OBJET);
        $this->psend();

    }

    /**
     * Génération et envoi d'un mail lors d'une nouvelle demande d'un admissible
     * Préviens l'X qu'un admissible a demandé à loger chez lui.
     * Par ce mail, l'X peut confirmer la demande de l'admissible.
     * @author francois.espinet
     */
    public function nouvelleDemande()
    {
        $this->AltBody = $this->_substitute(self::Action_NvDemande, self::CONTENT_TYPE_TXT, array('HOST' => $_SERVER['HTTP_HOST']));

        $this->Body = $this->_substitute(self::Action_NvDemande, self::CONTENT_TYPE_HTML, array('HOST' => $_SERVER['HTTP_HOST']));

        $this->Subject = $this->_substitute(self::Action_NvDemande, self::CONTENT_TYPE_OBJET);
        $this->psend();
    }

    /**
     * fonction outil
     * @see Mail::substitute()
     * @author francois.espinet
     * @param string $sAction
     * @param string $sType
     * @param array $aRemplacement
     * @return string le texte substitué
     */
    protected function _substitute($sAction='', $sType, $aRemplacement = array()) {
        return parent::substitute(self::Pers_X, $sAction, $sType, $aRemplacement);
    }

    /**
     * (non-PHPdoc)
     * Permet la gestion spécifique des exceptions
     * @author francois.espinet
     * @see Mail::psend()
     */
    protected function psend() {
        try {
            parent::psend();
        } catch (Exception_Mail $e) {
            /**
             * Dans ce cas, le mail n'a pas pu être envoyé à l'X, c'est grave, mais mois que dans le cas où l'admissible ne reçoit pas de mail
             * En effet, l'X a toujours accès à sa plateforme en ligne sur laquelle se trouvent toutes ses demandes
             */
            throw new Exception_Mail("Le mail n'a pas pu être envoyé à l'élève polytechnicien : " . $this->XEmail, Exception_Mail::Send_Echec_X, $e);
            //TODO : set message ici?
        }
    }

}