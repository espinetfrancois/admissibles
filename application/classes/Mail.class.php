<?php

/**
 * Classe mère de gestion des envois de mail
 * Surcouche de PHPMailer
 * @author francois.espinet
 * @version 1.0
 *
 */
abstract class Mail extends PHPMailer {

    const Ini_File = 'mail.ini';

    const Pers_Admin_Tech = 'admin_tech';
    const Pers_Admin_Fonc = 'admin_fonc';
    const Pers_X = 'x';
    const Pers_Admissible = 'admissible';

//     const From_Mail = 'admissible@polytechnique.edu';
//     const From_Nom = 'Accueil des admissibles';

    const CONTENT_TYPE_TXT = "txt";
    const CONTENT_TYPE_HTML = "html";
    const CONTENT_TYPE_OBJET = "objet";

    /**
     * L'url racine de l'application (de type http://application) sans / !
     * @var string
     */
    protected $appRootUrl = "";
    /**
     * talbeau contenant les textes pour les mails
     * @var array
     */
    private $mails = null;

    protected $adminMail = "admissibles@binets.polytechnique.fr";

    /**
     * Constructeur
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct(true);
        $this->readIni();
        $this->Mailer = 'sendmail';
        $this->appRootUrl = 'http://'.$_SERVER['HTTP_HOST'];
    }

    /**
     * Méthode proxiée qui interromp le send si la constante d'envoi des mails est fausse
     * @see parend::Send
     * @author francois.espinet
     */
    protected function psend()
    {
        if (APP_MAIL) {
            try {
                $this->IsHTML(true);
                parent::Send();
            } catch (Exception $e) {
                throw new Exception_Mail("Un problème est survenu lors de l'envoi du mail", null, $e);
            }
        } else {
            $this->PreSend();
        }
    }

    /**
     * Méthode de lecture du fichier de configuration
     * @author francois.espinet
     */
    private function readIni()
    {
        $this->mails = parse_ini_file(CONFIG_PATH.'/'.self::Ini_File, true);
        $this->SetFrom($this->mails['application']['app_email'], $this->mails['application']['app_name']);
        unset($this->mails['application']);
        $this->adminMail = $this->mails['admin']['email'];
        unset($this->mails['admin']);
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
     * @param   string $sType Type du champ (txt, html ou objet)
     * @param    array $aRemplacement tableau associatif de remplacement
     * @param    optionnel $sDelimiteur string delimiteur des variables
     * @return    string avec les variables substituées
     */
    protected function substitute($sPersonne, $sAction='', $sType, $aRemplacement, $sDelimiteur = '__')
    {
        $sChaine = $this->mails[$sPersonne][$sAction.'.'.$sType];
        $aKeys = array();
        $aValues = array();
        foreach ($aRemplacement as $sVar => $sVarReplace) {
            $aKeys[]=$sDelimiteur.$sVar.$sDelimiteur;
            $aValues[]=$sVarReplace;
        }
        return str_replace($aKeys, $aValues, $sChaine);
    }

}