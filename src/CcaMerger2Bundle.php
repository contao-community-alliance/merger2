<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @package   Merger²
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @copyright 2013-2014 bit3 UG
 * @copyright 2015-2022 Contao Community Alliance
 * @license   https://github.com/contao-community-alliance/merger2/blob/master/LICENSE LGPL-3.0-or-later
 * @link      https://github.com/contao-community-alliance/merger2
 */

declare(strict_types=1);

namespace ContaoCommunityAlliance\Merger2;

use ContaoCommunityAlliance\Merger2\DependencyInjection\Compiler\RootContentCompositionPass;
use ContaoCommunityAlliance\Merger2\DependencyInjection\FunctionCollectionCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
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
        // We must load after \Terminal42\ServiceAnnotationBundle\DependencyInjection\Compiler\ServiceAnnotationPass
        // which uses priority 110
        $container->addCompilerPass(new RootContentCompositionPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 109);
    }
}
