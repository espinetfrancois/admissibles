<?php

/**
 * Décrit une erreur de php transformée en exception
 * Wrapper pour les erreurs php
 * @author francois.espinet
 *
 */
class Exception_Error extends Exception_Projet
{
    protected $_errno = null;
    protected $_errstr = null;
    protected $_errfile = null;
    protected $_errline = null;
    protected $_additionalMessage = null;

    public function __construct($errno, $errstr, $errfile, $errline, $additionalMessage = null) {
        $this->_errfile = $errfile;
        $this->_errstr = $errstr;
        $this->_errline = $errline;
        $this->_errno = $errno;
        $this->_additionalMessage = $additionalMessage;
    }

    public function __toString() {
        return "Une erreur de type : ".$this->_errno." a été détectée dans le fichier : ".
         $this->_errfile." ligne : " .
         $this->_errline.'<br/>'."\n" .
         $this->_errstr .
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