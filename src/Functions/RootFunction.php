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
 * Class RootFunction.
 *
 * @package ContaoCommunityAlliance\Merger2\Functions
 */
class RootFunction extends AbstractPageFunction
{
    /**
     * Contao framework.
     *
     * @var ContaoFrameworkInterface
     */
    private $framework;

    /**
     * Construct.
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
     * Function: root(..).
     *
     * Test the root page id or alias.
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
            return intval($pageId) == $page->rootId;
        }

        /** @var PageModel $adapter */
        $adapter  = $this->framework->getAdapter(PageModel::class);
        $rootPage = $adapter->findByPK($page->rootId);

        return $pageId === $rootPage->alias;
    }

    /**
     * {@inheritDoc}
     */
    public function describe()
    {
        return Description::create(static::getName())
            ->setDescription('Test the root page id or alias.')
            ->addArgument('pageId')
                ->setDescription('Page id or alias')
                ->setType(Argument::TYPE_INTEGER | Argument::TYPE_STRING)
            ->end();
    }
}
