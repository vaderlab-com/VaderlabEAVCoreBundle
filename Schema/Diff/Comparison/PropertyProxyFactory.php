<?php


namespace Vaderlab\EAV\Core\Schema\Diff\Comparison;


class PropertyProxyFactory
{
    /**
     * @var PropertyProxy[]
     */
    private $proxies;

    /**
     * PropertyProxyFactory constructor.
     * @param PropertyProxy ...$proxies
     */
    public function __construct(PropertyProxy ...$proxies)
    {
        $this->proxies = [];

        foreach ($proxies as $proxy) {
            $this->addProxy($proxy);
        }
    }

    /**
     * @param PropertyProxy $proxy
     */
    public function addProxy(PropertyProxy $proxy)
    {
        $this->proxies[$proxy->getAlias()] = $proxy;
    }

    /**
     * @param $alias
     * @return PropertyProxy
     */
    public function getProxy($alias): PropertyProxy
    {
        return $this->proxies[$alias];
    }

    /**
     * @return array
     */
    public function getAliases(): array
    {
        return array_keys($this->proxies);
    }
}