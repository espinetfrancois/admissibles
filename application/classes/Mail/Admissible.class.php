<?php
/**
 * Class pour l'envoi de mail aux admissibles
 * @author francois.espinet
 * @version 1.0
 *
 */

class Mail_Admissible extends Mail {

    const Action_Cancel = 'anndemande';
    const Action_NvDemande = 'nvdemande';
    const Action_Accepted = 'acceptdemande';

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
     * Adresse email de l'admissible
     * @var string
     */
    public $email;

    /**
     * Constructeur
     * @access public
     * @param string $sNom
     * @param string $sPrenom
     * @param string $sEmail
     * @return void
     */
    public function __construct($sNom, $sPrenom, $sEmail)
    {
        $this->nom = $sNom;
        $this->prenom = $sPrenom;
        $this->email = $sEmail;
        parent::__construct();
        $this->AddAddress($this->email, $this->nom.' '.$this->prenom);

    }

    /**
     * Envoi de l'email d'annulation d'une demande
     * @access public
     * @param string $sEleveX
     * @return void
     */
    public function demandeAnnulee($sEleveX)
    {
        $this->AltBody = $this->_substitute(self::Action_Cancel,self::CONTENT_TYPE_TXT,
                                         array('HOST'         => $_SERVER['HTTP_HOST'],
                                              'ELEVE_X'      => $sEleveX));

        $this->Body = $this->_substitute(self::Action_Cancel,self::CONTENT_TYPE_HTML,
                                        array('HOST'         => $_SERVER['HTTP_HOST'],
                                              'ELEVE_X'      => $sEleveX));

        $this->Subject = $this->_substitute(self::Action_Cancel,self::CONTENT_TYPE_OBJET,
                                        array('NOM'    => $this->nom,
                                              'PRENOM' => $this->prenom));
        $this->psend();

    }

    /**
     * Envoi de l'email de demande d'h�bergement
     * @access public
     * @param string $sEleveX
     * @param string $sLinkCancel
     * @param string $sLinkConfirm
     * @return void
     */
    public function demandeEnvoyee($sEleveX, $sLinkCancel, $sLinkConfirm)
    {
        $this->AltBody = $this->_substitute(self::Action_NvDemande,self::CONTENT_TYPE_TXT,
                                        array('LINK_CONFIRM' => $sLinkConfirm,
                                              'LINK_CANCEL'  => $sLinkCancel,
                                              'ELEVE_X'      => $sEleveX));

        $this->Body = $this->_substitute(self::Action_NvDemande,self::CONTENT_TYPE_HTML,
                                        array('LINK_CONFIRM' => $sLinkConfirm,
                                              'LINK_CANCEL'  => $sLinkCancel,
                                              'ELEVE_X'      => $sEleveX));

        $this->Subject = $this->_substitute(self::Action_NvDemande,self::CONTENT_TYPE_OBJET,
                                        array('NOM'          => $this->nom,
                                              'PRENOM'       => $this->prenom));
        $this->psend();
    }

    /**
     * Envoi de l'email de confirmation de demande
     * @access public
     * @param string $sEleveX
     * @param string $sXmail
     * @return void
     */
    public function demandeConfirmee($sEleveX, $sXmail)
    {
        $this->AltBody = $this->_substitute(self::Action_Accepted,self::CONTENT_TYPE_TXT,
                    array('ELEVE_X'      => $sEleveX,
                          'X_MAIL'       => $sXmail));

        $this->Body = $this->_substitute(self::Action_Accepted,self::CONTENT_TYPE_HTML,
                array('X_MAIL'         => $sXmail,
                        'ELEVE_X'      => $sEleveX));

        $this->Subject = $this->_substitute(self::Action_Accepted,self::CONTENT_TYPE_OBJET,
                array('NOM'          => $this->nom,
                      'PRENOM'       => $this->prenom));
        $this->psend();

    }

    protected function _substitute($sAction='', $sType, $aRemplacement = array()) {
        return parent::substitute(self::Pers_Admissible, $sAction, $sType, $aRemplacement);
    }
}