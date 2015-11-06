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

namespace Bit3\Contao\Merger2;

/**
 * Class StandardFunctions.
 */
class StandardFunctions
{
    /**
     * function: language(..)
     * Test the page language.
     *
     * @param mixed $strLanguage
     *
     * @return bool
     */
    public static function language($strLanguage)
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
    public static function page($strId)
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
    public static function root($strId)
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
    public static function pageInPath($strId)
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
    public static function depth($strValue)
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
        } else {
            throw new \RuntimeException('Illegal depth value: "'.$strValue.'"');
        }
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
    public static function articleExists($strColumn, $boolIncludeUnpublished = false)
    {
        global $objPage;
        $time = time();
        $objArticle = \Database::getInstance()
            ->prepare(
                'SELECT COUNT(id) as count FROM tl_article WHERE pid=? AND inColumn=?'.
                ($boolIncludeUnpublished ? '' : " AND (start='' OR start<?) AND (stop='' OR stop>?) AND published=1")
            )
            ->limit(1)
            ->execute($objPage->id, $strColumn, $time, $time);
        if ($objArticle->next()) {
            return $objArticle->count > 0;
        } else {
            return false;
        }
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
    public static function children($intCount, $boolIncludeUnpublished = false)
    {
        global $objPage;
        $time = time();
        $objChildren = \Database::getInstance()
            ->prepare(
                'SELECT COUNT(id) as count FROM tl_page WHERE pid=?'.
                ($boolIncludeUnpublished ? '' : " AND (start='' OR start<?) AND (stop='' OR stop>?) AND published=1")
            )
            ->limit(1)
            ->execute($objPage->id, $time, $time);
        if ($objChildren->next()) {
            return $objChildren->count >= $intCount;
        }

        return false;
    }

    /**
     * function: platform(..).
     *
     * @param int  $intCount
     * @param bool $boolIncludeUnpublished
     *
     * @return bool
     */
    public static function platform($platform)
    {
        if (in_array(
            'theme-plus',
            \Config::getInstance()
                ->getActiveModules()
        )
        ) {
            return \Bit3\Contao\ThemePlus\ThemePlus::checkFilter(
                null,
                null,
                null,
                null,
                $platform
            );
        } else {
            $mobileDetect = new \Mobile_Detect();

            switch ($platform) {
                case 'desktop':
                    return !$mobileDetect->isMobile();
                case 'tablet':
                    return $mobileDetect->isTablet();
                case 'smartphone':
                    return !$mobileDetect->isTablet() && $mobileDetect->isMobile();
                case 'mobile':
                    return $mobileDetect->isMobile();
                default:
                    return false;
            }
        }
    }
}
