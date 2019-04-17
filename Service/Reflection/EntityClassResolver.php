<?php


namespace Vaderlab\EAV\Core\Service\Reflection;


use Symfony\Bridge\Doctrine\RegistryInterface;
use Vaderlab\EAV\Core\Entity\Entity;
use Vaderlab\EAV\Core\Entity\Schema;


class EntityClassResolver
{
    private $doctrine;
    private $schemaRepository;
    private $entityRepository;

    public function __construct(
        RegistryInterface $doctrine
    ) {
        $this->doctrine = $doctrine;
        $this->schemaRepository = $doctrine->getRepository(Schema::class);
        $this->entityRepository = $doctrine->getRepository(Entity::class);
    }

    public function resolve(object $entityClass): Entity
    {
        $className  = get_class($entityClass);
        $schema     = $this->schemaRepository->findOneBy(['entityClass' => $className]);

        if(!$schema || !($schema instanceof Schema )) {

        }
    }
}