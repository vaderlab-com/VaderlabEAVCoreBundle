<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 2018-12-05
 * Time: 00:37
 */

namespace Vaderlab\EAV\Core\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class VaderlabEAVCoreExtension extends Extension
{
    private $configuration;
    /**
     * Loads a specific configuration.
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);
        // $container->setParameter('vaderlab_eav_core.entity.value_types', $config['entity']['value_types']);

    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        if( !$this->configuration ) {
            $this->configuration = new Configuration();
        }

        return $this->configuration;
    }
}