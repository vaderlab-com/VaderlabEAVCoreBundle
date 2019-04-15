<?php


namespace Vaderlab\EAV\Core\Service\Entity;


class EntityServiceFactory
{
    private $entityService;

    /**
     * EntityServiceFactory constructor.
     * @param EntityServiceInterface $entityService
     */
    public function __construct(EntityServiceInterface $entityService)
    {
        $this->entityService = $entityService;
    }

    /**
     * @return EntityServiceInterface
     */
    public function getService() : EntityServiceInterface
    {
        return $this->entityService;
    }
}