<?php


namespace Vaderlab\EAV\Core\Schema\Diff;


use Vaderlab\EAV\Core\Schema\Discover\SchemaDiscoverInterface;

/**
 * Class Diff
 * @package Vaderlab\EAV\Core\Schema\Diff
 *
 * @todo: temporary fast solution !!!!!
 */
class Diff implements DiffInterface
{
    /**
     * @var SchemaDiscoverInterface
     */
    private $dbDiscover;

    /**
     * @var SchemaDiscoverInterface
     */
    private $classDiscover;

    /**
     * Diff constructor.
     * @param SchemaDiscoverInterface $dbDiscover
     * @param SchemaDiscoverInterface $classDiscover
     */
    public function __construct(
        SchemaDiscoverInterface $dbDiscover,
        SchemaDiscoverInterface $classDiscover
    )
    {
        $this->dbDiscover = $dbDiscover;
        $this->classDiscover = $classDiscover;
    }

    /**
     * Create difference between file and database schemas
     * @return array
     */
    public function diff(): array
    {
        $schemaFile = $this->classDiscover->getSchema();
        $schemaDB = $this->dbDiscover->getSchema();

        $result = [];
        $schemaKey = 'class';

        foreach ($schemaFile as $fschema) {
            $fname = $fschema[$schemaKey];
            $dSchema = $this->findElementInArray($schemaDB, $schemaKey, $fname);

            if($dSchema === null) {
                $result[$fname] = $this->createDiffArray(null, $fschema, self::SCHEMA_CREATE);

                continue;
            }

            $diff = $this->createSchemaDiff($fschema, $dSchema);

            if(!$diff) {
                continue;
            }

            $result[$fname] = $diff;
        }

        return $result;
    }

    /**
     * @param array $schemaFile
     * @param array $schemaDb
     * @return array|null
     */
    public function createSchemaDiff(array $schemaFile, array $schemaDb): ?array
    {
        $diffRes = [];
        $diffAttrs = [
            'name',
        ];

        foreach ($diffAttrs as $tmpAttrName) {
            $fVal = $schemaFile[$tmpAttrName];
            $dVal = $schemaDb[$tmpAttrName];

            if($fVal === $dVal) {
                continue;
            }

            $diffRes[$tmpAttrName] = $this->createDiffArray($dVal, $fVal, self::ATTRIBUTE_UPDATE);
        }

        $fAttrs = $schemaFile['attributes'];
        $dAttrs = $schemaDb['attributes'];

        $aDiff = $this->createDiffAttributes($fAttrs, $dAttrs);

        if(!!count($aDiff)) {
            $diffRes['attributes'] = $aDiff;
        }


        return !!count($diffRes) ? $diffRes : null;
    }

    /**
     * @param array $attributesFile
     * @param array $attributesDb
     * @return array|null
     */
    protected function createDiffAttributes(array $attributesFile, array $attributesDb): ?array
    {
        $result = [];
        foreach ($attributesFile as $attribute) {
            $name = $attribute['name'];

            $element = $this->findElementInArray($attributesDb, 'name', $name);

            if(!$element) {
                $result[$name] = $this->createDiffArray(null, $attribute, self::ATTRIBUTE_ADD);

                continue;
            }

            $diff = $this->createAssocArrayDiff($attribute, $element);

            if($diff === null) {
                continue;
            }

            $result[$name] = $diff;
        }

        return !!count($result) ? $result : null;
    }

    /**
     * @param array $attributeFile
     * @param array $attributeDb
     * @return array|null
     */
    protected function createDiffAttribute(array $attributeFile, array $attributeDb): ?array
    {
        return $this->createAssocArrayDiff($attributeFile, $attributeDb);
    }

    /**
     * @param array $source
     * @param string $attributeName
     * @param string $attributeValue
     * @return mixed|null
     */
    protected function findElementInArray(array $source, string $attributeName, string $attributeValue)
    {
        foreach ($source as $element) {
            if(!isset($element[$attributeName])) {
                continue;
            }

            if($element[$attributeName] !== $attributeValue) {
                continue;
            }

            return $element;
        }

        return null;
    }

    /**
     * Create diff between 2 associative arrays
     *
     * @param array $a
     * @param array $b
     * @return array|null
     */
    protected function createAssocArrayDiff(array $a, array $b): ?array
    {
        $diff = array_diff_assoc($a, $b);

        if( !count($diff)) {
            return null;
        }

        $result = [];
        foreach ($diff as $key => $value) {
            $result[$key] = $this->createDiffArray($a[$key], $b[$key], self::ATTRIBUTE_UPDATE);
        }

        return $result;
    }

    /**
     * @param $oldValue
     * @param $newValue
     * @param $status
     * @return array
     */
    protected function createDiffArray($oldValue, $newValue, $status): array
    {
        $diff = [
            'status'    => $status,
        ];
        switch ($status) {
            case self::ATTRIBUTE_ADD:
            case self::SCHEMA_CREATE:
            case self::SCHEMA_UPDATE:
                $diff['data'] = $newValue;

                return $diff;
            case self::ATTRIBUTE_UPDATE:
                $diff['old'] = $oldValue;
                $diff['new'] = $newValue;

                return $diff;
        }
    }
}