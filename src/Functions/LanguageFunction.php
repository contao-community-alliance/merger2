<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @package   Merger²
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @copyright 2013-2014 bit3 UG
 * @copyright 2015-2017 Contao Community Alliance
 * @license   https://github.com/contao-community-alliance/merger2/blob/master/LICENSE LGPL-3.0+
 * @link      https://github.com/contao-community-alliance/merger2
 */

namespace ContaoCommunityAlliance\Merger2\Functions;

use ContaoCommunityAlliance\Merger2\Functions\Description\Description;

/**
 * Class LanguageFunction.
 *
 * @package ContaoCommunityAlliance\Merger2\Functions
 */
class LanguageFunction extends AbstractPageFunction
{
    /**
     * Function: language(..).
     *
     * Test the page language.
     *
     * @param string $language Page language.
     *
     * @return bool
     */
    public function __invoke($language)
    {
        $page = $this->pageProvider->getPage();

        return (strtolower($page->language) === strtolower($language));
    }

    /**
     * {@inheritDoc}
     */
    public function describe()
    {
        return Description::create(static::getName())
            ->setDescription('Test the page language.')
            ->addArgument('language')
                ->setDescription('Page language')
            ->end();
    }
}