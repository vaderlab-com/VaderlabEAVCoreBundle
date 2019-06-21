<?php


namespace Vaderlab\EAV\Core\Schema\Diff\Comparison;


use Vaderlab\EAV\Core\Model\AttributeInterface;

abstract class AbstractCompareProcessor
{
    /**
     * @var PropertyProxyFactory
     */
    protected $propertyProxyFactory;

    /**
     * AbstractCompareProcessor constructor.
     * @param PropertyProxyFactory $propertyProxyFactory
     */
    public function __construct(PropertyProxyFactory $propertyProxyFactory)
    {
        $this->propertyProxyFactory = $propertyProxyFactory;
    }

    /**
     * @param AttributeInterface $source
     * @param AttributeInterface $dest
     * @param boolean $apply
     * @return array
     */
    public function process($source, $dest, bool $apply = false): array
    {

        $aliases = $this->propertyProxyFactory->getAliases();
        $prepare = [];

        foreach ($aliases as $alias) {
            $properyProxy = $this->propertyProxyFactory->getProxy($alias);
            $newValue = $properyProxy->getValue($dest);
            $oldValue = $properyProxy->getValue($source);

            if($oldValue === $newValue) {
                continue;
            }

            if($apply) {
                $properyProxy->setValue($source, $newValue);
            }

            $prepare[$alias] = $this->createDiffArray($oldValue, $newValue);
        }

        return $prepare;
    }

    /**
     * @param $old
     * @param $new
     * @return array#
     */
    protected function createDiffArray($old, $new)
    {
        return [
            'old'   => $old,
            'new'   => $new,
        ];
    }
}