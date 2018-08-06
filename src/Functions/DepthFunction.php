<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @package   Merger²
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @copyright 2013-2014 bit3 UG
 * @copyright 2015-2018 Contao Community Alliance
 * @license   https://github.com/contao-community-alliance/merger2/blob/master/LICENSE LGPL-3.0+
 * @link      https://github.com/contao-community-alliance/merger2
 */

declare(strict_types=1);

namespace ContaoCommunityAlliance\Merger2\Functions;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\PageModel;
use ContaoCommunityAlliance\Merger2\Functions\Description\Description;
use ContaoCommunityAlliance\Merger2\PageProvider;

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
     * Function: depth(..).
     *
     * Test the page depth.
     *
     * @param string $value Depth with comparing operator.
     *
     * @return bool
     *
     * @throws \RuntimeException When illegal depth value is given.
     */
    public function __invoke(string $value): bool
    {
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

        while ($page->pid > 0 && $page->type != 'root') {
            ++$depth;
            $page = $pageAdapter->findByPk($page->pid);
        }

        return $this->compareDepth($cmp, $depth, $expectedDepth);
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

    /**
     * Compare the depth
     *
     * @param string $cmp           Compare operator.
     * @param string $depth         Given depth.
     * @param string $expectedDepth Expected depth.
     *
     * @return bool
     */
    private function compareDepth($cmp, $depth, $expectedDepth)
    {
        switch ($cmp) {
            case '<':
                return $depth < $expectedDepth;
            case '>':
                return $depth > $expectedDepth;
            case '<=':
                return $depth <= $expectedDepth;
            case '>=':
                return $depth >= $expectedDepth;
            case '!=':
            case '<>':
                return $depth != $expectedDepth;
            default:
                return $depth == $expectedDepth;
        }
    }
}
