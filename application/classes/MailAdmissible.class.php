<?php
/**
 * Class pour l'envoi de mail aux admissibles
 * @author francois.espinet
 * @version 1.0
 *
 */

class MailAdmissible extends Mail {

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
        $this->SetFrom($this->email, $this->nom.' '.$this->prenom);

    }

    /**
     * Envoi de l'email d'annulation d'une demande
     * @access public
     * @param string $sEleveX
     * @return void
     */
    public function demandeAnnulee($sEleveX)
    {
        $this->AltBody = $this->subsitute(self::Section_Admissible,self::Action_Cancel.'.txt',
                                         array('HOST'         => $_SERVER['HTTP_HOST'],
                                              'ELEVE_X'      => $sEleveX));
        
        $this->Body = $this->subsitute(self::Section_Admissible,self::Action_Cancel.'.html',
                                        array('HOST'         => $_SERVER['HTTP_HOST'],
                                              'ELEVE_X'      => $sEleveX));
        
        $this->Subject = $this->subsitute(self::Section_Admissible,self::Action_Cancel.'.objet',
                                        array('NOM'    => $this->nom,
                                              'PRENOM' => $this->prenom));
        $this->send();
                         
    }

    /**
     * Envoi de l'email de demande d'hébergement
     * @access public
     * @param string $sEleveX
     * @param string $sLinkCancel
     * @param string $sLinkConfirm
     * @return void
     */
    public function demandeEnvoyee($sEleveX, $sLinkCancel, $sLinkConfirm)
    {
        $this->AltBody = $this->subsitute(self::Section_Admissible,self::Action_NvDemande.'.txt', 
                                        array('LINK_CONFIRM' => $sLinkConfirm,
                                              'LINK_CANCEL'  => $sLinkCancel,
                                              'ELEVE_X'      => $sEleveX));

        $this->Body = $this->subsitute(self::Section_Admissible,self::Action_NvDemande.'.html', 
                                        array('LINK_CONFIRM' => $sLinkConfirm,
                                              'LINK_CANCEL'  => $sLinkCancel,
                                              'ELEVE_X'      => $sEleveX));

        $this->Subject = $this->subsitute(self::Section_Admissible,self::Action_NvDemande.'.objet',
                                        array('NOM'          => $this->nom,
                                              'PRENOM'       => $this->prenom));
        $this->send();

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
        $this->AltBody = $this->subsitute(self::Section_Admissible,self::Action_Accepted.'.txt',
                    array('ELEVE_X'      => $sEleveX,
                          'X_MAIL'       => $sXmail));
        
        $this->Body = $this->subsitute(self::Section_Admissible,self::Action_Accepted.'.html',
                array('X_MAIL'         => $sXmail,
                        'ELEVE_X'      => $sEleveX));
        
        $this->Subject = $this->subsitute(self::Section_Admissible,self::Action_Accepted.'.objet',
                array('NOM'          => $this->nom,
                      'PRENOM'       => $this->prenom));
        $this->send();

    }

}