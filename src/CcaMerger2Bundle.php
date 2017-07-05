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

namespace ContaoCommunityAlliance\Merger2;

use ContaoCommunityAlliance\Merger2\DependencyInjection\FunctionCollectionCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class CcaMerger2Bundle.
 *
 * @package ContaoCommunityAlliance\Merger2
 */
class CcaMerger2Bundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new FunctionCollectionCompilerPass());
    }
}
