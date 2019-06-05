<?php


namespace Vaderlab\EAV\Core\Model;


interface EntityInterface
{
    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @param string $key
     * @return mixed
     */
    public function getValue(string $key);

    /**
     * @param string $key
     * @param $value
     * @return EntityInterface
     */
    public function setValue(string $key, $value): EntityInterface;

    /**
     * @return array
     */
    public function getValues(): array;

    /**
     * @param array $values
     * @return EntityInterface
     */
    public function setValues(array $values): EntityInterface;
}