<?php


namespace Vaderlab\EAV\Core\Model;


trait EntityTrait
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var array
     */
    private $__values = [];

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getValue(string $key)
    {
        return isset($this->__values[$key]) ? $this->__values[$key] : null;
    }

    /**
     * @param string $key
     * @param $value
     * @return EntityInterface
     */
    public function setValue(string $key, $value): EntityInterface
    {
        $this->__values[$key] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->__values;
    }

    /**
     * @param array $values
     * @return EntityInterface
     */
    public function setValues(array $values): EntityInterface
    {
        foreach ($values as $key => $value) {
            $this->setValue($key, $value);
        }

        return $this;
    }
}