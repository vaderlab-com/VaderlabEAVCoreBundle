<?php
/**
 * Created by PhpStorm.
 * User: Mi
 * Date: 29.03.2019
 * Time: 1:42
 */

namespace Vaderlab\EAV\Core\Validator\Schema\Attribute;


use Vaderlab\EAV\Core\Exception\Validator\Schema\Attribute\UnknownAttributeValidatorException;

class AttributeSchemaValidatorFactory
{
    /**
     * @var array
     */
    private $validatorCollection;

    /**
     * ValidatorFactory constructor.
     * @param array $validatorCollection
     */
    public function __construct(array $validatorCollection)
    {
        $this->validatorCollection = $validatorCollection;
    }

    /**
     * @param String $attributeType
     * @return AttributeValidatorInterface
     * @throws UnknownAttributeValidatorException
     */
    public function getValidator(String $attributeType): AttributeValidatorInterface
    {
        if( !isset( $this->validatorCollection[$attributeType] ) ) {
            throw new UnknownAttributeValidatorException($attributeType);
        }

        return $this->validatorCollection[$attributeType];
    }
}