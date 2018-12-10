<?php
/**
 * Created by PhpStorm.
 * User: kost
 * Date: 2018-12-05
 * Time: 00:37
 */

namespace Vaderlab\EAV\Core\DependencyInjection;


use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class VaderlabEAVExtension extends ConfigurableExtension
{

    /**
     * Configures the passed container according to the merged configuration.
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $config = array();
        // let resources override the previous set value
        foreach ($mergedConfig as $subConfig) {
            $config = array_merge($config, $subConfig);
        }

    }
}