<?php

/**
 * Classe de facade pour toutes les classes de mise en cache
 * Permet d'avoir un appel unifié pour tous les caches : Cache::fonction
 * @author francois.espinet
 *
 */
class Cache
{

    /**
     * Le cache présent sur le serveur (et à défaut Registry)
     * @var Cache_Interface
     */
    private static $_cache = null;

    /**
     * Initialisation de l'instance du cache
     * @author francois.espinet
     */
    public function __construct() {
        $this->setInstance();
    }

    /**
     * Initialisation de l'instance du cache en fonction des caches installés sur le serveur.
     * L'ordre des priorités est apc > xcache > memcache
     * @author francois.espinet
     * @throws Exception_Cache
     */
    protected static function setInstance() {
        if (self::$_cache === null) {
            try {
                if (extension_loaded('apc')) {
                    self::$_cache = new Cache_Apc();
                    return;
                }
                if (extension_loaded('xcache')) {
                    self::$_cache = new Cache_Xcache();
                    return;
                }
                if (extension_loaded('memcache')) {
                    self::$_cache = new Cache_Memcached();
                    return;
                }

                //implémentation par défaut
                self::$_cache = new Cache_Registry();
            } catch (Exception_Cache $e) {
                throw new Exception_Cache("L'instaciation du cache à échoué", null, $e);
            }
        } else {
            throw new Exception_Cache("L'instance à déjà été créée");
        }
    }

    /**
     * Récupère l'instance courante du cache en la créant si elle n'existe pas
     * @author francois.espinet
     * @return Cache_Interface
     */
    public static function getInstance() {
        if (self::$_cache === null) {
            self::setInstance();
        }
        return self::$_cache;
    }

    /**
     * Les méthodes ci-dessous sont des raccourcis évitant l'usage systematique de Cache::getInstance->fonction();
     * Raccourcis (permettent d'éviter de getInstance()
     * @author francois.espinet
     */

    /**
     * Test si l'index à déjà été enregistré pour une valeur
     * @author francois.espinet
     * @param unknown $id l'id de l'élèment à rechercher dans le cache
     */
    public static function test($id)
    {
        return self::getInstance()->test($id);
    }

    /**
     * Nettoie le cache de l'application
     * @author francois.espinet
     */
    public static function clean()
    {
        return self::getInstance()->clean();
    }

    /**
     * Renvoie l'objet stocké à l'index $id, s'il n'existe pas, renvoie false
     * @author francois.espinet
     * @param unknown $id
     */
    public static function load($id)
    {
        return self::getInstance()->load($id);
    }

    /**
     * Supprime l'objet $id du cache
     * @author francois.espinet
     * @param unknown $id
     */
    public static function remove($id)
    {
        return self::getInstance()->remove($id);
    }

    /**
     * Sauvegarde un nouvel objet $data dans le cache, à l'index $id
     * @author francois.espinet
     * @param unknown $data
     * @param unknown $id
     * @param unknown $tags
     * @param boolean $specificLifetime
     */
    public static function save($data, $id, $tags = array(), $specificLifetime = false)
    {
        return self::getInstance()->save($data, $id, $tags, $specificLifetime);
    }

}