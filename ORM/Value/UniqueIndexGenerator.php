<?php


namespace Vaderlab\EAV\Core\ORM\Value;


use Vaderlab\EAV\Core\Entity\AbstractValue;
use Vaderlab\EAV\Core\Entity\UniqueIndex;

class UniqueIndexGenerator
{
    /**
     * @param AbstractValue $value
     * @return UniqueIndex
     */
    public function generate(AbstractValue $value): UniqueIndex
    {
        $uniqueIndex = new UniqueIndex();
        $attribute = $value->getAttribute();
        $entity = $value->getEntity();
        $schema = $entity->getSchema();

        $key = sprintf('%d-%d-%s',
            $schema->getId(),
            $attribute->getId(),
            (string)$value->getValue()
        );

        $uniqueIndex
            ->setUniqueKey(hash('sha512', $key))
            ->setEntity($entity)
            ->setAttribute($attribute)
        ;

        $entity->getUniqueIndexes()->add($uniqueIndex);

        return $uniqueIndex;
    }
}