<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @package   Merger²
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @copyright 2013-2014 bit3 UG. 2015-2017 Contao Community Alliance
 * @license   https://github.com/contao-community-alliance/merger2/blob/master/LICENSE LGPL-3.0+
 * @link      https://github.com/contao-community-alliance/merger2
 */

namespace ContaoCommunityAlliance\Merger2\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * FunctionCollectionCompilerPass finds all tagged function collections and passes them to the default
 * function collection.
 *
 * @package ContaoCommunityAlliance\Merger2\DependencyInjection
 */
class FunctionCollectionCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('cca.merger2.function_collection')) {
            return;
        }

        $definition       = $container->findDefinition('cca.merger2.function_collection');
        $taggedServiceIds = $container->findTaggedServiceIds('cca.merger2.function_collection');
        $services         = (array) $definition->getArgument(0);

        foreach (array_keys($taggedServiceIds) as $serviceId) {
            $services[] = new Reference($serviceId);
        }

        $definition->replaceArgument(0, $services);
    }
}
