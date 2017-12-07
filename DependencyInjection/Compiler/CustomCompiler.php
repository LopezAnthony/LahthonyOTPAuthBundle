<?php

namespace LahthonyOTPAuthBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CustomCompiler implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds('otp.user.tag.entity');
        foreach ($taggedServices as $key => $val) {
            $container->setParameter('otp.user.entity', $key);
        }
    }
}
