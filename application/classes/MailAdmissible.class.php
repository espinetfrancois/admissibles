<?php

class MailAdmissible extends Mail {

    const ACTION_CANCEL = 'anndemande';
    const ACTION_NVDEMANDE = 'nvdemande';
    const ACTION_ACCEPTED = 'acceptdemande';
    
    public $nom;
    public $prenom;
    public $email;

    public function __construct($sNom, $sPrenom, $sEmail)
    {
        $this->nom = $sNom;
        $this->prenom = $sPrenom;
        $this->email = $sEmail;
        parent::__construct();
        $this->SetFrom($this->email, $this->nom.' '.$this->prenom);
    }

    public function demandeAnnulee($sEleveX)
    {
        $this->AltBody = $this->subsitute(self::SECTION_ADMISSIBLE,self::ACTION_CANCEL.'.txt',
                                         array('HOST'         => $_SERVER['HTTP_HOST'],
                                              'ELEVE_X'      => $sEleveX));
        
        $this->Body = $this->subsitute(self::SECTION_ADMISSIBLE,self::ACTION_CANCEL.'.html',
                                        array('HOST'         => $_SERVER['HTTP_HOST'],
                                              'ELEVE_X'      => $sEleveX));
        
        $this->Subject = $this->subsitute(self::SECTION_ADMISSIBLE,self::ACTION_CANCEL.'.objet',
                                        array('NOM'    => $this->nom,
                                              'PRENOM' => $this->prenom));
        $this->send();
                         
    }

    public function demandeEnvoyee($sEleveX, $sLinkCancel, $sLinkConfirm)
    {
        $this->AltBody = $this->subsitute(self::SECTION_ADMISSIBLE,self::ACTION_NVDEMANDE.'.txt', 
                                        array('LINK_CONFIRM' => $sLinkConfirm,
                                              'LINK_CANCEL'  => $sLinkCancel,
                                              'ELEVE_X'      => $sEleveX));

        $this->Body = $this->subsitute(self::SECTION_ADMISSIBLE,self::ACTION_NVDEMANDE.'.html', 
                                        array('LINK_CONFIRM' => $sLinkConfirm,
                                              'LINK_CANCEL'  => $sLinkCancel,
                                              'ELEVE_X'      => $sEleveX));

        $this->Subject = $this->subsitute(self::SECTION_ADMISSIBLE,self::ACTION_NVDEMANDE.'.objet',
                                        array('NOM'          => $this->nom,
                                              'PRENOM'       => $this->prenom));
        $this->send();
    }
    
    public function demandeConfirmee($sEleveX, $sXmail)
    {
        $this->AltBody = $this->subsitute(self::SECTION_ADMISSIBLE,self::ACTION_ACCEPTED.'.txt',
                    array('ELEVE_X'      => $sEleveX,
                          'X_MAIL'       => $sXmail));
        
        $this->Body = $this->subsitute(self::SECTION_ADMISSIBLE,self::ACTION_ACCEPTED.'.html',
                array('X_MAIL'         => $sXmail,
                        'ELEVE_X'      => $sEleveX));
        
        $this->Subject = $this->subsitute(self::SECTION_ADMISSIBLE,self::ACTION_ACCEPTED.'.objet',
                array('NOM'          => $this->nom,
                        'PRENOM'       => $this->prenom));
        $this->send();
    }

}