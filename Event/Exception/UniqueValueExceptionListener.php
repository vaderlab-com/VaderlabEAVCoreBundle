<?php


namespace Vaderlab\EAV\Core\Event\Exception;


use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Vaderlab\EAV\Core\Entity\AbstractValue;
use Vaderlab\EAV\Core\Entity\Attribute;
use Vaderlab\EAV\Core\Entity\Entity;
use Vaderlab\EAV\Core\Entity\UniqueIndex;
use Vaderlab\EAV\Core\Entity\ValueInterface;
use Vaderlab\EAV\Core\Exception\Validator\Schema\Value\ValueUniqueException;
use Vaderlab\EAV\Core\ORM\PersistentEntityCollection;

class UniqueValueExceptionListener
{

    private const D_VAL = 0;
    private const D_SCHEMA = 1;
    private const D_ATTRIBUTE = 2;
    private const D_ENTITY = 3;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var PersistentEntityCollection
     */
    private $persistentEntityCollection;

    /**
     * UniqueValueExceptionListener constructor.
     * @param EntityManagerInterface $entityManager
     * @param PersistentEntityCollection $persistentEntityCollection
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        PersistentEntityCollection $persistentEntityCollection
    ) {
        $this->entityManager = $entityManager;
        $this->persistentEntityCollection = $persistentEntityCollection;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     * @throws ValueUniqueException
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        /** @var UniqueConstraintViolationException $exc */
        $exc = $event->getException();
        if(!($exc instanceof UniqueConstraintViolationException)) {
            return;
        }

        $msg = $exc->getMessage();
        if(strpos($msg, 'vaderlab_eav_unique_idx') === -1) {
            return;
        }

        preg_match('/\[\"(.*?)(\")/', $msg, $match);
        if(!isset($match[1])) {
            return;
        }

        $uniqueKey = $match[1];
        $data = $this->getEntityAttributeUniqueIndex($uniqueKey);
        if(!$data) {
            return;
        }

        throw new ValueUniqueException(
            $data[self::D_SCHEMA],
            $data[self::D_ATTRIBUTE],
            $data[self::D_VAL],
            0,
            $exc
        );
    }

    /**
     * @param string $unique
     * @return array<Entity, Attribute, ValueInterface>|null
     */
    private function getEntityAttributeUniqueIndex(string $unique): ?array
    {
        $entities = $this->persistentEntityCollection->getEntities();
        $entity = null;
        $attribute = null;
        $value = null;
        $idx = null;
        $schema = null;

        /** @var Entity $entity */
        foreach ($entities as $tmpEntity) {
            /** @var UniqueIndex $idx */
            $idx = $tmpEntity->getUniqueIndexes()->filter(function (UniqueIndex $index) use ($unique) {
                return $index->getUniqueKey() === $unique;
            })->first();
            if(!$idx) {
                continue;
            }

            $entity = $tmpEntity;

            break;
        }

        if(!$entity) {
            return null;
        }

        $attribute = $idx->getAttribute();
        $schema = $attribute->getSchema();
        $value = $entity->getValues()->filter(function (AbstractValue $value) use ($attribute) {
            return $value->getAttribute() === $attribute;
        })->first();

        return [
            self::D_VAL => $value,
            self::D_SCHEMA => $schema,
            self::D_ATTRIBUTE => $attribute,
            self::D_ENTITY => $entity
        ];
    }
}