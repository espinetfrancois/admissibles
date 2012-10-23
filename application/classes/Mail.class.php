<?php

/**
 * Classe mère de gestion des envois de mail
 * Surcouche de PHPMailer
 * @author francois.espinet
 * @version 1.0
 *
 */
class Mail extends PHPMailer {
    
    const Ini_File = 'mail.ini';
    
    const Section_Admin_Tech = 'admin_tech';
    const Section_Admin_Fonc = 'admin_fonc';
    const Section_X = 'x';
    const Section_Admissible = 'admissible';
    
    const From_Mail = 'admissible@polytechnique.edu';
    const From_Nom = 'Accueil des admissibles';
    
    /**
     * talbeau contenant les textes pour les mails
     * @var array
     */
    private $mails = null;
    
    /**
     * Constructeur
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct(true);
        $this->readIni();
        $this->SetFrom(self::From_Mail, self::From_Nom);

    }
   
    /**
     * Méthode proxiée qui interromp le send si la constante d'envoi des mails est fausse
     * @see parend::Send
     * @author francois.espinet
     */
    protected function send()
    {
        if (APP_MAIL) {
            $this->IsHTML(true);
            parent::Send();
        }

    }
    
    /**
     * Méthode de lecture du fichier de configuration
     * @author francois.espinet
     */
    private function readIni()
    {
        $this->mails = parse_ini_file(CONFIG_PATH.'/'.self::Ini_File, true);
        
    }
    
    /**
     * @brief    Permet d'integrer des variables dans une tradution ou chaine de caractères
     *
     * Dans la chaine de caractère, les élements a remplacer doivent suivre la syntaxe __ELEMENT__
     * Le tableau de remplacement est de la forme : ELEMENT => variable
     *
     * @author        francoisespinet
     * @version        20 mars 2012 - 10:12:13
     * @throws
     *
     * @param     string $sPersonne la personne à qui s'adresse le mail
     * @param   string $sAction l'action dont la personne est notifiée
     * @param    array $aRemplacement tableau associatif de remplacement
     * @param    optionnel $sDelimiteur string delimiteur des variables
     * @return    string avec les variables substituées
     */
    protected function substitute($sPersonne, $sAction='', $sType, $aRemplacement, $sDelimiteur = '__')
    {
        $sChaine = $this->mails[$sPersonne][$sAction];
        $aKeys = array();
        $aValues = array();
        foreach ($aRemplacement as $sVar => $sVarReplace) {
            $aKeys[]=$sDelimiteur.$sVar.$sDelimiteur;
            $aValues[]=$sVarReplace;
        }
        return str_replace($aKeys, $aValues, $sChaine);
    }
    
}