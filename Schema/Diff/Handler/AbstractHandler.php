<?php


namespace Vaderlab\EAV\Core\Schema\Diff\Handler;


interface HandlerInterface
{
    public function handle(string $status, $source, $newValue, $oldValue): void;
}