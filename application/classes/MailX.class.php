<?php

/**
 * Class pour l'envoi de mail aux élèves polytechniciens
 * @author francois.espinet
 *
 */
class MailX extends Mail {
    
    const ACTION_CANCELED = 'anndemande';
    const ACTION_NVDEMANDE = "nvdemande";
    
    public $nom;
    public $prenom;
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
        $this->AltBody = $this->subsitute($this->mails[self::SECTION_X][self::ACTION_CANCELED.".txt"],array());

        $this->Body = $this->subsitute($this->mails[self::SECTION_X][self::ACTION_CANCELED.".html"], array());
        
        $this->Subject = $this->subsitute($this->mails[self::SECTION_X][self::ACTION_CANCELED.".objet"],array());
    }
    
    public function nouvelleDemande($sLinkCancel, $sLinkConfirm, $sAdmMail)
    {
        $this->AltBody = $this->subsitute($this->mails[self::SECTION_X][self::ACTION_NVDEMANDE.".txt"],array('HOST' => $_SERVER['HTTP_HOST']));

        $this->Body = $this->subsitute($this->mails[self::SECTION_X][self::ACTION_NVDEMANDE.".html"], array('HOST' => $_SERVER['HTTP_HOST']));
        
        $this->Subject = $this->subsitute($this->mails[self::SECTION_X][self::ACTION_NVDEMANDE.".objet"],array());
    }
}