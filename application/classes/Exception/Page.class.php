<?php

/**
 * Exception lancé dans les pages.
 *
 * @author francois.espinet
 *
 */
class Exception_Page extends Exception_Projet
{

    const FATAL_ERROR = 1;
    const ERROR = 2;
    const WARNING = 4;

    /**
     * Le message user friendly
     * @var string
     */
    protected $userMessage = null;

    /**
     *
     * @author francois.espinet
     * @param string $message        un message technique à logger
     * @param string $userMessage    un message à afficher pour l'utilisateur
     * @param long $code
     * @param Exception $previous
     */
    public function __construct($message, $userMessage, $code = null, $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->userMessage = $userMessage;
    }

    /**
     * Retourne une message lisible et compréhensible par l'utilisateur.
     *
     * @author francois.espinet
     * @return string
     */
    public function getUserMessage()
    {
        if ($this->userMessage === null)
            return $this->getMessage();

        return $this->userMessage;
    }

}
