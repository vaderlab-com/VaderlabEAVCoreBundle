<?php


namespace Vaderlab\EAV\Core\Service\Entity;


use Symfony\Component\DependencyInjection\ContainerInterface;

class EntityServiceProxy
{
    /**
     * @var string
     */
    private $entityService;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * EntityServiceProxy constructor.
     * @param ContainerInterface $containerAware
     * @param string $entityService
     */
    public function __construct(ContainerInterface $container, string $entityService)
    {
        $this->entityService = $entityService;
        $this->container = $container;
    }

    /**
     * @return EntityServiceInterface
     */
    public function getService() : EntityServiceInterface
    {
        return $this->container->get($this->entityService);
    }
}