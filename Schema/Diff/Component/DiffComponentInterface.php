<?php


namespace Vaderlab\EAV\Core\Schema\Diff\Component;


interface DiffComponentInterface
{
    public function getStatus();

    public function getName();

    public function getNewValue();

    public function getOldValue();
}