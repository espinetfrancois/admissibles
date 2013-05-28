<?php

/**
 * Class pour l'envoi de mail aux admissibles
 * @author francois.espinet
 * @version 1.0
 *
 */
class Mail_Admissible extends Mail
{

    const Action_Cancel = 'anndemande';
    const Action_NvDemande = 'nvdemande';
    const Action_Accepted = 'acceptdemande';

    /**
     * Nom de l'admissible.
     *
     * @var string
     */
    public $nom;

    /**
     * Prenom de l'admissible.
     *
     * @var string
     */
    public $prenom;

    /**
     * Adresse email de l'admissible.
     *
     * @var string
     */
    public $email;

    /**
     * Constructeur.
     *
     * @access public
     * @param string $sNom      nom de l'admissible
     * @param string $sPrenom   prénom de l'admissible
     * @param string $sEmail    email de l'admissible
     */
    public function __construct($sNom, $sPrenom, $sEmail)
    {
        $this->nom = $sNom;
        $this->prenom = $sPrenom;
        $this->email = $sEmail;
        parent::__construct();
        //ajout du destinataire (l'admissible)
        $this->AddAddress($this->email, $this->nom . ' ' . $this->prenom);
    }

    /**
     * Envoi de l'email d'annulation d'une demande.
     * Executée dans le cas ou l'admissible annule sa demande auprès d'un X.
     *
     * @access public
     * @param string $sEleveX le nom de l'élève X auprès de qui la demande à été annulée
     * @throws Exception_Mail
     * @return void
     */
    public function demandeAnnulee($sEleveX)
    {
        $sEleveX = $this->getNameFromUid($sEleveX);
        try {
            $this->AltBody = $this
                    ->_substitute(self::Action_Cancel, self::CONTENT_TYPE_TXT, array('HOST' => $this->appRootUrl, 'ELEVE_X' => $sEleveX));

            $this->Body = $this
                    ->_substitute(self::Action_Cancel, self::CONTENT_TYPE_HTML,
                            array('HOST' => $this->appRootUrl, 'ELEVE_X' => $sEleveX));

            $this->Subject = $this
                    ->_substitute(self::Action_Cancel, self::CONTENT_TYPE_OBJET, array('NOM' => $this->nom, 'PRENOM' => $this->prenom));
            $this->psend();
        } catch (Exception_Mail $e) {
            throw new Exception_Mail("Impossible d'envoyer le mail de notification d'annulation d'une demande.", Exception_Mail::Send_Echec_Admissible_DemandeAnnulee, $e);
        }
    }

    /**
     * Envoi de l'email de confirmation de demande d'hébergement.
     * Ce mail lui permet d'annuler sa demande et de la confirmer.
     * Il vérifie aussi que l'admissible est bien l'auteur de la demande.
     *
     * @access public
     * @param string $sEleveX        l'élève X dont la demande est l'objet
     * @param string $sLinkCancel    le suffixe (sans la partie http://) du lien d'annulation de la demande
     * @param string $sLinkConfirm   le suffixe (sans la partie http://) du lien de confirmation de la demande
     * @throws Exception_Mail
     * @return void
     */
    public function demandeEnvoyee($sEleveX, $sLinkCancel, $sLinkConfirm)
    {
        $sEleveX = $this->getNameFromUid($sEleveX);
        try {
            $this->AltBody = $this
                    ->_substitute(self::Action_NvDemande, self::CONTENT_TYPE_TXT,
                            array('LINK_CONFIRM' => $this->appRootUrl . $sLinkConfirm, 'LINK_CANCEL' => $this->appRootUrl . $sLinkCancel, 'ELEVE_X' => $sEleveX));

            $this->Body = $this
                    ->_substitute(self::Action_NvDemande, self::CONTENT_TYPE_HTML,
                            array('LINK_CONFIRM' => $this->appRootUrl . $sLinkConfirm, 'LINK_CANCEL' => $this->appRootUrl . $sLinkCancel, 'ELEVE_X' => $sEleveX));

            $this->Subject = $this
                    ->_substitute(self::Action_NvDemande, self::CONTENT_TYPE_OBJET,
                            array('NOM' => $this->nom, 'PRENOM' => $this->prenom));
            $this->psend();
        } catch (Exception_Mail $e) {
            throw new Exception_Mail("Impossible d'envoyer le mail de notification de demande envoyée.", Exception_Mail::Send_Echec_Admissible_DemandeEnvoyee, $e);
        }
    }

    /**
     * Envoi de l'email de confirmation de demande.
     * Informe l'admissible que sa demande a été confirmée par un X (il peut donc loger chez lui).
     * Ce mail lui permet de prendre contact avec l'élève.
     *
     * @access public
     * @param string $sEleveX   l'élève à qui la demande à été envoyée
     * @param string $sXmail    l'email de l'élève pour que l'admissible puisse prendre contact avec lui
     * @throws Exception_Mail
     * @return void
     */
    public function demandeConfirmee($sXmail, $sLinkCancel, $sEleveX)
    {
        $sEleveX = $this->getNameFromUid($sEleveX);
        try {
            $this->AltBody = $this
                    ->_substitute(self::Action_Accepted, self::CONTENT_TYPE_TXT,
                            array('ELEVE_X' => $sEleveX, 'X_MAIL' => $sXmail, 'LINK_CANCEL' => $sLinkCance));

            $this->Body = $this
                    ->_substitute(self::Action_Accepted, self::CONTENT_TYPE_HTML,
                            array('X_MAIL' => $sXmail, 'ELEVE_X' => $sEleveX, 'LINK_CANCEL' => $sLinkCancel));

            $this->Subject = $this
                    ->_substitute(self::Action_Accepted, self::CONTENT_TYPE_OBJET, array('NOM' => $this->nom, 'PRENOM' => $this->prenom));
            $this->psend();
        } catch (Exception_Mail $e) {
            throw new Exception_Mail("Impossible d'envoyer le mail de notification de confirmation de demande", Exception_Mail::Send_Echec_Admissible_DemandeConfirmee, $e);
        }
    }

    /**
     * Fonction outil pour les substitutions de texte.
     *
     * @see Mail::subsitute()
     * @author francois.espinet
     * @param string $sAction
     * @param unknown $sType
     * @param unknown $aRemplacement
     * @throws Exception_Mail
     * @return Ambigous <string, mixed>
     */
    protected function _substitute($sAction = '', $sType, $aRemplacement = array())
    {
        return parent::substitute(self::Pers_Admissible, $sAction, $sType, $aRemplacement);
    }

    protected function psend()
    {
        try {
            parent::psend();
        } catch (Exception_Mail $e) {
            /**
             * Ici l'erreur est plus grave!!
             * En effet, l'admissible n'a aucun recours s'il ne reçoit pas de mail
             */
            throw new Exception_Mail("Un mail n'a pas pu être envoyé à un admissible", Exception_Mail::Send_Echec_Admissible, $e);
        }
    }

    protected function getNameFromUid($sUid)
    {
        return ucwords(str_replace('.', ' ', $sUid));
    }
}
