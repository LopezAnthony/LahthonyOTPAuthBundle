<?php

namespace LahthonyOTPAuthBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('lahthony_otp_auth');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic
        $rootNode
            ->children()
                ->enumNode('digest_algo')
                    ->values(hash_algos())
                    ->defaultValue('sha1')
                ->end()
                ->integerNode('digit')
                    ->defaultValue(6)
                ->end()
                ->integerNode('period')
                    ->defaultValue(30)
                ->end()
                ->scalarNode('issuer')
                    ->defaultValue('your_website_name')
                ->end()
                ->scalarNode('image')
                    ->defaultNull()
                ->end()
                ->scalarNode('sender_address')
                    ->defaultValue('2factorAuth@gmail.com')
                ->end()
        ;

        return $treeBuilder;
    }
}
