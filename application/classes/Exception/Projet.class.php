<?php

/**
 * Exception mère pour le projet.
 * Elle à l'avantage de pouvoir être envoyée vers la page d'erreur pour traitement
 * Code à utiliser pour le projet :
 *     1 erreur fatale, pas de récupération possible
 *     2 problème grave entrainant une disfontionnement majeur de l'application (mais celle-ci peut encore fonctionner de façon limitée)
 *
 * @author francois.espinet
 *
 */
class Exception_Projet extends Exception
{

    protected $log_file = 'exceptions.log';

    public function url()
    {
        return urlencode($this->render());
    }

    public static function unurl($exception)
    {
        return urldecode(unserialize($exception));
    }

    public function render()
    {
        $sErrTxt = '<div class="error exception">';
        $sErrTxt .= '<p>Erreur : ' . $this->getMessage() . '</p>';
        $sErrTxt .= '<h3>Localisation :</h3>';
        $sErrTxt .= '<p>Fichier : ' . $this->getFile() . '</p>';
        $sErrTxt .= '<p>Ligne : ' . $this->getLine() . '</p>';
        $sErrTxt .= '<div class="trace"><h3>Trace:</h3>';
        $sErrTxt .= '<div class="pre"><pre>' . $this->getTraceAsString() . '</pre></div></div>';
        $sErrTxt .= '<div class="previous"><h3>Renseignements supplémentaires:</h3>';
        $sErrTxt .= '<div class="pre"><pre>' . $this->getPrevious() . '</pre></div></div>';
        $sErrTxt .= '</div>';
        return $sErrTxt;
    }

    public function handleException()
    {
        header("Location: /errors?exception=" . $this->url());
    }

    /**
     * Permet de logger une exception.
     *
     * @author francois.espinet
     */
    public function log()
    {
        $texte = '[' . date('Y-m-d H:i:s', time()) . '] ' . $_SERVER['REMOTE_ADDR'] . ' - Type d\'exeption : ' . get_class($this) . ' - Message : ' . $this->getMessage() . ' - Fichier : '
                . $this->getFile() . ' - L : ' . $this->getLine() . "\n";
        error_log($texte, 3, LOGS_PATH . '/' . $this->log_file);
    }
}
