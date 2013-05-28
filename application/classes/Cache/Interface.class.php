<?php

/**
 * Interface que tout bon cadidat pour être cache doit implémenter.
 *
 * @author francois.espinet
 *
 */
interface Cache_Interface
{

    public function test($id);
    public function clean();
    public function load($id);
    public function remove($id);
    public function save($data, $id, $tags = array(), $specificLifetime = false);
}
