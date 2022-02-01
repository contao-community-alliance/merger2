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

use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\PageModel;
use ContaoCommunityAlliance\Merger2\Functions\Description\Argument;
use ContaoCommunityAlliance\Merger2\Functions\Description\Description;
use ContaoCommunityAlliance\Merger2\PageProvider;

/**
 * Class PageInPathFunction
 *
 * @package ContaoCommunityAlliance\Merger2\Functions
 */
final class PageInPathFunction extends AbstractPageFunction
{
    /**
     * Contao framework.
     *
     * @var ContaoFramework
     */
    private $framework;

    /**
     * PageInPathFunction constructor.
     *
     * @param PageProvider    $pageProvider Page provider.
     * @param ContaoFramework $framework    Contao framework.
     */
    public function __construct(PageProvider $pageProvider, ContaoFramework $framework)
    {
        parent::__construct($pageProvider);

        $this->framework = $framework;
    }

    /**
     * Function: pageInPath(..).
     *
     * Test if page id or alias is in path.
     *
     * @param mixed $pageId Page id or alias.
     *
     * @return bool
     */
    public function __invoke($pageId): bool
    {
        $page = $this->pageProvider->getPage();
        if ($page === null) {
            return false;
        }

        while (true) {
            if ((int) $pageId === (int) $page->id || $pageId === $page->alias) {
                return true;
            }

            if ($page->pid > 0) {
                /** @var Adapter<PageModel> $adapter */
                $adapter = $this->framework->getAdapter(PageModel::class);
                $page    = $adapter->findByPk($page->pid);
                /** @var PageModel|null $page */

                if ($page === null) {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function describe(): Description
    {
        return Description::create(static::getName())
            ->setDescription('Test if page id or alias is in path.')
            ->addArgument('pageId')
                ->setType(Argument::TYPE_INTEGER | Argument::TYPE_STRING)
                ->setDescription('Page id or alias')
            ->end();
    }
}
