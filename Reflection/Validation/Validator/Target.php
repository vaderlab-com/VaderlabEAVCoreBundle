<?php


namespace Vaderlab\EAV\Core\Reflection\Validation\Validator;


use Vaderlab\EAV\Core\Annotation\Attribute;
use Vaderlab\EAV\Core\Exception\Service\Reflection\PropertySchemeInvalidException;

class Target
{

    public function validate(): void
    {

    }

    /**
     * @param array<Attribute> $attributes
     */
    public function validate1(array $attributes)
    {
        $targets = [];
        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {
            $targets[] = $attribute->target;
        }

        $countTargets = array_count_values($targets);
        $filtered = array_filter(
            $countTargets,
            function ($elem) {
                return($elem > 1);
            });

        if(!count($filtered)) {
            return;
        }

        throw new PropertySchemeInvalidException('Incorrectly declared property "target" for attribute %s');
    }
}