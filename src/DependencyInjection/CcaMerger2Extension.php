<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @copyright 2013,2014 bit3 UG. 2015-2017 Contao Community Alliance
 * @author    David Molineus <david.molineus@netzmacht.de>
 *
 * @link      https://github.com/contao-community-alliance/merger2
 *
 * @license   LGPL-3.0+
 */


namespace ContaoCommunityAlliance\Merger2\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class CcaMergerExtension.
 *
 * @package ContaoCommunityAlliance\Merger2\DependencyInjection
 */
class CcaMerger2Extension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('services.yml');
    }
}
