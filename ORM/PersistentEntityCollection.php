<?php


namespace Vaderlab\EAV\Core\ORM;


use Vaderlab\EAV\Core\Entity\Entity;

class PersistentEntityCollection
{
    private $coll;

    private const POS_ENTITY = 0;
    private const POS_EAV = 1;

    public function __construct()
    {
        $this->clear();
    }

    /**
     * @param Entity $entity
     * @param $eavEntity
     * @return PersistentEntityCollection
     */
    public function add(Entity $entity, $eavEntity): self
    {
        $this->coll[] = [
            self::POS_ENTITY => $entity,
            self::POS_EAV   => $eavEntity,
        ];

        return $this;
    }

    public function clear()
    {
        $this->coll = [];
    }

    /**
     * @param Entity $entity
     * @return mixed|null
     */
    public function getEavByEntity(Entity $entity)
    {
        return $this->search($entity, self::POS_ENTITY, self::POS_EAV);
    }

    /**
     * @param $eavObject
     * @return mixed|null
     */
    public function getEntityByEa($eavObject)
    {
        return $this->search($eavObject, self::POS_EAV, self::POS_ENTITY);
    }

    /**
     * @param $object
     * @param int $searchIndex
     * @param int $getIndex
     * @return mixed|null
     */
    protected function search($object, int $searchIndex, int $getIndex)
    {
        foreach ($this->coll as $data) {
            if($data[$searchIndex] !== $object) {
                continue;
            }

            return $data[$getIndex];
        }

        return null;
    }
}