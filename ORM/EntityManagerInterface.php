<?php


namespace Vaderlab\EAV\Core\ORM;


interface EntityManagerInterface
{
    /**
     * @param object $object
     * @return void
     */
    public function persist($object);

    /**
     * @param object $object
     * @return void
     */
    public function detach($object);

    /**
     * @return void
     */
    public function flush();

    /**
     * @param object $object
     * @return void
     */
    public function refresh($object);

    /**
     * @param object $object
     * @return void
     */
    public function remove($object);

}