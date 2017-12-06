<?php

namespace LahthonyOTPAuthBundle;

use LahthonyOTPAuthBundle\DependencyInjection\Compiler\CustomCompiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class LahthonyOTPAuthBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new CustomCompiler());
    }
}
