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

namespace ContaoCommunityAlliance\Merger2\Functions;

use ContaoCommunityAlliance\Merger2\Functions\Description\Description;
use Detection\MobileDetect;

/**
 * Class PlatformFunction.
 *
 * @package ContaoCommunityAlliance\Merger2\Functions
 */
final class PlatformFunction extends AbstractFunction
{
    /**
     * Mobile detect service.
     *
     * @var MobileDetect
     */
    private $mobileDetect;

    /**
     * PlatformFunction constructor.
     *
     * @param MobileDetect $mobileDetect Mobile detect service.
     */
    public function __construct(MobileDetect $mobileDetect)
    {
        $this->mobileDetect = $mobileDetect;
    }

    /**
     * Function: platform(..).
     *
     * @param string $platform Platform value.
     *
     * @return bool
     */
    public function __invoke(string $platform): bool
    {
        switch ($platform) {
            case 'desktop':
                return !$this->mobileDetect->isMobile();
            case 'tablet':
                return $this->mobileDetect->isTablet();
            case 'smartphone':
                return !$this->mobileDetect->isTablet() && $this->mobileDetect->isMobile();
            case 'mobile':
                return $this->mobileDetect->isMobile();
            default:
                return false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function describe(): Description
    {
        return Description::create(static::getName())
            ->setDescription('Test the user platform')
            ->addArgument('platform')
                ->setDescription('Platform type. Valid values are desktop, tablet, smartphone or mobile.')
            ->end();
    }
}
