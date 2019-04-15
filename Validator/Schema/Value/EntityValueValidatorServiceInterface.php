<?php


namespace Vaderlab\EAV\Core\Validator\Schema\Value;


use Vaderlab\EAV\Core\Entity\AbstractValue;
use Vaderlab\EAV\Core\Exception\Validator\Schema\Value\InvalidValueException;


interface EntityValueValidatorServiceInterface
{
    /**
     * @param AbstractValue $value
     * @throws InvalidValueException
     */
    public function validate(AbstractValue $value): void;
}