-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- G�n�r� le: Sam 15 Septembre 2012 � 10:47
-- Version du serveur: 5.5.24-log
-- Version de PHP: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de donn�es: `admissibles`
--

-- --------------------------------------------------------

--
-- Structure de la table `adresses`
--

CREATE TABLE IF NOT EXISTS `adresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identifiant unique',
  `nom` varchar(150) NOT NULL COMMENT 'Nom de l''�tablissement recommand�',
  `adresse` varchar(300) NOT NULL COMMENT 'Adresse complete de
l''�tablissement recommand�',
  `tel` varchar(50) NOT NULL COMMENT 'Num�ro de t�l�phone',
  `mail` varchar(150) NOT NULL COMMENT 'Adresse email de contact',
  `comment` text NOT NULL COMMENT 'Description de l''h�bergement',
  `valide` int(1) NOT NULL COMMENT 'Validation par l''administrateur des
adresses propos�es par les �l�ves',
  `ordre` int(2) NOT NULL COMMENT 'Ordre d''apparition sur le site (0 =
invisible)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Liste des h�bergements
recommand�s � proximit� du campus' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `demandes`
--

CREATE TABLE IF NOT EXISTS `demandes` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identifiant unique',
  `nom` varchar(250) NOT NULL COMMENT 'Nom',
  `prenom` varchar(250) NOT NULL COMMENT 'Pr�nom',
  `mail` varchar(250) NOT NULL COMMENT 'Adresse email de l''admissible',
  `sexe` varchar(1) NOT NULL COMMENT 'Sexe : M ou F',
  `prepa` varchar(250) NOT NULL COMMENT 'Etablissement d''origine',
  `filiere` varchar(250) NOT NULL COMMENT 'Fili�re d''origine',
  `user_eleves` varchar(250) NOT NULL COMMENT 'Lien avec la table eleves :
nom d''utilisateur de l''�l�ve X recevant la demande',
  `serie` int(11) NOT NULL COMMENT 'S�rie d''admissibilit�',
  `status` int(1) NOT NULL COMMENT 'Statut de la demande : 0 = en attente de
validation, 1 = en attente d''acceptation, 2 = Accept�e, 3 = Refus�e, 4 =
Annul�e',
  `code` varchar(250) NOT NULL COMMENT 'Code unique pour lees param�tres GET
des liens',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Demandes d''h�bergement des
admissibles du concours' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `eleves`
--

CREATE TABLE IF NOT EXISTS `eleves` (
  `user` varchar(200) NOT NULL COMMENT 'Nom d''utilisateur de l''�l�ve X :
prenom.nom',
  `sexe` varchar(1) NOT NULL COMMENT 'Sexe : M ou F',
  `promo` int(11) NOT NULL COMMENT 'Promotion : 20XX',
  `section` varchar(50) NOT NULL COMMENT 'Section sportive',
  `prepa` varchar(250) NOT NULL COMMENT 'Etablissement d''origine',
  `filiere` varchar(200) NOT NULL COMMENT 'Fili�re d''origine',
  `serie1` int(11) NOT NULL COMMENT 'Disponibilit� � l''accueil d''un
admissible en s�rie 1 : 0 ou 1',
  `serie2` int(11) NOT NULL COMMENT 'Disponibilit� � l''accueil d''un
admissible en s�rie 2 : 0 ou 1',
  `serie3` int(11) NOT NULL COMMENT 'Disponibilit� � l''accueil d''un
admissible en s�rie 3 : 0 ou 1',
  `serie4` int(11) NOT NULL COMMENT 'Disponibilit� � l''accueil d''un
admissible en s�rie 4 : 0 ou 1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Disponibilit� d''h�bergement
des �l�ves X';

-- --------------------------------------------------------

--
-- Structure de la table `parametres`
--

CREATE TABLE IF NOT EXISTS `parametres` (
  `type` int(11) NOT NULL COMMENT '0 = non typ�, 1 = Promotions, 2 =
Etablissements, 3 = Fili�res, 4 = Sections',
  `id` int(11) NOT NULL COMMENT 'Identifiants uniques pour un type donn�',
  `value` varchar(250) NOT NULL COMMENT 'Valeur du param�tre'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Param�tres d''administration
et choix pr�d�finis';
