<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 2018-12-10
 * Time: 00:38
 */

namespace Vaderlab\EAV\Core\Tests\Unit\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;
use Vaderlab\EAV\Core\Entity\Schema;

class SchemaTest extends TestCase
{
    /**
     * @var Schema
     */
    private $schema;

    public function setUp()
    {
        $this->schema = new Schema();
    }

    public function testSetName()
    {
        $name = 'testModel_1';
        $this->assertInstanceOf( Schema::class, $this->schema->setName( $name ) );
        $this->assertEquals( $name, $this->schema->getName() );

    }

    public function testGetId()
    {
        $this->assertClassHasAttribute( 'id', Schema::class );
    }

    public function testGetAttributes()
    {
        $this->assertInstanceOf( Collection::class, $this->schema->getAttributes() );
    }

    public function testSetModels()
    {
        $this->assertInstanceOf( Schema::class, $this->schema->setEntities( new ArrayCollection() ) );
    }

    public function testGetModels()
    {
        $this->assertInstanceOf( Collection::class, $this->schema->getEntities() );
    }

    public function testSetAttributes()
    {
        $this->assertInstanceOf( Schema::class, $this->schema->setAttributes( new ArrayCollection() ) );
    }
}
