<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @copyright 2013,2014 bit3 UG
 * @author    Tristan Lins <tristan.lins@bit3.de>
 * @author    David Molineus <david.molineus@netzmacht.de>
 *
 * @link      http://bit3.de
 *
 * @license   LGPL-3.0+
 */

namespace ContaoCommunityAlliance\Merger2\Functions;

use Detection\MobileDetect;
use Doctrine\DBAL\Connection;

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
     */
    public function execute($name, array $arguments)
    {
        if (method_exists($this, $name)) {
            return call_user_func_array([$this, $name], $arguments);
        }

        throw new \RuntimeException(sprintf('Unsupported function "%s"', $name));
    }

    /**
     * function: language(..)
     * Test the page language.
     *
     * @param mixed $strLanguage
     *
     * @return bool
     */
    public function language($strLanguage)
    {
        global $objPage;

        return (strtolower($objPage->language) == strtolower($strLanguage));
    }

    /**
     * function: page(..)
     * Test the page id or alias.
     *
     * @param mixed $strId
     *
     * @return bool
     */
    public function page($strId)
    {
        global $objPage;

        return is_numeric($strId) && intval($strId) == $objPage->id ||
            $strId == $objPage->alias;
    }

    /**
     * function: root(..)
     * Test the root page id or alias.
     *
     * @param mixed $strId
     *
     * @return bool
     */
    public function root($strId)
    {
        global $objPage;

        return is_numeric($strId) && intval($strId) == $objPage->rootId ||
        $strId == \PageModel::findByPk($objPage->rootId)->alias;
    }

    /**
     * function: pageInPath(..)
     * Test if page id or alias is in path.
     *
     * @param mixed $strId
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    public function pageInPath($strId)
    {
        $page = $GLOBALS['objPage'];
        while (true) {
            if (intval($strId) == $page->id || $strId == $page->alias) {
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
     * function: depth(..)
     * Test the page depth.
     *
     * @param mixed $strValue
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    public function depth($strValue)
    {
        if (preg_match('#^(<|>|<=|>=|=|!=|<>)?\\s*(\\d+)$#', $strValue, $matches)) {
            $cmp = $matches[1] ? $matches[1] : '=';
            $expectedDepth = intval($matches[2]);

            $depth = 0;
            $page = \PageModel::findByPk($GLOBALS['objPage']->id);
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

        throw new \RuntimeException('Illegal depth value: "'.$strValue.'"');
    }

    /**
     * function: articleExists(..)
     * Test if an article exists in the column.
     *
     * @param string $strColumn
     * @param bool   $boolIncludeUnpublished
     *
     * @return boolean;
     */
    public function articleExists($strColumn, $boolIncludeUnpublished = false)
    {
        global $objPage;

        $time  = time();
        $query = 'SELECT COUNT(id) as count FROM tl_article WHERE pid=? AND inColumn=?';

        if ($boolIncludeUnpublished) {
            $query    .= ' AND (start=\'\' OR start<?) AND (stop=\'\' OR stop>?) AND published=1 LIMIT 0,1';
            $statement = $this->connection->prepare($query);

            $statement->bindValue(3, $time);
            $statement->bindValue(4, $time);
        } else {
            $statement = $this->connection->prepare($query);
        }

        $statement->bindValue(1, $objPage->id);
        $statement->bindValue(2, $strColumn);

        if ($statement->execute()) {
            return $statement->fetchColumn('count') > 0;
        }

        return false;
    }

    /**
     * function: children(..)
     * Test if the page have the specific count of children.
     *
     * @param int  $intCount
     * @param bool $boolIncludeUnpublished
     *
     * @return bool
     */
    public function children($intCount, $boolIncludeUnpublished = false)
    {
        global $objPage;

        $time  = time();
        $query = 'SELECT COUNT(id) as count FROM tl_page WHERE pid=?';

        if ($boolIncludeUnpublished) {
            $query     .= ' AND (start=\'\' OR start<?) AND (stop=\'\' OR stop>?) AND published=1 LIMIT 0,1';
            $statement  = $this->connection->prepare($query);

            $statement->bindValue(2, $time);
            $statement->bindValue(3, $time);
        } else {
            $statement  = $this->connection->prepare($query);
        }

        $statement->bindValue(1, $objPage->id);

        if ($statement->execute()) {
            return $statement->fetchColumn('count') >= $intCount;
        }

        return false;
    }

    /**
     * function: platform(..).
     *
     * @param string $platform
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
