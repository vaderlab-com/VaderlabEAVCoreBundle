<?php


namespace Vaderlab\EAV\Core\Event\Entity;


use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Vaderlab\EAV\Core\Entity\AbstractValue;
use Vaderlab\EAV\Core\Validator\Schema\Value\EntityValueValidatorServiceInterface;


class ValueValidationSubscriber implements EventSubscriber
{

    private $valueValidatorService;

    /**
     * ValueValidationSubscriber constructor.
     * @param EntityValueValidatorServiceInterface $valueValidatorService
     */
    public function __construct(EntityValueValidatorServiceInterface $valueValidatorService)
    {
        $this->valueValidatorService = $valueValidatorService;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return string[]
     */
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    /**
     * @param LifecycleEventArgs $arg
     * @throws \Vaderlab\EAV\Core\Exception\Validator\Schema\Value\InvalidValueException
     */
    public function preUpdate(LifecycleEventArgs $arg): void
    {
        $this->validateValue($arg);
    }

    /**
     * @param LifecycleEventArgs $arg
     * @throws \Vaderlab\EAV\Core\Exception\Validator\Schema\Value\InvalidValueException
     */
    public function prePersist(LifecycleEventArgs $arg): void
    {
        $this->validateValue($arg);
    }

    /**
     * @param LifecycleEventArgs $arg
     * @throws \Vaderlab\EAV\Core\Exception\Validator\Schema\Value\InvalidValueException
     */
    public function validateValue(LifecycleEventArgs $arg): void
    {
        $valueObject = $arg->getObject();

        if(!($valueObject instanceof AbstractValue)) {
            return;
        }

        $this->valueValidatorService->validate($valueObject);
    }
}