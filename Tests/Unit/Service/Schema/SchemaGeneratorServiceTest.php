<?php
/**
 * Created by PhpStorm.
 * User: Mi
 * Date: 29.03.2019
 * Time: 1:14
 */

namespace Vaderlab\EAV\Core\Tests\Unit\Service\Schema;

use Vaderlab\EAV\Core\Entity\Attribute;
use Vaderlab\EAV\Core\Entity\Schema;
use Vaderlab\EAV\Core\Service\Schema\SchemaGeneratorService;
use PHPUnit\Framework\TestCase;

class SchemaGeneratorServiceTest extends TestCase
{
    /**
     * @var SchemaGeneratorService
     */
    private $schemaGeneratorService;

    private $validAttributesConfig = [
        [
            'name'  => 'Some_Attribute',
            'nullable'  => true,
            'type'  => 'integer',
            'length'    => null,
            'description'   => 'Simple description',
            'indexable' => true,
        ]
    ];

    private $nonExitParamConfig;

    private $unregisteredParamConfig;

    /**
     * @TODO: Attribute and Schema cless hardcoded !
     */
    public function setUp()
    {
        $this->schemaGeneratorService = new SchemaGeneratorService(
            Schema::class,
            Attribute::class
        );
    }

    public function testGenerate()
    {
        $schema = $this->schemaGeneratorService->generate('Example', $this->validAttributesConfig);

        $this->assertInstanceOf(Schema::class, get_class($schema));
    }

    public function testCreateSchemaModel()
    {
    }
}
