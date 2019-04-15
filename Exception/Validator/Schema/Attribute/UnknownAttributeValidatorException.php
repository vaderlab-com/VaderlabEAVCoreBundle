<?php
/**
 * Created by PhpStorm.
 * User: Mi
 * Date: 29.03.2019
 * Time: 1:45
 */

namespace Vaderlab\EAV\Core\Exception\Validator\Schema\Attribute;


use Throwable;

class UnknownAttributeValidatorException extends AttributeConfigurationException
{
    public function __construct(string $attrTypr = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Unregistered validator for attribute type "%s"', $attrTypr), $code, $previous);
    }
}