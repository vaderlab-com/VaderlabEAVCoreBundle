<?php


namespace Vaderlab\EAV\Core\Schema\Diff;


    interface DiffInterface
{
    const SCHEMA_CREATE     = 'schema_create';

    const SCHEMA_UPDATE     = 'schema_update';

    const ATTRIBUTE_ADD     = 'attribute_add';

    const ATTRIBUTE_UPDATE  = 'attribute_update';

    public function diff(): array;
}