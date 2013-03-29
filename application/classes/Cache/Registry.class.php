<?php

/**
 * Class d'apatation du registre à un cache
 * Dans le cas ou aucun cache n'est disponible sur le serveur, on utilise le registre comme classe
 * @author francois.espinet
 *
 */
class Cache_Registry extends Registry implements Cache_Interface
{

    public function test($id) {
        return self::isRegistered($id);
    }

    public function load($id) {
        if (! self::isRegistered($id)) {
            return false;
        }
        return $this->get($id);
    }

    public function clean() {
        //ne pas nettoyer le registre, d'autres choses peuvent en avoir besoin!
        return;
    }

    public function remove($id) {
        $this->offsetUnset($id);
    }

    public function save($data, $id , $tags = array(), $specificLifetime = false) {
        $this->set($id, $data);
    }
}