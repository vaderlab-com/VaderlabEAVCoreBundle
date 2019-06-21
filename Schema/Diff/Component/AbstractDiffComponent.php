<?php


namespace Vaderlab\EAV\Core\Schema\Diff\Component;


class AbstractDiffComponent implements DiffComponentInterface
{

    private $status;

    private $name;

    private $newValue;

    private $oldValue;

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getNewValue()
    {
        return $this->newValue;
    }

    public function getOldValue()
    {
        return $this->oldValue;
    }
}