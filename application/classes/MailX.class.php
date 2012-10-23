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
        $this->SetFrom($this->XEmail, $this->nom.' '.$this->prenom);
        
    }

    public function demandeAnnulee()
    {
        $this->AltBody = $this->subsitute($this->mails[self::Section_X][self::Action_Canceled.".txt"],array());

        $this->Body = $this->subsitute($this->mails[self::Section_X][self::Action_Canceled.".html"], array());
        
        $this->Subject = $this->subsitute($this->mails[self::Section_X][self::Action_Canceled.".objet"],array());

    }
    
    public function nouvelleDemande($sLinkCancel, $sLinkConfirm, $sAdmMail)
    {
        $this->AltBody = $this->subsitute($this->mails[self::Section_X][self::Action_NvDemande.".txt"],array('HOST' => $_SERVER['HTTP_HOST']));

        $this->Body = $this->subsitute($this->mails[self::Section_X][self::Action_NvDemande.".html"], array('HOST' => $_SERVER['HTTP_HOST']));
        
        $this->Subject = $this->subsitute($this->mails[self::Section_X][self::Action_NvDemande.".objet"],array());

    }

}