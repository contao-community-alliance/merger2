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

namespace ContaoCommunityAlliance\Merger2\Functions;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\Input;
use ContaoCommunityAlliance\Merger2\Functions\Description\Description;
use ContaoCommunityAlliance\Merger2\PageProvider;

/**
 * Class IsMobileFunction
 *
 * @package ContaoCommunityAlliance\Merger2\Functions
 */
final class IsMobileFunction extends AbstractPageFunction
{
    /**
     * Contao framework.
     *
     * @var ContaoFrameworkInterface
     */
    private $framework;

    /**
     * IsMobileFunction constructor.
     *
     * @param ContaoFrameworkInterface $framework Contao framework.
     * @param PageProvider             $pageProvider Page provider.
     */
    public function __construct(ContaoFrameworkInterface $framework, PageProvider $pageProvider)
    {
        parent::__construct($pageProvider);

        $this->framework = $framework;
    }

    /**
     * Check if page is rendered in mobile view.
     *
     * @param bool $cookieOnly If true only the TL_VIEW cookie is recognized.
     *
     * @return bool
     */
    public function __invoke(bool $cookieOnly = false): bool
    {
        if ($cookieOnly) {
            $this->framework->initialize();

            return $this->framework->getAdapter(Input::class)->cookie('TL_VIEW') === 'mobile';
        }

        return (bool) $this->pageProvider->getPage()->isMobile;
    }

    /**
     * {@inheritdoc}
     */
    public function describe(): Description
    {
        return Description::create(static::getName())
            ->setDescription('Detect if page is rendered as mobile page.')
            ->addArgument('cookieOnly')
                ->setDefaultValue(false)
                ->setDescription(
                    // @codingStandardsIgnoreStart
                    'If true only the TL_VIEW cookie is recognized. Otherwise the user agent might active mobile view'
                    . ' if an mobile layout exist.'
                    // @codingStandardsIgnoreEnd
                )
            ->end();
    }
}
