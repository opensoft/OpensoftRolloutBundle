<?php

namespace Opensoft\RolloutBundle;

use Opensoft\RolloutBundle\DependencyInjection\Compiler\AddGroupDefinitionPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class OpensoftRolloutBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddGroupDefinitionPass());
    }
}
