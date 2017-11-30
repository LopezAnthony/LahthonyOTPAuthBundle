<?php

namespace LahthonyOTPAuthBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class LahthonyOTPAuthExtension extends Extension
{
    /**
     * @Todo refactor methods less than 20 lines
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $def = $container->getDefinition('lahthony_otp_auth.otp_manager');
        $def->replaceArgument(0, $config['period']);
        $def->replaceArgument(1, $config['digest_algo']);
        $def->replaceArgument(2, $config['digit']);
        $def->replaceArgument(3, $config['issuer']);
        $def->replaceArgument(4, $config['image']);

        $subDef = $container->getDefinition('LahthonyOTPAuthBundle\EventListener\RegisterOTPAuthKeySubscriber');
        $subDef->replaceArgument(0, $config['sender_address']);
    }

    public function getNamespace()
    {
        return 'http://www.lahthonyotpauthextension.fr';
    }
}
