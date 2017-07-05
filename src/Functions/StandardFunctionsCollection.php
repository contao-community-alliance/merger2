<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @package   Merger²
 * @author    Tristan Lins <tristan.lins@bit3.de>
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @copyright 2013-2014 bit3 UG. 2015-2017 Contao Community Alliance
 * @license   https://github.com/contao-community-alliance/merger2/blob/master/LICENSE LGPL-3.0+
 * @link      https://github.com/contao-community-alliance/merger2
 */

namespace ContaoCommunityAlliance\Merger2\Functions;

use Detection\MobileDetect;
use Doctrine\DBAL\Connection;
use Imagine\Exception\RuntimeException;

/**
 * Class StandardFunctions.
 */
class StandardFunctionsCollection implements FunctionCollectionInterface
{
    /**
     * Mobile detect service.
     *
     * @var MobileDetect
     */
    private $mobileDetect;

    /**
     * Database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * StandardFunctionsCollection constructor.
     *
     * @param MobileDetect $mobileDetect Mobile detect service.
     * @param Connection   $connection   Database connection.
     */
    public function __construct(MobileDetect $mobileDetect, Connection $connection)
    {
        $this->mobileDetect = $mobileDetect;
        $this->connection   = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($name)
    {
        return in_array(
            $name,
            [
                'articleExists',
                'children',
                'depth',
                'language',
                'page',
                'pageInPath',
                'platform',
                'root',
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException When function is not supported.
     */
    public function execute($name, array $arguments)
    {
        if (method_exists($this, $name)) {
            return call_user_func_array([$this, $name], $arguments);
        }

        throw new \RuntimeException(sprintf('Unsupported function "%s"', $name));
    }

    /**
     * Function: language(..).
     *
     * Test the page language.
     *
     * @param string $language Page language.
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function language($language)
    {
        return (strtolower($GLOBALS['objPage']->language) == strtolower($language));
    }

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
    public function page($pageId)
    {
        return is_numeric($pageId) && intval($pageId) == $GLOBALS['objPage']->id ||
            $pageId == $GLOBALS['objPage']->alias;
    }

    /**
     * Function: root(..).
     *
     * Test the root page id or alias.
     *
     * @param mixed $pageId Page id or alias.
     *
     * @return bool
     */
    public function root($pageId)
    {
        return is_numeric($pageId) && intval($pageId) == $GLOBALS['objPage']->rootId
            || $pageId == \PageModel::findByPk($GLOBALS['objPage']->rootId)->alias;
    }

    /**
     * Function: pageInPath(..).
     *
     * Test if page id or alias is in path.
     *
     * @param mixed $pageId Page id or alias.
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    public function pageInPath($pageId)
    {
        $page = $GLOBALS['objPage'];
        while (true) {
            if (intval($pageId) == $page->id || $pageId == $page->alias) {
                return true;
            }
            if ($page->pid > 0) {
                $page = \PageModel::findByPk($page->pid);
            } else {
                return false;
            }
        }
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
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    public function depth($value)
    {
        if (preg_match('#^(<|>|<=|>=|=|!=|<>)?\\s*(\\d+)$#', $value, $matches)) {
            $cmp           = $matches[1] ? $matches[1] : '=';
            $expectedDepth = intval($matches[2]);

            $depth = 0;
            $page  = \PageModel::findByPk($GLOBALS['objPage']->id);
            while ($page->pid > 0 && $page->type != 'root') {
                ++$depth;
                $page = \PageModel::findByPk($page->pid);
            }

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

        throw new \RuntimeException('Illegal depth value: "'.$value.'"');
    }

    /**
     * Function: articleExists(..).
     *
     * Test if an article exists in the column.
     *
     * @param string $column             Column or section name.
     * @param bool   $includeUnpublished If true also unpublished articles are recognized.
     *
     * @return boolean;
     */
    public function articleExists($column, $includeUnpublished = false)
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

        $statement->bindValue(1, $GLOBALS['objPage']->id);
        $statement->bindValue(2, $column);

        if ($statement->execute()) {
            return $statement->fetchColumn('count') > 0;
        }

        return false;
    }

    /**
     * Function: children(..).
     *
     * Test if the page have the specific count of children.
     *
     * @param int  $count              Count of children.
     * @param bool $includeUnpublished Include unpublished pages.
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function children($count, $includeUnpublished = false)
    {
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

        $statement->bindValue(1, $GLOBALS['objPage']->id);

        if ($statement->execute()) {
            return $statement->fetchColumn('count') >= $count;
        }

        return false;
    }

    /**
     * Function: platform(..).
     *
     * @param string $platform Platform value.
     *
     * @return bool
     */
    public function platform($platform)
    {
        switch ($platform) {
            case 'desktop':
                return !$this->mobileDetect->isMobile();
            case 'tablet':
                return $this->mobileDetect->isTablet();
            case 'smartphone':
                return !$this->mobileDetect->isTablet() && $this->mobileDetect->isMobile();
            case 'mobile':
                return $this->mobileDetect->isMobile();
            default:
                return false;
        }
    }
}
