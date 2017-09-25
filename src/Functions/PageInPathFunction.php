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

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\PageModel;
use ContaoCommunityAlliance\Merger2\Functions\Description\Argument;
use ContaoCommunityAlliance\Merger2\Functions\Description\Description;
use ContaoCommunityAlliance\Merger2\PageProvider;

/**
 * Class PageInPathFunction
 *
 * @package ContaoCommunityAlliance\Merger2\Functions
 */
class PageInPathFunction extends AbstractPageFunction
{
    /**
     * Contao framework.
     *
     * @var ContaoFrameworkInterface
     */
    private $framework;

    /**
     * PageInPathFunction constructor.
     *
     * @param PageProvider             $pageProvider Page provider.
     * @param ContaoFrameworkInterface $framework    Contao framework.
     */
    public function __construct(PageProvider $pageProvider, ContaoFrameworkInterface $framework)
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
    public function __invoke($pageId)
    {
        $page = $this->pageProvider->getPage();

        while (true) {
            if (intval($pageId) == $page->id || $pageId == $page->alias) {
                return true;
            }

            if ($page->pid > 0) {
                /** @var PageModel $adapter */
                $adapter = $this->framework->getAdapter(PageModel::class);
                $page    = $adapter->findByPk($page->pid);

                if (!$page) {
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
    public function describe()
    {
        return Description::create(static::getName())
            ->setDescription('Test if page id or alias is in path.')
            ->addArgument('pageId')
                ->setType(Argument::TYPE_INTEGER | Argument::TYPE_STRING)
                ->setDescription('Page id or alias')
            ->end();
    }
}
