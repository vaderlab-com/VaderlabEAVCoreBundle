<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 2018-12-07
 * Time: 00:50
 */

namespace Vaderlab\EAV\Tests\Entity;

use PHPUnit\Framework\TestCase;

use Vaderlab\EAV\Entity\Model;

class ModelTest extends TestCase
{
    /**
     * @var Model
     */
    private $model;

    protected function setUp()
    {
        $this->model = new Model();
    }

    public function testGetUpdatedAt()
    {
        $this->assertEquals( null, $this->model->getUpdatedAt() );
        $this->model->preUpdate();
        $this->assertTrue( $this->model->getUpdatedAt() instanceof \DateTime );
    }

    public function testGetType()
    {

    }

    public function testGetId()
    {

    }

    public function testGetCreatedAt()
    {

    }

    public function testSetType()
    {
    }
}
