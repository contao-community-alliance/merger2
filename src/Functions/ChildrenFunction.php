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

use ContaoCommunityAlliance\Merger2\Functions\Description\Argument;
use ContaoCommunityAlliance\Merger2\Functions\Description\Description;
use ContaoCommunityAlliance\Merger2\PageProvider;
use ContaoCommunityAlliance\Merger2\Util\CompareUtil;
use Doctrine\DBAL\Connection;

/**
 * Class ChildrenFunction.
 *
 * @package ContaoCommunityAlliance\Merger2\Functions
 */
final class ChildrenFunction extends AbstractPageFunction
{
    /**
     * Database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * Construct.
     *
     * @param PageProvider $pageProvider Page provider.
     * @param Connection   $connection   Database connection.
     */
    public function __construct(PageProvider $pageProvider, Connection $connection)
    {
        parent::__construct($pageProvider);

        $this->connection = $connection;
    }

    /**
     * Function: children(..).
     *
     * Test if the page have the specific count of children.
     *
     * @param string|int $count              Count of children, additional starting with comparator.
     * @param bool       $includeUnpublished Include unpublished pages.
     *
     * @return bool
     *
     * @throws \RuntimeException When an illegal count value if given.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function __invoke($count, bool $includeUnpublished = false): bool
    {
        $page = $this->pageProvider->getPage();
        if ($page === null) {
            return false;
        }

        $count = (string) $count;

        if (!preg_match('#^(<|>|<=|>=|=|!=|<>)?\\s*(\\d+)$#', $count, $matches)) {
            throw new \RuntimeException('Illegal count value: "'.$count.'"');
        }

        $cmp   = $matches[1] ? $matches[1] : '=';
        $count = intval($matches[2]);
        $time  = time();
        $query = 'SELECT COUNT(id) as count FROM tl_page WHERE pid=?';

        if ($includeUnpublished) {
            $query    .= ' AND (start=\'\' OR start<?) AND (stop=\'\' OR stop>?) AND published=1 LIMIT 0,1';
            $statement = $this->connection->prepare($query);

            $statement->bindValue(2, $time);
            $statement->bindValue(3, $time);
        } else {
            $statement = $this->connection->prepare($query);
        }

        $statement->bindValue(1, $page->id);
        $result = $statement->executeQuery();

        return CompareUtil::compare($result->fetchOne(), $count, $cmp);
    }

    /**
     * {@inheritDoc}
     */
    public function describe(): Description
    {
        return Description::create(self::getName())
            ->setDescription('Test if the page have the specific count of children.')
            ->addArgument('count')
                ->setDescription('Count of children.')
                ->setType(Argument::TYPE_INTEGER)
            ->end()
            ->addArgument('includeUnpublished')
                ->setDescription('Include unpublished pages.')
                ->setType(Argument::TYPE_BOOLEAN)
                ->setDefaultValue(false)
            ->end();
    }
}
