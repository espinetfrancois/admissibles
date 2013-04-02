<?php
/**
* Clé secrète fournie par l'équipe frankiz lors de l'inscription du site.
* Cette clé sert à signer les requêtes et à authentifier le site.
*/
/**
 * @var Config
 */

function frankiz_do_auth($loc = "")
{
    $config = Registry::get('config')->getFrankiz();
    $FKZ_KEY = $config['key'];
   /**
    * Prendre le timestamp permet d'éviter le rejeu de la requête
    */
   $timestamp = time();
   /**
    * url de la page de login, doit correspondre *exactement* à celle entrée dans
    * la base de données de Frankiz (définie lors de l'inscription)
    */
//    $site = 'http://localhost.admissibles/authentification';
//    $site = 'http://admissibles.binets.fr/authentification';
   $site = $config['site'];
   /**
    * Champ non utile pour l'authentification et retransmis tel quel par frankiz.
    * Il est prévu pour pouvoir mettre en place un système de redirection après
    * authentification, vers la page à partir de laquelle le client avait tenté de se connecter.
    */
   $location  = $loc;
   /**
    * Nature de la requête.
    * Fkz renverra ici à la fois les noms de la personne mais aussi ses droits dans différents groupes.
    * Il faut cependant que le site ait les droits sur les informations en question (à définir lors de son inscription).
    */
   $request = json_encode(array('names', 'email', 'sport', 'rights', 'promo'));

   $hash = md5($timestamp . $site . $FKZ_KEY . $request);

   $remote  = $config['host'].'/remote?timestamp=' . $timestamp .
       '&site=' . $site .
       '&location=' . $location .
       '&hash=' . $hash .
       '&request=' . $request;
    header("Location:" . $remote);
    exit();
}

function frankiz_get_response()
{
    $config = Registry::get('config')->getFrankiz();
    $FKZ_KEY = $config['key'];
    // Read request
    $timestamp = (isset($_GET['timestamp']) ? $_GET['timestamp'] : 0);
    $response  = (isset($_GET['response'])  ? urldecode($_GET['response']) : "");
    $hash      = (isset($_GET['hash'])      ? $_GET['hash']      : "");
    $location  = (isset($_GET['location'])  ? $_GET['location']  : "");

    // Frankiz security protocol
    if (abs($timestamp - time()) > 600)
       die("Délai de réponse dépassé. Annulation de la requête");

    //$response = stripslashes($response);
    if (md5($timestamp . $FKZ_KEY . $response) != $hash)
       die("Session compromise.");

    $response = json_decode($response, true);
    $response['location'] = $location;
    // Set empty fields
    $fields = array('hruid',
      'firstname', 'lastname', 'nickname',
     'promo', 'location', 'email');
    foreach ($fields as $k) {
        if (!isset($response[$k]))
        $response[$k] = "";
    }
    return $response;
}
?>