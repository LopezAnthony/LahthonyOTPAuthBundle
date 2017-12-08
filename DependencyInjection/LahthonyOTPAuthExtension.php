<?php

namespace LahthonyOTPAuthBundle\DependencyInjection;

use LahthonyOTPAuthBundle\EventListener\LoginEventListener;
use LahthonyOTPAuthBundle\Manager\OTPManager;
use LahthonyOTPAuthBundle\Model\OTPAuthInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @see http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class LahthonyOTPAuthExtension extends Extension
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $def = $container->getDefinition(OTPManager::class);
        $def->replaceArgument(0, $config['period']);
        $def->replaceArgument(1, $config['digest_algo']);
        $def->replaceArgument(2, $config['digit']);
        $def->replaceArgument(3, $config['issuer']);
        $def->replaceArgument(4, $config['image']);

        $def = $container->getDefinition(LoginEventListener::class);
        $def->replaceArgument(0, $config['roles']);

        $container->registerForAutoconfiguration(OTPAuthInterface::class)->addTag('otp.user.tag.entity');
    }

    public function getNamespace()
    {
        return 'http://www.lahthonyotpauthextension.fr';
    }
}
