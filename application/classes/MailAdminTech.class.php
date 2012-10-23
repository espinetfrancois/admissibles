<?php
/**
 * Class pour l'envoi de mail techniques
 * @author francois.espinet
 * @version 1.0
 *
 */

class MailAdminTech extends Mail {
    
    const Admin_Level_Warning = 'warning';
    const Admin_Level_Error = 'error';
    const Admin_Level_Notice = 'notice';
    
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