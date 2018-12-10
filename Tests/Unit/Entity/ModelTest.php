<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 2018-12-07
 * Time: 00:50
 */

namespace Vaderlab\EAV\Core\Tests\Unit\Entity;


use PHPUnit\Framework\TestCase;
use Vaderlab\EAV\Core\Entity\Model;
use Vaderlab\EAV\Core\Entity\ModelType;

class ModelTest extends TestCase
{
    /**
     * @var Model
     */
    private $model;

    private $modelType;

    protected function setUp()
    {
        $this->model = new Model();
    }

    public function testGetUpdatedAt()
    {
        $this->assertEquals( null, $this->model->getUpdatedAt() );
        $this->model->preUpdate();
        $this->assertInstanceOf(\DateTime::class, $this->model->getUpdatedAt() );
    }

    public function testGetType()
    {
        $type = $this->model->getType();
        $this->assertInstanceOf( ModelType::class, $type );
    }

    public function testGetId()
    {
        $this->assertClassHasAttribute( 'id', Model::class );
    }

    public function testGetCreatedAt()
    {
        $createdAt = $this->model->getCreatedAt();
        $this->assertNull( $createdAt );
        $this->model->prePersist();
        $createdAt = $this->model->getCreatedAt();
        $this->assertInstanceOf( \DateTime::class, $createdAt );
    }
}
