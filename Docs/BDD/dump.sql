-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Sam 15 Septembre 2012 à 10:47
-- Version du serveur: 5.5.24-log
-- Version de PHP: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données: `admissibles`
--

-- --------------------------------------------------------

--
-- Structure de la table `adresses`
--

CREATE TABLE IF NOT EXISTS `adresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identifiant unique',
  `nom` varchar(150) NOT NULL COMMENT 'Nom de l''établissement recommandé',
  `adresse` varchar(300) NOT NULL COMMENT 'Adresse complete de
l''établissement recommandé',
  `tel` varchar(50) NOT NULL COMMENT 'Numéro de téléphone',
  `mail` varchar(150) NOT NULL COMMENT 'Adresse email de contact',
  `comment` text NOT NULL COMMENT 'Description de l''hébergement',
  `valide` int(1) NOT NULL COMMENT 'Validation par l''administrateur des
adresses proposées par les élèves',
  `ordre` int(2) NOT NULL COMMENT 'Ordre d''apparition sur le site (0 =
invisible)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Liste des hébergements
recommandés à proximité du campus' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `demandes`
--

CREATE TABLE IF NOT EXISTS `demandes` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identifiant unique',
  `nom` varchar(250) NOT NULL COMMENT 'Nom',
  `prenom` varchar(250) NOT NULL COMMENT 'Prénom',
  `mail` varchar(250) NOT NULL COMMENT 'Adresse email de l''admissible',
  `sexe` varchar(1) NOT NULL COMMENT 'Sexe : M ou F',
  `prepa` varchar(250) NOT NULL COMMENT 'Etablissement d''origine',
  `filiere` varchar(250) NOT NULL COMMENT 'Filière d''origine',
  `user_eleves` varchar(250) NOT NULL COMMENT 'Lien avec la table eleves :
nom d''utilisateur de l''élève X recevant la demande',
  `serie` int(11) NOT NULL COMMENT 'Série d''admissibilité',
  `status` int(1) NOT NULL COMMENT 'Statut de la demande : 0 = en attente de
validation, 1 = en attente d''acceptation, 2 = Acceptée, 3 = Refusée, 4 =
Annulée',
  `code` varchar(250) NOT NULL COMMENT 'Code unique pour lees paramètres GET
des liens',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Demandes d''hébergement des
admissibles du concours' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `eleves`
--

CREATE TABLE IF NOT EXISTS `eleves` (
  `user` varchar(200) NOT NULL COMMENT 'Nom d''utilisateur de l''élève X :
prenom.nom',
  `sexe` varchar(1) NOT NULL COMMENT 'Sexe : M ou F',
  `promo` int(11) NOT NULL COMMENT 'Promotion : 20XX',
  `section` varchar(50) NOT NULL COMMENT 'Section sportive',
  `prepa` varchar(250) NOT NULL COMMENT 'Etablissement d''origine',
  `filiere` varchar(200) NOT NULL COMMENT 'Filière d''origine',
  `serie1` int(11) NOT NULL COMMENT 'Disponibilité à l''accueil d''un
admissible en série 1 : 0 ou 1',
  `serie2` int(11) NOT NULL COMMENT 'Disponibilité à l''accueil d''un
admissible en série 2 : 0 ou 1',
  `serie3` int(11) NOT NULL COMMENT 'Disponibilité à l''accueil d''un
admissible en série 3 : 0 ou 1',
  `serie4` int(11) NOT NULL COMMENT 'Disponibilité à l''accueil d''un
admissible en série 4 : 0 ou 1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Disponibilité d''hébergement
des élèves X';

-- --------------------------------------------------------

--
-- Structure de la table `parametres`
--

CREATE TABLE IF NOT EXISTS `parametres` (
  `type` int(11) NOT NULL COMMENT '0 = non typé, 1 = Promotions, 2 =
Etablissements, 3 = Filières, 4 = Sections',
  `id` int(11) NOT NULL COMMENT 'Identifiants uniques pour un type donné',
  `value` varchar(250) NOT NULL COMMENT 'Valeur du paramètre'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Paramètres d''administration
et choix prédéfinis';
