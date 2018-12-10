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
use Vaderlab\EAV\Core\Entity\ModelType;

class ModelTypeTest extends TestCase
{
    /**
     * @var ModelType
     */
    private $modelType;

    public function setUp()
    {
        $this->modelType = new ModelType();
    }

    public function testSetName()
    {
        $name = 'testModel_1';
        $this->assertInstanceOf( ModelType::class, $this->modelType->setName( $name ) );
        $this->assertEquals( $name, $this->modelType->getName() );

    }

    public function testGetId()
    {
        $this->assertClassHasAttribute( 'id', ModelType::class );
    }

    public function testGetAttributes()
    {
        $this->assertInstanceOf( Collection::class, $this->modelType->getAttributes() );
    }

    public function testSetModels()
    {
        $this->assertInstanceOf( ModelType::class, $this->modelType->setModels( new ArrayCollection() ) );
    }

    public function testGetModels()
    {
        $this->assertInstanceOf( Collection::class, $this->modelType->getModels() );
    }

    public function testSetAttributes()
    {
        $this->assertInstanceOf( ModelType::class, $this->modelType->setAttributes( new ArrayCollection() ) );
    }
}
