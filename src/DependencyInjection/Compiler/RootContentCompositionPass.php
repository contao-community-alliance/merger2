<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @package   Merger²
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @copyright 2013-2014 bit3 UG
 * @copyright 2015-2023 Contao Community Alliance
 * @license   https://github.com/contao-community-alliance/merger2/blob/master/LICENSE LGPL-3.0-or-later
 * @link      https://github.com/contao-community-alliance/merger2
 */

declare(strict_types=1);

namespace ContaoCommunityAlliance\Merger2\DependencyInjection\Compiler;

use Contao\CoreBundle\Controller\Page\RootPageController;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * This compiler pass enables articles for root pages that can be inherited by merger²
 */
final class RootContentCompositionPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (! $container->hasDefinition(RootPageController::class)) {
            return;
        }

        $definition = $container->getDefinition(RootPageController::class);
        $tags       = $definition->getTags();

        if (! isset($tags['contao.page'][0])) {
            return;
        }

        $tags['contao.page'][0]['contentComposition'] = true;
        $definition->setTags($tags);
    }
}
