<?php


namespace Vaderlab\EAV\Core\Service\ORM;


interface EAVEntityManagerInterface
{
    /**
     * @param object $object
     * @return void
     */
    public function persist(object $object);

    /**
     * @param object $object
     * @return void
     */
    public function detach(object $object);

    /**
     * @return void
     */
    public function flush();

    /**
     * @param object $object
     * @return void
     */
    public function refresh(object $object);

    /**
     * @param object $object
     * @return void
     */
    public function remove(object $object);

}