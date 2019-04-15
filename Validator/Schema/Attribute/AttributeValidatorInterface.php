<?php
/**
 * Created by PhpStorm.
 * User: Mi
 * Date: 29.03.2019
 * Time: 1:39
 */

namespace Vaderlab\EAV\Core\Validator\Schema\Attribute;


use Vaderlab\EAV\Core\Exception\Validator\Schema\Attribute\AttributeConfigurationException;


interface AttributeValidatorInterface
{
    /**
     * @param array $attributeConfig
     * @return bool
     * @throws AttributeConfigurationException
     */
     public function validate(array $attributeConfig): bool;
}