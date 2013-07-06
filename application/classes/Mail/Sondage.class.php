<?php

/**
 * Classe pour l'envoi d'un mail de sondage
 *
 * @author francois.espinet
 * @version 1.0
 * @package Mail
 */
class Mail_Sondage extends Mail
{

    private $nom;
    private $prenom;
    private $mail;


    public function __construct($email, $nom, $prenom)
    {
        $this->nom = ucfirst($nom);
        $this->prenom = ucfirst($prenom);
        $this->mail = $email;
        parent::__construct();
        $this->AddAddress($this->mail, $this->nom . ' ' . $this->prenom);
    }

    public function sondage($sujet, $corps) {
        $this->AltBody = $corps;

        $this->Body = $corps;

        $this->Subject = $sujet;
        $this->psend();
    }

    /**
     * (non-PHPdoc)
     * Permet la gestion spécifique des exceptions
     * @author francois.espinet
     * @see Mail::psend()
     * @throws Exception_Mail
     */
    protected function psend()
    {
        try {
            parent::psend();
        } catch (Exception_Mail $e) {
            throw new Exception_Mail("Le mail n'a pas pu être envoyé", $e);
            //TODO : set message ici?
        }
    }

}
