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

use ContaoCommunityAlliance\Merger2\Functions\Description\Argument;
use ContaoCommunityAlliance\Merger2\Functions\Description\Description;
use ContaoCommunityAlliance\Merger2\PageProvider;
use Doctrine\DBAL\Connection;

/**
 * Class ArticleExistsFunction.
 *
 * @package ContaoCommunityAlliance\Merger2\Functions
 */
final class ArticleExistsFunction extends AbstractPageFunction
{
    /**
     * Database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * ArticleExistsFunction constructor.
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
     * Function: articleExists(..).
     *
     * Test if an article exists in the column.
     *
     * @param string $column             Column or section name.
     * @param bool   $includeUnpublished If true also unpublished articles are recognized.
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException If an database exception occurs.
     */
    public function __invoke(string $column, bool $includeUnpublished = false): bool
    {
        $time  = time();
        $query = 'SELECT COUNT(id) as count FROM tl_article WHERE pid=? AND inColumn=?';

        if ($includeUnpublished) {
            $query    .= ' AND (start=\'\' OR start<?) AND (stop=\'\' OR stop>?) AND published=1 LIMIT 0,1';
            $statement = $this->connection->prepare($query);

            $statement->bindValue(3, $time);
            $statement->bindValue(4, $time);
        } else {
            $statement = $this->connection->prepare($query);
        }

        $statement->bindValue(1, $this->pageProvider->getPage()->id);
        $statement->bindValue(2, $column);

        if ($statement->execute()) {
            return $statement->fetchColumn(0) > 0;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function describe(): Description
    {
        return Description::create(static::getName())
            ->setDescription('Test if an article exists in the column.')
            ->addArgument('column')
                ->setDescription('Column or section name.')
            ->end()
            ->addArgument('includeUnpublished')
                ->setDescription('If true also unpublished articles are recognized.')
                ->setType(Argument::TYPE_BOOLEAN)
                ->setDefaultValue(false)
            ->end();
    }
}
