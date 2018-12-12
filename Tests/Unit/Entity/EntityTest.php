<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 2018-12-07
 * Time: 00:50
 */

namespace Vaderlab\EAV\Core\Tests\Unit\Entity;


use PHPUnit\Framework\TestCase;
use Vaderlab\EAV\Core\Entity\Entity;
use Vaderlab\EAV\Core\Entity\Schema;

class EntityTest extends TestCase
{
    /**
     * @var Entity
     */
    private $entity;

    private $schema;

    protected function setUp()
    {
        $this->entity = new Entity();
    }

    public function testGetUpdatedAt()
    {
        $this->assertEquals( null, $this->entity->getUpdatedAt() );
        $this->entity->preUpdate();
        $this->assertInstanceOf(\DateTime::class, $this->entity->getUpdatedAt() );
    }

    public function testGetType()
    {
        $type = $this->entity->getType();
        $this->assertInstanceOf( Schema::class, $type );
    }

    public function testGetId()
    {
        $this->assertClassHasAttribute( 'id', Entity::class );
    }

    public function testGetCreatedAt()
    {
        $createdAt = $this->entity->getCreatedAt();
        $this->assertNull( $createdAt );
        $this->entity->prePersist();
        $createdAt = $this->entity->getCreatedAt();
        $this->assertInstanceOf( \DateTime::class, $createdAt );
    }
}
