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

namespace ContaoCommunityAlliance\Merger2\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerBundle\ContaoManagerBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use ContaoCommunityAlliance\Merger2\CcaMerger2Bundle;
use SunCat\MobileDetectBundle\MobileDetectBundle;

/**
 * Contao Manager plugin.
 *
 * @package ContaoCommunityAlliance\Merger2\ContaoManager
 */
class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(MobileDetectBundle::class),
            BundleConfig::create(CcaMerger2Bundle::class)
                ->setReplace(['merger2'])
                ->setLoadAfter([ContaoCoreBundle::class, ContaoManagerBundle::class,MobileDetectBundle::class])
        ];
    }
}
