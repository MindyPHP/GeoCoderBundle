<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\GeoCoderBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class GeoCoderPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('mindy.bundle.geocoder.chain_geocoder')) {
            return;
        }

        $definition = $container->getDefinition('mindy.bundle.geocoder.chain_geocoder');
        foreach ($container->findTaggedServiceIds('geocoder.geocoder') as $id => $parameters) {
            $definition->addMethodCall('addGeoCoder', [new Reference($id)]);
        }
    }
}
