<?php
return array(
       'root'             => 'accueil.php',
       'accueil'          => 'accueil.php',
       'fonctionnement'   => 'fonctionnement.html',
       'demande'          => 'admissible.php',
       'annulation'       => 'annulation.php',
       'validation'       => 'validation.php',
       'authentification' => 'authentification.php',
       'deconnexion'      => 'deconnexion.php',
       'mentions-legales' => 'mentions-legales.php',
       'contacts'         => 'contacts.php',
       'x'                => array(
                                   'root'                    => 'eleve.html',
                                   'connexion'               => 'eleve.php',
                                   'donnees-personnelles'    => 'eleve.php',
                                   'espace-personnel'        => 'eleve.php'
                                   ),
       'admissible'       => array(
                                   'inscription'            => 'admissible.php',
                                   'adresses'               => 'adresses.php',
                                   'annulation-demande'     => 'annulation.php',
                                   'validation-demande'     => 'validation.php'
                                   ),
       'administration'   => array(
                                   'root'                   => 'admin.php',
                                   'gestion'                => 'admin.php',
                                   'listes-admissibles'     => 'admin/insertion_admissibles.php',
                                   'series-admissibilites'  => 'admin/series.php',
                                   'etablissements'         => 'admin/etablissements.php',
                                   'filieres'               => 'admin/filieres.php',
                                   'inscriptionsx'          => 'admin/inscriptionsx.php',
                                   'demandes'               => 'admin/demandes.php',
                                   'hebergements'           => 'admin/hebergements.php',
                                   'remise-a-zero'          => 'admin/raz.php'
                                  )
);
