<?php

/**
 * Classe d'exception pour les mails
 * @author francois.espinet
 *
 */
class Exception_Mail extends Exception_Projet
{
    const Send_Echec_X = 1;
    const Send_Echec_X_NouvelleDemande = 11;
    const Send_Echec_X_DemandeAnnulee = 12;

    const Send_Echec_Admissible = 2;
    const Send_Echec_Admissible_DemandeConfirmee = 21;
    const Send_Echec_Admissible_DemandeAnnulee  = 22;
    const Send_Echec_Admissible_DemandeEnvoyee  = 23;

    const Send_Echec_Admin = 4;

    protected $log_file = 'mails.log';
}