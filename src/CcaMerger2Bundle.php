<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @package   Merger²
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @copyright 2013-2014 bit3 UG
 * @copyright 2015-2018 Contao Community Alliance
 * @license   https://github.com/contao-community-alliance/merger2/blob/master/LICENSE LGPL-3.0-or-later
 * @link      https://github.com/contao-community-alliance/merger2
 */

declare(strict_types=1);

namespace ContaoCommunityAlliance\Merger2;

use ContaoCommunityAlliance\Merger2\DependencyInjection\FunctionCollectionCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class CcaMerger2Bundle.
 *
 * @package ContaoCommunityAlliance\Merger2
 */
final class CcaMerger2Bundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new FunctionCollectionCompilerPass());
    }
}
