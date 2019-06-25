<?php


namespace Vaderlab\EAV\Core\Model;


interface AttributeInterface
{
    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @return string
     */
    public function getType(): ?string;
    /**
     * @return bool
     */
    public function isNullable(): bool;

    /**
     * @return int
     */
    public function getLength(): ?int;

    /**
     * @return String
     */
    public function getDescription(): ?string;

    /**
     * @return String
     */
    public function getDefaultValue(): ?String;

     /**
     * @return bool
     */
    public function isUnique(): bool;
}