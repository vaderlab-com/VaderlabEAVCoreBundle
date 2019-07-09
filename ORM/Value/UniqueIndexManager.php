<?php


namespace Vaderlab\EAV\Core\ORM\Value;


use Doctrine\ORM\EntityManagerInterface;
use Vaderlab\EAV\Core\Entity\AbstractValue;
use Vaderlab\EAV\Core\Entity\Attribute;
use Vaderlab\EAV\Core\Entity\Entity;
use Vaderlab\EAV\Core\Entity\UniqueIndex;

class UniqueIndexManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UniqueIndexGenerator
     */
    private $uniqueIndexGenerator;

    public function __construct(
        EntityManagerInterface $entityManager,
        UniqueIndexGenerator $uniqueIndexGenerator
    ) {
        $this->entityManager = $entityManager;
        $this->uniqueIndexGenerator = $uniqueIndexGenerator;
    }

    /**
     * @param Attribute $attribute
     */
    public function removeIndexFromAttribute(Attribute $attribute): void
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->delete()
            ->from(UniqueIndex::class, 'q')
            ->andWhere('q.attribute = :attribute')
            ->setParameters([
                'attribute' => $attribute,
            ])
        ;

        $qb->getQuery()->getResult();
    }

    /**
     * @param Attribute $attribute
     * @return array<UniqueIndex>
     */
    public function generateIndexForAttribute(Attribute $attribute): array
    {
        $indexes = [];
        $schema = $attribute->getSchema();
        $limit = 200;
        $offset = 0;

        $qb = $this->entityManager->getRepository(Entity::class)->createQueryBuilder('q');
        $qb->andWhere('q.schema = :schema')
            ->setParameter('schema', $schema);

        while(true) {
            $entities = $qb->setMaxResults($limit)
                ->setFirstResult($offset)
                ->getQuery()
                ->getResult();
            if(!$entities || !count($entities)) {
                break;
            }

            $indexes = array_merge($indexes, $this->updateIndexForEntities($entities, $attribute));
            $offset += $limit;
        }

        return $indexes;
    }

    /**
     * @param array<Entity> $entities
     * @param Attribute $attribute
     * @return array<UniqueIndex>
     */
    protected function updateIndexForEntities(array $entities, Attribute $attribute): array
    {
        $idx = [];
        foreach ($entities as $entity) {
            $tmp = $this->updateIndexForEntity($entity, $attribute);
            $idx = array_merge($idx, $tmp);
        }

        return $idx;
    }

    /**
     * @param Entity $entity
     * @param Attribute $attribute
     * @return array<UniqueIndex>
     */
    protected function updateIndexForEntity(Entity $entity, Attribute $attribute): array
    {
        $idx = [];
        $values = $entity->getValues()->filter(function (AbstractValue $value) use ($attribute) {
            return $value->getAttribute()->getName() === $attribute->getName();
        });

        if(!$values) {
            return [];
        }

        /** @var AbstractValue $value */
        foreach ($values as $value) {
            $uniqueIndex = $this->uniqueIndexGenerator->generate($value);

            $idx[] = $uniqueIndex;
        }

        return $idx;
    }
}