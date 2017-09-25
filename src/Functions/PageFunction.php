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

use ContaoCommunityAlliance\Merger2\Functions\Description\Argument;
use ContaoCommunityAlliance\Merger2\Functions\Description\Description;

/**
 * Class PageFunction
 *
 * @package ContaoCommunityAlliance\Merger2\Functions
 */
class PageFunction extends AbstractPageFunction
{
    /**
     * Function: page(..).
     *
     * Test the page id or alias.
     *
     * @param mixed $pageId Page id or alias.
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function __invoke($pageId)
    {
        $page = $this->pageProvider->getPage();

        if (is_numeric($pageId)) {
            return intval($pageId) == $page->id;
        }

        return $pageId === $page->alias;
    }

    /**
     * {@inheritDoc}
     */
    public function describe()
    {
        return Description::create(static::getName())
            ->setDescription('Test the page id or alias.')
            ->addArgument('pageId')
                ->setDescription('Page id or alias')
                ->setType(Argument::TYPE_INTEGER | Argument::TYPE_STRING)
            ->end();
    }
}
