<?php
/**
 * Class pour l'envoi de mail administrateur
 * @author francois.espinet
 * @version 1.0
 *
 */
class Mail_AdminFonc extends Mail {
    
    const X_Action_Canceled = 'anndemande';
    const X_Action_NvDemande = 'nvdemande';
    
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
    
    
}