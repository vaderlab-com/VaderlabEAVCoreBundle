<?php


namespace Vaderlab\EAV\Core\Schema\Diff\Comparison;


class PropertyProxy
{
    /**
     * @var string
     */
    private $setter;

    /**
     * @var string
     */
    private $getter;

    /**
     * @var string
     */
    private $alias;

    /**
     * PropertyProxy constructor.
     * @param string $alias
     * @param string $setter
     * @param string $getter
     */
    public function __construct(
        string $alias,
        string $setter,
        string $getter
    ) {
        $this->setter = $setter;
        $this->getter = $getter;
        $this->alias  = $alias;
    }

    /**
     * @param mixed $object
     * @return mixed
     */
    public function getValue($object)
    {
        return call_user_func([$object, $this->getter]);
    }

    /**
     * @param mixed $attribute
     * @param $value
     * @return mixed
     */
    public function setValue($object, $value)
    {
        return call_user_func_array([$object, $this->setter], [$value]);
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }
}