<?php


namespace Vaderlab\EAV\Core\Schema\Update;


interface SchemaMigrationInterface
{

    const SCHEMA_MIGRATED = 'SCHEMA_MIGRATED';

    const SCHEMA_NO_CHANGES = 'SCHEMA_NO_CHANGES';

    /**
     * @return string
     */
    public function migrate(): string;
}