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

namespace ContaoCommunityAlliance\Merger2\Module;

use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\PageModel;
use ContaoCommunityAlliance\Merger2\Constraint\Parser\InputStream;

/**
 * Class ModuleMerger2.
 */
class ModuleMerger2 extends \Module
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'mod_merger2';

    /**
     * Generate a front end module and return it as HTML string.
     *
     * @param PageModel $page            Page model.
     * @param string    $moduleId        Frontend module id.
     * @param string    $columnName      Column or section name.
     * @param bool      $inheritableOnly If true only inheritable module is found.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function getPageFrontendModule($page, $moduleId, $columnName = 'main', $inheritableOnly = false)
    {
        if (!is_object($moduleId) && !strlen($moduleId)) {
            return '';
        }

        // Articles
        if ($moduleId == 0) {
            // Show a particular article only
            if ($page->type == 'regular' && \Input::get('articles')) {
                list($sectionName, $articleName) = explode(':', \Input::get('articles'));

                if ($articleName === null) {
                    $articleName = $sectionName;
                    $sectionName = 'main';
                }

                if ($sectionName == $columnName) {
                    $article = \ArticleModel::findByIdOrAliasAndPid($articleName, $page->id);

                    // Send a 404 header if the article does not exist
                    if ($article === null) {
                        throw new PageNotFoundException('Page not found: ' . $articleName);
                    }

                    // Send a 403 header if the article cannot be accessed
                    if (!static::isVisibleElement($article))
                    {
                        throw new AccessDeniedException('Access denied: ' . $articleName);
                    }

                    if (!$inheritableOnly || $article->inheritable) {
                        // Add the "first" and "last" classes (see #2583)
                        $article->classes = array('first', 'last');

                        return $this->getArticle($article);
                    }

                    return '';
                }
            }

            // HOOK: add custom logic
            if (isset($GLOBALS['TL_HOOKS']['getArticles']) && is_array($GLOBALS['TL_HOOKS']['getArticles'])) {
                foreach ($GLOBALS['TL_HOOKS']['getArticles'] as $callback) {
                    $return = static::importStatic($callback[0])->{$callback[1]}($page->id, $columnName);

                    if (is_string($return)) {
                        return $return;
                    }
                }
            }

            // Show all articles (no else block here, see #4740)
            $articleCollection = \ArticleModel::findPublishedByPidAndColumn($page->id, $columnName);

            if ($articleCollection === null) {
                return '';
            }

            $return       = '';
            $intCount     = 0;
            $blnMultiMode = ($articleCollection->count() > 1);
            $intLast      = ($articleCollection->count() - 1);

            while ($articleCollection->next()) {
                if ($inheritableOnly && !$articleCollection->inheritable) {
                    continue;
                }

                $articleRow = $articleCollection->current();

                // Add the "first" and "last" classes (see #2583)
                if ($intCount == 0 || $intCount == $intLast) {
                    $cssClasses = array();

                    if ($intCount == 0) {
                        $cssClasses[] = 'first';
                    }

                    if ($intCount == $intLast) {
                        $cssClasses[] = 'last';
                    }

                    $articleRow->classes = $cssClasses;
                }

                $return .= $this->getArticle($articleRow, $blnMultiMode, false, $columnName);
                ++$intCount;
            }

            return $return;
        } else {
            // Other modules
            if (is_object($moduleId)) {
                $articleRow = $moduleId;
            } else {
                $articleRow = \ModuleModel::findByPk($moduleId);

                if ($articleRow === null) {
                    return '';
                }
            }

            // Check the visibility (see #6311)
            if (!static::isVisibleElement($articleRow)) {
                return '';
            }

            $moduleClassName = \Module::findClass($articleRow->type);

            // Return if the class does not exist
            if (!class_exists($moduleClassName)) {
                $this->log(
                    'Module class "'.$moduleClassName.'" (module "'.$articleRow->type.'") does not exist',
                    __METHOD__,
                    TL_ERROR
                );

                return '';
            }

            $articleRow->typePrefix = 'mod_';
            /** @var \Module $module */
            $module = new $moduleClassName($articleRow, $columnName);
            $buffer = $module->generate();

            // HOOK: add custom logic
            if (isset($GLOBALS['TL_HOOKS']['getFrontendModule'])
                && is_array($GLOBALS['TL_HOOKS']['getFrontendModule'])
            ) {
                foreach ($GLOBALS['TL_HOOKS']['getFrontendModule'] as $callback) {
                    $this->import($callback[0]);
                    $buffer = $this->{$callback[0]}->{$callback[1]}($articleRow, $buffer, $module);
                }
            }

            // Disable indexing if protected
            if ($module->protected && !preg_match('/^\s*<!-- indexer::stop/', $buffer)) {
                $buffer = "\n<!-- indexer::stop -->".$buffer."<!-- indexer::continue -->\n";
            }

            return $buffer;
        }
    }

    /**
     * Generate an article and return it as string.
     *
     * @param int
     * @param bool
     * @param bool
     * @param string
     *
     * @return string
     */
    protected function getPageArticle(
        $page,
        $articleId
    ) {
        $article = \ArticleModel::findByIdOrAliasAndPid($articleId, $page->id);

        if ($article === null) {
            return '';
        }

        return $this->getArticle($article);
    }

    /**
     * Inherit article from parent page.
     *
     * @param \PageModel $page
     * @param int        $maxLevel
     * @param int        $currentLevel
     *
     * @return string
     */
    protected function inheritArticle($page, $maxLevel = 0, $currentLevel = 0)
    {
        $parentPage = \PageModel::findPublishedById($page->pid);

        if ($parentPage === null) {
            return '';
        }

        $html = $this->getPageFrontendModule($parentPage, 0, $this->strColumn, true);
        if ($maxLevel == 0 || $maxLevel < ++$currentLevel) {
            $html .= $this->inheritArticle($parentPage, $maxLevel, $currentLevel);
        }

        return $html;
    }

    /**
     * Mode is "all".
     *
     * @return bool
     */
    protected function isModeAll()
    {
        return $this->merger_mode == 'all';
    }

    /**
     * Mode is "up first false".
     *
     * @return bool
     */
    protected function isModeUpFirstFalse()
    {
        return $this->merger_mode == 'upFirstFalse';
    }

    /**
     * Mode is "up first true".
     *
     * @return bool
     */
    protected function isModeUpFirstTrue()
    {
        return $this->merger_mode == 'upFirstTrue';
    }

    /**
     * Display a wildcard in the back end.
     *
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE') {
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### MERGER2 ###';
            $objTemplate->title    = $this->headline;
            $objTemplate->id       = $this->id;
            $objTemplate->link     = $this->name;
            $objTemplate->href     = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        // generate the merger container
        if ($this->merger_container) {
            return parent::generate();
        } else {
            // or only the content
            return $this->generateContent();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function compile()
    {
        $this->Template->content = $this->generateContent();
    }

    /**
     * Generate content.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     *
     * @return string
     */
    protected function generateContent()
    {
        $modules = deserialize($this->merger_data);
        $buffer  = '';

        foreach ($modules as $module) {
            if ($module['disabled']) {
                continue;
            }

            $result    = null;
            $condition = trim(html_entity_decode($module['condition']));

            if (strlen($condition)) {
                $input  = new InputStream($condition);
                $node   = $this->getContainer()->get('cca.merger2.constraint_parser')->parse($input);
                $result = $node->evaluate();
            }

            if ($result || $result === null) {
                $content = '';
                switch ($module['content']) {
                    case '-':
                        break;

                    /*
                     * Include the articles from current page.
                     */

                    case 'article':
                        $content = $this->getPageFrontendModule(
                            $GLOBALS['objPage'],
                            0,
                            $this->strColumn
                        );
                        break;

                    /*
                     * Inherit articles from one upper level that contains articles.
                     */

                    case 'inherit_articles':
                        $content = $this->inheritArticle(
                            $GLOBALS['objPage'],
                            1
                        );
                        break;

                    /*
                     * Inherit articles from all upper levels.
                     */

                    case 'inherit_all_articles':
                        $content = $this->inheritArticle(
                            $GLOBALS['objPage']
                        );
                        break;

                    /*
                     * Include the articles from current page or inherit from one upper level that contains articles.
                     */

                    case 'inherit_articles_fallback':
                        $content = $this->getPageFrontendModule(
                            $GLOBALS['objPage'],
                            0,
                            $this->strColumn
                        );

                        if (!strlen($content)) {
                            $content = $this->inheritArticle($GLOBALS['objPage'], 1);
                        }
                        break;

                    /*
                     * Include the articles from current page or inherit from upper all upper levels.
                     */

                    case 'inherit_all_articles_fallback':
                        $content = $this->getPageFrontendModule(
                            $GLOBALS['objPage'],
                            0,
                            $this->strColumn
                        );

                        if (!strlen($content)) {
                            $content = $this->inheritArticle($GLOBALS['objPage']);
                        }
                        break;

                    /*
                     * Include a module.
                     */

                    default:
                        $content = $this->getPageFrontendModule(
                            $GLOBALS['objPage'],
                            $module['content'],
                            $this->strColumn
                        );
                }

                $buffer .= $content;
                if ($result === null) {
                    $result = strlen($content) > 0;
                }
            }
            if ($result && $this->isModeUpFirstTrue() || !$result && $this->isModeUpFirstFalse()) {
                break;
            }
        }

        $tpl          = new \FrontendTemplate($this->merger_template);
        $tpl->content = $buffer;

        return $tpl->parse();
    }
}