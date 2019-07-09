<?php


namespace Vaderlab\EAV\Core\Validator\Schema\Value;


use Symfony\Bridge\Doctrine\RegistryInterface;
use Vaderlab\EAV\Core\Entity\AbstractValue;
use Vaderlab\EAV\Core\Entity\Attribute;
use Vaderlab\EAV\Core\Exception\Validator\Schema\Value\InvalidValueException;
use Vaderlab\EAV\Core\Exception\Validator\Schema\Value\ValueCanNotBeNullException;
use Vaderlab\EAV\Core\Exception\Validator\Schema\Value\ValueUniqueException;

/**
 * Class EntityValueValidatorService
 * @package Vaderlab\EAV\Core\Service\Entity
 *
 * @TODO: Temporary solution !!!
 */
class EntityValueValidatorService implements EntityValueValidatorServiceInterface
{
    /**
     * @var RegistryInterface
     */
    private $doctrine;

    /**
     * EntityValueValidatorService constructor.
     * @param RegistryInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param AbstractValue $value
     * @throws InvalidValueException
     */
    public function validate(AbstractValue $value): void
    {
        $attribute = $value->getAttribute();

        $this->validateIsNullable($value, $attribute);
    }

    /**
     * @param AbstractValue $valueObj
     * @param Attribute $attribute
     * @throws ValueCanNotBeNullException
     */
    public function validateIsNullable(AbstractValue $valueObj, Attribute $attribute)
    {
        if($valueObj->getValue() === null && !$attribute->isNullable()) {
            throw new ValueCanNotBeNullException($attribute->getName());
        }
    }
}