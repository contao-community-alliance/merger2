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

namespace ContaoCommunityAlliance\Merger2\Functions;

use ContaoCommunityAlliance\Merger2\PageProvider;

/**
 * Class AbstractPageFunction.
 *
 * @package ContaoCommunityAlliance\Merger2\Functions
 */
abstract class AbstractPageFunction extends AbstractFunction
{
    /**
     * Current page provider.
     *
     * @var PageProvider
     */
    protected $pageProvider;

    /**
     * AbstractPageFunction constructor.
     *
     * @param PageProvider $pageProvider Page provider.
     */
    public function __construct(PageProvider $pageProvider)
    {
        $this->pageProvider = $pageProvider;
    }
}
