<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 2018-12-13
 * Time: 01:38
 */

namespace Vaderlab\EAV\Core\Entity\ValueType;


use Vaderlab\EAV\Core\Entity\ValueTypeHasDefaultInterface;
use Doctrine\ORM\Mapping as ORM;


trait ValueTypeHasDefaultTrait
{
    /**
     * @ORM\Column(name="default_value", type="string", length=100, nullable=true)
     */
    protected $defaultValue;

    /**
     * @return string
     * @throws \LogicException
     */
    protected function getCastType(): string
    {
        throw new \LogicException('The method must be overwritten');
    }

    /**
     * @return bool|mixed
     */
    public function getDefaultValue()
    {
        return settype($this->defaultValue,$this->getCastType());
    }

    /**
     * @param $value
     * @return ValueTypeHasDefaultInterface
     */
    public function setDefaultValue($value): ValueTypeHasDefaultInterface
    {
        $this->defaultValue = $value;

        return $this;
    }
}