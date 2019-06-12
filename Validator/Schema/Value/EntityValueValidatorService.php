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
        $this->validateIsUnique($value, $attribute);
    }

    /**
     * @param AbstractValue $valueObj
     * @param Attribute $attribute
     * @throws ValueCanNotBeNullException
     */
    public function validateIsNullable(AbstractValue $valueObj, Attribute $attribute)
    {
        if(!$valueObj->getValue() && !$attribute->isNullable()) {
            throw new ValueCanNotBeNullException($attribute->getName());
        }
    }

    /**
     * @param AbstractValue $valueObj
     * @param Attribute $attribute
     * @throws ValueUniqueException
     */
    public function validateIsUnique(AbstractValue $valueObj, Attribute $attribute): void
    {
        if(!$attribute->isUnique()) {
            return;
        }

        $value          = $valueObj->getValue();
        $schema         = $attribute->getSchema();
        $valueClass     = get_class($valueObj);
        $repository     = $this->doctrine->getRepository($valueClass);
        $uniqueValue    = $repository->findOneBy([
            'attribute'     => $attribute,
            'value'         => $value
        ]);

        if(!$uniqueValue) {
            return;
        }

        if($uniqueValue->getId() !== $valueObj->getId()) {
            throw new ValueUniqueException($schema, $attribute, $valueObj);
        }
    }
}