<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 2018-12-05
 * Time: 01:26
 */

namespace Vaderlab\EAV\Core\Repository;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository as BaseEntityRepository;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\Query\Expr\Join;
use Vaderlab\EAV\Core\Entity\AbstractValue;
use Vaderlab\EAV\Core\Entity\Attribute;
use Vaderlab\EAV\Core\Entity\EAVEntityInterface;
use Vaderlab\EAV\Core\Entity\Entity;
use Vaderlab\EAV\Core\Entity\Schema;
use Vaderlab\EAV\Core\Entity\ValueTypeHasDefaultInterface;
use Vaderlab\EAV\Core\Service\DataType\DataTypeProvider;
use Vaderlab\EAV\Core\Service\Entity\EntityServiceORM;
use Vaderlab\EAV\Core\Service\Entity\EntityServiceProxy;

/**
 * Class EntityRepository
 * @package Vaderlab\EAV\Core\Repository
 *
 */
class EntityRepository extends BaseEntityRepository
{
}