<?php
/**
 * Created by PhpStorm.
 * User: Etudiant
 * Date: 08/12/2017
 * Time: 12:27.
 */

namespace LahthonyOTPAuthBundle\Test\DependencyInjection;

use LahthonyOTPAuthBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class ConfigurationTest extends TestCase
{
    public function testGetConfiguration()
    {
        $config = new Configuration();
        $treeBuilder = $config->getConfigTreeBuilder();
        $this->assertInstanceOf(TreeBuilder::class, $treeBuilder);
    }
}
