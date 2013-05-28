<?php

/**
 * Classe pour l'envoi de mail administrateur fonctionnelle.
 * Elle sert à prévenir les administrateurs d'un problème de fonctionnement métier de l'application.
 *
 * @author francois.espinet
 * @version 1.0
 *
 */
class Mail_AdminFonc extends Mail {

    const X_Action_Canceled = 'anndemande';
    const X_Action_NvDemande = 'nvdemande';

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
    		parent::psend();
    	} catch (Exception_Mail $e) {
    		throw new Exception_Mail("Impossible d'envoyer un mail à l'administateur fonctionnel.", Exception_Mail::Send_Echec_Admin, $e);
    	}
    }
}