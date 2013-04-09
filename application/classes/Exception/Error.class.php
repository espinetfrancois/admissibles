<?php

/**
 * Décrit une erreur de php transformée en exception
 * Wrapper pour les erreurs php
 * @author francois.espinet
 *
 */
class Exception_Error extends Exception_Projet
{
    const Log_Path = "/errors.log";

    protected $message;
    private $string;
    protected $code;
    protected $file;
    protected $line;

    protected $_errno = null;
    protected $_additionalMessage = null;

    public function __construct($errno, $errstr, $errfile, $errline, $additionalMessage = null) {
        $this->file = $errfile;
        $this->message = $errstr;
        $this->line = $errline;
        $this->_errno = $errno;
        $this->code = $errno;
        $this->_additionalMessage = $additionalMessage;
    }

    public function __toString() {
        return "Une erreur de type : ".$this->_errno." a été détectée dans le fichier : ".
         $this->file." ligne : " .
         $this->line.'<br/>'."\n" .
         $this->message .
         ($this->_additionalMessage !== null ? '' : "\n".'<br/>Information Additionnelle : "'.$this->_additionalMessage.'"');
    }

    public function get_errno()
    {
        return $this->_errno;
    }

    public function set_errno($_errno)
    {
        $this->_errno = $_errno;
    }
}