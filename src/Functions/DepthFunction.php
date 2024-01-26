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

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\PageModel;
use ContaoCommunityAlliance\Merger2\Functions\Description\Description;
use ContaoCommunityAlliance\Merger2\PageProvider;
use ContaoCommunityAlliance\Merger2\Util\CompareUtil;

/**
 * Class DepthFunction.
 *
 * @package ContaoCommunityAlliance\Merger2\Functions
 */
final class DepthFunction extends AbstractPageFunction
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
     * Function: depth(..).
     *
     * Test the page depth.
     *
     * @param string|int $value Depth with comparing operator.
     *
     * @return bool
     *
     * @throws \RuntimeException When illegal depth value is given.
     */
    public function __invoke($value): bool
    {
        $value = (string) $value;

        if (!preg_match('#^(<|>|<=|>=|=|!=|<>)?\\s*(\\d+)$#', $value, $matches)) {
            throw new \RuntimeException('Illegal depth value: "'.$value.'"');
        }

        $cmp           = $matches[1] ? $matches[1] : '=';
        $expectedDepth = intval($matches[2]);

        /** @var PageModel $pageAdapter */
        $pageAdapter = $this->framework->getAdapter(PageModel::class);
        $depth       = 0;
        $page        = $this->pageProvider->getPage();

        if (!$page) {
            return false;
        }

        while ($page && $page->pid > 0 && $page->type !== 'root') {
            ++$depth;
            $page = $pageAdapter->findByPk($page->pid);
        }

        return CompareUtil::compare($depth, $expectedDepth, $cmp);
    }

    /**
     * {@inheritDoc}
     */
    public function describe(): Description
    {
        return Description::create(static::getName())
            ->setDescription('Test the page depth.')
            ->addArgument('value')
                ->setDescription('Depth with comparing operator, e.g. ">2".')
            ->end();
    }
}
