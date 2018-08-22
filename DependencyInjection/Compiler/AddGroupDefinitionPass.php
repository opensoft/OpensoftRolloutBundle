<?php

namespace Opensoft\RolloutBundle\DependencyInjection\Compiler;

use Opensoft\RolloutBundle\Rollout\GroupDefinitionAwareRollout;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class AddGroupDefinitionPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(GroupDefinitionAwareRollout::class)) {
            return;
        }

        $definition = $container->getDefinition(GroupDefinitionAwareRollout::class);

        foreach ($container->findTaggedServiceIds('rollout.group') as $id => $attributes) {
            $definition->addMethodCall('addGroupDefinition', array(new Reference($id)));
        }
    }
}
